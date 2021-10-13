<?php

include_once 'processes/dbhandler.php';
// Default input product values
$customer = array(
    'group_name' => '',
    'name' => '',
    'lastname' => '',
    'ine' => '',
    'birthday' => '',
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
    
);
if (isset($_GET['id'])) {
    // Get the customers from the database
    $stmt = $con->prepare('SELECT name, lastname, ine, birthday, email, phone, address, city, state, 
    guar_name, guar_lastname, guar_phone, guar_address, guar_city, guar_state, guar_ine, group_name  FROM customers WHERE id = ?');
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $stmt->bind_result($customer['name'], $customer['lastname'], $customer['ine'], $customer['birthday'], $customer['email'], 
        $customer['phone'], $customer['address'], $customer['city'], $customer['state'], 
        $customer['guar_name'], $customer['guar_lastname'], $customer['guar_phone'], $customer['guar_address'], $customer['guar_city'], $customer['guar_state'], 
        $customer['guar_ine'], $customer['group_name']);
    $stmt->fetch();
    $stmt->close();

    // ID param exists, edit an existing customers
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the customers
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
        $groupName = ucwords($_POST['group_name']);
        $stmt = $con->prepare('UPDATE customers SET name = ?, lastname = ?, ine = ?, birthday = ?, email = ?, phone = ?, address = ?, city = ?, state = ?, guar_name = ?, guar_lastname = ?, 
            guar_phone = ?, guar_address = ?, guar_city = ?, guar_state = ?, guar_ine = ?, group_name = ? WHERE id = ?');
        $stmt->bind_param('sssssssssssssssssi', $upperName, $upperLastname, $upperIne, $_POST['birthday'], $_POST['email'], $_POST['phone'], $upperAddress, 
            $upperCity, $upperState, $upperGuarName, $upperGuarLastname, $_POST['guar_phone'], $upperGuarAddress, $upperGuarCity,  
            $upperGuarState, $upperGuarIne, $groupName,  $_GET['id']);
        $stmt->execute();
        header('Location: home.php');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the customers
        $stmt = $con->prepare('DELETE FROM customers WHERE id = ?');
        $stmt->bind_param('i', $_GET['id']);
        $stmt->execute();
        header('Location: home.php');
        exit;
    }
} else {
    // Create a new customers
    $page = 'Create';
    // format of regDate ('N = day of the week starting with 1 which is monday, 
    //                     W = number of week starting on monday,
    //                     d = day of month with 2 digits 01 to 31-
    //                     m = month of the year with 2 digits 01 to 12-
    //                     Y = full numeric representation of the year, 4 digits')
    $regDate = date('N,W,d-m-Y');
    if (isset($_POST['submit'])) {
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
        $groupName = ucwords($_POST['group_name']);
        $stmt = $con->prepare('INSERT IGNORE INTO customers (group_name, name, lastname, birthday, address, city, state, ine, phone, email, registration_date, guar_name, guar_lastname, guar_phone, guar_address, 
            guar_city, guar_state, guar_ine) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->bind_param('ssssssssssssssssss',$groupName, $upperName, $upperLastname, $_POST['birthday'], $upperAddress, $upperCity, $upperState, $upperIne, $_POST['phone'], 
            $_POST['email'], $regDate, $upperGuarName, $upperGuarLastname, $_POST['guar_phone'], $upperGuarAddress, $upperGuarCity, $upperGuarState, $upperGuarIne);
        $stmt->execute();
        header('Location: home.php');
        exit;
    }
}
include 'header.php';
?>

<?php 
// Change of title 
if ($page == 'Edit'){
    $Espage = 'Editando';
}elseif ($page == 'Create'){
    $Espage = 'Creando';
}   
?>


<?php if ($page == 'Edit'): ?>
    <h2>Editar Cuenta</h2>

<?php elseif ($page == 'Create'): ?>
    <h2>Crear Cuenta</h2>
<?php endif; ?>

<div class="content profile">
    <form method="post" class="form responsive-width-100">
        <p>Información del cliente:</p>
        <label for="name">Nombre(s)</label>
        <input type="text" id="name" name="name" placeholder="Nombre" value="<?=$customer['name']?>" required>

        <label for="lastname">Apellidos</label>
        <input type="text" id="lastname" name="lastname" placeholder="Apellido" value="<?=$customer['lastname']?>" required>

        <label for="ine">Clave de elector</label>
        <input type="text" id="ine" name="ine" placeholder="Clave de elector" value="<?=$customer['ine']?>" required>

        <label for="birthday">Fecha de nacimiento</label>
        <input type="text" id="birthday" name="birthday" placeholder="DD-MM-AAAA" value="<?=$customer['birthday']?>">

        <p>Información de contacto:</p>
        <label for="email">Correo</label>
        <input type="text" id="email" name="email" placeholder="ejemplo@correo.com" value="<?=$customer['email']?>">

        <label for="phone">Teléfono</label>
        <input type="text" id="phone" name="phone" placeholder="Número de Teléfono" value="<?=$customer['phone']?>">

        <label for="address">Domicilio</label>
        <input type="text" id="address" name="address" placeholder="Domicilio (ejemplo #123)" value="<?=$customer['address']?>">

        <label for="city">Ciudad</label>
        <input type="text" id="city" name="city" placeholder="Ciudad/Pueblo" value="<?=$customer['city']?>">

        <label for="state">Estado</label>
        <input type="text" id="state" name="state" placeholder="Estado" value="<?=$customer['state']?>">

        <p>Información de colaborador:</p>
        <label for="group_name">Nombre de grupo</label>
        <select name="group_name" id="group_name">
            <?php if ($page == 'Edit'): ?>
                <?php
                    $stmt = $con->prepare('SELECT id, group_name FROM groups WHERE id = ?');
                    $stmt->bind_param('i', $customer['group_name']);
                    $stmt->execute();
                    $stmt->bind_result($groupID, $groupName);
                    $stmt->fetch();
                    $stmt->close();


                ?>
                <option value="<?=$groupID?>"><?=$groupID." | ".$groupName?></option>
            <?php elseif ($page == 'Create'): ?>
                <option value="" selected="true" disabled="disabled">Selecciona grupo</option>
            <?php endif; ?>
            <?php 
                $stmt = $con->prepare('SELECT id, group_name FROM groups');
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($groupID, $groupName);

            ?>
            <?php if ($stmt->num_rows == 0): ?>
                <option disabled="disabled">No existe ningun grupo en la base de datos</option>
            <?php else: ?>
                <?php while ($stmt->fetch()): ?>
                    <option value="<?=$groupID?>"><?=$groupID." | ".$groupName?></option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>

        <p>Información de Aval:</p>
        <label for="guar_name">Nombre</label>
        <input type="text" id="guar_name" name="guar_name" placeholder="Nombre(s) de aval" value="<?=$customer['guar_name']?>">

        <label for="guar_lastname">Apellido</label>
        <input type="text" id="guar_lastname" name="guar_lastname" placeholder="Apellidos de aval" value="<?=$customer['guar_lastname']?>">

        <label for="guar_phone">Teléfono</label>
        <input type="text" id="guar_phone" name="guar_phone" placeholder="Número de Teléfono" value="<?=$customer['guar_phone']?>">

        <label for="guar_address">Domicilio</label>
        <input type="text" id="guar_address" name="guar_address" placeholder="Domicilio (ejemplo #123)" value="<?=$customer['guar_address']?>">

        <label for="guar_city">Ciudad</label>
        <input type="text" id="guar_city" name="guar_city" placeholder="Ciudad/Pueblo" value="<?=$customer['guar_city']?>">

        <label for="guar_state">Estado</label>
        <input type="text" id="guar_state" name="guar_state" placeholder="Estado" value="<?=$customer['guar_state']?>">

        <label for="guar_ine">Clave de elector</label>
        <input type="text" id="guar_ine" name="guar_ine" placeholder="Clave de elector" value="<?=$customer['guar_ine']?>">

        <div class="submit-btns">
            <input type="submit" name="submit" value="Guardar">
            <?php if ($page == 'Edit'): ?>
            <input type="submit" name="delete" value="Borrar" class="delete">
            <?php endif; ?>
        </div>

    </form>
</div>
<script src="./JS/menu.js"></script>
	</body>
</html>
