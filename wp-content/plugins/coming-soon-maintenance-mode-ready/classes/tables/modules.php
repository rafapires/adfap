<?php
class tableModulesCsp extends tableCsp {
    public function __construct() {
        $this->_table = '@__modules';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_m';
        $this->_addField('label', 'text', 'varchar', 0, langCsp::_('Label'), 128)
                ->_addField('type_id', 'selectbox', 'smallint', 0, langCsp::_('Type'))
                ->_addField('active', 'checkbox', 'tinyint', 0, langCsp::_('Active'))
                ->_addField('params', 'textarea', 'text', 0, langCsp::_('Params'))
                ->_addField('has_tab', 'checkbox', 'tinyint', 0, langCsp::_('Has Tab'))
                ->_addField('description', 'textarea', 'text', 0, langCsp::_('Description'), 128)
                ->_addField('code', 'hidden', 'varchar', '', langCsp::_('Code'), 64)
                ->_addField('ex_plug_dir', 'hidden', 'varchar', '', langCsp::_('External plugin directory'), 255);
    }
}
?>
