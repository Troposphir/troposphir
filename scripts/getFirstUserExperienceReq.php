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

class getFirstUserExperienceReq extends RequestResponse {
	public function work($json) {
		$fields = array( 
			"id", "name", "description", "author", 
			"ownerId", "downloads", "dataId", 
			"screenshotId", "draft", "version",
 			"nextLevelId", "editable", "gcid", "editMode"
		);
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("SELECT @fields FROM @table", array(
			"fields" 	=> $db->arrayToSQLGroup($fields, array("", "", "`")),
			"table" 	=> $this->config["table_map"]
		));
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$levelList = array();
			for ($count = 0; $row = $statement->fetch(); $count++) {
				$level = array();
				$level["id"]            = (integer)$row["id"];
				$level["name"]          = (string)$row["name"];
				$level["description"]   = (string)$row["description"];
				$level["ownerId"]       = (integer)$row["ownerId"];
				$level["draft"]         = (strtolower($rows["draft"]) == 'true') ? true : false;
				$level["downloads"]     = (integer)$row["downloads"];
				$level["version"]       = (integer)$row["version"];
				$level["editable"]      = (strtolower($rows["editable"]) == 'true') ? true : false;
				$level["dataId"]        = (integer)$row["dataId"];
				$level["screenshotId"]  = (integer)$row["screenshotId"];
				
				$props = array();
				$props["gcid"]     = (string)$row["gcid"];
				$props["editMode"] = (string)$row["editMode"];
				$level["props"] = $props;
				
				$levelList[] = $level;
			}
			$fres = array(
				"total" => $count,
				"results" => $levelList
			);
			$this->addBody("fres", $fres);
		}
	}
}
?>