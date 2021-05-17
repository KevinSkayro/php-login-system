<?php
include 'main.php';
// Default input product values
$account = array(
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
    'activation_code' => '',
    'rememberme' => '',
    'role' => 'Member',
    'guar_name' => '',
    'guar_lastname' => '',
    'guar_phone' => '',
    'guar_address' => '',
    'guar_city' => '',
    'guar_state' => '',
    'guar_ine' => '',
	'group_name' =>''
);
$id = $_GET['id'];
if (isset($_GET['id'])) {
    // Get the account from the database
    $stmt = $con->prepare('SELECT name, lastname, ine, birthday, password, email, phone, address, city, state, activation_code, rememberme, role, 
        guar_name, guar_lastname, guar_phone, guar_address, guar_city, guar_state, guar_ine, group_name  FROM accounts WHERE id = ?');
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $stmt->bind_result($account['name'], $account['lastname'], $account['ine'], $account['birthday'], $account['password'], $account['email'], 
        $account['phone'], $account['address'], $account['city'], $account['state'], $account['activation_code'], $account['rememberme'], $account['role'], 
        $account['guar_name'], $account['guar_lastname'], $account['guar_phone'], $account['guar_address'], $account['guar_city'], $account['guar_state'], 
        $account['guar_ine'], $account['group_name']);
    $stmt->fetch();
    $stmt->close();

} else {
    // If id doesn't return it does not exist
    echo'Esta cuenta no existe';
    
}
?>
<?=template_admin_header('Perfil')?>
<h2>Perfil de <?=$account['name'].', '.$id?></h2>
<div class="content profile">
<div class="error_display">
	<?php
		$fullUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		if (strpos($fullUrl, "upload=success") == true){
			echo"<span class='success'>¡La imagen/documento ha sido subido exitosamente!</span>";
		}elseif(strpos($fullUrl, "upload=file-too-big") == true){
			echo"<span class='fail'>¡El documento es muy grande(10 mb por documento maximo)!</span>";
		}elseif(strpos($fullUrl, "upload=error") == true){
			echo"<span class='fail'>¡Hubo un error al tratar de subir este documento!</span>";
		}elseif(strpos($fullUrl, "upload=incompatible") == true){
			echo"<span class='fail'>¡No puedes subir documentos de este tipo (solo JPG y JPEG)!</span>";
		}
	?>
</div>
<div class="pic_uploader_container">
	<div class="pic_uploader">
		<?php
			$sql = "SELECT * FROM accounts";
			$result = mysqli_query($con, $sql);
			if(mysqli_num_rows($result) > 0){
					$sqlImg = "SELECT * FROM accounts WHERE id='$id'";
					$resultImgOne = mysqli_query($con, $sqlImg);
					while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
						echo "<div class='pic_container'>";
						if($rowImgOne['imgStatus_1'] == 0){
							echo "<img src='../employee_uploads/profile1".$id.".jpg' class='profile_pics'>";
						}else{
							echo "<img src='../IMG/profiledefault.jpg' class='profile_pics'>";
						}
					echo "</div>";
					}
			}else{
				echo"No existen usuarios en la base de datos!";
			}
		?>
		<span>Comprobante de domicilio</span>
		<form action="upload.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
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
			if(mysqli_num_rows($result) > 0){
					$sqlImg = "SELECT * FROM accounts WHERE id='$id'";
					$resultImgOne = mysqli_query($con, $sqlImg);
					while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
						echo "<div class='pic_container'>";
						if($rowImgOne['imgStatus_2'] == 0){
							echo "<img src='../employee_uploads/profile2".$id.".jpg' class='profile_pics'>";
						}else{
							echo "<img src='../IMG/profiledefault.jpg' class='profile_pics'>";
						}
					echo "</div>";
					}
			}else{
				echo"No existen usuarios en la base de datos!";
			}
		?>
		<span>Comprobante de identificación(INE)</span>
		<form action="upload.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
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
		<td><?=$account['name']?></td>
	</tr>
	<tr>
		<td>Apellido:</td>
		<td><?=$account['lastname']?></td>
	</tr>
	<tr>
		<td>Clave de elector:</td>
		<td><?=$account['ine']?></td>
	</tr>
	<tr>
		<td>Fecha de nacimiento:</td>
		<td><?=$account['birthday']?></td>
	</tr>
</table>
<span class="description">Información de contacto</span>
<table>
	<tr>
		<td>Correo:</td>
		<td><?=$account['email']?></td>
	</tr>
	<tr>
		<td>Teléfono:</td>
		<td><?=$account['phone']?></td>
	</tr>
	<tr>
		<td>Domicilio:</td>
		<td><?=$account['address']?></td>
	</tr>
	<tr>
		<td>Ciudad:</td>
		<td><?=$account['city']?></td>
	</tr>
	<tr>
		<td>Estado:</td>
		<td><?=$account['state']?></td>
	</tr>
</table>
<span class="description">Información de colaborador</span>
<table>
	<tr>
		<td>Código de identificación:</td>
		<td><?=$_GET['id']?></td>
	</tr>
	<tr>
		<td>Puesto de trabajo:</td>
		<td><?=$account['role']?></td>
	</tr>
	<tr>
		<td>Nombre de grupo:</td>
		<td><?=$account['group_name']?></td>
	</tr>
</table>
<p>Información de Aval:</p>
<div class="pic_uploader_container">
	<div class="pic_uploader">
		<?php
			$sql = "SELECT * FROM accounts";
			$result = mysqli_query($con, $sql);
			if(mysqli_num_rows($result) > 0){
					$sqlImg = "SELECT * FROM accounts WHERE id='$id'";
					$resultImgOne = mysqli_query($con, $sqlImg);
					while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
						echo "<div class='pic_container'>";
						if($rowImgOne['guar_imgStatus_1'] == 0){
							echo "<img src='../employee_uploads/guarantor1".$id.".jpg' class='profile_pics'>";
						}else{
							echo "<img src='../IMG/profiledefault.jpg' class='profile_pics'>";
						}
					echo "</div>";
					}
			}else{
				echo"No existen usuarios en la base de datos!";
			}
		?>
		<span>Comprobante de domicilio</span>
		<form action="upload.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
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
			if(mysqli_num_rows($result) > 0){
					$sqlImg = "SELECT * FROM accounts WHERE id='$id'";
					$resultImgOne = mysqli_query($con, $sqlImg);
					while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
						echo "<div class='pic_container'>";
						if($rowImgOne['guar_imgStatus_2'] == 0){
							echo "<img src='../employee_uploads/guarantor2".$id.".jpg' class='profile_pics'>";
						}else{
							echo "<img src='../IMG/profiledefault.jpg' class='profile_pics'>";
						}
					echo "</div>";
					}
			}else{
				echo"No existen usuarios en la base de datos!";
			}
		?>
		<span>Comprobante de identificación(INE)</span>
		<form action="upload.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
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
		<td><?=$account['guar_name']?></td>
	</tr>
	<tr>
		<td>Apellido:</td>
		<td><?=$account['guar_lastname']?></td>
	</tr>
	<tr>
		<td>Teléfono:</td>
		<td><?=$account['guar_phone']?></td>
	</tr>
	<tr>
		<td>Domicilio:</td>
		<td><?=$account['guar_address']?></td>
	</tr>
	<tr>
		<td>Ciudad:</td>
		<td><?=$account['guar_city']?></td>
	</tr>
	<tr>
		<td>Estado:</td>
		<td><?=$account['guar_state']?></td>
	</tr>
	<tr>
		<td>Clave de elector:</td>
		<td><?=$account['guar_ine']?></td>
	</tr>
</table>
<div class="links">
	<a onclick="location.href='account.php?id=<?=$id?>'" >Editar Perfil</a>
</div>
</div>
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
<?=template_admin_footer()?>