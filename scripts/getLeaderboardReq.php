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
class getLeaderboardReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["cid"]) || 
			!is_numeric($json["body"]["cid"]) ||
			!isset($json["body"]["freq"])) {
			return;
		}
		$db = $this->getConnection();
		$statement = $db->prepare("SELECT * FROM " . $this->config["table_scores"] . " 
			WHERE `levelId`=:levelId 
			ORDER BY `score` 
			LIMIT :start,:size");
		$statement->bindParam(':levelId', $json['body']['cid'], PDO::PARAM_INT);
		$statement->bindParam(':start', $json['body']['freq']['start'], PDO::PARAM_INT);
		$statement->bindParam(':size', $json['body']['freq']['blockSize'], PDO::PARAM_INT);
		$statement->execute();

		if ($statement == null || $statement == false) {
			$this->error("NOT_FOUND");
		} else {
			$scores = array();
			$row = null;
			$count = 0; 
			for (; $row = $statement->fetch(); $count++) {
				$scores[] = array(
					"uid"         => intval($row["userId"], 10),
					"s1"         => intval($row["score"], 10)
				);
            }
			$this->addBody("fres", array(
				"results" 	=> $scores,
				"total" 	=> $count
			));
		}
	}
	
}
?>
