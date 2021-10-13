<?php
include 'dbhandler.php';
// Output message
$msg = '';
// Now we check if the email from the resend activation form was submitted, isset() will check if the email exists.
if (isset($_POST['email'])) {
    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.
    $stmt = $con->prepare('SELECT activation_code FROM accounts WHERE email = ? AND activation_code != "" AND activation_code != "activated"');
    // In this case we can use the account ID to get the account info.
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    $stmt->store_result();
    // Check if the account exists:
    if ($stmt->num_rows > 0) {
        // account exists
        $stmt->bind_result($activation_code);
        $stmt->fetch();
        $stmt->close();
        // Account exist, the $msg variable will be used to show the output message (on the HTML form)
        send_activation_email($_POST['email'], $activation_code);
        $msg = 'Activaton link has been sent to your email!';
    } else {
        $msg = 'We do not have an account with that email!';
    }
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>Resend Activation Email</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body>
		<div class="login">
			<h1>Resend Activation Email</h1>
			<form action="resendactivation.php" method="post">
                <label for="email">
					<i class="fas fa-envelope"></i>
				</label>
				<input type="email" name="email" placeholder="Your Email" id="email" required>
				<div class="msg"><?=$msg?></div>
				<input type="submit" value="Submit">
			</form>
		</div>
	</body>
</html>