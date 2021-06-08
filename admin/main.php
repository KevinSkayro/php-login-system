<?php
// Include the root "dbhandler.php" file and check if user is logged-in...
include_once '../config.php';
include_once '../dbhandler.php';
check_loggedin($con, '../index.php');
$stmt = $con->prepare('SELECT password, email, role, name FROM accounts WHERE id = ?');
// Get the account info using the logged-in session ID
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email, $role, $username);
$stmt->fetch();
$stmt->close();
// Check if the user is an admin...
if ($role != 'Admin') {
    exit('You do not have permission to access this page!');
}
// Template admin header
function template_admin_header($title) {
echo <<<EOT
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
        <title>$title</title>
        <link rel="stylesheet" href="../CSS/reset.css" type="text/css" />
		<link rel="stylesheet" href="../CSS/style.css"  type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="admin">
        <header>
            <h1>Panel de Administración</h1>
            <div class="burger_menu" data-burger-menu>
                <div class="burger_line line_one" ></div>
                <div class="burger_line line_two" ></div>
                <div class="burger_line line_three" ></div>
            </div>
        </header>
        <aside class="nav_links admin" data-nav-links>
            <a href="../home.php"><i class="fas fa-home"></i>Inicio</a>
            <a href="index.php"><i class="fas fa-users"></i>Perfiles</a>
            <a class="link_hidden" href="emailtemplate.php"><i class="fas fa-envelope"></i>Plantilla de correo</a>
            <a class="link_hidden" href="settings.php"><i class="fas fa-tools"></i>Configuraciones</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i>Salir</a>
        </aside>
        <main class="responsive-width-100">
EOT;
}
// Template admin footer
function template_admin_footer() {
echo <<<EOT
        </main>
        <script src="../JS/menu.js">
        </script>
    </body>
</html>
EOT;
}
?>
