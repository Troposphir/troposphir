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

class a_llsReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json["body"]["query"])) return;
		if (!isset($json["body"]["freq"]["start"])) return;
		if (!is_numeric($json["body"]["freq"]["start"])) return;
		
		//Adjust user's query syntax to conform to appropriate database syntax.
		$query = $json["body"]["query"];
		$begpos = strpos($query, "AND ct:[");
		if ($begpos !== false) {
			$endpos = strpos($query, ']', $begpos) + 1; 
			$query = substr($query, 0, $begpos) . substr($query, $endpos, strlen($query));
		}  
		
		//Todo: Change dll instead from Lucene to SQL format.
		//This doesn't work in all cases.
		$query = str_replace(':', '=', $query);
		$query = str_replace('xis.lotd', "'xisLOTD'", $query);
		$query = str_replace('is.lotd', "`isLOTD`", $query);
		$query = str_replace('xp.reward', "'xpReward'", $query);
		$query = str_replace('xp.level', "'xpLevel'", $query);
		
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("SELECT * FROM `@table` WHERE @query", array(
			"table" 	=> $this->config["table_map"],
			"query" 	=> $query
		));
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$levelList = array();
			for ($count = 0; $row = $statement->fetch(); $count++) {
				$level = array();
				$level["id"]          = (string)$row["id"];
				$level["name"]        = (string)$row["name"];
				$level["description"] = (string)$row["description"];
				$level["ownerId"]     = (string)$row["ownerId"];
				$level["dc"]          = (string)$row["dc"]; 
				$level["version"]     = (string)$row["version"];
				$level["author"]      = (string)$row["author"];
				$level["draft"]       = (string)$row["draft"];
				$level["editable"]    = (string)$row["editable"];

				$props = array();
				$props["gcid"]     = (string)$row["gcid"];
				$props["editMode"] = (string)$row["editMode"];
				$level["props"]    = $props;
				
				$lc = array("props" => array());
				$lc["props"]["is.lotd"] = (string)$row["is.lotd"];
				$level["lc"]            = $lc;
				
				$levelList[] = $level;
			}
			$fres = array(
				"total" 	=> $count,
				"results" 	=> $levelList
			);
			$this->addBody("fres", $fres);
		}
	}
}
?>