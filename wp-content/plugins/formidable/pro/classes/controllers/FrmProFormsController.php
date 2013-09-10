<?php

class FrmProFormsController{
    function FrmProFormsController(){
        add_filter('frm_admin_list_form_action', 'FrmProFormsController::process_bulk_form_actions');
        add_action('frm_additional_form_options', 'FrmProFormsController::add_form_options');
        add_action('frm_additional_form_notification_options', 'FrmProFormsController::notifications', 10, 2);
        add_action('wp_ajax_frm_add_email_list', 'FrmProFormsController::add_email_list');
        add_action('wp_ajax_frm_add_postmeta_row', 'FrmProFormsController::_postmeta_row');
        add_action('wp_ajax_frm_add_posttax_row', 'FrmProFormsController::_posttax_row');
        add_action('frm_extra_form_instruction_tabs', 'FrmProFormsController::instruction_tabs');
        add_action('frm_extra_form_instructions', 'FrmProFormsController::instructions');
        add_action('frm_translation_page', 'FrmProFormsController::translate', 10, 2);
        add_filter('get_frm_stylesheet', 'FrmProFormsController::custom_stylesheet');
        add_action('frm_template_action_links', 'FrmProFormsController::template_action_links');
        add_action('frm_ajax_forms_export', 'FrmProFormsController::export_template');
        add_action('frm_ajax_forms_import', 'FrmProFormsController::import_templates');
        add_filter('frmpro_field_links', 'FrmProFormsController::add_field_link', 10, 3);
        add_filter('frm_drag_field_class', 'FrmProFormsController::drag_field_class');
        add_action('formidable_shortcode_atts', 'FrmProFormsController::formidable_shortcode_atts');
        add_filter('frm_form_fields_class', 'FrmProFormsController::form_fields_class', 10, 2);
        add_filter('frm_content', 'FrmProFormsController::filter_content', 10, 3);
        add_filter('frm_submit_button', 'FrmProFormsController::submit_button_label', 5, 2);
        add_filter('frm_error_icon', 'FrmProFormsController::error_icon');
        add_filter('frm_form_replace_shortcodes', 'FrmProFormsController::replace_shortcodes', 10, 2);
        add_action('wp_ajax_frm_add_form_logic_row', 'FrmProFormsController::_logic_row');
        add_action('wp_ajax_frm_get_default_html', 'FrmProFormsController::get_email_html');
    }
    
    public static function process_bulk_form_actions($errors){
        if(!isset($_POST)) return;
        global $frm_form;
        
        $bulkaction = FrmAppHelper::get_param('action');
        if($bulkaction == -1)
            $bulkaction = FrmAppHelper::get_param('action2');

        if(!empty($bulkaction) and strpos($bulkaction, 'bulk_') === 0){
            if(isset($_GET) and isset($_GET['action']))
                $_SERVER['REQUEST_URI'] = str_replace('&action='.$_GET['action'], '', $_SERVER['REQUEST_URI']);
            if(isset($_GET) and isset($_GET['action2']))
                $_SERVER['REQUEST_URI'] = str_replace('&action='.$_GET['action2'], '', $_SERVER['REQUEST_URI']);
            
            $bulkaction = str_replace('bulk_', '', $bulkaction);
        }else{
            $bulkaction = '-1';
            if(isset($_POST['bulkaction']) and $_POST['bulkaction'] != '-1')
                $bulkaction = $_POST['bulkaction'];
            else if(isset($_POST['bulkaction2']) and $_POST['bulkaction2'] != '-1')
                $bulkaction = $_POST['bulkaction2'];
        }

        $ids = FrmAppHelper::get_param('item-action', '');
        if (empty($ids)){
            $errors[] = __('No forms were specified', 'formidable');
        }else{                
            if($bulkaction == 'export'){
                $controller = 'forms';
                if(is_array($ids))
                    $ids = implode(',', $ids);
                
                if(isset($_GET['page']) and $_GET['page'] == 'formidable-templates')
                    $is_template = true;
                include_once(FRMPRO_VIEWS_PATH.'/shared/xml.php');
            }else{
                if(!current_user_can('frm_delete_forms')){
                    global $frm_settings;
                    $errors[] = $frm_settings->admin_permission;
                }else{
                    if(!is_array($ids))
                        $ids = explode(',', $ids);
                        
                    if(is_array($ids)){
                        if($bulkaction == 'delete'){
                            foreach($ids as $form_id)
                                $frm_form->destroy($form_id);
                        }
                    }
                }
            }
        }
        return $errors;
    }
    
    public static function add_form_options($values){ 
        global $frm_editable_roles;
        
        if(!$frm_editable_roles)
            $frm_editable_roles = get_editable_roles();
        $post_types = FrmProAppHelper::get_custom_post_types();
        $show_post_type = false;
        if(isset($values['fields']) and $values['fields']){
            foreach($values['fields'] as $field){
                if(!$show_post_type and $field['post_field'] != '')
                    $show_post_type = true;
            }
        }
        
        require(FRMPRO_VIEWS_PATH.'/frmpro-forms/add_form_options.php');
    }
    
    public static function notifications($values, $atts){
        global $wpdb, $frmdb;
        extract($atts);
        require(FRMPRO_VIEWS_PATH.'/frmpro-forms/notifications.php');
    }
    
    public static function add_email_list(){
        global $frm_field;
        $email_key = $_POST['list_id'];
        $form_id = $_POST['form_id'];
        $first_email = ($email_key) ? false : true;
        $frmpro_is_installed = true;
        
        $notification = FrmProFormsHelper::get_default_notification_opts();
        $values = array('fields' => array(), 'id' => $form_id);
        $fields = $frm_field->getAll(array('fi.form_id' => $form_id));
        foreach($fields as $k => $f){
            $values['fields'][] = (array)$f;
            unset($k);
            unset($f);
        }
        include(FRM_VIEWS_PATH.'/frm-forms/notification.php');
        die();
    }
    
    public static function post_options($values){
        $post_types = FrmProAppHelper::get_custom_post_types();
        if(!$post_types) return;
        
        $post_type = FrmProForm::post_type($values);
        if(function_exists('get_object_taxonomies'))
            $taxonomies = get_object_taxonomies($post_type);
        
        $echo = true;
        $show_post_type = false;
        if(isset($values['fields']) and $values['fields']){
            foreach($values['fields'] as $field){
                if(!$show_post_type and $field['post_field'] != '')
                    $show_post_type = true;
            }
        }
        
        if($show_post_type)
            $values['create_post'] = true;
            
        $form_id = (int)$_GET['id'];
        $display = FrmProDisplay::get_form_custom_display($form_id);
        if($display)
            $display = FrmProDisplaysHelper::setup_edit_vars($display, true);
            
        require(FRMPRO_VIEWS_PATH.'/frmpro-forms/post_options.php');
    }
    
    public static function _postmeta_row(){
        global $frm_field;
        $custom_data = array('meta_name' => $_POST['meta_name'], 'field_id' => '');
        $values = array();
        
        if(isset($_POST['form_id']))
            $values['fields'] = $frm_field->getAll("fi.form_id='$_POST[form_id]' and fi.type not in ('divider', 'html', 'break', 'captcha')", ' ORDER BY field_order');
        $echo = false;
        include(FRMPRO_VIEWS_PATH.'/frmpro-forms/_custom_field_row.php');
        die();
    }
    
    public static function _posttax_row(){
        global $frm_field;
        
        $field_vars = array('meta_name' => '', 'field_id' => '', 'show_exclude' => 0, 'exclude_cat' => 0);
        $post_type = $_POST['post_type'];
        $tax_key = $_POST['meta_name'];
        
        if($post_type and function_exists('get_object_taxonomies'))
            $taxonomies = get_object_taxonomies($post_type);
        
        $values = array();
        
        if(isset($_POST['form_id'])){
            $values['fields'] = $frm_field->getAll("fi.form_id='$_POST[form_id]' and fi.type in ('checkbox', 'radio', 'select', 'tag', 'data')", ' ORDER BY field_order');
            $values['id'] = $_POST['form_id'];
        }
        $echo = false;
        include(FRMPRO_VIEWS_PATH.'/frmpro-forms/_post_taxonomy_row.php');
        die();
    }
    
    public static function instruction_tabs(){
        include(FRMPRO_VIEWS_PATH.'/frmpro-forms/instruction_tabs.php');
    }
    
    public static function instructions(){
        $tags = array(
            'date' => __('Current Date', 'formidable'), 
            'time' => __('Current Time', 'formidable'), 
            'email' => __('Email', 'formidable'), 
            'login' => __('Login', 'formidable'), 
            'display_name' => __('Display Name', 'formidable'), 
            'first_name' => __('First Name', 'formidable'), 
            'last_name' => __('Last Name', 'formidable'), 
            'user_id' => __('User ID', 'formidable'), 
            'user_meta key=whatever' => __('User Meta', 'formidable'), 
            'post_id' => __('Post ID', 'formidable'), 
            'post_title' => __('Post Title', 'formidable'),
            'post_author_email' => __('Author Email', 'formidable'),
            'post_meta key=whatever' => __('Post Meta', 'formidable'),
            'ip' => __('IP Address', 'formidable'), 
            'auto_id start=1' => __('Auto Increment', 'formidable'), 
            'get param=whatever' => array('label' => __('GET/POST', 'formidable'), 'title' => __('A variable from the URL or value posted from previous page.', 'formidable') .' '. __('Replace \'whatever\' with the parameter name. In url.com?product=form, the variable is \'product\'. You would use [get param=product] in your field.', 'formidable'))
        );
        include(FRMPRO_VIEWS_PATH.'/frmpro-forms/instructions.php');
    }
    
    public static function translate($form, $action){
        global $frm_field, $wpdb, $sitepress, $sitepress_settings;
        
        if(!function_exists('icl_t')){
            _e('You do not have WPML installed', 'formidable');
            return;
        }
        
        if($action == 'update_translate' and isset($_POST) and isset($_POST['frm_wpml'])){
            foreach($_POST['frm_wpml'] as $tkey => $t){
                $st = array('value' => $t['value']);
                $st['status'] = (isset($t['status'])) ? $t['status'] : ICL_STRING_TRANSLATION_NOT_TRANSLATED;
                
                if(is_numeric($tkey)){
                    $wpdb->update("{$wpdb->prefix}icl_string_translations", $st, array('id' => $tkey));
                }else if(!empty($t['value'])){
                    $info = explode('_', $tkey);
                    if(!is_numeric($info[0]))
                        continue;
                        
                    $st['string_id'] = $info[0];
                    $st['language']  = $info[1];
                    $st['translator_id'] = get_current_user_id();
                    $st['translation_date'] = current_time('mysql');
 
                    $wpdb->insert("{$wpdb->prefix}icl_string_translations", $st);
                }
                unset($t);
                unset($tkey);
            }
        }
        
        $id = $form->id;
        $langs = $sitepress->get_active_languages();
        $default_language = !empty($sitepress_settings['st']['strings_language']) ? $sitepress_settings['st']['strings_language'] : $sitepress->get_default_language();
        ksort($langs);
        $lang_count = (count($langs)-1);
        
        if(class_exists('FormidableWPML')){
            $formidable_wpml = new FormidableWPML();
            $formidable_wpml->get_translatable_items(array(), 'formidable', '');
        }
        
        $strings = $wpdb->get_results("SELECT id, name, value, language FROM {$wpdb->prefix}icl_strings
            WHERE context='formidable' AND name LIKE '{$id}_%' ORDER BY name DESC", OBJECT_K
        );

        if($strings){
            $translations = $wpdb->get_results("SELECT id, string_id, value, status, language 
                FROM {$wpdb->prefix}icl_string_translations WHERE string_id in (". implode(',', array_keys($strings)).") 
                ORDER BY language ASC"
            );
            $col_order = array($default_language);
        }
        
        $fields = $frm_field->getAll(array('fi.form_id' => $id), 'field_order');
        $values = FrmAppHelper::setup_edit_vars($form, 'forms', $fields, true);
        
        include(FRMPRO_VIEWS_PATH . '/frmpro-forms/translate.php');
    }
    
    public static function custom_stylesheet($previous_css){
        global $frmpro_settings, $frm_datepicker_loaded, $frm_css_loaded;
        $uploads = wp_upload_dir();
        $css_file = array();
        
        if(!$frm_css_loaded){
            //include css in head
            if(is_readable($uploads['basedir'] .'/formidable/css/formidablepro.css')){
                if(is_ssl() and !preg_match('/^https:\/\/.*\..*$/', $uploads['baseurl']))
                    $uploads['baseurl'] = str_replace('http://', 'https://', $uploads['baseurl']);
                $css_file['formidable'] = $uploads['baseurl'] .'/formidable/css/formidablepro.css';
            }else
                $css_file['formidable'] = FRM_SCRIPT_URL . '&amp;controller=settings';
        }

        if($frm_datepicker_loaded and !empty($frm_datepicker_loaded))
            wp_enqueue_style('jquery-theme');

        return $css_file;
    }
    
    public static function template_action_links($form){
        echo '| <span><a href="'.FRM_SCRIPT_URL.'&controller=forms&frm_action=export&id='. $form->id .'" title="'. __('Export Template', 'formidable') . ' '. $form->name .'">'. __('Export Template', 'formidable') .'</a></span>';
    }
    
    public static function export_template(){
        $form_id = FrmAppHelper::get_param('id');
        if(current_user_can('frm_edit_forms')){
            global $frmdb, $frm_form, $frm_field, $current_user, $frm_settings, $frmpro_settings;
            $form = $frm_form->getOne($form_id);
            $form->options = maybe_unserialize($form->options);
            $fields = $frm_field->getAll(array('fi.form_id' => $form_id), 'field_order');
            
            require(FRMPRO_VIEWS_PATH.'/frmpro-forms/export_template.php');
        }else{
            global $frm_settings;
            wp_die($frm_settings->admin_permission);
        }
    }
    
    public static function import_templates(){
        if(current_user_can('frm_edit_forms')){
            global $frmpro_settings;
            $path = (isset($_POST) and isset($_POST['path'])) ? $_POST['path'] : $frmpro_settings->template_path;         
            FrmFormsController::add_default_templates($path, false);
        }else{
            global $frm_settings;
            wp_die($frm_settings->admin_permission);
        }
    }
    
    public static function add_field_link($field_type, $id, $field_key){
        return "<a href=\"javascript:add_frm_field_link($id,'$field_key');\">$field_type</a>";
    }
    
    public static function drag_field_class($class){
        return ' class="field_type_list"';
    }
    
    public static function formidable_shortcode_atts($atts){
        global $frm_readonly, $frm_editing_entry, $frm_show_fields, $frmdb;
        $frm_readonly = $atts['readonly'];
        $frm_editing_entry = false;
        
        if(!is_array($atts['fields']))
            $frm_show_fields = explode(',', $atts['fields']);
        else
            $frm_show_fields = array();
            
        if($atts['entry_id'] == 'last'){
            global $user_ID, $frm_entry_meta;
            if($user_ID){
                $where_meta = array('form_id' => $atts['id'], 'user_id' => $user_ID);
                $frm_editing_entry = $frmdb->get_var($frmdb->entries, $where_meta, 'id', 'created_at DESC');
            }
        }else if($atts['entry_id']){
            $frm_editing_entry = $atts['entry_id'];
        }
    }
    
    public static function form_fields_class($class, $values){
        global $frm_page_num;
        if($frm_page_num)
            $class .= ' frm_page_num_'. $frm_page_num;

        return $class;
    }
    
    public static function filter_content($content, $form, $entry=false){
        if($entry and is_numeric($entry)){
            global $frm_entry;
            $entry = $frm_entry->getOne($entry);
        }else{
            $entry_id = (isset($_POST) and isset($_POST['id'])) ? $_POST['id'] : false;
            if($entry_id){
                global $frm_entry;
                $entry = $frm_entry->getOne($entry_id);
            }
        }
        if(!$entry) return $content;
        if(is_object($form))
            $form = $form->id;
        $shortcodes = FrmProAppHelper::get_shortcodes($content, $form);
        $content = FrmProFieldsHelper::replace_shortcodes($content, $entry, $shortcodes);
        return $content;
    }
    
    public static function submit_button_label($submit, $form){
        global $frm_next_page;
        if(isset($frm_next_page[$form->id])){ 
            $submit = $frm_next_page[$form->id];
            if(is_object($submit))
                $submit = $submit->name;
        }
        return $submit;
    }
    
    public static function error_icon(){
        global $frmpro_settings;
        $icon = FRMPRO_IMAGES_URL .'/error_icons/'. $frmpro_settings->error_icon;
        return $icon;
    }
    
    public static function replace_shortcodes($html, $form){
        preg_match_all("/\[(if )?(deletelink|back_label|back_hook|back_button)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?/s", $html, $shortcodes, PREG_PATTERN_ORDER);
        
        if(empty($shortcodes[0]))
            return $html;
            
        foreach ($shortcodes[0] as $short_key => $tag){
            $replace_with = '';
            $atts = shortcode_parse_atts( $shortcodes[3][$short_key] );
            
            switch($shortcodes[2][$short_key]){
                case 'deletelink':
                    $replace_with = FrmProEntriesController::entry_delete_link($atts);
                break;
                case 'back_label':
                    $replace_with = __('Previous', 'formidable');
                break;
                case 'back_hook':
                    $replace_with = apply_filters('frm_back_button_action', '', $form);
                break;
                case 'back_button':
                    global $frm_prev_page;
                    if(!$frm_prev_page or !is_array($frm_prev_page) or !isset($frm_prev_page[$form->id]) or empty($frm_prev_page[$form->id]))
                        unset($replace_with);
                    else
                        $html = str_replace('[/if back_button]', '', $html);
                break;
            } 
            
            if(isset($replace_with))
                $html = str_replace($shortcodes[0][$short_key], $replace_with, $html);
            
            unset($short_key);
            unset($tag);
            unset($replace_with);
        }
        
        return $html;
    }
    
    public static function _logic_row(){
	    global $frm_form, $frm_field;
	    
	    $meta_name = FrmAppHelper::get_param('meta_name');
	    $form_id = FrmAppHelper::get_param('form_id');
	    $email_key = FrmAppHelper::get_param('email_id');
	    $hide_field = '';
        
        $form_fields = $frm_field->getAll("fi.form_id = ". (int)$form_id ." and (type in ('select','radio','checkbox','10radio','scale','data') or (type = 'data' and (field_options LIKE '\"data_type\";s:6:\"select\"%' OR field_options LIKE '%\"data_type\";s:5:\"radio\"%' OR field_options LIKE '%\"data_type\";s:8:\"checkbox\"%') ))", " ORDER BY field_order");
        
        $form = $frm_form->getOne($form_id);
        $form->options = maybe_unserialize($form->options);
        $notification = (isset($form->options['notification']) and isset($form->options['notification'][$email_key])) ? $form->options['notification'][$email_key] : array();
        
        if(!isset($notification['conditions']))
            $notification['conditions'] = array();
        
        if(isset($notification['conditions'][$meta_name]))
            $condition = $notification['conditions'][$meta_name];
        else
            $condition = array('hide_field_cond' => '==', 'hide_field' => '');
            
        if(!isset($condition['hide_field_cond']))
            $condition['hide_field_cond'] = '==';

        include(FRMPRO_VIEWS_PATH.'/frmpro-forms/_logic_row.php');
        die();
	}
	
	public static function get_email_html(){
	    echo FrmProEntriesController::show_entry_shortcode(array('form_id' => $_POST['form_id'], 'default_email' => true, 'plain_text' => $_POST['plain_text']));
	    die();
	}
}
