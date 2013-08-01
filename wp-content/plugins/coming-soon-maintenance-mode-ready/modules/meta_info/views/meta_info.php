<?php
class meta_infoViewCsp extends viewCsp {
	public function getAdminOptions() {
		$metaTags = $this->getModule()->getList();
		foreach($metaTags as $key => $icon) {
			if(isset($icon['optsTplEngine']) && !empty($icon['optsTplEngine']))
				$metaTags[$key]['adminOptsContent'] = call_user_func($icon['optsTplEngine']);
		}
		$this->assign('metaTags', $metaTags);
		return parent::getContent('metaAdminOpts');
	}
	public function getTitleOpts() {
		$this->assign('optsModel', frameCsp::_()->getModule('options')->getController()->getModel());
		return parent::getContent('metaTitleOpts');
	}
	public function getKeywordsOpts() {
		$this->assign('optsModel', frameCsp::_()->getModule('options')->getController()->getModel());
		return parent::getContent('metaDescOpts');
	}
	public function getDescOpts() {
		$this->assign('optsModel', frameCsp::_()->getModule('options')->getController()->getModel());
		return parent::getContent('metaKeywordsOpts');
	}
}