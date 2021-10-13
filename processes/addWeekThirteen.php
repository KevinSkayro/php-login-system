<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once 'dbhandler.php';
$customerID = $_GET['id'];
//check if there's a customer 
$stmt = $con->prepare('SELECT customerStatus FROM customers WHERE id = ?');
$stmt->bind_param('i', $customerID);
$stmt->execute();
$stmt->store_result();
//if there is a customer bind customer status
if ($stmt->num_rows > 0) {
	$stmt->bind_result($customerStatus);
	$stmt->fetch();
	$stmt->close();
    //if active  continue with the process(1 = active, 0 = unactive)
    if ($customerStatus == 1) {
        $stmt = $con->prepare('SELECT loanStatus FROM loans WHERE customerID = ?');
        $stmt->bind_param('i', $customerID);
        $stmt->execute();
        // Store result so we can check if customer has any loan active
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($loanStatus);
            while ($stmt->fetch()){
                if ($loanStatus == 1) {
                    $stmt = $con->prepare('SELECT loanPlusInterest, weekThirteen FROM loans WHERE customerID = ? AND loanStatus = 1');
                    $stmt->bind_param('i', $customerID);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($loanAmount, $weekThirteen);
                    $stmt->fetch();
                    $stmt->close();
                    if ($weekThirteen == 0) {
                        $penalty = $loanAmount / 12;
                        $updatedAmount = $loanAmount + $penalty;
                        $stmt = $con->prepare('UPDATE loans SET weekThirteen=1, amountOwed=?  WHERE customerID=? AND loanStatus=1');
                        $stmt->bind_param('di',$updatedAmount, $customerID);
                        $stmt->execute();
                        header("Location: ../customer.php?id=$customerID");
                        exit();
                    }
                    header("Location: ../customer.php?id=$customerID&weekThirteen-active");
                    exit();

                }

            }
        }

    }
    header("Location: ../customer.php?id=$customerID&weekThirteen=unactive-loan");
    exit();
}