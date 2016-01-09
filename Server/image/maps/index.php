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
//Get level screenshot
error_reporting(0);
require("../../configs.php");
require("../../include/CDatabase.php");

$id  = $_REQUEST['id'];  //asset id

header("Content-Type: image/png");

if (isset($id) && is_numeric($id))
{
	//Cross check request with map data
	$db = new Database($config['driver'], $config['host'], $config['dbname'], $config['user'], $config['password']);
	$stmt = $db->prepare("SELECT fileName FROM " . $config['table_assets'] . "
		WHERE `id`=:dataId");
	$stmt->bindParam(':dataId', $id, PDO::PARAM_INT);
	$stmt->execute();

	//Invalid uid and id pair
	$result = $stmt->fetch();
	if ($stmt == false) { die(); }

	$file = $result['fileName'];
	if (file_exists("./$file")) {
		echo file_get_contents("./$file");
	}
}


?>
