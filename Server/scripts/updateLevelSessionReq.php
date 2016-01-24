<?php
/*==============================================================================
  Troposphir - Part of the Troposphir Project
  Copyright (C) 2015  Troposphir Development Team

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
==============================================================================

//UPDATE MAP SESSION REQUEST: {"header":{"_t":"mfheader", "auth":"438509398", "debug":"true"}, "_t":"mfmessage", "body":{"sessionId":924, "levelSessionMode":"SOLO", "_t":"updateLevelSessionReq", "usedPerc":false, "state":"LOST"}}
*/

if (!defined("INCLUDE_SCRIPT")) return;
class updateLevelSessionReq extends RequestResponse {
	public function work($json) {
		if (!isset($json['body']['sessionId'])) return;
    if (!isset($json['body']['levelSessionMode'])) return;
    if (!isset($json['body']['state'])) return;
		if (!isset($json['header']['auth'])) return; //Using this to get the user since we don't have a userId

		$db = $this->getConnection();
    $statement = $db->query("SELECT userId FROM " . $this->config['table_user'] . " WHERE token = " . $json['header']['auth'], null);
    $userId = 0;
    for ($count = 0; $row = $statement->fetch(); $count++) {
			$userId = $row['userId'];
		}

    $stmt = $db->prepare("UPDATE " . $this->config["table_playRecord"] . "
        SET `levelSessionMode`=:mode, `state`=:state
        WHERE `userId`=:userId
        AND `levelId`=:levelId");
    $stmt->bindParam(':mode', $json['body']['levelSessionMode'], PDO::PARAM_STR);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':levelId', $json['body']['sessionId'], PDO::PARAM_INT);
    $stmt->bindParam(':state', $json['body']['state'], PDO::PARAM_STR);
    $stmt->execute();
	}
}
?>
