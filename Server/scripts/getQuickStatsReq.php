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
class getQuickStatsReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json['body']['userId'])) return;
		
		$fields = array(
			'wins', 'losses', 'abandons', 'memberSince', 'clubMemberSince', 
			'levelDesigned', 'levelComments', 'designModeTime'
		);
		
		//Get user 
		$db = $this->getConnection();
		$statement = $db->prepare("SELECT " . $db->arrayToSQLGroup($fields, array("", "", "`")) . " 
			FROM `" . $this->config['table_user'] . "` 
			WHERE `userId`=:userId"); 
		$statement->bindParam(':userId', $json['body']['userId'], PDO::PARAM_INT);
		$statement->execute();
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$row = $statement->fetch();
	
			$this->addBody("wins", (string)$row['wins']);	
			$this->addBody("losses", (string)$row['losses']);	
			$this->addBody("abandons", (string)$row['abandons']);	
			$this->addBody("memberSince", (string)$row['memberSince']);	
			$this->addBody("clubMemberSince", (string)$row['clubMemberSince']);	
			$this->addBody("levelDesigned", (string)$row['levelDesigned']);	
			$this->addBody("forumPost", "0");	
			$this->addBody("levelComments", (string)$row['levelComments']);	
			$this->addBody("designModeTime", (string)$row['designModeTime']);	
		}
	}
}
?>