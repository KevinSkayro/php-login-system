<?php
include 'dbhandler.php';
check_loggedin($con);
include 'header.php';
$stmt = $con->prepare('SELECT id, name, lastname, ine, customerStatus  FROM customers');
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $name, $lastname, $ine, $customerStatus);

?>
		<div class="content home">
			<h2>Inicio</h2>
			<p class="block">¡Bienvenido(a), <?=$_SESSION['name']?>!</p>
			<div class="links">
    			<a href="addCustomer.php">Nuevo cliente</a>
			</div>
			<div class="search_container">
				<div class="search_input">
					<input type="text" name="customer_search" id="customer_search" class="search_bar" onkeyup="myFunction()">
					<select name="filter_customers" id="filter_customers">
						<option value="all">Todos</option>
						<option value="active">Vigente</option>
						<option value="inactive">Vencidos</option>
					</select>
					<select name="filterType" id="filterType">
						<option value="ine">Buscar por clave de elector</option>
						<option value="name">Buscar por Nombre</option>
					</select>
				</div>
				<div class="table">
					<div class="listHead">
						<div class="column id">#</div>
						<div class="column">Nombre Completo</div>
						<div class="column">Clave de elector</div>
						<div class="column">Vigencia</div>
					</div>
					<div class="list_container">
						<ul id="ul">
							<?php if ($stmt->num_rows == 0): ?>
							<li>
								<div colspan="8" style="text-align:center;">No existe ninguna cuenta en la base de datos</div>
							</li>
							<?php else: ?>
							<?php while ($stmt->fetch()): ?>
							<li class="row" onclick="location.href='customer.php?id=<?=$id?>'">
								<div class="column id"><?=$id?></div>
								<div class="column name"><?=$name." ".$lastname?></div>
								<div class="column ine"><?=$ine?></div>
								<?php if ($customerStatus == '0'): ?>
									<div class="column">Vencido</div>

								<?php elseif ($customerStatus == '1'): ?>
									<div class="column">Vigente</div>
								<?php endif; ?>
								
							</li>
							<?php endwhile; ?>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<script>
			function myFunction() {
				// variables
				const searchByIneOrName = document.querySelector('#filterType')
				const input = document.getElementById('customer_search');
				const filter = input.value.toUpperCase();
				const ine = document.querySelectorAll('div.ine')
				const name = document.querySelectorAll('div.name')
				const row = document.querySelectorAll('.row')
				// Loop through all list items, and hide those who don't match the search query
				if(searchByIneOrName.value == "ine"){
					for (let i = 0; i < ine.length; i++) {
						let txt = ine[i]
						let txtValue = txt.textContent || txt.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
						row[i].style.display = "";
						} else {
						row[i].style.display = "none";
						}
					}
				}else if(searchByIneOrName.value == "name"){
					for (let i = 0; i < name.length; i++) {
						txt = name[i]
						txtValue = txt.textContent || txt.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
						row[i].style.display = "";
						} else {
						row[i].style.display = "none";
						}
					}
				}
			}
		</script>
		<script src="./JS/menu.js">

		</script>
	</body>
</html>