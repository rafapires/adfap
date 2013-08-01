<?php
class csp_tpl_standardViewCsp extends templateViewCsp {
    protected function _beforeShow() {
        // adding JS script
        $this->addScript($this->getModule()->getModPath(). 'js/script.js');
    }
}