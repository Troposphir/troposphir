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
class addSessionReq extends RequestResponse {
	public function work($json) {
    if (!isset($json['body']['mpSession'])) return;
		if (!isset($json['body']['mpSession']['props'])) return;

    //Build query
		$fields = array('ipAddress_local', 'ipAddress_external', 'name', 'maxUsers',
			'connectedPlayers', 'mode', 'ip', 'nat', 'status', 'natPort', 'port', 'levelId', 'ownerId', 'ipAddress_nat');
		$params = array();
		$cond = array();
		$json['body']['mpSession']['props']['nat'] = (strtolower($json['body']['mpSession']['props']['nat']) == 'true') ? 1 : 0;
    $json['body']['mpSession']['props']['ip'] = $json['body']['mpSession']['props']['ip'] . "," . $_SERVER['REMOTE_ADDR'];
    foreach ($json['body']['mpSession'] as $propname => $propvalue){
			if ($propname != "props" && in_array($propname, $fields)) {
				$cond[] = "`$propname`";
				$params[] = "\"".$propvalue."\"";
			}
		}
    foreach ($json['body']['mpSession']['props'] as $propname => $propvalue){
			if (in_array($propname, $fields)) {
				$cond[] = "$propname";
				$params[] = "\"".$propvalue."\"";
			}
		}

    $db = $this->getConnection();
    $statement = $db->prepare("INSERT INTO `" . $this->config['table_mpSessions']  . "` (".implode(', ', $cond).")
			VALUES(".implode(', ', $params).")");
		$statement->execute();
    $id = $db->lastInsertId();
    $this->addBody('sessionId', (int)$id);
	}
}
?>
