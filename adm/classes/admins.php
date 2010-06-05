<?php

if(file_exists('../config.php')) {
	require_once '../config.php';
}
else
{
	require_once '../../config.php';
}

class Admins
{
	private $db;
	private $table;
	private $salt = 'cynosura';
	
	function Admins() 
	{
		$this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die("Error connecting to database \n");
		$this->table = DB_EXT . '_admins';
	}
	
	function register($username, $password)
	{
		$check_login = $this->check_username($username);
		
		if($check_login)
		{
			die($lang['USERNAME_EXISTS']);
		}
		else
		{
			$this->add_user($username, $password);
		}
	}
	
	function login($username, $password, $token)
	{
		//Escape the strings to prevent SQL injections
		$qpassword = base64_encode($password . $this->salt);
		
		$query = 'SELECT * FROM ' . $this->table . ' WHERE admin_user = ? AND admin_pass = ? LIMIT 1'; //Construct query
		$stmt = $this->db->prepare($query); //Prepare the query
		
		if($stmt)
		{
			$stmt->bind_param('ss', $username, $qpassword); //Bind variables into strings where '?' is.
			$stmt->execute(); //Rebuild string with bound variables
			$result = $stmt->fetch(); //Check for query success
			
			$stmt->close(); //End SQL queries
		}
		
		$this->db->close();
		
		//Results of login
		if($result && strlen($token) === 40 && $token == $_SESSION['token'])
		{
			//Set our session and initialize the trending security
			$_SESSION['logged'] = TRUE;
			$_SESSION['fingerprint'] = sha1($_SERVER['HTTP_USER_AGENT'] . session_id() . $_SERVER['REMOTE_ADDR']);
			unset($_SESSION['token']);
			
			return true;
		} 
		else 
		{
			//Return login fail
			return false;
		}
	}
	
	function logout()
	{	
		if(isset($_SESSION['HTTP_USER_AGENT']))
		{
			unset($_SESSION['HTTP_USER_AGENT']);
		}
		
		if(isset($_SESSION['logged']))
		{
			//Kill session
			unset($_SESSION['logged']);
			$my_cookie = $_COOKIE[session_name()];
			
			//Remove cookies
			if(isset($my_cookie))
			{
				setcookie(session_name(), '', time() - 9999);
				session_destroy();
			}
		}
	}
	
	function authenticate()
	{
		//Check if logged in or send to login and check for a consistent user agent
		if(!isset($_SESSION['logged']) || $_SESSION['logged'] != TRUE || ($_SESSION['logged'] == TRUE && sha1($_SERVER['HTTP_USER_AGENT'] . session_id() . $_SERVER['REMOTE_ADDR']) != $_SESSION['fingerprint']))
		{
			$this->logout();
		    header('location: login.php');
		    exit;
		}
	}
	
	function check_username($username)
	{
		$qusername = $this->db->real_escape_string($username);
		$query = 'SELECT * FROM ' . $this->table . ' WHERE admin_user=' . $qusername;
		$result = $this->db->query($query);
		
		return (($result->num_rows > 0) ? true : false);
		$result->free_result();
		$this->db->close();
	}
	
	function add_user($username, $password)
	{
		//Escape the strings to prevent SQL injections
		$qpassword = base64_encode($password . $this->salt);
		
		$query = 'INSERT INTO ' . $this->table . ' (admin_user, admin_pass, admin_join) VALUES (?, ?, ?)'; //Construct query
		$stmt = $this->db->prepare($query); //Prepare the query
		
		if($stmt)
		{
			$stmt->bind_param('ssd', $username, $qpassword, time()); //Bind variables into strings where '?' is.
			$stmt->execute(); //Rebuild string with bound variables
			$stmt->close(); //End SQL queries
		}
		
		$this->db->close();
	}
}