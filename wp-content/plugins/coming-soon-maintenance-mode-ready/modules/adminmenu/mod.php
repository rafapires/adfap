<?php
class adminmenuCsp extends moduleCsp {
    public function init() {
        parent::init();
        $this->getController()->getView('adminmenu')->init();
		$plugName = plugin_basename(CSP_DIR. CSP_MAIN_FILE);
		add_filter('plugin_action_links_'. $plugName, array($this, 'addSettingsLinkForPlug') );
    }
	public function addSettingsLinkForPlug($links) {
		array_unshift($links, '<a href="'. uriCsp::_(array('baseUrl' => admin_url('admin.php'), 'page' => plugin_basename(frameCsp::_()->getModule('adminmenu')->getView()->getFile()))). '">'. langCsp::_('Settings'). '</a>');
		return $links;
	}
}

