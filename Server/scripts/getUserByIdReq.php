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
class getUserByIdReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json['body']['uid'])) return;
		
		//Get user
		$db = $this->getConnection();
		$statement = $db->prepare("SELECT * 
			FROM `" . $this->config['table_user'] . "` 
			WHERE `userId`=:userId");
		$statement->bindParam(':userId', $json['body']['uid'], PDO::PARAM_INT);
		$statement->execute();
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("USER_NOT_FOUND");
		} else {
			$row = $statement->fetch();
			
			//Setup user:{ array
			$user = array();
			$user['username']  = (string)$row['username'];
			$user['created']   = (integer)$row['created'];
			$user['flags']     = (integer)$row['flags'];
			$user['locale']    = (string)$row['locale'];
			
			//Setup user:{prop:{ array
			$props = array();
			$props['development'] = ($row['development'] == 1) ? 'true' : 'false';;
			$props['external']    = ($row['external'] == 1) ? 'true' : 'false';
			$user['props'] 		  = $props;
			
			$this->addBody("verified", true); //Modified by DLL
			$this->addBody("xpp", intval($row['xpp']));
			$this->addBody('isClubMember', ((bool)$row['isClubMember']) ? true : false);
			$this->addBody('paidBy', (string)$row['paidBy']);
			$this->addBody("user", $user);
		}
	}
}
?>
