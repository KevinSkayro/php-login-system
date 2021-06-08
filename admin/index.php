<?php
include 'main.php';
// query to get all accounts from the database
$stmt = $con->prepare('SELECT id, name, lastname, password, email, activation_code, role FROM accounts');
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $name, $lastname, $password, $email, $activation_code, $role);
?>

<?=template_admin_header('Perfiles')?>

<h2>Perfiles</h2>

<div class="links">
    <a href="account.php">Crear Cuenta</a>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td>Nombre Completo</td>
                    <td class="responsive-hidden">Correo</td>
                    <td class="responsive_hidden_1200">Código de activación</td>
                    <td class="responsive-hidden">Rango</td>
                </tr>
            </thead>
            <tbody>
                <?php if ($stmt->num_rows == 0): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">No existe ninguna cuenta en la base de datos</td>
                </tr>
                <?php else: ?>
                <?php while ($stmt->fetch()): ?>
                <tr class="details" onclick="location.href='profile.php?id=<?=$id?>'">
                    <td><?=$id?></td>
                    <td><?=$name." ".$lastname?></td>
                    <td class="responsive-hidden"><?=$email?></td>
                    <td class="responsive_hidden_1200"><?=$activation_code?></td>
                    <td class="responsive-hidden"><?=$role?></td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>
