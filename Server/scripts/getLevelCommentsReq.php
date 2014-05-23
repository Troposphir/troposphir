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

/*
{"freq":
    {"start":0,
     "_t":"freq",
     "blockSize":10,
     "skipCount":"false"
    },
    "_t":"getLevelCommentsReq",
    "levelId":6
}
*/

if (!defined("INCLUDE_SCRIPT")) return;
class getLevelCommentsReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["levelId"]) ||
			!is_numeric($json["body"]["levelId"]) ||
			!isset($json["body"]["freq"]["start"]) ||
			!is_numeric($json["body"]["freq"]["start"])) {
			$this->error("MALFORMED");
			return;
		}
		$fields = array( 
			"commentId", "userId", "body"
		);
        $begin = $json["body"]["freq"]["start"];
        $length = $json["body"]["freq"]["blockSize"];
        if($begin < 0) $begin = 0;
        
		//Retrieve level comments
		$db = $this->getConnection();
		$statement = $db->prepare("SELECT `commentId`, `userId`, `body` 
			FROM `" . $this->config['table_comments'] . "` 
			WHERE `levelId` = :levelId 
			ORDER BY commentId DESC 
			LIMIT 0, 9999999");
		$statement->bindParam(':levelId', $json['body']['levelId'], PDO::PARAM_INT);
		$statement->execute();
        		
		$all = $statement->fetchAll();
		if ($all == false || count($all) <= 0) {
			//Return an empty result
			$fres = array(
				"total"     => 0,
				"results" 	=> array()
			);
			$this->addBody("fres", $fres);
		} else {
			//Return comments
			$commentList = array();
			for ($i = $begin; $i < $begin + $length && $i < count($all); $i++) {
				$row = $all[$i];
				$comment = array();
				
				$comment["id"]      = (integer)$row["commentId"];
				$comment["uid"]     = (integer)$row["userId"];
				$comment["body"]    = (string)$row["body"];		
				$commentList[] = $comment;
			}
			$fres = array(
				"results" 	=> $commentList,
				"total" 	=> count($all)
			);
			$this->addBody("fres", $fres);
		}
	}
}
?>
