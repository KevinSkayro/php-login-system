<?php
include 'processes/dbhandler.php';
check_loggedin($con);
// output message (errors, etc)
$msg = '';
// We don't have the password or email info stored in sessions so instead we can get the results from the database.
$stmt = $con->prepare('SELECT name, lastname, ine, birthday, phone, address, city, state, password, email, guar_name, guar_lastname, guar_phone, guar_address, guar_city, guar_state, guar_ine, activation_code, role, plaza_name FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($name, $lastname, $ine, $dob, $phone, $address, $city, $state, $password, $email, $guarName, $guarLastname, $guarPhone, $guarAddress, $guarCity, $guarState, $guarIne, $activation_code, $role, $plazaName);
$stmt->fetch();
$stmt->close();
// Handle edit profile post data
if (isset($_POST['name'], $_POST['newPassword'], $_POST['repeatPwd'], $_POST['email'])) {
	// Make sure the submitted registration values are not empty.
	if (empty($_POST['name']) || empty($_POST['email'])) {
		$msg = 'The input fields must not be empty!';
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$msg = 'Please provide a valid email address!';
	} else if (!empty($_POST['newPassword']) && (strlen($_POST['repeatPwd']) > 20 || strlen($_POST['newPassword']) < 5)) {
		$msg = 'Password must be between 5 and 20 characters long!';
	} else if ($_POST['repeatPwd'] != $_POST['newPassword']) {
		$msg = 'Passwords do not match!';
	}
	if (empty($msg)) {
		// Check if new username or email already exists in database
		$stmt = $con->prepare('SELECT * FROM accounts WHERE (ine = ? OR email = ?) AND ine != ? AND email != ?');
		$stmt->bind_param('ssss', $_POST['ine'], $_POST['email'], $_SESSION['id'], $email);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			$msg = 'INE y/o correo ya existe en la base de datos';
		} else {
			//no errors occured, update the account...
			$stmt->close();
			$uniqid = account_activation && $email != $_POST['email'] ? uniqid() : $activation_code;
			$stmt = $con->prepare('UPDATE accounts SET name = ?, lastname = ?, ine = ?, birthday = ?, phone = ?, address = ?, city = ?, state = ?, password = ?, email = ?, guar_name = ?, guar_lastname = ?, guar_phone = ?, guar_address = ?, guar_city = ?, guar_state = ?, guar_ine = ?, activation_code = ? WHERE id = ?');
			// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
			$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $password;
			//convert the first letter of every word to uppercase and uppercase every letter on the INE to keep a consistency in the database
			$upperName = ucwords($_POST['name']);
			$upperLastname = ucwords($_POST['lastname']);
			$upperIne = strtoupper($_POST['ine']);
			$upperAddress = ucwords($_POST['address']);
			$upperCity = ucwords($_POST['city']);
			$upperState = ucwords($_POST['state']);
			$upperGuarName = ucwords($_POST['guar_name']);
			$upperGuarLastname = ucwords($_POST['guar_lastname']);
			$upperGuarAddress = ucwords($_POST['guar_address']);
			$upperGuarCity = ucwords($_POST['guar_city']);
			$upperGuarState = ucwords($_POST['guar_state']);
			$upperGuarIne = strtoupper($_POST['guar_ine']);
			$stmt->bind_param('ssssssssssssssssssi', $upperName, $upperLastname, $upperIne, $_POST['birthday'], $_POST['phone'], $upperAddress, $upperCity, $upperState, $password, $_POST['email'], $upperGuarName, $upperGuarLastname, $_POST['guar_phone'], $upperGuarAddress, $upperGuarCity, $upperGuarState, $upperGuarIne, $uniqid, $_SESSION['id']);
			$stmt->execute();
			$stmt->close();
			// Update the session variables
			$_SESSION['name'] = $_POST['name'];
			if (account_activation && $email != $_POST['email']) {
				// Account activation required, send the user the activation email with the "send_activation_email" function from the "main.php" file
				send_activation_email($_POST['email'], $uniqid);
				// Log the user out
				unset($_SESSION['loggedin']);
				$msg = 'You have changed your email address, you need to re-activate your account!';
			} else {
				// profile updated redirect the user back to the profile page and not the edit profile page
				header('Location: profile.php');
				exit;
			}
		}
	}
}
include_once  'header.php';
?>
	<?php if (!isset($_GET['action'])): ?>
		<div class="content profile">
			<h2>Perfil</h2>
			<div class="block">
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
							$sql = "SELECT * FROM accounts";
							$result = mysqli_query($con, $sql);
							if (mysqli_num_rows($result) > 0) {
									$id = $_SESSION['id'];
									$sqlImg = "SELECT * FROM accounts WHERE id='$id'";
									$resultImgOne = mysqli_query($con, $sqlImg);
									while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
										echo "<div class='pic_container'>";
										if ($rowImgOne['imgStatus_1'] == 0) {
											echo "<img src='./employee_uploads/profile1".$id.".jpg' class='profile_pics'>";
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
						<form action="processes/upload.php" method="post" enctype="multipart/form-data">
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
							$sql = "SELECT * FROM accounts";
							$result = mysqli_query($con, $sql);
							if (mysqli_num_rows($result) > 0) {
								// while($row = mysqli_fetch_assoc($result)){
									$id = $_SESSION['id'];
									$sqlImg = "SELECT * FROM accounts WHERE id='$id'";
									$resultImgOne = mysqli_query($con, $sqlImg);
									while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
										echo "<div class='pic_container'>";
										if ($rowImgOne['imgStatus_2'] == 0) {
											echo "<img src='./employee_uploads/profile2".$id.".jpg' class='profile_pics'>";
										} else {
											echo "<img src='./IMG/profiledefault.jpg' class='profile_pics'>";
										}
									echo "</div>";
									}
								// }
							} else {
								echo"No existen usuarios en la base de datos!";
							}
						?>
						<span>Comprobante de identificación(INE)</span>
						<form action="processes/upload.php" method="post" enctype="multipart/form-data">
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
						<td><?=$name?></td>
					</tr>
					<tr>
						<td>Apellido:</td>
						<td><?=$lastname?></td>
					</tr>
					<tr>
						<td>Clave de elector:</td>
						<td><?=$ine?></td>
					</tr>
					<tr>
						<td>Fecha de nacimiento:</td>
						<td><?=$dob?></td>
					</tr>
				</table>
				<span class="description">Información de contacto</span>
				<table>
					<tr>
						<td>Correo:</td>
						<td><?=$email?></td>
					</tr>
					<tr>
						<td>Teléfono:</td>
						<td><?=$phone?></td>
					</tr>
					<tr>
						<td>Domicilio:</td>
						<td><?=$address?></td>
					</tr>
					<tr>
						<td>Ciudad:</td>
						<td><?=$city?></td>
					</tr>
					<tr>
						<td>Estado:</td>
						<td><?=$state?></td>
					</tr>
				</table>
				<span class="description">Información de colaborador</span>
				<table>
					<tr>
						<td>Código de identificación:</td>
						<td><?=$_SESSION['id']?></td>
					</tr>
					<tr>
						<td>Puesto de trabajo:</td>
						<td><?=$role?></td>
					</tr>
					<tr>
						<?php if ($role == 'Miembro'): ?>
							<td>Nombre de grupo:</td>
						<?php else: ?>
							<td>Nombre de plaza(s):</td>
						<?endif; ?>
						<?php
						if ($role == 'Ejecutivo') {
							$roleArray = explode(',', $plazaName);
							$counter = count($roleArray) - 1;
							if ($counter == 1) {
								$stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE id = ?');
								$stmt->bind_param('i', $roleArray[0]);
								$stmt->execute();
								$stmt->bind_result($plazaNames);
								$stmt->fetch();
								$stmt->close();
								echo '<td>' . $plazaNames . '</td>';
							} else if ($counter == 2) {
								$stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE id = ?');
								$stmt->bind_param('i', $roleArray[0]);
								$stmt->execute();
								$stmt->bind_result($plazaNames1);
								$stmt->fetch();
								$stmt->close();
								$stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE id = ?');
								$stmt->bind_param('i', $roleArray[1]);
								$stmt->execute();
								$stmt->bind_result($plazaNames2);
								$stmt->fetch();
								$stmt->close();
								echo '<td>' . $plazaNames1 . '<br>' . $plazaNames2 . '</td>';
							} else if ($counter == 3) {
								$stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE id = ?');
								$stmt->bind_param('i', $roleArray[0]);
								$stmt->execute();
								$stmt->bind_result($plazaNames1);
								$stmt->fetch();
								$stmt->close();
								$stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE id = ?');
								$stmt->bind_param('i', $roleArray[1]);
								$stmt->execute();
								$stmt->bind_result($plazaNames2);
								$stmt->fetch();
								$stmt->close();
								$stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE id = ?');
								$stmt->bind_param('i', $roleArray[2]);
								$stmt->execute();
								$stmt->bind_result($plazaNames3);
								$stmt->fetch();
								$stmt->close();
								echo '<td>' . $plazaNames1 . '<br>' . $plazaNames2 . '<br>' . $plazaNames3 . '</td>';
							} else if ($counter == 4) {
								$stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE id = ?');
								$stmt->bind_param('i', $roleArray[0]);
								$stmt->execute();
								$stmt->bind_result($plazaNames1);
								$stmt->fetch();
								$stmt->close();
								$stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE id = ?');
								$stmt->bind_param('i', $roleArray[1]);
								$stmt->execute();
								$stmt->bind_result($plazaNames2);
								$stmt->fetch();
								$stmt->close();
								$stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE id = ?');
								$stmt->bind_param('i', $roleArray[2]);
								$stmt->execute();
								$stmt->bind_result($plazaNames3);
								$stmt->fetch();
								$stmt->close();
								$stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE id = ?');
								$stmt->bind_param('i', $roleArray[3]);
								$stmt->execute();
								$stmt->bind_result($plazaNames4);
								$stmt->fetch();
								$stmt->close();
								echo '<td>'.$plazaNames1.'<br>'.$plazaNames2.'<br>'.$plazaNames3.'<br>'.$plazaNames4.'</td>';
							}
						}else if ($role == 'Miembro') {
							$stmt = $con->prepare('SELECT group_name FROM groups WHERE id = ?');
							$stmt->bind_param('i', $plazaName);
							$stmt->execute();
							$stmt->bind_result($groupName);
							$stmt->fetch();
							$stmt->close();
							echo "<td>$groupName</td>";
				
						} else {
							$stmt = $con->prepare('SELECT plaza_name FROM plazas WHERE id = ?');
							$stmt->bind_param('i', $plazaName);
							$stmt->execute();
							$stmt->bind_result($plazaName);
							$stmt->fetch();
							$stmt->close();
							echo "<td>$plazaName</td>";
						}
						?>
					</tr>
				</table>
				<p>Información de Aval:</p>
				<div class="pic_uploader_container">
					<div class="pic_uploader">
						<?php
							$sql = "SELECT * FROM accounts";
							$result = mysqli_query($con, $sql);
							if (mysqli_num_rows($result) > 0) {
								// while($row = mysqli_fetch_assoc($result)){
									$id = $_SESSION['id'];
									$sqlImg = "SELECT * FROM accounts WHERE id='$id'";
									$resultImgOne = mysqli_query($con, $sqlImg);
									while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
										echo "<div class='pic_container'>";
										if ($rowImgOne['guar_imgStatus_1'] == 0) {
											echo "<img src='./employee_uploads/guarantor1".$id.".jpg' class='profile_pics'>";
										} else {
											echo "<img src='./IMG/profiledefault.jpg' class='profile_pics'>";
										}
									echo "</div>";
									}
								// }
							} else {
								echo"No existen usuarios en la base de datos!";
							}
						?>
						<span>Comprobante de domicilio</span>
						<form action="processes/upload.php" method="post" enctype="multipart/form-data">
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
							$sql = "SELECT * FROM accounts";
							$result = mysqli_query($con, $sql);
							if (mysqli_num_rows($result) > 0) {
								// while($row = mysqli_fetch_assoc($result)){
									$id = $_SESSION['id'];
									$sqlImg = "SELECT * FROM accounts WHERE id='$id'";
									$resultImgOne = mysqli_query($con, $sqlImg);
									while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
										echo "<div class='pic_container'>";
										if ($rowImgOne['guar_imgStatus_2'] == 0) {
											echo "<img src='./employee_uploads/guarantor2".$id.".jpg' class='profile_pics'>";
										} else {
											echo "<img src='./IMG/profiledefault.jpg' class='profile_pics'>";
										}
									echo "</div>";
									}
								// }
							} else {
								echo"No existen usuarios en la base de datos!";
							}
						?>
						<span>Comprobante de identificación(INE)</span>
						<form action="processes/upload.php" method="post" enctype="multipart/form-data">
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
						<td><?=$guarName?></td>
					</tr>
					<tr>
						<td>Apellido:</td>
						<td><?=$guarLastname?></td>
					</tr>
					<tr>
						<td>Teléfono:</td>
						<td><?=$guarPhone?></td>
					</tr>
					<tr>
						<td>Domicilio:</td>
						<td><?=$guarAddress?></td>
					</tr>
					<tr>
						<td>Ciudad:</td>
						<td><?=$guarCity?></td>
					</tr>
					<tr>
						<td>Estado:</td>
						<td><?=$guarState?></td>
					</tr>
					<tr>
						<td>Clave de elector:</td>
						<td><?=$guarIne?></td>
					</tr>
				</table>
				<a class="profile_btn" href="profile.php?action=edit">Editar Perfil</a>
			</div>
		</div>
	<?php elseif ($_GET['action'] == 'edit'): ?>
		<div class="content profile">
			<h2>Edición de perfil</h2>
			<div class="note">
				<span><i class="fas fa-exclamation-triangle"></i>IMPORTANTE:  solo llena los  espacios  "Contraseña"  y  "Confirma contraseña"  si deseas  cambiar la  contraseña,  de lo contrario  solo actualiza  los espacios  que  deseas  modificar  y guarda los cambios.</span>
			</div>
			<div class="block">
				<form action="profile.php?action=edit" method="post">
					<p>Acerca de ti:</p>
					<label for="name">Nombre</label>
					<input type="text" value="<?=$name?>" name="name" id="name" placeholder="Nombre(s)">

					<label for="lastname">Apellido</label>
					<input type="text" value="<?=$lastname?>" name="lastname" id="lastname" placeholder="Apellidos">

					<label for="ine">Clave de elector</label>
					<input type="text" value="<?=$ine?>" name="ine" id="ine" placeholder="Clave de elector">

					<label for="birthday">Fecha de nacimiento</label>
					<input type="text" value="<?=$dob?>" name="birthday" id="dob" placeholder="DD-MM-AAAA">

					<p>Información de contacto:</p>
					<label for="email">Correo</label>
					<input type="text" value="<?=$email?>" name="email" id="email" placeholder="ejemplo@correo.com">
					
					<label for="phone">Teléfono</label>
					<input type="text" value="<?=$phone?>" name="phone" id="phone" placeholder="Numero de Teléfono">

					<label for="address">Domicilio</label>
					<input type="text" value="<?=$address?>" name="address" id="address" placeholder="Domicilio(ejemplo #123)">

					<label for="city">Ciudad</label>
					<input type="text" value="<?=$city?>" name="city" id="city" placeholder="Ciudad/Pueblo">

					<label for="state">Estado</label>
					<input type="text" value="<?=$state?>" name="state" id="state" placeholder="Estado">
					
					<p>Editar contraseña:</p>
					<label for="password">Contraseña</label>
					<input type="password" name="newPassword" id="password" placeholder="Contraseña">
					
					<label for="cpassword">Confirma contraseña</label>
					<input type="password" name="repeatPwd" id="cpassword" placeholder="Confirma contraseña">
					
					<p>Información de Aval:</p>
					<label for="guar_name">Nombre</label>
					<input type="text" value="<?=$guarName?>" name="guar_name" id="guar_name" placeholder="Nombre(s) de aval">

					<label for="guar_lastname">Apellido</label>
					<input type="text" value="<?=$guarLastname?>" name="guar_lastname" id="guar_lastname" placeholder="Apellidos de aval">
					
					<label for="guar_phone">Teléfono</label>
					<input type="text" value="<?=$guarPhone?>" name="guar_phone" id="guar_phone" placeholder="Numero de Teléfono">

					<label for="guar_address">Domicilio</label>
					<input type="text" value="<?=$guarAddress?>" name="guar_address" id="guar_address" placeholder="Domicilio(ejemplo #123)">

					<label for="guar_city">Ciudad</label>
					<input type="text" value="<?=$guarCity?>" name="guar_city" id="guar_city" placeholder="Ciudad/Pueblo">

					<label for="guar_state">Estado</label>
					<input type="text" value="<?=$guarState?>" name="guar_state" id="guar_state" placeholder="Estado">

					<label for="guar_ine">Clave de elector</label>
					<input type="text" value="<?=$guarIne?>" name="guar_ine" id="guar_ine" placeholder="Clave de elector">


					<br>
					<input class="profile_btn" type="submit" value="Guardar">
					<span><?=$msg?></span>
				</form>
			</div>
		</div>
		<?php endif; ?>
		<script>
			const selectFileOne = document.getElementById("select_file1");
			const fileChosenOne = document.getElementById("file_chosen_one");
			const selectFileTwo = document.getElementById("select_file2");
			const fileChosenTwo = document.getElementById("file_chosen_two");
			const selectFileThree = document.getElementById("select_file3");
			const fileChosenThree = document.getElementById("file_chosen_three");
			const selectFileFour = document.getElementById("select_file4");
			const fileChosenFour = document.getElementById("file_chosen_four");

			selectFileOne.addEventListener("change", function() {
  			fileChosenOne.textContent = this.files[0].name;
			});
			selectFileTwo.addEventListener("change", function() {
  			fileChosenTwo.textContent = this.files[0].name;
			});
			selectFileThree.addEventListener("change", function() {
  			fileChosenThree.textContent = this.files[0].name;
			});
			selectFileFour.addEventListener("change", function() {
  			fileChosenFour.textContent = this.files[0].name;
			});
		</script>
		<script src="./JS/menu.js"></script>
	</body>
</html>