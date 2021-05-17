<?php
include 'dbhandler.php';
// No need for the user to see the login form if they're logged-in so redirect them to the home page
if (isset($_SESSION['loggedin'])) {
	// If the user is not logged in redirect to the home page.
	header('Location: home.php');
	exit;
}
// Also check if they are "remembered"
if (isset($_COOKIE['rememberme']) && !empty($_COOKIE['rememberme'])) {
	// If the remember me cookie matches one in the database then we can update the session variables.
	$stmt = $con->prepare('SELECT id, name, role FROM accounts WHERE rememberme = ?');
	$stmt->bind_param('s', $_COOKIE['rememberme']);
	$stmt->execute();
	$stmt->store_result();
	if ($stmt->num_rows > 0) {
		// Found a match
		$stmt->bind_result($id, $name, $role);
		$stmt->fetch();
		$stmt->close();
		session_regenerate_id();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['name'] = $name;
		$_SESSION['id'] = $id;
		$_SESSION['role'] = $role;
		header('Location: home.php');
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./CSS/reset.css" />
    <link rel="stylesheet" href="./CSS/style.css" />
    <link
      rel="stylesheet"
      href="https://use.fontawesome.com/releases/v5.7.1/css/all.css"
    />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;0,900;1,500&display=swap"
      rel="stylesheet"
    />
    <title>Castañeda</title>
  </head>
  <body>
    <div class="main_page_container">
      <div class="main_login_container">
        <div class="main_login_left">
          <h1>Captura de clientes</h1>
          <span>Captura de datos</span>
        </div>
        <div class="main_login_right">
          <div class="login_form_container">
            <span class="login_header">Inicio de sesión</span>
            <div class="msg login_msg"></div>
            <form action="authenticate.php" method="post" class="login_form">
              <input type="text" name="id" placeholder="Código de empleado" />
              <input
                type="password"
                name="password"
                class="password"
                placeholder="Contraseña"
              />
              <button type="submit">Iniciar sesión</button>
            </form>
            <div class="create_new_account">
              <button class="open_registration">Crear nueva cuenta</button>
            </div>
          </div>
        </div>
      </div>
      <div class="main_registration_container">
        <div class="close_registration_container">
          <button class="close_registration">
            <i class="fas fa-chevron-down"></i>
          </button>
        </div>
        <div class="registration_form_container">
          <span class="registration_header">Registrate</span>
          <div class="msg"></div>
          <form action="register.php" method="post" class="registration_form">
            <input type="text" name="name" placeholder="Nombre(s)" required />
            <input
              type="text"
              name="lastname"
              placeholder="Apellidos"
              required
            />
            <input
              type="email"
              name="email"
              placeholder="Correo electrónico"
              required
            />
            <input
              type="password"
              name="newPassword"
              placeholder="Contraseña"
              required
            />
            <input
              type="password"
              name="repeatPwd"
              placeholder="Confirma contraseña"
              required
            />
            <!-- <label for="date of birth">Fecha de nacimiento</label>
            <input type="date" name="dob" required /> -->
            <button type="submit" name="submit">Registrarte</button>
          </form>
        </div>
      </div>
    </div>
    <script>
    document.querySelector(".login_form").onsubmit = function (event) {
    event.preventDefault();
    var form_data = new FormData(document.querySelector(".login_form"));
    var xhr = new XMLHttpRequest();
    xhr.open("POST", document.querySelector(".login_form").action, true);
    xhr.onload = function () {
      if (this.responseText.toLowerCase().indexOf("success") !== -1) {
        window.location.href = "home.php";
      } else {
        document.querySelector(".msg").innerHTML = this.responseText;
      }
    };
    xhr.send(form_data);
};
    </script>
    <script src="./JS/login_code.js"></script>
  </body>
</html>
