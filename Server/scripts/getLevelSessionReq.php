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

//MAP SESSION REQUEST: {"header":{"_t":"mfheader", "auth":"438509398", "debug":"true"}, "_t":"mfmessage", "body":{"levelSessionMode":"SOLO", "_t":"getLevelSessionReq", "levelId":186, "userId":55}}
*/

if (!defined("INCLUDE_SCRIPT")) return;
class getLevelSessionReq extends RequestResponse {
	public function work($json) {

		if (!isset($json['body']['levelSessionMode'])) return;
    if (!isset($json['body']['levelId'])) return;
    if (!isset($json['body']['userId'])) return;

		$db = $this->getConnection();
    $getAmt = $db->query("SELECT levelId FROM " . $this->config['table_playRecord'] . " WHERE levelId = " . $json['body']['levelId'] . " AND userId = ".$json['body']['userId'], null);

    $results = 0;
    for(; $row = $getAmt->fetch(); $results++){}

    if($results < 1){
      $stmt = $db->prepare("INSERT INTO `" . $this->config["table_playRecord"] . "`
				(`levelId`, `userId`, `state`)
				VALUES (:levelId, :userId, 'ABANDONED')");
			$stmt->bindParam(':levelId', $json['body']['levelId'], PDO::PARAM_INT);
			$stmt->bindParam(':userId', $json['body']['userId'], PDO::PARAM_INT);
      $stmt->execute();
    }

    $statement = $db->query("SELECT levelId, userId levelSessionMode, state FROM " . $this->config['table_playRecord'] . " WHERE levelId = " . $json['body']['levelId'] . " AND userId = '".$json['body']['userId']."' AND levelSessionMode = '".$json['body']['levelSessionMode']."'", null);
    $sessions = array();
    $lvSess = array();
    for($count = 0; $row = $statement->fetch(); $count++){
      $session = array();
      $session['id'] = (integer)$row['levelId'];
      $session['levelId'] = (integer)$row['levelId'];
      $session['sessionId'] = (integer)$row['levelId'];
      $session['state'] = $row['state'];
      $session['lastUpdate'] = 123;
      $session['levelSessionMode'] = "SOLO";
      $session['userId'] = (integer)$json['body']['userId'];
      $lvSess = $session;
      $sessions[] = $session;
    }

    // I really don't know which of these is the ACTUAL way we're supposed to pass the results.
    // But this works so I'm not gonna mess with it.
    $this->addBody("fres", array("results" => $sessions, "total" => count($sessions)));
    $this->addBody("fres", array("levelSession" => $lvSess));
    $this->addBody("levelSession", $lvSess);

	}
}
?>
