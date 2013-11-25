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
			$filename = basename($json['body']['asset']['filename']);
			if (!endsWith($filename, '.png')) return;
			
			$dir = '';
			if (startsWith($filename, 'MapImage_')) $dir = 'maps';
			else if (startsWith($filename, 'AvatarImage_')) $dir = 'avatars';
			
			$my_file = $this->config['dir_imgs'] . "/$dir/$filename";
			$handle = fopen($my_file, 'w') or die("");
			fwrite($handle, pack("H*", $json['body']['data']));
			fclose($handle);
			
			//Todo: Figure out a way to remove this ugly hack.
			$id = str_replace('.png', '', $filename); 
			$id = str_replace('MapImage_', '', $id); 
			$id = str_replace('AvatarImage_', '', $id); 
			$this->addBody("asset", array("id" => (integer)$id));
	
		} else if ($json["body"]["asset"]["_t"] == "asset") {
			$filename = basename($json['body']['asset']['filename']);
			//Todo: Create a template atmo file in its stead if some other file detected.
			if (!endsWith($filename, '.atmo')) return;

			$my_file = $this->config['dir_maps'] . "/$filename";
			$handle = fopen($my_file, 'w') or die("");
			fwrite($handle, pack("H*", $json['body']['data']));
			fclose($handle);
			
			$id = str_replace('.atmo', '', $id); 
			$this->addBody("asset", array("id" => (integer)$id));
		}
	}
}
?>