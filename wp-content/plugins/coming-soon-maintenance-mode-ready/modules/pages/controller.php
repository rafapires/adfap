<?php
class pagesControllerCsp extends controllerCsp {
    public function recreatePages() {
		$res = new responseCsp();
		if($this->getModel()->recreatePages()) {
			$res->addMessage(langCsp::_('Pages was recreated'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		$res->ajaxExec();
	}
}

