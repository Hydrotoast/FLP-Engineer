<?php

if(file_exists('../config.php')) {
	require_once '../config.php';
}
else
{
	require_once '../../config.php';
}

include 'auth.php';
include '../lang/' . LOCALISATION . '.php';

include 'classes/logs.php';
include 'classes/sites.php';
include 'classes/config.php';

// Instantiate the models
$logs_model = new Logs();
$sites_model = new Sites();
$config_model = new Config();