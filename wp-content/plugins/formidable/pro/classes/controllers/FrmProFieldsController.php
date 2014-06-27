<?php

class FrmProFieldsController{
    public static function load_hooks() {
        add_filter('frm_show_normal_field_type', 'FrmProFieldsController::show_normal_field', 10, 2);
        add_filter('frm_normal_field_type_html', 'FrmProFieldsController::normal_field_html', 10, 2);
        add_action('frm_show_other_field_type', 'FrmProFieldsController::show_other', 10, 3);
        add_filter('frm_field_type', 'FrmProFieldsController::change_type', 15, 2);
        add_filter('frm_field_value_saved', 'FrmProFieldsController::use_field_key_value', 10, 3);
        add_action('frm_get_field_scripts', 'FrmProFieldsController::show_field', 10, 2);
        add_action('frm_display_added_fields', 'FrmProFieldsController::show');
        add_filter('frm_html_label_position', 'FrmProFieldsController::label_position');
        add_filter('frm_display_field_options', 'FrmProFieldsController::display_field_options');
        add_action('frm_form_fields', 'FrmProFieldsController::form_fields', 10, 2);
        add_action('frm_field_input_html', 'FrmProFieldsController::input_html', 10, 2);
        add_filter('frm_field_classes', 'FrmProFieldsController::add_field_class', 20, 2);
        add_action('frm_add_multiple_opts_labels', 'FrmProFieldsController::add_separate_value_opt_label'); 
        add_action('frm_field_options_form', 'FrmProFieldsController::options_form', 10, 3);
        add_action('wp_ajax_frm_get_field_selection', 'FrmProFieldsController::get_field_selection');
        add_action('wp_ajax_frm_get_field_values', 'FrmProFieldsController::get_field_values');
        add_action('wp_ajax_frm_get_cat_opts', 'FrmProFieldsController::get_cat_opts');
        add_action('wp_ajax_frm_get_title_opts', 'FrmProFieldsController::get_title_opts');        
        add_action('frm_date_field_js', 'FrmProFieldsController::date_field_js', 10, 2);
        add_action('wp_ajax_frm_fields_ajax_get_data', 'FrmProFieldsController::ajax_get_data');
        add_action('wp_ajax_nopriv_frm_fields_ajax_get_data', 'FrmProFieldsController::ajax_get_data');
        add_action('wp_ajax_frm_fields_ajax_data_options', 'FrmProFieldsController::ajax_data_options');
        add_action('wp_ajax_nopriv_frm_fields_ajax_data_options', 'FrmProFieldsController::ajax_data_options');
        add_action('wp_ajax_frm_add_field_option', 'FrmProFieldsController::add_option', 5); // run before the add_option in the FrmFieldsController class
        add_action('wp_ajax_frm_add_table_row', 'FrmProFieldsController::add_table_row');
        add_action('wp_ajax_frm_fields_ajax_time_options', 'FrmProFieldsController::ajax_time_options');
        add_action('wp_ajax_nopriv_frm_fields_ajax_time_options', 'FrmProFieldsController::ajax_time_options');
        add_action('wp_ajax_frm_add_logic_row', 'FrmProFieldsController::_logic_row');
        add_action('wp_ajax_frm_populate_calc_dropdown', 'FrmProFieldsController::populate_calc_dropdown');
        
        // Trigger field model
        add_filter('frm_before_field_created', 'FrmProFieldsController::create');
        add_filter('frm_update_field_options', 'FrmProFieldsController::update', 10, 3);
        add_filter('frm_duplicated_field', 'FrmProFieldsController::duplicate');
    }
    
    public static function &show_normal_field($show, $field_type){
        if (in_array($field_type, array('hidden', 'user_id', 'break')))
            $show = false;
        return $show;
    }
    
    public static function &normal_field_html($show, $field_type){
        if (in_array($field_type, array('hidden', 'user_id', 'break', 'divider', 'html')))
            $show = false;
        return $show;
    }
    
    public static function show_other($field, $form, $args) {
        global $frm_vars;
        $field_name = "item_meta[$field[id]]";
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/show-other.php');
    }
    
    public static function &change_type($type, $field){
        global $frm_vars;
        if($type != 'user_id' and (isset($frm_vars['show_fields']) and !empty($frm_vars['show_fields'])) and !in_array($field->id, $frm_vars['show_fields']) and !in_array($field->field_key, $frm_vars['show_fields']))
            $type = 'hidden';
        if($type == 'website') 
            $type = 'url';
        else if($type == '10radio')
            $type = 'scale';
        
        if((!is_admin() or defined('DOING_AJAX')) and $type != 'hidden'){
            $field->field_options = maybe_unserialize($field->field_options);
            if ( !FrmProFieldsHelper::is_field_visible_to_user($field) ) {
                $type = 'hidden';
            }
        }
        
        return $type;    
    }
    
    public static function use_field_key_value($opt, $opt_key, $field){
        //if(in_array($field['post_field'], array('post_category', 'post_status')) or ($field['type'] == 'user_id' and is_admin() and current_user_can('administrator')))
        if((isset($field['use_key']) and $field['use_key']) or 
            (isset($field['type']) and $field['type'] == 'data') or 
            (isset($field['post_field']) and $field['post_field'] == 'post_status')
        )
            $opt = $opt_key;
        return $opt;
    }
    
    public static function show_field($field, $form){
        global $frm_vars;
        
        if (!empty($field['hide_field'])){
            $first = reset($field['hide_field']);
            if(is_numeric($first)){
                if(!isset($frm_vars['hidden_fields']))
                    $frm_vars['hidden_fields'] = array();
                $frm_vars['hidden_fields'][] = $field;
            }
        }
        
        if ($field['use_calc'] and $field['calc']){
            $ajax = (isset($form->options['ajax_submit']) and $form->options['ajax_submit']) ? true : false;
            $ajax_now = (defined('DOING_AJAX') and (!isset($frm_vars['preview']) or !$frm_vars['preview']));
            if($ajax and $ajax_now)
                return;
            
            global $frm_vars;
            if(!isset($frm_vars['calc_fields']))
                $frm_vars['calc_fields'] = array();
            $frm_vars['calc_fields'][$field['field_key']] = $field['calc'];
        }
    }
    
    public static function show($field){
        $field_name = "item_meta[". $field['id'] ."]";
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/show.php');    
    }
    
    public static function label_position($position){
        global $frmpro_settings;
        return ($position and $position != '') ? $position : ($frmpro_settings->position == 'none' ? 'top' : $frmpro_settings->position);
    }
    
    public static function display_field_options($display){
        
        switch($display['type']){
            case 'radio':
                $display['unique'] = true;
                $display['default_blank'] = false;
                break;
            break;
            case 'text':
            case 'textarea':
            case 'select':
                $display['read_only'] = true;
                $display['unique'] = true;
                break;
            break;
            case 'checkbox':
                $display['unique'] = true;
                break;
            case 'user_id':
            case 'hidden':
                $display['unique'] = true;
                $display['label_position'] = false;
                $display['description'] = false;
            case 'form':
                $display['required'] = false;
                $display['default_blank'] = false;
                break;
            case 'break':
                $display['required'] = false;
                $display['options'] = true;
                $display['default_blank'] = false;
                $display['css'] = false;
                $display['label_position'] = false;
                $display['description'] = false;
                break;
            case 'email':
            case 'url':
            case 'website':
            case 'phone':
            case 'image':
            case 'date':
            case 'number':
                $display['size'] = true;
                $display['invalid'] = true;
                $display['clear_on_focus'] = true;
                $display['read_only'] = true;
                $display['unique'] = true;
                break;
            case 'password':
                $display['size'] = true;
                $display['clear_on_focus'] = true;
                $display['read_only'] = true;
                $display['unique'] = true;
                break;
            case 'time':
                $display['size'] = true;
                $display['unique'] = true;
                break;
            case 'rte':
                $display['size'] = true;
                $display['default_blank'] = false;
                $display['unique'] = true;
                break;
            case 'file':
                $display['invalid'] = true;
                $display['size'] = true;
                $display['read_only'] = true;
                break;
            case 'scale':
                $display['default_blank'] = false;
                $display['unique'] = true;
                break;
            case 'html':
                $display['label_position'] = false;
                $display['description'] = false;
            case 'divider':
                $display['required'] = false;
                $display['default_blank'] = false;
                break;
            case 'data':
                if(isset($display['field_data']['data_type'])){
                    $display['read_only'] = false;
                    $display['unique'] = true;
                    if($display['field_data']['data_type'] == 'data'){
                        $display['required'] = false;
                        $display['default_blank'] = false;
                        $display['read_only'] = false;
                    }else if($display['field_data']['data_type'] == 'select'){
                        $display['size'] = true;
                        $display['read_only'] = true;
                    }
                }
                break;
        }
        
        return $display;
    }

    public static function form_fields($field, $field_name){
        global $frmpro_settings, $frm_settings, $frm_vars, $frm_field;
        $entry_id = isset($frm_vars['editing_entry']) ? $frm_vars['editing_entry'] : false;
        
        if($field['type'] == 'form' and $field['form_select'])
            $dup_fields = $frm_field->getAll("fi.form_id='$field[form_select]' and fi.type not in ('break', 'captcha')");
        
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/form-fields.php');
    }
    
    public static function input_html($field, $echo=true){
        global $frm_settings, $frm_vars;
        
        $add_html = '';

        if ( isset($field['read_only']) && $field['read_only'] && $field['type'] != 'hidden' ) {
            global $frm_vars;

            if ( (isset($frm_vars['readonly']) && $frm_vars['readonly'] == 'disabled') || (current_user_can('frm_edit_entries') && is_admin() && !defined('DOING_AJAX')) ) {
                //not read only
            //}else if($field['type'] == 'select'){
                //$add_html .= ' disabled="disabled" ';
            }else{
                $add_html .= ' readonly="readonly" ';
            }
        }
        
        if(isset($field['multiple']) and $field['multiple'] and ($field['type'] == 'select' or ($field['type'] == 'data' and isset($field['data_type']) and $field['data_type'] == 'select'))){
            $add_html .= ' multiple="multiple" ';
        }
        
        if(isset($_GET) and isset($_GET['page']) and ($_GET['page'] == 'formidable')){
            if($echo)
                echo $add_html;

            //don't continue if we are on the form builder page
            return $add_html;
        }
        
        if($frm_settings->use_html){
            if(isset($field['autocom']) and $field['autocom'] and ($field['type'] == 'select' or ($field['type'] == 'data' and isset($field['data_type']) and $field['data_type'] == 'select'))){
                //add label for autocomplete fields
                $add_html .= ' data-placeholder=" "';
            }
            
            if($field['type'] == 'number' or $field['type'] == 'range'){
                if(!is_numeric($field['minnum']))
                    $field['minnum'] = 0;
                if(!is_numeric($field['maxnum']))
                    $field['maxnum'] = 9999999;
                if(!is_numeric($field['step']))
                    $field['step'] = 1;
                $add_html .= ' min="'.$field['minnum'].'" max="'.$field['maxnum'].'" step="'.$field['step'].'"';
            }else if(in_array($field['type'], array('url', 'email', 'image'))){
                if((!isset($frm_vars['novalidate']) or !$frm_vars['novalidate']) and ($field['type'] != 'email' or (isset($field['value']) and $field['default_value'] == $field['value'])))
                    $frm_vars['novalidate'] = true;
            }
        }

        if(isset($field['dependent_fields']) and $field['dependent_fields']){
            $trigger = ($field['type'] == 'checkbox' or $field['type'] == 'radio') ? 'onclick' : 'onchange';            
            
            $add_html .= ' '. $trigger .'="frmCheckDependent('. (($field['type'] == 'select' or ($field['type'] == 'data' and isset($field['data_type']) and $field['data_type'] == 'select')) ? 'jQuery(this).val()' : 'this.value') .',\''.$field['id'].'\')"';
        }
        
        if($echo)
            echo $add_html;

        return $add_html;
    }
    
    public static function add_field_class($class, $field){
        if($field['type'] == 'scale' and isset($field['star']) and $field['star'])
            $class .= ' star';
        else if($field['type'] == 'date')
            $class .= ' frm_date';
        
        if((!is_admin() or defined('DOING_AJAX')) and isset($field['autocom']) and $field['autocom'] and ($field['type'] == 'select' or ($field['type'] == 'data' and isset($field['data_type']) and $field['data_type'] == 'select'))){
            global $frm_vars;
            $frm_vars['chosen_loaded'] = true;
            $class .= ' frm_chzn';
        }
            
        return $class;
    }
    
    public static function add_separate_value_opt_label($field){
        $style = $field['separate_value'] ? '' : "style='display:none;'";
        echo '<div class="frm-show-click">';
        echo '<div class="field_'. $field['id'] .'_option_key frm_option_val_label" '. $style .'>'. __('Option Label', 'formidable') .'</div>';
        echo '<div class="field_'. $field['id'] .'_option_key frm_option_key_label" '. $style .'>'. __('Saved Value', 'formidable') .'</div>';
        echo '</div>';
    }
    
    public static function options_form($field, $display, $values){
        global $frm_field, $frm_settings, $frm_vars;
        
        $form_fields = false;
        if(!in_array($field['type'], array('hidden', 'user_id')) and !empty($field['hide_field']) and is_array($field['hide_field']))
            $form_fields = $frm_field->getAll(array('fi.form_id' => (int)$field['form_id']), 'field_order');
        
        $frm_field_selection = FrmFieldsHelper::field_selection();
            
        if($field['type'] == 'date'){
            $locales = array(
                '' => __('English/Western', 'formidable'), 'af' => __('Afrikaans', 'formidable'), 
                'sq' => __('Albanian', 'formidable'), 'ar' => __('Arabic', 'formidable'), 
                'hy' => __('Armenian', 'formidable'), 'az' => __('Azerbaijani', 'formidable'), 
                'eu' => __('Basque', 'formidable'), 'bs' => __('Bosnian', 'formidable'), 
                'bg' => __('Bulgarian', 'formidable'), 'ca' => __('Catalan', 'formidable'), 
                'zh-HK' => __('Chinese Hong Kong', 'formidable'), 'zh-CN' => __('Chinese Simplified', 'formidable'), 
                'zh-TW' => __('Chinese Traditional', 'formidable'), 'hr' => __('Croatian', 'formidable'), 
                'cs' => __('Czech', 'formidable'), 'da' => __('Danish', 'formidable'), 
                'nl' => __('Dutch', 'formidable'), 'en-GB' => __('English/UK', 'formidable'), 
                'eo' => __('Esperanto', 'formidable'), 'et' => __('Estonian', 'formidable'), 
                'fo' => __('Faroese', 'formidable'), 'fa' => __('Farsi/Persian', 'formidable'), 
                'fi' => __('Finnish', 'formidable'), 'fr' => __('French', 'formidable'), 
                'fr-CH' => __('French/Swiss', 'formidable'), 'de' => __('German', 'formidable'), 
                'el' => __('Greek', 'formidable'), 'he' => __('Hebrew', 'formidable'), 
                'hu' => __('Hungarian', 'formidable'), 'is' => __('Icelandic', 'formidable'), 
                'it' => __('Italian', 'formidable'), 'ja' => __('Japanese', 'formidable'), 
                'ko' => __('Korean', 'formidable'), 'lv' => __('Latvian', 'formidable'), 
                'lt' => __('Lithuanian', 'formidable'), 'ms' => __('Malaysian', 'formidable'), 
                'no' => __('Norwegian', 'formidable'), 'pl' => __('Polish', 'formidable'), 
                'pt-BR' => __('Portuguese/Brazilian', 'formidable'), 'ro' => __('Romanian', 'formidable'), 
                'ru' => __('Russian', 'formidable'), 'sr' => __('Serbian', 'formidable'), 
                'sr-SR' => __('Serbian', 'formidable'), 'sk' => __('Slovak', 'formidable'), 
                'sl' => __('Slovenian', 'formidable'), 'es' => __('Spanish', 'formidable'), 
                'sv' => __('Swedish', 'formidable'), 'ta' => __('Tamil', 'formidable'), 
                'th' => __('Thai', 'formidable'), 'tu' => __('Turkish', 'formidable'), 
                'uk' => __('Ukranian', 'formidable'), 'vi' => __('Vietnamese', 'formidable') 
            );
        }else if($field['type'] == 'file'){
            $mimes = get_allowed_mime_types();
        }
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/options-form.php');  
    }
    
    public static function get_field_selection(){
        global $frm_field;
        $ajax = true;
        $current_field_id = (int)$_POST['field_id'];
        if(is_numeric($_POST['form_id'])){
            $selected_field = '';
            $fields = $frm_field->getAll(array('fi.form_id' => (int)$_POST['form_id']), 'field_order');
            if ($fields)
                require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/field-selection.php');
        }else{
            $selected_field = $_POST['form_id'];
        }
        
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/data_cat_selected.php');
        
        die();
    }
    
    public static function get_field_values(){
        global $frm_field, $frm_entry_meta;
        $current_field_id = $_POST['current_field'];
        $new_field = $frm_field->getOne($_POST['field_id']);
        
        $is_settings_page = ( $_POST['form_action'] == 'update_settings' ) ? true : false;
        $anything = $is_settings_page ? '' : __('Anything', 'formidable');
        
        if(!empty($_POST['name']) and $_POST['name'] != 'undefined')
            $field_name = $_POST['name'];
        if(!empty($_POST['t']) and $_POST['t'] != 'undefined')
            $field_type = $_POST['t'];
            
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/field-values.php');
        die();
    }
    
    public static function get_cat_opts(){
        global $frmpro_display, $frm_field;
        $display = $frmpro_display->getOne($_POST['display_id']);
        $fields = $frm_field->getAll("fi.form_id=$display->form_id and fi.type in ('select','radio')", 'field_order');
        echo '<option value=""></option>';
        foreach ($fields as $field)
            echo '<option value="'. $field->id .'">' . $field->name . '</option>';
        die();
    }
    
    public static function get_title_opts(){
        global $frmpro_display, $frm_field;
        $display = $frmpro_display->getOne($_POST['display_id']);
        
        if($display){
            $fields = $frm_field->getAll("fi.form_id=$display->form_id and fi.type not in ('divider','captcha','break','html')", 'field_order');
            echo '<option value=""></option>';
            foreach ($fields as $field)
                echo '<option value="'. $field->id .'">' . $field->name . '</option>';
        }
        die();
    }
    
    
    public static function date_field_js($field_id, $options){
        if(!isset($options['unique']) or !$options['unique'])
            return;
        
        $defaults = array(
            'entry_id' => 0, 'start_year' => 2000, 'end_year' => 2020,
            'locale' => '', 'unique' => 0, 'field_id' => 0
        );
        
        $options = wp_parse_args($options, $defaults);
        
        global $wpdb;
        
        $frm_field = new FrmField();
        $field = $frm_field->getOne($options['field_id']);
        unset($frm_field);

        if(isset($field->field_options['post_field']) and $field->field_options['post_field'] != ''){
            if($field->field_options['post_field'] == 'post_custom'){
                $query = "SELECT meta_value FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts p ON (p.ID=pm.post_id) WHERE meta_value != '' AND meta_key='". $field->field_options['custom_field'] ."'";
            }else{
                $query = "SELECT $post_field FROM $wpdb->posts WHERE 1=1";
            }
            $query .= " and post_status in ('publish','draft','pending','future','private')";

            $post_dates = $wpdb->get_col($query);
        }
        
        $query = $wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}frm_item_metas WHERE field_id=%d", $options['field_id']);
        if(is_numeric($options['entry_id'])){
            $query .= $wpdb->prepare(" and item_id != %d", $options['entry_id']);
        }else{
            $disabled = wp_cache_get($options['field_id'], 'frm_used_dates');
        }
        
        if(!isset($disabled) or !$disabled)
            $disabled = $wpdb->get_col($query);
        
        if(isset($post_dates) and $post_dates)
            $disabled = array_unique(array_merge((array)$post_dates, (array)$disabled));

        $disabled = apply_filters('frm_used_dates', $disabled, $field, $options);
        
        if(!$disabled)
            return;
            
        if(!is_numeric($options['entry_id']))
            wp_cache_set($options['field_id'], $disabled, 'frm_used_dates');
            
        $formatted = array();    
        foreach($disabled as $dis) //format to match javascript dates
           $formatted[] = date('Y-n-j', strtotime($dis)); 
        
        $disabled = $formatted;
        unset($formatted);
        
        echo ',beforeShowDay: function(date){var m=(date.getMonth()+1),d=date.getDate(),y=date.getFullYear();var disabled='. json_encode($disabled) .';if($.inArray(y+"-"+m+"-"+d,disabled) != -1){return [false];} return [true];}';

        //echo ',beforeShowDay: $.datepicker.noWeekends';
    }
    
    public static function ajax_get_data(){
        $entry_id = FrmAppHelper::get_param('entry_id'); 
        $field_id = FrmAppHelper::get_param('field_id');
        $current_field = (int)FrmAppHelper::get_param('current_field');
        
        global $frm_entry_meta, $frm_field;
        $data_field = $frm_field->getOne($field_id);
        $current = $frm_field->getOne($current_field);
        if(strpos($entry_id, ',')){
            $entry_id = explode(',', $entry_id);
            $meta_value = array();
            foreach($entry_id as $eid){
                $new_meta = FrmProEntryMetaHelper::get_post_or_meta_value($eid, $data_field);
                if($new_meta){
                    if(is_array($new_meta)){
                        foreach($new_meta as $nm){
                            array_push($meta_value, $nm);
                            unset($nm);
                        }
                    }else{
                        array_push($meta_value, $new_meta);
                    }
                }
                unset($new_meta);
                unset($eid);
            }
            
        }else{
            $meta_value = FrmProEntryMetaHelper::get_post_or_meta_value($entry_id, $data_field);
        }
        
        $value = FrmProFieldsHelper::get_display_value($meta_value, $data_field, array('html' => true));
        if(is_array($value))
            $value = implode(', ', $value);
            
        if(is_array($meta_value))
            $meta_value = implode(', ', $meta_value);
        
        if($value and !empty($value))
            echo "<p class='frm_show_it'>". $value ."</p>\n";
            
        $current_field = (array)$current;
        foreach($current->field_options as $o => $v){
            if(!isset($current_field[$o]))
                $current_field[$o] = $v;
            unset($o);
            unset($v);
        }
        echo '<input type="hidden" id="field_'. $current->field_key .'" name="item_meta['. $current->id .']" value="'. esc_attr($meta_value) .'" '. do_action('frm_field_input_html', $current_field, false) .'/>';
        die();
    }
    
    public static function ajax_data_options(){
        $hide_field = FrmAppHelper::get_param('hide_field');
        $entry_id = FrmAppHelper::get_param('entry_id');
        $selected_field_id = FrmAppHelper::get_param('selected_field_id');
        $field_id = FrmAppHelper::get_param('field_id');
        
        global $frm_field;
        $data_field = $frm_field->getOne($selected_field_id);
        
        if ( $entry_id == '' ) {
            die();
        }
        
        $entry_id = explode(',', $entry_id);
        
        $field_data = $frm_field->getOne($field_id);
        
        $field_name = "item_meta[$field_id]";
        if(isset($field_data->field_options['multiple']) and $field_data->field_options['multiple'] and ($field_data->type == 'select' or ($field_data->type == 'data' and isset($field_data->field_options['data_type']) and $field_data->field_options['data_type'] == 'select')))
            $field_name .= '[]';
        
        
        $field = array(
            'id' => $field_id, 'value' => '', 'default_value' => '', 'form_id' => $field_data->form_id,
            'type' => apply_filters('frm_field_type', $field_data->type, $field_data, ''),
            'options' => $field_data->options,
            'size' => (isset($field_data->field_options['size']) && $field_data->field_options['size'] != '') ? $field_data->field_options['size'] : '',
            'field_key' => $field_data->field_key
            //'value' => $field_data->value
        );
        
        if ($field['size'] == ''){
            global $frm_vars;
            $field['size'] = isset($frm_vars['sidebar_width']) ? $frm_vars['sidebar_width'] : '';
        }
        
        if(is_numeric($selected_field_id)){
            $field['options'] = array();
            
            $metas = FrmProEntryMetaHelper::meta_through_join($hide_field, $data_field, $entry_id);
			$metas = stripslashes_deep($metas);
            if($metas and (!isset($field_data->field_options['data_type']) or !in_array($field_data->field_options['data_type'], array('radio', 'checkbox'))) and
                (!isset($field_data->field_options['multiple']) or !$field_data->field_options['multiple'] or
                (isset($field_data->field_options['autocom']) and $field_data->field_options['autocom'])))
                $field['options'][''] = '';

            foreach ($metas as $meta){
                $field['options'][$meta->item_id] = FrmProEntryMetaHelper::display_value($meta->meta_value, $data_field, 
                    array('type' => $data_field->type, 'show_icon' => true, 'show_filename' => false)
                );
                unset($meta);
            }
            
            $field = apply_filters('frm_setup_new_fields_vars', $field, $field_data);
        }else if($selected_field_id == 'taxonomy'){
            if($entry_id == 0)
                die();
            
            if(is_array($entry_id)){
                $zero = array_search(0, $entry_id);
                if($zero !== false)
                    unset($entry_id[$zero]);
                if(empty($entry_id))
                    die();
            }
            
            $field = apply_filters('frm_setup_new_fields_vars', $field, $field_data);
            $cat_ids = array_keys($field['options']);
            
            $args = array('include' => implode(',', $cat_ids), 'hide_empty' => false);
            
            $post_type = FrmProFormsHelper::post_type($field_data->form_id);
            $args['taxonomy'] = FrmProAppHelper::get_custom_taxonomy($post_type, $field_data);
            if ( !$args['taxonomy'] ) {
                die();
            }
            
            $cats = get_categories($args);
            foreach($cats as $cat){
                if(!in_array($cat->parent, (array)$entry_id))
                    unset($field['options'][$cat->term_id]);
            }
            
            if(count($field['options']) == 1 and reset($field['options']) == '')
                die();
        } else {
            $field = apply_filters('frm_setup_new_fields_vars', $field, $field_data);
        }

        $auto_width = (isset($field['size']) && $field['size'] > 0) ? 'class="auto_width"' : '';
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/data-options.php');
        die();
    }
    
    public static function add_option(){
        global $frm_field;
        
        $id = $_POST['field_id'];
        $t = (isset($_POST['t'])) ? $_POST['t'] : false;
		if ($t == 'row' or $t == 'col'){
	        $field = $frm_field->getOne($id);
	        $options = maybe_unserialize($field->options);
		    list($columns,$rows) = FrmProFieldsHelper::get_table_options($options);
			if ($t == 'col'){
		        $last = (count($columns) ? array_pop(array_keys($columns)) : 'col_0');
				preg_match('/[0-9]+$/',$last,$matches);
		        $opt_key = 'col_' . ($matches[0] + 1);
		        $opt = 'Column '.(count($columns)+1);
		        $columns[$opt_key] = $opt;
		        $row_num = count($rows)-1;
		        $col_num = count($columns);
			}else{
		        $last = (count($rows) ? array_pop(array_keys($rows)) : 'row_0');
				preg_match('/[0-9]+$/',$last,$matches);
		        $opt_key = 'row_' . ($matches[0] + 1);
		        $opt = 'Row '.(count($rows)+1);
		        $rows[$opt_key] = $opt;
		        $row_num = count($rows);
			}
			$options = FrmProFieldsHelper::set_table_options($options, $columns, $rows);
	        $frm_field->update($id, array('options' => maybe_serialize($options)));

	        $field_data = $frm_field->getOne($id);
	        $field = (array) $field_data;
	        $field['value'] = null;
	        $field_name = "item_meta[$id]";
 
            $include_js = true;
            /*if($t == 'row')
                require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/grid-single-row.php');
            else
	            require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/grid-option.php'); 
	        */
	            
			die();
		}else
			FrmFieldsController::add_option();
	}
	
	public static function ajax_time_options(){
	    global $frmpro_settings, $frmdb, $wpdb, $frm_entry_meta;
	    
	    //posted vars = $time_field, $date_field, $date
	    extract($_POST);
	    
	    $time_key = str_replace('field_', '', $time_field);
	    $date_key = str_replace('field_', '', $date_field);
	    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($date)))
	        $date = FrmProAppHelper::convert_date($date, $frmpro_settings->date_format, 'Y-m-d');
	    $date_entries = $frm_entry_meta->getEntryIds(array('fi.field_key' => $date_key, 'meta_value' => $date));
        
        $remove = array();
        
	    if($date_entries and !empty($date_entries)){
	        $query = $wpdb->prepare("SELECT meta_value FROM $frmdb->entry_metas it LEFT JOIN $frmdb->fields fi ON (it.field_id = fi.id) WHERE fi.field_key=%s", $time_key);
	        if(is_numeric($entry_id))
	            $query = " and it.item_id != ". (int)$entry_id;
	        $used_times = $wpdb->get_col("$query and it.item_id in (". implode(',', $date_entries).")");
	        
	        if($used_times and !empty($used_times)){
	            $number_allowed = apply_filters('frm_allowed_time_count', 1, $time_key, $date_key);
	            $count = array();
	            foreach($used_times as $used){
	                if(isset($remove[$used]))
	                    continue;
	                    
	                if(!isset($count[$used]))
	                    $count[$used] = 0;
	                $count[$used]++;
	                
	                if((int)$count[$used] >= $number_allowed)
	                    $remove[$used] = $used;
	            }
	            unset($count);
	        }
	    }
	    
	    echo json_encode($remove);
	    die();
	}
	
	public static function _logic_row(){
	    if(!current_user_can('frm_edit_forms')){
	        global $frm_settings;
            die($frm_settings->admin_permission);
        }
	    
	    global $frm_field;
	    
	    $meta_name = FrmAppHelper::get_param('meta_name');
	    $form_id = FrmAppHelper::get_param('form_id');
	    $field_id = FrmAppHelper::get_param('field_id');
	    $hide_field = '';
        
        $form_fields = $frm_field->getAll(array('fi.form_id' => (int)$form_id), 'field_order');
        
        $field = $frm_field->getOne($field_id);
        $field = FrmFieldsHelper::setup_edit_vars($field);
        
        if(!isset($field['hide_field_cond'][$meta_name]))
            $field['hide_field_cond'][$meta_name] = '==';

        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/_logic_row.php');
        die();
	}
	
	public static function populate_calc_dropdown(){
	    if(isset($_POST['form_id']) and isset($_POST['field_id']))
	        echo FrmProFieldsHelper::get_shortcode_select($_POST['form_id'], 'frm_calc_'. $_POST['field_id'], 'calc');
	    die();
	}
	
	/* Trigger model actions */
	
	public static function create($field_data) {
        $frmpro_field = new FrmProField();
        return $frmpro_field->create($field_data);
    }
    
    public static function update($field_options, $field, $values) {
        $frmpro_field = new FrmProField();
        return $frmpro_field->update($field_options, $field, $values);
    }
    
    public static function duplicate($values) {
        $frmpro_field = new FrmProField();
        return $frmpro_field->duplicate($values);
    }

}
