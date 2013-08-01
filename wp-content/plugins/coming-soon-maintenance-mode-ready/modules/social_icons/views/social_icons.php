<?php
class social_iconsViewCsp extends viewCsp {
	public function getAdminOptions() {
		$iconsList = $this->getModule()->getList();
		foreach($iconsList as $key => $icon) {
			if(isset($icon['optsTplEngine']) && !empty($icon['optsTplEngine']))
				$iconsList[$key]['adminOptsContent'] = call_user_func($icon['optsTplEngine']);
		}
		$this->assign('iconsList', $iconsList);
		$this->assign('optsModel', frameCsp::_()->getModule('options')->getController()->getModel());
		return parent::getContent('socAdminOptions');
	}
	public function getFbOpts() {
		$this->assign('optsModel', frameCsp::_()->getModule('options')->getController()->getModel());
		return parent::getContent('socFbOpts');
	}
	public function getFbButtons() {
		$out = array();
		$optsModel = frameCsp::_()->getModule('options')->getController()->getModel();
		$this->assign('optsModel', $optsModel);
		$this->assign('currentUrl', get_bloginfo('wpurl'). $_SERVER['REQUEST_URI']);
		if(!$optsModel->isEmpty('soc_facebook_enable_link'))
			$out['link'] = parent::getContent('socFbLink');
		if(!$optsModel->isEmpty('soc_facebook_enable_share'))
			$out['share'] = parent::getContent('socFbShare');
		if(!$optsModel->isEmpty('soc_facebook_enable_like'))
			$out['like'] = parent::getContent('socFbLike');
		if(!$optsModel->isEmpty('soc_facebook_enable_follow'))
			$out['follow'] = parent::getContent('socFbFollow');
		if(!empty($out)) {
			// If there are some content - include sdk scripts, let's make it only one time for all fb buttons
			$out['sdk'] = parent::getContent('socFbSdk');
		}
		return $out;
	}
	public function getFrontendContent() {
		$iconsList = $this->getModule()->getList();
		foreach($iconsList as $key => $icon) {
			if(isset($icon['engine']) && !empty($icon['engine'])) {
				$iconsData = call_user_func($icon['engine']);
				if(!empty($iconsData)) {
					$iconsList[$key]['htmlContent'] = $iconsData;
				}
			}
		}
		$this->assign('iconsList', $iconsList);
		return parent::getContent('socFrontendIcons');
	}
	public function getTwOpts() {
		$this->assign('optsModel', frameCsp::_()->getModule('options')->getController()->getModel());
		return parent::getContent('socTwOpts');
	}
	public function getTwButtons() {
		$out = array();
		$optsModel = frameCsp::_()->getModule('options')->getController()->getModel();
		$this->assign('optsModel', $optsModel);
		$this->assign('currentUrl', get_bloginfo('wpurl'). $_SERVER['REQUEST_URI']);
		$this->assign('langIso2Code', substr(CSP_WPLANG, 0, 2));
		if(!$optsModel->isEmpty('soc_tw_enable_link'))
			$out['link'] = parent::getContent('socTwLink');
		if(!$optsModel->isEmpty('soc_tw_enable_tweet'))
			$out['like'] = parent::getContent('socTwTweet');
		if(!$optsModel->isEmpty('soc_tw_enable_follow'))
			$out['follow'] = parent::getContent('socTwFollow');
		if(!empty($out)) {
			// If there are some content - include sdk scripts, let's make it only one time for all fb buttons
			$out['sdk'] = parent::getContent('socTwSdk');
		}
		return $out;
	}
	public function getGpOpts() {
		$this->assign('optsModel', frameCsp::_()->getModule('options')->getController()->getModel());
		return parent::getContent('socGpOpts');
	}
	public function getGpButtons() {
		$out = array();
		$optsModel = frameCsp::_()->getModule('options')->getController()->getModel();
		$this->assign('optsModel', $optsModel);
		$this->assign('currentUrl', get_bloginfo('wpurl'). $_SERVER['REQUEST_URI']);
		$this->assign('langCode', get_bloginfo('language'));
		if(!$optsModel->isEmpty('soc_gp_enable_link'))
			$out['link'] = parent::getContent('socGpLink');
		if(!$optsModel->isEmpty('soc_gp_enable_badge'))
			$out['follow'] = parent::getContent('socGpBadge');
		if(!$optsModel->isEmpty('soc_gp_enable_like'))
			$out['like'] = parent::getContent('socGpLike');
		if(!empty($out)) {
			// If there are some content - include sdk scripts, let's make it only one time for all fb buttons
			$out['sdk'] = parent::getContent('socGpSdk');
		}
		return $out;
	}
}