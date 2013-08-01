<?php
/**
 * Class for templates module tab at options page
 */
class templatesViewCsp extends viewCsp {
    /**
     * Get the content for templates module tab
     * 
     * @return type 
     */
    public function getTabContent(){
       $templates = frameCsp::_()->getModule('templatesCsp')->getModel()->get();
       if(empty($templates)) {
           $tpl = 'noTemplates';
       } else {
           $this->assign('templatesCsp', $templates);
           $this->assign('default_theme', frameCsp::_()->getModule('optionsCsp')->getModel()->get('default_theme'));
           $tpl = 'templatesTab';
       }
       return parent::getContent($tpl);
   }
}

