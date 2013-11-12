<?php
/*==============================================================================
  Troposphir - Part of the Tropopshir Project
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

class getLevelByIdReq extends RequestResponse {
	public function work($json) {
		$fields = array( //We don't need the myriad of properties stored in the maps table, so we'll query only the columns we need.
			"id", "name", "description", "author", 
			"ownerId", "downloads", "dataId", 
			"screenshotId", "draft", "version", 
			"nextLevelId", "editable", "gcid", "editMode"
		);
		if (!isset($json["body"]["levelId"]) || 
			!is_numeric($json["body"]["levelId"])){
			return;
		}
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$results = $db->query("SELECT @fields FROM `@table` WHERE `id`=@levelId", array(
			"fields" 	=> $db->arrayToSQLGroup($fields, array("","","`")),
			"table" 	=> $this->config["table_map"],
			"levelId" 	=> $json["body"]["levelId"]
		));
		if (empty($results)) {
			$this->error("NOT_FOUND");
			return;
		}
		$level = array();
		$row = $results->fetch();
		foreach ($fields as $field) {
			$level[$field] = $this->convertJSONTypes($row[$field]);
		}
		$this->addBody("level", $level);
	}
}
?>