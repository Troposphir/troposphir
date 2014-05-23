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
if (!defined("INCLUDE_SCRIPT")) return;
class updateLevelReq extends RequestResponse {
	public function work($json) {
		$fields = array('dataId', 'screenshotId', 'deleted', 'draft', 'description', 'version');
		$params = array();
		$cond = array();
		
		//Build Query
		foreach ($json['body'] as $propname => $propvalue) {
			if (in_array($propname, $fields)) {
				$cond[] = "`$propname` = ?";
				$params[] = $propvalue;
			}
		}
		$params[] = $json['body']['levelId'];
	
		//Update level data id
		$stmt = $this->getConnection()->prepare("UPDATE " . $this->config['table_map'] . 
			" SET " . implode(' , ', $cond) .
			" WHERE `id`=?");
		$stmt->execute($params);
	}
}
?>