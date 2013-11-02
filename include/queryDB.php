<?php 
function QUERY_DB($query) {
	if($query != null) {
		//ADJUST MYSQL SYNTAX
		$begpos = strpos($query, "AND ct:[");
		if ($begpos !== false) {
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
		$link = mysql_connect($GLOBALS['configs']['db_host'],$GLOBALS['configs']['db_user'],$GLOBALS['configs']['db_password']);
		if ($link == NULL) {echo 'There were problems connecting to the database.'; return NULL;}

		$success = mysql_select_db($GLOBALS['configs']['db_name'], $link);
		if ($success == false) {echo "Database could not be found."; return NULL;}
		 
		$result = mysql_query(stripslashes(mysql_real_escape_string($query)));
		if (!$result) {echo "Invalid Query."; return NULL;}
		 
		while($row = mysql_fetch_assoc($result)) {
			array_push($array, $row);
		}
		mysql_free_result($result);
		mysql_close($link);

		return $array;
	}
}
?>