<?php
session_start();



include 'classes/admins.php';
$admins_model = new Admins();
$admins_model->authenticate();