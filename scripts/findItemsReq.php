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

//INCOMPLETE
//itypeId = Look Above
//damagePoints has to be in range of 0-5 GetConvertedDamage()
//blockFactorsPoints has to be in range of 1-6 GetConvertedBlockFactor()
//impulsePoints for Weapons (itypeId=5) is 0-5
//impulseBlockFactor for Weapons is 1-6	

class findItemsReq extends RequestResponse {
	public function work($json) {
		$itemList = array();
		
		$itemOne = array();
		$itemOne["id"]        = 10;
		$itemOne["name"]      = "ci_wooden_sword";
		$itemOne["itypeId"]   = 5;
		$itemOne["created"]   = 9000;
		$itemOne["isid"]      = 0;
		$itemOne["levels"]    = 10;
		$itemOneProps = array();
		$itemOneProps["shown"]               = (string)"true";
		$itemOneProps["vehicleCategory"]     = (string)"";
		$itemOneProps["is.free"]             = (string)"true";
		$itemOneProps["is.pro"]              = (string)"false";
		$itemOneProps["is.gift"]             = (string)"false";
		$itemOneProps["is.featured"]         = (string)"false";
		$itemOneProps["duration"]            = (string)"100000";
		$itemOneProps["description"]         = (string)"Wooden Sword";
		$itemOneProps["upgrade.description"] = "";
		$itemOneProps["is.rcextra"]          = (string)"false";
		$itemOneProps["quickEquipped"]       = (string)"true";
		$itemOneProps["gearType"]            = (string)"0";
		$itemOneProps["damagePoints"]        = (string)"0";
		$itemOneProps["damagePluses"]        = (string)"1";
		$itemOneProps["blockFactorPoints"]   = (string)"1";
		$itemOneProps["blockFactorPluses"]   = (string)"1";
		$itemOneProps["impulsePoints"]       = (string)"1";
		$itemOneProps["impulsePluses"]       = (string)"1";
		$itemOneProps["impulseBlockFactorPoints"] = (string)"1";
		$itemOneProps["impulseBlockFactorPluses"] = (string)"1";
		$itemOneProps["label"]                   = (string)"Label";
		$itemOneProps["genders"]                 = (string)"0,1";
		$itemOne["props"]     = $itemOneProps;
		$itemList[] = $itemOne;
	
		$itemTwo = array();
		$itemTwo["id"]      = 9;
		$itemTwo["name"]    = "ci_gender_male";
		$itemTwo["itypeId"] = 2;
		$itemTwo["isid"]    = 0;
		$itemTwo["created"] = 12412412;
		$itemTwo["levels"]  = 1;
		$itemList[] = $itemTwo;
		
		$this->addBody("fres", array("results" => $itemList));
	}
}
?>