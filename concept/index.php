<?php 
class Configuration {
	private static $configs;
	public static function loadFromIni($path) {
		self::$configs = parse_ini_file($path, true);	
	}
	/*
	 * $ipath dot-separated path string as such: "section.value"
	 * This function returns a string from the configuration with the correspnoding setting
	 */
	private static function _get($ipath, $ivar) {
		$ipath = explode(".", $ipath);
		$pitem = $ipath[0];
		if (!is_null($pitem)) {
			if (is_array($ivar) && count($ivar) > 1) {
				$restOfPath = implode(".", array_slice($ipath, 1));
				return self::_get($restOfPath, $ivar[$pitem]);
			} else {
				return $ivar;
			}
		}
		return isset($ivar[$pitem]);
	}
	public static function get($ipath) {
		return self::_get($ipath, self::$configs);
	}
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 01 Jan 1996 00:00:00 GMT');
header('Content-type: application/json');

Configuration::loadFromIni("configs.ini");
Configuration::get("database.table_maps");
$_REQUEST["json"] = '
{
	"header": {},
	"body": {
		"_t": "derp"
	}
}';
if(isset($_REQUEST["json"])) {
	$json = get_magic_quotes_gpc() ? json_decode(stripslashes($_REQUEST['json']), true) : json_decode($_REQUEST['json'], true);
	if (!isset($json['header'])) return;
	if (!isset($json['body'])) return;
	if (!isset($json['body']['_t'])) return;
	
	$reqtype   = basename($json["body"]["_t"]); 

	$whiteList = array('derp' => 'derp.php'); 
	$associated_file = $whiteList[$reqtype];
	if ($associated_file == '') return;
	
	require($associated_file);
	$request = new $reqtype();
	$request->work($json);
	$request->send();
}
?>
