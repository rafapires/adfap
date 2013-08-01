<?php

/**
 * Class for messenger module tab at options page
 */
class messengerViewCsp extends viewCsp {
	public function getOneEmailTplEditor($d = array()) {
		// For some cases we want to provide data from outside this method
		$tplData = isset($d['tplData']) ? $d['tplData'] : array();
		if(empty($tplData)) {
			$tplData = $this->getModel('email_templates')->get($d);
		}
		if(!empty($tplData)) {
			$this->assign('tplData', $tplData);
			return parent::getContent('oneEmailTplEditor');
		}
		return false;
	}
}
