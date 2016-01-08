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
//GET PLAYER'S CURRENTLY EQUIPPED ITEMS
//INCOMPLETE
if (!defined("INCLUDE_SCRIPT")) return;
class findItemInstanceSetsReq extends RequestResponse {
	public function work($json) {
		if (!isset($json['body']['oid'])) return; //userID
		if (!isset($json['body']['name'])) return;

		$itemSetsList = array();
		//Atmo client does this on update: "removals":[131, 133], "additions":[136, 137, 100],

		//1 - male character
		//2 - wooden sword
		//3 - female character
		//6 - Atmo Shirt
		//7 - skin_b_02

		$itemSetOne = array();
		$itemSetOne['id']     = 142;
		$itemSetOne['itemis'] = array(1,3, 6, 7, 2);
		$itemSetsList[] = $itemSetOne;

		$fres = array(
			"total" => 1,
			"results" => $itemSetsList
		);

		$this->addBody("fres", $fres);
	}
}
?>
