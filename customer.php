<?php
include 'processes/dbhandler.php';
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Default input product values
$customer = array(
    'name' => '',
    'lastname' => '',
    'ine' => '',
    'birthday' => '',
    'password' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'state' => '',
    'guar_name' => '',
    'guar_lastname' => '',
    'guar_phone' => '',
    'guar_address' => '',
    'guar_city' => '',
    'guar_state' => '',
    'guar_ine' => '',
	'group_name' =>'',
    'customerStatus' =>''
);
$id = $_GET['id'];
if (isset($_GET['id'])) {
    // Get the account from the database
    $stmt = $con->prepare('SELECT name, lastname, ine, birthday, email, phone, address, city, state, 
        guar_name, guar_lastname, guar_phone, guar_address, guar_city, guar_state, guar_ine, group_name, customerStatus, maxLoan FROM customers WHERE id = ?');
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $stmt->bind_result($customer['name'], $customer['lastname'], $customer['ine'], $customer['birthday'], $customer['email'], 
        $customer['phone'], $customer['address'], $customer['city'], $customer['state'], 
        $customer['guar_name'], $customer['guar_lastname'], $customer['guar_phone'], $customer['guar_address'], $customer['guar_city'], $customer['guar_state'], 
        $customer['guar_ine'], $customer['group_name'], $customer['customerStatus'], $customer['maxLoan']);
    $stmt->fetch();
    $stmt->close();

} else {
    // If id doesn't return it does not exist
    echo'Esta cuenta no existe';
    
}
include_once  'header.php';
?>

<div class="header">
    <h2>Cliente: <?=$customer['name'].' '.$customer['lastname']?></h2>
</div>
<div class="content profile">
	<div class="error_display">
		<?php
			$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

			if (strpos($fullUrl, "loan=active-customer") == true) {
				echo"<span class='fail'>¡Cliente tiene un préstamo vigente, por lo tanto no se le puede otorgar otro préstamo!</span>";
			} elseif (strpos($fullUrl, "payment=unactive-loan") == true) {
				echo"<span class='fail'>¡No tienes un préstamo vigente, por lo tanto no puedes realizar pagos!</span>";
			} elseif (strpos($fullUrl, "payment=never-had-loan") == true) {
				echo"<span class='fail'>¡Nunca has tenido un préstamo vigente, por lo tanto no puedes realizar pagos!</span>";
			} elseif (strpos($fullUrl, "payment=punctuality-null") == true) {
				echo"<span class='fail'>¡No selecciono puntualidad de pago!</span>";
			} elseif (strpos($fullUrl, "payment=payment-made") == true) {
				echo"<span class='success'>¡Pago fue realizado exitosamente!</span>";
			} elseif (strpos($fullUrl, "loan=fully-paid") == true) {
				echo"<span class='success'>¡Préstamo ha sido pagado en su totalidad!</span>";
			} elseif (strpos($fullUrl, "loan=invalid-amount") == true) {
				echo"<span class='fail'>¡No es posible realizar un préstamo sin seleccionar cantidad!</span>";
			} elseif (strpos($fullUrl, "loan=invalid-group") == true) {
				echo"<span class='fail'>¡No es posible realizar un préstamo sin seleccionar grupo!</span>";
			} elseif (strpos($fullUrl, "weekThirteen-active") == true) {
				echo"<span class='fail'>¡Semana trece ya fue aplicada antes por lo tanto no se puede continuar!</span>";
			} elseif (strpos($fullUrl, "weekThirteen=unactive-loan") == true) {
				echo"<span class='fail'>¡No puedes aplicar la semana 13 si el cliente no tiene préstamo vigente!</span>";
			} elseif (strpos($fullUrl, "renewal=invalid-group") == true) {
				echo"<span class='fail'>¡No es posible realizar una renovación de préstamo sin seleccionar grupo!</span>";
			} elseif (strpos($fullUrl, "renewal=invalid-amount") == true) {
				echo"<span class='fail'>¡No es posible realizar una renovación de préstamo sin seleccionar cantidad!</span>";
			} elseif (strpos($fullUrl, "renewal=week13-renewal-failed") == true) {
				echo"<span class='fail'>¡Este perfil tiene la semana 13 aplicada, por lo tanto la renovación de préstamo es solo posible después de 12 semanas pagando puntualmente después de la primera falla!</span>";
			} elseif (strpos($fullUrl, "renewal=normal-renewal-failed") == true) {
				echo"<span class='fail'>¡Tienes que esperar a la semana 10 haciendo pagos puntuales para poder ser elegible a la renovación de préstamo!</span>";
			} elseif (strpos($fullUrl, "renewal=completed=successfully") == true) {
				echo"<span class='success'>¡Proceso de renovación de préstamo realizado exitosamente!</span>";
			} elseif (strpos($fullUrl, "loan=active-loan") == true) {
				echo"<span class='fail'>¡Ya tiene un préstamo vigente!</span>";
			} elseif (strpos($fullUrl, "loan=invalid-folio") == true) {
				echo"<span class='fail'>¡No puedes empezar un préstamo sin número de folio!</span>";
			} elseif (strpos($fullUrl, "loan=duplicated-folio") == true) {
				echo"<span class='fail'>¡El número de folio que ingresaste ya existe en el sistema, favor de verificar número!</span>";
			}
		?>
	</div>
    <div class="current_balance_container">
        <div class="current_balance">
            <?php if ($customer['customerStatus'] == 0): ?>
                <span>$0.00 pesos</span>

			<?php elseif ($customer['customerStatus'] == 1): ?>
				<?php
				$stmt = $con->prepare('SELECT loanID, loanAmount, loanInterest, loanPlusInterest, amountOwed, weekThirteen, executive, folio, pastDue FROM loans WHERE customerID = ? AND loanStatus = 1');
				$stmt->bind_param('i', $_GET['id']);
				$stmt->execute();
				$stmt->bind_result($loanID, $loanAmount, $loanInterest, $loanPlusInterest, $amountOwed, $weekThirteen, $executive, $folio, $pastDue);
				$stmt->fetch();
				$stmt->close();
				?>

				<?php if ( strlen(substr(strrchr($amountOwed, "."), 1)) == 1 ): ?>
					<span>$<?=$amountOwed?>0 pesos</span>
				<?php elseif ( strlen(substr(strrchr($amountOwed, "."), 1)) == 2 ): ?>
					<span>$<?=$amountOwed?> pesos</span>
				<?php else: ?>
					<span>$<?=$amountOwed?>.00 pesos</span>
				<?php endif; ?>

			<?php endif; ?>
            <span class="under_text">Cantidad total a pagar</span>
        </div>
    </div>
    <div class="btns_container">
        <?php if ($customer['customerStatus'] == '0'): ?>
		    <button data-loan-btn>Iniciar prestamo</button>
			<button class="hidden" data-pay-btn>Hacer pago</button>
            <button class="hidden" data-renewal-btn>Renovar prestamo</button>
        <?php elseif ($customer['customerStatus'] == '1'): ?>
			<button class="hidden" data-loan-btn>Iniciar prestamo</button>
            <button data-pay-btn>Hacer pago</button>
            <button data-renewal-btn>Renovar prestamo</button>
	    <?php endif; ?>
    </div>
	<div class="info_windows_container">
		<div class="window">
			<div class="left">
				<?php if ($customer['customerStatus'] == 1): ?>
					<i class="fas fa-dot-circle green"></i>
				<?php elseif ($customer['customerStatus'] == 0): ?>
					<i class="fas fa-dot-circle red"></i>
				<?php endif; ?>
			</div>
			<div class="right">
				<div class="right_upper">
					<?php if ($customer['customerStatus'] == 1): ?>
						<span class="text">Vigente</span>
					<?php elseif ($customer['customerStatus'] == 0): ?>
						<span class="text">Vencido</span>
					<?php endif; ?>
				</div>
				<div class="right_lower">
					<span>Vigencia</span>
				</div>
			</div>
		</div>
		<div class="window">
			<div class="left">
				<i class="fas fa-receipt"></i>
			</div>
			<div class="right">
				<div class="right_upper">
				<?php if ($customer['customerStatus'] == 0): ?>
						<span>No folio</span>
					<?php elseif ($customer['customerStatus'] == 1): ?>
						<span>#<?=$folio?></span>
					<?php endif; ?>
				</div>
				<div class="right_lower">
					<span>Folio</span>
				</div>
			</div>
		</div>
		<div class="window">
			<div class="left">
				<i class="fas fa-map-marker-alt"></i>
			</div>
			<div class="right">
				<div class="right_upper">
					<?php if ($customer['group_name'] == ''): ?>
						<span>No tiene plaza asignada</td>
					<?php else: ?>
						<?php
						//select plazaID to get name
						$stmt = $con->prepare('SELECT plazaID FROM groups WHERE id = ?');
						$stmt->bind_param('i', $customer['group_name']);
						$stmt->execute();
						$stmt->bind_result($plazaID);
						$stmt->fetch();
						$stmt->close();
						//Get plaza name
						$stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE id = ?');
						$stmt->bind_param('i', $plazaID);
						$stmt->execute();
						$stmt->bind_result($plazaName);
						$stmt->fetch();
						$stmt->close();
						?>
						<span><?=$plazaName?></span>
					<?php endif; ?>
				</div>
				<div class="right_lower">
					<span>Plaza</span>
				</div>
			</div>
		</div>
		<div class="window">
			<div class="left">
				<i class="fas fa-users"></i>
			</div>
			<div class="right">
				<div class="right_upper">
					<?php if ($customer['group_name'] == ''): ?>
						<span>No tiene grupo asignado</td>
					<?php else: ?>
						<?php
						$stmt = $con->prepare('SELECT group_name FROM groups WHERE id = ?');
						$stmt->bind_param('i', $customer['group_name']);
						$stmt->execute();
						$stmt->bind_result($groupName);
						$stmt->fetch();
						$stmt->close();
						?>
						<span><?=$groupName?></span>
					<?php endif; ?>
				</div>
				<div class="right_lower">
					<span>Grupo</span>
				</div>
			</div>
		</div>
		<?php if ($customer['customerStatus'] == '1'): ?>
			<div class="window">
				<div class="left">
					<i class="fas fa-calendar-week"></i>
				</div>
				<div class="right">
					<div class="right_upper">
						<?php 	
							//week thirteen number meaning
							//1 = true
							//0 = false
						?>
						<?php if ($weekThirteen == 1): ?>
							<span class="text">Activa</span>
						<?php elseif ($weekThirteen == 0): ?>
							<span class="text">Inactiva</span>
						<?php endif; ?>
					</div>
					<div class="right_lower">
						<span>Semana 13</span>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<div class="window">
			<div class="left">
				<i class="fas fa-hourglass-end"></i>
			</div>
			<div class="right">
				<div class="right_upper">
					<?php if ($customer['customerStatus'] == 1): ?>
						<span>$<?=$pastDue?></span>
					<?php elseif ($customer['customerStatus'] == 0): ?>
						<span>$0.00 pesos</span>
					<?php endif; ?>
						

				</div>
				<div class="right_lower">
					<span>Deuda Atrasada</span>
				</div>
			</div>
		</div>
		<div class="window">
			<div class="left">
				<i class="fas fa-money-check"></i>
			</div>
			<div class="right">
				<div class="right_upper">
					<?php if ($customer['customerStatus'] == 0): ?>
						<span>$0.00 pesos</span>
					<?php elseif ($customer['customerStatus'] == 1): ?>
						<span>$<?= $loanAmount?>.00</span>
					<?php endif; ?>
				</div>
				<div class="right_lower">
					<span>Prestamo</span>
				</div>
			</div>
		</div>
		<div class="window">
			<div class="left">
				<i class="fas fa-money-bill-wave"></i>	
			</div>
			<div class="right">
				<div class="right_upper">
					<?php if ($customer['maxLoan'] >= 0): ?>
						<span>$<?=$customer['maxLoan']?>.00</span>
					<?php endif; ?>
				</div>
				<div class="right_lower">
					<span>Prestamo Maximo</span>
				</div>
			</div>
		</div>
	</div>
	<div class="payments_history" data-payment-history>
		<div class="expand_btn" data-expandPaymentHistory-btn>
			<i class="fas fa-expand" id="expand"></i>
		</div>
		<div class="listHead payments_header">
			<div class="responsive_hidden_768">
				No. de pago
			</div>
			<div class="responsive_hidden_1000">
				Identificación de pago
			</div>
			<div class="responsive_hidden_411">
				No. de prestamo
			</div>
			<div>
				Cantidad pagada
			</div>
			<div>
				Fecha de pago
			</div>
		</div>
		<div class="payment_history_inner_container">
			<?php 
				$stmt = $con->prepare('SELECT COUNT(loanID) as numOfLoans FROM loans WHERE customerID = ?');
				$stmt->bind_param('i', $_GET['id']);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($numOfLoans);
				$stmt->fetch();
				$stmt->close();
			?>
			<?php if ($numOfLoans <= 0): ?>
				<span>No historial disponible</span>
			<?php elseif ($numOfLoans > 0): ?>
				<?php 
					$stmt = $con->prepare('SELECT paymentID, loanID, paymentAmount, paymentDate, latePayment FROM payments WHERE customerID = ?');
					$stmt->bind_param('i', $_GET['id']);
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result($paymentID, $loanID, $paymentAmount, $paymentDate, $latePayment);
				?>
			<ul class="payment_list">
				<?php 
					$NumOfPayment = 0;
					$actualLoanID = $loanID;
				?>

				<?php while ($stmt->fetch()): ?>
					<?php 
						$NumOfPayment++;
						$dateSplit = explode(',',$paymentDate);
						if ($actualLoanID != $loanID) {
							$actualLoanID = $loanID;
							$NumOfPayment = 1;
						} 
					?>
					<?php if ($latePayment == 1):?>
						<li class="payment_item late" onclick="location.href='editPayment.php?id=<?=$id?>&payId=<?=$paymentID?>'">
					<?php elseif ($latePayment == 0):?>
						<li class="payment_item" onclick="location.href='editPayment.php?id=<?=$id?>&payId=<?=$paymentID?>'">
					<?php endif; ?>
							<div>
								<div class="responsive_hidden_768">
									<?=$NumOfPayment?>
								</div>
								<div class="responsive_hidden_1000">
									<?=$paymentID?>
								</div>
								<div class="responsive_hidden_411">
									<?=$loanID?>
								</div>
								<div>
									<?=$paymentAmount?>
								</div>
								<div>
									<?=$dateSplit[2]?>
								</div>
							</div>
						</li>
				<?php endwhile; ?>
			</ul>
			<?php endif; ?>
		</div>
	</div>
	<?php if ($customer['customerStatus'] == 1 && $weekThirteen == 0): ?>
		<?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Ejecutivo'): ?>
			<div class="week13_btn_container">
				<button data-week13-btn>Añadir semana 13</button>
			</div>
			<div class="week13_confirm_background" data-week13-bkgd></div>
			<div class="week13_confirm_window" data-week13-container>
				<div>
					<span>¿Estás seguro(a) que quieres añadir la semana 13?</span>
				</div>
				<div class="confirm_window_btns_container">
					<button onclick="location.href='processes/addWeekThirteen.php?id=<?php echo $id; ?>'">Si</button>
					<button data-week13-close-btn>No</button>
				</div>

			</div>
			<script>
				const openBtn = document.querySelector('[data-week13-btn]');
				const bkgd = document.querySelector('[data-week13-bkgd]');
				const windowContainer = document.querySelector('[data-week13-container]');
				const closeBtn = document.querySelector('[data-week13-close-btn]')

				openBtn.addEventListener('click', () =>{
					bkgd.classList.add('active');
					windowContainer.classList.add('active');
				});
				bkgd.addEventListener('click', () =>{
					bkgd.classList.remove('active');
					windowContainer.classList.remove('active');
				});
				closeBtn.addEventListener('click', () =>{
					bkgd.classList.remove('active');
					windowContainer.classList.remove('active');
				});
			</script>
		<?php endif; ?>
	<?php endif; ?>
</div>

<div class="customer_form_background" data-loan-form-bkgd></div>
<div class="customer_form" data-loan-form>
	<div class="customer_form_inner_container">
		<?php 
			$stmt = $con->prepare('SELECT id, name, lastname FROM accounts');
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($executiveID, $name, $lastname);
		?>
		<div class="back_btn_container">
			<div class="back_btn" data-back-btn-loanForm>
				<i class="fas fa-chevron-left"></i> Atrás
			</div>
		</div>
		<h2>Forma de préstamo</h2>
		<form action="processes/customerLoan.php?id=<?php echo $id; ?>" method="post">
			<div class="alert">
				<p><b>Atención:</b>Si desea cambiar el nombre de socio bajo el cual se va a registrar este préstamo, seleccione el nombre del socio en esta ventana, de lo contrario ignore este cuadro y continúe con el proceso.</p>
				<select name="select_employee" id="select_employee" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;">
				<option value="<?=$_SESSION['id']?>">Seleccionar socio</option>
				<?php if ($stmt->num_rows == 0): ?>
						<option value="">No usuarios</option>
					<?php else: ?>
					<?php while ($stmt->fetch()): ?>
						<option value="<?=$executiveID?>"><?=$name." ".$lastname?></option>
					<?php endwhile; ?>
					<?php endif; ?>
				</select>
			</div>
			<input type="text" name="folio" id="folio" placeholder="Folio">
			<h4 class="display-plaza"></h4>
			<select name="group" id="group" onmousedown="if(this.options.length>10){this.size=10;}"  onchange="this.size=0; displayPlaza(this);" onblur="this.size=0;" required>
				<?php
					// query to get all groups from the database
					$stmt = $con->prepare('SELECT groups.id, groups.group_name, plazas.plaza_name FROM groups INNER JOIN plazas ON groups.plazaID = plazas.id');
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result($groupListID, $groupListName, $plazaListName);
				?>
				<option selected disabled hidden value="">Seleccionar Grupo</option>
				<?php if ($stmt->num_rows == 0): ?>
				<option value="">No Grupos</option>
				<?php else: ?>
					<?php if ($customer['group_name'] !== ""): ?>
					<option selected hidden value="<?=$customer['group_name']?>"><?=$groupName?></option>
					<?php endif; ?>
					<?php while ($stmt->fetch()): ?>
						<option value="<?=$groupListID?>" data-plaza="<?=$plazaListName?>"><?=$groupListName?></option>
					<?php endwhile; ?>
				<?php endif; ?>
			</select>

			<select name="amount" id="amount" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;" required>
				<option value="">Selecciona cantidad</option>
				<option value="500">500.00 pesos</option>
				<option value="1000">1000.00 pesos</option>
				<option value="1500">1500.00 pesos</option>
				<option value="2000">2000.00 pesos</option>
				<option value="2500">2500.00 pesos</option>
				<option value="3000">3000.00 pesos</option>
				<option value="3500">3500.00 pesos</option>
				<option value="4000">4000.00 pesos</option>
				<option value="4500">4500.00 pesos</option>
				<option value="5000">5000.00 pesos</option>
				<option value="5500">5500.00 pesos</option>
				<option value="6000">6000.00 pesos</option>
				<option value="6500">6500.00 pesos</option>
				<option value="7000">7000.00 pesos</option>
				<option value="7500">7500.00 pesos</option>
				<option value="8000">8000.00 pesos</option>
				<option value="8500">8500.00 pesos</option>
				<option value="9000">9000.00 pesos</option>
				<option value="9500">9500.00 pesos</option>
				<option value="10000">10,000.00 pesos</option>
			</select>
			<button type="submit" name="grantLoan">Otorgar prestamo</button>
		</form>
	</div>
</div>

<div class="customer_form_background" data-pay-form-bkgd></div>
<div class="customer_form" data-pay-form>
	<div class="customer_form_inner_container">
		<?php 
			$stmt = $con->prepare('SELECT id, name, lastname FROM accounts');
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($executiveID, $name, $lastname);

			//payment ammount
			$paymentAmount = $loanPlusInterest / 12;
			//###########################################################
			//*********add only request info if customer is active******
			//###########################################################
		?>
		<div class="top_btns_container">
			<div class="back_btn_container top_btns">
				<div class="back_btn" data-back-btn-payForm>
					<i class="fas fa-chevron-left"></i> Atrás
				</div>
			</div>
		</div>
		
		<h2>Forma de pago</h2>
		<form action="processes/customerLoan.php?id=<?php echo $id; ?>" method="post">
			<div class="alert">
				<p><b>Atención:</b>Si desea cambiar el nombre de socio bajo el cual se va a registrar este préstamo, seleccione el nombre del socio en esta ventana, de lo contrario ignore este cuadro y continúe con el proceso.</p>
				<select name="select_employee" id="select_employee" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;">
				<option value="<?=$_SESSION['id']?>">Seleccionar socio</option>
				<?php if ($stmt->num_rows == 0): ?>
						<option>No usuarios</option>
					<?php else: ?>
					<?php while ($stmt->fetch()): ?>
						<option value="<?=$executiveID?>"><?=$name." ".$lastname?></option>
					<?php endwhile; ?>
					<?php endif; ?>
				</select>
			</div>
			<div class="payment_text">
				<?php if ($paymentAmount < $amountOwed): ?>
					<?php if ( is_float($paymentAmount) && strlen(substr(strrchr($paymentAmount, "."), 1)) == 1 ): ?>
						<span class="payment_amount">$<?=$paymentAmount?>0 pesos</span>
						<span>Cantidad a pagar</span>
						<input type="text" name="payment" value="<?=$paymentAmount?>" readonly="readonly">
					<?php elseif ( is_float($paymentAmount) && strlen(substr(strrchr($paymentAmount, "."), 1)) == 2 ): ?>
						<span class="payment_amount">$<?=$paymentAmount?> pesos</span>
						<span>Cantidad a pagar</span>
						<input type="text" name="payment" value="<?=$paymentAmount?>" readonly="readonly">
					<?php else: ?>
						<span class="payment_amount">$<?=$paymentAmount?>.00 pesos</span>
						<span>Cantidad a pagar</span>
						<input type="text" name="payment" value="<?=$paymentAmount?>" readonly="readonly">
					<?php endif; ?>
				<?php elseif ($paymentAmount >= $amountOwed): ?>
					<?php if (strlen(substr(strrchr($amountOwed, "."), 1)) == 1 ): ?>
						<span class="payment_amount">$<?=$amountOwed?>0 pesos</span>
						<span>Cantidad a pagar</span>
						<input type="text" name="payment" value="<?=$amountOwed?>" readonly="readonly">
					<?php elseif (strlen(substr(strrchr($amountOwed, "."), 1)) == 2 ): ?>
						<span class="payment_amount">$<?=$amountOwed?> pesos</span>
						<span>Cantidad a pagar</span>
						<input type="text" name="payment" value="<?=$amountOwed?>" readonly="readonly">
					<?php else: ?>
						<span class="payment_amount">$<?=$amountOwed?>.00 pesos</span>
						<span>Cantidad a pagar</span>
						<input type="text" name="payment" value="<?=$amountOwed?>" readonly="readonly">
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<select name="payment_punctuality" id="payment_punctuality">
				<!-- 
					1 = late payment
					0 = on time payment 
				-->
				<option value="" selected disabled hidden>Selecciona puntualidad</option>
				<option value="0">Puntual</option>
				<option value="1">Tardío</option>
			</select>
			<button type="submit" name="grantPayment">Hacer pago</button>
			<button class="red" type="submit" name="notPaid">Pago no realizado</button>
		</form>
	</div>
</div>

<div class="customer_form_background" data-renewal-form-bkgd></div>
<div class="customer_form" data-renewal-form>
	<div class="customer_form_inner_container">
		<div class="back_btn_container">
			<div class="back_btn" data-back-btn-renewalForm>
				<i class="fas fa-chevron-left"></i> Atrás
			</div>
		</div>
		<?php
		// week thirteen number meaning
		// 1 = true
		// 0 = false
			if ($weekThirteen == 1):
				//count how many payments have been made with active loan ID
				$stmt = $con->prepare('SELECT COUNT(paymentID) as numOfPayments FROM payments WHERE customerID = ? AND loanID =?');
				$stmt->bind_param('ii', $id, $loanID);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($numOfPayments);
				$stmt->fetch();
				$stmt->close();?>
				<?php if ($numOfPayments >= 12 && $paymentAmount >= $amountOwed): ?>
					<?php 
						$stmt = $con->prepare('SELECT id, name, lastname FROM accounts');
						$stmt->execute();
						$stmt->store_result();
						$stmt->bind_result($executiveID, $name, $lastname);
					?>
					<h2>Forma de renovación</h2>
					<form action="processes/customerLoan.php?id=<?php echo $id; ?>" method="post">
						<div class="alert">
							<p><b>Atención:</b>Si desea cambiar el nombre de socio bajo el cual se va a registrar este préstamo, seleccione el nombre del socio en esta ventana, de lo contrario ignore este cuadro y continúe con el proceso.</p>
							<select name="select_employee" id="select_employee" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;">
							<option value="<?=$_SESSION['id']?>">Seleccionar socio</option>
							<?php if ($stmt->num_rows == 0): ?>
									<option value="">No usuarios</option>
								<?php else: ?>
								<?php while ($stmt->fetch()): ?>
									<option value="<?=$executiveID?>"><?=$name." ".$lastname?></option>
								<?php endwhile; ?>
								<?php endif; ?>
							</select>
						</div>
						<select name="group" id="group" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;" required>
							<?php
								// query to get all groups from the database
								$stmt = $con->prepare('SELECT id, group_name FROM groups');
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($groupListID, $groupListName);
							?>
							<option value="">Seleccionar Grupo</option>
							<?php if ($stmt->num_rows == 0): ?>
							<option value="">No Grupos</option>
							<?php else: ?>
								<?php while ($stmt->fetch()): ?>
									<option value="<?=$groupListID?>"><?=$groupListName?></option>
								<?php endwhile; ?>
							<?php endif; ?>
						</select>

						<select name="amount" id="amount" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;" required>
							<option value="">Selecciona cantidad</option>
							<option value="500">500.00 pesos</option>
							<option value="1000">1000.00 pesos</option>
							<option value="1500">1500.00 pesos</option>
							<option value="2000">2000.00 pesos</option>
							<option value="2500">2500.00 pesos</option>
							<option value="3000">3000.00 pesos</option>
							<option value="3500">3500.00 pesos</option>
							<option value="4000">4000.00 pesos</option>
							<option value="4500">4500.00 pesos</option>
							<option value="5000">5000.00 pesos</option>
							<option value="5500">5500.00 pesos</option>
							<option value="6000">6000.00 pesos</option>
							<option value="6500">6500.00 pesos</option>
							<option value="7000">7000.00 pesos</option>
							<option value="7500">7500.00 pesos</option>
							<option value="8000">8000.00 pesos</option>
							<option value="8500">8500.00 pesos</option>
							<option value="9000">9000.00 pesos</option>
							<option value="9500">9500.00 pesos</option>
							<option value="10000">10,000.00 pesos</option>
						</select>
						<button type="submit" name="grantRenewal">Otorgar renovación</button>
					</form>
				<?php else: echo'<div class="alert"><p><b>Atención:</b> La semana 13 ha sido aplicada en este perfil, por lo tanto debes esperar hasta la semana 12 haciendo los pagos puntualmente para poder ser elegible para una renovación de préstamo.</p></div>';?>
				<?php endif; ?>
			<?php elseif ($weekThirteen == 0):
				//count how many payments have been made with active loan ID
				$stmt = $con->prepare('SELECT COUNT(paymentID) as numOfPayments FROM payments WHERE customerID = ? AND loanID =?');
				$stmt->bind_param('ii', $id, $loanID);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($numOfPayments);
				$stmt->fetch();
				$stmt->close();?>
				<?php if ($numOfPayments >= 10 && $paymentAmount >= $amountOwed): ?>
					<?php 
						$stmt = $con->prepare('SELECT id, name, lastname FROM accounts');
						$stmt->execute();
						$stmt->store_result();
						$stmt->bind_result($executiveID, $name, $lastname);
					?>
					<h2>Forma de renovación</h2>
					<form action="processes/customerLoan.php?id=<?php echo $id; ?>" method="post">
						<div class="alert">
							<p><b>Atención:</b>Si desea cambiar el nombre de socio bajo el cual se va a registrar este préstamo, seleccione el nombre del socio en esta ventana, de lo contrario ignore este cuadro y continúe con el proceso.</p>
							<select name="select_employee" id="select_employee" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;">
							<option value="<?=$_SESSION['id']?>">Seleccionar socio</option>
							<?php if ($stmt->num_rows == 0): ?>
									<option value="">No usuarios</option>
								<?php else: ?>
								<?php while ($stmt->fetch()): ?>
									<option value="<?=$executiveID?>"><?=$name." ".$lastname?></option>
								<?php endwhile; ?>
								<?php endif; ?>
							</select>
						</div>
						<select name="group" id="group" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;" required>
							<?php
								// query to get all groups from the database
								$stmt = $con->prepare('SELECT id, group_name FROM groups');
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($groupListID, $groupListName);
							?>
							<option value="">Seleccionar Grupo</option>
							<?php if ($stmt->num_rows == 0): ?>
							<option value="">No Grupos</option>
							<?php else: ?>
								<?php while ($stmt->fetch()): ?>
									<option value="<?=$groupListID?>"><?=$groupListName?></option>
								<?php endwhile; ?>
							<?php endif; ?>
						</select>

						<select name="amount" id="amount" onmousedown="if(this.options.length>10){this.size=10;}"  onchange='this.size=0;' onblur="this.size=0;" required>
							<option value="">Selecciona cantidad</option>
							<option value="500">500.00 pesos</option>
							<option value="1000">1000.00 pesos</option>
							<option value="1500">1500.00 pesos</option>
							<option value="2000">2000.00 pesos</option>
							<option value="2500">2500.00 pesos</option>
							<option value="3000">3000.00 pesos</option>
							<option value="3500">3500.00 pesos</option>
							<option value="4000">4000.00 pesos</option>
							<option value="4500">4500.00 pesos</option>
							<option value="5000">5000.00 pesos</option>
							<option value="5500">5500.00 pesos</option>
							<option value="6000">6000.00 pesos</option>
							<option value="6500">6500.00 pesos</option>
							<option value="7000">7000.00 pesos</option>
							<option value="7500">7500.00 pesos</option>
							<option value="8000">8000.00 pesos</option>
							<option value="8500">8500.00 pesos</option>
							<option value="9000">9000.00 pesos</option>
							<option value="9500">9500.00 pesos</option>
							<option value="10000">10,000.00 pesos</option>
						</select>
						<button type="submit" name="grantRenewal">Otorgar renovación</button>
					</form>
				<?php else: echo'<div class="alert"><p><b>Atención:</b> La renovación de préstamo solo es posible después de la semana 10 realizando pagos puntuales.</p></div>';?>
				<?php endif; ?>
			<?php endif; ?>
	</div>
</div>

<div class="personal_info_btn_container">
    <button class="personal_info_btn" data-expand-info-btn>
        Expander perfil <i class="fas fa-chevron-down"></i>
    </button>
</div>
<div class="content profile hidden active" data-personal-info-container>
	<div class="error_display">
		<?php
			$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

			if (strpos($fullUrl, "upload=success") == true) {
				echo"<span class='success'>¡La imagen/documento ha sido subido exitosamente!</span>";
			} elseif (strpos($fullUrl, "upload=file-too-big") == true) {
				echo"<span class='fail'>¡El documento es muy grande(10 mb por documento maximo)!</span>";
			} elseif (strpos($fullUrl, "upload=error") == true) {
				echo"<span class='fail'>¡Hubo un error al tratar de subir este documento!</span>";
			} elseif (strpos($fullUrl, "upload=incompatible") == true) {
				echo"<span class='fail'>¡No puedes subir documentos de este tipo (solo JPG y JPEG)!</span>";
			}
		?>
	</div>
	<div class="pic_uploader_container">
		<div class="pic_uploader">
			<?php
				$sql = "SELECT * FROM customers";
				$result = mysqli_query($con, $sql);
				if (mysqli_num_rows($result) > 0) {
						$sqlImg = "SELECT * FROM customers WHERE id='$id'";
						$resultImgOne = mysqli_query($con, $sqlImg);
						while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
							echo "<div class='pic_container'>";
							if ($rowImgOne['imgStatus_1'] == 0) {
								echo "<img src='./customer_uploads/profile1".$id.".jpg' class='profile_pics'>";
							} else {
								echo "<img src='./IMG/profiledefault.jpg' class='profile_pics'>";
							}
						echo "</div>";
						}
				} else {
					echo"No existen usuarios en la base de datos!";
				}
			?>
	<span>Comprobante de domicilio</span>
	<form action="processes/customerUpload.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
		<input type="file" name="file" id="select_file1" hidden>
		<div class="choose_file_container">
			<label for="select_file1">Selecciona documento</label>
			<span id="file_chosen_one">Ningún documento seleccionado</span>
		</div>
		<button type="submit" name="submit1">Guardar Documento</button>
	</form>
	</div>
	<div class="pic_uploader">
	<?php
			$sql = "SELECT * FROM customers";
			$result = mysqli_query($con, $sql);
			if (mysqli_num_rows($result) > 0) {
					$sqlImg = "SELECT * FROM customers WHERE id='$id'";
					$resultImgOne = mysqli_query($con, $sqlImg);
					while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
						echo "<div class='pic_container'>";
						if ($rowImgOne['imgStatus_2'] == 0) {
							echo "<img src='./customer_uploads/profile2".$id.".jpg' class='profile_pics'>";
						} else {
							echo "<img src='./IMG/profiledefault.jpg' class='profile_pics'>";
						}
					echo "</div>";
					}
			}else{
				echo"No existen usuarios en la base de datos!";
			}
		?>
		<span>Comprobante de identificación(INE)</span>
		<form action="processes/customerUpload.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
			<input type="file" name="file" id="select_file2" hidden>
			<div class="choose_file_container">
				<label for="select_file2">Selecciona documento</label>
				<span id="file_chosen_two">Ningún documento seleccionado</span>
			</div>
			<button type="submit" name="submit2">Guardar Documento</button>
		</form>
	</div>
</div>
<p>Información de cuenta:</p>
<span class="description">Acerca de ti:</span>
<table>
	<tr>
		<td>Nombre:</td>
		<td><?=$customer['name']?></td>
	</tr>
	<tr>
		<td>Apellido:</td>
		<td><?=$customer['lastname']?></td>
	</tr>
	<tr>
		<td>Clave de elector:</td>
		<td><?=$customer['ine']?></td>
	</tr>
	<tr>
		<td>Fecha de nacimiento:</td>
		<td><?=$customer['birthday']?></td>
	</tr>
</table>
<span class="description">Información de contacto</span>
<table>
	<tr>
		<td>Correo:</td>
		<td><?=$customer['email']?></td>
	</tr>
	<tr>
		<td>Teléfono:</td>
		<td><?=$customer['phone']?></td>
	</tr>
	<tr>
		<td>Domicilio:</td>
		<td><?=$customer['address']?></td>
	</tr>
	<tr>
		<td>Ciudad:</td>
		<td><?=$customer['city']?></td>
	</tr>
	<tr>
		<td>Estado:</td>
		<td><?=$customer['state']?></td>
	</tr>
</table>
<span class="description">Información de colaborador</span>
<table>
	<tr>
		<td>Código de identificación:</td>
		<td><?=$_GET['id']?></td>
	</tr>
	<tr>
		<td>Nombre de grupo:</td>
		<?php if ($customer['group_name'] == ''): ?>
			<td>No tiene grupo asignado.</td>
		<?php else: ?>
			<?php
			$stmt = $con->prepare('SELECT id, group_name FROM groups WHERE id = ?');
			$stmt->bind_param('i', $customer['group_name']);
			$stmt->execute();
			$stmt->bind_result($groupID, $groupName);
			$stmt->fetch();
			$stmt->close();
        	?>
			<td><?=$groupID." | ".$groupName?></td>
		<?php endif; ?>
	</tr>
</table>
<p>Información de Aval:</p>
<div class="pic_uploader_container">
	<div class="pic_uploader">
		<?php
			$sql = "SELECT * FROM customers";
			$result = mysqli_query($con, $sql);
			if (mysqli_num_rows($result) > 0) {
					$sqlImg = "SELECT * FROM customers WHERE id='$id'";
					$resultImgOne = mysqli_query($con, $sqlImg);
					while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
						echo "<div class='pic_container'>";
						if ($rowImgOne['guar_imgStatus_1'] == 0) {
							echo "<img src='./customer_uploads/guarantor1".$id.".jpg' class='profile_pics'>";
						} else {
							echo "<img src='./IMG/profiledefault.jpg' class='profile_pics'>";
						}
					echo "</div>";
					}
			} else {
				echo"No existen usuarios en la base de datos!";
			}
		?>
		<span>Comprobante de domicilio</span>
		<form action="processes/customerUpload.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
			<input type="file" name="file" id="select_file3" hidden>
			<div class="choose_file_container">
				<label for="select_file3">Selecciona documento</label>
				<span id="file_chosen_three">Ningún documento seleccionado</span>
			</div>
			<button type="submit" name="submit3">Guardar Documento</button>
		</form>
	</div>
	<div class="pic_uploader">
	<?php
			$sql = "SELECT * FROM customers";
			$result = mysqli_query($con, $sql);
			if (mysqli_num_rows($result) > 0) {
					$sqlImg = "SELECT * FROM customers WHERE id='$id'";
					$resultImgOne = mysqli_query($con, $sqlImg);
					while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
						echo "<div class='pic_container'>";
						if ($rowImgOne['guar_imgStatus_2'] == 0) {
							echo "<img src='./customer_uploads/guarantor2".$id.".jpg' class='profile_pics'>";
						} else {
							echo "<img src='./IMG/profiledefault.jpg' class='profile_pics'>";
						}
					echo "</div>";
					}
			}else{
				echo"No existen usuarios en la base de datos!";
			}
		?>
		<span>Comprobante de identificación(INE)</span>
		<form action="processes/customerUpload.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
			<input type="file" name="file" id="select_file4" hidden>
			<div class="choose_file_container">
				<label for="select_file4">Selecciona documento</label>
				<span id="file_chosen_four">Ningún documento seleccionado</span>
			</div>
			<button type="submit" name="submit4">Guardar Documento</button>
		</form>
	</div>
</div>
<table>
	<tr>
		<td>Nombre:</td>
		<td><?=$customer['guar_name']?></td>
	</tr>
	<tr>
		<td>Apellido:</td>
		<td><?=$customer['guar_lastname']?></td>
	</tr>
	<tr>
		<td>Teléfono:</td>
		<td><?=$customer['guar_phone']?></td>
	</tr>
	<tr>
		<td>Domicilio:</td>
		<td><?=$customer['guar_address']?></td>
	</tr>
	<tr>
		<td>Ciudad:</td>
		<td><?=$customer['guar_city']?></td>
	</tr>
	<tr>
		<td>Estado:</td>
		<td><?=$customer['guar_state']?></td>
	</tr>
	<tr>
		<td>Clave de elector:</td>
		<td><?=$customer['guar_ine']?></td>
	</tr>
</table>
<div class="links">
	<a class ="profile_btn" onclick="location.href='addCustomer.php?id=<?=$id?>'" >Editar Perfil</a>
</div>
</div>
    <script>
        //loan window opener and closer
        const loanFormBkgd = document.querySelector('[data-loan-form-bkgd]');
        const loanForm = document.querySelector('[data-loan-form]');
        const loanBtn = document.querySelector('[data-loan-btn]');
		const closeLoanBtn = document.querySelector('[data-back-btn-loanForm]');

        loanBtn.addEventListener('click', () => {
            loanFormBkgd.classList.add('active');
            loanForm.classList.add('active');
        });
        loanFormBkgd.addEventListener('click', () => {
            loanFormBkgd.classList.remove('active');
            loanForm.classList.remove('active');

        });
		closeLoanBtn.addEventListener('click', () => {
			loanFormBkgd.classList.remove('active');
            loanForm.classList.remove('active');
		});

		//Payment window opener and closer
		const payFormBkgd = document.querySelector('[data-pay-form-bkgd');
		const payForm = document.querySelector('[data-pay-form]');
		const payBtn = document.querySelector('[data-pay-btn]');
		const closePayBtn = document.querySelector('[data-back-btn-payForm]');

		payBtn.addEventListener('click', () => {
			payFormBkgd.classList.add('active');
			payForm.classList.add('active');
		});
        payFormBkgd.addEventListener('click', () => {
			payFormBkgd.classList.remove('active');
			payForm.classList.remove('active');
		});
		closePayBtn.addEventListener('click', () => {
			payFormBkgd.classList.remove('active');
			payForm.classList.remove('active');
		});

		//Renewal window opener and closer
		const renewFormBkgd = document.querySelector('[data-renewal-form-bkgd');
		const renewForm = document.querySelector('[data-renewal-form]');
		const renewBtn = document.querySelector('[data-renewal-btn]');
		const closeRenewBtn = document.querySelector('[data-back-btn-renewalForm]');

		renewBtn.addEventListener('click', () => {
			renewFormBkgd.classList.add('active');
			renewForm.classList.add('active');
		});
        renewFormBkgd.addEventListener('click', () => {
			renewFormBkgd.classList.remove('active');
			renewForm.classList.remove('active');
		});
		closeRenewBtn.addEventListener('click', () => {
			renewFormBkgd.classList.remove('active');
			renewForm.classList.remove('active');
		});

		//expand customers payment list
		const paymentHistoryContainer = document.querySelector('[data-payment-history]');
		const expandBtn = document.querySelector('[data-expandPaymentHistory-btn]');
		
		expandBtn.addEventListener('click', () =>{
			paymentHistoryContainer.classList.toggle('active');
		});
        //detailed profile opener
        const expandPersonalInfoBtn = document.querySelector('[data-expand-info-btn]');
        const personalInfoContainer = document.querySelector('[data-personal-info-container]');

        expandPersonalInfoBtn.addEventListener('click', () => {
            if (personalInfoContainer.classList.contains('active')) {
                expandPersonalInfoBtn.innerHTML = "Cerrar perfil <i class='fas fa-chevron-up'></i>";
                personalInfoContainer.classList.toggle('active');
            } else if (!personalInfoContainer.classList.contains('active')) {
                expandPersonalInfoBtn.innerHTML = "Expandir perfil <i class='fas fa-chevron-down'></i>";
                personalInfoContainer.classList.toggle('active');
            }
        });

        //custom file searcher 
        const selectFileOne = document.getElementById('select_file1');
        const fileChosenOne = document.getElementById('file_chosen_one');
        const selectFileTwo = document.getElementById('select_file2');
        const fileChosenTwo = document.getElementById('file_chosen_two');
        const selectFileThree = document.getElementById('select_file3');
        const fileChosenThree = document.getElementById('file_chosen_three');
        const selectFileFour = document.getElementById('select_file4');
        const fileChosenFour = document.getElementById('file_chosen_four');

        selectFileOne.addEventListener('change', function() {
        fileChosenOne.textContent = this.files[0].name;
        });
        selectFileTwo.addEventListener('change', function() {
        fileChosenTwo.textContent = this.files[0].name;
        });
        selectFileThree.addEventListener('change', function() {
        fileChosenThree.textContent = this.files[0].name;
        });
        selectFileFour.addEventListener('change', function() {
        fileChosenFour.textContent = this.files[0].name;
        });

		//Plaza display on loan select change
		function displayPlaza(data) {
			let plaza = data.options[data.selectedIndex].getAttribute('data-plaza');
			const displayPlaza = document.querySelector('.display-plaza');
			
			displayPlaza.textContent = "Plaza: " + plaza;
		}
    </script>
    <script src="./JS/menu.js"></script>
</body>
</html>