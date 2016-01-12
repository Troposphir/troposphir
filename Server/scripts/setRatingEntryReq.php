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
			FROM `" . $this->config['table_ratings'] . "`
			WHERE `userId`=:userId
			AND `levelId`=:levelId");
		$statement->bindParam(':userId', $json['body']['ratingKey']['raterId'], PDO::PARAM_INT);
		$statement->bindParam(':levelId', $json['body']['ratingKey']['thingKey']['id'], PDO::PARAM_INT);
		$statement->execute();
		
		$entryResult = $statement->fetch();
		if ($entryResult == false) {
			//no entry exists for this user
			
			//insert a new entry
			$statement = $db->prepare("INSERT INTO `" . $this->config["table_ratings"] . "` (`userId`, `levelId`, `$field`) 
				VALUES (:userid, :levelId, :rating)");
			$statement->bindValue(':userid', $json['body']['ratingKey']['raterId'], PDO::PARAM_INT);
			$statement->bindValue(':levelId', $json['body']['ratingKey']['thingKey']['id'], PDO::PARAM_INT);
			$statement->bindValue(':rating', $json['body']['rating'], PDO::PARAM_INT);
			$statement->execute();
		} else {
			//The record entry already exists for user.
			
			//Modify the existing record entry to reflect change.
			$statement = $db->prepare("UPDATE " . $this->config['table_ratings'] . " SET `$field`=:userVote 
				WHERE `userId`=:userId
				AND `levelId`=:levelId");
			$statement->bindParam(':userVote', $json['body']['rating'], PDO::PARAM_INT);
			$statement->bindParam(':userId', $json['body']['ratingKey']['raterId'], PDO::PARAM_INT);
			$statement->bindParam(':levelId', $json['body']['ratingKey']['thingKey']['id'], PDO::PARAM_INT);
			$statement->execute();
		}

        //apply entry rating to level

        $statement = $db->prepare("SELECT IF(COUNT(`$field`), ROUND(SUM(`$field`)/COUNT(`$field`)/20), 0) as value FROM `".$this->config["table_ratings"]."` WHERE `$field` <> 0 AND `levelId`=:levelId");
        $statement->bindParam(':levelId', $json['body']['ratingKey']['thingKey']['id'], PDO::PARAM_INT); 
        $statement->execute();
        $rating = $statement->fetch()["value"];

        $statement = $db->prepare("UPDATE `".$this->config["table_map"]."`
            SET `$field`=:value
            WHERE `id`=:levelId");
        $statement->bindParam(':value', $rating, PDO::PARAM_INT);
        $statement->bindParam(':levelId', $json['body']['ratingKey']['thingKey']['id'], PDO::PARAM_INT);
        $statement->execute();
	}
}
?>
