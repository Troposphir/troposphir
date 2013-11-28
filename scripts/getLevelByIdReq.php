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
		$statement = $db->prepare("SELECT " . $db->arrayToSQLGroup($fields, array("", "", "`")) .
		" FROM " . $this->config["table_map"] .
		" WHERE `id`=:levelId");
		$statement->bindValue(':levelId', $json['body']['levelId'], PDO::PARAM_INT);
		$statement->execute();
		
		$row = $statement->fetch();
		if ($row == false || count($row) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$level = array();
			
			$level["id"]          = (integer)$row["id"];
			$level["name"]        = (string)$row["name"];
			$level["description"] = (string)$row["description"];
			$level["author"]      = (string)$row["author"];
			$level["ownerId"]     = (integer)$row["ownerId"];
			$level["downloads"]   = (integer)$row["downloads"];
			$level["dataId"]      = (integer)$row["dataId"];
			$level["screenshotId"]= (integer)$row["screenshotId"];
			$level["draft"]       = ((bool)$row['draft']) ? true : false;
			$level["version"]     = (integer)$row["version"];
			$level["nextLevelId"] = (integer)$row["nextLevelId"];
			$level["editable"]    = ((bool)$row['editable']) ? true : false;
			
			$props = array();
			$props["gcid"]     = (string)$row["gcid"];
			$props["editMode"] = (string)$row["editMode"];
			$level["props"] = $props;
			
			$this->addBody("level", $level);
		}
	}
	
}
?>