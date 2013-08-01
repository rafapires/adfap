<div class="g-plus" 
	 data-width="<?php echo $this->optsModel->get('soc_gp_badge_width')?>" 
	 data-href="<?php echo (strpos($this->optsModel->get('soc_gp_badge_account'), 'plus.google.com') !== false ? $this->optsModel->get('soc_gp_badge_account') : 'https://plus.google.com/'. $this->optsModel->get('soc_gp_badge_account'))?>" 
	 data-rel="author" 
	 data-theme="<?php echo $this->optsModel->get('soc_gp_badge_color_scheme')?>"></div>
