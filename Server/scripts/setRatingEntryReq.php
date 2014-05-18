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

/*
	Request:
    "body":{"_t":"setRatingEntryReq", "rating":3, "ratingKey":{"_t":"ratingKey", "raterId":2, "thingKey":{"_t":"thingKey", "id":6, "type":3101}}}
                                   theActualRating                               userIdOfRater                            levelId  ratingType     
    
    Response:
        "body":{"rating":{"rating":100, "count":20}}
*/
if (!defined("INCLUDE_SCRIPT")) return;
class setRatingEntryReq extends RequestResponse {
	public function work($json) {
		//Constants
		$type_rating = 3101;
        $type_difficulty = 3102;

		//Input validation
		if (!isset($json['body']['rating'])) return;
        if (!isset($json['body']['ratingKey'])) return;
        if (!isset($json['body']['ratingKey']['thingKey'])) return;
		if ($json['body']['ratingKey']['rating'] < 0 && $json['body']['ratingKey']['rating'] > 100) return;
		
		//Check that user is valid.
		if (!$this->verifyUserById($json['body']['ratingKey']['raterId'])) {
			$this->log("Hacking attempt [setRatingEntryReq()]: Attempting to ilegally modify level data.");
			return;
		}
		
		//Code
        $field = "";
        if($json['body']['ratingKey']['thingKey']['type'] == $type_rating) {
			$field = "rating";
        } else {
            $field = "difficulty";
        }
		
		$db = $this->getConnection();
		//Check for existing entry
		$statement = $db->prepare("SELECT `levelId`, `userId`, `$field` 
			FROM `" . $this->config['table_playRecord'] . "`
			WHERE `userId`=:userId
			AND `levelId`=:levelId");
		$statement->bindParam(':userId', $json['body']['ratingKey']['raterId'], PDO::PARAM_INT);
		$statement->bindParam(':levelId', $json['body']['ratingKey']['thingKey']['id'], PDO::PARAM_INT);
		$statement->execute();
		
		$entryResult = $statement->fetch();
		if ($entryResult == false) {
			//no entry exists for this user
			
			//insert a new entry
			$statement = $db->prepare("INSERT INTO `" . $this->config["table_playRecord"] . "` (`userId`, `levelId`, `$field`) 
				VALUES (:userid, :levelId, :rating)");
			$statement->bindValue(':userid', $json['body']['ratingKey']['raterId'], PDO::PARAM_INT);
			$statement->bindValue(':levelId', $json['body']['ratingKey']['thingKey']['id'], PDO::PARAM_INT);
			$statement->bindValue(':rating', $json['body']['rating'], PDO::PARAM_INT);
			$statement->execute();
		
			//apply entry rating to level using following algorithm:
			//avgRating = ((currentRating*currentRatingCount) + userVote) / (currentRatingCount + 1)
			$statement = $db->prepare("UPDATE " . $this->config['table_map'] . " SET `$field`=(((`$field`*`" . $field . "Count`) + :userVote)/(`" . $field . "Count`+1)), `" . $field . "Count`=`" . $field . "Count`+1  WHERE `id`=:levelId");
			$statement->bindParam(':userVote', $t = ($json['body']['rating'] / 20), PDO::PARAM_INT);
			$statement->bindParam(':levelId', $json['body']['ratingKey']['thingKey']['id'], PDO::PARAM_INT); 
			$statement->execute();
			
		} else {
			//The record entry already exists for user.
			
			//Modify the existing record entry to reflect change.
			$statement = $db->prepare("UPDATE " . $this->config['table_playRecord'] . " SET `$field`=:userVote 
				WHERE `userId`=:userId
				AND `levelId`=:levelId");
			$statement->bindParam(':userVote', $json['body']['rating'], PDO::PARAM_INT);
			$statement->bindParam(':userId', $json['body']['ratingKey']['raterId'], PDO::PARAM_INT);
			$statement->bindParam(':levelId', $json['body']['ratingKey']['thingKey']['id'], PDO::PARAM_INT);
			$statement->execute();
			
			//Reflect the change in the level data.
			if ($entryResult[$field] == 0) {
				//This rating type (difficulty/star) was not initially set.
				//Set it using the following algorithm:
				//avgRating = ((currentRating*currentRatingCount) + (userVote/20)) / (currentRatingCount + 1)
				$statement = $db->prepare("UPDATE " . $this->config['table_map'] . " SET `$field`=(((`$field`*`" . $field . "Count`) + :userVote)/(`" . $field . "Count`+1)), `" . $field . "Count`=`" . $field . "Count`+1  WHERE `id`=:levelId");
				$statement->bindParam(':userVote', $t = ($json['body']['rating'] / 20), PDO::PARAM_INT);
				$statement->bindParam(':levelId', $json['body']['ratingKey']['thingKey']['id'], PDO::PARAM_INT); 
				$statement->execute();
			} else {
				//Set it using the following algorithm:
				//avgRating = ((currentRating * currentRatingCount) - (prevRating/20) + (newRating/20))/(currentRatingCount) 
				$statement = $db->prepare("UPDATE " . $this->config['table_map'] . " SET `$field`=((((`$field` * `" . $field . "Count`) - :prevUserVote) + :newUserVote)/(`" . $field . "Count`)) WHERE `id`=:levelId");
				$statement->bindParam(':prevUserVote', $t = ($entryResult[$field] / 20), PDO::PARAM_INT);
				$statement->bindParam(':newUserVote', $t = ($json['body']['rating'] / 20), PDO::PARAM_INT);
				$statement->bindParam(':levelId', $json['body']['ratingKey']['thingKey']['id'], PDO::PARAM_INT);
				$statement->execute();
			}
		}
	}
}
?>
