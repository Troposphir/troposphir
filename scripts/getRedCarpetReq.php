<?php
/*==============================================================================
  Troposphir - Part of the Tropopshir Project
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
if (!defined("INCLUDE_SCRIPT")) return;
class getRedCarpetReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json['body']['userId'])) return;
		
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("SELECT `finished` FROM `@table` WHERE `userId`='@userId'", array(
			"table" 	=> $this->config["table_user"],
			"userId"    => $json['body']['userId']
		));
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error('NOT_FOUND');
		} else {
			$row = $statement->fetch();
			$this->addBody('finished', ((bool)$row['finished']) ? 'true' : 'false');
		}
	}
}
?>