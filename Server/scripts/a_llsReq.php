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
if (!defined("INCLUDE_SCRIPT")) return;
class a_llsReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json["body"]["query"])) return;
		if (!isset($json["body"]["freq"]["start"])) return;
		if (!is_numeric($json["body"]["freq"]["start"])) return;
		if (!is_numeric($json["body"]["freq"]["blockSize"])) return;
		
		$fields = array( 
			"isLOTD", "xpReward", "xgms", "gms", "gmm", "gff", 
			"gsv", "gbs", "gde", "gdb", "gctf", "gab", "gra", "gco", 
			"gtc", "gmmp1", "gmmp2", "gmcp1", "gmcp2", "gmcdt", 
			"gmcff", "ast", "aal", "ghosts", "ipad", "dcap", "dmic", 
			"denc", "dpuc", "dcoc", "dtrc", "damc", "dphc", "ddoc", 
			"dkec", "dgcc", "dmvc", "dsbc", "dhzc", "dmuc", "dtmi", 
			"ddtm", "dttm", "dedc", "dtsc", "dopc", "dpoc", "deleted"
		);
		
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
		$query = str_replace('deleted=false', 'deleted=0', $query);
		$query = str_replace('deleted=true', 'deleted=1', $query);
	
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$stmt = $db->query("SELECT * FROM `@table` WHERE `deleted`=0  ORDER BY id DESC", array(
			"table" 	=> $this->config["table_map"],
			"query" 	=> $query
		));
		
		if ($stmt == false || $db->getRowCount($stmt) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$levelList = array();
			for ($count = 0; $row = $stmt->fetch(); $count++) {
				if ($count >= ($json['body']['freq']['start'] + $json['body']['freq']['blockSize'])) continue;
				if ($count < $json['body']['freq']['start']) continue;
				
				$level = array();
				$level["id"]           = (string)$row["id"];
				$level["name"]         = (string)$row["name"];
				$level["description"]  = (string)$row["description"];
				$level["ownerId"]      = (string)$row["ownerId"];
				$level["dc"]           = (string)$row["dc"]; 
				$level["version"]      = (string)$row["version"];
				$level["draft"]        = ((bool)$row['draft']) ? 'true' : 'false';
				$level["author"]       = (string)$row["author"];
				$level["editable"]     = ((bool)$row['editable']) ? 'true' : 'false';
				$level['screenshotId'] = (string)$row['screenshotId'];
				$level['rating']       = (string)$row['rating'];
				$level['difficulty']   = (string)$row['difficulty'];
				
				foreach ($fields as $field) {
					if ($field == 'deleted') continue;
					$level[$field] = $row[$field];
				}
				$level['isLOTD']    = ((bool)$level['isLOTD']) ? 'true' : 'false';
				$level["is.lotd"]   = $level['isLOTD']; unset($level['isLOTD']);
				$level["xp.reward"] = $level['xpReward']; unset($level['xpReward']);
	
				
				$props = array();
				$props["gcid"]     = (string)$row["gcid"];
				$props["editMode"] = (string)$row["editMode"];
				$level["props"]    = $props;
				
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