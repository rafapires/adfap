<div class="fb-like" 
	 data-href="<?php echo $this->currentUrl?>" 
	 data-send="<?php echo ($this->optsModel->isEmpty('soc_facebook_enable_send') ? 'false' : 'true')?>" 
	 data-width="<?php echo $this->optsModel->get('soc_facebook_like_width')?>" 
	 data-show-faces="<?php echo ($this->optsModel->isEmpty('soc_facebook_like_faces') ? 'false' : 'true')?>" 
	 data-font="<?php echo $this->optsModel->get('soc_facebook_like_font')?>"
	 data-colorscheme="<?php echo $this->optsModel->get('soc_facebook_like_color_scheme')?>"
	 data-layout="<?php echo $this->optsModel->get('soc_facebook_like_layout')?>"
	 data-action="<?php echo $this->optsModel->get('soc_facebook_like_verb')?>"></div>

