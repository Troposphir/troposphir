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

//TODO:Support error handling for PHP 5.3+
class CJSON
{
	protected static $_messages = array(
		JSON_ERROR_NONE             => 'No error has occurred',
        JSON_ERROR_DEPTH            => 'The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH   => 'Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR        => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX           => 'Syntax error',
        JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded',
		JSON_ERROR_RECURSION        => 'One or more recursive references in the value to be encoded',
		JSON_ERROR_INF_OR_NAN       => 'One or more NAN or INF values in the value to be encoded',
		JSON_ERROR_UNSUPPORTED_TYPE => 'A value of a type that cannot be encoded was given'
	);
	
	public static function encode($value) {
		$result = json_encode($value);
		if ($result) {
			return $result;
		}
		die("JSON error: encode()");
	}
	
	public static function decode($json_string, $assoc = false) {
		$result = $json_decode($json_string, $assoc);
		
		if ($result) { 
			return $result;
		}
		die("JSON error: decode()");
	}
}
?>