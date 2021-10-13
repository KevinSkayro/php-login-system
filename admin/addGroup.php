<?php
include 'main.php';
// Default input product values
$group = array(
    'group_name' => '',
    'plazaID' => '',
    'asesor' => ''
);

if (isset($_GET['id'])) {
    $groupID = $_GET['id'];
    // Get the group info from the database
    $stmt = $con->prepare('SELECT group_name, plazaID, asesor FROM groups WHERE id = ?');
    $stmt->bind_param('i', $groupID);
    $stmt->execute();
    $stmt->bind_result($group['group_name'], $group['plazaID'], $group['asesor']);
    $stmt->fetch();
    $stmt->close();

    // ID param exists, edit an existing account
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        $groupName = strtoupper($_POST['group_name']);
        $plazaID = $_POST['plazaID'];
        $asesor = $_POST['asesor'];
        //Get group from employee account
        $stmt = $con->prepare('SELECT role, plaza_name FROM accounts WHERE id = ?');
        $stmt->bind_param('i', $asesor);
        $stmt->execute();
        $stmt->bind_result($asesorRole, $asesorGroups);
        $stmt->fetch();
        $stmt->close();
        if ($asesorRole !== 'Miembro') {
            header("Location: addGroup.php?id=$groupID&group=no-asesor");
            exit();
        }
        if ($asesor !== $group['asesor']) {
            $stmt = $con->prepare('SELECT plaza_name FROM accounts WHERE id = ?');
            $stmt->bind_param('i', $asesor);
            $stmt->execute();
            $stmt->bind_result($aGroup);
            $stmt->fetch();
            $stmt->close();
            $deleteGroup = "";
            $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
            $stmt->bind_param('si', $deleteGroup, $group['asesor']);
            $stmt->execute();
            $stmt = $con->prepare('UPDATE groups SET asesor = ? WHERE id = ?');
            $stmt->bind_param('si', $deleteGroup, $aGroup);
            $stmt->execute();
        }
        // Update the group
        $stmt = $con->prepare('UPDATE groups SET group_name = ?, plazaID = ?, asesor = ? WHERE id = ?');
        $stmt->bind_param('sssi', $groupName, $plazaID, $asesor, $groupID);
        $stmt->execute();
        //update employee account
        $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
        $stmt->bind_param('si', $groupID, $asesor);
        $stmt->execute();
        header("Location: addGroup.php?id=$groupID&group=updated-successfully");
        exit;
    }
    if (isset($_POST['delete'])) {
        //get members of the group
        $stmt = $con->prepare('SELECT asesor FROM groups WHERE id = ?');
        $stmt->bind_param('i', $groupID);
        $stmt->execute();
        $stmt->bind_result($asesor);
        $stmt->fetch();
        $stmt->close();
        //asesor
        $stmt = $con->prepare('SELECT plaza_name FROM accounts WHERE id = ?');
        $stmt->bind_param('i', $asesor);
        $stmt->execute();
        $stmt->bind_result($aGroup);
        $stmt->fetch();
        $stmt->close();
        if ($aGroup != "") {
            $deleteGroup = "";
            $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
            $stmt->bind_param('si', $deleteGroup, $group['asesor']);
            $stmt->execute();
        }

        // Delete the account
        $stmt = $con->prepare('DELETE FROM groups WHERE id = ?');
        $stmt->bind_param('i', $groupID);
        $stmt->execute();
        header("Location: groups.php?group=deleted-successfully");
        exit;
    }
} else {
    // Create a new account
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $groupName = strtoupper($_POST['group_name']);
        if($groupName == "") {
            header("Location: addGroup.php?group=no-group-name");
            exit;
        }
        $plazaID = $_POST['plazaID'];
        if($plazaID == "") {
            header("Location: addGroup.php?group=empty-plaza");
            exit;
        }
        $asesor = $_POST['asesor'];
        if($asesor == "") {
            header("Location: addGroup.php?group=empty-asesor");
            exit;
        }
        //Get role from employee account
        $stmt = $con->prepare('SELECT role FROM accounts WHERE id = ?');
        $stmt->bind_param('i', $asesor);
        $stmt->execute();
        $stmt->bind_result($asesorRole);
        $stmt->fetch();
        $stmt->close();
        if ($asesorRole !== 'Miembro') {
            header("Location: addGroup.php?group=no-asesor");
            exit();
        }

        $stmt = $con->prepare('SELECT asesor FROM groups WHERE asesor = ?');
        $stmt->bind_param('s', $asesor);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            header("Location: addGroup.php?group=duplicated-asesor");
            exit;
        }
        $stmt = $con->prepare('INSERT IGNORE INTO groups (group_name, plazaID, asesor) VALUES (?,?,?)');
        $stmt->bind_param('sis', $groupName, $plazaID, $asesor);
        $stmt->execute();
        //select the ID of the just created group
        $stmt = $con->prepare('SELECT id FROM groups WHERE group_name = ? AND plazaID = ?');
        $stmt->bind_param('si', $groupName, $plazaID);
        $stmt->execute();
        $stmt->bind_result($groupID);
        $stmt->fetch();
        $stmt->close();
        $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
        $stmt->bind_param('si', $groupID, $asesor);
        $stmt->execute();
        header("Location: groups.php?group=created-successfully");
        exit;
    }
}
?>

<?php 
// Change of title 
if ($page == 'Edit'){
    $Espage = 'Editando';
}elseif ($page == 'Create'){
    $Espage = 'Creando';
}   
?>
<?=template_admin_header($Espage . ' Grupo')?>

<?php if ($page == 'Edit'): ?>
    <h2>Editar Grupo</h2>

<?php elseif ($page == 'Create'): ?>
    <h2>Crear Grupo</h2>
<?php endif; ?>

<div class="content-block">
    <div class="error_display">
		<?php
			$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

			if (strpos($fullUrl, "group=no-asesor") == true) {
				echo"<span class='fail'>¡Cambio denegado, solo asesores pueden ser seleccionados como asesor!</span>";
			} elseif (strpos($fullUrl, "group=updated-successfully") == true) {
				echo"<span class='success'>¡Grupo fue actualizado exitosamente!</span>";
			} elseif (strpos($fullUrl, "group=no-group-name") == true) {
				echo"<span class='fail'>¡Cambio denegado, no puedes crear un grupo sin nombre!</span>";
			} elseif (strpos($fullUrl, "group=empty-plaza") == true) {
				echo"<span class='fail'>¡Cambio denegado, no puedes crear un grupo sin seleccionar una plaza!</span>";
			} elseif (strpos($fullUrl, "group=empty-asesor") == true) {
				echo"<span class='fail'>¡Cambio denegado, no puedes crear un grupo sin seleccionar un asesor!</span>";
			} elseif (strpos($fullUrl, "group=duplicated-asesor") == true) {
				echo"<span class='fail'>¡Cambio denegado, asesor seleccionado ya tiene un grupo asignado!</span>";
			} 
		?>
	</div>
    <form action="" method="post" class="form responsive-width-100">
        <p>Información de grupo</p>
        <label for="group_name">Nombre de grupo</label>
        <input type="text" id="group_name" name="group_name" placeholder="Nombre de Grupo" value="<?=$group['group_name']?>" required>

        <label for="plazaID">Plaza</label>
        <select name="plazaID" id="plazaID" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;">
			<option value="<?=$group['plazaID']?>">
            <?php if ($page == 'Edit'): ?>
                <?php if ($group['plazaID'] == ''): ?>
                    Seleccionar plaza
                <?php else: ?>
                    <?php
                        //select plaza data
                        $stmt = $con->prepare('SELECT id, plaza_name FROM plazas WHERE id = ?');
                        $stmt->bind_param('i', $group['plazaID']);
                        $stmt->execute();
                        $stmt->bind_result($plazaID, $plazaName);
                        $stmt->fetch();
                        $stmt->close();
                    ?>  
                    <?=$plazaID." | ".$plazaName?>
                <?php endif; ?>
            <?php elseif ($page == 'Create'): ?>
                Seleccionar plaza
            <?php endif; ?>
            </option>
            <?php 
                $stmt = $con->prepare('SELECT id, plaza_name FROM plazas');
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($plazaID, $plazaName);
		    ?>
			<?php if ($stmt->num_rows == 0): ?>
				<option>No usuarios</option>
			<?php else: ?>
                <?php while ($stmt->fetch()): ?>
                    <option value="<?=$plazaID?>"><?=$plazaID." | ".$plazaName?></option>
                <?php endwhile; ?>
			<?php endif; ?>
		</select>
        <label for="asesor">Asesor</label>
        <select name="asesor" id="asesor" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;">
			<option value="<?=$group['asesor']?>">
            <?php if ($page == 'Edit'): ?>
                <?php if ($group['asesor'] == ''): ?>
                    Seleccionar asesor
                <?php else: ?>
                    <?php
                        //select asesor data
                        //the "a" at the beginning of variable names = asesor
                        $stmt = $con->prepare('SELECT id, name, lastname FROM accounts WHERE id = ?');
                        $stmt->bind_param('i', $group['asesor']);
                        $stmt->execute();
                        $stmt->bind_result($aCurrID, $aCurrName, $aCurrLastname);
                        $stmt->fetch();
                        $stmt->close();
                    ?>  
                    <?=$aCurrID." | ".$aCurrName." ".$aCurrLastname?>
                <?php endif; ?>
            <?php elseif ($page == 'Create'): ?>
                Seleccionar asesor
            <?php endif; ?>
            </option>
            <?php 
                $stmt = $con->prepare('SELECT id, name, lastname FROM accounts WHERE role = "Miembro"');
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($employeeID, $name, $lastname);
		    ?>
			<?php if ($stmt->num_rows == 0): ?>
				<option>No usuarios</option>
			<?php else: ?>
                <?php while ($stmt->fetch()): ?>
                    <option value="<?=$employeeID?>"><?=$employeeID." | ".$name." ".$lastname?></option>
                <?php endwhile; ?>
			<?php endif; ?>
		</select>
        <div class="submit-btns">
            <input type="submit" name="submit" value="Guardar">
            <?php if ($page == 'Edit'): ?>
            <input type="submit" name="delete" value="Borrar" class="delete">
            <?php endif; ?>
        </div>
    </form>
</div>
<?=template_admin_footer()?>
