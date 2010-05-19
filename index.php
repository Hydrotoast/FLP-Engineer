<?php 
// Redirect to installation page if it hasn't been configured.
if(file_exists('install.php') && !isset($_GET['debug']))
{
	header('Location: install.php');
}

include 'config.php';
include 'template.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Error connection to database. \n');

$query = 'SELECT config_key, config_value FROM ' . DB_EXT . '_config WHERE config_key=\'active_flp\' OR config_key=\'flp_changed\'';
$result = $db->query($query);

while($row = $result->fetch_assoc())
{
	if($row['config_key'] == 'active_flp') {
		$active_flp = $row['config_value'];
	}
	
	if($row['config_key'] == 'flp_changed') {
		$flp_changed = $row['config_value'];
	}
}

$result->free_result();

if(is_dir($active_flp))
{
	$active_flp = 'flps/plain.html';
}

ob_start("ob_gzhandler");
include 'lang/' . LOCALISATION . '.php';
include 'template/header.php';

$flp_file = 'flps/' . $active_flp;

$tags = array(
	'LOGIN_START' 	=> '<form id="login_form" action="login.php" method="post">',
	'LOGIN_END'		=> '</form>',

	'L_USERNAME'	=> '<label for="username">Username</label>',
	'L_PASSWORD'	=> '<label for="password">Password</label>',

	'I_USERNAME'	=> '<input type="text" name="username" value="" />',
	'I_PASSWORD'	=> '<input type="password" name="password" value="" />',

	'B_SUBMIT'		=> '<input type="submit" name="submit" value="Login" />',

	'FULL_LOGIN'	=> '<form action="login.php" method="post"><p><label for="username">Username</label><br /><input type="text" name="username" value="" /></p><p><label for="password">Password</label><br /><input type="password" name="password" value="" /></p><p class="btns"><input type="submit" name="submit" value="Login" /></p></form>'
);

$template = new Template($flp_file, $tags, $flp_changed);
$template->output();

if($flp_changed == 1) {
	$query2 = 'UPDATE ' . DB_EXT . '_config SET config_value=\'0\' WHERE config_key=\'flp_changed\'';
	$db->query($query2);
}

$db->close();

include 'template/footer.php'; 
?>