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
class setGameScoreReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["gameScore"]["uid"]) |
			!isset($json["body"]["gameScore"]["s1"]) |
			!isset($json["body"]["gameScore"]["configId"])) {
			return;
		}
		$db = $this->getConnection();
		$db->query("INSERT INTO @table @fields VALUES @values", array(
			"table" => $config["table_score"],
			"fields" => $db->arrayToSQLGroup(
				array(
					"contentId", 
					"userId", 
					"score"
				), 
				array("(", ")", "`")
			),
			"values" => $db->arrayToSQLGroup(
				array(
					$json["body"]["gameScore"]["configId"],
					$json["body"]["gameScore"]["uid"],
					$json["body"]["gameScore"]["s1"]
				), 
				array("(", ")", "`")
			)
		));
		
		$this->addBody("fres", array("results" => $itemList));
	}
}
?>