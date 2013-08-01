<?php
class userCsp extends moduleCsp {
    public function loadUserData() {
        return $this->getCurrent();
    }
    public function addProfileFieldsHtml($user) {
        //if($this->isCustomer($user->ID)) {
            $this->getController()->getView('user')->displayAllMeta($user->ID);
        //}
    }
    public function isAdmin() {
        $currentUser = $this->getCurrent();
        if(!empty($currentUser)) {
            return (bool) $currentUser->isAdmin;
        }
        return false;
    }
	public function getCurrentUserPosition() {
		if($this->isAdmin())
			return CSP_ADMIN;
		else if($this->getCurrentID())
			return CSP_LOGGED;
		else 
			return CSP_GUEST;
	}
    public function getCurrent() {
        return $this->getController()->getModel('user')->get();
    }

    public function getCurrentID() {
        return $this->getController()->getModel()->getCurrentID();
    }
    /**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
        $tab = new tabCsp(langCsp::_('User Fields'), $this->getCode());
        $tab->setView('userFieldsTab');
		$tab->setSortOrder(3);
		$tab->setParent('templatesCsp');
		$tab->setNestingLevel(1);
        $tabs[] = $tab;
        return $tabs;
    }
}

