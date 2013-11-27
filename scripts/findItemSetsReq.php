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

///armor = 20
//none = 0
//perk = 10
//INCOMPLETE	
if (!defined("INCLUDE_SCRIPT")) return;
class findItemSetsReq extends RequestResponse {
	public function work($json) {
		$fres = array();
		
		$itemSet = array();
		$itemSet["items"]     = array(10);
		$itemSet["id"]        = 142;
		$itemSet["name"]      = "Guest Item Set";
		$itemSet["created"]   = 12412412;
		
		$props = array();
		$props['shown']   = 'true';
		$props['is.free'] = 'true';
		$props['is.pro']  = 'false';
		$props['is.gift'] = 'false';
		$props['is.featured'] = 'false';
		$props['label']   = 'Label';
		$props['description'] = 'Default Items For Guests';
		$props['genders'] = '0,1';
		$props['setCategory'] = '20';
		$itemSet['props'] = $props;
	
		$this->addBody("fres", array("results" => array($itemSet)));
	}
}
?>