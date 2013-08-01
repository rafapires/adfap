<?php
class pagesViewCsp extends viewCsp {
    public function displayDeactivatePage() {
        $this->assign('GET', reqCsp::get('get'));
        $this->assign('POST', reqCsp::get('post'));
        $this->assign('REQUEST_METHOD', strtoupper(reqCsp::getVar('REQUEST_METHOD', 'server')));
        $this->assign('REQUEST_URI', basename(reqCsp::getVar('REQUEST_URI', 'server')));
        parent::display('deactivatePage');
    }
}

