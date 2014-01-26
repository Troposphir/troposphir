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
        
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("SELECT @fields FROM @table WHERE `levelId` = '@levelId' ORDER BY commentId DESC LIMIT @start, @end", array(
			"fields" 	=> $db->arrayToSQLGroup($fields, array("", "", "`")),
			"table" 	=> $this->config["table_comments"],
			"levelId" 	=> $json["body"]["levelId"],
			"start" 	=> $begin,
            "end"       => $begin + $length
		));
        		
		$all = $statement->fetchAll();
		if ($all == false || count($all) <= 0) {
			$fres = array(
				"total"     => 0,
				"results" 	=> array()
			);
			$this->addBody("fres", $fres);
		} else {
			$commentList = array();
			for ($count = 0; $count < count($all); $count++) {
				$row = $all[$count];
				$comment = array();
				
				$comment["id"]      = (integer)$row["commentId"];
				$comment["uid"]     = (integer)$row["userId"];
				$comment["body"]    = (string)$row["body"];
				
				$commentList[] = $comment;
			}
			$fres = array(
				"results" 	=> $commentList,
				"total" 	=> $count
			);
			$this->addBody("fres", $fres);
		}
	}
}
?>
