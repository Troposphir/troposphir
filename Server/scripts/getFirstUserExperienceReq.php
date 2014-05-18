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
class getFirstUserExperienceReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["freq"]["start"])) return;
		if (!is_numeric($json["body"]["freq"]["start"])) return;
		if (!is_numeric($json["body"]["freq"]["blockSize"])) return;
		
		$fields = array( 
			"id", "name", "description", "author", 
			"ownerId", "downloads", "dataId", 
			"screenshotId", "draft", "version",
 			"nextLevelId", "editable", "gcid", "editMode"
		);
		$json['body']['retDeleted'] = (strtolower($json['body']['retDeleted']) == 'true') ? 1 : 0;
		
		$db = $this->getConnection();
		$stmt = $db->prepare("SELECT id, name, description, author, ownerId,
				downloads, dataId, screenshotId, draft, version, nextLevelId,
				editable, gcid, editMode	
			FROM " . $this->config['table_map'] . "
			WHERE `deleted`=:deleted
			AND `draft`='false'
			ORDER BY ct DESC");
		$stmt->bindParam(':deleted', $json['body']['retDeleted'], PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt == false || $db->getRowCount($stmt) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$levelList = array();
			for ($count = 0; $row = $stmt->fetch(); $count++) {
				if ($count >= ($json['body']['freq']['start'] + $json['body']['freq']['blockSize'])) continue;
				if ($count < $json['body']['freq']['start']) continue;
				
				$level = array();
				$level["id"]            = (integer)$row["id"];
				$level["name"]          = (string)$row["name"];
				$level["description"]   = (string)$row["description"];
				$level["ownerId"]       = (integer)$row["ownerId"];
				$level["draft"]         = ((bool)$row['draft']) ? true : false;
				$level["downloads"]     = (integer)$row["downloads"];
				$level["version"]       = (integer)$row["version"];
				$level["editable"]      = ((bool)$row['editable']) ? true : false;
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