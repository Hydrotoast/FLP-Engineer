<?php
include 'common.php';

// Check if a form is being submitted
if(isset($_POST['submit']))
{
	// Check for the action
	$type = $_GET['type'];
	$backtrack = $_POST['backtrack'];
	$action = $_POST['action'];
	$available_actions = array('export_logs', 'delete_logs', 'add_site', 'activate_site', 'delete_site', 'activate_flp', 'add_user');
	
	if(!in_array($action, $available_actions)) die($lang['INVALID_ACTION']);
	
	// Get the ids to modify
	if($_POST['userid'])
	{
		for($i = 0; $i < count($_POST['userid']); $i++)
		{
			$ids[$i] = filter_var($_POST['userid'][$i], FILTER_SANITIZE_NUMBER_INT);
		}
	}
	
	switch($action)
	{
		case 'export_logs':
			redirect('export.php?type=plain');
			exit;
		case 'delete_logs':
			$logs_model->delete_logs($ids);
			break;
		case 'add_site':
			$redirect = $_POST['redirect'];
			$sites_model->add_site($redirect);
			break;
		case 'activate_site':
			$id = filter_var($_POST['siteid'], FILTER_SANITIZE_NUMBER_INT);
			$sites_model->activate_site($id);
			break;
		case 'delete_site':
			$id = filter_var($_POST['siteid'], FILTER_SANITIZE_NUMBER_INT);
			$sites_model->delete_site($id);
			break;
		case 'activate_flp':
			$flp_url = $_POST['flp_url'];
			if($flp_url && $flp_url != '')
			{
				$config_model->activate_flp($flp_url);
			}
			break;
		case 'add_user':
			// Get necessary details for a login
			$username = $_POST['username'];
			$password = $_POST['password'];
			
			$admins_model->register_user($username, $password);
			break;
		default:
			break;
	}
	
	redirect($backtrack);
}
else
{
	redirect("index.php");
}

function redirect($url)
{
	header("Location: $url");
}