<?php
/*============================================================================
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
error_reporting(0);
require("../configs.php");

//Try to retrieve level screenshot
$uid = $_REQUEST['lid']; //user id
$id  = $_REQUEST['id'];  //asset id

//Try to retrieve item icon
$item = $_REQUEST['item'];

//Requesting level screenshot?
if (isset($uid) && is_numeric($uid) && isset($id) && is_numeric($id))
{ 
	//Cross check request with map data
	$db = new PDO($config['driver'] . ":host=" . $config['host'] . ";dbname=" . $config['dbname'], $config['user'], $config['password']);
	$stmt = $db->prepare("SELECT avaid, userId FROM " . $config['table_user'] . " 
		WHERE `userId`=:userId
		AND `avaid`=:avaid");
	$stmt->bindParam(':userId', $uid, PDO::PARAM_INT);
	$stmt->bindParam(':avaid', $id, PDO::PARAM_INT);
	$stmt->execute();
	
	//Invalid uid and id pair?
	$result = $stmt->fetch();
	if ($stmt == false) { die(); }
	
	//Retrieve map file
	$stmt = $db->prepare("SELECT fileName FROM " . $config['table_assets'] . " 
		WHERE `id`=:id");
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();

	//Shouldn't happen at this point, but asset id doesn't exist.
	$result = $stmt->fetch();
	if ($stmt == false) { die(); }

	$file_name = $result['fileName'];
	$file_dir = "./maps/$file_name";
	
	if (file_exists($file_dir)) {
		echo file_get_contents($file_dir);
	}
} 
else if (isset($item)) 
{
	//Requesting icon?
	$item_name = preg_replace('/[^a-zA-Z0-9\_]/', "", basename($item));
	$item_dir = "./asseticons/$item_name.png";
	
	if (file_exists($item_dir)) {
		echo file_get_contents($item_dir);
	}	
}

?>