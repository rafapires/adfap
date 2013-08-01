<?php
class csp_tpl_mercuryViewCsp extends templateViewCsp {
    protected function _beforeShow() {
        // adding JS script
        $this->addScript($this->getModule()->getModPath(). 'js/init.js');
        $this->addScript($this->getModule()->getModPath(). 'js/modernizr.-2.6.2.custom.min.js');
        
        // adding styles
        $this->addStyle($this->getModule()->getModPath(). 'css/media-queries.css');
        $this->addStyle($this->getModule()->getModPath(). 'css/colors/anycolor.css');
    }
}