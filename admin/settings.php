<?php
include 'main.php';
// Read the configuration file
$config_file = file_get_contents('../processes/config.php');
preg_match_all('/define\(\'(.*?)\', ?(.*?)\)/', $config_file, $matches);
// Format key function
function format_key($key) {
    $key = str_replace(['_', 'db '], [' ', 'Database '], strtolower($key));
    return ucwords($key);
}
// Format variable to HTML function
function format_var_html($key, $value) {
    $html = '';
    $type = 'text';
    $value = htmlspecialchars(trim($value, '\''), ENT_QUOTES);
    $type = strpos($key, 'pass') !== false ? 'password' : $type;
    $type = in_array(strtolower($value), ['true', 'false']) ? 'checkbox' : $type;
    $checked = strtolower($value) == 'true' ? ' checked' : '';
    $html .= '<label for="' . $key . '">' . format_key($key) . '</label>';
    if ($type == 'checkbox') {
        $html .= '<input type="hidden" name="' . $key . '" value="false">';
    }
    $html .= '<input type="' . $type . '" name="' . $key . '" id="' . $key . '" value="' . $value . '" placeholder="' . format_key($key) . '"' . $checked . '>';
    return $html;
}
if (!empty($_POST)) {
    // Update the configuration file with the new keys and values
    foreach ($_POST as $k => $v) {
        $v = in_array(strtolower($v), ['true', 'false']) ? strtolower($v) : '\'' . $v . '\'';
        $config_file = preg_replace('/define\(\'' . $k . '\'\, ?(.*?)\)/s', 'define(\'' . $k . '\',' . $v . ')', $config_file);
    }
    file_put_contents('../processes/config.php', $config_file);
    header('Location: settings.php');
    exit;
}
?>

<?=template_admin_header('Configuraciones')?>

<h2>Configuraciones</h2>

<div class="content-block">
    <form action="" method="post" class="form responsive-width-100">
        <?php for($i = 0; $i < count($matches[1]); $i++): ?>
        <?=format_var_html($matches[1][$i], $matches[2][$i])?>
        <?php endfor; ?>
        <input type="submit" value="Save">
    </form>
</div>

<script>
document.querySelectorAll("input[type='checkbox']").forEach(function(checkbox) {
    checkbox.onclick = function() {
        checkbox.value = checkbox.checked ? 'true' : 'false';
    };
});
</script>

<?=template_admin_footer()?>
