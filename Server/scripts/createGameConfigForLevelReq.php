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

		//The client only sends this (createGameConfigForLevelReq) packet
		//when gcid == 0. 
		//Right now, it only sets gcid to the level's levelId. gcid is originally meant to be linked to a game config id table
		//that is meant to be created here..
		//Gcid is currently set to the level's id upon addLevelReq()
		/*
		$stmt = $this->getConnection()->prepare("UPDATE " . $this->config['table_map'] . 
			" SET `gcid`=:levelId 
			  WHERE `id`=:id");
		$stmt->bindParam(':levelId', $json["body"]["levelId"], PDO::PARAM_INT);
		$stmt->bindParam(':id', $json["body"]["levelId"],PDO::PARAM_INT);
		$stmt->execute();
		*/
		$this->addBody("gameConfig", array("id" => $json["body"]["levelId"]));
	}
}
?>