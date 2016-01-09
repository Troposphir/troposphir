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
//Purchase items
if (!defined("INCLUDE_SCRIPT")) return;
class purchaseReq extends RequestResponse {
	public function work($json) {
		if (!isset($json['body']['oids'])) return;
		if (!isset($json['header']['auth'])) return; //Using this to get the user since we don't have a userId

    //Convert oid to regular item id
    $oitemsToPurchase = $json['body']['oids'];
    $oitemsAsQuery = "";
    foreach($oitemsToPurchase as $oI){
      if($oitemsAsQuery != "")
        $oitemsAsQuery .= " OR `oid`=";
      $oitemsAsQuery .= $oI;
    }

		$db = $this->getConnection();
    $stmt = $db->query("SELECT id, price FROM " . $this->config['table_items'] . " WHERE `oid`=".$oitemsAsQuery, null);

    $itemsToPurchase = array();
    $totalToDeduct = 0;
    for($count = 0; $row = $stmt->fetch(); $count++){
      $itemsToPurchase[] = $row['id'];
      $totalToDeduct = $totalToDeduct + (int)$row['price'];
    }

    if(array_count_values($itemsToPurchase) == 0) return;

    // GET CURRENT INVENTORY AND BALANCE
    $statement = $db->query("SELECT userId, ownedItems, amt FROM " . $this->config['table_user'] . " WHERE token = " . $json['header']['auth'], null);
		$myInventory = array();
    $myMoney = 0;
    $userId = 0;
		for ($count = 0; $row = $statement->fetch(); $count++) {
			$myInventory = array_map('intval', explode(";", (string)$row['ownedItems']));
      $myMoney = $row['amt'];
      $userId = $row['userId'];
		}

    if($myMoney >= $totalToDeduct){ //Check if user has enough funds
      $myInventory = array_merge($myInventory, $itemsToPurchase); //Add existing items with new items

  		//Convert the array back into a string delimited by ';'
  		$updatedInventory = "";
  		foreach($myInventory as $item){
  			if(!$updatedInventory == "") $updatedInventory .= ";";
  			$updatedInventory .= $item;
  		}

      //UPDATE THE INVENTORY
  		$db->query("UPDATE ".$this->config['table_user']." SET ownedItems='".$updatedInventory."' WHERE `userId`=".$userId);
      //UPDATE funds
      $newBalance = $myMoney - $totalToDeduct;
      $db->query("UPDATE ".$this->config['table_user']." SET amt=".$newBalance." WHERE `userId`=".$userId);
    }
	}
}
?>
