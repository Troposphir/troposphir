<?php
/*==============================================================================
  Troposphir - Part of the Troposhir Project
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

//TODO: Check level id against database?
//download map from current directory if it exists

error_reporting(0);
if (isset($_REQUEST['id'])) { 
    $id = filter_var(basename($_REQUEST['id']), FILTER_VALIDATE_INT);
	if (file_exists("./$id")) {
		echo file_get_contents("./$id");
	}
}

?>