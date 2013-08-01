<?php
class tableModules_typeCsp extends tableCsp {
    public function __construct() {
        $this->_table = '@__modules_type';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_m_t';
        $this->_addField($this->_id, 'text', 'int', '', langCsp::_('ID'))->
                _addField('label', 'text', 'varchar', '', langCsp::_('Label'), 128);
    }
}
?>
