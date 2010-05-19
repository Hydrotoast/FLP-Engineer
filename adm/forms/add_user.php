<?php 
include_once '../auth.php';
?>

<form action="modify.php" method="post" id="modal_form">
	<p><label for="username">Username</label><br />
	<input type="text" name="username" value="" /></p>
	
	<p><label for="password">Password</label><br />
	<input type="password" name="password" value="" /></p>
	
	<input type="hidden" name="action" value="add_user" />
	<input type="hidden" name="backtrack" value="index.php" />
	<p class="btns"><input type="submit" name="submit" value="Submit" /></p>
</form>