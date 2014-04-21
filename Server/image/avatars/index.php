<?php

//////////////////////////////////////////////////////////////////////////////
//  Copyright (C) 2013  Kevin Sonoda
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License 
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.    
//////////////////////////////////////////////////////////////////////////////
//Get level screenshot
error_reporting(0);
require("../../configs.php");
require("../../include/CDatabase.php");

$uid = $_REQUEST['uid']; //user id
$id  = $_REQUEST['id'];  //asset id
if (isset($uid) && is_numeric($uid) && isset($id) && is_numeric($id))
{ 
	//Cross check request with map data
	$db = new Database($config['driver'], $config['host'], $config['dbname'], $config['user'], $config['password']);	
	$stmt = $db->prepare("SELECT avaid, userId FROM " . $config['table_users'] . " 
		WHERE `userId`=:userId
		AND `avaid`=:avaid");
	$stmt->bindParam(':userId', $uid, PDO::PARAM_INT);
	$stmt->bindParam(':avaid', $id, PDO::PARAM_INT);
	$stmt->execute();
	
	//Invalid uid and id pair
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

	$file = $result['fileName'];	
	if (file_exists("./$file")) {
		echo file_get_contents("./$file");
	}
}


?>