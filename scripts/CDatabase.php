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

class Database {
	private $connection;
	public function __construct() {
		//using MySQLi, see docs at http://www.php.net/manual/en/class.mysqli.php
		$this->connection = new mysqli($config["host"], $config["user"], $config["password"], $config["dbname"]);
	}
	public function query($queryString, $params) {
		if (!is_null($params)) {
			foreach ($params as $i =>$param) {
				$queryString = str_replace("@".$i, $param, $queryString);
			}
		}
		$this->connection->query($queryString);
	}
	public function close() {
		$this->connection->close();
	}
}
?>