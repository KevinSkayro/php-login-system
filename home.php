<?php
include 'processes/dbhandler.php';
check_loggedin($con);
include 'header.php';
$stmt = $con->prepare('SELECT id, name, lastname, ine, customerStatus, registration_date  FROM customers');
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $name, $lastname, $ine, $customerStatus, $registrationDate);

?>
		<div class="content home">
		<!-- <h2>inicio</h2> -->
			<p class="block">¡Bienvenido(a), <?=$_SESSION['name']?>!</p>
			<div class="links">
    			<a href="addCustomer.php">Nuevo cliente</a>
			</div>
			<div class="search_container">
				<div class="search_input">
					<input type="text" name="customer_search" id="customer_search" class="search_bar" data-customer-search onkeyup="updateSearch()">
					<select name="filter_customers" id="filter_customers" data-active-inactive onchange="filterSearch()">
						<option value="all">Todos</option>
						<option value="active">Vigente</option>
						<option value="unactive">Vencidos</option>
					</select>
					<select name="filterType" id="filterType" data-filter-type>
						<option value="ine">Buscar por clave de elector</option>
						<option value="name">Buscar por Nombre</option>
					</select>
				</div>
				<div class="table">
					<div class="listHead">
						<div class="column id">#</div>
						<div class="column">Nombre Completo</div>
						<div class="column responsive_hidden_1000">Clave de elector</div>
						<div class="column responsive_hidden_768">Vigencia</div>
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
								<div class="column ine responsive_hidden_1000"><?=$ine?></div>
								<?php if ($customerStatus == '0'): ?>
									<?php
										//get todays date
										$todaysDate = date('d-m-Y');
										//get registration date
										$dateSplit = explode(',',$registrationDate);
										$date1=date_create($dateSplit[2]);
										$date2=date_create($todaysDate);
										$diff=date_diff($date1,$date2);
										$DateDiff = $diff->format("%a");
									?>
									<?php if ($DateDiff <= 7 ): ?>
										<div class="column activity_status unactive responsive_hidden_768">Nuevo</div>
									<?php else: ?>
										<div class="column activity_status unactive responsive_hidden_768">Vencido</div>
									<?php endif; ?>
								<?php elseif ($customerStatus == '1'): ?>
									<div class="column activity_status active responsive_hidden_768">Vigente</div>
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
			//Global variables
			const filterActiveInactive = document.querySelector('[data-active-inactive]');
			const status = document.querySelectorAll('div.activity_status');
			const row = document.querySelectorAll('.row');

			//filter on key up by ine and name
			function updateSearch() {
				// local variables
				const searchByIneOrName = document.querySelector('[data-filter-type]');
				const input = document.querySelector('[data-customer-search]');
				const filter = input.value.toUpperCase();
				const ine = document.querySelectorAll('div.ine');
				const name = document.querySelectorAll('div.name');
				
				// Loop through all list items, and hide those who don't match the search query
				if (searchByIneOrName.value == "ine") {
					for (let i = 0; i < ine.length; i++) {
						let txt = ine[i];
						let txtValue = txt.textContent || txt.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
						row[i].style.display = "";
						} else {
						row[i].style.display = "none";
						}
					}
				} else if (searchByIneOrName.value == "name") {
					for (let i = 0; i < name.length; i++) {
						txt = name[i];
						txtValue = txt.textContent || txt.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
						row[i].style.display = "";
						} else {
						row[i].style.display = "none";
						}
					}
				}
				
				if (filterActiveInactive.value == "active") {
					for (let i = 0; i < status.length; i++) {
						if (status[i].classList.contains('unactive')) {
							row[i].style.display = "none";
						}
					}
				} else if (filterActiveInactive.value == "unactive") {
					for (let i = 0; i < status.length; i++) {
						if (status[i].classList.contains('active')) {
							row[i].style.display = "none";
						}
					}
				}
			}
			//filter on change to see active/unactive customers
			function filterSearch() {
				if (filterActiveInactive.value == "active") {
					for (let i = 0; i < status.length; i++) {
						if (status[i].classList.contains('unactive')) {
							row[i].style.display = "none";
						} else {
							row[i].style.display = "";
						}
					}
				} else if (filterActiveInactive.value == "unactive") {
					for (let i = 0; i < status.length; i++) {
						if (status[i].classList.contains('active')) {
							row[i].style.display = "none";
						} else {
							row[i].style.display = "";
						}
					}
				} else {
					for (let i = 0; i < row.length; i++) {
						row[i].style.display = "";
					}
				}
			}
		</script>
		<script src="./JS/menu.js">
		</script>
	</body>
</html>