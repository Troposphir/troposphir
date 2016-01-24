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
==============================================================================*/

if (!defined("INCLUDE_SCRIPT")) return;
class getLevelSessionBatchReq extends RequestResponse {
	public function work($json) {
		if (!isset($json['body']['levels'])) return;
		if (!isset($json['header']['auth'])) return; //Using this to get the user since we don't have a userId

		$db = $this->getConnection();
    $statement = $db->query("SELECT userId FROM " . $this->config['table_user'] . " WHERE token = " . $json['header']['auth'], null);
    $userId = 0;
    for ($count = 0; $row = $statement->fetch(); $count++) {
			$userId = $row['userId'];
		}

    $levels = $json['body']['levels'];
		$levelsAsQuery = "";
    foreach($levels as $lv){
      if($levelsAsQuery != "")
        $levelsAsQuery .= " OR `levelId`=";
      $levelsAsQuery .= $lv;
    }

		$statement = $db->query("SELECT levelId, userId levelSessionMode, state FROM " . $this->config['table_playRecord'] . " WHERE (levelId = " . $levelsAsQuery . ") AND userId = '".$userId."'", null);
    $sessions = array();
    for($count = 0; $row = $statement->fetch(); $count++){
      $session = array();
      $session['id'] = (integer)$row['levelId'];
      $session['levelId'] = (integer)$row['levelId'];
			$session['sessionId'] = (integer)$row['levelId'];
      $session['state'] = $row['state'];
      $session['lastUpdate'] = 123;
			$session['levelSessionMode'] = "SOLO";
			$session['userId'] = (integer)$userId;
      $sessions[] = $session;
    }

    $this->addBody("fres", array("results" => $sessions, "total" => count($sessions)));
	}
}
?>
