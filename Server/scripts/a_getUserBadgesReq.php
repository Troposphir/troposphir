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
class a_getUserBadgesReq extends RequestResponse {
	public function work($json) {
		if(!isset($json['body']['uid'])) return;
		
		$db = $this->getConnection();
		$statement = $db->prepare("SELECT `wins`, `losses`, `abandons` 
			FROM `" . $this->config['table_user'] . "` 
			WHERE `userId`=:userId");
		$statement->bindValue(':userId', $json['body']['uid'], PDO::PARAM_INT);
		$statement->execute();
	
		$row = $statement->fetch();
		if ($row == false || count($row) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$this->addBody("won", (integer)$row['wins']);
			$this->addBody("lost", (integer)$row['losses']);
			$this->addBody("abandoned", (integer)$row['abandons']);
		}
	}
}
?>