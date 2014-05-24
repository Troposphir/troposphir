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
	
//Todo: Added security measures.
if (!defined("INCLUDE_SCRIPT")) return;
class createAssetReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json["body"]["data"])) return;
		if (!isset($json["body"]["asset"])) return;
		if (!isset($json["body"]["asset"]["_t"])) return;
		if (!is_numeric($json["body"]["asset"]["ownerId"])) return;
	
		//Verify user operations
		if (!$this->verifyUserById($json['body']['asset']['ownerId'])) {
			$this->log("Hacking attempt [createAssetReq()]: Attempting to modify another user's data.");
			return;
		}
		$db = $this->getConnection();

		//Sanitize filename.
		//Todo: Set a limit for length of filename
		$filename = basename($json['body']['asset']['filename']);
		$filename = preg_replace("/[^a-zA-Z0-9\_\.]/", "", $filename);	
		$fileId = preg_replace("/[^0-9]/", "", $filename);
		if ($fileId == null) return;
		
		/*   UPLOAD IMAGE FILE   */
		if ($json["body"]["asset"]["_t"] == "imageAsset") {
			if (!preg_match("/^[a-zA-z]+\_\d+\.png$/", $filename)) return; //Validate filename
			
			$dir = '';
			if (startsWith($filename, 'MapImage_')) {
				$dir = $this->config['dir_imgs'] . "/maps";
			} else if (startsWith($filename, 'AvatarImage_')) {
				$dir = $this->config['dir_avatars'];
			}	
			else return;
			
			$assetId = $this->UploadAsset($dir, $filename, ".png", $json['body']['data']);
			if ($assetId == -1) return;
			
			if (startsWith($filename, 'MapImage_')) {
				$stmt = $db->prepare("UPDATE " . $this->config['table_map'] . " 
					SET `screenshotId`=:screenshotId 
					WHERE `ownerId`=:ownerId
					AND `id`=:id");
				$stmt->bindValue(':screenshotId', $assetId, PDO::PARAM_INT);
				$stmt->bindValue(':ownerId', $json['body']['asset']['ownerId'], PDO::PARAM_INT);
				$stmt->bindValue(':id', $fileId, PDO::PARAM_INT);
				$stmt->execute();
			} else if (startsWith($filename, 'AvatarImage_')) {
				$stmt = $db->prepare("UPDATE " . $this->config['table_user'] . " 
					SET `avaid`=:avaid 
					WHERE `id`=:id");
				$stmt->bindValue(':avaid', $assetId, PDO::PARAM_INT);
				$stmt->bindValue(':id', $json['body']['asset']['ownerId'], PDO::PARAM_INT);
				$stmt->execute();
			}	
			$this->addBody("asset", array("id" => (integer)$assetId));

		/*   UPLOAD ATMO FILE   */
		} else if ($json["body"]["asset"]["_t"] == "asset") {
			if (!preg_match("/^[a-zA-z]+\_\d+\.atmo$/", $filename)) return; //Validate filename
				
			$assetId = $this->UploadAsset($this->config['dir_maps'], $filename, ".atmo", $json['body']['data']);
			if ($assetId == -1) return;
				
			//Update level data id
			$stmt = $db->prepare("UPDATE " . $this->config['table_map'] . 
				" SET `dataId`=:dataId 
				WHERE `ownerId`=:ownerId
				AND `id`=:id");
			$stmt->bindValue(':dataId', $assetId, PDO::PARAM_INT);
			$stmt->bindValue(':ownerId', $json['body']['asset']['ownerId'], PDO::PARAM_INT);
			$stmt->bindValue(':id', $fileId, PDO::PARAM_INT);
			$stmt->execute();
			
			$this->addBody("asset", array("id" => (integer)$assetId));
		}
	}
	
	public function UploadAsset($dir, $filename, $ext, $data) {
			//Create unique file name
			$asset_file = uniqid() . "_" . md5(mt_rand()) . $ext;
			while (file_exists("./$dir/" . $asset_file)) {
				$asset_file = uniqid() . "_" . md5(mt_rand()) . $ext;
			}
			$asset_size = strlen($data);
			$created = date('d/m/Y');
		
			//Write file
			$handle = fopen("./$dir/" . $asset_file, 'w');
			if ($handle == false) return -1;
			if (!fwrite($handle, pack("H*", $data))) return -1;
			fclose($handle);
			
			//Insert item into asset table
		    $itemId = 0;
			$stmt = $this->getConnection()->prepare("INSERT INTO " . $this->config['table_assets'] . " 
				(uploadedBy, origFilename, fileName, size, created) 
				VALUES (?, ?, ?, ?, ?)");
			$stmt->execute(array('', $filename, $asset_file, $asset_size, $created));
			$itemId = $this->getConnection()->lastInsertId();
			if ($itemId == 0) return -1;
			
			return $itemId;
	}
}
?>