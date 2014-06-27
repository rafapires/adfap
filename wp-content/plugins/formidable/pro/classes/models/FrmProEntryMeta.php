<?php
class FrmProEntryMeta{
    
    function before_save($values) {
        global $frm_field;
        $field = $frm_field->getOne($values['field_id']);
        if(!$field)
            return $values;
            
        if ( $field->type == 'date' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($values['meta_value'])) ) {
            global $frmpro_settings;
            $values['meta_value'] = FrmProAppHelper::convert_date($values['meta_value'], $frmpro_settings->date_format, 'Y-m-d');
        } else if ( $field->type == 'number' && !is_numeric($values['meta_value']) ) {
            $values['meta_value'] = (float) $values['meta_value'];
        } else if( $field->type == 'rte' && $values['meta_value'] == '<br>' ) {
            $values['meta_value'] = '';
        }
        
        return $values;
    }
    
    function create($entry){
        global $frm_entry, $frm_field, $frm_entry_meta, $wpdb, $frm_vars;

        if ( !isset($_FILES) || !is_numeric($entry) ) {
            return;
        }
        
        $entry = $frm_entry->getOne($entry);
        if ( !$entry ) {
            return;
        }
        
        $fields = $frm_field->getAll("fi.form_id='". (int)$entry->form_id ."' and (fi.type='file' or fi.type='tag')");

        foreach ( $fields as $field ) {
                        
            if ( $field->type == 'file' ) {
                // keep existing files attached to post
                $frm_vars['media_id'][$field->id] = $_POST['item_meta'][$field->id];
            }
            
            if ( isset($_FILES['file'. $field->id]) && !empty($_FILES['file'. $field->id]['name']) && (int) $_FILES['file'. $field->id]['size'] > 0 ) {
                
                if(!isset($frm_vars['loading']) or !$frm_vars['loading'])
                    $frm_vars['loading'] = true;
                
                $media_ids = FrmProAppHelper::upload_file('file'. $field->id);
                $mids = array();
                
                foreach((array)$media_ids as $media_id){
                    if (is_numeric($media_id)){
                       $mids[] = $media_id;
                    }else{
                        foreach ($media_id->errors as $error){
                            if(!is_array($error[0]))
                                echo $error[0];
                            unset($error);
                        }
                    }
                    
                    unset($media_id);
                }
                
                if(!empty($mids)){
                    $frm_entry_meta->delete_entry_meta($entry->id, $field->id);
                    //TODO: delete media?
                    
                    if(isset($field->field_options['multiple']) and $field->field_options['multiple']){
                        if(isset($_POST['item_meta'][$field->id]))
                            $mids = array_merge((array)$_POST['item_meta'][$field->id], $mids);
                        
                        $frm_entry_meta->add_entry_meta($entry->id, $field->id, null, $mids);
                    }else{
                        $mids = reset($mids);
                        $frm_entry_meta->add_entry_meta($entry->id, $field->id, null, $mids);
                        
                        if(isset($_POST['item_meta'][$field->id]) and count($_POST['item_meta'][$field->id]) == 1 and $_POST['item_meta'][$field->id] != $mids)
                            $frm_vars['detached_media'][] = $_POST['item_meta'][$field->id];

                    }
                    
                    if(!isset($frm_vars['media_id']))
                        $frm_vars['media_id'] = array();
                    
                    if ( is_array($mids) ) {
                        $mids = array_filter($mids);
                    }
                    $_POST['item_meta'][$field->id] = $frm_vars['media_id'][$field->id] = $mids;
                    
                    if(isset($_POST['frm_wp_post']) and isset($field->field_options['post_field']) and $field->field_options['post_field'])
                        $_POST['frm_wp_post_custom'][$field->id .'='. $field->field_options['custom_field']] = $mids;

                }
            }
            
            if ( !isset($_POST['item_meta'] ) || !isset($_POST['item_meta'][$field->id]) ) {
                continue;
            }
            
            if($field->type == 'tag'){
                $tax_type = (isset($field->field_options['taxonomy']) and !empty($field->field_options['taxonomy'])) ? $field->field_options['taxonomy'] : 'frm_tag';
                
                $tags = explode(',', stripslashes($_POST['item_meta'][$field->id]));
                $terms = array();
                
                if(isset($_POST['frm_wp_post']))
                    $_POST['frm_wp_post'][$field->id.'=tags_input'] = $tags;
                    
                if($tax_type == 'frm_tag'){
                 
                    foreach($tags as $tag){
                        $slug = sanitize_title($tag);
                        if(!isset($_POST['frm_wp_post'])){
                            if(!term_exists($slug, $tax_type))
                                wp_insert_term( trim($tag), $tax_type, array('slug' => $slug));
                        }

                        $terms[] = $slug;
                    }
                
                    wp_set_object_terms($entry->id, $terms, $tax_type);
                    
                    unset($terms);
                }

            }
        }
    }

    function validate($errors, $field){
        if($field->type == 'user_id'){
            // make sure we have a user ID
            if ( !is_numeric($_POST['item_meta'][$field->id]) ) {
                $_POST['item_meta'][$field->id] = FrmProAppHelper::get_user_id_param($_POST['item_meta'][$field->id]);
            }
            
            //add user id to post variables to be saved with entry
            $_POST['frm_user_id'] = $_POST['item_meta'][$field->id];
        }else if($field->type == 'time' and is_array($_POST['item_meta'][$field->id])){
            $_POST['item_meta'][$field->id] = $value = $_POST['item_meta'][$field->id]['H'] .':'. $_POST['item_meta'][$field->id]['m'] . (isset($_POST['item_meta'][$field->id]['A']) ? ' '. $_POST['item_meta'][$field->id]['A'] : '');
        }
        
        // don't validate if going backwards
        if ( FrmProFormsHelper::going_to_prev($field->form_id) ){
            return array();
        }
        
        // clear any existing errors if draft
        if ( FrmProFormsHelper::saving_draft($field->form_id) && isset($errors['field'. $field->id]) ) {
            unset($errors['field'. $field->id]);
        }
        
        //if the field is a file upload, check for a file
        if ( $field->type == 'file' && isset($_FILES['file'. $field->id]) && !empty($_FILES['file'. $field->id]['name']) ) {
            $filled = true;
            if ( is_array($_FILES['file'. $field->id]['name']) ) {
                $filled = false;
                foreach ( $_FILES['file'. $field->id]['name'] as $n ) {
                    if ( !empty($n) ) {
                        $filled = true;
                    }
                }
            }
            
            if ( $filled ) {
                if ( isset($errors['field'. $field->id]) ) {
                    unset($errors['field'. $field->id]);
                }
                if ( isset($field->field_options['restrict']) && $field->field_options['restrict'] && isset($field->field_options['ftypes']) && !empty($field->field_options['ftypes']) ) {
                    $mimes = $field->field_options['ftypes'];
                } else {
                    $mimes = null;
                }
                
                //check allowed mime types for this field
                if ( is_array($_FILES['file'. $field->id]['name']) ) {
                    foreach ( $_FILES['file'. $field->id]['name'] as $name ) {
                        if ( empty($name) ) {
                            continue;
                        }

                        $file_type = wp_check_filetype( $name, $mimes );
                        unset($name);

                        if ( !$file_type['ext'] ) {
                            break;
                        }
                    }
                } else {
                    $file_type = wp_check_filetype( $_FILES['file'. $field->id]['name'], $mimes );
                } 
                
                if ( isset($file_type) && !$file_type['ext'] ) {
                    $errors['field'. $field->id] = ($field->field_options['invalid'] == __('This field is invalid', 'formidable') || $field->field_options['invalid'] == '' || $field->field_options['invalid'] == $field->name.' '. __('is invalid', 'formidable')) ? __('Sorry, this file type is not permitted for security reasons.', 'formidable') : $field->field_options['invalid'];
                }

                unset($file_type);
            }
            
            unset($filled);
        }
        
        // if saving draft, only check file type since it won't be checked later
        if ( FrmProFormsHelper::saving_draft($field->form_id) ) {
            return $errors;
        }
        
        if(in_array($field->type, array('break', 'html', 'divider'))){
            $hidden = FrmProFieldsHelper::is_field_hidden($field, stripslashes_deep($_POST));
            global $frm_hidden_break, $frm_hidden_divider;
            if($field->type == 'break')
                $frm_hidden_break = array('field_order' => $field->field_order, 'hidden' => $hidden);
            else if($field->type == 'divider')
                $frm_hidden_divider = array('field_order' => $field->field_order, 'hidden' => $hidden);
            
            if(isset($errors['field'. $field->id]))
                unset($errors['field'. $field->id]);
        }
        
        $value = $_POST['item_meta'][$field->id];
        if((($field->type != 'tag' and $value == 0) or ($field->type == 'tag' and $value == '')) and isset($field->field_options['post_field']) and $field->field_options['post_field'] == 'post_category' and $field->required == '1'){
            global $frm_settings;
            $errors['field'. $field->id] = (!isset($field->field_options['blank']) or $field->field_options['blank'] == '' or $field->field_options['blank'] == 'Untitled cannot be blank') ? $frm_settings->blank_msg : $field->field_options['blank']; 
        }
        
        //Don't require fields hidden with shortcode fields="25,26,27"
        global $frm_vars;
        if(isset($frm_vars['show_fields']) and !empty($frm_vars['show_fields']) and is_array($frm_vars['show_fields']) and $field->required == '1' and isset($errors['field'. $field->id]) and !in_array($field->id, $frm_vars['show_fields']) and !in_array($field->field_key, $frm_vars['show_fields'])){
            unset($errors['field'. $field->id]);
            $_POST['item_meta'][$field->id] = $value = '';
        }
        
        //Don't require a conditionally hidden field
        if (isset($field->field_options['hide_field']) and !empty($field->field_options['hide_field'])){
            if ( FrmProFieldsHelper::is_field_hidden($field, stripslashes_deep($_POST)) ) {
                if(isset($errors['field'. $field->id]))
                    unset($errors['field'. $field->id]);
                $_POST['item_meta'][$field->id] = $value = '';
            }
        }
        
        //Don't require a field hidden in a conditional page or section heading
        if(isset($errors['field'. $field->id]) or $_POST['item_meta'][$field->id] != ''){
            global $frm_hidden_break, $frm_hidden_divider;
            if(($frm_hidden_break and $frm_hidden_break['hidden']) or ($frm_hidden_divider and $frm_hidden_divider['hidden'] and (!$frm_hidden_break or $frm_hidden_break['field_order'] < $frm_hidden_divider['field_order']))){
                if(isset($errors['field'. $field->id]))
                    unset($errors['field'. $field->id]);
                $_POST['item_meta'][$field->id] = $value = '';
            }
        }
        
        //make sure the [auto_id] is still unique
        if(!empty($field->default_value) and !is_array($field->default_value) and !empty($value) and is_numeric($value) and strpos($field->default_value, '[auto_id') !== false){
            //make sure we are not editing
            if((isset($_POST) and !isset($_POST['id'])) or !is_numeric($_POST['id']))
                $_POST['item_meta'][$field->id] = $value = FrmProFieldsHelper::get_default_value($field->default_value, $field);
        }
        
        //check uniqueness
        if ($value and !empty($value) and isset($field->field_options['unique']) and $field->field_options['unique']){
            $entry_id = (isset($_POST) and isset($_POST['id'])) ? $_POST['id'] : false;
            if($field->type == 'time'){
                //TODO: add server-side validation for unique date-time
            }else if($field->type == 'date'){
                global $frmpro_settings;
                $old_value = $value;
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($value)))
        	        $value = FrmProAppHelper::convert_date($value, $frmpro_settings->date_format, 'Y-m-d');
                if ( FrmProEntryMetaHelper::value_exists($field->id, $value, $entry_id) ) {
                    $errors['field'.$field->id] = FrmProFieldsHelper::get_error_msg($field, 'unique_msg');
                }
                $value = $old_value;
            } else if ( FrmProEntryMetaHelper::value_exists($field->id, $value, $entry_id) ) {
                $errors['field'. $field->id] = FrmProFieldsHelper::get_error_msg($field, 'unique_msg');
            }
            unset($entry_id);
        }
        
        if(!empty($value) and ($field->type == 'website' or $field->type == 'url' or $field->type == 'image')){
            if(trim($value) == 'http://'){
                $_POST['item_meta'][$field->id] = $value = '';
            }else{    
                $value = esc_url_raw( $value );
		        $_POST['item_meta'][$field->id] = $value = preg_match('/^(https?|ftps?|mailto|news|feed|telnet):/is', $value) ? $value : 'http://'. $value;
	        }
		}
        
        $errors = FrmProEntryMetaHelper::set_post_fields($field, $value, $errors);
        
        if ( !FrmProFieldsHelper::is_field_visible_to_user($field) ) {
            //don't validate admin only fields that can't be seen
            unset($errors['field'. $field->id]);
            return $errors;
        }
		
		if(false and isset($field->field_options['use_calc']) and !empty($field->field_options['use_calc']) and !empty($field->field_options['calc'])){
		    $field->field_options['calc'] = trim($field->field_options['calc']);
		    preg_match_all( "/\[(.*?)\]/s", $field->field_options['calc'], $calc_matches, PREG_PATTERN_ORDER);
		    if(isset($calc_matches[1])){
		        foreach($calc_matches[1] as $c){
		            if(is_numeric($c)){
		                $c_id = $c;
		            }else{
		                global $frm_field;
		                $c_field = $frm_field->getOne($c);
		                if(!$c_field){
		                    $field->field_options['calc'] = str_replace('['. $c .']', 0, $field->field_options['calc']);
		                    continue;
		                }
		                $c_id = $c_field->id;
		                unset($c_field);
		            }
		            $c_val = trim($_POST['item_meta'][$c_id]);
		            if(!is_numeric($c_val)){
		                preg_match_all('/[0-9,]*\.?[0-9]+/', $c_val, $c_matches);
                        $c_val = ($c_matches) ? end($c_matches[0]) : 0;
                        unset($c_matches);
                    }
                    if($c_val == '')
                        $c_val = 0;
                    
		            $field->field_options['calc'] = str_replace('['. $c .']', $c_val, $field->field_options['calc']);
		            unset($c);
		            unset($c_id);
		        }
		        
		        include(FrmAppHelper::plugin_path() .'/pro/classes/helpers/FrmProMathHelper.php');
		        $m = new EvalMath;
		        if(strpos($field->field_options['calc'], ').toFixed(')){
		            $field->field_options['calc'] = str_replace(').toFixed(2', '', $field->field_options['calc']);
		            $round = 2;
		        }
		        
		        $result = $m->evaluate(str_replace('Math.', '', '('. $field->field_options['calc'] .')'));
            	if(isset($round) and $round)
            	    $result = sprintf('%.'. $round .'f', $result);
            	unset($m);
                
                $_POST['item_meta'][$field->id] = $value = $result;
                unset($result);
            }
            unset($calc_matches);
		}

        //Don't validate the format if field is blank
        if ($value == '' or is_array($value)) return $errors;
        
        $value = trim($value);

        //validate the format
        if (($field->type == 'number' and !is_numeric($value)) or 
            ($field->type == 'email' and !is_email($value)) or 
            (($field->type == 'website' or $field->type == 'url' or $field->type == 'image') and !preg_match('/^http(s)?:\/\/([\da-z\.-]+)\.([\da-z\.-]+)/i', $value))){
            $errors['field'. $field->id] = FrmProFieldsHelper::get_error_msg($field, 'invalid');
        }
        
        if ($field->type == 'phone'){
            $pattern = (isset($field->field_options['format']) and !empty($field->field_options['format'])) ? $field->field_options['format'] : '^((\+\d{1,3}(-|.| )?\(?\d\)?(-| |.)?\d{1,5})|(\(?\d{2,6}\)?))(-|.| )?(\d{3,4})(-|.| )?(\d{4})(( x| ext)\d{1,5}){0,1}$';
            $pattern = apply_filters('frm_phone_pattern', $pattern, $field);
            
            //check if format is already a regular expression
            if(strpos($pattern, '^') !== 0){
                //if not, create a regular expression
                $pattern = preg_replace('/\d/', '\d', preg_quote($pattern));
                $pattern = '/^'. $pattern .'$/';
            }else{
                $pattern = '/'. $pattern .'/';
            }
            
            if(!preg_match($pattern, $value))
                $errors['field'. $field->id] = FrmProFieldsHelper::get_error_msg($field, 'invalid');
            unset($pattern);
        }
        
        if($field->type == 'date'){ 
            if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)){
                global $frmpro_settings;
                $formated_date = FrmProAppHelper::convert_date($value, $frmpro_settings->date_format, 'Y-m-d');
                //check format before converting
                if($value != date($frmpro_settings->date_format, strtotime($formated_date)))
                    $errors['field'. $field->id] = FrmProFieldsHelper::get_error_msg($field, 'invalid');
                
                $value = $formated_date;
                unset($formated_date);
            }
            $date = explode('-', $value);

            if(count($date) != 3 or !checkdate( (int)$date[1], (int)$date[2], (int)$date[0]))
                $errors['field'.$field->id] = FrmProFieldsHelper::get_error_msg($field, 'invalid');
        }

        return $errors;
    }
    
    function set_post_fields($field, $value, $errors=null){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntryMetaHelper::set_post_fields');
        return FrmProEntryMetaHelper::set_post_fields($field, $value, $errors);
    }
    
    function meta_through_join($hide_field, $selected_field, $observed_field_val){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntryMetaHelper::meta_through_join');
        return FrmProEntryMetaHelper::meta_through_join($hide_field, $selected_field, $observed_field_val);
    }
    
    function value_exists($field_id, $value, $entry_id=false){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntryMetaHelper::value_exists');
        return FrmProEntryMetaHelper::value_exists($field_id, $value, $entry_id);
    }
    
    function post_value_exists($post_field, $value, $post_id, $custom_field=''){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntryMetaHelper::post_value_exists');
        return FrmProEntryMetaHelper::post_value_exists($post_field, $value, $post_id, $custom_field);
    }
    
    function get_max($field){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntryMetaHelper::get_max');
        return FrmProEntryMetaHelper::get_max($field);
    }
}
