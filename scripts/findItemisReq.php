<?php
/*==============================================================================
  Troposphir - Part of the Troposphir Project
  Copyright (C) 2013  Kevin Sonoda, Leonardo Giovanni Scur

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

//GET PLAYER OWNED ITEMS
//note: itemId relates to ItemCategory values
/*
 ITEM CATEGORY:
basicProp = 5,
block = 0,
connector = 2,
fallingPlatform = 4,
flag = 10,
floor = 1,
hazard = 7,
interactive = 6,
movingPlatform = 3,
music = 11,
none = -1,
powerUp = 9,
racingProps = 14,
skybox = 12,
treasure = 8,
ugc = 13

SETITEMSLIST();
itypeid =
{
charCategoryMapping[1L] = CharCategory.none = 0;
charCategoryMapping[2L] = CharCategory.gender = 10;
charCategoryMapping[12L] = CharCategory.skin = 15;
charCategoryMapping[6L] = CharCategory.hair = 20;
charCategoryMapping[7L] = CharCategory.head = 30;
charCategoryMapping[0x18L] = CharCategory.glasses = 0x23;
charCategoryMapping[0x11L] = CharCategory.eyebrows = 40;
charCategoryMapping[0x10L] = CharCategory.eyes = 50;
charCategoryMapping[0x13L] = CharCategory.nose = 60;
charCategoryMapping[0x12L] = CharCategory.mouth = 70;
charCategoryMapping[9L] = CharCategory.upperBody = 80;
charCategoryMapping[10L] = CharCategory.hands = 90;
charCategoryMapping[13L] = CharCategory.back = 100;
charCategoryMapping[8L] = CharCategory.lowerBody = 110;
charCategoryMapping[15L] = CharCategory.legs = 120;
charCategoryMapping[5L] = CharCategory.meleeWeapon = 130;
charCategoryMapping[0x17L] = CharCategory.rangedWeapon = 140;
charCategoryMapping[0x19L] = CharCategory.bombWeapon = 160;
charCategoryMapping[0x34L] = CharCategory.firearmWeapon = 180;
charCategoryMapping[0x1bL] = CharCategory.perk = 170;
charCategoryMapping[0x16L] = CharCategory.shield = 150;
charCategoryMapping[0x1aL] = CharCategory.animation = 190;
charCategoryMapping[0x21L] = CharCategory.extra = 200;
}
*/
//id:1 = ci_gender_male
//id:2 = ci_gender_female

//INCOMPLETE
class findItemisReq extends RequestResponse {
	public function work($json) {
		if (!isset($json["body"]["ownerId"])) return;
		
		$itemList = array();				
		
		$itemOne = array();
		$itemOne["itemId"]  = 10;
		$itemOne["id"]      = 132;
		$itemOne["created"] = 9000;
		$itemList[] = $itemOne;
		
		$itemTwo = array();
		$itemTwo["itemId"]  = 9;
		$itemTwo["id"]      = 131;
		$itemTwo["created"] = 9000;
		$itemList[] = $itemTwo;
	
		$this->addBody("fres", array(results => array($itemList)));
		}
	}
}
?>