<?php

if(file_exists('../config.php')) {
	require_once '../config.php';
}
else
{
	require_once '../../config.php';
}

include_once 'auth.php';
include_once '../lang/' . LOCALISATION . '.php';

include_once 'classes/logs.php';
include_once 'classes/sites.php';
include_once 'classes/config.php';

// Instantiate the models
$logs_model = new Logs();
$sites_model = new Sites();
$config_model = new Config();