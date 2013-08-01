<?php
class tableSubscribersCsp extends tableCsp {
    public function __construct() {
        $this->_table = '@__subscribers';
        $this->_id = 'id';
        $this->_alias = 'toe_subscr';
        $this->_addField('user_id', 'text', 'int', '', langCsp::_('User Id'), 11, '', '', langCsp::_('User Id'))
            ->_addField('email', 'text', 'varchar', '', langCsp::_('User E-mail'), 255, '', '', langCsp::_('Subscriber E-mail'))
            ->_addField('name', 'text', 'varchar', 0, langCsp::_('User Name'),255,'','', langCsp::_('User Name If User Is Registered'))
            ->_addField('created', 'text', 'datetime', '', langCsp::_('Subscription Date'), '', '','', langCsp::_('Date Of Subscription'))
            ->_addField('active', 'checkbox', 'tinyint', '', langCsp::_('Active Subscription'), 4, '','', langCsp::_('If Is Not Checked user will not get any newsletters'))
            ->_addField('token', 'hidden', 'varchar', '', langCsp::_('Token'), 255,'','','')
			->_addField('ip', 'hidden', 'varchar', '', langCsp::_('IP address'), 64,'','','');
    }
}
?>