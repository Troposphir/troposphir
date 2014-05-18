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
body:{"_t":"deleteLevelCommentReq", "userId":2, "messageId":4}
*/

if (!defined("INCLUDE_SCRIPT")) return;
class deleteLevelCommentReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]['messageId'])) return; 
		if (!isset($json['body']['userId'])) return;
	
		//Validate the user via an IP check.
		if (!$this->verifyUserById($json['body']['userId'])) {
			$this->log("Hacking attempt [deleteLevelCommentReq()]: Attempting to modify another user's data.");
            $this->error("AUTH_NOT_PERMITTED");
			return;
		}
		
		//Insert level data
		$stmt = $db->prepare("DELETE FROM " . $this->config['table_comments'] .
			" WHERE `commentId`=:commentId AND `userId`=:userId");
		$stmt->bindParam(':commentId', $json['body']['messageId'], PDO::PARAM_INT);
		$stmt->bindParam(':userId', $json['body']['userId'], PDO::PARAM_INT);
        
		if ($stmt->execute() == false) {
			$this->error("NOT_FOUND");
		} else {
            // success
		}
	}
}
?>