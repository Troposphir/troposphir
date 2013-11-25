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

class addLevelReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]['level']["ownerId"])) return; 
		if (!isset($json['body']['level']['name'])) return;
		if (!isset($json['body']['level']['description'])) return;
		if (!isset($json['body']['level']['editable'])) return;
		if (!isset($json['body']['level']['version'])) return;
		
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("INSERT INTO `@table` (ownerId, name, description, editable, version) 
			VALUES ('@ownerId', '@name', '@description', '@editable', '@version')", array(
			"table" 		=> $this->config["table_map"],
			"ownerId"		=> $json["body"]["level"]["ownerId"],
			"name"		 	=> $json["body"]["level"]['name'],
			"description" 	=> $json["body"]["level"]['description'],
			"editable" 		=> $json["body"]["level"]["editable"],
			"version"	 	=> $json["body"]["level"]['version']	
		));
		
		$statement = $db->query("SELECT id
			FROM `@table` 
			WHERE `ownerId`='@ownerId' AND `name`='@name' AND `description`='@description'", array(
			"table" 		=> $this->config["table_map"],
			"ownerId"		=> $json["body"]["level"]["ownerId"],
			"name"		 	=> $json["body"]["level"]['name'],
			"description" 	=> $json["body"]["level"]['description'],
			"editable" 		=> $json["body"]["level"]["editable"],
			"version"	 	=> $json["body"]["level"]['version']	
		));
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$row = $statement->fetch();
			$this->addBody("levelId", (integer)$row['id']);
		}
	}
}
?>