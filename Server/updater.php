<?php
function hasvar($variable) {
	return isset($_GET[$variable]);
}
function getvar($variable) {
	return $_GET[$variable];
}
if (hasvar("news")) {
	include("client/updateNews.php");
} elseif (hasvar("starting")) {
	echo "Acknowledged";
	//TODO: Track launcher opens?
} elseif (hasvar("hashes")) {
	echo file_get_contents("client/clientHashList.txt");
} elseif (hasvar("file")) {
	$filepath = str_replace("..", "", getvar("file")); //Prevent up-directory shortcutting
	$os = "";
	if (hasvar("os")) {
		$os = getvar("os");
	} else {
		$os = "windows";
	}
	$filepath = "client/ClientFiles/$os/$filepath";
	if (file_exists($filepath)) {
		echo file_get_contents($filepath);
	}
}