<?php
$path_parts = pathinfo($_SERVER['SCRIPT_NAME']);
$path_parts["dirname"] = str_replace("\\", "/", $path_parts['dirname']);

//Misc
$CONFIG_SITE = $_SERVER['SERVER_NAME'] . $path_parts['dirname'];

//Database Configurations
$CONFIG_DRIVER     = 'mysql';
$CONFIG_USER       = 'user';
$CONFIG_PASSWORD   = 'password';
$CONFIG_DBNAME     = 'atmoServer';
$CONFIG_HOST       = 'localhost';
$CONFIG_TABLE_MAP  = 'maps';
$CONFIG_TABLE_USER = 'users';

//Directory Setup
$CONFIG_DIR_IMG     = 'image';
$CONFIG_DIR_MAPS    = 'map';
$CONFIG_DIR_ASSETS  = 'asset';
?>