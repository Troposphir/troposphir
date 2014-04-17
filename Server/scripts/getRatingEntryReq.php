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

/*
	REQUEST:
    "body":{
			"_t":"getRatingEntryReq", 
			"ratingKey":{
						 "_t":"ratingKey", 
						 "thingKey":{
									 "id":6, 
									 "_t":"thingKey", 
									 "type":3101
									}, 
						 "raterId":2
						}
			}
    
    RESPONSE:
        "body":{"rating":{"rating":100, "count":20}}
*/
/*
	CALLED WHEN:
		A level is being loaded to play.
	NOTES:
		-Returning anything will raise a flag in the client that disables a (star/difficulty) rating dialog to show up.
*/

if (!defined("INCLUDE_SCRIPT")) return;
class getRatingEntryReq extends RequestResponse {
	public function work($json) {
		//Constants
		$type_star = 3101;
        $type_diff = 3102;
       
		//Input validation
		if (!isset($json['body']['ratingKey'])) return;
        if (!isset($json['body']['ratingKey']['raterId'])) return;
        if (!isset($json['body']['ratingKey']['thingKey']['id'])) return;

		//Code
        $field = "";
		if($json['body']['ratingKey']['thingKey']['type'] == $type_star) {
			$field = "rating";
        } else {
            $field = "difficulty";
        }        
      
		$db = $this->getConnection();
		$statement = $db->prepare("SELECT `$field` 
			FROM `" . $this->config['table_playRecord'] . "` 
			WHERE `levelId`=:levelId 
			AND `userId`=:userId");
		$statement->bindParam(':levelId', $json['body']['ratingKey']['thingKey']['id'], PDO::PARAM_INT);
		$statement->bindParam(':userId', $json['body']['ratingKey']['raterId'], PDO::PARAM_INT);
		$statement->execute();
		
		$result = $statement->fetch();
		if ($result == false) {
			//The following commented code will suppress the "Please Rate Level" dialog...
			//$this->error("NOT_FOUND"); 
		} else {
			$rating = array();
			$rating["rating"]   = (integer) $result["$field"];
			
			$this->addBody("ratingEntry", $rating);
		}
	}
}
?>
