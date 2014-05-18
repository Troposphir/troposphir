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
 			"nextLevelId", "editable", "gcid",
			"rating", "difficulty", "editMode"
		);
		$db = $this->getConnection();
		$statement = $db->prepare("SELECT " . $db->arrayToSQLGroup($fields, array("", "", "`")) . 
			" FROM `" . $this->config['table_map'] . "`
			WHERE `ownerId` = :ownerId
			LIMIT :start,9999999999");
		$statement->bindParam(':ownerId', $json['body']['authorId'], PDO::PARAM_INT);
		$statement->bindParam(':start', $json['body']['freq']['start'], PDO::PARAM_INT);
		$statement->execute();
		
		$all = $statement->fetchAll();
		if ($all == false || count($all) <= 0) {
			$fres = array(
				"total"     => 0,
				"results" 	=> array()
			);
			$this->addBody("fres", $fres);
		} else {
			$levelList = array();
			for ($count = 0; $count < count($all); $count++) {
				$row = $all[$count];
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
				$level["draft"]       = ((bool)$row['draft']) ? true : false;
				$level["version"]     = (integer)$row["version"];
				$level["editable"]    = ((bool)$row['editable']) ? true : false;
			
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
				"total" 	=> $count
			);
			$this->addBody("fres", $fres);
		}
	}
}
?>
