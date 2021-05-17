<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<?php
		$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		if (strpos($fullUrl, "home") == true){
			echo "<title>Inicio</title>";
		}else if(strpos($fullUrl, "profile") == true){
			echo "<title>Perfil</title>";
		}else if(strpos($fullUrl, "customer") == true){
			$id = $_GET['id'];
			echo "<title>Cliente $id</title>";
		}else if(strpos($fullUrl, "addCustomer.php?id=") == true){
			$id = $_GET['id'];
			echo "<title>Editando cliente $id...</title>";
		}else if(strpos($fullUrl, "addCustomer") == true){
			echo "<title>Añadiendo nuevo cliente</title>";
		}
		?>
		<link rel="stylesheet" href="./CSS/reset.css" />
		<link rel="stylesheet" href="./CSS/style.css" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<link
      		href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;0,900;1,500&display=swap"
      		rel="stylesheet"
    	/>
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div class="nav_container">
				<div class="nav_logo">
					<h1 >Website Title</h1>
				</div>
				<div class="nav_links">
					<a href="home.php"><i class="fas fa-home"></i>Inicio</a>
					<a href="profile.php"><i class="fas fa-user-circle"></i>Perfil</a>
					<?php if ($_SESSION['role'] == 'Admin'): ?>
					<a href="admin/index.php"><i class="fas fa-user-cog"></i>Admin</a>
					<?php endif; ?>
					<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Salir</a>
				</div>
				<div class="burger_menu">
					<div class="burger_line line_one"></div>
					<div class="burger_line line_two"></div>
					<div class="burger_line line_three"></div>
				</div>
			</div>
		</nav>
