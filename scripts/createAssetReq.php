<?php
/*==============================================================================
  Troposphir - Part of the Troposphir Project
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

//Todo: Added security measures.
if (!defined("INCLUDE_SCRIPT")) return;
class createAssetReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json["body"]["data"])) return;
		if (!isset($json["body"]["asset"])) return;
		if (!isset($json["body"]["asset"]["_t"])) return;
		if (!is_numeric($json["body"]["asset"]["ownerId"])) return;
	
		//Validate the user via an IP check.
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);	
		$ipStatement = $db->prepare("SELECT ipAddress 
			FROM " . $this->config['table_user']  . 
			" WHERE `userId`=:userId");
		$ipStatement->bindValue(':userId', $json['body']['asset']['ownerId'], PDO::PARAM_INT);
		$ipStatement->execute();
		$row = $ipStatement->fetch();

		if ($row['ipAddress'] != $_SERVER['REMOTE_ADDR']) {
			$this->log("Hacking attempt [createAssetReq()]: Attempting to modify another user's data.");
			return;
		}
	
		//Sanitize filename.
		//Todo: Set a limit for length of filename
		$filename = basename($json['body']['asset']['filename']);
		$filename = preg_replace("/[^a-zA-Z0-9\_\.]/", "", $filename);	
		$fileId = preg_replace("/[^0-9]/", "", $filename);
		if ($fileId == null) return;
		
		//---UPLOAD .PNG FILE---//
		if ($json["body"]["asset"]["_t"] == "imageAsset") {
			//Validate filename
			if (!preg_match("/^[a-zA-z]+\_\d+\.png$/", $filename)) return;
			
			$dir = '';
			if (startsWith($filename, 'MapImage_')) $dir = 'maps';
			else if (startsWith($filename, 'AvatarImage_')) $dir = 'avatars';
			
			//Upload file
			$my_file = $this->config['dir_imgs'] . "/$dir/$filename";
			$handle = fopen($my_file, 'w') or die("");
			fwrite($handle, pack("H*", $json['body']['data']));
			fclose($handle);
			
			//Update level data id
			$statement = $db->prepare("UPDATE " . $this->config['table_user'] . 
				" SET `screenshotId`=:screenshotId " . 
				" WHERE `userId`=:userId");
			$statement->bindValue(':screenshotId', $fileId, PDO::PARAM_INT);
			$statement->bindValue(':userId', $json['body']['asset']['ownerId'], PDO::PARAM_INT);
			$statement->execute();
			
			$this->addBody("asset", array("id" => (integer)$fileId));
	
		//---UPLOAD .ATMO FILE---//
		} else if ($json["body"]["asset"]["_t"] == "asset") {
			//Validate filename
			if (!preg_match("/^[a-zA-z]+\_\d+\.atmo$/", $filename)) return;
			
			//Upload file
			$my_file = $this->config['dir_maps'] . "/$fileId";
			$handle = fopen($my_file, 'w') or die("");
			fwrite($handle, pack("H*", $json['body']['data']));
			fclose($handle);
		
			//Update level data id
			$statement = $db->prepare("UPDATE " . $this->config['table_map'] . 
				" SET `dataId`=:dataId " . 
				" WHERE `id`=:id");
			$statement->bindValue(':dataId', $fileId, PDO::PARAM_INT);
			$statement->bindValue(':id', $fileId, PDO::PARAM_INT);
			$statement->execute();
			
			$this->addBody("asset", array("id" => (integer)$fileId));
		}
	}
}
?>