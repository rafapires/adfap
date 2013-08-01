<?php
class csp_tpl_myfactoryViewCsp extends templateViewCsp {
    protected function _beforeShow() {
        // adding JS script
        $this->addScript($this->getModule()->getModPath(). 'js/bootstrap.min.js');
        
        // adding styles
        $this->addStyle($this->getModule()->getModPath(). 'css/bootstrap.min.css');
        $this->addStyle($this->getModule()->getModPath(). 'css/bootstrap-responsive.min.css');
        $this->addStyle($this->getModule()->getModPath(). 'css/ie.css');
    }
}