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
class getLeaderboardReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["cid"]) ||
			!is_numeric($json["body"]["cid"]) ||
			!isset($json["body"]["freq"])) {
			return;
		}

		$db = $this->getConnection();
		//Retrieve leaderboard scores.
		$statement = $db->prepare("SELECT levelId, score, userId FROM " . $this->config["table_playRecord"] . "
			WHERE `levelId`=:levelId AND (`state`='WON' OR `state` = '' OR `state` is NULL)
			ORDER BY `score` DESC");
		$statement->bindParam(':levelId', $json['body']['cid'], PDO::PARAM_INT);
		$statement->execute();

		if ($statement == null || $statement == false) {
			$this->error("NOT_FOUND");
		} else {
			$scores = array();
			$row = null;
			$minCount = (int)$json["body"]["freq"]["start"];
			$amt = (int)$json['body']['freq']['blockSize'];
			$count = 0;
			$amtCount = 0;
			for (; $row = $statement->fetch(); $count++) {
				if($amtCount < $amt && $count >= $minCount){
					$scores[] = array(
						"uid"         => intval($row["userId"], 10),
						"s1"         => intval($row["score"], 10)
					);
					$amtCount++;
				}
      }

			$this->addBody("fres", array(
				"results" 	=> $scores,
				"total" 	=> $count
			));
		}
	}

}
?>
