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

class a_getLcReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["lid"]) &&
			is_numeric($json["body"]["lid"])) return;
		
		$fields = array( 
			"is.lotd", "xp.reward", "xgms", "gms", "gmm", "gff", 
			"gsv", "gbs", "gde", "gdb", "gctf", "gab", "gra", "gco", 
			"gtc", "gmmp1", "gmmp2", "gmcp1", "gmcp2", "gmcdt", 
			"gmcff", "ast", "aal", "ghosts", "ipad", "dcap", "dmic", 
			"denc", "dpuc", "dcoc", "dtrc", "damc", "dphc", "ddoc", 
			"dkec", "dgcc", "dmvc", "dsbc", "dhzc", "dmuc", "dtmi", 
			"ddtm", "dttm", "dedc", "dtsc", "dopc", "dpoc"
		);
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("SELECT @fields FROM @table WHERE `id`=@levelId", array(
			"fields" 	=> $db->arrayToSQLGroup($fields, array("", "", "`")),
			"table" 	=> $this->config["table_map"],
			"levelId"   => $json["body"]["lid"]
		));
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$row = $statement->fetch();
			
			$props = array();
			foreach ($fields as $field) {
				$props[$field] = (string)$row[$field];
			}
			
			$this->addBody("lc", array("props" => $props));
		}
	}
}
?>