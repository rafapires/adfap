<?php
class pagesCsp extends moduleCsp {
    /**
     * Current Page data
     */
    protected $_current = null;
    
    public function __construct($d, $params = array()) {
        parent::__construct($d, $params);
        $this->_current = new stdClass();
    }
    public function init() {
        add_action('posts_results', array($this, 'initPage'));
        add_filter('wp_get_nav_menu_items', array($this, 'preMenuLinksShow'));
        add_filter('get_pages', array($this, 'preMenuLinksShow'), 0, 2);
        add_filter('wp_page_menu_args', array($this, 'parsePagesArgs'));
        add_filter('loop_end', array($this, 'checkPermissionsGlobal'));     //Global permissions check
		add_action('wp_head', array($this, 'checkSysMessages'));
		add_filter('page_link', array($this, 'overwriteProtocol'), 10, 3);			//Use https on pages that we want
        parent::init();
    }
    public function parsePagesArgs($args) {
        $args['toeUseExcludePages'] = true;
        return $args;
    }
    public function getCurrent() {
        return $this->_current;
    }
    public function initPage($posts) {
        if(count($posts) == 1) {
            if($posts[0]->post_type == 'page') {
                if($page = $this->getByID($posts[0]->ID)) {
                    $this->_current = $page;
                    frameCsp::_()->getModule($page->mod)->exec($page->action);
					remove_filter('the_content', 'wpautop');
                }
            }
        }
        return $posts;
    }
    public function getByID($id) {
        foreach($this->_params as $p) {
            if((int)$p->page_id == (int)$id)
                return $p;
        }
        return NULL;
    }
    public function getAll() {
        return $this->_params;
    }
    public function getLink($params = array('id' => 0, 'mod' => '', 'action' => '', 'data' => array())) {
        if($page = $this->getPage($params)) {
            $urlParams = array('page_id' => $page->page_id);
            if(!empty($params['data']) && is_array($params['data']))
                $urlParams = array_merge($urlParams, $params['data']);
            return uriCsp::_($urlParams);
        }
        return '';
    }
    public function getPage($params = array('id' => 0, 'mod' => '', 'action' => '')) {
        foreach($this->_params as $key => $p) {
            if(($p->mod == $params['mod'] && $p->action == $params['action']) ||
                $p->page_id == $params['id']) {

                return $p;
            }
        }
        return NULL;
    }
    public function preMenuLinksShow($items, $r = array()) {
        if(is_admin()) return $items;
        $idFieldName = 'ID';
        if(empty($r)) {
            $r['toeUseExcludePages'] = true;
            $idFieldName = 'ID';
        }
        if(isset($r['toeUseExcludePages']) && $r['toeUseExcludePages']) {
            $res = array();
            if(frameCsp::_()->getModule('user')->getCurrent()) 
                $for = 'logged';
            else
                $for = 'guest';
            $included = array();
            foreach($items as $item) {
                $plugPage = $this->getByID($item->$idFieldName);
                if(is_null($plugPage) /*&& !in_array($item->$idFieldName, $included)*/) {
                    $res[] = $item;
                    $included[] = $item->$idFieldName;
                }
                if(in_array($plugPage->showFor, array('all', $for)) && !@$plugPage->hideFromMenu && !in_array($item->$idFieldName, $included)) {
                    $res[] = $item;
                    $included[] = $item->$idFieldName;
                }
            }
            return $res;
        }
        return $items;
    }
    public function checkLogged($params = array('id' => 0, 'mod' => '', 'action' => '', 'data' => array())) {
        $isLogged = frameCsp::_()->getModule('user')->getCurrent();
        $currentPage = $this->getPage($params);
        if(is_object($currentPage)) {
			if($currentPage->showFor == 'logged' && !$isLogged) {
				redirect( 
					$this->getLink(array(
						'mod' => 'user', 
						'action' => 'getLoginForm', 
						'data' => array(
							'toeReturn' => $this->getLink($params),
						),
					)) 
				);
			}
        }
        return true;
    }
    public function checkPermissionsGlobal() {
        global $post;
        if(is_page($post)) {
            $currentPage = $this->getByID($post->ID);
            $this->checkLogged(array('mod' => $currentPage->mod, 'action' => $currentPage->action));
        }
    }
    /**
     * Check if current page is page describet using mod and action params in $check variable
     * @see installerCsp::init() - $pages variable
     */
    public function is($check = array()) {
        $currentPage = $this->getCurrent();
        if(is_object($currentPage) && isset($currentPage->page_id)) {
            if(isset($check['mod']) && isset($check['action']) && $check['mod'] == $currentPage->mod && $check['action'] == $currentPage->action) {
                return true;
            }
        }
        return false;
    }
    /**
     * Check if current page is Login page
     */
    public function isLogin() {
		return basename($_SERVER['SCRIPT_NAME']) == 'wp-login.php';
    }

	/**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
        $tab = new tabCsp(langCsp::_('Pages'), $this->getCode());
        $tab->setView('pagesTab');
		$tab->setSortOrder(2);
		$tab->setParent('templatesCsp');
		$tab->setNestingLevel(1);
        $tabs[] = $tab;
        return $tabs;
    }
	/**
	 * Show messages at the top of the page. This is kinda weird way to do this, 
	 * but WP gives me no other way to do this - no action or hook right after body tag opening or somwere around this place
	 */
	public function checkSysMessages() {
		$messages = array();
		if(is_404() && frameCsp::_()->getModule('user')->isAdmin()) {
			$messages['adminAlerts'][] = langCsp::_(array(
				'If you are trying to view your product and see this message - maybe you have some troubles with permalinks settings.',
				'Try to go to <a href="'. get_admin_url(). 'options-permalink.php'. '">Admin panel -> Settings -> Permalinks</a> and re-saive this settings (just click on "Save Changes").<br />',
				'<a href="http://readyshoppingcart.com/faq/ecommerce-plugin-alerts">',
				'Please check FAQ.',
				'</a>',
				
			));
		}
		if(!empty($messages)) {
			$this->getView()->assign('forAdminOnly', langCsp::_('This message will be visible for admin only.'));
			$this->getView()->assign('messages', $messages);
			$this->getView()->display('pagesSystemMessages');
		}
	}
	public function overwriteProtocol($link, $id, $sample) {
		static $pagesCache;
		$makeHttpsReplace = false;
		if(frameCsp::_()->getModule('options')->get('ssl_on_checkout') || frameCsp::_()->getModule('options')->get('ssl_on_account')) {
			if(!isset($pagesCache[ $id ])) {
				$pageParams = $this->getByID($id);
				if($pageParams == NULL)
					$pagesCache[ $id ] = false;
				else 
					$pagesCache[ $id ] = $pageParams;
			}
			if($pagesCache[ $id ] && is_object($pagesCache[ $id ])) {
				if(
					(frameCsp::_()->getModule('options')->get('ssl_on_checkout')
					&& (($pagesCache[ $id ]->mod == 'checkout') 
						|| ($pagesCache[ $id ]->mod == 'user' && in_array($pagesCache[ $id ]->action, array('getShoppingCart')))))
					|| (frameCsp::_()->getModule('options')->get('ssl_on_account')
					&& (($pagesCache[ $id ]->mod == 'user' && in_array($pagesCache[ $id ]->action, array('getLoginForm', 'getRegisterForm', 'getAccountSummaryHtml', 'getProfileHtml', 'getOrdersList')))
						|| ($pagesCache[ $id ]->mod == 'digital_product' && in_array($pagesCache[ $id ]->action, array('getDownloadsList')))))
				) {
					$makeHttpsReplace = true;
				}
			}
		}
		if($makeHttpsReplace) {
			$link = uriCsp::makeHttps($link);
		}
		return $link;
	}
}

