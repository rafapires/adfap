<?php
class templateViewCsp extends viewCsp {
	protected $_styles = array();
	protected $_scripts = array();
	/**
	 * Provide or not html code of subscribe for to template. Can be re-defined for child classes
	 */
	protected $_useSubscribeForm = true;
	/**
	 * Provide or not html code of social icons for to template. Can be re-defined for child classes
	 */
	protected $_useSocIcons = true;
	public function getComingSoonPageHtml() {
		$this->_beforeShow();
		
		$this->assign('msgTitle', frameCsp::_()->getModule('options')->get('msg_title'));
		$this->assign('msgTitleColor', frameCsp::_()->getModule('options')->get('msg_title_color'));
		$this->assign('msgTitleFont', frameCsp::_()->getModule('options')->get('msg_title_font'));
		$msgTitleStyle = array();
		if(!empty($this->msgTitleColor))
			$msgTitleStyle['color'] = $this->msgTitleColor;
		if(!empty($this->msgTitleFont)) {
			$msgTitleStyle['font-family'] = $this->msgTitleFont;
			$this->_styles[] = 'http://fonts.googleapis.com/css?family='. $this->msgTitleFont. '&subset=latin,cyrillic-ext';
		}
		$this->assign('msgTitleStyle', utilsCsp::arrToCss( $msgTitleStyle ));
		
		$this->assign('msgText', frameCsp::_()->getModule('options')->get('msg_text'));
		$this->assign('msgTextColor', frameCsp::_()->getModule('options')->get('msg_text_color'));
		$this->assign('msgTextFont', frameCsp::_()->getModule('options')->get('msg_text_font'));
		$msgTextStyle = array();
		if(!empty($this->msgTextColor))
			$msgTextStyle['color'] = $this->msgTextColor;
		if(!empty($this->msgTextFont)) {
			$msgTextStyle['font-family'] = $this->msgTextFont;
			if($this->msgTitleFont != $this->msgTextFont)
				$this->_styles[] = 'http://fonts.googleapis.com/css?family='. $this->msgTextFont. '&subset=latin,cyrillic-ext';
		}
		$this->assign('msgTextStyle', utilsCsp::arrToCss( $msgTextStyle ));
		
		if($this->_useSubscribeForm && frameCsp::_()->getModule('options')->get('sub_enable')) {
			$this->_scripts[] = frameCsp::_()->getModule('subscribe')->getModPath(). 'js/frontend.subscribe.js';
			$this->assign('subscribeForm', frameCsp::_()->getModule('subscribe')->getController()->getView()->getUserForm());
		}
		if($this->_useSocIcons) {
			$this->assign('socIcons', frameCsp::_()->getModule('social_icons')->getController()->getView()->getFrontendContent());
		}
		
		if(file_exists($this->getModule()->getModDir(). 'css/style.css'))
			$this->_styles[] = $this->getModule()->getModPath(). 'css/style.css';
		
		$this->assign('logoPath', $this->getModule()->getLogoImgPath());
		$this->assign('bgCssAttrs', dispatcherCsp::applyFilters('tplBgCssAttrs', $this->getModule()->getBgCssAttrs()));
		$this->assign('styles', dispatcherCsp::applyFilters('tplStyles', $this->_styles));
		$this->assign('scripts', dispatcherCsp::applyFilters('tplScripts', $this->_scripts));
		$this->assign('initJsVars', dispatcherCsp::applyFilters('tplInitJsVars', $this->initJsVars()));
		$this->assign('messages', frameCsp::_()->getRes()->getMessages());
		$this->assign('errors', frameCsp::_()->getRes()->getErrors());
		return parent::getContent($this->getCode(). 'CSPHtml');
	}
	public function addScript($path) {
		if(!in_array($path, $this->_scripts))
			$this->_scripts[] = $path;
	}
	public function addStyle($path) {
		if(!in_array($path, $this->_styles))
			$this->_styles[] = $path;
	}
	public function initJsVars() {
		$ajaxurl = admin_url('admin-ajax.php');
		if(frameCsp::_()->getModule('options')->get('ssl_on_ajax')) {
			$ajaxurl = uriCsp::makeHttps($ajaxurl);
		}
		$jsData = array(
			'siteUrl'					=> CSP_SITE_URL,
			'imgPath'					=> CSP_IMG_PATH,
			'loader'					=> CSP_LOADER_IMG, 
			'close'						=> CSP_IMG_PATH. 'cross.gif', 
			'ajaxurl'					=> $ajaxurl,
			'animationSpeed'			=> frameCsp::_()->getModule('options')->get('js_animation_speed'),
			'CSP_CODE'					=> CSP_CODE,
		);
		return '<script type="text/javascript">
		// <!--
			var CSP_DATA = '. utilsCsp::jsonEncode($jsData). ';
		// -->
		</script>';
	}
	protected function _beforeShow() {
		
	}
}