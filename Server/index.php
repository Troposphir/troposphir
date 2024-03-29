<?php 
/*==============================================================================
  Troposphir - Part of the Troposphir Project
  Copyright (C) 2013  Troposphir Development Team

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License 
  along with this program.  If not, see <http://www.gnu.org/licenses/>.    
==============================================================================*/
//error_reporting(0);

if(isset($_REQUEST["json"])) {
	require('configs.php');
	require('./include/utils.php');
	require("./include/CRequest.php");
	header('X-Powered-By: Troposphir Beta');
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 01 Jan 1996 00:00:00 GMT');
	header('Content-Type: application/json');
			
	$json = json_decode($_REQUEST['json'], true);
	if (!isset($json['header'])) return;
	if (!isset($json['body'])) return;
	if (!isset($json['body']['_t'])) return;

	$reqtype = preg_replace("/[^a-zA-Z\_]/", "", basename($json['body']['_t']));
	if (!file_exists("./scripts/$reqtype.php")) return;

	define("INCLUDE_SCRIPT",  TRUE);
	require("./scripts/$reqtype.php");
	if (class_exists($reqtype)) {
		$request = new $reqtype($config);
		$request->work($json);
		//$request->addBody("requestSource", var_dump($_REQUEST));
		$request->send();
	}
} else {
	?>
	<!DOCTYPE html>
	<html>
		<head>
			<title>666 - Sanity not found</title>
		</head>
		<body>
			You shouldn't have come here... <br />
		</body>
	</html>
	<?php
	echo file_get_contents("php://input");
}
?>
