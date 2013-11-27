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

//INCOMPLETE
//'oid' somehow links different accounts together
if (!defined("INCLUDE_SCRIPT")) return;
class findAccountsReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json['body']['oid'])) return;
		
		$fields = array(
			'userId', 'username', 'cid', 'amt'
		);
		
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("SELECT @fields FROM `@table` WHERE `userId`='@userId'", array(
			"fields"    => $db->arrayToSQLGroup($fields, array("", "", "`")),
			"table" 	=> $this->config["table_user"],
			"userId" 	=> $json['body']['oid']
		));
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$accountList = array();
			for ($count = 0; $row = $statement->fetch(); $count++) {
				$account = array();
				$account['id']   = (integer)$row['userId'];
				$account['name'] = (string)$row['username'];
				
				$balance = array();
				$balance['cid'] = (integer)$row['cid'];
				$balance['amt'] = (integer)$row['amt'];
				$account['balance'] = $balance;
				$accountList[] = $account;
			}	
			$this->addBody("fres", array("results" => $accountList));
		}
		$db = null;
	}
}
?>