<?php
class bg_sliderViewCsp extends viewCsp {
	public function getAdminView() {
		$this->assign('cspSlides',	$this->getModule()->getSlidesFullPath());
		$this->assign('optModel',	frameCsp::_()->getModule('options')->getModel());
		return parent::getContent('bgSliderAdminOptions');
	}
	public function addSliderToTpl() {
		$this->assign('images',						$this->getModule()->getSlidesFullPath());
		$this->assign('slider_slide_interval',		frameCsp::_()->getModule('options')->get('slider_slide_interval'));
		$this->assign('slider_transition',			frameCsp::_()->getModule('options')->get('slider_transition'));
		$this->assign('slider_transition_speed',	frameCsp::_()->getModule('options')->get('slider_transition_speed'));
		parent::display('tplSlider');
	}
}