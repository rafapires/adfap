<?php
class FrmProField{
    
    function create($field_data){
        global $frmpro_settings;
        
        if ($field_data['field_options']['label'] != 'none')
            $field_data['field_options']['label'] = '';

        
        switch($field_data['type']){
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
                $page_num = $frmdb->get_count($frmdb->fields, array('form_id' => $field_data['form_id'], 'type' => 'break'));
                $field_data['name'] = __('Next', 'formidable');
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
        
        foreach ($defaults as $opt => $default){
            $field_options[$opt] = isset($values['field_options'][$opt.'_'.$field->id]) ? $values['field_options'][$opt.'_'.$field->id] : $default;
            unset($opt);
            unset($default);
        }
        
        foreach($field_options['hide_field'] as $i => $f){
            if(empty($f)){
                unset($field_options['hide_field'][$i]);
                unset($field_options['hide_field_cond'][$i]);
                if ( isset($field_options['hide_opt']) && is_array($field_options['hide_opt']) ) {
                    unset($field_options['hide_opt'][$i]);
                }
            }
            unset($i);
            unset($f);
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

        if($field->type == 'scale'){
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
        if ( empty($frm_duplicate_ids) || empty($values['field_options']) ) {
            return $values;
        }
        
        // switch out fields from calculation or default values
        $switch_string = array('default_value', 'calc');
        foreach ( $switch_string as $opt ) {
            if ( (!isset($values['field_options'][$opt]) || empty($values['field_options'][$opt])) &&
                (!isset($values[$opt]) || empty($values[$opt])) ) {
                continue;
            }
            
            $this_val = isset($values[$opt]) ? $values[$opt] : $values['field_options'][$opt];
            if ( is_array($this_val) ) {
                continue;
            }
            
            $ids = implode( array_keys($frm_duplicate_ids), '|' );
            
            preg_match_all( "/\[($ids)\]/s", $this_val, $matches, PREG_PATTERN_ORDER);
            unset($ids);
            
            if ( !isset($matches[1]) ) {
                unset($matches);
                continue;
            }
            
            foreach ( $matches[1] as $val ) {
                $new_val = str_replace('['. $val .']', '['. $frm_duplicate_ids[$val] .']', $this_val);
                if ( isset($values[$opt]) ) {
                    $this_val = $values[$opt] = $new_val;
                } else {
                    $this_val = $values['field_options'][$opt] = $new_val;
                }
                unset($new_val);
                unset($val);
            }
            
            unset($this_val);
            unset($matches);
        }
        
        // switch out field ids in conditional logic
        if ( isset($values['field_options']['hide_field']) && !empty($values['field_options']['hide_field']) ) {
            $values['field_options']['hide_field_cond'] = maybe_unserialize($values['field_options']['hide_field_cond']);
            $values['field_options']['hide_opt'] = maybe_unserialize($values['field_options']['hide_opt']);
            $values['field_options']['hide_field'] = maybe_unserialize($values['field_options']['hide_field']);
            
            foreach ( $values['field_options']['hide_field'] as $k => $f ) {
                if ( isset($frm_duplicate_ids[$f]) ) {
                    $values['field_options']['hide_field'][$k] = $frm_duplicate_ids[$f];
                }
                unset($k);
                unset($f);
            }
        }
        
        // switch out field ids if selected in a data from entries field
        if ( 'data' == $values['type'] && isset($values['field_options']['form_select']) &&
            !empty($values['field_options']['form_select']) && isset($frm_duplicate_ids[$values['field_options']['form_select']]) ) {
	        $values['field_options']['form_select'] = $frm_duplicate_ids[$values['field_options']['form_select']];
	    }
	    
	    // switch out ids for dependent fields
	    if ( isset($values['field_options']['dependent_fields']) && !empty($values['field_options']['dependent_fields']) ) {
	        foreach ( $values['field_options']['dependent_fields'] as $f => $v ) {
	            if ( isset($frm_duplicate_ids[$f]) ) {
	                $values['field_options']['dependent_fields'][$frm_duplicate_ids[$f]] = $v;
	                unset($values['field_options']['dependent_fields'][$f]);
	            }
	            unset($v);
	            unset($fid);
	        }
	    }
        
        return $values;
    }
    
    function delete(){
        //TODO: before delete do something with entries with data field meta_value = field_id
    }
    
    function is_field_hidden($field, $values){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProFieldsHelper::is_field_hidden');
        return FrmProFieldsHelper::is_field_hidden($field, $values);
    }
    
    function is_visible_to_user($field){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProFieldsHelper::is_field_visible_to_user');
        return FrmProFieldsHelper::is_field_visible_to_user($field);
    }
    
    function on_current_page($field){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProFieldsHelper::field_on_current_page');
        return FrmProFieldsHelper::field_on_current_page($field);
    }
}
