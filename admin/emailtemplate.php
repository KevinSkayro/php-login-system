<?php
include 'main.php';
// Get all the defined variable keys and values
if (isset($_POST['emailtemplate'])) {
    file_put_contents('../activation-email-template.html', $_POST['emailtemplate']);
}
// Read the activation email template HTML file
$contents = file_get_contents('../activation-email-template.html');
?>

<?=template_admin_header('Plantilla de correo')?>

<h2>Plantilla de correo</h2>

<div class="content-block">
    <form action="" method="post" class="form responsive-width-100">
        <textarea name="emailtemplate"><?=$contents?></textarea>
        <input type="submit" value="Save">
    </form>
</div>

<?=template_admin_footer()?>
