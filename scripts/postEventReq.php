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

class postEventReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["event"]["type"]));
		switch ($json["body"]["event"]["type"]) {
			case "firstInstallAndLogin":
				break;
			case "guestPlay":
				$db = new Database($this->config['driver'], $this->config['host'], 
					$this->config['dbname'], $this->config['user'], $this->config['password']);
				$db->query("UPDATE `@table` SET `dc`=`dc`+1 WHERE `id`='@id'", array(
					"table" => $this->config["table_user"],
					"id" => $json["body"]["event"]["v3"]
				));
				$db = null;
				break;
		}
		$this->log("Handled event: ".$json["body"]["event"]["type"]);
	}
}
?>