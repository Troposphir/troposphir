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
class postEventReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["event"]["type"])) return;
				
        $db = $this->getConnection();

        switch ($json["body"]["event"]["type"]) {
            case "loading":
                $stmt = $db->prepare("UPDATE `" . $this->config['table_map'] . "` 
                    SET `dc`=`dc`+1 
                    WHERE `id`=:levelId");      
                $stmt->bindValue(':levelId', $json['body']['event']['props']['levelId'], PDO::PARAM_INT);
                $stmt->execute();
                break;
            case "firstInstallAndLogin":
                break;
            case "guestPlay":
				$statement = $db->prepare("UPDATE `" . $this->config['table_map'] . "` 
					SET `dc`=`dc`+1 
					WHERE `id`=':id'");
				$statament->bindParam(':id', $json['body']['event']['v3'], PDO::PARAM_INT);
				$statement->execute();
				break;
		}
	}
}
?>