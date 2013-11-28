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
class a_getLcReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["lid"]) &&
			is_numeric($json["body"]["lid"])) return;
		
		$fields = array( 
			"isLOTD", "xpReward", "xgms", "gms", "gmm", "gff", 
			"gsv", "gbs", "gde", "gdb", "gctf", "gab", "gra", "gco", 
			"gtc", "gmmp1", "gmmp2", "gmcp1", "gmcp2", "gmcdt", 
			"gmcff", "ast", "aal", "ghosts", "ipad", "dcap", "dmic", 
			"denc", "dpuc", "dcoc", "dtrc", "damc", "dphc", "ddoc", 
			"dkec", "dgcc", "dmvc", "dsbc", "dhzc", "dmuc", "dtmi", 
			"ddtm", "dttm", "dedc", "dtsc", "dopc", "dpoc"
		);

		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->prepare("SELECT " . $db->arrayToSQLGroup($fields, array("", "", "`")) .  
		" FROM " . $this->config["table_map"] .
		" WHERE `id`=:lid");
		$statement->bindValue(':lid', $json['body']['lid'], PDO::PARAM_INT);
		$statement->execute();		
		$row = $statement->fetch();
		
		if ($row == false || count($row) <= 0) {
			$this->error("NOT_FOUND");
		} else {	
			$props = array();
			foreach ($fields as $field) {
				$props[$field] = (string)$row[$field];
			}
			$props["is.lotd"] = $props['isLOTD']; unset($props['isLOTD']);
			$props["xp.reward"] = $props['xpReward']; unset($props['xpReward']);
			
			$this->addBody("lc", array("props" => $props));
		}
	}
}
?>