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
class getLevelByIdReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["cid"]) || 
			!is_numeric($json["body"]["cid"]) ||
			!isset($json["body"]["freq"])) {
			return;
		}
		$fields = array("contentId", "userId", "score");
		$db = $this->getConnection();
		$statement = $db->query("SELECT @fields FROM @table WHERE `contentId`=@contentId ORDER BY `score` LIMIT @start,@size", array(
				"fields" 	=> $db->arrayToSQLGroup($fields, array("(", ")", "`")),
				"table" 	=> $this->config["table_score"],
				"contentId" => $json['body']['cid'],
				"start" 	=> $json['body']['freq']['start'],
				"size" 		=> $json['body']['freq']['blockSize']
		));
		if (count($row) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$scores = array();
			for ($count = 0; $row = $statement->fetch(); $count++) {
				$scores[] = array(
					"uid" 	=> $row["contentId"],
					"s1" 	=> $row["s1"]
				);
			}
			$this->addBody("fres", array(
				"results" => scores
			));
		}
	}
	
}
?>