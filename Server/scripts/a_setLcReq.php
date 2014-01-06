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
if (!defined("INCLUDE_SCRIPT")) return;
class a_setLcReq extends RequestResponse {
	public function work($json) {
		if (!isset($json['body']['lc']['id'])) return;
		if (!isset($json['body']['lc']['props'])) return;
		
		//Build query
		$fields = array('gms', 'gmmp1', 'gmmp2', 'gff',
			'gsv', 'gbs', 'gde', 'gdb', 'gmc', 'gmcp1', 'gmcp2', 'gctf', 'gab', 'gra',
			'gco', 'gtc', 'gmcdt', 'gmcff', 'ast', 'aal', 'ghosts', 'ipad', 'dcap', 'dmic',
			'denc', 'dpuc', 'dcoc', 'dtrc', 'damc', 'dphc', 'ddoc', 'dkec', 'dgcc', 'dmvc',
			'dsbc', 'dhzc', 'dmuc', 'dtmi', 'ddtm', 'dttm', 'dedc', 'dtsc', 'dopc', 'dpoc' 
		);
		$params = array();
		$cond = array();
		foreach ($json['body']['lc']['props'] as $propname => $propvalue) {
			if (in_array($propname, $fields)) {
				$cond[] = "`$propname` = ?";
				$params[] = $propvalue;
			}
			else if ($propname == 'is.lotd') {
				$cond[] = "`$isLOTD` = ?";
				$params[] = $propvalue;
			}
			else if ($propname == 'xp.reward') {
				$cond[] = "`xpReward` = ?";
				$params[] = $propvalue;
			}
		}
		$params[] = $json['body']['lc']['id'];
		
		//Set level config
		$stmt = $this->getConnection()->prepare("UPDATE " . $this->config['table_map'] . 
			" SET " . implode(' , ', $cond) .
			" WHERE `id`=?");
		$stmt->execute($params);
	}
}
?>