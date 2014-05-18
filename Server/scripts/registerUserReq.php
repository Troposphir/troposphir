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
//Todo: Implement token generation method from client.
error_reporting(-1);
if (!defined("INCLUDE_SCRIPT")) return;
class registerUserReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json['body']['username'])) return;
		if (!isset($json['body']['password'])) return;
		if (!isset($json['body']['email'])) return;
		if (!filter_var($json['body']['email'], FILTER_VALIDATE_EMAIL)) {
			//Invalid email format
			$this->error("EMAIL_INVALID");
			return;
		}
	
		//Constants
		$db = $this->getConnection();
	
		//Check if username or email already exists
		$statement = $db->prepare("SELECT `username`, `email` 
			FROM `" . $this->config['table_user'] . "` 
			WHERE `username`=:username
			OR `email`=:email");
		$statement->bindParam(':username', $json['body']['username'], PDO::PARAM_STR);
		$statement->bindParam(':email', $json['body']['email'], PDO::PARAM_STR);
		$statement->execute();
		$user = $statement->fetch();
		if (strcmp($user['username'], $json['body']['username']) == 0) {
			$this->error("USER_ALREADY_USED");
			return;
		} else if (strcmp($user['email'], $json['body']['username']) == 0) {
			$this->error("EMAIL_ALREADY_USED");
			return;
		}
		
		//Insert new account into table
		$statement = $db->prepare("INSERT INTO `" . $this->config['table_user']  . "` (username, password, email, userId, token, ipAddress) 
			VALUES(:username,:password,:email,:userId,:token,:ipAddress)");
		$statement->bindParam(':username', $json['body']['username'], PDO::PARAM_STR);
		$statement->bindValue(':password', md5($json['body']['password']), PDO::PARAM_STR);
		$statement->bindParam(':email', $json['body']['email'], PDO::PARAM_STR);
		$statement->bindValue(':userId', null, PDO::PARAM_INT);
		$statement->bindValue(':token', rand(100000000, 999999999), PDO::PARAM_INT);
		$statement->bindParam(':ipAddress', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
		$statement->execute();
	
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			//Get account from table
			$statement = $db->prepare("SELECT `username`,`token`,`userId` 
				FROM `" . $this->config['table_user'] . "` WHERE `username`=:username");
			$statement->bindParam(':username', $json['body']['username'], PDO::PARAM_STR);
			$statement->execute();
		
			if ($statement == false || $db->getRowCount($statement) <= 0) {
				$this->error("ACCOUNT_NOT_FOUND");
			} else {
				$row = $statement->fetch();
				$this->addBody("token", (string)$row['token']);
				$this->addBody("userId", (string)$row['userId']);
			}
		}
	}
}
?>