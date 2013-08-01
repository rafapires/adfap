<?php
class subscribeCsp extends moduleCsp {
   public function init() {
	   dispatcherCsp::addFilter('adminOptionsTabs', array($this, 'addOptionsTab'));
	   add_action('add_meta_boxes', array($this, 'addMetaBox'));
	   $postNonPublishStates = array(
		    'new',
			'pending',
			'draft',
			'auto-draft',
			'future',
			'private',
			'inherit',
			'trash');
	   // We will send messages only when moving post to published state
	   foreach($postNonPublishStates as $s) {
		   add_action($s. '_to_publish',	array($this, 'sendNewPostNotif'));
	   }
   }
   public function addOptionsTab($tabs) {
	   frameCsp::_()->addScript('adminSubscribeOptions', $this->getModPath(). 'js/admin.subscribe.options.js');
	   $tabs['cspSubscribeOptions'] = array(
		   'title' => 'Subscribers', 'content' => $this->getController()->getView()->getAdminOptions(),
	   );
	   return $tabs;
   }
   public function sendSiteOpenNotif() {
	   $this->getController()->getModel()->sendSiteOpenNotif();
   }
   public function addMetaBox() {
	   global $post;
	   if($post->post_status != 'publish' && frameCsp::_()->getModule('options')->get('sub_enable')) {
			add_meta_box('cspSubscribeMetaBox', langCsp::_('Coming Soon - Subscribe notifications'), array($this->getController()->getView(), 'getMetaBox'));
	   }
   }
   public function sendNewPostNotif($post) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if(!reqCsp::isEmpty('csp_sub_send_notif')) {
			$this->getController()->getModel()->sendNewPostNotif(array('post_id' => $post->ID));
		}
   }
}

