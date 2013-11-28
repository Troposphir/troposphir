<?php

require('configs.php');
require("./include/CDatabase.php");

$db = new Database($config['driver'], $config['host'], $config['dbname'], $config['user'], $config['password']);

//=====================SETUP USER TABLE=====================
// There might be new changes we need to apply to our user table
//that can only be done column by column, so instead we will:	
//(1) create a new temporary table (name will have an appended "_temp") with appropriate column definitions
$db->query("CREATE TABLE " . $config['table_user'] . "_temp ( 
	userId INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(255) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	token INT NOT NULL DEFAULT 0,
	created INT NOT NULL DEFAULT 0,
	avaid INT NOT NULL DEFAULT 0,
	sessionToken INT NOT NULL DEFAULT 0,
	isDev BOOL NOT NULL DEFAULT 0,
	isLOTDMaster BOOL NOT NULL DEFAULT 0,
	isXPMaster BOOL NOT NULL DEFAULT 0,
	development BOOL NOT NULL DEFAULT 0,
	external BOOL NOT NULL DEFAULT 0,
	flags INT NOT NULL DEFAULT 0,
	locale VARCHAR(255) NOT NULL DEFAULT '',
	verified BOOL NOT NULL DEFAULT 0,
	xpp INT NOT NULL DEFAULT 0,
	isClubMember BOOL NOT NULL DEFAULT 0,
	paidBy VARCHAR(255) NOT NULL DEFAULT '',
	sapo VARCHAR(255) NOT NULL DEFAULT '',
	vehicleInstanceSetId INT NOT NULL DEFAULT 0,
	activableItemShorcuts VARCHAR(255) NOT NULL DEFAULT '0;0;0;0;0;0;0;0;',
	saInstalled BOOL NOT NULL DEFAULT 0,
	signature VARCHAR(255) NOT NULL DEFAULT '',
	finished BOOL NOT NULL DEFAULT 0,
	wins INT NOT NULL DEFAULT 0,
	losses INT NOT NULL DEFAULT 0,
	abandons INT NOT NULL DEFAULT 0,
	memberSince INT NOT NULL DEFAULT 0,
	clubMemberSince INT NOT NULL DEFAULT 0,
	levelDesigned INT NOT NULL DEFAULT 0,
	levelComments INT NOT NULL DEFAULT 0,
	designModeTime INT NOT NULL DEFAULT 0,
	cid INT NOT NULL DEFAULT 0,
	amt INT NOT NULL DEFAULT 0
)", null);
$lastError = $db->errorInfo();
if ($lastError[0] != "00000") {
	echo "SETUP ABORTED. <br> Failed to create a temporary user table for the following reason: <i>$lastError[2]</i>";
	return;
}
	
//(2) copy the data over
$db->query("INSERT INTO " . $config['table_user'] . "_temp (
	userId, username, password, token, created, avaid, sessionToken, 
	isDev, isLOTDMaster, isXPMaster, development, external, flags, locale,
	verified, xpp, isClubMember, paidBy, sapo, vehicleInstanceSetId, 
	activableItemShorcuts, saInstalled, signature, finished, wins,
	losses, abandons, memberSince, clubMemberSince, levelDesigned, 
	levelComments, designModeTime, cid, amt
) 
SELECT *
FROM " . $config['table_user']
, null);
$lastError = $db->errorInfo();
if ($lastError[0] != "00000") {
	echo "SETUP ABORTED. <br> Failed to copy data over for the following reason: <i>$lastError[2]</i>";
	return;
}
	
//VARCHAR apparently does not treat blank values as null. 
//So we replace empty strings with our own values.
//$db->query("UPDATE " . $config['table_user'] . "_temp SET activableItemShorcuts = '0;0;0;0;0;0;0;0;0;0;'	WHERE activableItemShorcuts = ''", null);

//(3) drop the original
$db->query("DROP TABLE " . $config['table_user'], null);
	
//(4) rename the temporary table name to the original
$db->query("RENAME TABLE `" . $config['table_user'] . "_temp` TO `" . $config['table_user'] . "`", null);




//===================SETUP MAP TABLE=================
//There might be new changes we need to apply to our user table
//that can only be done column by column, so instead we will:	
//(1) create a new temporary table (name will have an appended "_temp") with appropriate column definitions
$statement = $db->query("CREATE TABLE " . $config['table_map'] . "_temp ( 
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) NOT NULL UNIQUE,
	description VARCHAR(255) NOT NULL DEFAULT '',
	author VARCHAR(255) NOT NULL,
	dc INT NOT NULL DEFAULT 0,
	rating INT NOT NULL DEFAULT 0,
	difficulty FLOAT NOT NULL DEFAULT 0,
	ownerId INT NOT NULL DEFAULT 0,
	downloads INT NOT NULL DEFAULT 0,
	dataId INT NOT NULL DEFAULT 0,
	screenshotId INT NOT NULL DEFAULT 0,
	version INT NOT NULL DEFAULT 0,
	draft BOOL NOT NULL DEFAULT 0,
	nextLevelId INT NOT NULL DEFAULT 0,
	editable BOOL NOT NULL DEFAULT 0,
	deleted BOOL NOT NULL DEFAULT 0,
	gcid INT NOT NULL DEFAULT 0,
	editMode INT NOT NULL DEFAULT 0,
	xisLOTD INT NOT NULL DEFAULT 0,
	isLOTD BOOL NOT NULL DEFAULT 0,
	xpReward INT NOT NULL DEFAULT 0,
	xgms INT NOT NULL DEFAULT 0,
	gms INT NOT NULL DEFAULT 0,
	gmm INT NOT NULL DEFAULT 0,
	gff INT NOT NULL DEFAULT 0,
	gsv INT NOT NULL DEFAULT 0,
	gbs INT NOT NULL DEFAULT 0,
	gde INT NOT NULL DEFAULT 0,
	gdb INT NOT NULL DEFAULT 0,
	gctf INT NOT NULL DEFAULT 0,
	gab INT NOT NULL DEFAULT 0,
	gra INT NOT NULL DEFAULT 0,
	gco INT NOT NULL DEFAULT 0,
	gtc INT NOT NULL DEFAULT 0,
	gmmp1 INT NOT NULL DEFAULT 0,
	gmmp2 INT NOT NULL DEFAULT 0,
	gmcp1 INT NOT NULL DEFAULT 0,
	gmcp2 INT NOT NULL DEFAULT 0,
	gmcdt INT NOT NULL DEFAULT 0,
	gmcff INT NOT NULL DEFAULT 0,
	ast INT NOT NULL DEFAULT 0,
	aal INT NOT NULL DEFAULT 0,
	ghosts INT NOT NULL DEFAULT 0,
	ipad INT NOT NULL DEFAULT 0,
	dcap INT NOT NULL DEFAULT 0,
	dmic INT NOT NULL DEFAULT 0,
	xpLevel INT NOT NULL DEFAULT 0,
	denc INT NOT NULL DEFAULT 0,
	dpuc INT NOT NULL DEFAULT 0,
	dcoc INT NOT NULL DEFAULT 0,
	dtrc INT NOT NULL DEFAULT 0,
	damc INT NOT NULL DEFAULT 0,
	dphc INT NOT NULL DEFAULT 0,
	ddoc INT NOT NULL DEFAULT 0,
	dkec INT NOT NULL DEFAULT 0,
	dgcc INT NOT NULL DEFAULT 0,
	dmvc INT NOT NULL DEFAULT 0,
	dsbc INT NOT NULL DEFAULT 0,
	dhzc INT NOT NULL DEFAULT 0,
	dmuc INT NOT NULL DEFAULT 0,
	dtmi INT NOT NULL DEFAULT 0,
	ddtm INT NOT NULL DEFAULT 0,
	dttm INT NOT NULL DEFAULT 0,
	dedc INT NOT NULL DEFAULT 0,
	dtsc INT NOT NULL DEFAULT 0,
	dopc INT NOT NULL DEFAULT 0,
	dpoc INT NOT NULL DEFAULT 0
)", null);
$lastError = $db->errorInfo();
if ($lastError[0] != "00000") {
	echo "SETUP ABORTED. <br> Failed to create a temporary map table for the following reason: <i>$lastError[2]</i>";
	return;
}
	
//(2) copy the data over
$statement = $db->query("INSERT INTO " . $config['table_map'] . "_temp (
	id, name, description, author, dc, rating, difficulty, 
	ownerId, downloads, dataId, screenshotId, version, draft, nextLevelId,
	editable, deleted, gcid, editMode, xisLOTD, isLOTD, xpReward,
	xgms, gms, gmm, gff, gsv, gbs, gde, gdb, gctf, gab, gra, gco, gtc,
	gmmp1, gmmp2, gmcp1, gmcp2, gmcdt, gmcff, ast, aal, ghosts, ipad,
	dcap, dmic, xpLevel, denc, dpuc, dcoc, dtrc, damc, dphc, ddoc, dkec, 
	dgcc, dmvc, dsbc, dhzc, dmuc, dtmi, ddtm, dttm, dedc, dtsc, dopc, dpoc
) 
SELECT *
FROM " . $config['table_map']
, null);
$lastError = $db->errorInfo();
if ($lastError[0] != "00000") {
	echo "SETUP ABORTED. <br> Failed to copy data over for the following reason: <i>$lastError[2]</i>";
	return;
}

//(3) drop the original
$db->query("DROP TABLE " . $config['table_map'], null);
	
//(4) rename the temporary table name to the original
$db->query("RENAME TABLE `" . $config['table_map'] . "_temp` TO `" . $config['table_map'] . "`", null);
		
echo 'SETUP COMPLETED.';
?>