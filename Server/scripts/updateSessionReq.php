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
class updateSessionReq extends RequestResponse {
	public function work($json) {
		/*
		UPDATE SESSION REQUEST: {"header":{"_t":"mfheader", "auth":"568197105", "debug":"true"}, "_t":"mfmessage", "body":{"sessionId":9, "_t":"updateSessionReq", "levelId":930, "props":{"connectedPlayers":",963,", "mode":"FC", "ip":"127.0.0.1,192.168.1.101", "nat":"False"}, "state":"CLOSED", "maxUsers":2}}
		*/
		if (!isset($json['body']['sessionId'])) return;
    if (!isset($json['body']['levelId'])) return;
    if (!isset($json['body']['props'])) return;
		if (!isset($json['body']['props']['connectedPlayers'])) return;
		if (!isset($json['body']['props']['mode'])) return;
		if (!isset($json['body']['props']['ip'])) return;
		if (!isset($json['body']['props']['nat'])) return;
		if (!isset($json['body']['state'])) return;
		if (!isset($json['body']['maxUsers'])) return;

		$db = $this->getConnection();
		$stmt = $db->prepare("UPDATE `" . $this->config["table_mpSessions"] . "`
				SET `levelId`=:levelId, `connectedPlayers`=:connectedPlayers, `mode`=:mode, `ip`=:ip, `nat`=:nat, `status`=:state, `maxUsers`=:maxUsers
        WHERE `id`=:sessionId");
    $stmt->bindParam(':sessionId', $json['body']['sessionId'], PDO::PARAM_INT);
    $stmt->bindParam(':levelId', $json['body']['levelId'], PDO::PARAM_INT);
		$stmt->bindParam(':connectedPlayers', $json['body']['props']['connectedPlayers'], PDO::PARAM_STR);
		$stmt->bindParam(':mode', $json['body']['props']['mode'], PDO::PARAM_STR);
		$IPs = $json['body']['props']['ip'] . "," . $_SERVER['REMOTE_ADDR'];
		$stmt->bindParam(':ip', $IPs, PDO::PARAM_STR);
		$natToInt = (strtolower($json['body']['props']['nat']) == 'true') ? 1 : 0;
		$stmt->bindParam(':nat', $natToInt, PDO::PARAM_INT);
		$stmt->bindParam(':state', $json['body']['state'], PDO::PARAM_STR);
		$stmt->bindParam(':maxUsers', $json['body']['maxUsers'], PDO::PARAM_INT);
    $stmt->execute();
	}
}
?>
