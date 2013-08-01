<?php
class tableOptions_categoriesCsp extends tableCsp {
    public function __construct() {
        $this->_table = '@__options_categories';
        $this->_id = 'id';     
        $this->_alias = 'toe_opt_cats';
        $this->_addField('id', 'hidden', 'int', 0, langCsp::_('ID'))
            ->_addField('label', 'text', 'varchar', 0, langCsp::_('Method'), 128);
    }
}
?>
