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
    'guar_ine' => ''
);
$roles = array('Miembro', 'Admin', 'Ejecutivo', 'Supervisor');
if (isset($_GET['id'])) {
    // Get the account from the database
    $stmt = $con->prepare('SELECT name, lastname, ine, birthday, password, email, phone, address, city, state, activation_code, rememberme, role, 
    guar_name, guar_lastname, guar_phone, guar_address, guar_city, guar_state, guar_ine FROM accounts WHERE id = ?');
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $stmt->bind_result($account['name'], $account['lastname'], $account['ine'], $account['birthday'], $account['password'], $account['email'], 
        $account['phone'], $account['address'], $account['city'], $account['state'], $account['activation_code'], $account['rememberme'], $account['role'], 
        $account['guar_name'], $account['guar_lastname'], $account['guar_phone'], $account['guar_address'], $account['guar_city'], $account['guar_state'], 
        $account['guar_ine']);
    $stmt->fetch();
    $stmt->close();

    // ID param exists, edit an existing account
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the account
        $password = $account['password'] != $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $account['password'];
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
        $stmt = $con->prepare('UPDATE accounts SET name = ?, lastname = ?, ine = ?, birthday = ?, password = ?, email = ?, phone = ?, address = ?, city = ?, state = ?, activation_code = ?, rememberme = ?, role = ?, guar_name = ?, guar_lastname = ?, guar_phone = ?, guar_address = ?, guar_city = ?, guar_state = ?, guar_ine = ? WHERE id = ?');
        $stmt->bind_param('ssssssssssssssssssssi', $upperName, $upperLastname, $upperIne, $_POST['birthday'], $password, $_POST['email'], $_POST['phone'], 
            $upperAddress, $upperCity, $upperState, $_POST['activation_code'], $_POST['rememberme'], $_POST['role'], $upperGuarName, 
            $upperGuarLastname, $_POST['guar_phone'], $upperGuarAddress, $upperGuarCity,  $upperGuarState, $upperGuarIne, $_GET['id']);
        $stmt->execute();
        $employeeID =  $_GET['id'];
        header("Location: profile.php?id=$employeeID");
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the account
        $stmt = $con->prepare('DELETE FROM accounts WHERE id = ?');
        $stmt->bind_param('i', $_GET['id']);
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
} else {
    // Create a new account
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $con->prepare('INSERT IGNORE INTO accounts (name,lastname,ine,birthday,password,email,phone,address,city,state,activation_code,rememberme,role,guar_name,guar_lastname,guar_phone,guar_address,guar_city,guar_state,guar_ine ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->bind_param('ssssssssssssssssssss', $_POST['name'], $_POST['lastname'], $_POST['ine'], $_POST['birthday'], $password, $_POST['email'], $_POST['phone'], 
            $_POST['address'], $_POST['city'], $_POST['state'], $_POST['activation_code'], $_POST['rememberme'], $_POST['role'], $_POST['guar_name'], 
            $_POST['guar_lastname'], $_POST['guar_phone'], $_POST['guar_address'], $_POST['guar_city'],  $_POST['guar_state'], $_POST['guar_ine']);
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
}
?>

<?php 
// Change of title 
if ($page == 'Edit') {
    $Espage = 'Editando';
}elseif ($page == 'Create') {
    $Espage = 'Creando';
}   
?>
<?=template_admin_header($Espage . ' Perfil')?>

<?php if ($page == 'Edit'): ?>
    <h2>Editar Cuenta</h2>

<?php elseif ($page == 'Create'): ?>
    <h2>Crear Cuenta</h2>
<?php endif; ?>

<div class="content-block">
    <form action="" method="post" class="form responsive-width-100">
        <p>Acerca de ti:</p>
        <label for="name">Nombre</label>
        <input type="text" id="name" name="name" placeholder="Nombre" value="<?=$account['name']?>" required>

        <label for="lastname">Apellido</label>
        <input type="text" id="lastname" name="lastname" placeholder="Apellido" value="<?=$account['lastname']?>" required>

        <label for="ine">Clave de elector</label>
        <input type="text" id="ine" name="ine" placeholder="Clave de elector" value="<?=$account['ine']?>" required>

        <label for="birthday">Fecha de nacimiento</label>
        <input type="text" id="birthday" name="birthday" placeholder="DD-MM-AAAA" value="<?=$account['birthday']?>">



        <p>Información de contacto:</p>
        <label for="email">Correo</label>
        <input type="text" id="email" name="email" placeholder="ejemplo@correo.com" value="<?=$account['email']?>">
    
        <label for="phone">Teléfono</label>
        <input type="text" id="phone" name="phone" placeholder="Número de Teléfono" value="<?=$account['phone']?>">

        <label for="address">Domicilio</label>
        <input type="text" id="address" name="address" placeholder="Domicilio (ejemplo #123)" value="<?=$account['address']?>">

        <label for="city">Ciudad</label>
        <input type="text" id="city" name="city" placeholder="Ciudad/Pueblo" value="<?=$account['city']?>">

        <label for="state">Estado</label>
        <input type="text" id="state" name="state" placeholder="Estado" value="<?=$account['state']?>">

        <p>Información de colaborador:</p>
        <label for="role">Rol</label>
        <select id="role" name="role" style="margin-bottom: 30px;">
            <?php foreach ($roles as $role): ?>
            <option value="<?=$role?>"<?=$role==$account['role']?'selected':''?>><?=$role?></option>
            <?php endforeach; ?>
        </select>

        <p>Editar contraseña:</p>
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Password">

        <p>Información de Aval:</p>
        <label for="guar_name">Nombre</label>
        <input type="text" id="guar_name" name="guar_name" placeholder="Nombre(s) de aval" value="<?=$account['guar_name']?>">

        <label for="guar_lastname">Apellido</label>
        <input type="text" id="guar_lastname" name="guar_lastname" placeholder="Apellidos de aval" value="<?=$account['guar_lastname']?>">

        <label for="guar_phone">Teléfono</label>
        <input type="text" id="guar_phone" name="guar_phone" placeholder="Número de Teléfono" value="<?=$account['guar_phone']?>">

        <label for="guar_address">Domicilio</label>
        <input type="text" id="guar_address" name="guar_address" placeholder="Domicilio (ejemplo #123)" value="<?=$account['guar_address']?>">

        <label for="guar_city">Ciudad</label>
        <input type="text" id="guar_city" name="guar_city" placeholder="Ciudad/Pueblo" value="<?=$account['guar_city']?>">

        <label for="guar_state">Estado</label>
        <input type="text" id="guar_state" name="guar_state" placeholder="Estado" value="<?=$account['guar_state']?>">

        <label for="guar_ine">Clave de elector</label>
        <input type="text" id="guar_ine" name="guar_ine" placeholder="Clave de elector" value="<?=$account['guar_ine']?>">

        <label for="activation_code">Activation Code</label>
        <input type="text" id="activation_code" name="activation_code" placeholder="Activation Code" value="<?=$account['activation_code']?>">

        <label for="rememberme">Remember Me Code</label>
        <input type="text" id="rememberme" name="rememberme" placeholder="Remember Me Code" value="<?=$account['rememberme']?>">

        <div class="submit-btns">
            <input type="submit" name="submit" value="Guardar">
            <?php if ($page == 'Edit'): ?>
            <input type="submit" name="delete" value="Borrar" class="delete">
            <?php endif; ?>
        </div>
    </form>
</div>

<?=template_admin_footer()?>
