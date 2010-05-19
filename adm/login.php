<?php
session_start();

include 'classes/admins.php';
include '../lang/' . LOCALISATION . '.php';

$admins_model = new Admins(); //Instantiate class

if(isset($_GET['action']) && $_GET['action'] == 'logout')
{
	$admins_model->logout();
}

if($_POST && !empty($_POST['username']) && !empty($_POST['password']))
{
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	//Attempt to login user
	$result = $admins_model->login($username, $password);
	
	//Results of login attempt
	if($result)
	{
		header('location: index.php'); //Redirect to main page
	}
	else
	{
		$status = $lang['INVALID_LOGIN']; //Report error
	}
}
elseif(isset($_SESSION['logged']) && $_SESSION['logged'] === TRUE)
{
	header('location: index.php'); //Redirect to main page
}

ob_start("ob_gzhandler");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $lang['LOGIN']; ?></title>

<link rel="stylesheet" href="styles/reset.css" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="styles/default.css" type="text/css" media="screen, projection" />
</head>
<body class="login">
	<div id="login">
		<h1><?php echo $lang['FLP_ENGINEER']; ?></h1>
	
		<?php if(isset($status)):?>
			<div id="status"><?php echo $status;?></div>
		<?php endif; ?>
		
		<form action="login.php" method="post">
			<fieldset>
				<p>
					<label><?php echo $lang['USERNAME']; ?></label><br />
					<input type="text" name="username" value="" />
				</p>
				
				<p>
					<label><?php echo $lang['PASSWORD']; ?></label><br />
					<input type="password" name="password" value="" />
				</p>
				
				<p class="btns"><input type="submit" name="submit" value="Login" /></p>
			</fieldset>
		</form>
	</div>
</body>
</html>