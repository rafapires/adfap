<?php
class tableOptionsCsp extends tableCsp {
     public function __construct() {
        $this->_table = '@__options';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_opt';
        $this->_addField('id', 'text', 'int', 0, langCsp::_('ID'))->
                _addField('code', 'text', 'varchar', '', langCsp::_('Code'), 64)->
                _addField('value', 'text', 'varchar', '', langCsp::_('Value'), 134217728)->
                _addField('label', 'text', 'varchar', '', langCsp::_('Label'), 255)->
                _addField('description', 'text', 'text', '', langCsp::_('Description'))->
                _addField('htmltype_id', 'selectbox', 'text', '', langCsp::_('Type'))->
				_addField('cat_id', 'hidden', 'int', '', langCsp::_('Category ID'))->
				_addField('sort_order', 'hidden', 'int', '', langCsp::_('Sort Order'))->
				_addField('value_type', 'hidden', 'varchar', '', langCsp::_('Value Type'));;
    }
}
?>
