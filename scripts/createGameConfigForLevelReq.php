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

//Todo: Implement actual game config table
if (!defined("INCLUDE_SCRIPT")) return;
class createGameConfigForLevelReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["levelId"])) {
			return;
		}
		
		//Set level config
		//Right now, it only sets it to 1. The client only sends this packet
		//when gcid == 0. gcid is originally meant to be linked to a game config id table.
		$stmt = $this->getConnection()->prepare("UPDATE " . $this->config['table_map'] . 
			" SET gcid=1 
			  WHERE `id`=?");
		$stmt->execute($json["body"]["levelId"]);
		
		$this->addBody("gameConfig", array("id" => 1));
	}
}
?>
