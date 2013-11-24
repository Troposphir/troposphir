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

class loginReq extends RequestResponse {
	public function work($json) {
		$fields = array ( 
			"token", "userId"
		);
		if (!isset($json["body"]["password"])) return;
		if (!isset($json["body"]["username"])) return;
		
		$db = new Database($this->config['driver'], $this->config['host'], 
					$this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("SELECT * FROM `@table` WHERE `username`='@username' AND `password`='@password'", array(
					"table" => $this->config["table_user"],
					"username" => $json["body"]["username"],
					"password" => md5($json["body"]["password"])
		));
	
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("USER_NOT_FOUND");
		} else {
			$user = $statement->fetch();
			$this->addBody("token", $user['token']);
			$this->addBody("userId", $user['userId']);
		}			
		$db = null;
	}
}
?>