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

//$_REQUEST['lid'] Needs To Be Checked With DataBase For Security Check
//width=, height=

//Get Level Images
if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id']))
{ 
   if (file_exists('./' . $_REQUEST['id']))
   {
     echo file_get_contents('./' . $_REQUEST['id']);
   }
}

//Get Asset Icons
else if (isset($_REQUEST['item']))
{ 
   if (file_exists('./asseticons/'  . $_REQUEST['id'] . '.png'))
   {
     echo file_get_contents('./asseticons/'  . $_REQUEST['id'] . '.png');
   }
}

?>