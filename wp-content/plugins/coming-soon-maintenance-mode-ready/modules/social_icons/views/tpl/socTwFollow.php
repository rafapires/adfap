<a href="<?php echo (strpos($this->optsModel->get('soc_tw_follow_account'), 'http') === 0 ? $this->optsModel->get('soc_tw_follow_account') : 'https://twitter.com/'. $this->optsModel->get('soc_tw_follow_account'))?>" 
   class="twitter-follow-button" 
   data-show-count="<?php echo ($this->optsModel->isEmpty('soc_tw_follow_count') ? 'false' : 'true')?>" 
   data-size="<?php echo $this->optsModel->get('soc_tw_follow_size')?>"
   data-lang="<?php echo $this->langIso2Code?>"
   data-show-screen-name="<?php echo ($this->optsModel->isEmpty('soc_tw_follow_show_name') ? 'false' : 'true')?>">Follow @<?php echo $this->optsModel->get('soc_tw_follow_account')?></a>