<?php

class FrmProFieldsHelper{
    function FrmProFieldsHelper(){
        add_filter('frm_get_default_value', 'FrmProFieldsHelper::get_default_value', 10, 3);
        add_filter('frm_setup_edit_field_vars', 'FrmProFieldsHelper::setup_new_field_vars');
        add_filter('frm_setup_new_fields_vars', 'FrmProFieldsHelper::setup_new_vars', 10, 2);
        add_filter('frm_setup_edit_fields_vars', 'FrmProFieldsHelper::setup_edit_vars', 10, 3);
        add_filter('frm_posted_field_ids', 'FrmProFieldsHelper::posted_field_ids');
        add_action('frm_after_checkbox', 'FrmProFieldsHelper::get_child_checkboxes');
        add_filter('frm_get_paged_fields', 'FrmProFieldsHelper::get_form_fields', 10, 3);
        add_filter('frm_get_current_page', 'FrmProFieldsHelper::get_current_page', 10, 3);
        add_filter('frm_show_custom_html', 'FrmProFieldsHelper::show_custom_html', 10, 2);
        add_filter('frm_other_custom_html', 'FrmProFieldsHelper::get_default_html', 10, 2);
        add_filter('frm_conditional_value', 'FrmProFieldsHelper::conditional_replace_with_value', 10, 4);
        add_filter('frm_before_replace_shortcodes', 'FrmProFieldsHelper::before_replace_shortcodes', 10, 2);
        add_filter('frm_replace_shortcodes', 'FrmProFieldsHelper::replace_html_shortcodes', 10, 2);
        add_filter('frm_display_entry_content', 'FrmProFieldsHelper::replace_shortcodes', 10, 6);
    }
    
    public static function get_default_value($value, $field, $dynamic_default=true, $return_array=false){
        if (is_array(maybe_unserialize($value))) return $value;

        if($field and $dynamic_default){
            $field->field_options = maybe_unserialize($field->field_options);
            if(isset($field->field_options['dyn_default_value']) and !empty($field->field_options['dyn_default_value'])){
                $prev_val = $value;
                $value = $field->field_options['dyn_default_value'];
            }
        }

        preg_match_all( "/\[(date|time|email|login|display_name|first_name|last_name|user_meta|user_id|post_meta|post_id|post_title|post_author_email|ip|auto_id|siteurl|sitename|get|get-(.?))\b(.*?)(?:(\/))?\]/s", $value, $matches, PREG_PATTERN_ORDER);

        if (!isset($matches[0])) return do_shortcode($value);

        foreach ($matches[0] as $match_key => $val){
            switch($val){
                case '[date]':
                    global $frmpro_settings;
                    $new_value = date_i18n($frmpro_settings->date_format, strtotime(current_time('mysql')));
                    break;
                case '[time]':
                    $new_value = date('H:i:s', strtotime(current_time('mysql')));
                    break;
                case '[email]':
                    global $current_user;
                    $new_value = (isset($current_user->user_email)) ? $current_user->user_email : '';
                    break;
                case '[login]':
                    global $current_user;
                    $new_value = (isset($current_user->user_login)) ? $current_user->user_login : '';
                    break;
                case '[display_name]':
                    global $current_user;
                    $new_value = (isset($current_user->display_name)) ? $current_user->display_name : '';
                    break;
                case '[first_name]':
                    global $current_user;
                    $new_value = (isset($current_user->user_firstname)) ? $current_user->user_firstname : '';
                    break;
                case '[last_name]':
                    global $current_user;
                    $new_value = (isset($current_user->user_lastname)) ? $current_user->user_lastname : '';
                    break;
                case '[post_id]':
                    global $post;
                    if($post)
                        $new_value = $post->ID;
                    break;
                case '[post_title]':
                    global $post;
                    if($post)
                        $new_value = $post->post_title;
                    break;
                case '[post_author_email]':
                    $new_value = get_the_author_meta('user_email');
                    break;
                case '[user_id]':
                    global $user_ID;
                    $new_value = $user_ID ? $user_ID : '';
                    break;
                case '[ip]':
                    $new_value = $_SERVER['REMOTE_ADDR'];
                    break;
                case '[siteurl]':
                    global $frm_siteurl;
                    $new_value = $frm_siteurl;
                    break;
                case '[sitename]':
                    $new_value = get_option('blogname');
                    break;
                default:
                    $atts = shortcode_parse_atts(stripslashes($matches[3][$match_key]));
                    if(isset($atts['return_array']))
                        $return_array = $atts['return_array'];
                    
                    $shortcode = $matches[1][$match_key];
                    
                    if (preg_match("/\[get-(.?)\b(.*?)?\]/s", $val)){
                        $param = str_replace('[get-', '', $val);
                        if (preg_match("/\[/s", $param))
                            $val .= ']';
                        else
                            $param = trim($param, ']'); //only if is doesn't create an imbalanced []
                        $new_value = FrmAppHelper::get_param($param);
                        if(is_array($new_value) and !$return_array)
                            $new_value = implode(', ', $new_value);
                    }else{
                        switch($shortcode){
                            case 'get':
                                $new_value = '';
                                if(isset($atts['param'])){
                                    if(strpos($atts['param'], '&#91;')){
                                        $atts['param'] = str_replace('&#91;', '[', $atts['param']);
                                        $atts['param'] = str_replace('&#93;', ']', $atts['param']);
                                    }
                                    $new_value = FrmAppHelper::get_param($atts['param'], false);
                                    if(!$new_value){
                                        global $wp_query;
                                        if(isset($wp_query->query_vars[$atts['param']]))
                                            $new_value = $wp_query->query_vars[$atts['param']];
                                    } 
                                    if(!$new_value and isset($atts['default']))
                                        $new_value = $atts['default'];
                                    else if(!$new_value and isset($prev_val))
                                        $new_value = $prev_val;
                                }

                                if(is_array($new_value) and !$return_array)
                                    $new_value = implode(', ', $new_value);
                            break;
                            case'auto_id':
                                global $frmpro_entry_meta;

                                $last_entry = $frmpro_entry_meta->get_max($field);

                                if(!$last_entry and isset($atts['start']))
                                    $new_value = (int)$atts['start'];

                                if (!isset($new_value)) $new_value = $last_entry + 1;
                            break;
                            case 'user_meta':
                                if(isset($atts['key'])){
                                    global $current_user;
                                    $new_value = (isset($current_user->{$atts['key']})) ? $current_user->{$atts['key']} : '';
                                    if(is_array($new_value))
                                        $new_value = implode(', ', $new_value);
                                }
                            break;
                            case 'post_meta':
                                if(isset($atts['key'])){
                                    global $post;
                                    if($post){
                                        if(isset($post->{$atts['key']}))
                                            $post_meta = $post->{$atts['key']};
                                        else
                                            $post_meta = get_post_meta($post->ID, $atts['key'], true);
                                        if($post_meta)
                                            $new_value = $post_meta;
                                    }
                                }
                            break;
                            default:
                                //check for posted item_meta
                                if(is_numeric($shortcode) and isset($_REQUEST) and isset($_REQUEST['item_meta'])){ 
                                    $new_value = FrmAppHelper::get_param('item_meta['. $shortcode .']', false, 'post');

                                    if(!$new_value and isset($atts['default']))
                                        $new_value = $atts['default'];

                                    if(is_array($new_value) and !$return_array)
                                        $new_value = implode(', ', $new_value);
                                }else{
                                    //don't replace this if it's a shortcode that still needs to be processed
                                    $new_value = $val;
                                }
                            break;
                        }
                    }
            }            
            
            if (!isset($new_value)) $new_value = '';
            
            if(is_array($new_value)){
                if(count($new_value) === 1)
                    $new_value = reset($new_value);
                $value = $new_value;
            }else{
                $value = str_replace($val, $new_value, $value);
            }
                
            unset($new_value);
        }
        
        unset($matches);
        preg_match_all( "/\[(\d*)\b(.*?)(?:(\/))?\]/s", $value, $matches, PREG_PATTERN_ORDER);
        if (isset($matches[0])){
            foreach ($matches[0] as $match_key => $val){
                $shortcode = $matches[1][$match_key];
                if(is_numeric($shortcode) and isset($_REQUEST) and isset($_REQUEST['item_meta'])){ 
                    $new_value = FrmAppHelper::get_param('item_meta['. $shortcode .']', false, 'post');

                    if(!$new_value and isset($atts['default']))
                        $new_value = $atts['default'];

                    if(is_array($new_value) and !$return_array)
                        $new_value = implode(', ', $new_value);
                        
                    if(is_array($new_value))
                        $value = $new_value;
                    else
                        $value = str_replace($val, $new_value, $value);
                }
            }
        }
        
        global $frm_skip_shortcode;
        $frm_skip_shortcode = true;
                
        $value = do_shortcode($value);
        
        $frm_skip_shortcode = false;
        return $value;
    }
    
    public static function setup_new_field_vars($values){
        $values['field_options'] = maybe_unserialize($values['field_options']);
        $defaults = FrmProFieldsHelper::get_default_field_opts($values);
        
        foreach ($defaults as $opt => $default)
            $values[$opt] = (isset($values['field_options'][$opt])) ? $values['field_options'][$opt] : $default;
        
        unset($defaults);
            
        if(!empty($values['hide_field']) and !is_array($values['hide_field']))
            $values['hide_field'] = (array)$values['hide_field'];
        
        return $values;
    }
    
    public static function setup_new_vars($values, $field){
        $values['use_key'] = false;

        $field->field_options = maybe_unserialize($field->field_options);
        foreach (self::get_default_field_opts($values, $field) as $opt => $default)
            $values[$opt] = (isset($field->field_options[$opt]) && $field->field_options[$opt] != '') ? $field->field_options[$opt] : $default;
            
        $values['hide_field'] = (array)$values['hide_field'];    
        $values['hide_field_cond'] = (array)$values['hide_field_cond'];
        $values['hide_opt'] = (array)$values['hide_opt'];

        if ($values['type'] == 'data' && in_array($values['data_type'], array('select', 'radio', 'checkbox')) && is_numeric($values['form_select'])){
            global $frm_entry_meta;
            $check = self::check_data_values($values);

            if($check)
                $values['options'] = self::get_linked_options($values, $field);
            else if(is_numeric($values['value']))
                $values['options'] = array($values['value'] => $frm_entry_meta->get_entry_meta_by_field($values['value'], $values['form_select']));
            
            unset($check);
        }else if ($values['type'] == '10radio' or $values['type'] == 'scale'){
            $values['minnum'] = 1;
            $values['maxnum'] = 10;
        }else if ($values['type'] == 'date'){
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $values['value'])){ 
                global $frmpro_settings;
                $values['value'] = FrmProAppHelper::convert_date($values['value'], 'Y-m-d', $frmpro_settings->date_format);
            }
        }else if($values['type'] == 'user_id' and is_admin() and current_user_can('frm_edit_entries') and ($_GET['page'] != 'formidable')){
            global $user_ID;
            $values['type'] = 'select';
            $values['options'] = self::get_user_options();
            $values['use_key'] = true;
            $values['custom_html'] = FrmFieldsHelper::get_default_html('select');
            $values['value'] = $user_ID;
        }else if(!empty($values['options'])){
            foreach($values['options'] as $val_key => $val_opt){
                if(is_array($val_opt)){
                    foreach($val_opt as $opt_key => $opt){
                        $values['options'][$val_key][$opt_key] = self::get_default_value($opt, $field, false);
                        unset($opt_key);
                        unset($opt);
                    }
                }else{
                   $values['options'][$val_key] = self::get_default_value($val_opt, $field, false);
                }
                unset($val_key);
                unset($val_opt);
            }
        }
        
        if($values['post_field'] == 'post_category'){
            $values['use_key'] = true;
            if($values['type'] == 'data' and $values['data_type'] == 'select' and !$values['multiple'])
                $values['options'] = array('') + self::get_category_options($values);
            else
                $values['options'] = self::get_category_options($values);
        }else if($values['post_field'] == 'post_status'){
            $values['use_key'] = true;
            $values['options'] = self::get_status_options($field);
        }
        
        if(is_array($values['value'])){
            foreach($values['value'] as $val_key => $val)
                $values['value'][$val_key] = apply_filters('frm_get_default_value', $val, $field);
        }else if(!empty($values['value'])){
            $values['value'] = apply_filters('frm_get_default_value', $values['value'], $field);
        }
        
        FrmProFieldsHelper::setup_conditional_fields($values);
        
        return $values;
    }
        
    public static function setup_edit_vars($values, $field, $entry_id=false){
        $values['use_key'] = false;
   
        $field->field_options = maybe_unserialize($field->field_options);
        foreach (self::get_default_field_opts($values, $field) as $opt => $default){
            $values[$opt] = ($_POST and isset($_POST['field_options'][$opt.'_'.$field->id]) ) ? stripslashes_deep($_POST['field_options'][$opt.'_'.$field->id]) : (isset($field->field_options[$opt]) ? $field->field_options[$opt]: $default);
        }

        $values['hide_field'] = (array)$values['hide_field'];    
        $values['hide_field_cond'] = (array)$values['hide_field_cond'];
        $values['hide_opt'] = (array)$values['hide_opt'];

        if ($values['type'] == 'data' && in_array($values['data_type'], array('select', 'radio', 'checkbox')) && is_numeric($values['form_select'])){
            global $frm_entry_meta;
            $check = self::check_data_values($values);

            if($check)
                $values['options'] = self::get_linked_options($values, $field, $entry_id);
            else if(is_numeric($values['value']))
                $values['options'] = array($values['value'] => $frm_entry_meta->get_entry_meta_by_field($values['value'], $values['form_select']));
            unset($check);
        }else if ($values['type'] == 'date'){
            global $frmpro_settings;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $values['value']))
                $values['value'] = FrmProAppHelper::convert_date($values['value'], 'Y-m-d', $frmpro_settings->date_format);
            else if (preg_match('/^\d{4}-\d{2}-\d{2}/', $values['value']))
                $values['value'] = FrmProAppHelper::convert_date($values['value'], 'Y-m-d H:i:s', $frmpro_settings->date_format);
        }else if ($values['type'] == 'file'){
            //if (isset($_POST)) ???
            if($values['post_field'] != 'post_custom'){
                global $frm_entry_meta;
                $values['value'] = $frm_entry_meta->get_entry_meta_by_field($entry_id, $values['id']);
            }
        }else if($values['type'] == 'hidden' and is_admin() and is_super_admin() and (!isset($_GET['page']) or $_GET['page'] != 'formidable')){
            global $frmpro_field;
            if($frmpro_field->on_current_page($field)){
                $values['type'] = 'text';
                $values['custom_html'] = FrmFieldsHelper::get_default_html('text');
            }
        }else if($values['type'] == 'user_id' and is_admin() and current_user_can('frm_edit_entries') and (!isset($_GET['page']) or $_GET['page'] != 'formidable')){
            $values['type'] = 'select';
            $values['options'] = self::get_user_options();
            $values['use_key'] = true;
            $values['custom_html'] = FrmFieldsHelper::get_default_html('select');
        }else if($values['type'] == 'tag'){
            if(empty($values['value'])){
                global $wpdb, $frmdb;
                $post_id = $wpdb->get_var("SELECT post_id FROM $frmdb->entries WHERE id=$entry_id");
                if($post_id and ($tags = get_the_terms( $post_id, $values['taxonomy'] ))){
                    $names = array();
                    foreach($tags as $tag)
                        $names[] = $tag->name;
                    $values['value'] = implode(', ', $names);
                }
            }
        }else if(!empty($values['options']) and (!is_admin() or (isset($_GET) and isset($_GET['page']) and $_GET['page'] != 'formidable'))){
            foreach($values['options'] as $val_key => $val_opt){
                if(is_array($val_opt)){
                    foreach($val_opt as $opt_key => $opt){
                        $values['options'][$val_key][$opt_key] = self::get_default_value($opt, $field, false);
                        unset($opt_key);
                        unset($opt);
                    }
                }else{
                   $values['options'][$val_key] = self::get_default_value($val_opt, $field, false);
                }
                unset($val_key);
                unset($val_opt);
            }
        }
        
        if($values['post_field'] == 'post_category'){
            $values['use_key'] = true;
            if($values['type'] == 'data' and $values['data_type'] == 'select' and !$values['multiple'])
                $values['options'] = array('') + self::get_category_options($values);
            else
                $values['options'] = self::get_category_options($values);
        }else if($values['post_field'] == 'post_status'){
            $values['use_key'] = true;
            $values['options'] = self::get_status_options($field);
        }
        
        FrmProFieldsHelper::setup_conditional_fields($values);

        return $values;
    }
    
    public static function get_default_field_opts($values=false, $field=false){
        global $frmpro_settings, $frm_settings;
        
        $minnum = 1;
        $maxnum = 10;
        $step = 1;
        $align = 'block';
        $show_hide = 'show';
        if($values){
            if($values['type'] == 'number'){
                $minnum = 0;
                $maxnum = 9999;
            }else if($values['type'] == '10radio' or $values['type'] == 'scale' and $field){
                $range = maybe_unserialize($field->options);
                $minnum = $range[0];
                $maxnum = end($range);
            }else if ($values['type'] == 'time'){
                $step = 30;
            }else if($values['type'] == 'radio'){
                $align = $frmpro_settings->radio_align;
            }else if($values['type'] == 'checkbox'){
                $align = $frmpro_settings->check_align;
            }else if($values['type'] == 'break'){
                $show_hide = 'hide';
            }
        }
        $end_minute = 60 - (int)$step;
        
        unset($values);
        unset($field);
        
        return array(
            'slide' => 0, 'form_select' => '', 'show_hide' => $show_hide, 'any_all' => 'any', 'align' => $align,
            'hide_field' => array(), 'hide_field_cond' =>  array('=='), 'hide_opt' => array(), 'star' => 0,
            'post_field' => '', 'custom_field' => '', 'taxonomy' => 'category', 'exclude_cat' => 0, 'ftypes' => array(),
            'data_type' => '', 'restrict' => 0, 'start_year' => 2000, 'end_year' => 2020, 'read_only' => 0, 
            'admin_only' => '', 'locale' => '', 'attach' => false, 'minnum' => $minnum, 'maxnum' => $maxnum,
            'step' => $step, 'clock' => 12, 'start_time' => '00:00', 'end_time' => '23:'.$end_minute, 
            'dependent_fields' => 0, 'unique' => 0, 'use_calc' => 0, 'calc' => '', 'duplication' => 1, 'rte' => 'mce',
            'dyn_default_value' => '', 'multiple' => 0, 'unique_msg' => $frm_settings->unique_msg, 'autocom' => 0
        );
    }
    
    public static function check_data_values($values){
        global $frm_field;
        $check = true;
        if(!empty($values['hide_field']) and (!empty($values['hide_opt']) or !empty($values['form_select']))){
            foreach($values['hide_field'] as $hkey => $f){
                if(!$check or !empty($values['hide_opt'][$hkey])) continue;
                $f = $frm_field->getOne($f);
                if($f and $f->type == 'data')
                    $check = false;
                unset($f);
                unset($hkey);
            }
        }
        return $check;
    }
    
    
    public static function setup_conditional_fields($field){
        if(is_admin() and (isset($_GET) and isset($_GET['page']) and $_GET['page'] == 'formidable'))
            return;
            
        if(!empty($field['hide_field']) and (!empty($field['hide_opt']) or !empty($field['form_select']))){
                
            global $frm_rules, $frm_field;
            if(!$frm_rules)
                $frm_rules = array();

            $conditions = array();
            
            if(!isset($field['show_hide']))
                $field['show_hide'] = 'show';
            
            if(!isset($field['any_all']))
                $field['any_all'] = 'any';
                
            foreach($field['hide_field'] as $i => $cond){
                if(!is_numeric($cond))
                    continue;
                    
                $parent_field = $frm_field->getOne($cond);

                if(!$parent_field)
                    continue;
                
                $parent_opts = maybe_unserialize($parent_field->field_options);
                
                if(empty($conditions)){  
                    foreach($field['hide_field'] as $i2 => $cond2){
                        if(!is_numeric($cond2))
                            continue;
                            
                        if((int)$cond2 == (int)$parent_field->id){ 
                            $sub_field = $parent_field;
                            $sub_opts = $parent_opts;
                        }else{
                            $sub_field = $frm_field->getOne($cond2);
                            if($sub_field)
                                $sub_opts = maybe_unserialize($sub_field->field_options);
                        }
                            
                        $condition = array('FieldName' => $sub_field->id, 'Condition' => $field['hide_field_cond'][$i2]);

                        if($sub_field->type == 'data' and $field['type'] == 'data' and (is_numeric($field['form_select']) or $field['form_select'] == 'taxonomy')){
                            $condition['LinkedField'] = $field['form_select'];
                            $condition['DataType'] = $field['data_type'];
                        }else if(isset($field['hide_opt']) and (!empty($field['hide_opt'][$i2]) or $field['hide_opt'][$i2] == 0)){
                            $condition['Value'] = stripslashes(str_replace('"', '&quot;', ( apply_filters('frm_get_default_value', $field['hide_opt'][$i2], $frm_field->getOne($field['id']), false ))));
                        }
                        if($sub_field->type == 'scale') $sub_field->type = 'radio';
                        $condition['Type'] = $sub_field->type . (($sub_field->type == 'data') ? '-'. $sub_opts['data_type'] : '');
                        $conditions[] = $condition;
                    }
                }
                 
                $rule = array('Show' => $field['show_hide'], 'MatchType' => $field['any_all']);

                $rule['Setting'] = array(
                    'FieldName' => $field['id']
                );

                $rule['Conditions'] = $conditions;

                if(!isset($frm_rules[$parent_field->id]))
                    $frm_rules[$parent_field->id] = array();

                $frm_rules[$parent_field->id][] = $rule;
                
                unset($rule);
                unset($parent_field);
                unset($i);
                unset($cond);
            }
        }
    }
    
    public static function get_category_options($field){
        $field = (array)$field;
        $post_type = FrmProForm::post_type($field['form_id']);
        if(!isset($field['exclude_cat']))
            $field['exclude_cat'] = 0;
            
        $exclude = (is_array($field['exclude_cat'])) ? implode(',', $field['exclude_cat']) : $field['exclude_cat'];
        $exclude = apply_filters('frm_exclude_cats', $exclude, $field);
        
        $args = array(
            'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false, 
            'exclude' => $exclude, 'type' => $post_type
        );
        
        if($field['type'] != 'data')
            $args['parent'] = '0';
            
        if(function_exists('get_object_taxonomies')){
            $args['taxonomy'] = FrmProAppHelper::get_custom_taxonomy($post_type, $field);
            if(!$args['taxonomy'])
                return;
        }
        
        $categories = get_categories($args);

        $options = array();
        foreach($categories as $cat)
            $options[$cat->term_id] = $cat->name;
        
        return $options;
    }
    
    public static function get_child_checkboxes($args){
        $defaults = array(
            'field' => 0, 'field_name' => false, 'opt_key' => 0, 'opt' => '', 
            'type' => 'checkbox', 'value' => false, 'exclude' => 0, 'hide_id' => false
        );
        extract(wp_parse_args($args, $defaults));
       
        if(!$field or !isset($field['post_field']) or $field['post_field'] != 'post_category') return;
        if(!$value) $value = (isset($field['value'])) ? $field['value'] : '';
        if(!$exclude) $exclude = (is_array($field['exclude_cat'])) ? implode(',', $field['exclude_cat']) : $field['exclude_cat'];
        if(!$field_name) $field_name = "item_meta[$field[id]]";
        if($type == 'checkbox'){ 
            $field_name .= '[]';
            $onchange = ''; //' onchange="frmCheckParents(this.id)"';
        }else{
            $onchange = '';
        }
        $post_type = FrmProForm::post_type($field['form_id']);
        $taxonomy = 'category';
        
        $args = array(
            'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false, 
            'parent' => $opt_key, 'exclude' => $exclude, 'type' => $post_type
        );
        if(!$opt_key and function_exists('get_object_taxonomies')){
            $args['taxonomy'] = FrmProAppHelper::get_custom_taxonomy($post_type, $field);
            if(!$args['taxonomy']){
                echo '<p>'. __('No Categories', 'formidable' ) .'</p>';
                return;
            }

            $taxonomy = $args['taxonomy'];
        }
            
        $children = get_categories($args);
        $level = ($opt_key) ? 2 : 1;
    	foreach($children as $key => $cat){  ?> 	    
    	<div class="frm_catlevel_<?php echo $level ?>"><?php FrmProFieldsHelper::_show_category(compact('cat', 'field', 'field_name', 'exclude', 'type', 'value', 'exclude', 'level', 'onchange', 'post_type', 'taxonomy', 'hide_id')) ?></div>
<?php   }
    }
    
    public static function _show_category($atts) {
        extract($atts);
    	if(!is_object($cat)) return;
    	$checked = '';
    	
    	if(is_array($value)) 
    		$checked = (in_array($cat->cat_ID, $value)) ? 'checked="checked" ' : '';
    	else if($cat->cat_ID == $value)
    	    $checked = 'checked="checked" ';
    	else
    	    $checked = '';
    	$class = '';
    	//$class = ' class="frm_selectit"'; //TODO: option to check parent cats
    	$sanitized_name = ((isset($field['id'])) ? $field['id'] : $field['field_options']['taxonomy']).'-'. $cat->cat_ID;
    	
    	?>
    	<div class="frm_<?php echo $type ?>" id="frm_<?php echo $type .'_'. $sanitized_name ?>">
    	    <label<?php echo $class ?> for="field_<?php echo $sanitized_name ?>"><input type="<?php echo $type ?>" name="<?php echo $field_name ?>" <?php echo (isset($hide_id) and $hide_id)? '' : 'id="field_'. $sanitized_name .'"'; ?> value="<?php echo $cat->cat_ID ?>" <?php echo $checked;  do_action('frm_field_input_html', $field); //echo ($onchange); ?> /><?php echo $cat->cat_name ?></label>
<?php
    	$children = get_categories(array('type' => $post_type, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false, 'exclude' => $exclude, 'parent' => $cat->cat_ID, 'taxonomy' => $taxonomy));
    	if($children){ 
    	    $level++;
    	    foreach($children as $key => $cat){ ?>
    	<div class="frm_catlevel_<?php echo $level ?>"><?php FrmProFieldsHelper::_show_category(compact('cat', 'field', 'field_name', 'exclude', 'type', 'value', 'exclude', 'level', 'onchange', 'post_type', 'taxonomy', 'hide_id')) ?></div>
<?php       }
        }
    	echo '</div>';
    }
    
    public static function get_status_options($field){
        $post_type = FrmProForm::post_type($field->form_id);
        $post_type_object = get_post_type_object($post_type);
        $options = array();
        
        if(!$post_type_object)
            return $options;
            
        $can_publish = current_user_can($post_type_object->cap->publish_posts);
        $options = get_post_statuses(); //'draft', pending, publish, private

        if(!$can_publish){ // Contributors only get "Unpublished" and "Pending Review"
        	unset($options['publish']);
        	if(isset($options['future']))
        	    unset($options['future']);
        }
        return $options;
    }
    
    public static function get_user_options(){
        global $wpdb;
        $users = (function_exists('get_users')) ? get_users(array( 'fields' => array('ID','user_login','display_name'), 'blog_id' => $GLOBALS['blog_id'], 'orderby' => 'display_name')) : get_users_of_blog();
        $options = array('' => '');
        foreach($users as $user)
            $options[$user->ID] = (!empty($user->display_name)) ? $user->display_name : $user->user_login;
        return $options;
    }
    
    public static function get_linked_options($values, $field, $entry_id=false){
        global $frm_entry_meta, $user_ID, $frm_field, $frmdb;
            
        $metas = array();
        $selected_field = $frm_field->getOne($values['form_select']);
        if(!$selected_field)
            return array();
        
        $linked_posts = (isset($selected_field->field_options['post_field']) and 
            $selected_field->field_options['post_field'] and 
            $selected_field->field_options['post_field'] != '') ? true : false;
        
        $post_ids = array();

        if (is_numeric($values['hide_field']) and (empty($values['hide_opt']))){
            global $frmpro_entry_meta;

            if (isset($_POST) and isset($_POST['item_meta']))
                $observed_field_val = (isset($_POST['item_meta'][$values['hide_field']])) ? $_POST['item_meta'][$values['hide_field']] : ''; 
            else if($entry_id)
                $observed_field_val = $frm_entry_meta->get_entry_meta_by_field($entry_id, $values['hide_field']);
            else
                $observed_field_val = '';
            
            $observed_field_val = maybe_unserialize($observed_field_val);
            $metas = $frmpro_entry_meta->meta_through_join($values['hide_field'], $selected_field, $observed_field_val);
            
        }else if ($values['restrict'] and $user_ID){
            $entry_user = $user_ID;
            if($entry_id and is_admin()){
                $entry_user = $frmdb->get_var($frmdb->entries, array('id' => $entry_id), 'user_id');
                if(!$entry_user or empty($entry_user))
                    $entry_user = $user_ID;
            }
            
            if (isset($selected_field->form_id)){
                $linked_where = array('form_id' => $selected_field->form_id, 'user_id' => $entry_user);
                if($linked_posts){
                    $post_ids = $frmdb->get_records($frmdb->entries, $linked_where, '', '', 'id, post_id');
                }else{
                    $entry_ids = $frmdb->get_col($frmdb->entries, $linked_where, 'id');
                }
                unset($linked_where);
            }
            
            if (isset($entry_ids) and !empty($entry_ids))
                $metas = $frm_entry_meta->getAll("it.item_id in (".implode(',', $entry_ids).") and field_id=". (int)$values['form_select'], ' ORDER BY meta_value', '');
        }else{
            $limit = '';
            if(is_admin() and isset($_GET) and isset($_GET['page']) and $_GET['page'] == 'formidable')
                $limit = 500;
            $metas = $frmdb->get_records($frmdb->entry_metas, array('field_id' => $values['form_select']), 'meta_value', $limit, 'item_id, meta_value');
            $post_ids = $frmdb->get_records($frmdb->entries, array('form_id' => $selected_field->form_id), '', $limit, 'id, post_id');
        }
        
        if($linked_posts and !empty($post_ids)){
            foreach($post_ids as $entry){
                $meta_value = FrmProEntryMetaHelper::get_post_value($entry->post_id, $selected_field->field_options['post_field'], $selected_field->field_options['custom_field'], array('type' => $selected_field->type, 'form_id' => $selected_field->form_id, 'field' => $selected_field));
                $metas[] = array('meta_value' => $meta_value, 'item_id' => $entry->id);
            }
        }

        $options = array();
        foreach ($metas as $meta){
            $meta = (array)$meta;
            if($meta['meta_value'] == '') continue;
            
            if($selected_field->type == 'image')
                $options[$meta['item_id']] = $meta['meta_value'];
            else
                $options[$meta['item_id']] = FrmProEntryMetaHelper::display_value($meta['meta_value'], $selected_field, array('type' => $selected_field->type, 'show_icon' => true, 'show_filename' => false));
            
            unset($meta);
        }

        $options = apply_filters('frm_data_sort', $options, array('metas' => $metas, 'field' => $selected_field));
        unset($metas);
        
        if(!empty($options) and $field->field_options['data_type'] == 'select' and (!isset($field->field_options['multiple']) or empty($field->field_options['multiple'])))
            $options = array('' => '') + $options;
        
        return stripslashes_deep($options);
    }
    
    public static function posted_field_ids($where){
        if (isset($_POST['form_id']) and isset($_POST['frm_page_order_'. $_POST['form_id']]))
            $where .= ' and fi.field_order < '. (int)$_POST['frm_page_order_'. $_POST['form_id']];
        return $where;
    }
    
    public static function get_error_msg($field, $error){
        global $frm_settings;
        $default_settings = $frm_settings->default_options();
        
        $defaults = array(
            'unique_msg' => array('full' => $default_settings['unique_msg'], 'part' => $field->name.' '. __('must be unique', 'formidable')),
            'invalid'   => array('full' => __('This field is invalid', 'formidable'), 'part' => $field->name.' '. __('is invalid', 'formidable'))
        );
        
        $msg = ($field->field_options[$error] == $defaults[$error]['full'] || empty($field->field_options[$error])) ? ($defaults[$error]['part']) : $field->field_options[$error];
        return $msg;
    }
    
    public static function get_form_fields($fields, $form_id, $error=false){
        global $frm_prev_page, $frm_field, $frm_next_page, $frm_page_num;

        $prev_page = FrmAppHelper::get_param('frm_page_order_'. $form_id, false);
        $prev_page = (int)$prev_page;
        
        $go_back = $next_page = false;
        if(FrmProFormsHelper::going_to_prev($form_id)){
            $go_back = true;
            $next_page = FrmAppHelper::get_param('frm_next_page');
            $prev_page = $set_prev = $next_page - 1;
        }
           
        //$current_form_id = FrmAppHelper::get_param('form_id', false);

        //if (is_numeric($current_form_id) and $current_form_id != $form_id)
        //    return $fields;
        $get_last = false;
        if ($error and !$prev_page){
            $prev_page = 999;
            $get_last = true;
        }
        
        $page_breaks = array();
        
        foreach($fields as $f){
            if($f->type == 'captcha')
                $recap = $f;
            
            if ($f->type != 'break')
                continue;
            
            $page_breaks[$f->field_order] = $f;
            if(($prev_page or $go_back) and !$get_last){
                if ((($error or $go_back) and ($f->field_order < $prev_page)) or (!$error and !$go_back and !isset($prev_page_obj) and ($f->field_order == $prev_page))){
                    $prev_page_obj = true;
                    $prev_page = $f->field_order;
                }else if(isset($set_prev) and $f->field_order < $set_prev){
                    $prev_page_obj = true;
                    $prev_page = $f->field_order;
                }else if(($f->field_order > $prev_page) and !isset($set_next) and (!$next_page or is_numeric($next_page))){
                    $next_page = $f;
                    $set_next = true;
                }
                
            }else if($get_last){
                $prev_page_obj = true;
                $prev_page = $f->field_order;
                $next_page = false;
            }else if(!$next_page){
                $next_page = $f;
            }
            unset($f);
        }
        
        if (!isset($prev_page_obj) and $prev_page)
            $prev_page = 0;
        
        if($prev_page){
            $current_page = $page_breaks[$prev_page];
            global $frmpro_field;
            if($frmpro_field->is_field_hidden($current_page, $_POST)){
                $current_page = apply_filters('frm_get_current_page', $current_page, $page_breaks, $go_back);
                if(!$current_page or $current_page->field_order != $prev_page){
                    $prev_page = ($current_page) ? $current_page->field_order : 0;
                    foreach($page_breaks as $o => $pb){
                        if($o > $prev_page){
                            $next_page = $pb;
                            break;
                        }
                    }
                    
                    if($next_page->field_order <= $prev_page)
                        $next_page = false;
                }
            }
        }
        
        if ($prev_page)            
            $frm_prev_page[$form_id] = $prev_page;
        else
            unset($frm_prev_page[$form_id]);
         
        if(!isset($next_page))
            $next_page = false;
        
        if($next_page){
            $frm_next_page[$form_id] = $next_page;
            $next_page = $next_page->field_order;
        }else{
            unset($frm_next_page[$form_id]);
        }
        
        $pages = array_keys($page_breaks);
        $frm_page_num = $prev_page ? (array_search($prev_page, $pages) + 2) : 1;
        
        unset($page_breaks);
        
        if ($next_page or $prev_page){
            foreach($fields as $f){
                if($prev_page and $next_page and ($f->field_order < $prev_page) and ($f->field_order > $next_page)){
                    $f->type = 'hidden';
                }else if($prev_page and $f->field_order < $prev_page){
                    $f->type = 'hidden';
                }else if($next_page and $f->field_order > $next_page){
                    $f->type = 'hidden';
                }
                
                unset($f);
            }
            
            global $frm_settings;
            if(isset($recap) and $recap and !empty($frm_settings->pubkey) and !defined('DOING_AJAX')){
                //check to see if recaptcha script should be loaded on this page
                global $frm_recap_script;
                $frm_recap_script = true;
            }
        }
        
        return $fields;
    }
    
    public static function get_current_page($next_page, $page_breaks, $go_back){
        global $frmpro_field;
        
        $first = $next_page;
        $set_back = false;
        foreach($page_breaks as $o => $pb){
            if(($go_back and $o < $next_page->field_order)){
                $next_page = $pb;
                $set_back = true;
            }else if(!$go_back and $o > $next_page->field_order and ($pb->field_order != $first->field_order)){
                $next_page = $pb;
                break;
            }
            unset($o);
            unset($pb);
        }
        
        if($go_back and !$set_back)
            $next_page = 0;
        
        if($next_page and $frmpro_field->is_field_hidden($next_page, $_POST)){
            if($first == $next_page){
                //TODO: submit form if last page is conditional
            }
            $next_page = self::get_current_page($next_page, $page_breaks, $go_back);
        }
        
        return $next_page;
    }
    
    public static function show_custom_html($show, $field_type){
        if (in_array($field_type, array('hidden', 'user_id', 'break')))
            $show = false;
        return $show;
    }
    
    public static function get_default_html($default_html, $type){
        if ($type == 'divider'){
            $default_html = <<<DEFAULT_HTML
<div id="frm_field_[id]_container" class="frm_form_field frm_section_heading form-field[error_class]">
<h3 class="frm_pos_[label_position][collapse_class]">[field_name]</h3>
[collapse_this]
[if description]<div class="frm_description">[description]</div>[/if description]
</div>
DEFAULT_HTML;
        }else if($type == 'html'){
            $default_html = '<div id="frm_field_[id]_container" class="frm_form_field form-field">[description]</div>';       
        }
        return $default_html;
    }
    
    public static function before_replace_shortcodes($html, $field){
        global $frmpro_settings;
        
        if(isset($field['align']) and ($field['type'] == 'radio' or $field['type'] == 'checkbox')){
            $required_class = '[required_class]';
            if(($field['type'] == 'radio' and $field['align'] != $frmpro_settings->radio_align) or 
                ($field['type'] == 'checkbox' and $field['align'] != $frmpro_settings->check_align)){
                $required_class .= ($field['align'] == 'inline') ? ' horizontal_radio' : ' vertical_radio';
                
                $html = str_replace('[required_class]', $required_class, $html);
            }
        }

        if(isset($field['classes']) and strpos($field['classes'], 'frm_grid') !== false){
            $opt_count = count($field['options']) + 1;
            $html = str_replace('[required_class]', '[required_class] frm_grid_'. $opt_count, $html);
            unset($opt_count);
        }
        
        if($field['type'] == 'html' and isset($field['classes']))
            $html = str_replace('frm_form_field', 'frm_form_field '. $field['classes'], $html);
            
        return $html;
    }
    
    public static function replace_html_shortcodes($html, $field){
        if ($field['type'] == 'divider'){
            global $frm_div;
            $trigger = '';
            $html = str_replace(array('frm_none_container', 'frm_hidden_container', 'frm_top_container', 'frm_left_container', 'frm_right_container'), '', $html);
            $collapse_div = '<div>'."\n";
            if (isset($field['slide']) and $field['slide']){
                $trigger =  ' frm_trigger" onclick="frmToggleSection(jQuery(this));';
                $collapse_div = '<div class="frm_toggle_container" style="display:none;">';
                if(preg_match('/\<\/div\>$/', $html)){
                    if($frm_div and $frm_div != $field['id']){
                        $html = "</div>\n". $html;
                        $frm_div = false;
                    }
                    $frm_div = $field['id'];
                    $html = preg_replace('/\<\/div\>$/', '', $html); //"</div>\n";
                }
            }else if($frm_div and $frm_div != $field['id']){
                $html = "</div>\n". $html;
                $html = preg_replace('/\<\/div\>$/', '', $html); //"</div>\n";
            }
            
            if (preg_match('/\[(collapse_this)\]/s', $html)){
                $html = "</div>\n". $html;
                $html = str_replace('[collapse_this]', $collapse_div, $html);
            }
                
            $html = str_replace('[collapse_class]', $trigger, $html);
        }else if($field['type'] == 'html'){
            if(apply_filters('frm_use_wpautop', true))
                $html = wpautop($html);
            $html = apply_filters('frm_get_default_value', $html, (object)$field, false);
            $html = do_shortcode($html);
        }
        
        if (preg_match('/\[(collapse_this)\]/s', $html))
            $html = str_replace('[collapse_this]', '', $html);
        
        return $html;
    }

    
    public static function get_file_icon($media_id){
        if (!$media_id or !is_numeric($media_id) )
            return;
         
        $attachment = get_post($media_id); 
        if(!$attachment)
            return;
            
        $image = wp_get_attachment_image($media_id, 'thumbnail', true);
        
        if($image and !preg_match("/wp-content\/uploads/", $image)){ //if this is a mime type icon
            $label = basename($attachment->guid);
            $image .= " <span id='frm_media_$media_id' class='frm_upload_label'><a href='". wp_get_attachment_url($media_id) ."'>$label</a></span>";
        }
            
        return $image;
    }
    
    public static function get_file_name($media_ids, $short=true){
        $value = '';
        foreach((array)$media_ids as $media_id){
            if ( is_numeric($media_id) ){
                $attachment = get_post($media_id);
                if(!$attachment)
                    continue;
                
                $url = wp_get_attachment_url($media_id);
                
                if ($short)
                    $label = basename($attachment->guid);
                else
                    $label = $url;

                if(isset($_GET) and isset($_GET['frm_action']) and $_GET['frm_action'] == 'csv'){
                    if(!empty($value))
                        $value .= ', ';
                }else if (is_admin()){
                    $url = '<a href="'. $url .'">'. $label .'</a>';
                    if(isset($_GET) and isset($_GET['page']) and preg_match('/formidable*/', $_GET['page'])){                        
                        global $frm_siteurl;
                        $url .= '<br/><a href="'. $frm_siteurl .'/wp-admin/media.php?action=edit&attachment_id='. $media_id .'">'. __('Edit Uploaded File', 'formidable') .'</a>';
                    }
                }else if(!empty($value)){
                    $value .= "<br/>\r\n";
                }
                
                $value .= $url;
        	}
	    }
	    return $value;
    }
    
    public static function get_data_value($value, $field, $atts=array()){
        global $frm_field;
        if(!is_object($field))
            $field = $frm_field->getOne($field);
        
        $orig_val = $value;
        $linked_field_id = isset($atts['show']) ? $atts['show'] : false;
        $field->field_options = maybe_unserialize($field->field_options);
        
        if (is_numeric($value) and (!isset($field->field_options['form_select']) or $field->field_options['form_select'] != 'taxonomy')){
            if (!$linked_field_id and is_numeric($field->field_options['form_select']))
                $linked_field_id = $field->field_options['form_select'];
            
            if ($linked_field_id){
                global $frm_entry_meta, $frmdb;
                $linked_field = $frm_field->getOne($linked_field_id);
                if($linked_field and isset($linked_field->field_options['post_field']) and $linked_field->field_options['post_field']){
                    global $frmdb;
                    $post_id = $frmdb->get_var($frmdb->entries, array('id' => $value), 'post_id');
                    if($post_id){
                        if(!isset($atts['truncate']))
                            $atts['truncate'] = false;
                            
                        $new_value = FrmProEntryMetaHelper::get_post_value($post_id, $linked_field->field_options['post_field'], $linked_field->field_options['custom_field'], array('form_id' => $linked_field->form_id, 'field' => $linked_field, 'type' => $linked_field->type, 'truncate' => $atts['truncate']));
                    }else{
                        $new_value = $frm_entry_meta->get_entry_meta_by_field($value, $linked_field->id);
                    }
                }else if($linked_field){
                    $new_value = $frm_entry_meta->get_entry_meta_by_field($value, $linked_field->id);
                }else{
                    //no linked field
                    global $wpdb, $frmdb;
                    $user_id = $wpdb->get_var("SELECT user_id FROM $frmdb->entries WHERE id=". (int)$value);
                    if($user_id)
                        $new_value = self::get_display_name($user_id, $linked_field_id, array('blank' => true));
                    else
                        $new_value = '';
                }
                
                $value = (!empty($new_value) or $new_value === 0) ? $new_value : $value;
                
                if($linked_field){
                    if(isset($atts['show']) and !is_numeric($atts['show']))
                        $atts['show'] = $linked_field->id;
                    else if(isset($atts['show']) and ((int)$atts['show'] == $linked_field->id or $atts['show'] == $linked_field->field_key))
                        unset($atts['show']);
                    if(!isset($atts['show']) and isset($atts['show_info']))
                        $atts['show'] = $atts['show_info'];
                    $value = FrmProFieldsHelper::get_display_value($value, $linked_field, $atts); //get display value
                }
            }
        }
        
        if($value == $orig_val and $field->field_options['data_type'] != 'data')
            $value = '';
          
        if(is_array($value))
            $value = implode((isset($atts['show']) ? $atts['show'] : ', '), $value);
            
        return $value;
    }
    
    public static function get_date($date, $date_format=false){
        if(empty($date))
            return $date;
                    
        if (!$date_format)
            $date_format = get_option('date_format');
            
        if (preg_match('/^\d{1-2}\/\d{1-2}\/\d{4}$/', $date)){ 
            global $frmpro_settings;
            $date = FrmProAppHelper::convert_date($date, $frmpro_settings->date_format, 'Y-m-d');
        }
        
        return date_i18n($date_format, strtotime($date));
    }
    
    public static function get_display_name($user_id, $user_info='display_name', $args=array()){
        $defaults = array(
            'blank' => false, 'link' => false, 'size' => 96
        );
        
        extract(wp_parse_args($args, $defaults));
        
        $user = get_userdata($user_id);
        $info = '';
        
        if($user){
            if($user_info == 'avatar'){
                $info = get_avatar( $user_id, $size );
            }else{
                $info = isset($user->$user_info) ? $user->$user_info : '';
            }
                
            if(empty($info) and !$blank)
                $info = $user->user_login;
        }
        
        if($link)
            $info = '<a href="'.  admin_url('user-edit.php') .'?user_id='. $user_id .'">'. $info .'</a>';
            
        return $info;
    }
    
    public static function get_field_options($form_id, $value='', $include='not', $types="'break','divider','data','file','captcha'", $data_children=false){
        global $frm_field;
        $fields = $frm_field->getAll("fi.type $include in ($types) and fi.form_id=". (int)$form_id, 'fi.field_order');
        foreach ($fields as $field){ 
            $field->field_options = maybe_unserialize($field->field_options);
            if($field->type == 'data' and (!isset($field->field_options['data_type']) or $field->field_options['data_type'] == 'data' or $field->field_options['data_type'] == ''))
                continue;
                  
            ?>
            <option value="<?php echo $field->id ?>" <?php selected($value, $field->id) ?>><?php echo FrmAppHelper::truncate($field->name, 50) ?></option>
        <?php   
        }
    }
    
    public static function get_field_stats($id, $type='total', $user_id=false, $value=false, $round=100, $limit='', $atts=array()){
        global $frm_entry_meta, $wpdb, $frmdb, $frm_post_ids, $frm_field;
        
        $field = $frm_field->getOne($id);
        
        if(!$field)
            return 0;
            
        $id = $field->id;
        $field->field_options = maybe_unserialize($field->field_options);  
        
        if($field->type == 'checkbox')
            $where_value = ($value) ? " AND meta_value LIKE '%".addslashes($value)."%'" : '';
        else
            $where_value = ($value) ? " AND meta_value='".addslashes($value)."'" : '';
         
        //if(!$frm_post_ids)
            $frm_post_ids = array();
        
        $post_ids = array();
        
        if(isset($frm_post_ids[$id])){
            $form_posts = $frm_post_ids[$id];
        }else{
            $where_post = array('form_id' => $field->form_id, 'post_id >' => 1);
            if($user_id)
                $where_post['user_id'] = $user_id;

            $form_posts = $frmdb->get_records($frmdb->entries, $where_post, '', '', 'id,post_id');
            
            $frm_post_ids[$id] = $form_posts;
        }

        if($form_posts){
            foreach($form_posts as $form_post)
                $post_ids[$form_post->id] = $form_post->post_id;
        }
        
        if(!empty($limit))
            $limit = " LIMIT ". $limit;
        
        if($value)
            $atts[$id] = $value;
            
        if(!empty($atts)){
            $entry_ids = array();
            
            if(isset($atts['entry_id']) and $atts['entry_id'] and is_numeric($atts['entry_id']))
                $entry_ids[] = $atts['entry_id'];
            
            $after_where = false;
            
            foreach($atts as $orig_f => $val){
                if((strpos($val, '"') === 0 and substr($val, -1) != '"') or (strpos($val, "'") === 0 and substr($val, -1) != "'")){
                    //parse atts back together if they were broken at spaces
                    $next_val = array('char' => substr($val, 0, 1), 'val' => $val);
                    continue;
                }else if(!isset($next_val)){
                    $temp = FrmAppHelper::replace_quotes($val);
                    foreach(array('"', "'") as $q){
                        if(substr($temp, -1) != $q and (strpos($temp, '<'. $q) or strpos($temp, '>'. $q))){
                            $next_val = array('char' => $q, 'val' => $val);
                            $cont = true;
                        }
                        unset($q);
                    }
                    unset($temp);
                    if(isset($cont)){
                        unset($cont);
                        continue;
                    }
                }
                
                if(isset($next_val)){
                    if(substr(FrmAppHelper::replace_quotes($val), -1) == $next_val['char']){
                        $val = $next_val['val'] .' '. $val;
                        unset($next_val);
                    }else{
                        $next_val['val'] .= ' '. $val;
                        continue;
                    }
                }
                
                $entry_ids = FrmProFieldsHelper::get_field_matches(compact('entry_ids', 'orig_f', 'val', 'id', 'atts', 'field', 'form_posts', 'after_where'));
                $after_where = true;
            }
            
            if(empty($entry_ids)){
                if($type == 'star'){
                    $stat = '';
                    ob_start();
                    include(FRMPRO_VIEWS_PATH.'/frmpro-fields/star_disabled.php');
                    $contents = ob_get_contents();
                    ob_end_clean();
                    return $contents;
                }else{
                    return 0;
                }
            }
              
            foreach($post_ids as $entry_id => $post_id){
                if(!in_array($entry_id, $entry_ids))
                    unset($post_ids[$entry_id]);
            }   
            
               
            $where_value .= " AND it.item_id in (". implode(',', $entry_ids).")";
        }
        
        $join = '';
        
        if((is_numeric($id))){
            $where = "field_id='$id'";
        }else{
            $join .= " LEFT OUTER JOIN $frmdb->fields fi ON it.field_id=fi.id";
            $where = "fi.field_key='$id'";
        }
        $where .= $where_value;
        
        if($user_id){
            $where .= " AND en.user_id='$user_id'";
            $join .= " LEFT OUTER JOIN $frmdb->entries en ON en.id=it.item_id";
        }

        $field_metas = $wpdb->get_col("SELECT meta_value FROM $frmdb->entry_metas it $join WHERE $where ORDER BY it.created_at DESC". $limit);
        
        if(!empty($post_ids)){
            if(isset($field->field_options['post_field']) and $field->field_options['post_field']){
                if($field->field_options['post_field'] == 'post_custom'){ //get custom post field value
                    $post_values = $wpdb->get_col($wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key= %s AND post_id in (".implode(',', $post_ids) .")", $field->field_options['custom_field']));
                }else if($field->field_options['post_field'] == 'post_category'){
                    $post_query = "SELECT tr.object_id FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy = %d AND tr.object_id in (". implode(',', $post_ids) .")";
                    $post_query_vars = array($field->field_options['taxonomy']);
                    
                    if($value){
                        $post_query .= ' AND (t.term_id = %s OR t.slug = %s OR t.name = %s)';
                        $post_query_vars[] = $value;
                        $post_query_vars[] = $value;
                        $post_query_vars[] = $value;
                    }
                    
                    $post_values = $wpdb->get_col($wpdb->prepare($post_query, $post_query_vars));
                    $post_values = array_unique($post_values);
                }else{
                    $post_values = $wpdb->get_col("SELECT {$field->field_options['post_field']} FROM $wpdb->posts WHERE ID in (".implode(',', $post_ids) .")");
                }
                
                $field_metas = array_merge($post_values, $field_metas);
            }
        }
        
        if($type != 'star')
            unset($field);
        
        if (empty($field_metas)){
            if($type == 'star'){
                $stat = '';
                ob_start();
                include(FRMPRO_VIEWS_PATH.'/frmpro-fields/star_disabled.php');
                $contents = ob_get_contents();
                ob_end_clean();
                return $contents;
            }else{
                return 0;
            }
        }
        
        $count = count($field_metas);
        $total = array_sum($field_metas);

        switch($type){
            case 'average':
            case 'mean':
            case 'star':
                $stat = ($total / $count);
            break;
            case 'median':
                rsort($field_metas); 
                $n = ceil($count / 2); // Middle of the array
                if ($count % 2){
                    $stat = $field_metas[$n-1]; // If number is odd
                }else{
                    $n2 = floor($count / 2); // Other middle of the array
                    $stat = ($field_metas[$n-1] + $field_metas[$n2-1]) / 2;
                }
                $stat = maybe_unserialize($stat);
                if (is_array($stat))
                    $stat = 0;
            break;
            case 'deviation':
                $mean = ($total / $count);
                $stat = 0.0;
                foreach ($field_metas as $i)
                    $stat += pow($i - $mean, 2);
                
                if($count > 1){
                    $stat /= ( $count - 1 );
                
                    $stat = sqrt($stat);
                }else{
                    $stat = 0;
                }
            break;
            case 'minimum':
                $stat = min($field_metas);
            break;
            case 'maximum':
                $stat = max($field_metas);
            break;
            case 'count':
                $stat = $count;
            break;
            case 'unique':
                $stat = array_unique($field_metas);
                $stat = count($stat);
            break;
            case 'total':
            default:
                $stat = $total;
        }
        
        $stat = round($stat, $round);
        if($type == 'star'){
            ob_start();
            include(FRMPRO_VIEWS_PATH.'/frmpro-fields/star_disabled.php');
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        if($round and $round < 5)
            $stat = number_format($stat, $round);
        
        return $stat;
    }
    
    public static function get_field_matches($args){
        extract($args);
        
        $f = $orig_f;
        $where_is = '=';
        
        if(!is_numeric($f))
            return $entry_ids;
            
        if($f < 20 and !is_numeric($val)){
            // >, <, <=, >=  TODO: !=, %, !%
            $orig_val = $val;
            $lpos = strpos($val, '<');
            $gpos = strpos($val, '>');
            if($lpos !== false or $gpos !== false){
                $where_is = (($gpos !== false and $lpos !== false and $lpos > $gpos) or $lpos === false) ? '>' : '<';
                
                $str = explode($where_is, $orig_val);
                
                if(count($str) == 2){
                    $f = $str[0];
                    $val = $str[1];
                }else if(count($str) == 3){
                    //3 parts assumes a structure like '-1 month'<255<'1 month'
                    $val = str_replace($str[0] . $where_is, '', $orig_val);
                    $entry_ids = FrmProFieldsHelper::get_field_matches(compact('entry_ids', 'orig_f', 'val', 'id', 'atts', 'field', 'form_posts', 'after_where'));
                    
                    $after_where = true;
                    
                    $f = $str[1];
                    $val = $str[0];
                    $where_is = ($where_is == '<') ? '>' : '<';
                }
                
                if(strpos($val, '=') === 0){
                    $where_is .= '=';
                    $val = substr($val, 1);
                }
                
                $val = FrmAppHelper::replace_quotes($val);
                $val = trim(trim($val, "'"), '"');
            }
            
            if(!is_numeric($f))
                return $entry_ids;
        }
            
        unset($orig_f);
        
        $where_atts = apply_filters('frm_stats_where', array('where_is' => $where_is, 'where_val' => $val), array('id' => $id, 'atts' => $atts));
        $val = $where_atts['where_val'];
        $where_is = $where_atts['where_is'];
        unset($where_atts);
        
        $entry_ids = FrmProAppHelper::filter_where($entry_ids, array('where_opt' => $f, 'where_is' => $where_is, 'where_val' => $val, 'form_id' => $field->form_id, 'form_posts' => $form_posts, 'after_where' => $after_where));
        
        unset($f);
        unset($val);
        
        return $entry_ids;
    }
    
    public static function value_meets_condition($observed_value, $cond, $hide_opt){
        if(is_array($observed_value)){
            if($cond == '=='){
                if(is_array($hide_opt)){
                    $m = array_intersect($hide_opt, $observed_value); 
                    $m = empty($m) ? false : true;
                }else{
                    $m = in_array($hide_opt, $observed_value);
                }
            }else if($cond == '!='){
                $m = !in_array($hide_opt, $observed_value);
            }else if($cond == '>'){
                $min = min($observed_value);
                $m = $min > $hide_opt;
            }else if($cond == '<'){
                $max = max($observed_value);
                $m = $max < $hide_opt;
            }else if($cond == 'LIKE' or $cond == 'not LIKE'){
                foreach($observed_value as $ob){
                    $m = strpos($ob, $hide_opt);
                    if($m !== false){
                        $m = ($m === false) ? false : true;
                        break;
                    }
                    unset($ob);
                }
                
                if($cond == 'not LIKE')
                    $m = ($m === false) ? true : false;
            }
        }else{
            if($cond == '=='){
                $m = $observed_value == $hide_opt;
            }else if($cond == '!='){
                $m = $observed_value != $hide_opt;
            }else if($cond == '>'){
                $m = $observed_value > $hide_opt;
            }else if($cond == '<'){
                $m = $observed_value < $hide_opt;
            }else if($cond == 'LIKE' or $cond == 'not LIKE'){
                $m = strpos($observed_value, $hide_opt);
                $m = ($m === false) ? false : true;
                if($cond == 'not LIKE')
                    $m = ($m === false) ? true : false;
            }
        }
        return $m;
    }
    
    public static function get_shortcode_select($form_id, $target_id='content', $type='all'){ 
        global $frm_field, $frmdb;  
        $field_list = array();
        if(is_numeric($form_id)){
            $exclude = "'divider','captcha','break','html'";
            if($type == 'field_opt')
                $exclude .= ",'data','checkbox'";
            else if($type == 'calc')
                $exclude .= ",'data'";
            $field_list = $frm_field->getAll("fi.type not in (". $exclude .") and fi.form_id=". (int)$form_id, 'field_order');
        }
        
        $linked_forms = array();
        ?>
        <select class="frm_shortcode_select" onchange="frmInsertFieldCode('<?php echo $target_id ?>',this.value);this.value='';">
            <option value="">- <?php _e('Select a value to insert into the box below', 'formidable') ?> -</option>
            <?php if($type != 'field_opt' and $type != 'calc'){ ?>
            <option value="id"><?php _e('Entry ID', 'formidable') ?></option>
            <option value="key"><?php _e('Entry Key', 'formidable') ?></option>
            <option value="post_id"><?php _e('Post ID', 'formidable') ?></option>
            <option value="ip"><?php _e('User IP', 'formidable') ?></option>
            <option value="created-at"><?php _e('Entry creation date', 'formidable') ?></option>
            <option value="updated-at"><?php _e('Entry update date', 'formidable') ?></option>
            
            <optgroup label="<?php _e('Form Fields', 'formidable') ?>">
            <?php }
            
            if(!empty($field_list)){
            foreach ($field_list as $field){ 
                $field->field_options = maybe_unserialize($field->field_options);
                if($field->type == 'data' and (!isset($field->field_options['data_type']) or $field->field_options['data_type'] == 'data' or $field->field_options['data_type'] == ''))
                    continue;
            ?>
                <option value="<?php echo $field->id ?>"><?php echo $field_name = FrmAppHelper::truncate($field->name, 60) ?> (<?php _e('ID', 'formidable') ?>)</option>
                <option value="<?php echo $field->field_key ?>"><?php echo $field_name ?> (<?php _e('Key', 'formidable') ?>)</option>
                <?php if ($field->type == 'file' and $type != 'field_opt' and $type != 'calc'){ ?>
                    <option class="frm_subopt" value="<?php echo $field->field_key ?> size=thumbnail"><?php _e('Thumbnail', 'formidable') ?></option>
                    <option class="frm_subopt" value="<?php echo $field->field_key ?> size=medium"><?php _e('Medium', 'formidable') ?></option>
                    <option class="frm_subopt" value="<?php echo $field->field_key ?> size=large"><?php _e('Large', 'formidable') ?></option>
                    <option class="frm_subopt" value="<?php echo $field->field_key ?> size=full"><?php _e('Full Size', 'formidable') ?></option>
                <?php }else if($field->type == 'data'){ //get all fields from linked form
                    if (isset($field->field_options['form_select']) && is_numeric($field->field_options['form_select'])){
                        $linked_form = $frmdb->get_var($frmdb->fields, array('id' => $field->field_options['form_select']), 'form_id');
                        if(!in_array($linked_form, $linked_forms)){
                            $linked_forms[] = $linked_form;
                            $linked_fields = $frm_field->getAll("fi.type not in ('divider','captcha','break','html') and fi.form_id =". (int)$linked_form);
                            foreach ($linked_fields as $linked_field){ ?>
                    <option class="frm_subopt" value="<?php echo $field->id ?> show=<?php echo $linked_field->id ?>"><?php echo FrmAppHelper::truncate($linked_field->name, 60) ?> (<?php _e('ID', 'formidable') ?>)</option>
                    <option class="frm_subopt" value="<?php echo $field->field_key ?> show=<?php echo $linked_field->field_key ?>"><?php echo FrmAppHelper::truncate($linked_field->name, 60) ?> (<?php _e('Key', 'formidable') ?>)</option>
                    <?php
                            }
                        } 
                    } 
                }
            }
            }
            
            if($type != 'field_opt' and $type != 'calc'){ ?>
            </optgroup>
            <optgroup label="<?php _e('Helpers', 'formidable') ?>">
                <option value="editlink"><?php _e('Admin link to edit the entry', 'formidable') ?></option>
                <?php if ($target_id == 'content'){ ?>
                <option value="detaillink"><?php _e('Link to view single page if showing dynamic entries', 'formidable') ?></option>
                <?php }
                
                if($type != 'email'){ ?>
                <option value="evenodd"><?php _e('Add a rotating \'even\' or \'odd\' class', 'formidable') ?></option>
                <?php }else if($target_id == 'email_message'){ ?>
                <option value="default-message"><?php _e('Default Email Message', 'formidable') ?></option>   
                <?php } ?>
                <option value="siteurl"><?php _e('Site URL', 'formidable') ?></option>
                <option value="sitename"><?php _e('Site Name', 'formidable') ?></option>
            </optgroup>
            <?php } ?>
        </select>    
    <?php    
    }
    
    public static function replace_shortcodes($content, $entry, $shortcodes, $display=false, $show='one', $odd=''){
        global $frm_field, $frm_entry_meta, $post, $frmpro_settings;

        if($display){
            $param_value = ($display->frm_type == 'id') ? $entry->id : $entry->item_key;
            
            if($entry->post_id){
                $detail_link = get_permalink($entry->post_id);
            }else{
                $param = (isset($display->frm_param) && !empty($display->frm_param)) ? $display->frm_param : 'entry';
                if($post)
                    $detail_link = add_query_arg($param, $param_value, get_permalink($post->ID));
                else
                    $detail_link = add_query_arg($param, $param_value);
                //if( FrmProAppHelper::rewriting_on() && $frmpro_settings->permalinks )
                //    $detail_link = get_permalink($post->ID) .$param_value .'/';
            }
        }
        
        foreach ($shortcodes[0] as $short_key => $tag){
            $conditional = (preg_match('/^\[if/s', $shortcodes[0][$short_key])) ? true : false;
            $atts = shortcode_parse_atts( $shortcodes[3][$short_key] );

            if(!empty($shortcodes[3][$short_key])){
                if($conditional)
                    $tag = str_replace('[if ', '', $shortcodes[0][$short_key]);
                else
                    $tag = str_replace('[', '',$shortcodes[0][$short_key]);
                $tag = str_replace(']', '', $tag);
                $tags = explode(' ', $tag);
                if(is_array($tags))
                    $tag = $tags[0];
            }else
                $tag = $shortcodes[2][$short_key];
                             
            switch($tag){                
                case 'detaillink':
                    if($display and $detail_link)
                        $content = str_replace($shortcodes[0][$short_key], $detail_link, $content);
                break;
                
                case 'id':
                    $content = str_replace($shortcodes[0][$short_key], $entry->id, $content);
                break;
                
                case 'post-id':
                case 'post_id':
                    $content = str_replace($shortcodes[0][$short_key], $entry->post_id, $content);
                break;
                
                case 'key':
                    $content = str_replace($shortcodes[0][$short_key], $entry->item_key, $content);
                break;
                
                case 'ip':
                    $content = str_replace($shortcodes[0][$short_key], $entry->ip, $content);
                break;
                
                case 'user_agent':
                case 'user-agent':
                    $entry->description = maybe_unserialize($entry->description);
                    $content = str_replace($shortcodes[0][$short_key], $entry->description['browser'], $content);
                break;
                
                case 'created_at':
                case 'updated_at':
                case 'created-at':
                case 'updated-at':
                    if(!isset($atts['format'])){
                        $atts['format'] = get_option('date_format');
                        $time_format = false;
                    }else{
                        $time_format = ' ';
                    }
                      
                    $this_tag = str_replace('-', '_', $tag);
                    if ($conditional){
                        $replace_with = apply_filters('frm_conditional_value', $entry->{$this_tag}, $atts, false, $tag);
                        
                        if($atts)
                            $content = str_replace($shortcodes[0][$short_key], '[if '.$tag.']', $content);
                        
                        if (empty($replace_with)){
                            $content = preg_replace('/(\[if\s+'.$tag.'\])(.*?)(\[\/if\s+'.$tag.'\])/mis', '', $content);
                        }else{  
                            $content = preg_replace('/(\[if\s+'.$tag.'\])/', '', $content, 1);
                         	$content = preg_replace('/(\[\/if\s+'.$tag.'\])/', '', $content, 1); 
                        }
                    }else{
                        if(isset($atts['time_ago']))
                            $date = FrmProAppHelper::human_time_diff( strtotime($entry->{$this_tag}) );
                        else
                            $date = FrmProAppHelper::get_formatted_time($entry->{$this_tag}, $atts['format'], $time_format);
                        
                        $content = str_replace($shortcodes[0][$short_key], $date, $content);
                    }
                    
                    unset($this_tag);
                break;
                
                case 'evenodd':
                    $content = str_replace($shortcodes[0][$short_key], $odd, $content);
                break;
                
                case 'siteurl':
                    global $frm_siteurl;
                    $content = str_replace($shortcodes[0][$short_key], $frm_siteurl, $content);
                break;
                
                case 'sitename':
                    $content = str_replace($shortcodes[0][$short_key], get_option('blogname'), $content);
                break;
                
                case 'get':
                    if(isset($atts['param'])){
                        $param = $atts['param'];
                        $replace_with = FrmAppHelper::get_param($param);
                        if(is_array($replace_with))
                            $replace_with = implode(', ', $replace_with);
                        
                        $content = str_replace($shortcodes[0][$short_key], $replace_with, $content);
                        unset($param);
                        unset($replace_with);
                    }
                break;
                
                default:
                    if($tag == 'deletelink'){
                        $page_id = isset($atts['page_id']) ? $atts['page_id'] : ($post ? $post->ID : 0);
                        
                        $can_delete = FrmProEntriesHelper::allow_delete($entry);
                        if($can_delete){
                            if(isset($atts['label'])){
                                $delete_atts = $atts;
                                $delete_atts['id'] = $entry->id;
                                $delete_atts['page_id'] = $page_id;

                                $replace_with = FrmProEntriesController::entry_delete_link($delete_atts);
                                unset($delete_atts);
                            }else{
                                $replace_with = add_query_arg(array('frm_action' => 'destroy', 'entry' => $entry->id), get_permalink($page_id));
                            }
                        }else{
                            $replace_with = '';
                        }
                        $field = false;
                    }else if($tag == 'editlink'){
                        $replace_with = '';
                        $link_text = (isset($atts['label'])) ? $atts['label'] : false;
                        if(!$link_text)
                            $link_text = (isset($atts['link_text'])) ? $atts['link_text'] : __('Edit', 'formidable');
                        
                        $class = (isset($atts['class'])) ? $atts['class'] : '';
                        $page_id = isset($atts['page_id']) ? $atts['page_id'] : ($post ? $post->ID : 0);

                        if(isset($atts['location']) and $atts['location'] == 'front'){
                            $edit_atts = $atts;
                            $edit_atts['id'] = $entry->id;
                            $delete_atts['page_id'] = $page_id;

                            $replace_with = FrmProEntriesController::entry_edit_link($edit_atts);
                        }else{
                            if($entry->post_id){
                                $replace_with = get_edit_post_link($entry->post_id);
                            }else{
                                global $frm_siteurl;
                                if(current_user_can('frm_edit_entries'))
                                    $replace_with = esc_url($frm_siteurl . '/wp-admin/admin.php?page=formidable-entries&frm_action=edit&id='.$entry->id );
                            }

                            if(!empty($replace_with))
                                $replace_with = '<a href="'. $replace_with . '" class="frm_edit_link '. $class .'">'. $link_text .'</a>';

                        }
                        unset($class);
                    }else{
                        $field = $frm_field->getOne( $tag );
                    }
                    
                    $sep = (isset($atts['sep'])) ? $atts['sep'] : ', ';
                    
                    if(!isset($field))
                        $field = false;
                        
                    if($field){
                        $replace_with = FrmProEntryMetaHelper::get_post_or_meta_value($entry, $field, $atts);
                        //$replace_with = stripslashes_deep(maybe_unserialize($replace_with));
                        $atts['entry_id'] = $entry->id;
                        $atts['entry_key'] = $entry->item_key;
                        $atts['post_id'] = $entry->post_id;
                        $replace_with = apply_filters('frmpro_fields_replace_shortcodes', $replace_with, $tag, $atts, $field); 
                    }   

                    if ($field and $field->type == 'file'){
                        //size options are thumbnail, medium, large, or full
                        $size = (isset($atts['size'])) ? $atts['size'] : (isset($atts['show']) ? $atts['show'] : 'thumbnail');
                        $inc_html = (isset($atts['html']) and $atts['html']) ? true : false;
                        $inc_links = (isset($atts['links']) and $atts['links']) ? true : false;
                        $sep = (isset($atts['sep'])) ? $atts['sep'] : ' ';
                        
                        if($size != 'id' and !empty($replace_with))
                            $replace_with = FrmProFieldsHelper::get_media_from_id($replace_with, $size, array('html' => $inc_html, 'links' => $inc_links));

                        unset($size);
                    }
                    
                    if (isset($replace_with) and is_array($replace_with))
                        $replace_with = implode($sep, $replace_with);
                        
                    if ($conditional){
                        if(!isset($replace_with))
                            $replace_with = '';
                        $replace_with = apply_filters('frm_conditional_value', $replace_with, $atts, $field, $tag);                        
                        
                        $start_pos = strpos($content, $shortcodes[0][$short_key]);
                        $start_pos_len = strlen($shortcodes[0][$short_key]);
                        $end_pos = strpos($content, '[/if '.$tag.']');
                        $end_pos_len = strlen('[/if '.$tag.']');
                        
                        if($start_pos !== false){
                            if (empty($replace_with)){
                                $total_len = ($end_pos+$end_pos_len)-$start_pos;
                                $content = substr_replace($content, '', $start_pos, $total_len);
                            }else{
                                $content = substr_replace($content, '', $end_pos, $end_pos_len);
                                $content = substr_replace($content, '', $start_pos, $start_pos_len);
                            }
                        }
                    }else{
                        if($field){
                            if (isset($atts['show']) and $atts['show'] == 'field_label'){
                                $replace_with = $field->name;
                            }else if (isset($atts['show']) and $atts['show'] == 'description'){
                                $replace_with = $field->description;
                            }else if (empty($replace_with) and $replace_with != '0'){
                                $replace_with = '';
                                if ($field->type == 'number')
                                    $replace_with = '0';
                            }else{
                                $replace_with = FrmProFieldsHelper::get_display_value($replace_with, $field, $atts);
                            }
                        }
                            
                        if (isset($atts['sanitize']))
                            $replace_with = sanitize_title_with_dashes($replace_with);
                            
                        if (isset($atts['sanitize_url'])){
                            if(seems_utf8($replace_with))
                                $replace_with = utf8_uri_encode($replace_with, 200);
                            $replace_with = urlencode(htmlentities($replace_with));
                        }
                            
                        if (isset($atts['truncate'])){
                            if(isset($atts['more_text']))
                                $more_link_text = $atts['more_text'];
                            else
                                $more_link_text = (isset($atts['more_link_text'])) ? $atts['more_link_text'] : '. . .';

                            if ($display and $display->frm_show_count == 'dynamic'){
                                $more_link_text = ' <a href="'. $detail_link .'">'. $more_link_text .'</a>';
                                $replace_with = FrmAppHelper::truncate($replace_with, (int)$atts['truncate'], 3, $more_link_text); 
                            }else{
                                $replace_with = wp_specialchars_decode(strip_tags($replace_with), ENT_QUOTES);
                                $part_one = substr($replace_with, 0, (int)$atts['truncate']);
                                $part_two = substr($replace_with, (int)$atts['truncate']);
                                if(!empty($part_two))
                                    $replace_with = $part_one .'<a href="#" onclick="jQuery(this).next().css(\'display\', \'inline\');jQuery(this).css(\'display\', \'none\');return false;" class="frm_text_exposed_show"> '. $more_link_text .'</a><span style="display:none;">'. $part_two .'</span>';
                            }
                        }
                        
                        if(isset($atts['clickable']))
                            $replace_with = make_clickable($replace_with);

                        if (!isset($replace_with))
                            $replace_with = '';
                            
                        $content = str_replace($shortcodes[0][$short_key], $replace_with, $content);
                        
                    }
                    
                    unset($replace_with);
                    
                    if(isset($field))
                        unset($field);
            }
            unset($atts);
            unset($conditional);
         }

         return $content;
     }
     
     public static function conditional_replace_with_value($replace_with, $atts, $field, $tag){
         if($field and isset($atts['show']) and $field->type == 'data'){
             $old_replace_with = $replace_with;    
             $replace_with = FrmProFieldsHelper::get_display_value($replace_with, $field, $atts);      
             
             if($old_replace_with == $replace_with)                                
                 $replace_with = '';
         }
         
         if(isset($atts['equals']) and ($replace_with != $atts['equals'])){
             if($field and $field->type == 'data'){
                 $replace_with = FrmProFieldsHelper::get_display_value($replace_with, $field, $atts);
                 if($replace_with != $atts['equals'])
                     $replace_with = '';
             }else if(isset($field->field_options['post_field']) and $field->field_options['post_field'] == 'post_category'){
                 $cats = explode(', ', $replace_with);
                 $replace_with = '';
                 foreach($cats as $cat){  
                     if($replace_with == true)
                         continue;
                         
                     if($atts['equals'] == strip_tags($cat))
                         $replace_with = true;
                 }
             }else{
                 $replace_with = '';
             }
         }else if(isset($atts['equals']) and (($atts['equals'] == '' and $replace_with == '') or ($atts['equals'] == '0' and $replace_with == '0'))){
             $replace_with = true; //if the field is blank, give it a value
         }
             
            
         if(isset($atts['not_equal'])){
             if($replace_with == $atts['not_equal']){
                 $replace_with = '';
             }else if(!empty($replace_with) and $field->field_options['post_field'] == 'post_category'){
                 $cats = explode(', ', $replace_with);
                 foreach($cats as $cat){
                     if(empty($replace_with))
                         continue;
                         
                     if($atts['not_equal'] == strip_tags($cat))
                         $replace_with = '';
                 }
             }
         }
         
         if(isset($atts['like'])){
             if(strpos($replace_with, $atts['like']) === false)
                 $replace_with = '';
         }
         
         if(isset($atts['not_like'])){            
             if($replace_with == '')
                $replace_with = true;
             else if(strpos($replace_with, $atts['not_like']) !== false)
                $replace_with = '';
         }
         
         if(isset($atts['less_than'])){
             if($field and $field->type == 'date' and !preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($atts['less_than'])))
                 $atts['less_than'] = date_i18n('Y-m-d', strtotime($atts['less_than']));
             
             if($atts['less_than'] <= $replace_with)
                 $replace_with = '';
             else if($atts['less_than'] > 0 and $replace_with == '0')
                $replace_with = true;
         }
         
         if(isset($atts['greater_than'])){
             if($field and $field->type == 'date' and !preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($atts['greater_than'])))
                 $atts['greater_than'] = date_i18n('Y-m-d', strtotime($atts['greater_than']));

             if($atts['greater_than'] >= $replace_with)
                 $replace_with = '';
         }
         
         return $replace_with;
    }
     
    public static function get_media_from_id($ids, $size='thumbnail', $atts=array()){
        $defaults = array('html' => false, 'links' => false);
        extract(wp_parse_args( $atts, $defaults ));
                
        $replace_with = array();
        if($size == 'label'){
            foreach((array)$ids as $id){
                if(!is_numeric($id))
                    continue;
                    
                $attachment = get_post($id);
                if($attachment)
                    $replace_with[] = basename($attachment->guid);
            }
        }else{
            foreach((array)$ids as $id){
                if(!is_numeric($id)){
                    if(!empty($id))
                        $replace_with[] = $id;
                    continue;
                }
                
                $image = wp_get_attachment_image_src($id, $size); //Returns an array (url, width, height) or false

                if($image){
                    $img = $image[0];
                    if($html)
                        $img = '<img src="'. $img .'" />';
                }else{
                    if(!$html and !$links)
                        $img = wp_get_attachment_url($id);
                        
                    if($html)
                        $links = true;
                }
                
                if($links)
                    $img = wp_get_attachment_link($id, $size, false);
                // show the sized image representation of the attachment if available, and link to the raw file

                $replace_with[] = $img;
                
                unset($img);
                unset($id);
            }
        }
        
        if(count($replace_with) == 1)
            $replace_with = reset($replace_with);
        
        return $replace_with;
    }
     
     public static function get_display_value($replace_with, $field, $atts=array()){
         $sep = (isset($atts['sep'])) ? $atts['sep'] : ', ';
         if ($field->type == 'user_id'){
             $user_info = (isset($atts['show'])) ? $atts['show'] : 'display_name';
             $replace_with = FrmProFieldsHelper::get_display_name($replace_with, $user_info, $atts);
             if(is_array($replace_with)){
                 $new_val = '';
                 foreach($replace_with as $key => $val){
                     if(!empty($new_val))
                         $new_val .= ', ';
                     $new_val .= $key .'. '. $val;
                }
                    
                 $replace_with = $new_val;
             }
         }else if ($field->type == 'date'){
             if(isset($atts['time_ago']))
                 $atts['format'] = 'Y-m-d H:i:s';
                 
             if(!isset($atts['format']))
                $atts['format'] = false;
             
             $replace_with = FrmProFieldsHelper::get_date($replace_with, $atts['format']);
             
             if(isset($atts['time_ago']))
                 $replace_with = FrmProAppHelper::human_time_diff( strtotime($replace_with), strtotime(date_i18n('Y-m-d')) );
         }else if ((is_numeric($replace_with) or is_array($replace_with)) and $field->type == 'file'){ 
             //size options are thumbnail, medium, large, or full
             $size = (isset($atts['size'])) ? $atts['size'] : (isset($atts['show']) ? $atts['show'] : 'thumbnail');
             $inc_html = (isset($atts['html']) and $atts['html']) ? true : false;
             $inc_links = (isset($atts['links']) and $atts['links']) ? true : false;
             $sep = (isset($atts['sep'])) ? $atts['sep'] : ' ';
             if($size != 'id')
                 $replace_with = FrmProFieldsHelper::get_media_from_id($replace_with, $size, array('html' => $inc_html, 'links' => $inc_links));
             
             if(is_array($replace_with))
                 $replace_with = implode($sep, $replace_with);
         }else if ($field->type == 'data'){ //and (is_numeric($replace_with) or is_array($replace_with))
             if(isset($field->field_options['form_select']) and $field->field_options['form_select'] == 'taxonomy')
                 return $replace_with;
             
             if(!empty($replace_with) and !is_array($replace_with))    
                 $replace_with = explode($sep, $replace_with);
             
             if (isset($atts['show'])){
                 if (in_array($atts['show'], array('key', 'created-at', 'created_at', 'updated-at', 'updated_at', 'post_id'))){
                     global $frm_entry;
                     if(is_array($replace_with)){
                         $linked_ids = $replace_with;
                         $replace_with = '';
                         foreach($linked_ids as $linked_id){
                             $linked_entry = FrmEntry::getOne($linked_id);
                             if(!empty($replace_with))
                                 $replace_with .= $sep;
                                 
                             if($atts['show'] == 'created-at')
                                 $replace_with .= $linked_entry->created_at;
                             else if($atts['show'] == 'updated-at')
                                 $replace_with .= $linked_entry->updated_at;
                             else if($atts['show'] == 'key')
                                 $replace_with .= $linked_entry->item_key;
                             else
                                 $replace_with .= (isset($linked_entry->{$atts['show']})) ? $linked_entry->{$atts['show']} : $linked_entry->item_key;
                         }
                     }else{
                         $linked_entry = FrmEntry::getOne($replace_with);
                         if($atts['show'] == 'created-at')
                             $replace_with = $linked_entry->created_at;
                         else if($atts['show'] == 'updated-at')
                              $replace_with = $linked_entry->updated_at;
                         else if($atts['show'] == 'key')
                             $replace_with = $linked_entry->item_key;
                         else
                             $replace_with = (isset($linked_entry->{$atts['show']})) ? $linked_entry->{$atts['show']} : $linked_entry->item_key;
                     }
                 }else if ($atts['show'] == 'id'){
                     if(is_array($replace_with))
                         $replace_with = implode($sep, $replace_with);
                     //just keep the value since it's already the id
                 }else{
                     if(is_array($replace_with)){
                         $linked_ids = $replace_with;
                         $replace_with = array();
                         foreach($linked_ids as $linked_id){
                             $new_val = FrmProFieldsHelper::get_data_value($linked_id, $field, $atts);
                             
                             if($linked_id != $new_val){
                                 if(is_array($new_val))
                                    $new_val = implode($sep, $new_val);
                                    
                                 $replace_with[] = $new_val;
                             }
                             
                             unset($new_val);
                         }
                         
                         $replace_with = implode($sep, $replace_with);
                     }else
                         $replace_with = FrmProFieldsHelper::get_data_value($replace_with, $field, $atts);
                 }
             }else{   
                 if(is_array($replace_with)){ 
                     $linked_ids = $replace_with;
                     $replace_with = array();
                     foreach($linked_ids as $linked_id){
                         $new_val = FrmProFieldsHelper::get_data_value($linked_id, $field, $atts);
                         
                         if($linked_id != $new_val)
                             $replace_with[] = $new_val;
                         
                         unset($new_val);
                     }
                     
                     $replace_with = implode($sep, $replace_with);
                 }else
                     $replace_with = FrmProFieldsHelper::get_data_value($replace_with, $field, $atts);
             }
         }else if($field->type == 'textarea'){
             $autop = isset($atts['wpautop']) ? $atts['wpautop'] : true;
             if(apply_filters('frm_use_wpautop', $autop))
                 $replace_with = wpautop($replace_with);
             unset($autop);
         }else if($field->type == 'number'){
             $new_val = array();
             foreach((array)$replace_with as $v){
                 if(!isset($atts['decimal'])){
                     $num = explode('.', $v);
                     $atts['decimal'] = (isset($num[1])) ? strlen($num[1]) : 0;
                  }

                  if(!isset($atts['dec_point']))
                     $atts['dec_point'] = '.';

                  if(!isset($atts['thousands_sep']))
                     $atts['thousands_sep'] = '';

                  if($v != '')
                      $v = number_format($v, $atts['decimal'], $atts['dec_point'], $atts['thousands_sep']);
                    
                  if($v != '')
                      $new_val[] = $v;
                  unset($v);
              }
              $replace_with = implode($sep, $new_val);
         }

         //$replace_with = stripslashes_deep($replace_with);
         return $replace_with;
     }
     
     public static function get_table_options($field_options){
 		$columns = array();
 		$rows = array();
 		if (is_array($field_options)){
 			foreach ($field_options as $opt_key => $opt){
 				switch(substr($opt_key,0,3)){
 				case 'col':
 					$columns[$opt_key] = $opt;
 					break;
 				case 'row':
 					$rows[$opt_key] = $opt;
 					break;
 				}
 			}
 		}
 		return array($columns,$rows);
 	}

 	public static function set_table_options($field_options, $columns, $rows){
 		if (is_array($field_options)){
 			foreach ($field_options as $opt_key => $opt){
 				if (substr($opt_key, 0, 3) == 'col' or substr($opt_key, 0, 3) == 'row')
 					unset($field_options[$opt_key]);
 			}
 		}else
 			$field_options = array();
 		
 		foreach ($columns as $opt_key => $opt)
 			$field_options[$opt_key] = $opt;
 		
 		foreach ($rows as $opt_key => $opt)
 			$field_options[$opt_key] = $opt;
 		
 		return $field_options;
 	}
 	
 	public static function mobile_check(){
 	    global $frm_mobile;
 	            
 	    if(function_exists('wp_is_mobile')){
 	        $frm_mobile = wp_is_mobile();
 	        return $frm_mobile;
 	    }
 	    
 	    if($frm_mobile)
 	        return $frm_mobile;
 	        
    	if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
    		$is_mobile = false;
    	} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
    		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
    		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
    		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
    		|| strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
    		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
    		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
    			$is_mobile = true;
    	} else {
    		$is_mobile = false;
    	}
    	
        $frm_mobile = $ismobile;
        return $frm_mobile;
    }
}
