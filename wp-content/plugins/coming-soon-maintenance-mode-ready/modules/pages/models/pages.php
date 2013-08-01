<?php
class pagesModelCsp extends modelCsp {
	public function recreatePages() {
		installerCsp::createPages();
		return true;
	}
}
