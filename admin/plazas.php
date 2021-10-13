<?php
include 'main.php';
// query to get all groups from the database
$stmt = $con->prepare('SELECT id, plaza_name, executive, supervisor FROM plazas');
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $plazaName, $executive, $supervisor);
?>
<?=template_admin_header('Plazas')?>

<h2>Plazas</h2>

<div class="links">
    <a href="addPlaza.php">Crear Plaza</a>
</div>

<div class="content-block home">
    <div class="error_display">
		<?php
			$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            if (strpos($fullUrl, "plaza=deleted-successfully") == true) {
				echo"<span class='success'>¡Plaza fue borrada exitosamente!</span>";
			}
		?>
	</div>
    <div class="table">
        <div class="listHead">
            <div class="column id">#</div>
            <div class="column">Nombre de plaza</div>
            <div class="column">Ejecutivo</div>
            <div class="column">Supervisor</div>
        </div>
        <div class="list_container">
            <ul>
                <?php if ($stmt->num_rows == 0): ?>
                        <li>No existe ninguna plaza en la base de datos</li>
                <?php else: ?>
                    <?php while ($stmt->fetch()): ?>
                        <li class="row" onclick="location.href='plaza.php?id=<?=$id?>'">
                            <div class="column id"><?=$id?></div>
                            <div class="column"><?=$plazaName?></div>
                            <div class="column"><?=$executive?></div>
                            <div class="column"><?=$supervisor?></div>
                        </li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</div>
<?=template_admin_footer()?>