<?php 
/*==============================================================================
  Troposphir - Part of the Troposhir Project
  Copyright (C) 2013  Kevin Sonoda, Leonardo Giovanni Scur

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

error_reporting(0);
require('configs.php');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 01 Jan 1996 00:00:00 GMT');
header('Content-type: application/json');

if(isset($_REQUEST["json"])) {
	$json = get_magic_quotes_gpc() ? json_decode(stripslashes($_REQUEST['json']), true) : json_decode($_REQUEST['json'], true);
	if (!isset($json['header'])) return;
	if (!isset($json['body'])) return;
	if (!isset($json['body']['_t'])) return;

	$reqtype   = basename($json["body"]["_t"]); 
	require('./scripts/' . $reqtype . '.php');
	$request = new $reqtype($config);
	$request->work($json);
	$request->send();
}
?>
