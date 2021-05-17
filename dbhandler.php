<?php
// The dbhandler file contains the database connection, session initializing, and functions, other PHP files will depend on this file.
// Include the configuration file
include_once 'config.php';
// start session using the below function
session_start();
// Connect to the MySQL database using MySQLi
$con = mysqli_connect(db_host, db_user, db_pass, db_name);
if (mysqli_connect_errno()) {
	exit('Fallo la conexión con el servidor MySQL: ' . mysqli_connect_error());
}
// Update the charset
mysqli_set_charset($con, db_charset);
// The below function will check if the user is logged-in and also check the remember me cookie
function check_loggedin($con, $redirect_file = 'index.php') {
	// Check for remember me cookie variable and loggedin session variable
    if (isset($_COOKIE['rememberme']) && !empty($_COOKIE['rememberme']) && !isset($_SESSION['loggedin'])) {
    	// If the remember me cookie matches one in the database then we can update the session variables.
    	$stmt = $con->prepare('SELECT id, name, role FROM accounts WHERE rememberme = ?');
		$stmt->bind_param('s', $_COOKIE['rememberme']);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			// Found a match, update the session variables and keep the user logged-in
			$stmt->bind_result($id, $name, $role);
			$stmt->fetch();
            $stmt->close();
			session_regenerate_id();
			$_SESSION['loggedin'] = TRUE;
			$_SESSION['name'] = $name;
			$_SESSION['id'] = $id;
			$_SESSION['role'] = $role;
		} else {
			// If the user is not remembered redirect to the login page.
			header('Location: index.php');
			exit;
		}
    } else if (!isset($_SESSION['loggedin'])) {
    	// If the user is not logged in redirect to the login page.
    	header('Location: index.php');
    	exit;
    }
}
// Send activation email function
function send_activation_email($email, $code) {
	$subject = 'Account Activation Required';
	$headers = 'From: ' . mail_from . "\r\n" . 'Reply-To: ' . mail_from . "\r\n" . 'Return-Path: ' . mail_from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
	$activate_link = activation_link . '?email=' . $email . '&code=' . $code;
	$email_template = str_replace('%link%', $activate_link, file_get_contents('activation-email-template.html'));
	mail($email, $subject, $email_template, $headers);
}