<?php
include 'dbhandler.php';
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
        guar_name, guar_lastname, guar_phone, guar_address, guar_city, guar_state, guar_ine, group_name, customerStatus FROM customers WHERE id = ?');
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $stmt->bind_result($customer['name'], $customer['lastname'], $customer['ine'], $customer['birthday'], $customer['email'], 
        $customer['phone'], $customer['address'], $customer['city'], $customer['state'], 
        $customer['guar_name'], $customer['guar_lastname'], $customer['guar_phone'], $customer['guar_address'], $customer['guar_city'], $customer['guar_state'], 
        $customer['guar_ine'], $customer['group_name'], $customer['customerStatus']);
    $stmt->fetch();
    $stmt->close();

} else {
    // If id doesn't return it does not exist
    echo'Esta cuenta no existe';
    
}
include_once  'header.php';
?>
<div class="loan_form_background">

</div>
<div class="loan_form">
<div class="loan_form_inner_container">
    <h2>Forma de préstamo</h2>

    <div class="alert">
        <p><b>Atención:</b>Si desea cambiar el nombre de socio bajo el cual se va a registrar este préstamo, seleccione el nombre del socio en esta ventana, de lo contrario ignore este cuadro y continúe con el proceso.</p>
    </div>

</div>
</div>
<div class="header">
    <h2>Perfil de cliente: <?=$customer['name'].' '.$customer['lastname']?></h2>
</div>
<div class="content profile">
    <div class="current_balance_container">
        <div class="current_balance">
            <?php if ($customer['customerStatus'] == '0'): ?>
                <span>$0.00 pesos</span>

			<?php elseif ($customer['customerStatus'] == '1'): ?>
				<span>to add next</span>
			<?php endif; ?>
            
            <span class="under_text">Cantidad total a pagar</span>
        </div>
    </div>
    <div class="btns_container">
        <?php if ($customer['customerStatus'] == '0'): ?>
		    <button class="loan_btn">Iniciar prestamo</button>
        <?php elseif ($customer['customerStatus'] == '1'): ?>
            <button>Hacer pago</button>
            <button>Renovar prestamo</button>
	    <?php endif; ?>
    </div>
</div>
<div class="personal_info_btn_container">
    <button class="personal_info_btn">
        Expander perfil <i class="fas fa-chevron-down"></i>
    </button>
</div>
<div class="content profile hidden active">
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
			$sql = "SELECT * FROM customers";
			$result = mysqli_query($con, $sql);
			if(mysqli_num_rows($result) > 0){
					$sqlImg = "SELECT * FROM customers WHERE id='$id'";
					$resultImgOne = mysqli_query($con, $sqlImg);
					while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
						echo "<div class='pic_container'>";
						if($rowImgOne['imgStatus_1'] == 0){
							echo "<img src='./customer_uploads/profile1".$id.".jpg' class='profile_pics'>";
						}else{
							echo "<img src='./IMG/profiledefault.jpg' class='profile_pics'>";
						}
					echo "</div>";
					}
			}else{
				echo"No existen usuarios en la base de datos!";
			}
		?>
		<span>Comprobante de domicilio</span>
		<form action="customerUpload.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
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
			if(mysqli_num_rows($result) > 0){
					$sqlImg = "SELECT * FROM customers WHERE id='$id'";
					$resultImgOne = mysqli_query($con, $sqlImg);
					while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
						echo "<div class='pic_container'>";
						if($rowImgOne['imgStatus_2'] == 0){
							echo "<img src='./customer_uploads/profile2".$id.".jpg' class='profile_pics'>";
						}else{
							echo "<img src='./IMG/profiledefault.jpg' class='profile_pics'>";
						}
					echo "</div>";
					}
			}else{
				echo"No existen usuarios en la base de datos!";
			}
		?>
		<span>Comprobante de identificación(INE)</span>
		<form action="customerUpload.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
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
		<td><?=$customer['group_name']?></td>
	</tr>
</table>
<p>Información de Aval:</p>
<div class="pic_uploader_container">
	<div class="pic_uploader">
		<?php
			$sql = "SELECT * FROM customers";
			$result = mysqli_query($con, $sql);
			if(mysqli_num_rows($result) > 0){
					$sqlImg = "SELECT * FROM customers WHERE id='$id'";
					$resultImgOne = mysqli_query($con, $sqlImg);
					while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
						echo "<div class='pic_container'>";
						if($rowImgOne['guar_imgStatus_1'] == 0){
							echo "<img src='./customer_uploads/guarantor1".$id.".jpg' class='profile_pics'>";
						}else{
							echo "<img src='./IMG/profiledefault.jpg' class='profile_pics'>";
						}
					echo "</div>";
					}
			}else{
				echo"No existen usuarios en la base de datos!";
			}
		?>
		<span>Comprobante de domicilio</span>
		<form action="customerUpload.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
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
			if(mysqli_num_rows($result) > 0){
					$sqlImg = "SELECT * FROM customers WHERE id='$id'";
					$resultImgOne = mysqli_query($con, $sqlImg);
					while ($rowImgOne = mysqli_fetch_assoc($resultImgOne)) {
						echo "<div class='pic_container'>";
						if($rowImgOne['guar_imgStatus_2'] == 0){
							echo "<img src='./customer_uploads/guarantor2".$id.".jpg' class='profile_pics'>";
						}else{
							echo "<img src='./IMG/profiledefault.jpg' class='profile_pics'>";
						}
					echo "</div>";
					}
			}else{
				echo"No existen usuarios en la base de datos!";
			}
		?>
		<span>Comprobante de identificación(INE)</span>
		<form action="customerUpload.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
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
        //loan window opener
        const loanFormBack = document.querySelector('.loan_form_background')
        const loanForm = document.querySelector('.loan_form')
        const loanBtn = document.querySelector('.loan_btn')

        loanBtn.addEventListener("click", function(){
            loanFormBack.classList.add("active")
            loanForm.classList.add("active")
        });
        
        loanFormBack.addEventListener("click", function(){
            loanFormBack.classList.remove("active")
            loanForm.classList.remove("active")

        });
        

        //detailed profile opener
        const expandPersonalInfoBtn = document.querySelector('.personal_info_btn');
        const personalInfoContainer = document.querySelector('.content.profile.hidden');

        expandPersonalInfoBtn.addEventListener("click", function(){
            if(personalInfoContainer.classList.contains("active")){
                expandPersonalInfoBtn.innerHTML = "Cerrar perfil <i class='fas fa-chevron-up'></i>";
                personalInfoContainer.classList.toggle("active");
            }else if(!personalInfoContainer.classList.contains("active")){
                expandPersonalInfoBtn.innerHTML = "Expandir perfil <i class='fas fa-chevron-down'></i>";
                personalInfoContainer.classList.toggle("active");
            }
        });

        //custom file searcher 
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