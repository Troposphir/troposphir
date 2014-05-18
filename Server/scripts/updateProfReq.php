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
class updateProfReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json['body']['profId'])) return;
		if (!isset($json['body']['props']['sessionToken'])) return;

		//Constants
		$db = $this->getConnection();

		
		//Build query
		$fields = array('sessionToken', 'sapo', 'avaid', 'activableItemShorcuts',
			'vehicleInstanceSetId', 'signature', 'saInstalled', 'userId');
		$params = array();
		$cond = array();
		$json['body']['props']['saInstalled'] = (strtolower($json['body']['props']['saInstalled']) == 'true') ? 1 : 0;	
		foreach ($json['body']['props'] as $propname => $propvalue){
			if (in_array($propname, $fields)) {
				$cond[] = "`$propname` = ?";
				$params[] = $propvalue;
			}
		}
		$params[] = $json['body']['profId']; //userId = ?
		
		//Update Profile
		$stmt = $db->prepare("UPDATE " . $this->config['table_user'] . "
			SET " . implode(' , ', $cond) .
			"WHERE `userId` = ?");
		$stmt->execute($params);
		
		//Retrieve profile information
		$stmt = $db->prepare("SELECT userId, created, avaid, signature, sessionToken,
				isLOTDMaster, isXPMaster, sapo, vehicleInstanceSetId, activableItemShorcuts, saInstalled 
			FROM `" . $this->config['table_user'] . "` 
			WHERE `userId`=:userId");
		$stmt->bindParam(':userId', $json['body']['profId'], PDO::PARAM_INT);
		$stmt->execute();
		
		if ($stmt == false || $db->getRowCount($stmt) <= 0) {
			$this->error('NOT_FOUND');
		} else {
			$row = $stmt->fetch();
			
			$profile = array();
			$profile['id']      = (integer)$row['userId'];
			$profile['created'] = (integer)$row['created'];
			$props = array();
			$props['avaid']                 = (string)$row['avaid'];
			$props['signature']             = (string)$row['signature'];
			$props['sessionToken']          = (string)$row['sessionToken'];
			$props['isLOTDMaster']          = ((bool)$row['isLOTDMaster']) ? 'true' : 'false';
			$props['isXPMaster']            = ((bool)$row['isXPMaster']) ? 'true' : 'false';
			$props['sapo']                  = (string)$row['sapo'];
			$props['vehicleInstanceSetId']  = (string)$row['vehicleInstanceSetId'];
			$props['activableItemShorcuts'] = (string)$row['activableItemShorcuts'];
			$props['saInstalled']           = ((int)$row['saInstalled'] == 1) ? 'true' : 'false';
			$profile['props'] = $props;
			$this->addBody('profile', $profile);
		}
	}
}
?>