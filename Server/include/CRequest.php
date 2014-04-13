<?php 
/*==============================================================================
  Troposphir - Part of the Tropopshir Project
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
require("./include/CDatabase.php");
class RequestResponse {
	private $body_;
	private $header_;
	protected $config;
	protected $db;
	protected $errorCodes_ = array(
			'ACCOUNT_NOT_FOUND' => 0xcc,
			'ALREADY' => 6,
			'APP_NOT_FOUND' => 200,
			'AUTH_BANNED' => 0x68,
			'AUTH_FAILED' => 0x65,
			'AUTH_NOT_PERMITTED' => 0x66,
			'AUTH_REQUIRED' => 100,
			'AUTH_UNVERIFIED' => 0x67,
			'DUPLICATE_LEVEL' => 0x1f5,
			'DUPLICATE_MESSAGE' => 0x1f6,
			'DUPLICATE_MP_SESSION' => 0x1f8,
			'DUPLICATE_OBJECT' => 500,
			'DUPLICATE_TAG_ENTRY' => 0x1f7,
			'EMAIL_ALREADY_USED' => 0x259,
			'EMAIL_INVALID' => 600,
			'FOLDER_NOT_FOUND' => 0xcb,
			'INTERNAL' => 1,
			'INVALID' => 8,
			'INVALID_OPERATION_LEVEL' => 0x130,
			'INVALID_OPERATION_MESSAGE' => 0x12f,
			'INVALID_OPERATION_PURCHASE' => 0x131,
			'INVALID_OPERATION_SESSION' => 0x12e,
			'INVALID_OPERATION_STRATOS' => 0x12d,
			'INVALID_OPERATION_USER' => 300,
			'ITEM_NOT_FOUND' => 0xcd,
			'ITEM_SET_NOT_FOUND' => 0xd1,
			'LEVEL_CONFIG_NOT_FOUND' => 210,
			'LEVEL_NOT_FOUND' => 0xca,
			'LEVEL_SESSION_NOT_FOUND' => 0xce,
			'MALFORMED' => 4,
			'MESSAGE_NOT_FOUND' => 0xd0,
			'MP_SESSION_NOT_FOUND' => 0xcf,
			'NO_HANDLER' => 3,
			'NOCODEC' => 5,
			'NONE' => 0,
			'NOT_FOUND' => 7,
			'NULL'=> 2,
			'PASSWORD_INVALID' => 0x25d,
			'THING_NOT_FOUND' => 400,
			'TOO_MANY' => 9,
			'USER_ALREADY_USED' => 0x25c,
			'USER_EXTERNAL_FALSE'=> 0x260,
			'USER_HAS_REDCARPET' => 0x261,
			'USER_INVALID' => 0x25a,
			'USER_INVALID_CHARS' => 0x25e,
			'USER_NOT_EXTERNAL' => 0x25f,
			'USER_NOT_FOUND' => 0xc9,
			'USER_PROFANITY' => 0x25b
	);
	public function __construct($config) {
		$this->db = null;
		$this->config = $config;
		$this->body_   = array(); 
		$this->header_ = array("_t" => "mfheader");
	}
	public function work($json) {}
	public function send() {
		$content = json_encode(array(
			"header" => $this->header_,
			"_t" => "mfmessage",
			"body" => $this->body_
		));	//The current server doesn't support JSON_NUMERIC_CHECK.

		//Todo: Find a more elegant way to turn "body":[] into "body":{}
		if (empty($this->body_)) {
			$content = str_replace('[]', '{}', $content) ;
		}	
		echo $content;
		
		$db = null;
	}
	public function addBody($key, $value) {
		$this->body_[$key] = $value;
	}
	public function addHeader($key, $value) {
		$this->header_[$key] = $value;
	}
	public function error($code) {
		$this->addBody("_t", "mferror");
		$this->addBody("props", array(
			"errcode" => (string)$this->errorCodes_[$code]
		));
	}
	public function log($text) {
		if ($this->config["logging"] == "enabled") {
			$file = fopen($this->config["request_log"], "a"); 
			if ($file) {
				fwrite($file, "[".date("Y-m-d H:i:s")."] ".get_class($this).": ".$text."\n");
				fclose($file);
			}
		}
	}
	
	/*
		Purpose: Lazy initiate a connection to the database.
	*/
	public function getConnection() {
		if ($this->db == null) {
			$this->db = new Database($this->config['driver'], $this->config['host'], $this->config['dbname'], $this->config['user'], $this->config['password']);	
		}
		return $this->db;
	}
	
	/* 
		Purpose: Check if the user permission is allowed for certain operations.
		Compares it via an IP check.
	*/
	public function verifyUserById($id) {
		//Get user ip address from data table
		$stmt = $this->getConnection()->prepare("SELECT ipAddress 
			FROM " . $this->config['table_user']  . 
			" WHERE `userId`=:userId");
		$stmt->bindValue(':userId', $id, PDO::PARAM_INT);
		$stmt->execute();
		
		$row = $stmt->fetch();
		if ($row['ipAddress'] != $_SERVER['REMOTE_ADDR']) {
			return false;
		}
		else {
			return true;
		}
	}
}