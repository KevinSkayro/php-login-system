<?php
session_start();
session_destroy();
if (isset($_COOKIE['rememberme'])) {
    unset($_COOKIE['rememberme']);
    setcookie('rememberme', '', time() - 3600);
}
// Redirect to the login page:
header('Location: index.php');
?>
