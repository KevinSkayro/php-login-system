<?php
include 'main.php';
// query to get all groups from the database
$stmt = $con->prepare('SELECT id, group_name, plazaID, asesor FROM groups');
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $groupName, $plazaID, $asesor);
?>
<?=template_admin_header('Grupos')?>

<h2>Grupos</h2>

<div class="links">
    <a href="addGroup.php">Crear Grupo</a>
</div>

<div class="content-block home">
    <div class="error_display">
		<?php
			$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            if (strpos($fullUrl, "group=created-successfully") == true) {
				echo"<span class='success'>¡Grupo fue creado exitosamente!</span>";
			}elseif (strpos($fullUrl, "group=deleted-successfully") == true) {
				echo"<span class='success'>¡Grupo fue borrado exitosamente!</span>";
			}
		?>
	</div>
    <div class="table">
        <div class="listHead">
            <div class="column id">#</div>
            <div class="column">Nombre de grupo</div>
            <div class="column">Plaza</div>
            <div class="column">Asesor</div>
        </div>
        <div class="list_container">
            <ul>
                <?php if ($stmt->num_rows == 0): ?>
                        <li>No existe ningun grupo en la base de datos</li>
                <?php else: ?>
                    <?php while ($stmt->fetch()): ?>
                        <li class="row" onclick="location.href='addGroup.php?id=<?=$id?>'">
                            <div class="column id"><?=$id?></div>
                            <div class="column"><?=$groupName?></div>
                            <div class="column"><?=$plazaID?></div>
                            <div class="column"><?=$asesor?></div>
                        </li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</div>
<?=template_admin_footer()?>