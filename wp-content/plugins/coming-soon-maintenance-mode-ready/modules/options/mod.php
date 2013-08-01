<?php
class optionsCsp extends moduleCsp {
	protected $_uploadDir = 'csp';
	protected $_bgImgSubDir = 'bg_img';
	protected $_bgLogoImgSubDir = 'logo_img';

    /**
     * Method to trigger the database update
     */
    public function init(){
        parent::init();
        /*$add_option = array(
            'add_checkbox' => langCsp::_('Add Checkbox'),
            'add_radiobutton' => langCsp::_('Add Radio Button'),
            'add_item' => langCsp::_('Add Item'),
        );
        frameCsp::_()->addJSVar('adminOptions', 'TOE_LANG', $add_option);*/
    }
    /**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
        $tab = new tabCsp(langCsp::_('General'), $this->getCode());
        $tab->setView('optionTab');
        $tab->setSortOrder(-99);
        $tabs[] = $tab;
        return $tabs;
    }
    /**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
    public function get($d = array()) {
        return $this->getController()->getModel()->get($d);
    }
	/**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
	public function isEmpty($d = array()) {
		return $this->getController()->getModel()->isEmpty($d);
	}
	
	public function getUploadDir() {
		return $this->_uploadDir;
	}
	public function getBgImgDir() {
		return $this->_uploadDir. DS. $this->_bgImgSubDir;
	}
	public function getBgImgFullDir() {
		return utilsCsp::getUploadsDir(). DS. $this->getBgImgDir(). DS. $this->get('bg_image');
	}
	public function getBgImgFullPath() {
		return utilsCsp::getUploadsPath(). '/'. $this->_uploadDir. '/'. $this->_bgImgSubDir. '/'. $this->get('bg_image');
	}
	
	public function getLogoImgDir() {
		return $this->_uploadDir. DS. $this->_bgLogoImgSubDir;
	}
	public function getLogoImgFullDir() {
		return utilsCsp::getUploadsDir(). DS. $this->getLogoImgDir(). DS. $this->get('logo_image');
	}
	public function getLogoImgFullPath() {
		return utilsCsp::getUploadsPath(). '/'. $this->_uploadDir. '/'. $this->_bgLogoImgSubDir. '/'. $this->get('logo_image');
	}
}

