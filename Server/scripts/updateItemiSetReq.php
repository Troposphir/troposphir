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
/*
"body":{"iisid":55, "_t":"updateItemiSetReq", "removals":[8, 3], "additions":[2], "ownerId":55}
*/
if (!defined("INCLUDE_SCRIPT")) return;
class updateItemiSetReq extends RequestResponse {
	public function work($json) {
		if (!isset($json['body']['iisid'])) return;
		if (!isset($json['body']['ownerId'])) return;

		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("SELECT userId, equippedItems FROM " . $this->config['table_user'] . " WHERE userId = " . $json['body']['ownerId'], null);

		$the_user = 0;
		$itemSet = array();
		for ($count = 0; $row = $statement->fetch(); $count++) {
			$the_user = (int)$row['userId'];
			$itemSet = array_map('intval', explode(";", (string)$row['equippedItems']));
		}

		$additions = $json['body']['additions'];
		$itemSet = array_merge($itemSet, $additions);

		$removals = $json['body']['removals'];
		$itemSet = array_diff($itemSet, $removals);

		//Convert the array back into a string delimited by ';'
		$updatedItemSet = "";
		foreach($itemSet as $item){
			if(!$updatedItemSet == "") $updatedItemSet .= ";";
			$updatedItemSet .= $item;
		}

		$db->query("UPDATE ".$this->config['table_user']." SET equippedItems='".$updatedItemSet."' WHERE `userId`=".$the_user);
	}
}
?>
