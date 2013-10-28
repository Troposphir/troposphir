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

//REFERENCE: http://www.wmtips.com/php/tips-optimizing-php-code.htm

error_reporting(0);
require("./include/CDatabase.php");
require("./include/CJSON.php");

//=============CONFIGURATIONS============
//prevent caching
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 01 Jan 1996 00:00:00 GMT');
//JSON standard MIME header
header('Content-type: application/json');
 
$path_parts = pathinfo($_SERVER['SCRIPT_NAME']);
$GLOBALS = array
 (
  'path_images' => '/image',
  'path_maps'   => '/map',
  'path_assets' => '/asset',
  'host'        => $_SERVER['SERVER_NAME'] . $path_parts['dirname'],
  'dbusername'  => '[REDACTED]',
  'dbpassword'  => '[REDACTED]',
  'dbDatabase' =>  '[REDACTED]',
  'dbServer' => '[REDACTED]',
  'dbMapsTable' => 'maps',
  'dbUsersTable' => 'users'
);  



   //$table = QUERY_DB("UPDATE `" . $GLOBALS['dbUsersTable'] . '` SET token=710081493');
   //var_dump($table);
    
   //$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbUsersTable'] . '`');
   //var_dump($table);
     
//================README================
// * VERSION: 1.1.0
// * NOTES:   
//     -To Configure Game Context (level browser level/tutorial level/important urls) 
//       Go To getGameContextReq() function 
//     -MySQL Query Syntax May Have to Be Slightly Adjusted At Function QUERY_DB()
//     -Any fields with the tag [MODIFIED BY DLL] indicates that field doesn't matter and is given a set value by the modified game dll.
//
// * Required Files and Folders: 
//     - "index.php"   
//     - "/map"
//     - "/map/index.php"
//     - "/image"
//     - "/image/index.php"
//
// * MySQL Requirements:
//     1) Add A Database As Given By ['dbDatabase']
//     2) Configure $GLOBALS For username, password, and database name 
//     3) Uncomment Following On First Run. Keep Commented Afterwards.
//         QUERY_DB("CREATE TABLE IF NOT EXISTS `" . $GLOBALS['dbMapsTable'] . "`(`id` int,`name` text,`description` text,`author` text,`dc` int,`rating` int,`difficulty` int,`ownerId` int,`downloads` int,`dataId` int,`screenshotId` int,`version` int,`draft` tinytext,`nextLevelId` int,`editable` tinytext,`deleted` tinytext,`gcid` int,`editMode` int,`xis.lotd` int,`is.lotd` tinytext,`xp.reward` int,`xp.level` tinytext, `xgms` int,`gms` int,`gmm` int,`gff` int,`gsv` int,`gbs` int,`gde` int,`gdb` int,`gctf` int,`gab` int,`gra` int,`gco` int,`gtc` int,`gmmp1` int,`gmmp2` int,`gmcp1` int,`gmcp2` int,`gmcdt` int,`gmcff` int,`ast` int,`aal` int,`ghosts` int,`ipad` int,`dcap` int,`dmic` int)");
//         QUERY_DB("CREATE TABLE IF NOT EXISTS `" . $GLOBALS['dbUsersTable'] . "`(`userId` int, `username` text, `password` text, `token` int, `created` int, `avaid` int, `sessionToken` int, `isDev` text, `is.lotdMaster` text, `isXPMaster` text, `development` text, `external` text, `flags` int, `locale` text, `verified` text, `xpp` int, `isClubMember` text, `paidBy` text, `sapo` text, `vehicleInstanceSetId` text, `activableItemShorcuts` text, `saInstalled` text, `signature` int)");
//NOT THIS QUERY_DB("INSERT INTO " . $GLOBALS['dbMapsTable'] . "(id,name,description, author, dc, rating, difficulty, ownerId, downloads, dataId, screenshotId, version, draft, nextLevelId, editable, deleted, gcid, editMode, `xis.lotd`, `is.lotd`, `xp.Reward`, `xp.level`, xgms, gms, gmm, gff, gsv, gbs, gde, gdb, gctf, gab, gra, gco, gtc, gmmp1, gmmp2, gmcp1, gmcp2, gmcdt, gmcff, ast, aal, ghosts, ipad, dcap, dmic ) VALUES('21689','1-1 Cosa Plains','Tutorial Level', 'okaysamurai', 0, 5, 1, 0, 0, 66048, 4152, 2, 'false', 0, 'false', 'false',0, 0, 0, 'false', 0, 'false', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)"); 
//    QUERY_DB("UPDATE maps SET ownerId='0' WHERE `ownerId`='1'");
//      QUERY_DB("ALTER TABLE `maps` ADD `xp.level` TINYTEXT");
/*
 QUERY_DB("ALTER TABLE `users` denc");
 QUERY_DB("ALTER TABLE `users` `dpuc`");
 QUERY_DB("ALTER TABLE `users` `dcoc`");
 QUERY_DB("ALTER TABLE `users` `dtrc`");
 QUERY_DB("ALTER TABLE `users` `damc`");
 QUERY_DB("ALTER TABLE `users` `dphc`");
 QUERY_DB("ALTER TABLE `users` `ddoc`");
 QUERY_DB("ALTER TABLE `users` `dkec`");
 QUERY_DB("ALTER TABLE `users` `dgcc`");
 QUERY_DB("ALTER TABLE `users` `dmvc`");
 QUERY_DB("ALTER TABLE `users` `dsbc`");
 QUERY_DB("ALTER TABLE `users` `dhzc`");
 QUERY_DB("ALTER TABLE `users` `dmuc`");
 QUERY_DB("ALTER TABLE `users` `dtmi`");
 QUERY_DB("ALTER TABLE `users` `ddtm`");
 QUERY_DB("ALTER TABLE `users` `dttm`");
 QUERY_DB("ALTER TABLE `users` `dedc`");
 QUERY_DB("ALTER TABLE `users` `dtsc`");
 QUERY_DB("ALTER TABLE `users` `dopc`");
 QUERY_DB("ALTER TABLE `users` `dpoc`");
*/


//QUERY_DB("ALTER TABLE `users` MODIFY saInstalled TINYTEXT");


//=================CODE==================
if (isset($_REQUEST['json']))
{
	//decode json query
	$json = get_magic_quotes_gpc() ? json_decode(stripslashes($_REQUEST['json']), true) : json_decode($_REQUEST['json'], true);
 
	//check that json query is valid
	if (!isset($json2['header'])) return;
	if (!isset($json2['body'])) return;
	if (!isset($json2['body']['_t'])) return;
	// if (isset($json->header['enc'])) {} //FIX: adapt for encryption
	
	$requests = new Requests(); //Smashed epic switch
	$requests->$json["body"]["_t"]($json);
	unset($requests);
	
	return;
}
else
{	

  PRINT_ERROR_MSG('NULL');
  return;
}
 
 

//SERVER FUNCTIONS
function QUERY_DB($query)
{
 if($query != null)
 {
   //ADJUST MYSQL SYNTAX
   $begpos = strpos($query, "AND ct:[");
   if ($begpos !== false) 
   {
	$endpos = strpos($query, ']', $begpos) + 1; 
	$query = substr($query, 0, $begpos) . substr($query, $endpos, strlen($query));
   }  

   $query = str_replace(':', '=', $query);
   $query = str_replace('xis.lotd', "'xis.lotd'", $query);
   $query = str_replace('is.lotd', "`is.lotd`", $query);
   $query = str_replace('xp.reward', "'xp.reward'", $query);
   $query = str_replace('xp.level', "'xp.level'", $query);


   //ADJUST MYSQL SYNTAX
   $array = array(); 
   $link = mysql_connect($GLOBALS['dbServer'],$GLOBALS['dbusername'],$GLOBALS['dbpassword']);
   if ($link == NULL) {echo 'There were problems connecting to the database.'; return NULL;}


   $success = mysql_select_db($GLOBALS['dbDatabase'], $link); 
   if ($success == false) {echo "Database could not be found."; return NULL;}
   
   $result = mysql_query(stripslashes(mysql_real_escape_string($query)));
   if (!$result) {echo "Invalid Query."; return NULL;}
   

   while($row = mysql_fetch_assoc($result))
   {
	 array_push($array, $row);
   }
   mysql_free_result($result);
   mysql_close($link);

   return $array;

  
	  
 }
}

function PRINT_ERROR_MSG($error)
{
 $MFPErrorCode = array(
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


  echo '{"header":{"_t":"mfheader"},
	   "body":{
		   "_t":"mferror",
		   "props":{"errcode":' . hexdec($MFPErrorCode[$error]) . '}
		  }
	   }';
}
class Requests {
	static function getGameContextReq()
	{

		echo '{"header":{"_t":"mfheader"},
				"_t":"mfmessage",
				"body":{
				"assetEndpoint":"' . $GLOBALS['host'] . $GLOBALS['path_assets'] . '",
						"mailboxEndpoint":"",
						"friendsUrl":"",
						"masterServerHost":"' . $GLOBALS['host'] . '",
								"masterServerPort":80,
								"natFacilitatorPort":0,
								"redCarpetTutorialLevelId":21689,
								"charCustLevelId":21689,
								"levelBrowserLevelId":21689,
								"tutorialUserId":0,
								"unityBundleUrl":"",
								"staticImagesUrl":"' . $GLOBALS['host'] . $GLOBALS['path_images'] . '",
										"staticMapUrl":"' . $GLOBALS['host'] . $GLOBALS['path_maps'] . '",
												"staticAvatarUrl":"' . $GLOBALS['host'] . $GLOBALS['path_images'] . '/avatars"
	}
	}';
	}

	static function getLoadingTipReq()
	{
		$tips = array(
				"This server is hosted by OneMoreBlock. Come visit us at www.OneMoreBlock.com!",
				"This custom Atmosphir server was put together by OneMoreBlock user Nin. Thanks Nin!",
				"Currently, it isn't possible to log in or register for an account. Sorry, we're working on it!"
	 );

		echo CJSON::encode(
				array(
						"header" => array("_t" => "mfheader"),
						"_t"     => "mfmessage",
						"body"   => array(
								"categoryName" => "",
								"tip" => $tips[rand(0, count($tips)-1)]
						)
				)
		);
	}

	static function pingReq()
	{
		$ping = mktime(date("Y:m:d"),date("H:m:s")) ;
		echo CJSON::encode(
				array(
						"header" => array("_t" => "mfheader"),
						"_t"     => "mfmessage",
						"body"   => array(
								"timestamp" => $ping
						)
				)
		);
	}

	static function postEventReq($json)
	{
		if(!isset($json['body']['event'])) return;
		if(!isset($json['body']['event']['type'])) return;

		switch ($json['body']['event']['type'])
		{
			case 'firstInstallAndLogin':
				break;
			case 'guestPlay': //Guest Played a Level
				//TODO: Check for Play Count Boosting With TimeStamp -> $json['body']['event']['timestamp']
				//Why Is This Have UID? -> $json['body']['event']['uid']
				QUERY_DB("UPDATE " . $GLOBALS['dbMapsTable'] . " SET dc=dc+1 WHERE `id`='" . $json['body']['event']['v3'] . "'");
				echo '{"header":{"_t":"mfheader"}, "_t":"mfmessage", "body":{} }';
				break;
		}
	}

	static function getProfanityListReq()
	{
		echo CJSON::encode(
				array(
						"header" => array("_t" => "mfheader"),
						"_t"     => "mfmessage",
						"body"   => array(
								"fres" => array(
										"results" => array(array(
												"pattern" =>	"[fuck][fuark][fuk]"
										))
								)
						)
				)
	 );
	}

	static function getLevelByIdReq($json)
	{
		//SafeCheck
		if(!isset($json['body']['levelId'])) return;

		//Read Map Info
		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbMapsTable'] . "` WHERE `id`=" . $json['body']['levelId']);
		if (empty($table)) {
			PRINT_ERROR_MSG("NOT_FOUND");  return;
		}

		echo '{"header":{"_t":"mfheader"},
				"_t":"mfmessage",
				"body":{
				"level":
				{
				"id":' . $table[0]['id'] . ',
						"name":"' . $table[0]['name'] . '",
								"description":"' . $table[0]['description'] . '",
										"author":"' . $table[0]['author'] . '",
												"ownerId":' . $table[0]['ownerId'] . ',
														"downloads":' . $table[0]['downloads'] . ',
																"dataId":' . $table[0]['dataId'] . ',
																		"screenshotId":' . $table[0]['screenshotId'] . ',
																				"draft":' . $table[0]['draft'] . ',
																						"version":' . $table[0]['version'] . ',
																								"nextLevelId":' . $table[0]['nextLevelId'] . ',
																										"editable":' . $table[0]['editable'] . ',
																												"props":{
																												"gcid":"' . $table[0]['gcid'] . '",
																														"editMode":"' . $table[0]['gcid'] .'"
	}
	}
	}
	}';
	}

	static function getLevelsByAuthorReq($json)
	{
		if (!isset($json['body']['authorId'])) return;
		if (!isset($json['body']['freq']['start'])) return;
		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbMapsTable'] . '` WHERE `ownerId`="' . $json['body']['authorId'] . '"');

		echo '{"header":{"_t":"mfheader"},
				"_t":"mfmessage",
				"body":{
				"fres":{
				"total":' . count($table) . ',
						"results":[';
		for($i=$json['body']['freq']['start']; $i < count($table); $i++)
		{
			echo '{
					"id":' . $table[$i]['id'] . ',
							"name":"' . $table[$i]['name'] . '",
									"description":"' . $table[$i]['description'] . '",
											"ownerId":' . $table[$i]['ownerId'] . ',
													"downloads":' . $table[$i]['downloads'] . ',
															"version":' . $table[$i]['version'] . ',
																	"draft":' . $table[$i]['draft'] . ',
																			"author":"' . $table[$i]['author'] . '",
																					"editable":' . $table[$i]['editable'] . ',
																							"dataId":' . $table[$i]['dataId'] . ',
																									"screenshotId":' . $table[$i]['screenshotId'] . ',
																											"rating":"' . $table[$i]['rating'] . '",
																													"difficulty":"' . $table[$i]['difficulty'] . '",
																															"props":{"gcid":"' . $table[$i]['gcid'] . '", "editMode":"' . $table[$i]['editMode'] . '"},
																																	"lc":{
																																	"props":{
		}
		}
		}';
		 if (($i+1) < (count($table))) {
		 	echo ',';
		 }

		} //end of for loop
		echo ']
	}
	}
	}';

	}
	//INCOMPLETE
	static function a_llsReq($json)
	{
		if (!isset($json['body']['query'])) return;
		if (!isset($json['body']['freq']['start'])) return;
		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbMapsTable'] . "` WHERE " . $json['body']['query']);
		if (isset($table))
		{
			echo '{"header":{"_t":"mfheader", "debug":"true"},
					"_t":"mfmessage",
					"body":{
					"fres":{
					"total":' . count($table) . ',
							"results":[';
			for($i=$json['body']['freq']['start']; $i < count($table); $i++)
			{
				echo '{
						"id":"' . $table[$i]['id'] . '",
								"name":"' . $table[$i]['name'] . '",
										"description":"' . $table[$i]['description'] . '",
												"ownerId":"' . $table[$i]['ownerId'] . '",
														"dc":"' . $table[$i]['dc'] . '",
																"version":"' . $table[$i]['version'] . '",
																		"draft":"' . $table[$i]['draft'] . '",
																				"author":"' . $table[$i]['author'] . '",
																						"editable":"' . $table[$i]['editable'] . '",
																								"dataId":"' . $table[$i]['dataId'] . '",
																										"screenshotId":"' . $table[$i]['screenshotId'] . '",
																												"rating":"' . $table[$i]['rating'] . '",
																														"difficulty":"' . $table[$i]['difficulty'] . '",
																																"props":{"gcid":"' . $table[$i]['gcid'] . '", "editMode":"' . $table[$i]['editMode'] . '"},
																																		"lc":{
																																		"props":{
																																		"is.lotd":"' . $table[$i]['is.lotd'] . '"
			}
			}
			}';
				if (($i+1) < (count($table))) {
					echo ',';
				}

			} //end of for loop
			echo ']
		}
		}
		}';
		}
		else
		{
			echo '{"header":{"_t":"mfheader", "debug":"true"},
					"_t":"mfmessage",
					"body":{
					"fres":{"total":0,"results":[]}
		}
		}';
		}
	}

	static function getFirstUserExperienceReq($json)
	{
		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbMapsTable'] . "`");
		if ($table[0] == NULL) return;

		echo '{"header":{"_t":"mfheader", "debug":"true"},
				"_t":"mfmessage",
				"body":{
				"fres":{
				"total":' . count($table) . ',
					 "results":[';
		for($i=$json['body']['freq']['start']; $i < count($table); $i++)
		{
			echo '{
					"id":' . $table[$i]['id'] . ',
							"name":"' . $table[$i]['name'] . '",
									"description":"' . $table[$i]['description'] . '",
											"ownerId":' . $table[$i]['ownerId'] . ',
													"draft":' . strtolower($table[$i]['draft']) . ',
															"downloads":' . $table[$i]['downloads'] . ',
																	"version":' . $table[$i]['version'] . ',
																			"editable":' . strtolower($table[$i]['editable']) . ',
																					"dataId":' . $table[$i]['dataId'] . ',
																							"screenshotId":' . $table[$i]['screenshotId'] . ',
																									"props":{"gcid":"' . $table[$i]['gcid'] . '", "editMode":"' . $table[$i]['editMode'] . '"}
		}';
			if (($i+1) < (3)) {
				echo ',';
			}

		}
		echo ']
	}
	}
	}';

	}

	//INCOMPLETE
	static function a_getLcReq($json)
	{
		/*
		 Level of the Day
		XP Reward
		Can Play Solo
		Can Host
		Find Finish Flag
		Survival Time Limit
		Beat Score
		Defeat Enemies
		Defeat Bosses
		Can Host CTF
		Can Host Atmo Ball
		Can Host Race
		Can Host Free Combat
		Can Host Team Combat
		Minimum Coop Players
		Max Coop Players
		Minimum Computer Players
		Max Computer Players
		Default Computer Time
		Friendly Fire Enabled
		Abilities State
		Allowed Abilities
		Ghosts
		Ipad
		Is Ipad
		Map Items Count
		*/

		if (!isset($json['body']['lid'])) return;

		//Search Query
		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbMapsTable'] . '` WHERE `id`="' . $json['body']['lid'] . '"');
		if(empty($table)) return;

		echo '{"header":{"_t":"mfheader"},
				"_t":"mfmessage",
				"body":{
				"lc":
				{
				"props":
				{
				"is.lotd":"false",
				"xp.reward":"' . $table[0]['xp.reward'] . '",
						"xgms":"' . $table[0]['xgms'] . '",
								"gms":"' . $table[0]['gms'] . '",
										"gmm":"' . $table[0]['gmm'] . '",
												"gff":"' . $table[0]['gff'] . '",
														"gsv":"' . $table[0]['gsv'] . '",
																"gbs":"' . $table[0]['gbs'] . '",
																		"gde":"' . $table[0]['gde'] . '",
																				"gdb":"' . $table[0]['gdb'] . '",
																						"gctf":"' . $table[0]['gctf'] . '",
																								"gab":"' . $table[0]['gab'] . '",
																										"gra":"' . $table[0]['gra'] . '",
																												"gco":"' . $table[0]['gco'] . '",
																														"gtc":"' . $table[0]['gtc'] . '",
																																"gmmp1":"' . $table[0]['gmmp1'] . '",
																																		"gmmp2":"' . $table[0]['gmmp2'] . '",
																																				"gmcp1":"' . $table[0]['gmcp1'] . '",
																																						"gmcp2":"' . $table[0]['gmcp2'] . '",
																																								"gmcdt":"' . $table[0]['gmcdt'] . '",
																																										"gmcff":"' . $table[0]['gmcff'] . '",
																																												"ast":"' . $table[0]['ast'] . '",
																																														"aal":"' . $table[0]['aal'] . '",
																																																"ghosts":"' . $table[0]['ghosts'] . '",
																																																		"ipad":"' . $table[0]['ipad'] . '",
																																																				"dcap":"' . $table[0]['dcap'] . '",
																																																						"dmic":"' . $table[0]['dmic'] . '",
																																																								"denc":"' . $table[0]['denc'] . '",
																																																										"dpuc":"' . $table[0]['dpuc'] . '",
																																																												"dcoc":"' . $table[0]['dcoc'] . '",
																																																														"dtrc":"' . $table[0]['dtrc'] . '",
																																																																"damc":"' . $table[0]['damc'] . '",
																																																																		"dphc":"' . $table[0]['dphc'] . '",
																																																																				"ddoc":"' . $table[0]['ddoc'] . '",
																																																																						"dkec":"' . $table[0]['dkec'] . '",
																																																																								"dgcc":"' . $table[0]['dgcc'] . '",
																																																																										"dmvc":"' . $table[0]['dmvc'] . '",
																																																																												"dsbc":"' . $table[0]['dsbc'] . '",
																																																																														"dhzc":"' . $table[0]['dhzc'] . '",
																																																																																"dmuc":"' . $table[0]['dmuc'] . '",
																																																																																		"dtmi":"' . $table[0]['dtmi'] . '",
																																																																																				"ddtm":"' . $table[0]['ddtm'] . '",
																																																																																						"dttm":"' . $table[0]['dttm'] . '",
																																																																																								"dedc":"' . $table[0]['dedc'] . '",
																																																																																										"dtsc":"' . $table[0]['dtsc'] . '",
																																																																																												"dopc":"' . $table[0]['dopc'] . '",
																																																																																														"dpoc":"' . $table[0]['dpoc'] . '"

	}
	}
	}
	}';
	}

	//NOTE: INCOMPLETE
	static function loginReq($json)
	{
		if (!isset($json['body']['username'])) return;
		if (!isset($json['body']['password'])) return;

		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbUsersTable'] . "` WHERE `username`='" . $json['body']['username']. "' AND `password`='"  . md5($json['body']['password']) . "'");
		if (!empty($table))
		{
			echo '{"header":{"_t":"mfheader", "debug":"true"},
					"_t":"mfmessage",
					"body":
					{
					"token":"' . $table[0]['token'] . '",
							"userId":' .  $table[0]['userId'] . '
	 }
		}';
		}
		else
		{
			PRINT_ERROR_MSG('NULL');
			return;
		}
	}

	//INCOMPLETE
	//GET PLAYER OWNED ITEMS
	//note: itemId relates to ItemCategory values
	/*
	 ITEM CATEGORY:
	basicProp = 5,
	block = 0,
	connector = 2,
	fallingPlatform = 4,
	flag = 10,
	floor = 1,
	hazard = 7,
	interactive = 6,
	movingPlatform = 3,
	music = 11,
	none = -1,
	powerUp = 9,
	racingProps = 14,
	skybox = 12,
	treasure = 8,
	ugc = 13

	SETITEMSLIST();
	itypeid =
	{
	charCategoryMapping[1L] = CharCategory.none = 0;
	charCategoryMapping[2L] = CharCategory.gender = 10;
	charCategoryMapping[12L] = CharCategory.skin = 15;
	charCategoryMapping[6L] = CharCategory.hair = 20;
	charCategoryMapping[7L] = CharCategory.head = 30;
	charCategoryMapping[0x18L] = CharCategory.glasses = 0x23;
	charCategoryMapping[0x11L] = CharCategory.eyebrows = 40;
	charCategoryMapping[0x10L] = CharCategory.eyes = 50;
	charCategoryMapping[0x13L] = CharCategory.nose = 60;
	charCategoryMapping[0x12L] = CharCategory.mouth = 70;
	charCategoryMapping[9L] = CharCategory.upperBody = 80;
	charCategoryMapping[10L] = CharCategory.hands = 90;
	charCategoryMapping[13L] = CharCategory.back = 100;
	charCategoryMapping[8L] = CharCategory.lowerBody = 110;
	charCategoryMapping[15L] = CharCategory.legs = 120;
	charCategoryMapping[5L] = CharCategory.meleeWeapon = 130;
	charCategoryMapping[0x17L] = CharCategory.rangedWeapon = 140;
	charCategoryMapping[0x19L] = CharCategory.bombWeapon = 160;
	charCategoryMapping[0x34L] = CharCategory.firearmWeapon = 180;
	charCategoryMapping[0x1bL] = CharCategory.perk = 170;
	charCategoryMapping[0x16L] = CharCategory.shield = 150;
	charCategoryMapping[0x1aL] = CharCategory.animation = 190;
	charCategoryMapping[0x21L] = CharCategory.extra = 200;
	}

	*/
	//id:1 = ci_gender_male
	//id:2 = ci_gender_female
	static function findItemisReq($json)
	{

		if (!isset($json['body']['ownerId'])) return;

		echo '{"header":{"_t":"mfheader", "debug":"true"},
				"_t":"mfmessage",
				"body":
				{
				"fres":
				{
				"results":
				[
				{
				"itemId":10,
				"id":132,
				"created":9000
	},
	{
				"itemId":9,
				"id":131,
				"created":9000
	}
				]
	}
	}
	}';
	}

	//INCOMPLETE
	//itypeId = Look Above
	//damagePoints has to be in range of 0-5 GetConvertedDamage()
	//blockFactorsPoints has to be in range of 1-6 GetConvertedBlockFactor()
	//impulsePoints for Weapons (itypeId=5) is 0-5
	//impulseBlockFactor for Weapons is 1-6
	static function findItemsReq($json)
	{

		echo '{"header":{"_t":"mfheader", "debug":"true"},
				"_t":"mfmessage",
				"body":
				{
				"fres":
				{
				"results":
				[{"id":10, "name":"ci_w_wooden_sword",
				"itypeId":5, "created":9000,
				"isid":0, "levels":10,
				"props":
				{
			 "shown":"true",
			 "vehicleCategory":"",
			 "is.free":"true",
			 "is.pro":"false",
			 "is.gift":"false",
			 "is.featured":"false",
			 "duration":"100000",
			 "description":"Super Banhammer That Kills Everything",
			 "upgrade.description":"Huh",
			 "is.rcextra":"false",
			 "quickEquipped":"true",
			 "gearType":"0",
			 "damagePoints":"0",
			 "damagePluses":"1",
			 "blockFactorPoints":"1",
			 "blockFactorPluses":"1",
			 "impulsePoints":"1",
			 "impulsePluses":"1",
			 "impulseBlockFactorPoints":"1",
			 "impulseBlockFactorPluses":"1",
			 "label":"Label",
			 "genders":"0,1"
	}
	},
	{
				"name":"ci_gender_male",
				"id":9,
				"itypeId":2,
				"isid":0,
				"created":12412412,
				"levels":1
	}]
	}
	}
	}';
	}

	//armor = 20
	//none = 0
	//perk = 10
	//INCOMPLETE
	static function findItemSetsReq($json)
	{
		echo '{"header":{"_t":"mfheader", "debug":"true"},
				"_t":"mfmessage",
				"body":
				{
				"fres":
				{
				"results":
				[{"items":[10],
				"id":142,
				"name":"Guest Item Set",
				"created":12412412,
				"props":{
			 "shown":"true",
			 "is.free":"true",
			 "is.pro":"false",
			 "is.gift":"false",
			 "is.featured":"false",
			 "label":"Label",
			 "description":"Default Items for Guests",
			 "genders":"0,1",
			 "setCategory":"20"
	}}]
	}
	}
	}';
	}


	//GET PLAYER'S CURRENTLY EQUIPPED ITEMS
	static function findItemInstanceSetsReq($json)
	{
		if (!isset($json['body']['oid'])) return;
		if (!isset($json['body']['name'])) return;

		echo '{"header":{"_t":"mfheader", "debug":"true"},
				"_t":"mfmessage",
				"body":
				{
				"fres":
				{
				"total":1,
				"results":[{"id":142, "itemis":[132, 131]}]
	}
	}
	}';
	}

	//INCOMPLETE
	static function getProfilesReq($json)
	{
		if(!isset($json['body']['uid'])) return;
		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbUsersTable'] . "` WHERE `userId`='" . $json['body']['uid']. "'");

		if(!empty($table))
		{
			echo '{"header":{"_t":"mfheader"},
					"_t":"mfmessage",
					"body":
					{
					"fres":
					{
					"total":' . count($table) . ',
							"results":
							[';

			for($i=0; $i < count($table); $i++)
			{
				echo '{
						"props":{
						"avaid":"' . $table[$i]['avaid'] . '",
								"signature":"' . $table[$i]['signature'] . '",
										"sessionToken":"' . $table[$i]['sessionToken'] . '",
												"isLOTDMaster":"' . $table[$i]['isLOTDMaster'] . '",
														"isXPMaster":"' . $table[$i]['isXPMaster'] . '",
																"sapo":"' . $table[$i]['sapo'] . '",
																		"vehicleInstanceSetId":"' . $table[$i]['vehicleInstanceSetId'] . '",
																				"activableItemShorcuts":"' . $table[$i]['activableItemShorcuts'] . '",
																						"saInstalled":"' . $table[$i]['saInstalled'] . '"
			},
																								"id":' . $table[$i]['userId'] . ',
																										"created":90
			}';

				if (($i+1) < (count($table))) {
					echo ',';
				}
			}
			echo ']
		}
		}
		}';
		}
	}

	//To Do: Update Database
	//Request: {"header":{"_t":"mfheader", "auth":"0", "debug":"true"}, "_t":"mfmessage", "body":{"profId":1, "_t":"updateProfReq", "props":{"saInstalled":"True", "signature":"0", "sessionToken":"1534100270"}}}
	static function updateProfReq($json)
	{
		if (!isset($json['body']['profId'])) return;
		if (!isset($json['body']['props']['sessionToken'])) return;

		$query = "SET ";
		foreach ($json['body']['props'] as $propname => $propvalue) {
			$query = $query . "`" . $propname . "`='" . $propvalue . "',";
		}
		$query = rtrim($query, ',');

		QUERY_DB("UPDATE `" . $GLOBALS['dbUsersTable'] . "` " . $query . " WHERE `userId`='" . $json['body']['profId'] . "'");
		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbUsersTable'] . "` WHERE `userId`='" . $json['body']['profId']. "'");

		if(!empty($table))
		{
			echo '{"header":{"_t":"mfheader", "debug":"true"},
					"_t":"mfmessage",
					"body":
					{
					"profile":
					{
					"props":{
					"avaid":"' . $table[0]['avaid'] . '",
							"signature":"' . $table[0]['signature'] . '",
									"sessionToken":"' . $table[0]['sessionToken'] . '",
											"isLOTDMaster":"' . $table[0]['isLOTDMaster'] . '",
													"isXPMaster":"' . $table[0]['isXPMaster'] . '",
															"sapo":"' . $table[0]['sapo'] . '",
																	"vehicleInstanceSetId":"' . $table[0]['vehicleInstanceSetId'] . '",
																			"activableItemShorcuts":"' . $table[0]['activableItemShorcuts'] . '",
																					"saInstalled":"' . $table[0]['saInstalled'] . '"
		},
																							"id":' . $table[0]['userId'] . ',
																									"created":90908
		}
		}
		}
		}';
		}
	}

	//INCOMPLETE
	static function registerUserReq($json)
	{
		if (!isset($json['body']['username'])) return;
		if (!isset($json['body']['password'])) return;

		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbUsersTable'] . '` WHERE `username`="' . $json['body']['username'] . '"');
		if (empty($table))
		{
			$lastrow = QUERY_DB("SELECT * FROM " . $GLOBALS['dbUsersTable'] . " ORDER BY userId DESC LIMIT 1");
			QUERY_DB("INSERT INTO " . $GLOBALS['dbUsersTable'] . "(username,password,userId, token, created, avaid, sessionToken, finished, wins, losses, abandons, memberSince, clubMemberSince, levelDesigned,levelComments,designModeTime) VALUES('" . $json['body']['username'] . "','" . md5($json['body']['password']) . "','" . ($lastrow[0]['userId']+1) . "', '" . rand(100000000, 999999999) . "', '0', '0', '0', 'false', '0', '0', '0', '0', '0', '0', '0', '0')");
			$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbUsersTable'] . '` WHERE `username`="' . $json['body']['username'] . '" AND `password`="' . md5($json['body']['password']) . '"');
			if (empty($table)) return;

			echo '{"header":{"_t":"mfheader", "debug":"true"},
					"_t":"mfmessage",
					"body":
					{
					"token":"' . $table[0]['token'] . '",
							"userId":"' . $table[0]['userId'] . '"
		}
		}';
		}


	}

	//INCOMPLETE
	static function findAccountsReq($json)
	{
		if(!isset($json['body']['oid'])) return;
		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbUsersTable'] . "` WHERE `userId`=" . $json['body']['oid']);
		if (!empty($table))
		{
			echo '{"header":{"_t":"mfheader", "debug":"true"},
					"_t":"mfmessage",
					"body":{
					"fres":
					{
					"results":[{
				 "id":' . $table[0]['userId'] . ',
				 		"name":"' . $table[0]['username'] . '",
				 				"balance":{
				 				"cid":' . $table[0]['cid'] . ',
				 						"amt":' . $table[0]['amt'] . '
		}
		}]
		}
		}
		}';
		}
	}

	//NOTE: REQUIRES TESTING
	static function getUserIpAddressReq($json)
	{
		echo '{"header":{"_t":"mfheader", "debug":"true"},
				"_t":"mfmessage",
				"body":
				{
				"ipAddress":"' . $_SERVER['REMOTE_ADDR'] . '"
	}
	}';
	}

	static function getUserByIdReq($json)
	{
		if (!isset($json['body']['uid'])) return;
		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbUsersTable'] . "` WHERE `userId`='" . $json['body']['uid'] . "'");
		if (empty($table)) return;

		echo '{"header":{"_t":"mfheader", "debug":"true"},
				"_t":"mfmessage",
				"body":
				{
				"verified":"[MODIFIED BY DLL]",
				"xpp":' . $table[0]["xpp"] . ',
						"isClubMember":' . $table[0]["isClubMember"] . ',
								"paidBy":"' . $table[0]["paidBy"] . '",
										"user":
										{
										"props":
										{
										"development":"' . $table[0]["development"] . '",
												"external":"' . $table[0]["external"] . '"
	},
														"username":"' . $table[0]["username"] . '",
																"created":' . $table[0]["created"] . ',
																		"flags":' . $table[0]["flags"] . ',
																				"locale":"' . $table[0]["locale"] . '"
	}
	}
	}';
	}

	//NOTE: INCOMPLETE
	static function addLevelReq($json)
	{

		$lastrow = QUERY_DB("SELECT * FROM " . $GLOBALS['dbMapsTable'] . " ORDER BY `id` DESC LIMIT 1");
		$userTable = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbUsersTable'] . "` WHERE `userId`='" . $json['body']['level']['ownerId'] . "'");
		QUERY_DB("INSERT INTO " . $GLOBALS['dbMapsTable'] . "(id,name,description, author, downloads, editable, ownerId, draft) VALUES('" . $lastrow[0]['id'] + 1 . "','" . $json['body']['level']['name'] . "','" . $json['body']['level']['description'] . "','" . $userTable['username'] . "', '" . $json['body']['level']['downloads'] . "', '" . $json['body']['level']['editable']  . "', '" . $json['body']['level']['ownerId']   . "', '" . $json['body']['level']['draft'] . "')");

		echo '{"header":{"_t":"mfheader", "debug":"true"},
				"_t":"mfmessage",
				"body":
				{
				"levelId":"' . $lastrow[0]['id'] + 1 . '"
	}
	}';
	}


	//Incomplete
	//To Do: Red Carpet Handle
	//Response: {"header":{"_t":"mfheader", "auth":"0", "debug":"true"}, "_t":"mfmessage", "body":{"_t":"getRedCarpetReq", "userId":1}}
	static function getRedCarpetReq($json)
	{
		if (!isset($json['body']['userId'])) return;

		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbUsersTable'] . "` WHERE `userId`='" . $json['body']['userId'] . "'");
		if (!empty($table))
		{
			echo '{"header":{"_t":"mfheader", "debug":"true"},
					"_t":"mfmessage",
					"body":
					{
					"finished":"' . $table[0]['finished'] . '"
		}
		}';
		}
	}

	//INCOMPLETE
	//CREATE AVATAR IMAGES OR SUCH
	static function createAssetReq($json)
	{
		if (!isset($json['body']['asset'])) return;
		if (!isset($json['body']['data'])) return;

		$my_file = $_SERVER['DOCUMENT_ROOT'] . '//Atmosphir/' . $GLOBALS['path_images'] . "/avatars/AvatarImage_" . $json['body']['asset']['ownerId'] . ".png";
		$handle = fopen($my_file, 'w') or die("Could not create file:" . $my_file);
		//$data = pack("H" . strlen($json['body']['data']), $json['body']['data']);
		fwrite($handle, pack("H*", $json['body']['data']));
		fclose($handle);

		echo '{"header":{"_t":"mfheader", "debug":"true"},
				"_t":"mfmessage",
				"body":
				{
				"asset":
				{
				"id":' . $json['body']['asset']['ownerId'] . '
	}
	}
	}';
	}

	//INCOMPLETE
	//Update User Items
	static function updateItemiSetReq($json)
	{
		//if (!isset($json['body']['ownerId'])) return;
		//if (!isset($json['body']['additions'])) return;
		//if (!isset($json['body']['removals'])) return;
		if (!isset($json['body']['iisid'])) return;

		echo '{"header":{"_t":"mfheader"}, "body":{"_t":"mfsuccess"}}}';
	}

	//INCOMPLETE
	static function setRedCarpetReq($json)
	{
		if (!isset($json['body']['userId'])) return;
		$table = QUERY_DB("UPDATE `" . $GLOBALS['dbUsersTable'] . "` SET `finished`='true' WHERE `userId`='" . $json['body']['userId'] . "'");
		if (!empty($table))
		{
			echo '{"header":{"_t":"mfheader", "debug":"true"},
					"_t":"mfmessage",
					"body":
					{
					"finished":"' . $table[0]['finished'] . '"
		}
		}';
		}
	}

	//INCOMPLETE
	static function getQuickStatsReq($json)
	{
		if (!isset($json['body']['userId'])) return;

		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbUsersTable'] . "` WHERE `userId`='" . $json['body']['userId'] . "'");
		echo '{"header":{"_t":"mfheader", "debug":"true"},
				"_t":"mfmessage",
				"body":
				{
				"wins":"' . $table[0]['wins'] . '",
						"losses":"' . $table[0]['losses'] . '",
								"abandons":"' . $table[0]['abandons'] . '",
										"memberSince":"' . $table[0]['memberSince'] . '",
												"clubMemberSince":"' . $table[0]['clubMemberSince'] . '",
														"levelDesigned":"' . $table[0]['levelDesigned'] . '"
																"forumPost":"0",
																"levelComments":"' . $table[0]['levelComments'] . '",
																		"designModeTime":"' . $table[0]['designModeTime'] . '"
	}
	}';
	}

	static function a_getUserBadgesReq($json)
	{
		if (!isset($json['body']['uid'])) return;

		$table = QUERY_DB("SELECT * FROM `" . $GLOBALS['dbUsersTable'] . "` WHERE `userId`='" . $json['body']['uid'] . "'");
		echo '{"header":{"_t":"mfheader", "debug":"true"},
				"_t":"mfmessage",
				"body":
				{
				"won":' . $table[0]['wins'] . ',
						"lost":' . $table[0]['losses'] . ',
								"abandoned":' . $table[0]['abandons'] . '
	}
	}';
	}

	function getFriendsByIdReq($json)
	{
		echo '{"header":{"_t":"mfheader", "debug":"true"},
				"_t":"mfmessage",
				"body":
				{
				"total":1,
				"fres":{"results":[{"atype":"ENEMY"}]
	}
	}
	}';
	}
}


?>	