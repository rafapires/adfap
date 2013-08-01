<?php
class bg_sliderModelCsp extends modelCsp {
	public function saveSlide($d = array()) {
		if(!empty($d) && isset($d['slide_img']) && !empty($d['slide_img'])) {
			$uploader = toeCreateObjCsp('fileuploader', array());
			if($uploader->validate('slide_img', $this->getModule()->getSlidesImgDir()) && $uploader->upload()) {
				$fileInfo = $uploader->getFileInfo();
				// Save info for this option
				$slides = frameCsp::_()->getModule('options')->get('slider_images');
				$slides[] = $fileInfo['path'];
				frameCsp::_()->getModule('options')->getController()->getModel()->save(array('code' => 'slider_images', 'opt_values' => array('slider_images' => $slides)));
				return true;
			} else
				 $this->pushError( $uploader->getError() );
		} else
			$this->pushError(langCsp::_('Empty data to setup'));
		return false;
	}
	public function removeSlide($d = array()) {
		if(!empty($d) && isset($d['imgCode']) && !empty($d['imgCode'])) {
			$slides = frameCsp::_()->getModule('options')->get('slider_images');
			$newSlidesArray = array();
			$pathToFile = '';
			foreach($slides as $s) {
				if(trim($s) == trim($d['imgCode'])) {
					$pathToFile = $this->getModule()->getSlideFullDir($d['imgCode']);
				} else {
					$newSlidesArray[] = $s;
				}
			}
			if(!empty($pathToFile)) {
				// Remove file
				utilsCsp::deleteFile( $pathToFile );
				// Save new array to database
				frameCsp::_()->getModule('options')->getController()->getModel()->save(array('code' => 'slider_images', 'opt_values' => array('slider_images' => $newSlidesArray)));
				return true;
			} else
				$this->pushError(langCsp::_('No such slide were found'));
		} else
			$this->pushError(langCsp::_('Empty data to delete slide'));
		return false;
	}
}