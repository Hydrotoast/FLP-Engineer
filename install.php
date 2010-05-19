<?php
include 'config.php';

if(INSTALLED === 'true')
{
	echo 'Please delete this file (install.php) for security purposes before using this script.';
	exit();
}

if(isset($_POST['submit']))
{
	// Get the configuration options.
	$dbhost = $_POST['dbhost'];
	$dbuser = $_POST['dbuser'];
	$dbpass = $_POST['dbpassword'];
	$dbname = $_POST['dbname'];
	$dbext = $_POST['dbext'];
	
	$user = $_POST['username'];
	$pass = $_POST['password'];
	$local = $_POST['localisation'];
	
	// Validation
	$errors[] = (trim($user) == '' && strlen($user) < 3) ? 'Username cannot be blank and must have at least 3 characters.' : '';
	$errors[] = (trim($pass) == '' && strlen($pass) < 4) ? 'Password cannot be blank and must have at least 4 characters.' : '';
	
	// Connect to the database
	$db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	
	$salt = 'cynosura';
	$enc_pass = base64_encode($db->real_escape_string($pass) . $salt);
	
	if($db->connect_error)
	{
		$errors[] = 'Invalid database details. Please try again.';
	}
	
	// Create the database tables
	$query = 'CREATE TABLE IF NOT EXISTS `' . $dbext . '_users` (
		`user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
		`user_name` varchar(45) NOT NULL DEFAULT \'\',
		`user_password` varchar(45) NOT NULL DEFAULT \'\',
		`user_time` int(11) NOT NULL,
		`user_agent` varchar(45) NOT NULL DEFAULT \'\',
		`user_ip` varchar(40) NOT NULL DEFAULT \'\',
	PRIMARY KEY (`user_id`));';
		
	$query .= 'CREATE TABLE IF NOT EXISTS `' . $dbext . '_sites` (
	  	`site_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	  	`site_redirect` varchar(255) NOT NULL DEFAULT \'\',
	  	`site_active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
	PRIMARY KEY (`site_id`));';
	 
	$query .= 'CREATE TABLE IF NOT EXISTS `' . $dbext . '_admins` (
		`admin_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
		`admin_user` varchar(45) NOT NULL DEFAULT \'\',
		`admin_pass` varchar(45) NOT NULL DEFAULT \'\',
		`admin_join` int(11) NOT NULL,
	PRIMARY KEY (`admin_id`));';
	
	$query .= 'CREATE TABLE IF NOT EXISTS `' . $dbext . '_config` (
		`config_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
		`config_key` varchar(255) NOT NULL DEFAULT \'\',
		`config_value` varchar(255) NOT NULL DEFAULT \'\',
	PRIMARY KEY (`config_id`));';
	
	$query .= 'CREATE TABLE IF NOT EXISTS `' . $dbext . '_logs` (
		`log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
		`log_ip` varchar(40) NOT NULL DEFAULT \'\',
		`log_time` int(11) NOT NULL,
	PRIMARY KEY (`log_id`));';
	
	$query .= 'INSERT INTO ' . $dbext . '_admins (admin_user, admin_pass, admin_join) VALUES (\'' . $user . '\', \'' . $enc_pass . '\', \'' . time() . '\');';
	$query .= 'INSERT INTO ' . $dbext . '_config (config_key, config_value) VALUES (\'active_flp\', \'plain.html\');';
	$query .= 'INSERT INTO ' . $dbext . '_config (config_key, config_value) VALUES (\'flp_changed\', \'0\');';
	
	$db->multi_query($query);
	$db->close();
	
	// Write into the config file
	$config = "<?php
DEFINE('DB_HOST', '$dbhost');
DEFINE('DB_USER', '$dbuser');
DEFINE('DB_PASS', '$dbpass');
DEFINE('DB_NAME', '$dbname');
DEFINE('DB_EXT', '$dbext');

DEFINE('LOCALISATION', '$local');

DEFINE('INSTALLED', 'true');
?>";
	
	if(is_writable('config.php'))
	{
		if(count($errors) > 0) {
			// Handle errors
			if(!$fp = fopen('config.php', 'w')) 
			{
				$errors[] = 'Could not open config.php.';
			}
			
			if(fwrite($fp, $config) === false) 
			{
				$errors[] = 'Could not write to config.php.';
			}
			
			fclose($fp); 
			
			if(count($errors) === 0) {
				echo 'Successfully created the user and database! Please delete this file for security purposes.';
				exit();
			}
		}	
	}
	else
	{
		$errors[] = 'Please rewrite permissions on config.php to 777.';
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Install FLP Engineer</title>

<style type="text/css">
* {
	margin: 0;
	padding: 0;
}

body {
	text-align: center;
	font: 0.875em/1.5em Helvetica, Arial, sans-serif;
}

#wrapper {
	text-align: left;
	width: 800px;
	margin: 0 auto;
}

fieldset {
	margin: 1em 0;
	padding: 0.5em;
}

legend { font-weight: bold; }

.error {
	color: #D8000C;
	background-color: #FFBABA;
	border: 1px solid #D8000C;
	margin: 1em 0;
	padding: 0.5em;
}
</style>
</head>
<body>
<div id="wrapper">
	<?php 
	if(!empty($errors)){
		foreach($errors as $error){
			echo (($error != '') ? '<div class=error>'.$error.'</div>' : '');
		}
	} 
	?>

	<form action="install.php" method="post" id="install">
		<fieldset>
			<legend>Login Details</legend>
			<p>
				<label for="username">Username</label><br />
				<input type="text" name="username" />
			</p>
			
			<p>
				<label for="password">Password</label><br />
				<input type="password" name="password" />
			</p>
			
			<p>
				<label for="localisation">Localisation</label><br />
				<select name="localisation">
					<option value="en">English</option>
					<option value="es">Spanish</option>
				</select>
			</p>
		</fieldset>
		<fieldset>
			<legend>Database Install</legend>
			
			<p>
				<label for="dbhost">Host</label><br />
				<input type="text" name="dbhost" value="localhost" />
			</p>
			
			<p>
				<label for="dbuser">User</label><br />
				<input type="text" name="dbuser" value="root" />
			</p>
			
			<p>
				<label for="dbpassword">Password</label><br />
				<input type="password" name="dbpassword" />
			</p>
			
			<p>
				<label for="dbname">Database</label><br />
				<input type="text" name="dbname" />
			</p>
			
			<p>
				<label for="dbext">DB Extension</label><br />
				<input type="text" name="dbext" value="flp" />
			</p>
		</fieldset>
		
		<fieldset id="buttons"><input type="submit" name="submit" value="Install" /> <input type="reset" name="reset" value="Reset" /></fieldset>
	</form>
</div>
</body>
</html>