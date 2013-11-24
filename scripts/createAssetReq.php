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

//Todo: Added security measures.
class createAssetReq extends RequestResponse {
	public function work($json) {
		//Check input
		if (!isset($json["body"]["data"])) return;
		if (!isset($json["body"]["asset"])) return;
		if (!isset($json["body"]["asset"]["_t"])) return;
		if (!is_numeric($json["body"]["asset"]["ownerId"])) return;
	
		if ($json["body"]["asset"]["_t"] == "imageAsset") {
			$my_file = $this->config['dir_imgs'] . "/avatars/AvatarImage_" . basename($json['body']['asset']['ownerId']) . ".png";
			$handle = fopen($my_file, 'w') or die("");
			fwrite($handle, pack("H*", $json['body']['data']));
			fclose($handle);
			
			$this->addBody("asset", array("id" => $json['body']['asset']['ownerId']));
	
		} else if ($json["body"]["asset"]["_t"] == "level") {
		
		}
	}
}
?>