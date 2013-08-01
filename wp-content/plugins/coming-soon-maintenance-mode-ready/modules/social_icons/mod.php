<?php
class social_iconsCsp extends moduleCsp {
	public function init() {
		dispatcherCsp::addFilter('adminOptionsTabs', array($this, 'addOptionsTab'));
	}
	public function getList() {
		return dispatcherCsp::applyFilters('socIconsList', array(
			'facebook' => array(
				'label'				=> 'Facebook',
				'engine'			=> array($this->getController()->getView(), 'getFbButtons'),
				'optsTplEngine'		=> array($this->getController()->getView(), 'getFbOpts'),
			),
			'twitter' => array(
				'label'				=> 'Twitter',
				'engine'			=> array($this->getController()->getView(), 'getTwButtons'),
				'optsTplEngine'		=> array($this->getController()->getView(), 'getTwOpts'),
			),
			'gplus' => array(
				'label'				=> 'Google+',
				'engine'			=> array($this->getController()->getView(), 'getGpButtons'),
				'optsTplEngine'		=> array($this->getController()->getView(), 'getGpOpts'),
			),
		));
	}
	public function addOptionsTab($tabs) {
		frameCsp::_()->addScript('adminSocialOptions', $this->getModPath(). 'js/admin.social_icons.options.js');
		$tabs['cspSocIcons'] = array(
		   'title' => 'Social Buttons', 'content' => $this->getController()->getView()->getAdminOptions(),
		);
		return $tabs;
	}
}