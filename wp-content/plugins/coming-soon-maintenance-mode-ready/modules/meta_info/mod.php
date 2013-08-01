<?php
class meta_infoCsp extends moduleCsp {
	public function init() {
		dispatcherCsp::addFilter('adminOptionsTabs', array($this, 'addOptionsTab'));
	}
	public function addOptionsTab($tabs) {
		frameCsp::_()->addScript('adminMetaOptions', $this->getModPath(). 'js/admin.meta_info.options.js');
		$tabs['cspMetaIcons'] = array(
		   'title' => 'Meta Info', 'content' => $this->getController()->getView()->getAdminOptions(),
		);
		return $tabs;
	}
	public function getList() {
		return dispatcherCsp::applyFilters('metaTagsList', array(
			'meta_title' => array(
				'label'				=> 'Title',
				'optsTplEngine'		=> array($this->getController()->getView(), 'getTitleOpts'),
			),
			'meta_desc' => array(
				'label'				=> 'Description',
				'optsTplEngine'		=> array($this->getController()->getView(), 'getDescOpts'),
			),
			'meta_keywords' => array(
				'label'				=> 'Keywords',
				'optsTplEngine'		=> array($this->getController()->getView(), 'getKeywordsOpts'),
			),
		));
	}
}