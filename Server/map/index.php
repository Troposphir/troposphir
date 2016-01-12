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
require("../configs.php");
require("../include/CDatabase.php");

$lid = $_REQUEST['lid']; //map id
$id  = $_REQUEST['id'];  //asset id
if (isset($lid) && is_numeric($lid) &&
	isset($id) && is_numeric($id))
{ 
	//Cross check request with map data
	$db = new Database($config['driver'], $config['host'], $config['dbname'], $config['user'], $config['password']);	
	$stmt = $db->prepare("SELECT dataId FROM " . $config['table_map'] . " 
		WHERE `id`=:id
		AND `dataId`=:dataId");
	$stmt->bindParam(':id', $lid, PDO::PARAM_INT);
	$stmt->bindParam(':dataId', $id, PDO::PARAM_INT);
	$stmt->execute();
	
	//Invalid lid and id pair
	if ($stmt == false || $db->getRowCount($stmt) <= 0) {
		die();
	}
	
	//Retrieve map file
	$stmt = $db->prepare("SELECT fileName FROM " . $config['table_assets'] . " 
		WHERE `id`=:id");
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();

	//Shouldn't happen at this point, but asset id doesn't exist.
	if ($stmt == false || $db->getRowCount($stmt) <= 0) {
		die();
	}
	$row = $stmt->fetch();
	$file = $row['fileName'];
	
	if (file_exists("./$file")) {
		echo file_get_contents("./$file");
	}
}

?>