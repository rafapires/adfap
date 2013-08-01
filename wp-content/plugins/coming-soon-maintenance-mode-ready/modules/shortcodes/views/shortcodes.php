<?php
class shortcodesViewCsp extends viewCsp {
    public function adminTextEditorPopup() {
        $shortcodes = frameCsp::_()->getModule('shortcodesCsp')->getCodes();
        $shortcodesSelectOptions = array('' => langCsp::_('Select'));
        foreach($shortcodes as $code => $cinfo) {
            if(in_array($code, array('product', 'category'))) continue;
            $shortcodesSelectOptions[ $code ] = $code;
        }
        $this->assign('shortcodesCsp', $shortcodes);
        $this->assign('shortcodesSelectOptions', $shortcodesSelectOptions);
        return parent::getContent('adminTextEditorPopup');
    }
}
