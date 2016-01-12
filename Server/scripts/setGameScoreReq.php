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

        // get previous scoreboard entry (if it exists)
		$stmt = $db->prepare("SELECT levelId, score, atmosGained, xpGained FROM " . $this->config["table_playRecord"] . " 
			WHERE `levelId`=:levelId
			AND `userId`=:userId");
		$stmt->bindParam(':levelId', $json['body']['gameScore']['configId'], PDO::PARAM_INT);
		$stmt->bindParam(':userId', $json['body']['gameScore']['uid'], PDO::PARAM_INT);
		$stmt->execute();
		$scoreboard_entry = $stmt->fetch();
        
        // get the level
        $level_stmt = $db->prepare("SELECT isLOTD,xpReward FROM " . $this->config["table_map"] . " 
            WHERE `id`=:levelId");
        $level_stmt->bindParam(':levelId', $json['body']['gameScore']['configId'], PDO::PARAM_INT);
        $level_stmt->execute();
        $level = $level_stmt->fetch();


        // If they've gotten XP/atmos from this level in the past, we'll subtract it from
        // the amount they deserve so they don't get too much.
        $atmos_previously_given = 0;
        $xp_previously_given = 0;
        if($scoreboard_entry != false && $scoreboard_entry != null) { // scoreboard entry already exists
            $atmos_previously_given = $scoreboard_entry["atmosGained"];
            $xp_previously_given = $scoreboard_entry["xpGained"];
        }

        // The fact that we're posting on the leaderboards means we beat the level.
        // Calculate the rewards.
        $atmos_deserved = 0;
        $xp_deserved = 0;

        if($level["isLOTD"])
        {
            $atmos_deserved = $this->config["atmos_for_lotd"];
            $xp_deserved = $this->config["xp_per_tier"] * $this->config["xp_tier_of_lotds"];
        }
        else if($level["xpReward"] > 0)
        {
            $atmos_deserved = $this->config["atmos_for_xp"];
            $xp_deserved = $this->config["xp_per_tier"] * $level["xpReward"];
        }
        else
        {
            $atmos_deserved = $this->config["atmos_for_level"];
            $xp_deserved = 0;
        }

        $atmos_to_give = $atmos_deserved - $atmos_previously_given;
        $xp_to_give = $xp_deserved - $xp_previously_given;

        if($atmos_to_give < 0) $atmos_to_give = 0;
        if($xp_to_give < 0) $xp_to_give = 0;

        // Give the rewards!
        $rewards = $db->prepare("UPDATE `".$this->config["table_user"]."` SET `xpp`=(`xpp`+:xp), `amt`=(`amt`+:atmos) WHERE `userId`=:userId");
        $rewards->bindParam(':xp', $xp_to_give, PDO::PARAM_INT);
        $rewards->bindParam(':atmos', $atmos_to_give, PDO::PARAM_INT);
        $rewards->bindParam('userId', $json['body']['gameScore']['uid'], PDO::PARAM_INT);
        $rewards->execute();

        // Post the scoreboard entry. Make sure to update with the total rewards that were given
		if ($scoreboard_entry == false || $scoreboard_entry == null) 
		{
			//User entry for play record does not exist, so insert.
			$stmt = $db->prepare("INSERT INTO " . $this->config["table_playRecord"] . " 
				(`levelId`, `userId`, `score`, `atmosGained`, `xpGained`) 
				VALUES (:levelId, :userId, :score, :atmosGained, :xpGained)");
			$stmt->bindParam(':levelId', $json['body']['gameScore']['configId'], PDO::PARAM_INT);
			$stmt->bindParam(':userId', $json['body']['gameScore']['uid'], PDO::PARAM_INT);
			$stmt->bindParam(':score', $json['body']['gameScore']['s1'], PDO::PARAM_INT);
            
            // put 'deserved' instead of 'to_give' because we want to keep track of the total amount we've given them
            $stmt->bindParam(':atmosGained', $atmos_deserved, PDO::PARAM_INT);
            $stmt->bindParam(':xpGained', $xp_deserved, PDO::PARAM_INT);

            $stmt->execute();

                $this->addBody("asdf", "inserted");

        } else {
            //User entry for play record already exists,
            //so update the score if necessary.
            if ($json['body']['gameScore']['s1'] > $scoreboard_entry['score']) {
                $stmt = $db->prepare("UPDATE " . $this->config["table_playRecord"] . " 
                    SET `score`=:score, `atmosGained`=:atmosGained, `xpGained`=:xpGained
                    WHERE `userId`=:userId
                    AND `levelId`=:levelId");
                $stmt->bindParam(':score', $json['body']['gameScore']['s1'], PDO::PARAM_INT);
                $stmt->bindParam(':userId', $json['body']['gameScore']['uid'], PDO::PARAM_INT);
                $stmt->bindParam(':levelId', $json['body']['gameScore']['configId'], PDO::PARAM_INT);
                $stmt->bindParam(':atmosGained', $atmos_deserved, PDO::PARAM_INT);
                $stmt->bindParam(':xpGained', $xp_deserved, PDO::PARAM_INT);
				$stmt->execute();
                $this->addBody("asdf", $json['body']['gameScore']['s1']);
			}

		}

		//$this->addBody("fres", array("results" => $itemList));
	}
}
?>