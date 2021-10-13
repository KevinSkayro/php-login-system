<?php
include 'main.php';
$plazaID = $_GET['id'];
$stmt = $con->prepare('SELECT plaza_name, executive, supervisor FROM plazas WHERE id = ?');
$stmt->bind_param('i', $plazaID);
$stmt->execute();
$stmt->bind_result($plazaName, $executive, $supervisor);
$stmt->fetch();
$stmt->close();

$stmt = $con->prepare('SELECT name, lastname FROM accounts WHERE id = ?');
$stmt->bind_param('i', $executive);
$stmt->execute();
$stmt->bind_result($exName, $exLastname);
$stmt->fetch();
$stmt->close();

$stmt = $con->prepare('SELECT name, lastname FROM accounts WHERE id = ?');
$stmt->bind_param('i', $supervisor);
$stmt->execute();
$stmt->bind_result($supName, $supLastname);
$stmt->fetch();
$stmt->close();

// query to get all groups from the database
$stmt = $con->prepare('SELECT id, group_name, asesor FROM groups WHERE plazaID =?');
$stmt->bind_param('i', $plazaID);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $groupName, $asesor);
?>
<?=template_admin_header('Plaza '.$plazaName)?>

<h2>Plaza <?=$plazaName?></h2>
<div class="plaza_header">
    <h3>Ejecutivo: <?=$executive." | ".$exName." ".$exLastname?></h3>
    <h3>Supervisor: <?=$supervisor." | ".$supName." ".$supLastname?></h3>
</div>




<div class="links">
    <a href="addPlaza.php?id=<?=$plazaID?>">Editar plaza</a>
</div>

<div class="content-block home">
    <div class="error_display">
		<?php
			$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            if (strpos($fullUrl, "plaza=created-successfully") == true) {
				echo"<span class='success'>¡Plaza fue creada exitosamente!</span>";
			} elseif (strpos($fullUrl, "plaza=updated-successfully") == true) {
				echo"<span class='success'>¡La plaza fue actualizado exitosamente!</span>";
			}
		?>
	</div>
    <div class="table">
        <div class="listHead">
            <div class="column id">#</div>
            <div class="column">Nombre de grupo</div>
            <div class="column">Asesor</div>
        </div>
        <div class="list_container">
            <ul>
                <?php if ($stmt->num_rows == 0): ?>
                        <li>No existe ningun grupo en esta plaza</li>
                <?php else: ?>
                    <?php while ($stmt->fetch()): ?>
                        <li class="row" onclick="location.href='addGroup.php?id=<?=$id?>'">
                            <div class="column id"><?=$id?></div>
                            <div class="column"><?=$groupName?></div>
                            <div class="column"><?=$asesor?></div>
                        </li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</div>
<?=template_admin_footer()?>