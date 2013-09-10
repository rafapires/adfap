<?php
class FrmProField{

    function FrmProField(){
        add_filter('frm_before_field_created', array(&$this, 'create'));
        add_filter('frm_update_field_options', array(&$this, 'update'), 10, 3);
        add_filter('frm_duplicated_field', array(&$this, 'duplicate'));
    }
    
    function create($field_data){
        global $frmpro_settings;
        
        if ($field_data['field_options']['label'] != 'none')
            $field_data['field_options']['label'] = '';

        
        switch($field_data['type']){
            case '10radio':
            case 'scale':
                $field_data['options'] = serialize(array(1,2,3,4,5,6,7,8,9,10));
                $field_data['field_options']['minnum'] = 1;
                $field_data['field_options']['maxnum'] = 10;
                break;
            case 'number':
                $field_data['field_options']['maxnum'] = 9999999;
                break;
            case 'select':
                $field_data['field_options']['size'] = $frmpro_settings->auto_width;
                break;
            case 'date':
                $field_data['field_options']['size'] = '10';
                $field_data['field_options']['max'] = '10';
                break;
            case 'time':
                $field_data['field_options']['size'] = '10';
                $field_data['field_options']['max'] = '10';
                break;
            case 'phone':
                $field_data['field_options']['size'] = '15';
                break;
            case 'rte':
                $field_data['field_options']['max'] = '7';
                break;
            case 'user_id':
                $field_data['name'] = __('User ID', 'formidable');
                break;
            case 'website':
            case 'url':
                $field_data['name'] = __('Website', 'formidable');
                //$field_data['default_value'] = 'http://';
                //$field_data['field_options']['default_blank'] = true;
                break;
            case 'divider':
                $field_data['field_options']['label'] = 'top';
                break;
            case 'break':
                global $frmdb;
                $page_num = $frmdb->get_count($frmdb->fields, array("form_id" => $field_data['form_id'], "type" => 'break'));
                $field_data['name'] = __('Next', 'formidable');
                //$field_data['field_options']['label'] = 'top';
        }
        return $field_data;
    }
    
    function update($field_options, $field, $values){
        $defaults = FrmProFieldsHelper::get_default_field_opts(false, $field);
        unset($defaults['dependent_fields']);
        unset($defaults['post_field']);
        unset($defaults['custom_field']);
        unset($defaults['taxonomy']);
        unset($defaults['exclude_cat']);
        
        $defaults['minnum'] = 0;
        $defaults['maxnum'] = 9999;
        
        foreach ($defaults as $opt => $default)
            $field_options[$opt] = isset($values['field_options'][$opt.'_'.$field->id]) ? $values['field_options'][$opt.'_'.$field->id] : $default;
        
        foreach($field_options['hide_field'] as $i => $f){
            if(empty($f)){
                unset($field_options['hide_field'][$i]);
                unset($field_options['hide_field_cond'][$i]);
                unset($field_options['hide_opt'][$i]);
            }
        }
        
        /*
        if($field_options['exclude_cat'] and (!isset($values['field_options']['show_exclude_'.$field->id]))){
            $field_options['exclude_cat'] = 0;
            $_POST['field_options']['exclude_cat_'.$field->id] = 0;
        }else if(isset($field_options['exclude_cat']) and is_array($field_options['exclude_cat'])){
            foreach($field_options['exclude_cat'] as $ex => $cat){
                if(!$cat) unset($field_options['exclude_cat'][$ex]);
            }
        } */

        if($field->type == '10radio' or $field->type == 'scale'){
            global $frm_field;
            $options = array();
            if((int)$field_options['maxnum'] >= 99)
                $field_options['maxnum'] = 10;
                
            for( $i=$field_options['minnum']; $i<=$field_options['maxnum']; $i++ )
                $options[] = $i;
            
            $frm_field->update($field->id, array('options' => serialize($options)));
        }else if($field->type == 'hidden' and isset($field_options['required']) and $field_options['required']){
            $field_options['required'] = false;
        }
        
        return $field_options;
    }
    
    function duplicate($values){
        global $frm_duplicate_ids;
        if(empty($frm_duplicate_ids))
            return $values;

        $values['field_options'] = maybe_unserialize($values['field_options']);
        if(!empty($values['field_options'])){
            if(isset($values['field_options']['calc']) and !empty($values['field_options']['calc'])){
                $ids = implode(array_keys($frm_duplicate_ids), '|');
                preg_match_all( "/\[($ids)\]/s", $values['field_options']['calc'], $matches, PREG_PATTERN_ORDER);
                unset($ids);
                
                if (isset($matches[1])){
                    foreach ($matches[1] as $val){
                        $values['field_options']['calc'] = str_replace('['. $val .']', '['. $frm_duplicate_ids[$val] .']', $values['field_options']['calc']);
                        unset($val);
                    }
                }
                unset($matches);
            }
            
            if(isset($values['field_options']['hide_field']) and !empty($values['field_options']['hide_field'])){
                $values['field_options']['hide_field_cond'] = maybe_unserialize($values['field_options']['hide_field_cond']);
                $values['field_options']['hide_opt'] = maybe_unserialize($values['field_options']['hide_opt']);
                $values['field_options']['hide_field'] = maybe_unserialize($values['field_options']['hide_field']);
                foreach($values['field_options']['hide_field'] as $k => $f){
                    if(isset($frm_duplicate_ids[$f]))
                        $values['field_options']['hide_field'][$k] = $frm_duplicate_ids[$f];
                    unset($k);
                    unset($f);
                }
            }
        }
        
        return $values;
    }
    
    function delete(){
        //TODO: before delete do something with entries with data field meta_value = field_id
    }
    
    function is_field_hidden($field, $values){
        global $frm_field;
        
        $field->field_options = maybe_unserialize($field->field_options);
        
        if($field->type == 'user_id' or $field->type == 'hidden')
            return false;
            
        if(!isset($field->field_options['hide_field']) or empty($field->field_options['hide_field']))
            return false;

        //TODO: check if field is included in conditional heading
        
        $field->field_options['hide_field'] = (array)$field->field_options['hide_field']; 
        if(!isset($field->field_options['hide_field_cond']))
            $field->field_options['hide_field_cond'] = array('==');   
        $field->field_options['hide_field_cond'] = (array)$field->field_options['hide_field_cond'];
        $field->field_options['hide_opt'] = (array)$field->field_options['hide_opt'];
            
        if(!isset($field->field_options['show_hide']))
            $field->field_options['show_hide'] = 'show';
        
        if(!isset($field->field_options['any_all']))
            $field->field_options['any_all'] = 'any';
        
        $hidden = false;
        $hide = array();
        
        foreach($field->field_options['hide_field'] as $hide_key => $hide_field){
            if($hidden and $field->field_options['any_all'] == 'any' and $field->field_options['show_hide'] == 'hide')
                continue;
              
            $observed_value = stripslashes_deep((isset($values['item_meta'][$hide_field])) ? $values['item_meta'][$hide_field] : '');
            
            if($field->type == 'data' and empty($field->field_options['hide_opt'][$hide_key]) and (is_numeric($observed_value) or is_array($observed_value))){
                $observed_field = $frm_field->getOne($hide_field);
                if($observed_field->type == 'data')
                    $field->field_options['hide_opt'][$hide_key] = $observed_value;

                unset($observed_field);
            }

            $hidden = FrmProFieldsHelper::value_meets_condition($observed_value, $field->field_options['hide_field_cond'][$hide_key], $field->field_options['hide_opt'][$hide_key]); 
            if($field->field_options['show_hide'] == 'show')
                $hidden = ($hidden) ? false : true;

            $hide[$hidden] = $hidden;
        }
        
        if($field->field_options['any_all'] == 'all' and !empty($hide) and isset($hide[0]) and isset($hide[1]))
            $hidden = ($field->field_options['show_hide'] == 'show') ? true : false;
        else if($field->field_options['any_all'] == 'any' and $field->field_options['show_hide'] == 'show' and isset($hide[0]))
            $hidden = false;
        
        return $hidden;
    }
    
    function is_visible_to_user($field){
        $visible = true;
        
        if(isset($field->field_options['admin_only']) and !empty($field->field_options['admin_only'])){
            if($field->field_options['admin_only'] == 1) $field->field_options['admin_only'] = 'administrator';

            if(($field->field_options['admin_only'] == 'loggedout' and is_user_logged_in()) or
                ($field->field_options['admin_only'] == 'loggedin' and !is_user_logged_in()) or
                (!in_array($field->field_options['admin_only'], array('loggedout', 'loggedin', '')) and
                !FrmAppHelper::user_has_permission($field->field_options['admin_only']))){
                    $visible = false;
            }
        }

        return $visible;
    }
    
    function on_current_page($field){
        global $frm_prev_page, $frm_next_page, $frm_field;
        $current = true;
        
        $prev = 0;
        $next = 9999;
        if(!is_object($field))
            $field = $frm_field->getOne($field);
        
        if($frm_prev_page and is_array($frm_prev_page) and isset($frm_prev_page[$field->form_id]))
            $prev = $frm_prev_page[$field->form_id];
        
        if($frm_next_page and is_array($frm_next_page) and isset($frm_next_page[$field->form_id])){
            $next = $frm_next_page[$field->form_id];
            if(is_object($next))
                $next = $next->field_order;
        }
        
        if($field->field_order < $prev or $field->field_order > $next)
            $current = false;
        
        $current = apply_filters('frm_show_field_on_page', $current, $field);
        return $current;
    }
}
