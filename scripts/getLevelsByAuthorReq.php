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

class getLevelsByAuthorReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["authorId"]) || 
			!is_numeric($json["body"]["authorId"]) ||
			!isset($json["body"]["freq"]["start"]) ||
			!is_numeric($json["body"]["freq"]["start"])) {
			$this->error("MALFORMED");
			return;
		}
		$fields = array( //We don't need the myriad of properties stored in the maps table, so we'll query only the columns we need.
			"id", "name", "description", "author", 
			"ownerId", "downloads", "dataId", 
			"screenshotId", "draft", "version",
 			"nextLevelId", "editable", "gcid"
		);
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$results = $db->query("SELECT @fields FROM @table WHERE `ownerId` = '@owner' LIMIT @start,9999999999", array(
			"fields" 	=> $db->arrayToSQLGroup($fields, array("", "", "`")),
			"table" 	=> $this->config["table_map"],
			"owner" 	=> $json["body"]["authorId"],
			"start" 	=> $json["body"]["freq"]["start"]
		));
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$levelList = array();
			$count = 0
			for (; $row = $results->fetch(); $count++) {
				$level = array();
				foreach ($fields as $field) {
					$level[$field] = $this->convertJSONTypes($row[$field]);
				}
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