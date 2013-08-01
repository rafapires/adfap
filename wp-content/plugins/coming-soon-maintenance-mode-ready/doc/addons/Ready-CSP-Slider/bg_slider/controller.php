<?php
class bg_sliderControllerCsp extends controllerCsp {
	public function saveSlide() {
		$res = new responseCsp();
		if($this->getModel()->saveSlide(reqCsp::get('files'))) {
			$res->addData(array('slides' => frameCsp::_()->getModule('bg_slider')->getSlidesFullPath()));
			$res->addData(array('slidesNames' => frameCsp::_()->getModule('options')->get('slider_images')));
			$res->addMessage(langCsp::_('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function removeSlide() {
		$res = new responseCsp();
		if($this->getModel()->removeSlide(reqCsp::get('post'))) {
			$res->addMessage(langCsp::_('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	
	public function getPermissions() {
		return array(
			CSP_USERLEVELS => array(
				CSP_ADMIN => array('saveSlide', 'removeSlide')
			),
		);
	}
}