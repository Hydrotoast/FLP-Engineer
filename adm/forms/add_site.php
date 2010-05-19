<?php 
session_start();

//Check if logged in or send to login
if(!isset($_SESSION['logged']) || $_SESSION['logged'] != TRUE)
{
    header('location: ../login.php');
    exit;
}
?>

<form action="modify.php" method="post" id="modal_form">
	<p><label for="redirect">Redirect to</label><br />
	<input type="text" name="redirect" value="http://" /></p>
	
	<input type="hidden" name="action" value="add_site" />
	<input type="hidden" name="backtrack" value="index.php" />
	<p class="btns"><input type="submit" name="submit" value="Submit" /></p>
</form>