<?php
class FrmProForm{
    
    function update_options($options, $values){
        global $frmpro_settings;
            
        $defaults = FrmProFormsHelper::get_default_opts();
        unset($defaults['logged_in']);
        unset($defaults['editable']);
        unset($defaults['notification']);
        
        foreach($defaults as $opt => $default){
            $options[$opt] = (isset($values['options'][$opt])) ? $values['options'][$opt] : $default;
            
            unset($opt);
            unset($default);
        }
        
        unset($defaults);
        
        if(isset($values['options']['post_custom_fields'])){
            foreach($values['options']['post_custom_fields'] as $cf_key => $n){
                if(!isset($n['custom_meta_name']))
                    continue;
                
                if($n['meta_name'] == '' && $n['custom_meta_name'] != '')
                    $options['post_custom_fields'][$cf_key]['meta_name'] = $n['custom_meta_name'];
                
                unset($options['post_custom_fields'][$cf_key]['custom_meta_name']);
                
                unset($cf_key);
                unset($n);
            }
        }
        
        $options['single_entry'] = (isset($values['options']['single_entry'])) ? $values['options']['single_entry'] : 0;
        if ($options['single_entry'])
            $options['single_entry_type'] = (isset($values['options']['single_entry_type'])) ? $values['options']['single_entry_type'] : 'cookie';
            
        if (is_multisite())
            $options['copy'] = (isset($values['options']['copy'])) ? $values['options']['copy'] : 0;
        return $options;
    }
    
    function update_form_field_options($field_options, $field, $values){        
        $post_fields = array(
            'post_category', 'post_content', 'post_excerpt', 'post_title', 
            'post_name', 'post_date', 'post_status', 'post_password'
        );
        
        $field_options['post_field'] = $field_options['custom_field'] = '';
        $field_options['taxonomy'] = 'category';
        $field_options['exclude_cat'] = 0;
        
        if(!isset($values['options']['create_post']) or !$values['options']['create_post'])
            return $field_options;
            
        foreach($post_fields as $post_field){
            if(isset($values['options'][$post_field]) and $values['options'][$post_field] == $field->id)
                $field_options['post_field'] = $post_field;
        }
        
        //Set post categories
        if(isset($values['options']['post_category']) and isset($values['options']['post_category'])){
            foreach($values['options']['post_category'] as $field_name){
                if($field_name['field_id'] != $field->id)
                    continue;
                
                $field_options['post_field'] = 'post_category';
                $field_options['taxonomy'] = isset($field_name['meta_name']) ? $field_name['meta_name'] : 'category';
                $field_options['exclude_cat'] = isset($field_name['exclude_cat']) ? $field_name['exclude_cat'] : 0;
            }
        }
        
        //Set post custom fields
        if(isset($values['options']['post_custom_fields']) and isset($values['options']['post_custom_fields'])){
            foreach($values['options']['post_custom_fields'] as $field_name){
                if($field_name['field_id'] != $field->id)
                    continue;
                
                $field_options['post_field'] = 'post_custom';
                $field_options['custom_field'] = ($field_name['meta_name'] == '' && isset($field_name['custom_meta_name']) and $field_name['custom_meta_name'] != '') ? $field_name['custom_meta_name'] : $field_name['meta_name'];
            }
        }
        
        return $field_options;
    }
    
    function update($id, $values){
        global $wpdb, $frmdb, $frm_field;
        
        if (isset($values['options'])){
            $logged_in = isset($values['logged_in']) ? $values['logged_in'] : 0;
            $editable = isset($values['editable']) ? $values['editable'] : 0;
            $updated = $wpdb->update( $wpdb->prefix .'frm_forms', array('logged_in' => $logged_in, 'editable' => $editable), array( 'id' => $id ) );
            if($updated){
                wp_cache_delete( $id, 'frm_form');
                unset($updated);
            }
        }
        
        //create post category field
        if(isset($values['options']['post_category']) and isset($values['options']['post_category'])){
            foreach($values['options']['post_category'] as $field_name){
                if($field_name['field_id'] == 'checkbox'){
                    global $frm_field;
                    
                    //create a new field
                    $new_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('checkbox', $id));
                    $new_values['field_options']['taxonomy'] = isset($field_name['meta_name']) ? $field_name['meta_name'] : 'category';
                    $new_values['name'] = ucwords(str_replace('_', ' ', $new_values['field_options']['taxonomy']));
                    $new_values['field_options']['post_field'] = 'post_category';
                    $new_values['field_options']['exclude_cat'] = isset($field_name['exclude_cat']) ? $field_name['exclude_cat'] : 0;
                    $frm_field->create( $new_values );
                    unset($new_values);
                }
                unset($field_name);
            }
        }
        
        //create post status field
        if(isset($values['options']['post_status']) and $values['options']['post_status'] == 'dropdown'){
            global $frm_field;
            
            //create a new field
            $new_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('select', $id));
            $new_values['name'] = __('Status', 'formidable');
            $new_values['field_options']['post_field'] = 'post_status';
            $values['options']['post_status'] = $frm_field->create( $new_values );
            unset($new_values);
        }
        
        //update/create View
        if((isset($values['frm_dyncontent']) and !empty($values['frm_dyncontent'])) or (isset($values['frm_single_content']) and !empty($values['frm_single_content']))){
            
            if(isset($values['frm_display_id']) and is_numeric($values['frm_display_id'])){
                //updating View
                if(isset($values['frm_single_content']))
                    wp_insert_post(array('post_content' => $values['frm_single_content'], 'ID' => $values['frm_display_id']));
                else
                    update_post_meta($values['frm_display_id'], 'frm_dyncontent', $values['frm_dyncontent']);
            }else{
                //create new
                $cd_values = array('post_status' => 'publish', 'post_type' => 'frm_display');
                $cd_values['post_title'] = __('Single', 'formidable') .' '. $values['options']['post_type'];
                $cd_values['post_excerpt'] = __('Used for the single post page', 'formidable');
                $cd_values['post_content'] = __('Add content here if you would like to use this as a listing page.', 'formidable');
                
                $display_id = wp_insert_post( $cd_values );
                unset($cd_values);
                
                foreach(array(
                        'frm_dyncontent' => $values['frm_dyncontent'], 
                        'frm_param' => 'entry', 'frm_type' => 'display_key',
                        'frm_show_count' => 'dynamic', 'frm_form_id' => $id
                    ) as $key => $val){
                    update_post_meta($display_id, $key, $val);
                    unset($key);
                    unset($val);
                }
            }
        }

        //update dependent fields
        if (isset($values['field_options'])){
            $all_fields = $frm_field->getAll(array('fi.form_id' => $id), 'field_order');
            if ($all_fields){
                $changed = array();
                foreach($all_fields as $field){
                    $option_array[$field->id] = maybe_unserialize($field->field_options);
                    $changed[$field->id] = isset($option_array[$field->id]['dependent_fields']) ? $option_array[$field->id]['dependent_fields'] : false;
                    $option_array[$field->id]['dependent_fields'] = false;
                    unset($field);
                }

                foreach($all_fields as $field){
                    if(isset($option_array[$field->id]['hide_field']) and 
                        !empty($option_array[$field->id]['hide_field']) and
                        (!empty($option_array[$field->id]['hide_opt']) or !empty($option_array[$field->id]['form_select']))){
                        //save hidden fields to parent field

                        foreach((array)$option_array[$field->id]['hide_field'] as $i => $f){
                            if(!empty($f) and isset($option_array[$f]) and $option_array[$f])
                                $option_array[$f]['dependent_fields'][$field->id] = true;
                        }

                    }
                    unset($field);
                }
                unset($all_fields);
                
                foreach($option_array as $field_id => $field_options){
                    if($changed[$field_id] != $field_options['dependent_fields'])
                        $frm_field->update($field_id, array('field_options' => $field_options));
                    unset($field_id);
                    unset($field_options);
                }
                unset($changed);
                unset($option_array);
            }
        }
    }
    
    function after_duplicate($new_opts, $id) {
        if ( isset($new_opts['success_url']) ) {
            $new_opts['success_url'] = FrmFieldsHelper::switch_field_ids($new_opts['success_url']);
        }
        
        if ( !isset($new_opts['notification']) ) {
            return $new_opts;
        }
        
        global $frm_duplicate_ids;
        
        foreach ( (array) $new_opts['notification'] as $n => $v ) {
            foreach ( $v as $o => $opt ) {
                if ( in_array($o, array('reply_to_name', 'reply_to')) && is_numeric($opt) && isset($frm_duplicate_ids[$opt]) ) {
                    $new_opts['notification'][$n][$o] = $frm_duplicate_ids[$opt];
                } else if ( in_array($o, array('email_subject', 'email_to', 'email_message')) ) {
                    $new_opts['notification'][$n][$o] = FrmFieldsHelper::switch_field_ids($new_opts['notification'][$n][$o]);
                } else if ( 'conditions' == $o ) {
                    foreach ( (array) $opt as $ck => $cv ) {
                        if ( isset($cv['hide_field']) && is_numeric($cv['hide_field']) && isset($frm_duplicate_ids[$cv['hide_field']]) ) {
                            $new_opts['notification'][$n]['conditions'][$ck]['hide_field'] = $frm_duplicate_ids[$cv['hide_field']];
                        }
                        unset($ck);
                        unset($cv);
                    }
                }
                unset($o);
                unset($opt);
            }
            unset($n);
            unset($opt);
        }
        
        return $new_opts;
    }

    function validate( $errors, $values ){
        global $frm_field;
        /*
        if (isset($values['item_meta'])){    
            foreach($values['item_meta'] as $key => $value){
                $field = $frm_field->getOne($key);  
                if ($field && $field->type == 'hidden' and empty($value))
                    $errors[] = __("Hidden fields must have a value.", 'formidable');
            }

        }
        */
        
        // add a user id field if the form requires one
        if ( isset($values['logged_in']) || isset($values['editable']) || (isset($values['single_entry']) && isset($values['options']['single_entry_type']) && $values['options']['single_entry_type'] == 'user') || (isset($values['options']['save_draft']) && $values['options']['save_draft'] == 1) ) {
            $form_id = $values['id'];
            $user_field = $frm_field->getAll(array('fi.form_id' => $form_id, 'type' => 'user_id'));
            if ( !$user_field ) {
                $new_values = FrmFieldsHelper::setup_new_vars('user_id',$form_id);
                $new_values['name'] = __('User ID', 'formidable');
                $frm_field->create($new_values);
            }
        }
        
        if (isset($values['options']['auto_responder'])){
            if (!isset($values['options']['ar_email_message']) or $values['options']['ar_email_message'] == '')
                $errors[] = __("Please insert a message for your auto responder.", 'formidable');
            if (isset($values['options']['ar_reply_to']) and !is_email(trim($values['options']['ar_reply_to'])))
                $errors[] = __("That is not a valid reply-to email address for your auto responder.", 'formidable');
        }

        return $errors;
    }
    
    public static function has_field($type, $form_id, $single=true){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProFormsHelper::has_field');
        return FrmProFormsHelper::has_field($type, $form_id, $single);
    }
    
    public static function post_type($form_id){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProFormsHelper::post_type');
        return FrmProFormsHelper::post_type($form_id);
    }
}  
