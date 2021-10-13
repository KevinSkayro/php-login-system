<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once 'dbhandler.php';
$customerID = $_GET['id'];
$stmt = $con->prepare('SELECT customerStatus FROM customers WHERE id = ?');
$stmt->bind_param('i', $customerID);
$stmt->execute();
// Store the result so we can check if the account is active in the database.
$stmt->store_result();
if ($stmt->num_rows > 0) {
	$stmt->bind_result($customerStatus);
	$stmt->fetch();
	$stmt->close();
    if ($customerStatus == 1) {
        if (isset($_POST['grantPayment'])) {
            //check for active loans before making payment
            $stmt = $con->prepare('SELECT loanStatus FROM loans WHERE customerID = ?');
            $stmt->bind_param('i', $customerID);
            $stmt->execute();
            // Store result so we can check if customer has any loan active
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($loanStatus);
                while ($stmt->fetch()){
                    if ($loanStatus == 1) {
                        $stmt = $con->prepare('SELECT loanID, amountOwed, pastDue, weekThirteen FROM loans WHERE customerID = ? AND loanStatus = 1');
                        $stmt->bind_param('i', $customerID);
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($loanID, $amountOwed, $pastDue, $weekThirteen);
                        $stmt->fetch();
                        $stmt->close();
                        //count how many payments have been made
                        $stmt = $con->prepare('SELECT COUNT(paymentID) as numOfPayments FROM payments WHERE customerID = ? AND loanID =?');
                        $stmt->bind_param('ii', $customerID, $loanID);
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($numOfPayments);
                        $stmt->fetch();
                        $stmt->close();
                        //if this is true it means you already paid and account status is going to reset
                        if ($numOfPayments >= 12 && $amountOwed == 0) {
                            //uptate customer status
                            $stmt = $con->prepare('UPDATE customers SET customerStatus=0 WHERE id=?');
                            $stmt->bind_param('i', $customerID);
                            $stmt->execute();
                            $stmt->close();
                            $stmt = $con->prepare('UPDATE loans SET loanStatus=0 WHERE loanID=? AND customerID=?');
                            $stmt->bind_param('ii', $loanID, $customerID);
                            $stmt->execute();
                            $stmt->close();

                            //increase or reduce loan limit acording to previus history
                            //1 = late payment
					        //0 = on time payment
                            
                            //count how many late payments are on previous loan
                            $stmt = $con->prepare('SELECT COUNT(latePayment) as numOfFails FROM payments WHERE customerID = ? AND loanID = ? AND latePayment = 1');
                            $stmt->bind_param('ii', $customerID, $loanID);
                            $stmt->execute();
                            $stmt->store_result();
                            $stmt->bind_result($numOfFails);
                            $stmt->fetch();
                            $stmt->close();

                            //check maxLoan amount
                            $stmt = $con->prepare('SELECT maxLoan FROM customers WHERE id = ?');
                            $stmt->bind_param('i', $customerID);
                            $stmt->execute();
                            $stmt->store_result();
                            $stmt->bind_result($actualMaxLoan);
                            $stmt->fetch();
                            $stmt->close();


                            if ($weekThirteen == 0 && $numOfFails <= 1) {
                                $update = 1000;
                                $newMaxLoan = $actualMaxLoan + $update;
                                $stmt = $con->prepare('UPDATE customers SET maxLoan = ? WHERE id = ?');
                                $stmt->bind_param('ii', $newMaxLoan, $customerID);
                                $stmt->execute();
                                $stmt->close();
                            } else if ($weekThirteen == 1 && $numOfFails <= 1) {
                                $update = 1000;
                                $newMaxLoan = $actualMaxLoan + $update;
                                $stmt = $con->prepare('UPDATE customers SET maxLoan = ? WHERE id = ?');
                                $stmt->bind_param('ii', $newMaxLoan, $customerID);
                                $stmt->execute();
                                $stmt->close();
                            } else if ($weekThirteen == 1 && $numOfFails <= 2) {
                                $newMaxLoan = $actualMaxLoan;
                                $stmt = $con->prepare('UPDATE customers SET maxLoan = ? WHERE id = ?');
                                $stmt->bind_param('ii', $newMaxLoan, $customerID);
                                $stmt->execute();
                                $stmt->close();
                            } else if ($weekThirteen == 1 && $numOfFails >= 3) {
                                $update = 1000;
                                $newMaxLoan = $actualMaxLoan - $update;
                                $stmt = $con->prepare('UPDATE customers SET maxLoan = ? WHERE id = ?');
                                $stmt->bind_param('ii', $newMaxLoan, $customerID);
                                $stmt->execute();
                                $stmt->close();
                            }
                            header("Location: ../customer.php?id=$customerID&loan=fully-paid");
                            exit();
                        }
                        $executive = $_POST['select_employee'];
                        $paymentAmount = $_POST['payment'];
                        // format of regDate ('N = day of the week starting with 1 which is monday, 
                        //                     W = number of week starting on monday,
                        //                     d = day of month with 2 digits 01 to 31-
                        //                     m = month of the year with 2 digits 01 to 12-
                        //                     Y = full numeric representation of the year, 4 digits')
                        $paymentDate = date('N,W,d-m-Y');
                        $latePayment = $_POST['payment_punctuality'];
                        if ($latePayment == "") {
                            header("Location: ../customer.php?id=$customerID&payment=punctuality-null");
                            exit();
                        }
                        //code to subtract payment from pastDue if pastDue > 0
                        if ($pastDue > 0) {
                            $result = $pastDue - $paymentAmount;
                            if ($result < 0) {
                                $result = 0;
                            }
                            $stmt = $con->prepare('UPDATE loans SET pastDue = ? WHERE loanID = ? AND customerID = ?');
                            $stmt->bind_param('dii', $result , $loanID, $customerID);
                            $stmt->execute();
                            $stmt->close();
                        }
                        //code to make payment and check if loan is fully paid after payment
                        $stmt = $con->prepare('INSERT IGNORE INTO payments (loanID, customerID, paymentAmount, paymentDate, latePayment, executive) VALUES (?,?,?,?,?,?)');
                        $stmt->bind_param('iidsis',$loanID, $customerID, $paymentAmount, $paymentDate, $latePayment, $executive);
                        $stmt->execute();
                        $stmt = $con->prepare('SELECT amountOwed FROM loans WHERE customerID=? AND loanStatus=1');
                        $stmt->bind_param('i', $customerID);
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($amountOwed);
                        $stmt->fetch();
                        $stmt->close();
                        
                        $updatedAmount = $amountOwed - $paymentAmount;
                        $stmt = $con->prepare('UPDATE loans SET amountOwed=? WHERE customerID=? AND loanStatus=1');
                        $stmt->bind_param('di',$updatedAmount, $customerID);
                        $stmt->execute();
                        //count how many payments have been made
                        $stmt = $con->prepare('SELECT COUNT(paymentID) as numOfPayments FROM payments WHERE customerID = ? AND loanID =?');
                        $stmt->bind_param('ii', $customerID, $loanID);
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($numOfPayments);
                        $stmt->fetch();
                        $stmt->close();
                        if ($numOfPayments >= 12 && $updatedAmount == 0) {
                            $stmt = $con->prepare('UPDATE customers SET customerStatus=0 WHERE id=?');
                            $stmt-> bind_param('i', $customerID);
                            $stmt->execute();
                            $stmt->close();
                            $stmt = $con->prepare('UPDATE loans SET loanStatus=0 WHERE loanID=? AND customerID=?');
                            $stmt-> bind_param('ii', $loanID, $customerID);
                            $stmt->execute();
                            $stmt->close();

                            //increase or reduce loan limit acording to previus history
                            //1 = late payment
					        //0 = on time payment
                            
                            //count how many late payments are on previous loan
                            $stmt = $con->prepare('SELECT COUNT(latePayment) as numOfFails FROM payments WHERE customerID = ? AND loanID = ? AND latePayment = 1');
                            $stmt->bind_param('ii', $customerID, $loanID);
                            $stmt->execute();
                            $stmt->store_result();
                            $stmt->bind_result($numOfFails);
                            $stmt->fetch();
                            $stmt->close();

                            //check maxLoan amount
                            $stmt = $con->prepare('SELECT maxLoan FROM customers WHERE id = ?');
                            $stmt->bind_param('i', $customerID);
                            $stmt->execute();
                            $stmt->store_result();
                            $stmt->bind_result($actualMaxLoan);
                            $stmt->fetch();
                            $stmt->close();


                            if ($weekThirteen == 0 && $numOfFails <= 1) {
                                $update = 1000;
                                $newMaxLoan = $actualMaxLoan + $update;
                                $stmt = $con->prepare('UPDATE customers SET maxLoan = ? WHERE id = ?');
                                $stmt->bind_param('ii', $newMaxLoan, $customerID);
                                $stmt->execute();
                                $stmt->close();
                            } else if ($weekThirteen == 1 && $numOfFails <= 1) {
                                $update = 1000;
                                $newMaxLoan = $actualMaxLoan + $update;
                                $stmt = $con->prepare('UPDATE customers SET maxLoan = ? WHERE id = ?');
                                $stmt->bind_param('ii', $newMaxLoan, $customerID);
                                $stmt->execute();
                                $stmt->close();
                            } else if ($weekThirteen == 1 && $numOfFails <= 2) {
                                $newMaxLoan = $actualMaxLoan;
                                $stmt = $con->prepare('UPDATE customers SET maxLoan = ? WHERE id = ?');
                                $stmt->bind_param('ii', $newMaxLoan, $customerID);
                                $stmt->execute();
                                $stmt->close();
                            } else if ($weekThirteen == 1 && $numOfFails >= 3) {
                                $update = 1000;
                                $newMaxLoan = $actualMaxLoan - $update;
                                $stmt = $con->prepare('UPDATE customers SET maxLoan = ? WHERE id = ?');
                                $stmt->bind_param('ii', $newMaxLoan, $customerID);
                                $stmt->execute();
                                $stmt->close();
                            }
                            header("Location: ../customer.php?id=$customerID&loan=fully-paid");
                            exit();
                        }
                        header("Location: ../customer.php?id=$customerID&payment=payment-made");
                        exit();
                    }
                }
                //return to customer profile and say that he has no active loan
                header("Location: ../customer.php?id=$customerID&payment=unactive-loan");
                exit();
            }
            header("Location: ../customer.php?id=$customerID&payment=never-had-loan");
            exit();
        }
        if (isset($_POST['notPaid'])) {
            $stmt = $con->prepare('SELECT loanID, loanPlusInterest, pastDue FROM loans WHERE customerID = ? AND loanStatus = 1');
            $stmt->bind_param('i', $customerID);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($loanID, $loanPlusInterest, $pastDue);
            $stmt->fetch();
            $stmt->close();

            if ($loanPlusInterest > $pastDue) {
            $addPastDue = $loanPlusInterest / 12;
            $updatedAmount = $pastDue + $addPastDue;
            //update past due amount
            $stmt = $con->prepare('UPDATE loans SET pastDue = ? WHERE loanID = ? AND customerID = ?');
            $stmt->bind_param('dii', $updatedAmount , $loanID, $customerID);
            $stmt->execute();
            $stmt->close();
            }

            $executive = $_POST['select_employee'];
            $paymentAmount = 0;
            // format of regDate ('N = day of the week starting with 1 which is monday, 
            //                     W = number of week starting on monday,
            //                     d = day of month with 2 digits 01 to 31-
            //                     m = month of the year with 2 digits 01 to 12-
            //                     Y = full numeric representation of the year, 4 digits')
            $paymentDate = date('N,W,d-m-Y');
            $latePayment = 1;
            $stmt = $con->prepare('INSERT IGNORE INTO payments (loanID, customerID, paymentAmount, paymentDate, latePayment, executive) VALUES (?,?,?,?,?,?)');
            $stmt->bind_param('iidsis',$loanID, $customerID, $paymentAmount, $paymentDate, $latePayment, $executive);
            $stmt->execute();

            header("Location: ../customer.php?id=$customerID");
            exit();
        }
        if (isset($_POST['grantLoan'])) {
            header("Location: ../customer.php?id=$customerID&loan=active-customer");
            exit();

        }
        if(isset($_POST['grantRenewal'])) {
            $executive = $_POST['select_employee'];
            $group = $_POST['group'];
            if ($group == "") {
                header("Location: ../customer.php?id=$customerID&renewal=invalid-group");
                exit();
            }
            $loanAmount = $_POST['amount'];
            if ($loanAmount == "") {
                header("Location: ../customer.php?id=$customerID&renewal=invalid-amount");
                exit();
            }
            // format of regDate ('N = day of the week starting with 1 which is monday, 
            //                     W = number of week starting on monday,
            //                     d = day of month with 2 digits 01 to 31-
            //                     m = month of the year with 2 digits 01 to 12-
            //                     Y = full numeric representation of the year, 4 digits')
            $paymentDate = date('N,W,d-m-Y');
            $latePayment = 0;
            $stmt = $con->prepare('SELECT loanID, amountOwed, weekThirteen FROM loans WHERE customerID=? AND loanStatus=1');
            $stmt->bind_param('i', $customerID);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($loanID, $amountOwed, $weekThirteen);
            $stmt->fetch();
            $stmt->close();
            if ($weekThirteen == 1) {
                //count how many payments have been made
                $stmt = $con->prepare('SELECT COUNT(paymentID) as numOfPayments FROM payments WHERE customerID = ? AND loanID =?');
                $stmt->bind_param('ii', $customerID, $loanID);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($numOfPayments);
                $stmt->fetch();
                $stmt->close();
                if ($numOfPayments >= 12) {
                    $stmt = $con->prepare('UPDATE customers SET customerStatus=0 WHERE id=?');
                    $stmt-> bind_param('i', $customerID);
                    $stmt->execute();
                    $stmt->close();
                    $stmt = $con->prepare('UPDATE loans SET loanStatus=0 WHERE loanID=? AND customerID=?');
                    $stmt-> bind_param('ii', $loanID, $customerID);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    header("Location: ../customer.php?id=$customerID&renewal=week13-renewal-failed");
                    exit();
                }
            }
            if ($weekThirteen == 0) {
                //count how many payments have been made
                $stmt = $con->prepare('SELECT COUNT(paymentID) as numOfPayments FROM payments WHERE customerID = ? AND loanID =?');
                $stmt->bind_param('ii', $customerID, $loanID);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($numOfPayments);
                $stmt->fetch();
                $stmt->close();
                if ($numOfPayments >= 10) {
                    $stmt = $con->prepare('UPDATE customers SET customerStatus=0 WHERE id=?');
                    $stmt-> bind_param('i', $customerID);
                    $stmt->execute();
                    $stmt->close();
                    $stmt = $con->prepare('UPDATE loans SET loanStatus=0 WHERE loanID=? AND customerID=?');
                    $stmt-> bind_param('ii', $loanID, $customerID);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    header("Location: ../customer.php?id=$customerID&renewal=normal-renewal-failed");
                    exit();
                }
            }
            $stmt = $con->prepare('INSERT IGNORE INTO payments (loanID, customerID, paymentAmount, paymentDate, latePayment, executive) VALUES (?,?,?,?,?,?)');
            $stmt->bind_param('iidsis',$loanID, $customerID, $amountOwed, $paymentDate, $latePayment, $executive);
            $stmt->execute();

            $stmt = $con->prepare('UPDATE loans SET amountOwed=0 WHERE loanID=? AND customerID=?');
            $stmt->bind_param('ii', $loanID, $customerID);
            $stmt->execute();

            $interest = 1.5;
            $amountWithInterest = $loanAmount * $interest;
            $loanInterest = $amountWithInterest - $loanAmount;
            $stmt = $con->prepare('UPDATE customers SET customerStatus=1 WHERE id=?');
            $stmt-> bind_param('i', $customerID);
            $stmt->execute();
            $stmt->close();
            $stmt = $con->prepare('INSERT IGNORE INTO loans (customerID, loanAmount, loanInterest, loanPlusInterest, amountOwed, loanDate, executive, groupID) VALUES (?,?,?,?,?,?,?,?)');
            $stmt->bind_param('iiiidssi',$customerID, $loanAmount, $loanInterest, $amountWithInterest, $amountWithInterest, $paymentDate, $executive, $group);
            $stmt->execute();
            header("Location: ../customer.php?id=$customerID&renewal=completed=successfully");
            exit();
        }
    }
    if ($customerStatus == 0) {
        if (isset($_POST['grantLoan'])) {
            $stmt = $con->prepare('SELECT loanStatus FROM loans WHERE customerID = ?');
            $stmt->bind_param('i', $customerID);
            $stmt->execute();
            // Store result so we can check if customer has any loan active
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($loanStatus);
                while ($stmt->fetch()){
                    //#####################################
                    //execute if customer had a loan before
                    //#####################################
                    if ($loanStatus == 1) {
                        //return to customer profile and say that he has a active loan
                        header("Location: ../customer.php?id=$customerID&loan=active-loan");
                        exit();
                    }
                    if ($loanStatus == 0) {
                        $executive = $_POST['select_employee'];
                        $amount = $_POST['amount'];
                        if ($amount == "") {
                            header("Location: ../customer.php?id=$customerID&loan=invalid-amount");
                            exit();
                        }
                        $group = $_POST['group'];
                        if ($group == "") {
                            header("Location: ../customer.php?id=$customerID&loan=invalid-group");
                            exit();
                        }
                        $folio = $_POST['folio'];
                        if ($folio == "") {
                            header("Location: ../customer.php?id=$customerID&loan=invalid-folio");
                            exit();
                        }
                        $stmt = $con->prepare('SELECT folio FROM loans WHERE folio = ?');
                        $stmt->bind_param('s', $folio);
                        $stmt->execute();
                        $stmt->store_result();
                        if ($stmt->num_rows > 0) {
                            header("Location: ../customer.php?id=$customerID&loan=duplicated-folio");
                            exit();
                        }
                        $interest = 1.5;
                        $amountWithInterest = $amount * $interest;
                        $loanInterest = $amountWithInterest - $amount;
                        $loanDate = date('N,W,d-m-Y');
                        $stmt = $con->prepare('UPDATE customers SET customerStatus=1, group_name=? WHERE id=?');
                        $stmt-> bind_param('si', $group, $customerID);
                        $stmt->execute();
                        $stmt->close();
                        $stmt = $con->prepare('INSERT IGNORE INTO loans (customerID, loanAmount, loanInterest, loanPlusInterest, amountOwed, loanDate, executive, groupID, folio) VALUES (?,?,?,?,?,?,?,?,?)');
                        $stmt->bind_param('iiiidssis',$customerID, $amount, $loanInterest, $amountWithInterest, $amountWithInterest, $loanDate, $executive, $group, $folio);
                        $stmt->execute();
                        header("Location: ../customer.php?id=$customerID");
                        exit();
                    }
                }
            }
            //####################################
            //execute if customer never had a loan
            //####################################
            $executive = $_POST['select_employee'];
            $amount = $_POST['amount'];
            if ($amount == "") {
                header("Location: ../customer.php?id=$customerID&loan=invalid-amount");
                exit();
            }
            $group = $_POST['group'];
            if ($group == "") {
                header("Location: ../customer.php?id=$customerID&loan=invalid-group");
                exit();
            }
            $folio = $_POST['folio'];
            if ($folio == "") {
                header("Location: ../customer.php?id=$customerID&loan=invalid-folio");
                exit();
            }
            $stmt = $con->prepare('SELECT folio FROM loans WHERE folio = ?');
            $stmt->bind_param('s', $folio);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                header("Location: ../customer.php?id=$customerID&loan=duplicated-folio");
                exit();
            }
            $interest = 1.5;
            $amountWithInterest = $amount * $interest;
            $loanInterest = $amountWithInterest - $amount;
            $loanDate = date('N,W,d-m-Y');
            $maxLoanAmount = $amount;
            $stmt = $con->prepare('UPDATE customers SET customerStatus=1, maxLoan=? WHERE id=?');
            $stmt-> bind_param('ii', $maxLoanAmount, $customerID);
            $stmt->execute();
            $stmt->close();
            $stmt = $con->prepare('INSERT IGNORE INTO loans (customerID, loanAmount, loanInterest, loanPlusInterest, amountOwed, loanDate, executive, groupID, folio) VALUES (?,?,?,?,?,?,?,?,?)');
            $stmt->bind_param('iiiidssis',$customerID, $amount, $loanInterest, $amountWithInterest, $amountWithInterest, $loanDate, $executive, $group, $folio);
            $stmt->execute();
            header("Location: ../customer.php?id=$customerID");
            exit();
        }
        if (isset($_POST['grantPayment'])) {
            //return to customer profile and say that he has no active loan, no payments can be made
            header("Location: ../customer.php?id=$customerID&payment=unactive-loan");
            exit();
        }
    }
}


