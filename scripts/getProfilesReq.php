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
if (!defined("INCLUDE_SCRIPT")) return;
class getProfilesReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json['body']['uid'])) return;
		
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("SELECT * FROM `@table` WHERE `userId`='@userId'", array(
			"table" 	=> $this->config["table_user"],
			"userId" 	=> $json['body']['uid']
		));
		
		if ($statement == false || $db->getRowCount($statement) <= 0) {
			$this->error("NOT_FOUND");
		} else {
			$profileList = array();
			for ($count = 0; $row = $statement->fetch(); $count++) {
				$profile = array();
				$profile["id"]      = (integer)$row['userId'];
				$profile["created"] = (integer)$row['created'];
				
				$props = array();
				$props["avaid"]                 = (string)$row["avaid"];
				$props["signature"]             = (string)$row["signature"];
				$props["sessionToken"]          = (string)$row["sessionToken"];
				$props['isDev']                 = (string)$row['isDev'];
				$props["isLOTDMaster"]          = (string)$row["isLOTDMaster"];
				$props["isXPMaster"]            = (string)$row["isXPMaster"];
				$props["sapo"]                  = (string)$row["sapo"];
				$props["vehicleInstanceSetId"]  = (string)$row["vehicleInstanceSetId"];
				$props["activableItemShorcuts"] = (string)$row["activableItemShorcuts"];
				$props["saInstalled"]           = (string)$row["saInstalled"];
				$profile["props"] = $props;
				
				$profileList[] = $profile;
			}
			$fres = array(
				"total" 	=> $count,
				"results" 	=> $profileList
			);
			$this->addBody("fres", $fres);
		}
		
		$db = null;
	}
}
?>