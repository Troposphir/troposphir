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
    $itemSets = array();
		$itemSet = array();
		$itemSet["id"]        = 8;
		$itemSet["created"]   = 0;
    $itemSet["lastUpdated"]   = 0;
    $itemSet["ownerId"] = 55;
    $itemSet["levelId"] = 1;
    $itemSet["ipAddress_external"] = 19216811;
    $itemSet["ipAddress_nat"] = 127001;
    $itemSet["port"] = 4033;
    $itemSet["natPort"] = 4033;
    $itemSet["name"] = "My Game";
    $itemSet["status"] = "ACTIVE";
    $itemSet["maxUsers"] = 10;

		$props = array();
		$props['mode']   = 'comp';
		$props['connectedPlayers'] = 1;
		$props['ip']  = 19216811;
		$props['nat'] = 'false';
		$itemSet['props'] = $props;

    $itemSets[] = $itemSet;

		$this->addBody("fres", array("total" => 1, "results" => $itemSets));
	}
}
?>
