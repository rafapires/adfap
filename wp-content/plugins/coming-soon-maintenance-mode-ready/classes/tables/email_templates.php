<?php
class tableEmail_templatesCsp extends tableCsp {
    public function __construct() {
        $this->_table = '@__email_templates';
        $this->_id = 'id';
        $this->_alias = 'toe_etpl';
        $this->_addField('label', 'text', 'varchar', '', langCsp::_('Label'), 128, '','',langCsp::_('Template label'))
               ->_addField('subject', 'textarea', 'varchar','', langCsp::_('Subject'),255,'','',langCsp::_('E-mail Subject'))
               ->_addField('body', 'textarea', 'text','', langCsp::_('Body'),'','','',langCsp::_('E-mail Body'))
               ->_addField('variables', 'block', 'text','', langCsp::_('Variables'),'','','',langCsp::_('Template variables. They can be used in the body and subject'))
               ->_addField('active', 'checkbox', 'tinyint',0, langCsp::_('Active'),'','','',langCsp::_('If checked the notifications will be sent to receiver'))
               ->_addField('name', 'hidden', 'varchar','','',128)
               ->_addField('moduleCsp', 'hidden', 'varchar','','', 128);
    }
}
?>
