<?php
	$assetId = filter_var(preg_replace("/[^0-9]/", "", $_REQUEST['id']), FILTER_VALIDATE_INT);
	if ($assetId == false) return;
	
	echo @file_get_contents("./$assetId")
?>