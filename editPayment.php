<?php
include 'processes/dbhandler.php';
// Default input product values
$payment = array(
    'loanID' => '',
    'paymentAmount' => '',
    'paymentDate' => '',
    'latePayment' => '',
    'executive' => ''
);

if (isset($_GET['id'], $_GET['payId'])) {
    $customerID = $_GET['id'];
    $paymentID = $_GET['payId'];
    $stmt = $con->prepare('SELECT loanID, paymentAmount, paymentDate, latePayment, executive FROM payments WHERE customerID = ? AND paymentID = ?');
    $stmt->bind_param('ii', $customerID, $paymentID);
    $stmt->execute();
    $stmt->bind_result($payment['loanID'], $payment['paymentAmount'], $payment['paymentDate'], $payment['latePayment'], $payment['executive']);
    $stmt->fetch();
    $stmt->close();
    if (isset($_POST['submit'])) {
        $paymentAmount = $_POST['paymentAmount'];
        // format of regDate ('N = day of the week starting with 1 which is monday, 
        //                     W = number of week starting on monday,
        //                     d = day of month with 2 digits 01 to 31,
        //                     m = month of the year with 2 digits 01 to 12,
        //                     Y = full numeric representation of the year, 4 digits')
        $paymentDate = date('N,W,d-m-Y');
        $latePayment = $_POST['latePayment'];
        $stmt = $con->prepare('UPDATE payments SET paymentAmount=?, paymentDate=?, latePayment=? WHERE customerID=? AND paymentID=?');
        $stmt->bind_param('dsiii',$paymentAmount, $paymentDate, $latePayment, $customerID, $paymentID);
        $stmt->execute();
        header("Location: editPayment.php?id=$customerID&payId=$paymentID&payment=edited-succesfully");
        exit();
    }
} else {
        // If id and payId doesn't return it does not exist
        header("Location: editPayment.php?id=$customerID&payId=$paymentID&payment=inexistent");
        exit();
}

// echo htmlspecialchars($customerID);
// echo htmlspecialchars($paymentID);
include_once  'header.php';
?>
<div class="header">
    <div class="back_btn_container">
		<div class="back_btn" onclick="location.href='customer.php?id=<?=$customerID?>'">
			<i class="fas fa-chevron-left"></i> Atrás
		</div>
	</div>
    <?php 
        $stmt = $con->prepare('SELECT name, lastname FROM customers WHERE id = ?');
        $stmt->bind_param('i', $customerID);
        $stmt->execute();
        $stmt->bind_result($name, $lastname);
        $stmt->fetch();
        $stmt->close();
    ?>
    <h2>Cliente: <?=$customerID. ' | ' .$name.' '.$lastname?></h2>
    <br>
    <h3>Identificación de préstamo: <?=$payment['loanID']?></h3>
    <br>
    <h3>Clave única de pago: <?=$paymentID?></h3>
</div>
    <div class="content profile editPayment">
    <div class="error_display">
		<?php
			$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            if (strpos($fullUrl, "payment=edited-succesfully") == true) {
				echo"<span class='success'>¡Pago fue editado exitosamente!</span>";
			} elseif (strpos($fullUrl, "payment=inexistent") == true) {
				echo"<span class='fail'>¡Cliente y/o pago no existe!</span>";
			}
		?>
	</div>
        <form action="" method="post">
            <label for="paymentAmount">Cantidad pagada</label>
            <input type="text" value="<?=$payment['paymentAmount']?>" name="paymentAmount" id="paymentAmount" placeholder="Cantidad de pago recibida">
            <label for="latePayment">Puntualidad</label>
            <select name="latePayment" id="latePayment" required>
				<!-- 
					1 = late payment
					0 = on time payment 
				-->
                <?php if ($payment['latePayment'] == 1): ?>
                    <option value="1" selected>Tardío</option>
				    <option value="0">Puntual</option>
                <?php elseif ($payment['latePayment'] == 0): ?>
                    <option value="1">Tardío</option>
				    <option value="0" selected>Puntual</option>
                <?php endif; ?>
			</select>

            <input class="profile_btn" type="submit" name="submit" value="Guardar">
        </form>
    </div>
