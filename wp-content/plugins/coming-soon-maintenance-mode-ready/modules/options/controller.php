<?php
class optionsControllerCsp extends controllerCsp {
    public function save() {
		$res = new responseCsp();
		if($this->getModel()->save(reqCsp::get('post'))) {
			$res->addMessage(langCsp::_('Done'));
			if(reqCsp::getVar('code') == 'template') {
				$plTemplate = $this->getModel()->get('template');		// Current plugin template
				$tplName = ($plTemplate && frameCsp::_()->getModule($plTemplate)) ? frameCsp::_()->getModule($plTemplate)->getLabel() : '';
				$res->addData('new_name', $tplName);
			}
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
    }
	public function saveGroup() {
		$res = new responseCsp();
		if($this->getModel()->saveGroup(reqCsp::get('post'))) {
			$res->addMessage(langCsp::_('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function saveBgImg() {
		$res = new responseCsp();
		if($this->getModel()->saveBgImg(reqCsp::get('files'))) {
			$res->addData(array('imgPath' => frameCsp::_()->getModule('options')->getBgImgFullPath()));
			$res->addMessage(langCsp::_('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function saveLogoImg() {
		$res = new responseCsp();
		if($this->getModel()->saveLogoImg(reqCsp::get('files'))) {
			$res->addData(array('imgPath' => frameCsp::_()->getModule('options')->getLogoImgFullPath()));
			$res->addMessage(langCsp::_('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	/**
	 * Will save main options and detect - whether or not csp mode enabled/disabled to trigger appropriate actions
	 */
	public function saveMainGroup() {
		$res = new responseCsp();
		$oldMode = $this->getModel()->get('mode');
		if($this->getModel()->saveGroup(reqCsp::get('post'))) {
			$res->addMessage(langCsp::_('Done'));
			$newMode = $this->getModel()->get('mode');
			if($oldMode != $newMode && $newMode == 'disable' && $this->getModel()->get('sub_notif_end_maint')) {
				// Notify all subscribers that site was opened
				frameCsp::_()->getModule('subscribe')->sendSiteOpenNotif();
			}
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	/**
	 * Will save subscriptions options as usual options + try to re-saive email templates from this part
	 */
	public function saveSubscriptionGroup() {
		$res = new responseCsp();
		if($this->getModel()->saveGroup(reqCsp::get('post'))) {
			$res->addMessage(langCsp::_('Done'));
			$emailTplData = reqCsp::getVar('email_tpl');
			if(!empty($emailTplData) && is_array($emailTplData)) {
				foreach($emailTplData as $id => $tData) {
					frameCsp::_()->getModule('messenger')->getController()->getModel('email_templates')->save(array(
						'id'		=> $id, 
						'subject'	=> $tData['subject'],
						'body'		=> $tData['body'],
					));
				}
			}
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function setTplDefault() {
		$res = new responseCsp();
		$code = reqCsp::getVar('code');
		switch($code) {
			case 'bg_image':
				$newOptValue = $this->getModel()->setBgImgDefault(reqCsp::get('post'));
				break;
			case 'logo_image':
				$newOptValue = $this->getModel()->setLogoDefault(reqCsp::get('post'));
				break;
			case 'msg_title_params':
				$newOptValue = $this->getModel()->setTitleParamsDefault(reqCsp::get('post'));
				break;
			case 'msg_text_params':
				$newOptValue = $this->getModel()->setTextParamsDefault(reqCsp::get('post'));
				break;
			default:
				$newOptValue = $this->getModel()->setTplDefault(reqCsp::get('post'));
				break;
		}
		if($newOptValue !== false) {
			$res->addMessage(langCsp::_('Done'));
			$res->addData('newOptValue', $newOptValue);
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function removeBgImg() {
		$res = new responseCsp();
		if($this->getModel()->removeBgImg(reqCsp::get('post'))) {
			$res->addMessage(langCsp::_('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function removeLogoImg() {
		$res = new responseCsp();
		if($this->getModel()->removeLogoImg(reqCsp::get('post'))) {
			$res->addMessage(langCsp::_('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			CSP_USERLEVELS => array(
				CSP_ADMIN => array('save', 'saveGroup', 'saveBgImg', 'saveLogoImg', 'saveMainGroup', 'saveSubscriptionGroup', 'setTplDefault', 'removeBgImg', 'removeLogoImg')
			),
		);
	}
}

