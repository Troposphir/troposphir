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

$path_parts = pathinfo($_SERVER['SCRIPT_NAME']);
$path_parts["dirname"] = stripslashes($path_parts['dirname']) . '/';

$config = array(
	'site'			=> $_SERVER['SERVER_NAME'] . $path_parts['dirname'],
	'logging'		=> "disabled",
	'request_log'	=> $_SERVER['DOCUMENT_ROOT'] . $path_parts['dirname'] . "request.log",
	//Database Credentials
	'driver'		=> 'mysql',
	'user'			=> 'troposphir',
	'password'		=> 'troposphir',
	'dbname'		=> 'troposphir',
	'host'			=> 'localhost',
	//Database Setup
	'table_map'		=> 'maps',
	'table_user'	=> 'users',
	'table_items'   => 'items',
	'table_scores' 	=> 'scores',
	'table_assets'  => 'asset',
	'table_comments'=> 'comments',
	//Directory Setup
	'dir_imgs'		=> 'image/maps',
	'dir_avatars'   => 'image/avatars',
	'dir_maps'		=> 'map',
	'dir_assets'	=> 'asset'
);
?>
