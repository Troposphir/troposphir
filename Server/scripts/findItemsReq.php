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

//INCOMPLETE
//itypeId = Look Above
//damagePoints has to be in range of 0-5 GetConvertedDamage()
//blockFactorsPoints has to be in range of 1-6 GetConvertedBlockFactor()
//impulsePoints for Weapons (itypeId=5) is 0-5
//impulseBlockFactor for Weapons is 1-6	
if (!defined("INCLUDE_SCRIPT")) return;
class findItemsReq extends RequestResponse {
	public function work($json) {
	
		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("SELECT * FROM " . $this->config['table_items'], null);
		
		$itemList = array();
		for ($count = 0; $row = $statement->fetch(); $count++) {
			$item = array();
			$item['name']     = (string)$row['name'];
			$item['id']       = (integer)$row['id'];
			$item['itypeId']  = (integer)$row['itypeId'];
			$item['created']  = 0;
			$item['isid']     = 0;
			$item['levels']   = 0;
			
			$itemProps = array();
			$itemProps['shown']               = ($row['shown'] == 1) ? 'true' : 'false';
			$itemProps['vehicleCategory']     = (string)$row['vehicleCategory'];
			$itemProps['is.free']             = ($row['isFree'] == 1) ? 'true' : 'false';
			$itemProps['is.pro']              = ($row['isPro'] == 1) ? 'true' : 'false';
			$itemProps['is.gift']             = ($row['isGift'] == 1) ? 'true' : 'false';
			$itemProps['is.featured']         = ($row['isFeatured'] == 1) ? 'true' : 'false';
			$itemProps['duration']            = (string)$row['duration'];
			$itemProps['description']         = (string)$row['description'];
			//$itemProps['upgrade.description'] = (string)$row['upgradeDescription'];
			$itemProps['is.rcextra']          = ($row['isRCExtra'] == 1) ? 'true' : 'false';
			$itemProps['quickEquipped']       = ($row['quickEquipped'] == 1) ? 'true' : 'false';
			$itemProps['gearType']            = (string)$row['gearType'];
			//$itemProps['damagePoints']        = (string)$row['damagePoints'];
			//$itemProps['damagePluses']        = (string)$row['damagePluses'];
			//$itemProps['blockFactorPoints']   = (string)$row['blockFactorPoints'];
			//$itemProps['blockFactorPluses']   = (string)$row['blockFactorPluses'];
			//$itemProps['impulsePoints']       = (string)$row['impulsePoints'];
			//$itemProps['impulsePluses']       = (string)$row['impulsePluses'];
			//$itemProps['impulseBlockFactorPoints'] = (string)$row['impulseBlockFactorPoints'];
			//$itemProps['impulseBlockFactorPluses'] = (string)$row['impulseBlockFactorPluses'];
			//$itemProps['label']               = (string)$row['label'];
			$itemProps['genders']             = (string)$row['genders'];
			
			$item['props'] = $itemProps;
			
			$itemList[] = $item;
		}
		
		$this->addBody("fres", array("results" => $itemList));
	}
}
?>
