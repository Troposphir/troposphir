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
body:{"_t":"deleteLevelCommentReq", "userId":2, "messageId":4}
*/

if (!defined("INCLUDE_SCRIPT")) return;
class deleteLevelCommentReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]['messageId'])) return; 
		if (!isset($json['body']['userId'])) return;
	
		//Validate the user via an IP check.
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);	
		$stmt = $db->prepare("SELECT ipAddress FROM ".$this->config['table_user']." WHERE `userId`=:userId");
		$stmt->bindValue(':userId', $json['body']['userId'], PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt == false || $db->getRowCount($stmt) <= 0) {
			$this->error("NOT_FOUND");
			return;
		}
		
		$row = $stmt->fetch();
		if ($row['ipAddress'] != $_SERVER['REMOTE_ADDR'])
        {
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
            print_r($db->errorInfo());
			$this->error("NOT_FOUND");
		} else {
            // success
/*			$itemId = $db->lastInsertId();
			if ($itemId == 0) return;
			$this->addBody("commentId", (integer)$itemId);*/
		}
	}
}
?>