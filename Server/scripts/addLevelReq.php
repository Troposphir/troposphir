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
if (!defined("INCLUDE_SCRIPT")) return;
class addLevelReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]['level']["ownerId"])) return; 
		if (!isset($json['body']['level']['name'])) return;
		if (!isset($json['body']['level']['description'])) return;
		if (!isset($json['body']['level']['editable'])) return;
		if (!isset($json['body']['level']['version'])) return;
	
		//Validate the user via an IP check.
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);	
		$stmt = $db->prepare("SELECT ipAddress 
			FROM " . $this->config['table_user']  . 
			" WHERE `userId`=:userId");
		$stmt->bindValue(':userId', $json['body']['level']['ownerId'], PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt == false || $db->getRowCount($stmt) <= 0) {
			$this->error("NOT_FOUND");
			return;
		}
		
		$row = $stmt->fetch();
		if ($row['ipAddress'] != $_SERVER['REMOTE_ADDR']) {
			$this->log("Hacking attempt [addLevelReq()]: Attempting to modify another user's data.");
			return;
		}
		
		//Insert level data
		$stmt = $db->prepare("INSERT INTO " . $this->config['table_map'] . "
			(ownerId, name, description, editable, version)
			VALUES (:ownerId, :name, :description, :editable, :version)");
		$stmt->bindParam(':ownerId', $json['body']['level']['ownerId'], PDO::PARAM_INT);
		$stmt->bindParam(':name', $json['body']['level']['name'], PDO::PARAM_STR);
		$stmt->bindParam(':description', $json['body']['level']['description'], PDO::PARAM_STR);
		$json['body']['level']['editable'] = ($json['body']['level']['editable'] == true) ? 1 : 0;
		$stmt->bindParam(':editable', $json['body']['level']['editable'], PDO::PARAM_INT);
		$stmt->bindParam(':version', $json['body']['level']['version'], PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt == false || $db->getRowCount($stmt) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$itemId = $db->lastInsertId();
			if ($itemId == 0) return;
			$this->addBody("levelId", (integer)$itemId);
		}
	}
}
?>