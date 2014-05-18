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
class loginReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["password"])) return;
		if (!isset($json["body"]["username"])) return;
		
		$fields = array ( 
			"token", "userId"
		);
		
		$db = $this->getConnection();
		$statement = $db->prepare("SELECT `token`, `userId` 
			FROM `" . $this->config['table_user'] . "` 
			WHERE `username`=:username 
			AND `password`=:password");
		$statement->bindParam(':username', $json['body']['username'], PDO::PARAM_STR);
		$statement->bindValue(':password', md5($json['body']['password']), PDO::PARAM_STR);
		$statement->execute();
		$user = $statement->fetch();
			
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("USER_NOT_FOUND");
		} else {
			//Update user ipAddress
			$statement = $db->prepare("UPDATE `" . $this->config['table_user'] . "` 
				SET `ipAddress`=:ipAddress 
				WHERE `username`=:username");
			$statement->bindParam(':ipAddress', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
			$statement->bindParam(':username', $json['body']['username'], PDO::PARAM_STR);
			$statement->execute();
		
			$this->addBody("token",  (string)$user['token']);
			$this->addBody("userId", (integer)$user['userId']);
		}			
		$db = null;
	}
}
?>