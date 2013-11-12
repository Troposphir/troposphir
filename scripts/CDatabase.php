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

class Database extends PDO {
	public function __construct($driver, $host, $dbname, $user, $password) {
		parent::__construct("$driver:host=$host;dbname=$dbname", $user, $password);
	}
	
	public function query($statement, $params) {
		//PDO doesn't prevent SQL Injections through the query() function
		if (!is_null($params)) {
			foreach ($params as $i =>$param) {
				$statement = str_replace("@".$i, $param, $statement);
			}
		}
		return parent::query($statement);
	}
	public function arrayToSQLGroup($array = array(), $decorators = array("(", ")", "`")) {
		$str = $decorators[0];
		foreach ($array as $i => $field) {
			$str = $str.$decorators[2].$field.$decorators[2];
			if ($i != count($array)-1) {
				$str = $str.", ";
			}
		}
		$str = $str.$decorators[1];
		return $str;
	}
}
?>