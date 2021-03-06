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
class getLoadingTipReq extends RequestResponse {
	public $tips = array(
		"This server is hosted by OneMoreBlock. Come visit us at http://onemoreblock.com !",
		"You can check out the source code of this server at https://github.com/MusicalIdiot/Troposphir !",
		"Currently, it isn't possible to login or register for an account. Sorry, we're working on it!"
	);
	public function work($json) {
		$this->addBody("categoryName", "");
		$this->addBody("tip", $this->tips[rand(0, count($this->tips)-1)]);
	}
}
?>