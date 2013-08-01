<?php
class logViewCsp extends viewCsp {
    public function getList() {
        $this->assign('logs', frameCsp::_()->getModule('logCsp')->getModel()->getSorted());
        $this->assign('logTypes', frameCsp::_()->getModule('logCsp')->getModel()->getTypes());
        parent::display('logList');
    }
}