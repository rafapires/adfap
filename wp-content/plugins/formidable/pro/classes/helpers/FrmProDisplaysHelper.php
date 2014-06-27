<?php

class FrmProDisplaysHelper{
    
    public static function setup_new_vars(){
        $values = array();
        $defaults = FrmProDisplaysHelper::get_default_opts();
        foreach ($defaults as $var => $default)
            $values[$var] = FrmAppHelper::get_param($var, $default);
        
        return $values;
    }
    
    public static function setup_edit_vars($post, $check_post=true){
        if(!$post) return false;

        $values = (object)$post;
        $defaults = FrmProDisplaysHelper::get_default_opts();
        
        foreach (array('form_id', 'entry_id', 'post_id', 'dyncontent', 'param', 'type', 'show_count', 'insert_loc') as $var){
            if($check_post)
                $values->{'frm_'. $var} = FrmAppHelper::get_param($var, get_post_meta($post->ID, 'frm_'. $var, true));
            else
                $values->{'frm_'. $var} = get_post_meta($post->ID, 'frm_'. $var, true);
        }
        
        $options = get_post_meta($post->ID, 'frm_options', true);
        foreach ($defaults as $var => $default){
            if(!isset($values->{'frm_'. $var})){
                if($check_post){
                    $values->{'frm_'. $var} = FrmAppHelper::get_post_param('options['. $var .']', (isset($options[$var])) ? $options[$var] : $default);
                }else{
                    $values->{'frm_'. $var} = (isset($options[$var])) ? $options[$var] : $default;
                }
            }else if($var == 'param' and empty($values->{'frm_'. $var})){
                $values->{'frm_'. $var} = $default;
            }
        }
	    
	    $values->frm_form_id = (int) $values->frm_form_id;
		$values->frm_order_by = (empty($values->frm_order_by)) ? array() : (array) $values->frm_order_by;
        $values->frm_order = (empty($values->frm_order)) ? array() : (array) $values->frm_order;

        return $values;
    }
    
    public static function get_default_opts(){
        
        return array(
            'name' => '', 'description' => '', 'display_key' => '', 
            'form_id' => 0, 'date_field_id' => '', 'edate_field_id' => '',
			'repeat_event_field_id' => '', 'repeat_edate_field_id' => '', 'entry_id' => '',
            'post_id' => '', 'before_content' => '', 'content' => '', 
            'after_content' => '', 'dyncontent' => '', 'param' => 'entry', 
            'type' => '', 'show_count' => 'all', 'insert_loc' => 'none', 
            'insert_pos' => 1, 'no_rt' => 0,
            'order_by' => array(), 'order' => array(), 'limit' => '', 'page_size' => '', 
            'empty_msg' => __('No Entries Found', 'formidable'), 'copy' => 0, 
            'where' => array(), 'where_is' => array(), 'where_val' => array()
        );
    }

    public static function get_shortcodes($content, $form_id) {
        global $frm_field;
        
        if(empty($form_id))
            return false;
        
        $form_ids = array($form_id);
        //get linked form ids
        /*$data_fields = $frm_field->getAll(array('fi.type' => 'data', 'fi.form_id' => $form_id));
        if($data_fields){
            foreach($data_fields as $data_field){
                $data_field->field_options = maybe_unserialize($data_field->field_options);
                $linked_field = $frm_field->getOne($data_field->field_options['form_select']);
                if($linked_field)
                    $form_ids[] = $linked_field->form_id;
                unset($data_field);
                unset($linked_field);
            }
        }*/
        
        $fields = $frm_field->getAll("fi.type not in ('divider','captcha','break','html') and fi.form_id in (".implode(',', $form_ids) .')');
        
        $tagregexp = 'editlink|deletelink|detaillink|id|post[-|_]id|key|ip|created[-|_]at|updated[-|_]at|updated[-|_]by|evenodd|get|siteurl|sitename|entry_count';
        foreach ($fields as $field)
            $tagregexp .= '|'. $field->id . '|'. $field->field_key;

        preg_match_all("/\[(if )?($tagregexp)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?/s", $content, $matches, PREG_PATTERN_ORDER);

        return $matches;
    }

}
