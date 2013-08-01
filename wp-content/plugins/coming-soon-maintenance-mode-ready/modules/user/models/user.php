<?php
class userModelCsp extends modelCsp {
    protected $_data = array();
    protected $_curentID = 0;
    protected $_meta = array(
        
    );
	protected $_dataLoaded = false;
	
	public function getCurrentID() {
		$this->_loadUserData();
        return intval($this->_curentID);
    }
	protected function _loadUserData() {
		if(!$this->_dataLoaded) {
			if(!function_exists('wp_get_current_user')) frameCsp::_()->loadPlugins();
			$user = wp_get_current_user();
			$this->_data = $user->data;
			$this->_curentID = $this->_data->ID;
			$this->_dataLoaded = true;
		}
	}
}
