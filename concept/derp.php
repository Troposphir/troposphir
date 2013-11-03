<?php 
require("request.php");
class derp extends RequestResponse {
	public function work($json) {
		$this->addBody("hello", "this is an example request");
		$this->error("APP_NOT_FOUND");
	}
}
?>