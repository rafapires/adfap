<?php
class bg_sliderCsp extends moduleCsp {
	protected $_slidesSubDir = 'slides';
	public function init() {
		parent::init();
		dispatcherCsp::addFilter('adminTemplateOptions', array($this, 'addAdminOptions'));
		if(frameCsp::_()->getModule('options')->get('slider_enabled')) {
			$template = frameCsp::_()->getModule('options')->get('template');
			if(!empty($template) && frameCsp::_()->getModule($template)) {
				dispatcherCsp::addFilter('tplScripts', array($this, 'addSliderScripts'));
				dispatcherCsp::addFilter('tplStyles', array($this, 'addSliderStyles'));
				dispatcherCsp::addFilter('tplBgCssAttrs', array($this, 'clearBgCssAttrs'));
				
				dispatcherCsp::addAction('tplBodyBegin', array($this->getController()->getView(), 'addSliderToTpl'));
			}
		}
	}
	public function addSliderScripts($scripts) {
		if(!frameCsp::_()->getModule('options')->isEmpty('slider_images')) {
			$scripts[] = $this->getModPath(). 'js/supersized.core.3.2.1.min.js';
			$scripts[] = $this->getModPath(). 'js/supersized.3.2.7.min.js';
		}
		return $scripts;
	}
	public function clearBgCssAttrs($cssAttrs) {
		// Clear bg css attrs if slides exists - we will set slider instead
		return frameCsp::_()->getModule('options')->isEmpty('slider_images') ? $cssAttrs : '';
	}
	public function addSliderStyles($styles) {
		if(!frameCsp::_()->getModule('options')->isEmpty('slider_images')) {
			$styles[] = $this->getModPath(). 'css/supersized.css';
		}
		return $styles;
	}
	public function getSlidesImgDir() {
		return frameCsp::_()->getModule('options')->getUploadDir(). DS. $this->_slidesSubDir;
	}
	public function getSlideFullPath($slide) {
		return utilsCsp::getUploadsPath(). '/'. frameCsp::_()->getModule('options')->getUploadDir(). '/'. $this->_slidesSubDir. '/'. $slide;
	}
	public function getSlideFullDir($slide) {
		return utilsCsp::getUploadsDir(). DS. frameCsp::_()->getModule('options')->getUploadDir(). DS. $this->_slidesSubDir. DS. $slide;
	}
	public function getSlidesFullPath() {
		$res = array();
		foreach(frameCsp::_()->getModule('options')->get('slider_images') as $slide) {
			$res[] = $this->getSlideFullPath($slide);
		}
		return $res;
	}
	public function install() {
		parent::install();
		frameCsp::_()->getTable('options')->insert(array(
			'code' => 'slider_enabled',
			'value' => '1',
			'label' => langCsp::_('Slider Enabled'),
			'cat_id' => 2,
		));
		frameCsp::_()->getTable('options')->insert(array(
			'code' => 'slider_images',
			'label' => langCsp::_('Slider Images'),
			'cat_id' => 2,
			'value_type' => 'array',
		));
		frameCsp::_()->getTable('options')->insert(array(
			'code' => 'slider_slide_interval',
			'value' => '5000',
			'label' => langCsp::_('Slider Interval'),
			'cat_id' => 2,
		));
		frameCsp::_()->getTable('options')->insert(array(
			'code' => 'slider_transition',
			'value' => 'fade',
			'label' => langCsp::_('Controls which effect is used to transition between slides'),
			'cat_id' => 2,
		));
		frameCsp::_()->getTable('options')->insert(array(
			'code' => 'slider_transition_speed',
			'value' => '750',
			'label' => langCsp::_('Speed of transitions in milliseconds'),
			'cat_id' => 2,
		));
	}
	public function addAdminOptions($tplOptsData) {
		frameCsp::_()->addScript('adminBgSliderCsp', $this->getModPath(). 'js/admin.slider.options.js');
		frameCsp::_()->addScript('jquery-ui-sortable', '', array('jquery'));
		$tplOptsData['bg_slider'] = array('title' => 'Full screen slideshow',	'content' => $this->getController()->getView()->getAdminView());
		return $tplOptsData;
	}
}

