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
//{"header":{"_t":"mfheader"},"_t":"mfmessage","body":{"fres":{"results":[{"name":"cs_knight_black_b","id":11,"items":[18],"created":12412412,"props":{"shown":"true","is.free":"false","is.pro":"false","is.gift":"false","is.featured":"false","label":"label","description":"desc","genders":"1,2","setCategory":0}}]}}}
//{"header":{"_t":"mfheader"},"_t":"mfmessage","body":{"fres":{"results":[{"items":[18],"id":8,"name":"cs_knight_black_b","created":12412412,"props":{"shown":"true","is.free":"false","is.pro":"false","is.gift":"false","is.featured":"false","label":"Label","description":"Default Items For Guests","genders":"1,2","setCategory":"0"}}]}}}
///armor = 20
//none = 0
//perk = 10
//INCOMPLETE
if (!defined("INCLUDE_SCRIPT")) return;
class findItemSetsReq extends RequestResponse {
	public function work($json) {
		$fres = array();

		$db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);
		$statement = $db->query("SELECT * FROM itemSets", null);

		$itemSetsList = array();
		for ($count = 0; $row = $statement->fetch(); $count++) {
			$item = array();
    	$item['items']    = array_map('intval', explode(";", (string)$row['items']));
			$item['id']       = (integer)$row['id'];
			$item['name']     = (string)$row['name'];
			$item['created']  = 12412412;

			$props = array();
			$props['shown']   = ($row['shown'] == 1) ? 'true' : 'false';
			$props['is.free'] = ($row['isfree'] == 1) ? 'true' : 'false';
			$props['is.pro']  = ($row['ispro'] == 1) ? 'true' : 'false';
			$props['is.gift'] = ($row['isgift'] == 1) ? 'true' : 'false';
			$props['is.featured'] = ($row['isfeatured'] == 1) ? 'true' : 'false';
			$props['label']   = (string)$row['label'];
			$props['description'] = (string)$row['description'];
			$props['genders'] = (string)$row['genders'];
			$props['setCategory'] = (string)$row['setCategory'];
			$item['props'] = $props;

			$itemSetsList[] = $item;
		}

		// $itemSet = array();
		// $theitems = array();
		// $theitems[] = 18;
		// $itemSet["items"]     = $theitems;
		// $itemSet["id"]        = 8;
		// $itemSet["name"]      = "cs_knight_black_b";
		// $itemSet["created"]   = 12412412;
		//
		// $props = array();
		// $props['shown']   = 'true';
		// $props['is.free'] = 'false';
		// $props['is.pro']  = 'false';
		// $props['is.gift'] = 'false';
		// $props['is.featured'] = 'false';
		// $props['label']   = 'Label';
		// $props['description'] = 'Default Items For Guests';
		// $props['genders'] = '1,2';
		// $props['setCategory'] = '0';
		// $itemSet['props'] = $props;

		$this->addBody("fres", array("results" => $itemSetsList));
	}
}
?>
