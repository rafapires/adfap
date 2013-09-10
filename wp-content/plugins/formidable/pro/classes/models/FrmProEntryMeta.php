<?php
class FrmProEntryMeta{

    function FrmProEntryMeta(){
        add_filter('frm_add_entry_meta', array(&$this, 'before_create'));
        add_action('frm_after_create_entry', array(&$this, 'create'), 10);
        add_action('frm_after_update_entry', array(&$this, 'create'));
        add_filter('frm_validate_field_entry', array(&$this, 'validate'), 10, 2);
    }
    
    function before_create($values){
        global $frm_field;
        $field = $frm_field->getOne($values['field_id']);
        if(!$field)
            return $values;
            
        if ($field->type == 'date'){
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($values['meta_value']))){
                global $frmpro_settings;
                $values['meta_value'] = FrmProAppHelper::convert_date($values['meta_value'], $frmpro_settings->date_format, 'Y-m-d');
            }
        }else if ($field->type == 'number'){
            if(!is_numeric($values['meta_value']))
                $values['meta_value'] = (float)$values['meta_value'];
        }else if($field->type == 'rte' and $values['meta_value'] == '<br>'){
            $values['meta_value'] = '';
        }
        return $values;
    }
    
    function create($entry){
        global $frm_entry, $frm_field, $frm_entry_meta, $wpdb, $frm_loading, $frm_detached_media;

        if (!isset($_FILES) || !is_numeric($entry)) return;
        
        $entry = $frm_entry->getOne($entry);  
        $fields = $frm_field->getAll("fi.form_id='". (int)$entry->form_id ."' and (fi.type='file' or fi.type='tag')");

        foreach ($fields as $field){
            $field->field_options = maybe_unserialize($field->field_options);
            if( isset($_FILES['file'. $field->id]) and !empty($_FILES['file'. $field->id]['name']) and (int)$_FILES['file'. $field->id]['size'] > 0){
                    
                if(!$frm_loading)
                    $frm_loading = true;
                
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
                        
                        $frm_entry_meta->update_entry_meta($entry->id, $field->id, $field->field_key, serialize($mids));
                    }else{
                        $mids = reset($mids);
                        $frm_entry_meta->update_entry_meta($entry->id, $field->id, $field->field_key, $mids);
                        
                        if(isset($_POST['item_meta'][$field->id]) and count($_POST['item_meta'][$field->id]) == 1 and $_POST['item_meta'][$field->id] != $mids)
                            $frm_detached_media[] = $_POST['item_meta'][$field->id];

                    }
                    
                    $_POST['item_meta'][$field->id] = $mids;
                    global $frm_media_id;
                    $frm_media_id[$field->id] = $mids;
                    
                    if(isset($_POST['frm_wp_post']) and isset($field->field_options['post_field']) and $field->field_options['post_field'])
                        $_POST['frm_wp_post_custom'][$field->id .'='. $field->field_options['custom_field']] = $mids;

                }
                
            }

            if($field->type == 'tag'){
                $tax_type = (isset($field->field_options['taxonomy']) and !empty($field->field_options['taxonomy'])) ? $field->field_options['taxonomy'] : 'frm_tag';
                
                $tags = explode(',', $_POST['item_meta'][$field->id]);
                $terms = array();
                
                if(isset($_POST['frm_wp_post']))
                    $_POST['frm_wp_post'][$field->id.'=tags_input'] = $tags;
                    
                if($tax_type == 'frm_tag'){
                 
                    foreach($tags as $tag){
                        $slug = sanitize_title(stripslashes($tag));
                        if(!isset($_POST['frm_wp_post'])){
                            if(function_exists('term_exists'))
                                $exists = term_exists($slug, $tax_type);
                            else
                                $exists = is_term($slug, $tax_type);

                            if(!$exists)
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
        if(FrmProFormsHelper::going_to_prev($field->form_id))
            return array();
        
        global $frm_field, $frm_show_fields, $frm_settings, $frmpro_field;
        $field->field_options = maybe_unserialize($field->field_options);
        
        if(in_array($field->type, array('break', 'html', 'divider'))){
            $hidden = $frmpro_field->is_field_hidden($field, $_POST);
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
            $errors['field'. $field->id] = (!isset($field->field_options['blank']) or $field->field_options['blank'] == '' or $field->field_options['blank'] == 'Untitled cannot be blank') ? $frm_settings->blank_msg : $field->field_options['blank']; 
        }
        
        //Don't require fields hidden with shortcode fields="25,26,27"
        if(!empty($frm_show_fields) and is_array($frm_show_fields) and $field->required == '1' and isset($errors['field'. $field->id]) and !in_array($field->id, $frm_show_fields) and !in_array($field->field_key, $frm_show_fields)){
            unset($errors['field'. $field->id]);
            $_POST['item_meta'][$field->id] = $value = '';
        }
        
        //Don't require a conditionally hidden field
        if (isset($field->field_options['hide_field']) and !empty($field->field_options['hide_field'])){
            if($frmpro_field->is_field_hidden($field, $_POST)){
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
                if($this->value_exists($field->id, $value, $entry_id))
                    $errors['field'.$field->id] = FrmProFieldsHelper::get_error_msg($field, 'unique_msg');
                $value = $old_value;
            }else{
                if($this->value_exists($field->id, $value, $entry_id))
                    $errors['field'.$field->id] = FrmProFieldsHelper::get_error_msg($field, 'unique_msg');
            }
            unset($entry_id);
        }
        
        $errors = $this->set_post_fields($field, $value, $errors);
        
        if (!$frmpro_field->is_visible_to_user($field) and !is_admin()){
            //don't validate admin only fields that can't be seen
            unset($errors['field'. $field->id]);
            return $errors;
        }
        
        //if the field is a file upload, check for a file
        if($field->type == 'file' and isset($_FILES['file'. $field->id]) and !empty($_FILES['file'. $field->id]['name'])){
            $filled = true;
            if(is_array($_FILES['file'. $field->id]['name'])){
                $filled = false;
                foreach($_FILES['file'. $field->id]['name'] as $n){
                    if(!empty($n))
                        $filled = true;
                }
            }

            if($filled){
                unset($errors['field'. $field->id]);
                if(isset($field->field_options['restrict']) and $field->field_options['restrict'] and isset($field->field_options['ftypes']) and !empty($field->field_options['ftypes'])){
                    $mimes = $field->field_options['ftypes'];
                }else{
                    $mimes = null;
                }

                //check allowed mime types for this field
                if(is_array($_FILES['file'. $field->id]['name'])){
                    foreach($_FILES['file'. $field->id]['name'] as $name){
                        if(empty($name))
                            continue;

                        $file_type = wp_check_filetype( $name, $mimes );
                        unset($name);

                        if(!$file_type['ext'])
                            break;
                    }
                }else{
                    $file_type = wp_check_filetype( $_FILES['file'. $field->id]['name'], $mimes );
                } 

                if(isset($file_type) and !$file_type['ext'])
                    $errors['field'. $field->id] = ($field->field_options['invalid'] == __('This field is invalid', 'formidable') or $field->field_options['invalid'] == '' or $field->field_options['invalid'] == $field->name.' '. __('is invalid', 'formidable')) ? __('Sorry, this file type is not permitted for security reasons.', 'formidable') : $field->field_options['invalid'];

                unset($file_type);
            }
        }else if($field->type == 'user_id'){
            //add user id to post variables to be saved with entry
            $_POST['frm_user_id'] = $value;
        }
        
        if($field->type == 'website' or $field->type == 'url' or $field->type == 'image'){
            if(trim($value) == 'http://'){
                $_POST['item_meta'][$field->id] = $value = '';
            }else if(!empty($value)){    
                $value = esc_url_raw( $value );
		        $_POST['item_meta'][$field->id] = $value = preg_match('/^(https?|ftps?|mailto|news|feed|telnet):/is', $value) ? $value : 'http://'. $value;
	        }
		}
		
		if(false and isset($field->field_options['use_calc']) and !empty($field->field_options['use_calc']) and !empty($field->field_options['calc'])){
		    $field->field_options['calc'] = trim($field->field_options['calc']);
		    preg_match_all( "/\[(.*?)\]/s", $field->field_options['calc'], $calc_matches, PREG_PATTERN_ORDER);
		    if(isset($calc_matches[1])){
		        foreach($calc_matches[1] as $c){
		            if(is_numeric($c)){
		                $c_id = $c;
		            }else{
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
		        
		        include_once(FRMPRO_PATH .'/classes/helpers/FrmProMathHelper.php');
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
            (($field->type == 'website' or $field->type == 'url' or $field->type == 'image') and !preg_match('/^http(s)?:\/\/([\da-z\.-]+)\.([\da-z\.-]+)/i', $value)) or 
            ($field->type == 'phone' and !preg_match('/^((\+\d{1,3}(-|.| )?\(?\d\)?(-| |.)?\d{1,5})|(\(?\d{2,6}\)?))(-|.| )?(\d{3,4})(-|.| )?(\d{4})(( x| ext)\d{1,5}){0,1}$/', $value))){
            $errors['field'. $field->id] = FrmProFieldsHelper::get_error_msg($field, 'invalid');
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
        $field->field_options = maybe_unserialize($field->field_options);
        
        if($field->type == 'file'){
            global $frm_media_id;
            $frm_media_id[$field->id] = $value;
        }
        
        if(isset($field->field_options['post_field']) and $field->field_options['post_field'] != ''){
            global $frmpro_settings;
            
            if ($value and !empty($value) and isset($field->field_options['unique']) and $field->field_options['unique']){
                global $frmdb;
                
                $entry_id = (isset($_POST) and isset($_POST['id'])) ? $_POST['id'] : false;
                if($entry_id)
                    $post_id = $frmdb->get_var($frmdb->entries, array('id' => $entry_id), 'post_id');
                else
                    $post_id = false;
                    
                if(isset($errors) and $this->post_value_exists($field->field_options['post_field'], $value, $post_id, $field->field_options['custom_field'])){
                    $errors['field'.$field->id] = FrmProFieldsHelper::get_error_msg($field, 'unique_msg');
                }
                    
                unset($entry_id);
                unset($post_id);
            }
            
            if($field->field_options['post_field'] == 'post_custom'){
                if ($field->type == 'date' and !preg_match('/^\d{4}-\d{2}-\d{2}/', trim($value)))
                    $value = FrmProAppHelper::convert_date($value, $frmpro_settings->date_format, 'Y-m-d');
                    
                $_POST['frm_wp_post_custom'][$field->id.'='.$field->field_options['custom_field']] = $value;
                
            }else{
                if($field->field_options['post_field'] == 'post_date'){
                    if (!preg_match('/^\d{4}-\d{2}-\d{2}/', trim($value)))
                        $value = FrmProAppHelper::convert_date($value, $frmpro_settings->date_format, 'Y-m-d H:i:s');
                }else if($field->type != 'tag' and $field->field_options['post_field'] == 'post_category'){
                    $value = (array)$value;
                    if(isset($field->field_options['taxonomy']) and $field->field_options['taxonomy'] != 'category'){
                        $new_value = array();
                        foreach($value as $val){
                            if($val == 0)
                                continue;
                            
                            $term = get_term($val, $field->field_options['taxonomy']);

                            if(!isset($term->errors))
                                $new_value[$val] = $term->name;
                            else
                                $new_value[$val] = $val;
                                
                        }
                        
                        if(!isset($_POST['frm_tax_input']))
                            $_POST['frm_tax_input'] = array();

                        if(isset($_POST['frm_tax_input'][$field->field_options['taxonomy']])){
                            foreach($new_value as $new_key => $new_name)
                                $_POST['frm_tax_input'][$field->field_options['taxonomy']][$new_key] = $new_name;
                        }else{
                            $_POST['frm_tax_input'][$field->field_options['taxonomy']] = $new_value;
                        }
                    }else{
                        $_POST['frm_wp_post'][$field->id.'='.$field->field_options['post_field']] = $value;
                    }
                    
                }else if($field->type == 'tag' and $field->field_options['post_field'] == 'post_category'){
                    $value = trim($value);
                    $value = array_map('trim', explode(',', $value));
                    
                    $tax_type = (isset($field->field_options['taxonomy']) and !empty($field->field_options['taxonomy'])) ? $field->field_options['taxonomy'] : 'frm_tag';

                    if(!isset($_POST['frm_tax_input']))
                        $_POST['frm_tax_input'] = array();
                    
                    if ( is_taxonomy_hierarchical($tax_type) ){
                        //create the term or check to see if it exists
                        $terms = array();
                        foreach($value as $v){
                            if(function_exists('term_exists'))
                                $term_id = term_exists($v, $tax_type);
                            else
                                $term_id = is_term($v, $tax_type);

                            if(!$term_id)
                                $term_id = wp_insert_term($v, $tax_type);

                            if($term_id and is_array($term_id))  
                               $term_id = $term_id['term_id'];
                            
                            if(is_numeric($term_id))
                                $terms[$term_id] = $v;

                            unset($term_id);
                            unset($v);
                        }
                        
                        $value = $terms;
                        unset($terms);
                    }
                    
                    if(!isset($_POST['frm_tax_input'][$tax_type]))
                        $_POST['frm_tax_input'][$tax_type] = (array)$value;
                    else
                        $_POST['frm_tax_input'][$tax_type] += (array)$value;
                }

            	if($field->field_options['post_field'] != 'post_category')
                    $_POST['frm_wp_post'][$field->id.'='.$field->field_options['post_field']] = $value;
            }
        }
        
        if(isset($errors))
            return $errors;
    }
    
    function meta_through_join($hide_field, $selected_field, $observed_field_val){
        if (!is_numeric($observed_field_val) and !is_array($observed_field_val)) return array();
        global $frm_field, $frm_entry_meta;
        
        $observed_info = $frm_field->getOne($hide_field);
        
        if($selected_field)
            $join_fields = $frm_field->getAll(array('fi.form_id' => $selected_field->form_id, 'type' => 'data'));

        if(isset($join_fields) and $join_fields){
            foreach ($join_fields as $jf){
                if (isset($jf->field_options['form_select']) and isset($observed_info->field_options['form_select']) and $jf->field_options['form_select'] == $observed_info->field_options['form_select'])
                    $join_field = $jf->id;
            }
            
            if(isset($join_field)){
                $observed_field_val = (array) $observed_field_val;
                $query = "(it.meta_value in (". implode(',', $observed_field_val) .")";
                if(is_array($observed_field_val)){
                    foreach($observed_field_val as $obs_val)
                        $query .= " or it.meta_value LIKE '%s:". strlen($obs_val). ":\"". $obs_val ."\"%'"; 
                }else{
                    $query .= " or it.meta_value LIKE '%s:". strlen($observed_field_val). ":\"". $observed_field_val ."\"%'"; 
                }
                $query .= ") and field_id ='$join_field'";
                $entry_ids = $frm_entry_meta->getEntryIds($query);
            }
        }
        
        if (isset($entry_ids) and !empty($entry_ids))
            $metas = $frm_entry_meta->getAll("item_id in (".implode(',', $entry_ids).") and field_id=". $selected_field->id, ' ORDER BY meta_value');
        else
            $metas = array();
            
        return $metas;
    }
    
    function value_exists($field_id, $value, $entry_id=false){
        global $wpdb, $frmdb;
        $query = "SELECT id FROM $frmdb->entry_metas WHERE meta_value='$value' and field_id=$field_id";
        if($entry_id)
            $query .= " and item_id != ". $entry_id;
        return $wpdb->get_var($query);
    }
    
    function post_value_exists($post_field, $value, $post_id, $custom_field=''){
        global $wpdb;
        if($post_field == 'post_custom'){
            $query = "SELECT post_id FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts p ON (p.ID=pm.post_id) WHERE meta_value='$value' and meta_key='$custom_field'";
            if($post_id and is_numeric($post_id))
                $query .= " and post_id != ". $post_id;
        }else{
            $query = "SELECT ID FROM $wpdb->posts WHERE `$post_field`='$value'";
            if($post_id and is_numeric($post_id))
                $query .= " and ID != ". $post_id;
        }
        $query .= " and post_status in ('publish','draft','pending','future')";

        return $wpdb->get_var($query);
    }
    
    function &get_max($field){
        global $wpdb, $frmdb;
        
        if(!is_object($field)){
            global $frm_field;
            $field = $frm_field->getOne($field);
        }
        
        if(!$field)
            return;
            
        $query = "SELECT meta_value +0 as odr FROM $frmdb->entry_metas WHERE field_id='{$field->id}' ORDER BY odr DESC LIMIT 1";
        $max = $wpdb->get_var($query);
        
        if(isset($field->field_options['post_field']) and $field->field_options['post_field'] == 'post_custom'){
            $post_max = $wpdb->get_var($wpdb->prepare("SELECT meta_value +0 as odr FROM $wpdb->postmeta WHERE meta_key= %s ORDER BY odr DESC LIMIT 1", $field->field_options['custom_field']));
            if($post_max and (float)$post_max > (float)$max)
                $max = $post_max;
        }
        
        return $max;
    }
}
