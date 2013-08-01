<?php
class coming_soonCsp extends moduleCsp {
	public function init() {
		parent::init();
		add_action('plugins_loaded', array($this, 'doCominSoonPage'));
	}
	public function doCominSoonPage() {
		$mode = frameCsp::_()->getModule('options')->get('mode');
		if($mode != 'disable' && !is_admin() && !frameCsp::_()->getModule('pages')->isLogin() && !current_user_can('manage_options')) {
			switch($mode) {
				case 'coming_soon':
					$template = frameCsp::_()->getModule('options')->get('template');
					if(!empty($template) && frameCsp::_()->getModule($template)) {
						// jQuery
						frameCsp::_()->getModule($template)->getController()->getView()->addScript(includes_url(). 'js/jquery/jquery.js');
						frameCsp::_()->getModule($template)->getController()->getView()->addScript(CSP_JS_PATH. 'common.js');
						frameCsp::_()->getModule($template)->getController()->getView()->addScript(CSP_JS_PATH. 'core.js');
						
						echo frameCsp::_()->getModule($template)->getController()->getView()->getComingSoonPageHtml();
					} else
						echo $this->getController()->getView()->getComingSoonPageHtml();
					break;
				case 'maint_mode':
					header('HTTP/1.1 503 Service Temporarily Unavailable');
					header('Status: 503 Service Temporarily Unavailable');
					header('Retry-After: 300');
					break;
				case 'redirect':
					$redirectUrl = frameCsp::_()->getModule('options')->get('redirect');
					redirect($redirectUrl);
					break;
			}
			exit();
		}
		add_action('admin_bar_menu', array($this, 'addAdminBarNotice'), 999);
	}
	public function addAdminBarNotice($wp_admin_bar) {
		$wp_admin_bar->add_menu( array(
			'id'        => 'comingsoon',
			'parent'    => 'top-secondary',
			'title'     => langCsp::_('Coming Soon Mode is Enabled'),
			'href'      => uriCsp::_(array('baseUrl' => admin_url('admin.php'), 'page' => plugin_basename(frameCsp::_()->getModule('adminmenu')->getView()->getFile()))),
			'meta'      => array(
				'title'     => langCsp::_('Coming Soon Mode is Enabled'),
				'class'		=> (frameCsp::_()->getModule('options')->get('mode') == 'disable' ? 'cspHidden' : ''),
			),
		));
	}
}