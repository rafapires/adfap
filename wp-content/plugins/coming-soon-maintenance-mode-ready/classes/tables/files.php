<?php
class tableFilesCsp extends tableCsp {
    public function __construct() {
        $this->_table = '@__files';
        $this->_id = 'id';
        $this->_alias = 'toe_f';
        $this->_addField('pid', 'hidden', 'int', '', langCsp::_('Product ID'))
                ->_addField('name', 'text', 'varchar', '255', langCsp::_('File name'))
                ->_addField('path', 'hidden', 'text', '', langCsp::_('Real Path To File'))
                ->_addField('mime_type', 'text', 'varchar', '32', langCsp::_('Mime Type'))
                ->_addField('size', 'text', 'int', 0, langCsp::_('File Size'))
                ->_addField('active', 'checkbox', 'tinyint', 0, langCsp::_('Active Download'))
                ->_addField('date','text','datetime','',langCsp::_('Upload Date'))
                ->_addField('download_limit','text','int','',langCsp::_('Download Limit'))
                ->_addField('period_limit','text','int','',langCsp::_('Period Limit'))
                ->_addField('description', 'textarea', 'text', 0, langCsp::_('Descritpion'))
                ->_addField('type_id','text','int','',langCsp::_('Type ID'));
    }
}
