<?php
require("CRequest.php");
class getGameContextReq extends RequestResponse {
	public function work($json) {
		$this->addBody('assetEndpoint', $this->config_['site'] . $this->config_['dir_assets']);
		$this->addBody('mailboxEndpoint', "");
		$this->addBody('friendsUrl', "");
		$this->addBody('masterServerHost', $this->config_['site']);
		$this->addBody('masterServerPort', 80);
		$this->addBody('natFacilitatorPort', 0);
		$this->addBody('redCarpetTutorialLevelId', 21689);
		$this->addBody('charCustLevelId', 21689);
		$this->addBody('levelBrowserLevelId', 21689);
		$this->addBody('tutorialUserId', 0);
		$this->addBody('unityBundleUrl', "");
		$this->addBody('staticImagesUrl', $this->config_['site'] . $this->config_['dir_imgs']);
		$this->addBody('staticMapUrl', $this->config_['site'] . $this->config_['dir_maps']);
		$this->addBody('staticAvatarUrl', $this->config_['site'] . $this->config_['dir_imgs']);
	}
}
?>