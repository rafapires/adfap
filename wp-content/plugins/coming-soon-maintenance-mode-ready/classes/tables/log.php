<?php
class tableLogCsp extends tableCsp {
    public function __construct() {
        $this->_table = '@__log';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_log';
        $this->_addField('id', 'text', 'int', 0, langCsp::_('ID'), 11)
                ->_addField('type', 'text', 'varchar', '', langCsp::_('Type'), 64)
                ->_addField('data', 'text', 'text', '', langCsp::_('Data'))
                ->_addField('date_created', 'text', 'int', '', langCsp::_('Date created'))
				->_addField('uid', 'text', 'int', 0, langCsp::_('User ID'))
				->_addField('oid', 'text', 'int', 0, langCsp::_('Order ID'));
    }
}