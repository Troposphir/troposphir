<?php
//Minimalistic news update as plain text
$news_file = fopen("changelog.txt", "r");
header("Content-Type: text/plain");
while (($char = fread($news_file, 1)) != "^") {
	echo $char;
}
fclose($news_file);