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
class getRedCarpetReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json['body']['userId'])) return;
		if ($json['body']['userId'] == '1') {
			$this->addBody('finished', 'false');
			return;
		}
		$db = $this->getConnection();
		$statement = $db->prepare("SELECT `finished` 
			FROM `" . $this->config['table_user'] . "` 
			WHERE `userId`=:userId");
		$statement->bindParam(':userId', $json['body']['userId'], PDO::PARAM_INT);
		$statement->execute();
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error('NOT_FOUND');
		} else {
			$row = $statement->fetch();
			$this->addBody('finished', ((bool)$row['finished']) ? 'true' : 'false');
		}
	}
}
?>