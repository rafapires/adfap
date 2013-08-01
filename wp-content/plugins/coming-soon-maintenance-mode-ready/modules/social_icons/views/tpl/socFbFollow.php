<div class="fb-follow" 
	 data-href="<?php echo (strpos($this->optsModel->get('soc_facebook_follow_profile'), 'http') === 0 ? $this->optsModel->get('soc_facebook_follow_profile') : 'https://www.facebook.com/'. $this->optsModel->get('soc_facebook_follow_profile'))?>" 
	 data-show-faces="<?php echo ($this->optsModel->isEmpty('soc_facebook_follow_faces') ? 'false' : 'true')?>" 
	 data-colorscheme="<?php echo $this->optsModel->get('soc_facebook_follow_color_scheme')?>" 
	 data-font="<?php echo $this->optsModel->get('soc_facebook_follow_font')?>" 
	 data-layout="<?php echo $this->optsModel->get('soc_facebook_follow_layout')?>"
	 data-width="<?php echo $this->optsModel->get('soc_facebook_follow_width')?>"></div>