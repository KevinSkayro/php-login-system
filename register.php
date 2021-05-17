<?php
include 'dbhandler.php';
if (mysqli_connect_errno()) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Fallo la conexión con la base de datos: ' . mysqli_connect_error());
}
// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['name'], $_POST['lastname'], $_POST['email'], $_POST['newPassword'], $_POST['repeatPwd'])) {
	// Could not get the data that should have been sent.
	exit('¡Por favor completa la forma de registración!');
}
// Make sure the submitted registration values are not empty.
if (empty($_POST['name']) || empty($_POST['lastname']) || empty($_POST['email']) || empty($_POST['newPassword'])) {
	// One or more values are empty.
	exit('¡Por favor completa la forma de registración!');
}
// Check to see if the email is valid.
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	exit('Correo electrónico no válido');
}
// Password must be between 5 and 20 characters long.
if (strlen($_POST['newPassword']) > 20 || strlen($_POST['newPassword']) < 5) {
	exit('¡Contraseña tiene que tener entre 5 y 20 caracteres!');
}
// Check if both the password and confirm password fields match
if ($_POST['repeatPwd'] != $_POST['newPassword']) {
	exit('¡Contraseñas no coinciden!');
}
	// Insert new account
	$stmt = $con->prepare('INSERT INTO accounts (name, lastname, password, email, activation_code) VALUES (?, ?, ?, ?, ?)');
	// Do not want to expose passwords in our database, so hashed the password and used password_verify when a user logs in.
	$password = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
	$uniqid = account_activation ? uniqid() : 'activated';
	$stmt->bind_param('sssss', $_POST['name'], $_POST['lastname'], $password, $_POST['email'], $uniqid);
	$stmt->execute();
	$stmt->close();
	if (account_activation) {
		// Account activation required, send the user the activation email with the "send_activation_email" function from the "dbhandler.php" file
		send_activation_email($_POST['email'], $uniqid);
		echo '¡Por favor revisa tu correo electrónico para activar tu cuenta!';
	} else {
		echo '¡Tu registro ha sido exitoso, ahora puedes iniciar sesión!';
	}