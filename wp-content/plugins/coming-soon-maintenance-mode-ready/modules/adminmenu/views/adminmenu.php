<?php
class adminmenuViewCsp extends viewCsp {
    protected $_file = '';
    /**
     * Array for standart menu pages
     * @see initMenu method
     */
    /*protected $_options = array(
        
    );*/
    public function init() {
        $this->_file = __FILE__;
		//$this->_options = dispatcherCsp::applyFilters('adminMenuOptions', $this->_options);
        add_action('admin_menu', array($this, 'initMenu'), 9);
        parent::init();
    }
    public function initMenu() {
		
        add_menu_page(langCsp::_('Ready! Comming Soon'), langCsp::_('Ready! Comming Soon'), 10, $this->_file, array(frameCsp::_()->getModule('options')->getView(), 'getAdminPage'));
		/*if(!empty($this->_options)) {
			foreach($this->_options as $opt) {
				add_submenu_page($this->_file, langCsp::_($opt['title']), langCsp::_($opt['title']), $opt['capability'], $opt['menu_slug'], $opt['function']);
			}
		}*/
    }
    public function getFile() {
        return $this->_file;
    }
    /*public function getOptions() {
        return $this->_options;
    }*/
}