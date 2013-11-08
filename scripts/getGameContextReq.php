<?php
require("CRequest.php");
class getGameContextReq extends RequestResponse {
	public function work($json) {
		$this->addBody('assetEndpoint', $CONFIG_SITE . $CONFIG_DIR_ASSETS);
		$this->addBody('mailboxEndpoint', "");
		$this->addBody('friendsUrl', "");
		$this->addBody('masterServerHost', $CONFIG_SITE);
		$this->addBody('masterServerPort', 80);
		$this->addBody('natFacilitatorPort', 0);
		$this->addBody('redCarpetTutorialLevelId', 21689);
		$this->addBody('charCustLevelId', 21689);
		$this->addBody('levelBrowserLevelId', 21689);
		$this->addBody('tutorialUserId', 0);
		$this->addBody('unityBundleUrl', "");
		$this->addBody('staticImagesUrl', $CONFIG_SITE . $CONFIG_DIR_IMAGES);
		$this->addBody('staticMapUrl', $CONFIG_SITE . $CONFIG_DIR_MAPS);
		$this->addBody('staticAvatarUrl', $CONFIG_SITE . $CONFIG_DIR_IMGS);
	}
}
?>