<?php
include 'main.php';
// Default input product values
$plaza = array(
    'plaza_name' => '',
    'executive' => '',
    'supervisor' => ''
);

if (isset($_GET['id'])) {
    $plazaID = $_GET['id'];
    // Get the plaza info from the database
    $stmt = $con->prepare('SELECT plaza_name, executive, supervisor FROM plazas WHERE id = ?');
    $stmt->bind_param('i', $plazaID);
    $stmt->execute();
    $stmt->bind_result($plaza['plaza_name'], $plaza['executive'], $plaza['supervisor']);
    $stmt->fetch();
    $stmt->close();

    // ID param exists, edit an existing account
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        $plazaName = strtoupper($_POST['plaza_name']);
        $executive = $_POST['executive'];
        $supervisor = $_POST['supervisor'];
        $stmt = $con->prepare('SELECT role, plaza_name FROM accounts WHERE id = ?');
        $stmt->bind_param('i', $executive);
        $stmt->execute();
        $stmt->bind_result($executiveRole, $executivePlazas);
        $stmt->fetch();
        $stmt->close();
        if ($executiveRole !== 'Ejecutivo') {
            header("Location: addPlaza.php?id=$plazaID&plaza=no-executive");
            exit();
        }
        //delete plaza id from previous account if changed
        if ($executive !== $plaza['executive']) {
            $stmt = $con->prepare('SELECT plaza_name FROM accounts WHERE id = ?');
            $stmt->bind_param('i', $plaza['executive']);
            $stmt->execute();
            $stmt->bind_result($excPlazaArray);
            $stmt->fetch();
            $stmt->close();
            $modifiedPlazaID = $plazaID . ',';
            $deletePlaza = str_replace($modifiedPlazaID, '', $excPlazaArray);
            $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
            $stmt->bind_param('si', $deletePlaza, $plaza['executive']);
            $stmt->execute();
        }
        // delete plaza if from previous account if changed
        if ($supervisor !== $plaza['supervisor']) {
            $stmt = $con->prepare('SELECT plaza_name FROM accounts WHERE id = ?');
            $stmt->bind_param('i', $supervisor);
            $stmt->execute();
            $stmt->bind_result($supPlaza);
            $stmt->fetch();
            $stmt->close();
            $deletePlaza = "";
            $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
            $stmt->bind_param('si', $deletePlaza, $plaza['supervisor']);
            $stmt->execute();
            $stmt = $con->prepare('UPDATE plazas SET supervisor = ? WHERE id = ?');
            $stmt->bind_param('si', $deletePlaza, $supPlaza);
            $stmt->execute();
        }
        // Update the plaza
        $stmt = $con->prepare('UPDATE plazas SET plaza_name = ?, executive = ?, supervisor = ? WHERE id = ?');
        $stmt->bind_param('sssi', $plazaName, $executive, $supervisor, $plazaID);
        $stmt->execute();

        //update employee account
        if ($executivePlazas !== ''){
            if (strpos($executivePlazas, $plazaID) !== false) {
                $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
                $stmt->bind_param('si', $executivePlazas, $executive);
                $stmt->execute();
                
                $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
                $stmt->bind_param('si', $plazaID, $supervisor);
                $stmt->execute();
                header("Location: plaza.php?id=$plazaID&plaza=updated-successfully");
                exit;
            }
            $executiveArray = $executivePlazas . $plazaID . ',';
            // Update the plaza for executive
            $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
            $stmt->bind_param('si', $executiveArray, $executive);
            $stmt->execute();

            $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
            $stmt->bind_param('si', $plazaID, $supervisor);
            $stmt->execute();
            header("Location: plaza.php?id=$plazaID&plaza=updated-successfully");
            exit;
        }
        //this variable puts a comma after the plaza ID
        $PlazaIdPlusComma = $plazaID . ',';
        $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
        $stmt->bind_param('si', $plazaIdPlusComma, $executive);
        $stmt->execute();
        
        $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
        $stmt->bind_param('si', $plazaID, $supervisor);
        $stmt->execute();
        header("Location: plaza.php?id=$plazaID&plaza=updated-successfully");
        exit;
    }
    if (isset($_POST['delete'])) {
        //get members of the group
        $stmt = $con->prepare('SELECT executive, supervisor FROM plazas WHERE id = ?');
        $stmt->bind_param('i', $plazaID);
        $stmt->execute();
        $stmt->bind_result($executive, $supervisor);
        $stmt->fetch();
        $stmt->close();
        //executive
        $stmt = $con->prepare('SELECT plaza_name FROM accounts WHERE id = ?');
        $stmt->bind_param('i', $executive);
        $stmt->execute();
        $stmt->bind_result($excGroupArray);
        $stmt->fetch();
        $stmt->close();
        if ($excGroupArray != "") {
            $modifiedPlazaID = $plazaID . ',';
            $deleteGroup = str_replace($modifiedPlazaID, '', $excGroupArray);
            //delete group from the executives group string
            $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
            $stmt->bind_param('si', $deleteGroup, $executive);
            $stmt->execute();
        }

        //supervisor
        $stmt = $con->prepare('SELECT plaza_name FROM accounts WHERE id = ?');
        $stmt->bind_param('i', $supervisor);
        $stmt->execute();
        $stmt->bind_result($supGroup);
        $stmt->fetch();
        $stmt->close();
        if ($supGroup != "") {
            $deleteGroup = "";
            $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
            $stmt->bind_param('si', $deleteGroup, $supervisor);
            $stmt->execute();
        }
        // Delete the account
        $stmt = $con->prepare('DELETE FROM plazas WHERE id = ?');
        $stmt->bind_param('i', $plazaID);
        $stmt->execute();
        header("Location: plazas.php?plaza=deleted-successfully");
        exit;
    }
} else {
    // Create a new account
    $page = 'Create';
    //check if inputs are empty
    if (isset($_POST['submit'])) {
        $plazaName = strtoupper($_POST['plaza_name']);
        if($plazaName == "") {
            header("Location: addPlaza.php?plaza=no-plaza-name");
            exit;
        }
        $executive = $_POST['executive'];
        if($executive == "") {
            header("Location: addPlaza.php?plaza=empty-executive");
            exit;
        }
        $supervisor = $_POST['supervisor'];
        if($supervisor == "") {
            header("Location: addPlaza.php?plaza=empty-supervisor");
            exit;
        }
        //check if there's a plaza with the same name
        $stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE plaza_name = ?');
        $stmt->bind_param('s', $plazaName);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            header("Location: addPlaza.php?plaza=duplicated-plaza-name");
            exit;
        }
        //check if the member selected is an executive
        $stmt = $con->prepare('SELECT role, plaza_name FROM accounts WHERE id = ?');
        $stmt->bind_param('i', $executive);
        $stmt->execute();
        $stmt->bind_result($executiveRole, $executivePlazas);
        $stmt->fetch();
        $stmt->close();
        if ($executiveRole !== 'Ejecutivo') {
            header("Location: addPlaza.php?plaza=no-executive");
            exit();
        }
        //check if executive has more than 4 plazas under it's belt
        $stmt = $con->prepare('SELECT executive FROM plazas WHERE executive = ?');
        $stmt->bind_param('s', $executive);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows >= 4) {
            $stmt->close();
            header("Location: addPlaza.php?plaza=duplicated-executive");
            exit;
        }
        //check if member selected is a supervisor
        $stmt = $con->prepare('SELECT role FROM accounts WHERE id = ?');
        $stmt->bind_param('i', $supervisor);
        $stmt->execute();
        $stmt->bind_result($supervisorRole);
        $stmt->fetch();
        $stmt->close();
        if ($supervisorRole !== 'Supervisor') {
            header("Location: addPlaza.php?plaza=no-supervisor");
            exit();
        }
        //check that there's no other plaza under supervisor
        $stmt = $con->prepare('SELECT supervisor FROM plazas WHERE supervisor = ?');
        $stmt->bind_param('s', $supervisor);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            header("Location: addPlaza.php?plaza=duplicated-supervisor");
            exit;
        }
        //insert data into plazas table
        $stmt = $con->prepare('INSERT IGNORE INTO plazas (plaza_name, executive, supervisor) VALUES (?,?,?)');
        $stmt->bind_param('sss', $plazaName, $executive, $supervisor);
        $stmt->execute();
        //select the ID of the just created plaza
        $stmt = $con->prepare('SELECT id FROM plazas WHERE plaza_name = ?');
        $stmt->bind_param('s', $plazaName);
        $stmt->execute();
        $stmt->bind_result($plazaID);
        $stmt->fetch();
        $stmt->close();

        //update employee account
        if ($executivePlazas !== ''){
            $executiveArray = $executivePlazas . $plazaID . ',';
            // Update the plaza for executive
            $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
            $stmt->bind_param('si', $executiveArray, $executive);
            $stmt->execute();

            $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
            $stmt->bind_param('si', $plazaID, $supervisor);
            $stmt->execute();
            header("Location: plaza.php?id=$plazaID&plaza=created-successfully");
            exit;

        }
        //this variable puts a comma after the plaza ID
        $plazaIdPlusComma = $plazaID . ',';
        $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
        $stmt->bind_param('si', $plazaIdPlusComma, $executive);
        $stmt->execute();
        
        $stmt = $con->prepare('UPDATE accounts SET plaza_name = ? WHERE id = ?');
        $stmt->bind_param('si', $plazaID, $supervisor);
        $stmt->execute();
        header("Location: plazas.php?id=$plazaID&plaza=created-successfully");
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
<?=template_admin_header($Espage . ' Plaza')?>

<?php if ($page == 'Edit'): ?>
    <h2>Editar Plaza</h2>

<?php elseif ($page == 'Create'): ?>
    <h2>Crear Plaza</h2>
<?php endif; ?>

<div class="content-block">
    <div class="back_btn_container">
        <?php if (isset($plazaID)): ?>
            <div class="back_btn" onclick="location.href='plaza.php?id=<?=$plazaID?>'">
                <i class="fas fa-chevron-left"></i> Atrás
            </div>
        <?php else: ?>
            <div class="back_btn" onclick="location.href='plazas.php'">
                <i class="fas fa-chevron-left"></i> Atrás
            </div>
        <?php endif; ?>
	</div>
    <div class="error_display">
		<?php
			$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

			if (strpos($fullUrl, "plaza=no-plaza-name") == true) {
				echo"<span class='fail'>¡Cambio denegado, no puede crear una plaza sin nombre!</span>";
			} elseif (strpos($fullUrl, "plaza=empty-executive") == true) {
				echo"<span class='fail'>¡Cambio denegado, no puede crear una plaza sin ejecutivo!</span>";
			} elseif (strpos($fullUrl, "plaza=empty-supervisor") == true) {
				echo"<span class='fail'>¡Cambio denegado, no puede crear una plaza sin supervisor!</span>";
			} elseif (strpos($fullUrl, "plaza=duplicated-plaza-name") == true) {
				echo"<span class='fail'>¡Cambio denegado, ya existe una plaza con el mismo nombre!</span>";
			} elseif (strpos($fullUrl, "plaza=no-executive") == true) {
				echo"<span class='fail'>¡Cambio denegado, solo ejecutivos pueden ser seleccionados como ejecutivo de una plaza!</span>";
			} elseif (strpos($fullUrl, "plaza=duplicated-executive") == true) {
				echo"<span class='fail'>¡Cambio denegado, ejecutivo seleccionado excede el número máximo de plazas bajo su cargo!</span>";
			} elseif (strpos($fullUrl, "plaza=duplicated-supervisor") == true) {
				echo"<span class='fail'>¡Cambio denegado, supervisor seleccionado ya tiene una plaza asignada!</span>";
			} elseif (strpos($fullUrl, "plaza=no-supervisor") == true) {
				echo"<span class='fail'>¡Cambio denegado, solo supervisores pueden ser seleccionados como supervisores de una plaza!</span>";
			} 
		?>
	</div>

    <form action="" method="post" class="form responsive-width-100">

        <p>Información de plaza</p>

        <label for="plaza_name">Nombre de plaza</label>
        <input type="text" id="plaza_name" name="plaza_name" placeholder="Nombre de Plaza" value="<?=$plaza['plaza_name']?>" required>
        
        <label for="executive">Ejecutivo</label>
        <select name="executive" id="executive" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;">
			<option value="<?=$plaza['executive']?>">
            <?php if ($page == 'Edit'): ?>
                <?php if ($plaza['executive'] == ''): ?>
                    Seleccionar supervisor
                <?php else: ?>
                    <?php
                        //the "e" at the beginning of the variable names = executive
                        $stmt = $con->prepare('SELECT id, name, lastname FROM accounts WHERE id = ?');
                        $stmt->bind_param('i', $plaza['executive']);
                        $stmt->execute();
                        $stmt->bind_result($eCurrID, $eCurrName, $eCurrLastname);
                        $stmt->fetch();
                        $stmt->close();
                    ?>  
                    <?=$eCurrID." | ".$eCurrName." ".$eCurrLastname?>
                <?php endif; ?>
            <?php elseif ($page == 'Create'): ?>
                Seleccionar ejecutivo
            <?php endif; ?>
            </option>
            <?php 
                $stmt = $con->prepare('SELECT id, name, lastname FROM accounts WHERE role = "Ejecutivo"');
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

        <label for="supervisor">Supervisor</label>
        <select name="supervisor" id="supervisor" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;">
			<option value="<?=$plaza['supervisor']?>">
            <?php if ($page == 'Edit'): ?>
                <?php if ($plaza['supervisor'] == ''): ?>
                    Seleccionar supervisor
                <?php else: ?>
                    <?php
                        //the "s" at the beginning of the variable names = supervisor
                        $stmt = $con->prepare('SELECT id, name, lastname FROM accounts WHERE id = ?');
                        $stmt->bind_param('i', $plaza['supervisor']);
                        $stmt->execute();
                        $stmt->bind_result($sCurrID, $sCurrName, $sCurrLastname);
                        $stmt->fetch();
                        $stmt->close();
                    ?>  
                    <?=$sCurrID." | ".$sCurrName." ".$sCurrLastname?>
                <?php endif; ?>
            <?php elseif ($page == 'Create'): ?>
                Seleccionar supervisor
            <?php endif; ?>
            </option>
            <?php 
                $stmt = $con->prepare('SELECT id, name, lastname FROM accounts WHERE role = "Supervisor"');
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
