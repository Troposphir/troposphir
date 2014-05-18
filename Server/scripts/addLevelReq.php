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
if (!defined("INCLUDE_SCRIPT")) return;
class addLevelReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]['level']["ownerId"])) return; 
		if (!isset($json['body']['level']['name'])) return;
		if (!isset($json['body']['level']['description'])) return;
		if (!isset($json['body']['level']['editable'])) return;
		if (!isset($json['body']['level']['version'])) return;
	
		//Verify user operations
		if (!$this->verifyUserById($json['body']['level']['ownerId'])) {
			$this->log("[addLevelReq()]: Attempting to add a level under another user's id.");
			return;
		}
	
		//Get User
		$db = $this->getConnection();
		$stmt = $db->prepare("SELECT * 
			FROM `" . $this->config['table_user'] . "` 
			WHERE `userId`=:userId 
			LIMIT 1");
		$stmt->bindValue(':userId', $json['body']['level']['ownerId'], PDO::PARAM_INT);
		$stmt->execute();
		
        $user = $stmt->fetch();
		if ($user == false) {
				$this->error("NOT_FOUND");
		}
		
		//Insert level data
		$stmt = $db->prepare("INSERT INTO " . $this->config['table_map'] . "
			(ownerId, name, author, description, ct, editable, version)
			VALUES (:ownerId, :name, :author, :description, :ct, :editable, :version)");
		$stmt->bindParam(':ownerId', $json['body']['level']['ownerId'], PDO::PARAM_INT);
		$stmt->bindParam(':author', $user["username"], PDO::PARAM_STR);
		$stmt->bindParam(':name', $json['body']['level']['name'], PDO::PARAM_STR);
		$stmt->bindParam(':description', $json['body']['level']['description'], PDO::PARAM_STR);
		$stmt->bindParam(':ct', time(), PDO::PARAM_STR); //created time
		$json['body']['level']['editable'] = ($json['body']['level']['editable'] == true) ? 1 : 0;
		$stmt->bindParam(':editable', $json['body']['level']['editable'], PDO::PARAM_INT);
		$stmt->bindParam(':version', $json['body']['level']['version'], PDO::PARAM_INT);
		$stmt->execute();
		
		//Retrieve inserted level id
		$insertId = $db->lastInsertId();
		if ($insertId <= 0) {
			$this->error("NOT_FOUND");
			return;
		}
		
		//Set gcid equal to level id
		$stmt = $db->prepare("UPDATE `" . $this->config['table_map'] . "`
			SET `gcid`=:lastInsertId
			WHERE `id`=:lastInsertId");
		$stmt->bindParam(':lastInsertId', $insertId, PDO::PARAM_INT);
		$stmt->execute();
		
		//Build response
		$this->addBody("levelId", (integer)$insertId);
	}
}
?>