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
class getProfilesReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json['body']['uid'])) return;
		
		//Retrieve user profile
		$db = $this->getConnection();
		$statement = $db->prepare("SELECT * FROM `" . $this->config['table_user'] . "` 
			WHERE `userId`=:userId");
		$statement->bindParam(':userId', $json['body']['uid'], PDO::PARAM_INT);
		$statement->execute();
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			//Return user profile information
			$profileList = array();
			for ($count = 0; $row = $statement->fetch(); $count++) {
				$profile = array();
				$profile["id"]      = (integer)$row['userId'];
				$profile["created"] = (integer)$row['created'];
				
				$props = array();
				$props["avaid"]                 = (string)$row["avaid"];
				$props["signature"]             = (string)$row["signature"];
				$props["sessionToken"]          = (string)$row["sessionToken"];
				$props['isDev']                 = ((bool)$row['isDev']) ? 'true' : 'false';
				$props["isLOTDMaster"]          = ((bool)$row['isLOTDMaster']) ? 'true' : 'false';
				$props["isXPMaster"]            = ((bool)$row['isXPMaster']) ? 'true' : 'false';
				$props["sapo"]                  = (string)$row["sapo"];
				$props["vehicleInstanceSetId"]  = (string)$row["vehicleInstanceSetId"];
				$props["activableItemShorcuts"] = (string)$row["activableItemShorcuts"];
				$props["saInstalled"]           = ((bool)$row['saInstalled']) ? 'true' : 'false';
				$profile["props"] = $props;
				$profileList[] = $profile;
			}
			$fres = array(
				"total" 	=> $count,
				"results" 	=> $profileList
			);
			$this->addBody("fres", $fres);
		}
	}
}
?>