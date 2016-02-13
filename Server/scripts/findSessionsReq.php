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
//FIND MULTIPLAYER SESSIONS
if (!defined("INCLUDE_SCRIPT")) return;
class findSessionsReq extends RequestResponse {
	public function work($json) {
		if (!isset($json['body']['state'])) return;
		if($json['body']['state'] == "ACTIVE" || $json['body']['state'] == "CLOSED"){
			$stateSearch = $json['body']['state'];
		} else {
			$this->addBody("fres", array("total" => 0, "results" => array()));
			return;
		}

		$db = $this->getConnection();
		$statement = $db->query("SELECT * FROM `" . $this->config['table_mpSessions']."` WHERE `status`='".$stateSearch."'", null);

    $mpSessions = array();
		$sessionCount = 0;
		for ($count = 0; $row = $statement->fetch(); $count++) {
			$mpSession = array();
			$mpSession["id"]        				 = (integer)$row["id"];
			$mpSession["created"]   				 = 0;
	    $mpSession["lastUpdated"]  			 = 0;
	    $mpSession["ownerId"] 					 = (integer)$row["ownerId"];
	    $mpSession["levelId"] 					 = (integer)$row['levelId'];
	    $mpSession["ipAddress_external"] = (integer)$row['ipAddress_external'];
	    $mpSession["ipAddress_nat"] 	   = (integer)$row['ipAddress_nat'];
	    $mpSession["port"] 							 = (integer)$row['port'];
	    $mpSession["natPort"]						 = (integer)$row['natPort'];
	    $mpSession["name"]							 = $row["name"];
	    $mpSession["status"]						 = $row["status"];
	    $mpSession["maxUsers"]					 = (integer)$row["maxUsers"];

			$props = array();
			$props['mode']   								 = (string)$row['mode'];
			$props['connectedPlayers']			 = (string)$row['connectedPlayers'];
			$props['ip'] 										 = (string)$row['ip'];
			$props['nat']										 = ($row['nat'] == 1) ? 'true' : 'false';
			$mpSession['props']							 = $props;

			$mpSessions[] = $mpSession;
			$sessionCount++;
		}
		// $itemSet = array();
		// $itemSet["id"]        = 0;
		// $itemSet["created"]   = 0;
    // $itemSet["lastUpdated"]   = 0;
    // $itemSet["ownerId"] = 55;
    // $itemSet["levelId"] = 931;
    // $itemSet["ipAddress_external"] = 2130706433;
    // $itemSet["ipAddress_nat"] = 2130706433;
    // $itemSet["port"] = 25000;
    // $itemSet["natPort"] = 50000;
    // $itemSet["name"] = "My Game Test";
    // $itemSet["status"] = "ACTIVE";
    // $itemSet["maxUsers"] = 10;
		//
		// $props = array();
		// $props['mode']   = "FC";
		// $props['connectedPlayers'] = "55";
		// $props['ip']  = "127.0.0.1";
		// $props['nat'] = "false";
		// $itemSet['props'] = $props;
		//
		// $itemSets[] = $itemSet;

		$this->addBody("fres", array("total" => $sessionCount, "results" => $mpSessions));
	}
}
?>
