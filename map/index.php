<?php

//////////////////////////////////////////////////////////////////////////////
//  Copyright (C) 2013  Kevin Sonoda
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License 
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.    
//////////////////////////////////////////////////////////////////////////////

/*
:TO FINISH:
$_REQUEST['lid'] Needs To Be Checked With Map DataBase For Security Check
*/

//downloads map from current directory if it exists
if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) { 
   if (file_exists('./' . $_REQUEST['id'])) {
     echo file_get_contents('./' . $_REQUEST['id']);
   }
}

?>