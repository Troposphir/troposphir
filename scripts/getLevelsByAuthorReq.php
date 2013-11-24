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

class getLevelsByAuthorReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["authorId"]) || 
			!is_numeric($json["body"]["authorId"]) ||
			!isset($json["body"]["freq"]["start"]) ||
			!is_numeric($json["body"]["freq"]["start"])) {
			$this->error("MALFORMED");
			return;
		}
		$fields = array( 
			"id", "name", "description", "author", 
			"ownerId", "downloads", "dataId", 
			"screenshotId", "draft", "version",
 			"nextLevelId", "editable", "gcid"
		);
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("SELECT @fields FROM @table WHERE `ownerId` = '@ownerId' LIMIT @start,9999999999", array(
			"fields" 	=> $db->arrayToSQLGroup($fields, array("", "", "`")),
			"table" 	=> $this->config["table_map"],
			"ownerId" 	=> $json["body"]["authorId"],
			"start" 	=> $json["body"]["freq"]["start"]
		));
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$levelList = array();
			for ($count = 0; $row = $results->fetch(); $count++) {
				$level = array();
				
				$level["id"]          = (integer)$row["id"];
				$level["name"]        = (string)$row["name"];
				$level["description"] = (string)$row["description"];
				$level["author"]      = (string)$row["author"];
				$level["ownerId"]     = (integer)$row["ownerId"];
				$level["downloads"]   = (integer)$row["downloads"];
				$level["rating"]      = (string)$row["rating"];
				$level["difficulty"]  = (string)$row["difficulty"];
				$level["dataId"]      = (integer)$row["dataId"];
				$level["screenshotId"]= (integer)$row["screenshotId"];
				$level["draft"]       = (bool)$row["draft"];
				$level["version"]     = (integer)$row["version"];
				$level["editable"]    = (bool)$row["editable"];
			
				$props = array();
				$props["gcid"]     = (string)$row["gcid"];
				$props["editMode"] = (string)$row["editMode"];				
				$level["props"] = $props;
			
				$lc = array("props" => array());
				$level["lc"] = $lc;
				
				$levelList[] = $level;
			}
			$fres = array(
				"results" 	=> $levelList,
				"count" 	=> $count
			);
			$this->addBody("fres", $fres);
		}
	}
}
?>