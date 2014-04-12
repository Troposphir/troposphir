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
// Get the launcher's hash list path relative to server root.
if (!defined("INCLUDE_SCRIPT")) return;
class findItemInstanceSetsReq extends RequestResponse {
	public function work($json) {
		if (!isset($json['header']['origin'])
			&& $json['header']['origin'] != "troposphir-launcher") return;
	
		$this->addBody("endpoint", "/clientHashList.txt");
	}
}
?>