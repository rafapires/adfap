<?php
class installerCsp {
	static public $update_to_version_method = '';
	static public function init() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		//$start = microtime(true);					// Speed debug info
		//$queriesCountStart = $wpdb->num_queries;	// Speed debug info
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$current_version = get_option(CSP_DB_PREF. 'db_version', 0);
		$installed = (int) get_option(CSP_DB_PREF. 'db_installed', 0);
		/**
		 * htmltype 
		 */
		if (!dbCsp::exist($wpPrefix.CSP_DB_PREF."htmltype")) {
			$q = "CREATE TABLE IF NOT EXISTS `".$wpPrefix.CSP_DB_PREF."htmltype` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(32) NOT NULL,
			  `description` varchar(255) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE INDEX `label` (`label`)
			) DEFAULT CHARSET=utf8";
			dbDelta($q);
		}
		dbCsp::query("INSERT INTO `".$wpPrefix.CSP_DB_PREF."htmltype` VALUES
			(1, 'text', 'Text'),
			(2, 'password', 'Password'),
			(3, 'hidden', 'Hidden'),
			(4, 'checkbox', 'Checkbox'),
			(5, 'checkboxlist', 'Checkboxes'),
			(6, 'datepicker', 'Date Picker'),
			(7, 'submit', 'Button'),
			(8, 'img', 'Image'),
			(9, 'selectbox', 'Drop Down'),
			(10, 'radiobuttons', 'Radio Buttons'),
			(11, 'countryList', 'Countries List'),
			(12, 'selectlist', 'List'),
			(13, 'countryListMultiple', 'Country List with posibility to select multiple countries'),
			(14, 'block', 'Will show only value as text'),
			(15, 'statesList', 'States List'),
			(16, 'textFieldsDynamicTable', 'Dynamic table - multiple text options set'),
			(17, 'textarea', 'Textarea'),
			(18, 'checkboxHiddenVal', 'Checkbox with Hidden field')");
		/**
		 * modules 
		 */
		if (!dbCsp::exist($wpPrefix.CSP_DB_PREF."modules")) {
			$q = "CREATE TABLE IF NOT EXISTS `".$wpPrefix.CSP_DB_PREF."modules` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) NOT NULL,
			  `active` tinyint(1) NOT NULL DEFAULT '0',
			  `type_id` smallint(3) NOT NULL DEFAULT '0',
			  `params` text,
			  `has_tab` tinyint(1) NOT NULL DEFAULT '0',
			  `label` varchar(128) DEFAULT NULL,
			  `description` text,
			  `ex_plug_dir` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE INDEX `code` (`code`)
			) DEFAULT CHARSET=utf8;";
			dbDelta($q);
		}
		dbCsp::query("INSERT INTO `".$wpPrefix.CSP_DB_PREF."modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
		  (NULL, 'adminmenu',1,1,'',0,'Admin Menu',''),
		  (NULL, 'options',1,1,'',1,'Options',''),
		  (NULL, 'user',1,1,'',1,'Users',''),
		  (NULL, 'pages',1,1,'". json_encode(array()). "',0,'Pages',''),
		  (NULL, 'templates',1,1,'',1,'Templates for Plugin',''),
		  (NULL, 'messenger', 1, 1, '', 1, 'Notifications', 'Module provides the ability to create templates for user notifications and for mass mailing.'),
		  (NULL, 'shortcodes', 1, 6, '', 0, 'Shortcodes', 'Shortcodes data'),
		  (NULL, 'twitter_widget', 1, 4, '', 0, 'Twitter Widget', 'Twitter Widget'),
		  (NULL, 'log', 1, 1, '', 0, 'Log', 'Internal system module to log some actions on server'),
		  (NULL, 'coming_soon', 1, 1, '', 0, 'Coming soon', 'Coming soon'),
		  (NULL, 'csp_tpl_standard', 1, 7, '', 0, 'Standard Coming Soon template', 'Coming soon'),
		  (NULL, 'subscribe', 1, 1, '', 0, 'Subscribe', 'Subscribe'),
		  (NULL, 'social_icons', 1, 1, '', 0, 'Social Icons', 'Social Icons');");

		if(!$installed) {
			self::createPages();
		}

		/**
		 *  modules_type 
		 */
		if(!dbCsp::exist($wpPrefix.CSP_DB_PREF."modules_type")) {
			$q = "CREATE TABLE IF NOT EXISTS `".$wpPrefix.CSP_DB_PREF."modules_type` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(64) NOT NULL,
			  PRIMARY KEY (`id`)
			) AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;";
			dbDelta($q);
		}
		dbCsp::query("INSERT INTO `".$wpPrefix.CSP_DB_PREF."modules_type` VALUES
		  (1,'system'),
		  (2,'payment'),
		  (3,'shipping'),
		  (4,'widget'),
		  (5,'product_extra'),
		  (6,'addons'),
		  (7,'template')");
		/**
		 * options 
		 */
		if(!dbCsp::exist($wpPrefix.CSP_DB_PREF."options")) {
			$q = "CREATE TABLE IF NOT EXISTS `".$wpPrefix.CSP_DB_PREF."options` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) CHARACTER SET latin1 NOT NULL,
			  `value` text NULL,
			  `label` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
			  `description` text CHARACTER SET latin1,
			  `htmltype_id` smallint(2) NOT NULL DEFAULT '1',
			  `params` text NULL,
			  `cat_id` mediumint(3) DEFAULT '0',
			  `sort_order` mediumint(3) DEFAULT '0',
			  `value_type` varchar(16) CHARACTER SET latin1 DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `id` (`id`),
			  UNIQUE INDEX `code` (`code`)
			) DEFAULT CHARSET=utf8";
			dbDelta($q);
		}
		$eol = "\n";
		$msgText = 'We apologize, but at this time our site does not work. But we promise you, very soon we will resume work. '. $eol. 'We just want to improve our site for your comfort.Be among the first to see our new website! Just send your email using the form below and we will inform you.';
		dbCsp::query("INSERT INTO `".$wpPrefix.CSP_DB_PREF."options` (`id`,`code`,`value`,`label`,`description`,`htmltype_id`,`params`,`cat_id`,`sort_order`) VALUES
			(NULL,'mode','disable','Plugin Mode','Mode for Coming Soon Plugin',9,'". utilsCsp::serialize(array('options' => array('disable' => 'Disable', 'coming_soon' => 'Coming Soon Mode', 'maint_mode' => 'Maintenance Mode-Under Construction (HTTP 503)', 'redirect' => 'Redirect 301'))). "',1,0),
			(NULL,'template','csp_tpl_standard','Template','Your page Template',14,'',1,0),
			(NULL,'redirect','','Redirect URL','Redirect URL',1,'',1,0),
			(NULL,'sub_notif_end_maint','0','Notify Subscribers','Notify Subscribers that your site go live',4,'',1,0),
			
			(NULL,'bg_type','color','Bg Type','Bg Type',10,'',2,0),
			(NULL,'bg_color','#ffffff','Bg Color','Bg Color',1,'',2,0),
			(NULL,'bg_image','','Bg Image','Bg Image',1,'',2,0),
			(NULL,'bg_img_show_type','center','Bg Image show type','Bg Image show type',10,'',2,0),
			(NULL,'logo_image','','Logo image','Logo image',1,'',2,0),
			(NULL,'msg_title','Website is Under Construction','Message Title','Message Title',1,'',2,0),
			(NULL,'msg_title_color','#000000','Message Title Color','Message Title Color',1,'',2,0),
			(NULL,'msg_title_font','','Message Title Font','Message Title Font',1,'',2,0),
			(NULL,'msg_text','". $msgText. "','Message Text','Message Text',1,'',2,0),
			(NULL,'msg_text_color','#000000','Message Text Color','Message Text Color',1,'',2,0),
			(NULL,'msg_text_font','','Message Text Font','Message Text Font',1,'',2,0),
			
			(NULL,'sub_enable','1','Enable Subscribe','Enable Subscribe',1,'',3,0),
			(NULL,'sub_admin_email','','New Subscribe notification email','New Subscribe notification email',1,'',3,0),
			
			(NULL,'soc_facebook_enable_share','','Facebook enable share','Facebook enable share',18,'',4,0),
			(NULL,'soc_facebook_enable_like','','Facebook enable like','Facebook enable like',18,'',4,0),
			(NULL,'soc_facebook_enable_send','1','Facebook enable send','Facebook enable send',18,'',4,0),
			(NULL,'soc_facebook_like_layout','standard','Facebook like layout','Facebook like layout',9,'',4,0),
			(NULL,'soc_facebook_like_width','450','Facebook like width','Facebook like width',1,'',4,0),
			(NULL,'soc_facebook_like_faces','1','Facebook like faces','Facebook like faces',18,'',4,0),
			(NULL,'soc_facebook_like_font','verdana','Facebook like font','Facebook like font',9,'',4,0),
			(NULL,'soc_facebook_like_color_scheme','light','Facebook like color','Facebook like color',9,'',4,0),
			(NULL,'soc_facebook_like_verb','like','Facebook like verb','Facebook like verb',9,'',4,0),
			(NULL,'soc_facebook_share_layout','box_count','Facebook share layout','Facebook share layout',9,'',4,0),
			(NULL,'soc_facebook_enable_follow','','Facebook follow enable','Facebook follow enable',18,'',4,0),
			(NULL,'soc_facebook_follow_profile','','Facebook follow profile','Facebook follow profile',1,'',4,0),
			(NULL,'soc_facebook_follow_layout','standard','Facebook follow layout','Facebook follow layout',9,'',4,0),
			(NULL,'soc_facebook_follow_faces','1','Facebook follow faces','Facebook follow faces',18,'',4,0),
			(NULL,'soc_facebook_follow_color_scheme','light','Facebook follow color scheme','Facebook follow color scheme',9,'',4,0),
			(NULL,'soc_facebook_follow_font','verdana','Facebook follow font','Facebook follow font',9,'',4,0),
			(NULL,'soc_facebook_follow_width','450','Facebook follow width','Facebook follow width',1,'',4,0),
			(NULL,'soc_facebook_enable_link','1','Facebook link enable','Facebook link enable',18,'',4,0),
			(NULL,'soc_facebook_link_account','','Facebook link enable','Facebook link enable',18,'',4,0),
			
			(NULL,'soc_tw_enable_tweet','','Twitter','Twitter',1,'',18,0),
			(NULL,'soc_tw_enable_follow','','Twitter','Twitter',1,'',18,0),
			(NULL,'soc_tw_tweet_count','none','Twitter','Twitter',1,'',18,0),
			(NULL,'soc_tw_tweet_size','medium','Twitter','Twitter',1,'',18,0),
			(NULL,'soc_tw_follow_account','','Twitter','Twitter',1,'',18,0),
			(NULL,'soc_tw_follow_count','1','Twitter','Twitter',1,'',18,0),
			(NULL,'soc_tw_follow_size','medium','Twitter','Twitter',1,'',18,0),
			(NULL,'soc_tw_follow_show_name','','Twitter','Twitter',1,'',18,0),
			(NULL,'soc_tw_enable_link','1','Facebook link enable','Facebook link enable',18,'',4,0),
			(NULL,'soc_tw_link_account','','Facebook link enable','Facebook link enable',18,'',4,0),
			
			(NULL,'soc_gp_enable_badge','','Google+','Google+',1,'',18,0),
			(NULL,'soc_gp_enable_like','','Google+','Google+',1,'',18,0),
			(NULL,'soc_gp_badge_account','','Google+','Google+',1,'',18,0),
			(NULL,'soc_gp_badge_width','300','Google+','Google+',1,'',18,0),
			(NULL,'soc_gp_badge_color_scheme','light','Google+','Google+',1,'',18,0),
			(NULL,'soc_gp_like_size','medium','Google+','Google+',1,'',18,0),
			(NULL,'soc_gp_like_annotation','inline','Google+','Google+',1,'',18,0),
			(NULL,'soc_gp_like_width','200','Google+','Google+',1,'',18,0),
			(NULL,'soc_gp_enable_link','1','Facebook link enable','Facebook link enable',18,'',4,0),
			(NULL,'soc_gp_link_account','','Facebook link enable','Facebook link enable',18,'',4,0);");

		/* options categories */
		if(!dbCsp::exist($wpPrefix.CSP_DB_PREF."options_categories")) {
			$q = "CREATE TABLE IF NOT EXISTS `".$wpPrefix.CSP_DB_PREF."options_categories` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(128) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `id` (`id`)
			) DEFAULT CHARSET=utf8";
			dbDelta($q);
		}
		dbCsp::query("INSERT INTO `".$wpPrefix.CSP_DB_PREF."options_categories` VALUES
			(1, 'General'),
			(2, 'Template'),
			(3, 'Subscribe'),
			(4, 'Social');");
		/**
		 * Email Templates
		 */
		if(!dbCsp::exist($wpPrefix.CSP_DB_PREF."email_templates")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.CSP_DB_PREF."email_templates` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `label` varchar(128) NOT NULL,
				  `subject` varchar(255) NOT NULL,
				  `body` text NOT NULL,
				  `variables` text NOT NULL,
				  `active` tinyint(1) NOT NULL,
				  `name` varchar(128) NOT NULL,
				  `module` varchar(128) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE INDEX `name` (`name`)
				) DEFAULT CHARSET=utf8");
		}
		$eol = "\n\r";
		$emailTemplates = array(
			'sub_confirm' => array(
				'body' => 'Hello!'. $eol. 'Thank you for subscribing for :site_name!'. $eol. 'To complete your subscription please follow the link bellow:'. $eol. '<a href=":link">:link</a>'. $eol. 'Regards,'. $eol. ':site_name team.',
				'variables' => array('site_name', 'link'),
			),
			'sub_admin_notify' => array(
				'body' => 'Hello!'. $eol. 'New user activated subscription on your site :site_name for email :email.',
				'variables' => array('site_name', 'email'),
			),
			'sub_site_opened' => array(
				'body' => 'Hello!'. $eol. 'Please be advised that site :site_name are opened from now!'. $eol. 'You can visit site following this link <a href=":site_link">:site_link</a>.'. $eol. 'Regards,'. $eol. ':site_name team.',
				'variables' => array('site_name', 'site_link'),
			),
			'sub_new_post' => array(
				'body' => 'Hello!'. $eol. 'New entry was published on :site_name.'. $eol. 'Visit it by following next link:'. $eol. '<a href=":post_link">:post_title</a>'. $eol. 'Regards,'. $eol. ':site_name team.',
				'variables' => array('site_name', 'post_link', 'post_title'),
			),
		);
		dbCsp::query("INSERT INTO `".$wpPrefix.CSP_DB_PREF."email_templates` (`id`, `label`, `subject`, `body`, `variables`, `active`, `name`, `module`) VALUES 
			(NULL, 'Subscribe Confirm', 'Subscribe Confirmation', '". $emailTemplates['sub_confirm']['body']. "', '[\"". implode('","', $emailTemplates['sub_confirm']['variables'])."\"]', 1, 'sub_confirm', 'subscribe'),
			(NULL, 'Subscribe Admin Notify', 'New subscriber', '". $emailTemplates['sub_admin_notify']['body']. "', '[\"". implode('","', $emailTemplates['sub_admin_notify']['variables'])."\"]', 1, 'sub_admin_notify', 'subscribe'),
			(NULL, 'Subscribe Site Opened', 'Site :site_name Opened!', '". $emailTemplates['sub_site_opened']['body']. "', '[\"". implode('","', $emailTemplates['sub_site_opened']['variables'])."\"]', 1, 'sub_site_opened', 'subscribe'),
			(NULL, 'Subscribe New Entry', ':site_name - New Entry!', '". $emailTemplates['sub_new_post']['body']. "', '[\"". implode('","', $emailTemplates['sub_new_post']['variables'])."\"]', 1, 'sub_new_post', 'subscribe');");
		/**
		 * Subscribers
		 */
		if(!dbCsp::exist($wpPrefix.CSP_DB_PREF."subscribers")) {
			$q = "CREATE TABLE IF NOT EXISTS `".$wpPrefix.CSP_DB_PREF."subscribers` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `user_id` int(11) NOT NULL DEFAULT '0',
				  `email` varchar(255) NOT NULL,
				  `name` varchar(255) DEFAULT NULL,
				  `created` datetime NOT NULL,
				  `unsubscribe_date` datetime DEFAULT NULL,
				  `active` tinyint(4) NOT NULL DEFAULT '1',
				  `token` varchar(255) DEFAULT NULL,
				  `ip` varchar(64) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `user_id` (`user_id`)
				) DEFAULT CHARSET=utf8";
			dbDelta($q);
		}
		/**
		 * Log table - all logs in project
		 */
		if(!dbCsp::exist($wpPrefix.CSP_DB_PREF."log")) {
			dbDelta("CREATE TABLE `".$wpPrefix.CSP_DB_PREF."log` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `type` varchar(64) NOT NULL,
			  `data` text,
			  `date_created` int(11) NOT NULL DEFAULT '0',
			  `uid` int(11) NOT NULL DEFAULT 0,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8");
		}
		/**
		* Files
		*/
	   if(!dbCsp::exist($wpPrefix.CSP_DB_PREF."files")) {
		   dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.CSP_DB_PREF."files` (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			 `pid` int(11) NOT NULL,
			 `name` varchar(255) NOT NULL,
			 `path` varchar(255) NOT NULL,
			 `mime_type` varchar(255) DEFAULT NULL,
			 `size` int(11) NOT NULL DEFAULT '0',
			 `active` tinyint(1) NOT NULL,
			 `date` datetime DEFAULT NULL,
			 `download_limit` int(11) NOT NULL DEFAULT '0',
			 `period_limit` int(11) NOT NULL DEFAULT '0',
			 `description` text NOT NULL,
			 `type_id` SMALLINT(5) NOT NULL DEFAULT 1,
			 PRIMARY KEY (`id`)
		   ) DEFAULT CHARSET=utf8");
	   }

		installerDbUpdaterCsp::runUpdate();

		update_option(CSP_DB_PREF. 'db_version', CSP_VERSION);
		add_option(CSP_DB_PREF. 'db_installed', 1);
		dbCsp::query("UPDATE `".$wpPrefix.CSP_DB_PREF."options` SET value = '". CSP_VERSION. "' WHERE code = 'version' LIMIT 1");

		//$time = microtime(true) - $start;	// Speed debug info
	}
	/**
	 * Create pages for plugin usage
	 */
	static public function createPages() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		// for now
		return;
		$defaultPagesData = array(
			// array('page_id' => 0,	'mod' => 'digital_product',	'action' => 'getDownloadsList',				'showFor' => 'logged',	'title' => 'My Downloads',	'parentTitle' => 'My Account'),
		);
		$toePages = @json_decode(get_option($wpPrefix. 'pagesCsp'));
		if(empty($toePages) || !is_array($toePages)) {
			$toePages = array();
			foreach($defaultPagesData as $p) {
				$pageData = $p;
				if(isset($p['parentTitle']) && ($parentPage = self::_getPageByTitle($p['parentTitle'], $toePages)))
					$pageData['page_id'] = self::_addPageToWP($p['title'], $parentPage->page_id);
				else
					$pageData['page_id'] = self::_addPageToWP($p['title']);	
				$toePages[] = (object) $pageData;
			}
		} else {
			$existsTitles = array();
			foreach($toePages as $i => $p) {
				if(!isset($p->page_id)) continue;
				$existsTitles[] = $p->title;
				$page = get_page($p->page_id);
				if(empty($page)) {
					if(isset($p->parentTitle) && ($parentPage = self::_getPageByTitle($p->parentTitle, $toePages))) {
						$toePages[ $i ]->page_id = self::_addPageToWP($p->title, $parentPage->page_id);
					} else
						$toePages[ $i ]->page_id = self::_addPageToWP($p->title);	
				}
			}
			// Create new added after update pages
			if(count($existsTitles) != count($defaultPagesData)) {
				foreach($defaultPagesData as $p) {
					if(!in_array($p['title'], $existsTitles)) {
						$pageData = $p;
						if(isset($p['parentTitle']) && ($parentPage = self::_getPageByTitle($p['parentTitle'], $toePages)))
							$pageData['page_id'] = self::_addPageToWP($p['title'], $parentPage['page_id']);
						else
							$pageData['page_id'] = self::_addPageToWP($p['title']);	
						$toePages[] = (object) $pageData;
					}
				}
			}
		}
		dbCsp::query("UPDATE `".$wpPrefix.CSP_DB_PREF."modules` SET params = '". json_encode($toePages). "' WHERE code = 'pagesCsp' LIMIT 1");
		update_option($wpPrefix. 'pagesCsp', json_encode($toePages));
	}
	/**
	 * Return page data from given array, searched by title, used in self::createPages()
	 * @return mixed page data object if success, else - false
	 */
	static private function _getPageByTitle($title, $pageArr) {
		foreach($pageArr as $p) {
			if($p->title == $title)
				return $p;
		}
		return false;
	}
	static public function delete() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		$deleteOptions = reqCsp::getVar('deleteOptions');
		if(frameCsp::_()->getModule('pages')) {
			if(is_null($deleteOptions)) {
				frameCsp::_()->getModule('pages')->getView()->displayDeactivatePage();
				exit();
			}
			//Delete All pages, that was installed with plugin
			$pages = frameCsp::_()->getModule('pages');
			if(is_object($pages)) {
				$pages = $pages->getAll();
				if($pages) {
					foreach($pages as $p) {
						wp_delete_post($p->page_id, true);
					}
				}
			}
		}
		if((bool) $deleteOptions) {
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.CSP_DB_PREF."modules`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.CSP_DB_PREF."modules_type`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.CSP_DB_PREF."options`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.CSP_DB_PREF."htmltype`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.CSP_DB_PREF."templates`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.CSP_DB_PREF."email_templates`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.CSP_DB_PREF."subscribers`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.CSP_DB_PREF."files`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.CSP_DB_PREF."log`");
			$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.CSP_DB_PREF."options_categories`");
		}
		delete_option($wpPrefix. 'db_version');
		delete_option($wpPrefix. 'db_installed');
	}
	static protected function _addPageToWP($post_title, $post_parent = 0) {
		return wp_insert_post(array(
			 'post_title' => langCsp::_($post_title),
			 'post_content' => langCsp::_($post_title. ' Page Content'),
			 'post_status' => 'publish',
			 'post_type' => 'page',
			 'post_parent' => $post_parent,
			 'comment_status' => 'closed'
		));
	}
	static public function update() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		$currentVersion = get_option($wpPrefix. 'db_version', 0);
		$installed = (int) get_option($wpPrefix. 'db_installed', 0);
		if($installed && version_compare(CSP_VERSION, $currentVersion, '>')) {
			self::init();
			update_option($wpPrefix. 'db_version', CSP_VERSION);
		}
	}
}
