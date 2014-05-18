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
body:{"comment":"New C test", "_t":"addLevelCommentReq", "levelId":6, "userId":2}
*/
if (!defined("INCLUDE_SCRIPT")) return;
class addLevelCommentReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]['levelId'])) return; 
		if (!isset($json['body']['userId'])) return;
		if (!isset($json['body']['comment'])) return;
	
		//Validate the user via an IP check.
		if (!$this->verifyUserById($json['body']['userId'])) {
			$this->log("Hacking attempt[addLevelCommentReq()]: Attempting to add a comment under another user's name");
			return;
		}
		
		//Insert level data
		$db = $this->getConnection();
		$stmt = $db->prepare("INSERT INTO " . $this->config['table_comments'] . "
			(userId, levelId, body)
			VALUES (:userId, :levelId, :body)");
		$stmt->bindParam(':userId', $json['body']['userId'], PDO::PARAM_INT);
		$stmt->bindParam(':levelId', $json['body']['levelId'], PDO::PARAM_INT);
		$stmt->bindParam(':body', $json['body']['comment'], PDO::PARAM_STR);
		
		if ($stmt->execute()) {
			//Success
		} else {
			$this->error("INTERNAL");
			return;
		}
	}
}
?>