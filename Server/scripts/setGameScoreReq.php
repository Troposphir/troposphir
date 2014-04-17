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
		//Make sure level score is not negative.
		if (!is_numeric($json['body']['gameScore']['s1'])) return;
		
		//Verify user operations
		if (!$this->verifyUserById($json['body']['gameScore']['uid'])) {
			$this->log("Hacking attempt [createAssetReq()]: Attempting to modify another user's data.");
			return;
		}
		
		$db = $this->getConnection();
		$stmt = $db->prepare("SELECT levelId, score FROM " . $this->config["table_playRecord"] . " 
			WHERE `levelId`=:levelId
			AND `userId`=:userId");
		$stmt->bindParam(':levelId', $json['body']['gameScore']['configId'], PDO::PARAM_INT);
		$stmt->bindParam(':userId', $json['body']['gameScore']['uid'], PDO::PARAM_INT);
		$stmt->execute();
		
		$result = $stmt->fetch();		
		if ($result == false || $result == null) 
		{
			//User entry for play record does not exist, so insert.
			$stmt = $db->prepare("INSERT INTO " . $this->config["table_playRecord"] . " 
				(`levelId`, `userId`, `score`) 
				VALUES (:levelId, :userId, :score)");
			$stmt->bindParam(':levelId', $json['body']['gameScore']['configId'], PDO::PARAM_INT);
			$stmt->bindParam(':userId', $json['body']['gameScore']['uid'], PDO::PARAM_INT);
			$stmt->bindParam(':score', $json['body']['gameScore']['s1'], PDO::PARAM_INT);
			$stmt->execute();
		} else {
			//User entry for play record already exists,
			//so update the score if necessary.
			if ($json['body']['gameScore']['s1'] > $result['score']) {
				$stmt = $db->prepare("UPDATE " . $this->config["table_playRecord"] . " 
					SET `score`=:score 
					WHERE `userId`=:userId
					AND `levelId`=:levelId");
				$stmt->bindParam(':score', $json['body']['gameScore']['s1'], PDO::PARAM_INT);
				$stmt->bindParam(':userId', $json['body']['gameScore']['uid'], PDO::PARAM_INT);
				$stmt->bindParam(':levelId', $json['body']['gameScore']['configId'], PDO::PARAM_INT);
				$stmt->execute();
			}
		}
		//$this->addBody("fres", array("results" => $itemList));
	}
}
?>