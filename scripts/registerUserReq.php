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

//Todo: Implement token generation method from client.
if (!defined("INCLUDE_SCRIPT")) return;
class registerUserReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json['body']['username'])) return;
		if (!isset($json['body']['password'])) return;
		if (!isset($json['body']['email'])) return;
	
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		//Check if username already exists
		$statement = $db->query("SELECT `username` FROM `@table` WHERE `username`='@username'", array(
			"table" 	=> $this->config["table_user"],
			"username" 	=> $json['body']['username']
		));
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			//Insert new account into table
			$statement = $db->query("INSERT INTO `@table` (username, password, email, userId, token, ipAddress) VALUES('@username','@password', '@email', null," . rand(100000000, 999999999) . ", '@ipAddress')", array(
				"table" 	=> $this->config["table_user"],
				"username" 	=> $json['body']['username'],
				"password"  => md5($json['body']['password']),
				"email"     => $json['body']['email'],
				"ipAddress" => $_SERVER['REMOTE_ADDR']
 			));		
			
			if ($statement == false || $db->getRowCount($statement) <= 0) {
				$this->error("NOT_FOUND");
			} else {
				//Get account from table
				$statement = $db->query("SELECT `username`,`token`,`userId` FROM `@table` WHERE `username`='@username'", array(
					"table" 	=> $this->config["table_user"],
					"username" 	=> $json['body']['username']
				));
			
				if ($statement == false || $db->getRowCount($statement) <= 0) {
					$this->error("ACCOUNT_NOT_FOUND");
				} else {
					$row = $statement->fetch();
					$this->addBody("token", (string)$row['token']);
					$this->addBody("userId", (string)$row['userId']);
				}
			}
		} else {
			$this->error("USER_ALREADY_USED");
		}
		
		$db = null;
	}
}
?>