<?php

class FrmProFormsController{
    public static function load_hooks() {
        add_action('frm_additional_form_options', 'FrmProFormsController::add_form_options');
        add_action('frm_additional_form_notification_options', 'FrmProFormsController::notifications', 10, 2);
        add_action('wp_ajax_frm_add_email_list', 'FrmProFormsController::add_email_list');
        add_action('wp_ajax_frm_add_postmeta_row', 'FrmProFormsController::_postmeta_row');
        add_action('wp_ajax_frm_add_posttax_row', 'FrmProFormsController::_posttax_row');
        add_action('frm_extra_form_instruction_tabs', 'FrmProFormsController::instruction_tabs');
        add_action('frm_extra_form_instructions', 'FrmProFormsController::instructions');
        add_filter('get_frm_stylesheet', 'FrmProFormsController::custom_stylesheet');
        add_filter('frmpro_field_links', 'FrmProFormsController::add_field_link', 10, 3);
        add_filter('frm_drag_field_class', 'FrmProFormsController::drag_field_class');
        add_action('formidable_shortcode_atts', 'FrmProFormsController::formidable_shortcode_atts', 10, 2);
        add_filter('frm_form_fields_class', 'FrmProFormsController::form_fields_class', 10, 2);
        add_action('frm_entry_form', 'FrmProFormsController::form_hidden_fields', 10, 2);
        add_filter('frm_content', 'FrmProFormsController::filter_content', 10, 3);
        add_filter('frm_submit_button', 'FrmProFormsController::submit_button_label', 5, 2);
        add_filter('frm_form_replace_shortcodes', 'FrmProFormsController::replace_shortcodes', 10, 3);
        add_action('wp_ajax_frm_add_form_logic_row', 'FrmProFormsController::_logic_row');
        add_action('wp_ajax_frm_get_default_html', 'FrmProFormsController::get_email_html');
        
        add_filter('frm_setup_new_form_vars', 'FrmProFormsController::setup_new_vars');
        add_filter('frm_setup_edit_form_vars', 'FrmProFormsController::setup_edit_vars');
        
        // trigger form model
        add_filter('frm_form_options_before_update', 'FrmProFormsController::update_options', 10, 2);
        add_filter('frm_update_form_field_options', 'FrmProFormsController::update_form_field_options', 10, 3);
        add_action('frm_update_form', 'FrmProFormsController::update', 10, 2);
        add_filter('frm_after_duplicate_form_values', 'FrmProFormsController::after_duplicate', 10, 2);
        add_filter('frm_validate_form', 'FrmProFormsController::validate', 10, 2);
    }
    
    public static function add_form_options($values){ 
        global $frm_vars;
        
        if(!isset($frm_vars['editable_roles']) or !$frm_vars['editable_roles'])
            $frm_vars['editable_roles'] = get_editable_roles();
        $post_types = FrmProAppHelper::get_custom_post_types();
        $show_post_type = false;
        if(isset($values['fields']) and $values['fields']){
            foreach($values['fields'] as $field){
                if(!$show_post_type and $field['post_field'] != '')
                    $show_post_type = true;
            }
        }
        
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/add_form_options.php');
    }
    
    public static function notifications($values, $atts){
        global $wpdb, $frmdb;
        extract($atts);
        $form_fields = $values['fields'];
        unset($values['fields']);
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/notifications.php');
    }
    
    public static function add_email_list(){
        global $frm_field, $frm_vars;
        $email_key = $_POST['list_id'];
        
        $form_id = $_POST['form_id'];
        $frm_form = new FrmForm();
        $form = $frm_form->getOne($form_id);
        unset($frm_form);
        
        $first_email = $email_key ? false : true;
        
        $notification = FrmProFormsHelper::get_default_notification_opts();
        $values = array('fields' => array(), 'id' => $form_id);
        $fields = $frm_field->getAll(array('fi.form_id' => $form_id));
        foreach($fields as $k => $f){
            $values['fields'][] = (array)$f;
            unset($k);
            unset($f);
        }
        include(FrmAppHelper::plugin_path() .'/classes/views/frm-forms/notification.php');
        die();
    }
    
    public static function post_options($values){
        $post_types = FrmProAppHelper::get_custom_post_types();
        if(!$post_types) return;
        
        $post_type = FrmProFormsHelper::post_type($values);
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
            
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/post_options.php');
    }
    
    public static function _postmeta_row(){
        global $frm_field, $wpdb;
        $custom_data = array('meta_name' => $_POST['meta_name'], 'field_id' => '');
        $values = array();
        
        if(isset($_POST['form_id']))
            $values['fields'] = $frm_field->getAll("fi.form_id='$_POST[form_id]' and fi.type not in ('divider', 'html', 'break', 'captcha')", ' ORDER BY field_order');
        $echo = false;
        
        $limit = (int) apply_filters( 'postmeta_form_limit', 40 );
    	$cf_keys = $wpdb->get_col( "SELECT meta_key FROM $wpdb->postmeta GROUP BY meta_key ORDER BY meta_key LIMIT $limit" );
    	if(!is_array($cf_keys))
            $cf_keys = array();
        if(!in_array('_thumbnail_id', $cf_keys))
            $cf_keys[] = '_thumbnail_id';
        if ( !empty($cf_keys) )
    		natcasesort($cf_keys);
    	
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/_custom_field_row.php');
        die();
    }
    
    public static function _posttax_row(){
        if(isset($_POST['field_id']))
            $field_vars = array('meta_name' => $_POST['meta_name'], 'field_id' => $_POST['field_id'], 'show_exclude' => (int)$_POST['show_exclude'], 'exclude_cat' => ((int)$_POST['show_exclude']) ? '-1' : 0);
        else
            $field_vars = array('meta_name' => '', 'field_id' => '', 'show_exclude' => 0, 'exclude_cat' => 0);
        
        $tax_meta = $_POST['tax_key'];
        $post_type = $_POST['post_type'];
        
        
        if ( $post_type ) {
            $taxonomies = get_object_taxonomies($post_type);
        }
        
        $values = array();
        
        if(isset($_POST['form_id'])){
            $frm_field = new FrmField();
            $values['fields'] = $frm_field->getAll("fi.form_id='". (int)$_POST['form_id'] ."' and fi.type in ('checkbox', 'radio', 'select', 'tag', 'data')", ' ORDER BY field_order');
            unset($frm_field);
            $values['id'] = $_POST['form_id'];
        }
        $echo = false;
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/_post_taxonomy_row.php');
        die();
    }
    
    public static function instruction_tabs(){
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/instruction_tabs.php');
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
            'auto_id start=1' => __('Increment', 'formidable'), 
            'get param=whatever' => array('label' => __('GET/POST', 'formidable'), 'title' => __('A variable from the URL or value posted from previous page.', 'formidable') .' '. __('Replace \'whatever\' with the parameter name. In url.com?product=form, the variable is \'product\'. You would use [get param=product] in your field.', 'formidable')),
            'server param=whatever' => array('label' => __('SERVER', 'formidable'), 'title' => __('A variable from the PHP SERVER array.', 'formidable') .' '. __('Replace \'whatever\' with the parameter name. To get the url of the current page, use [server param="REQUEST_URI"] in your field.', 'formidable')),
        );
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/instructions.php');
    }
    
    public static function custom_stylesheet($previous_css){
        global $frmpro_settings, $frm_vars;
        $uploads = wp_upload_dir();
        $css_file = array();
        
        if(!isset($frm_vars['css_loaded']) || !$frm_vars['css_loaded']){
            //include css in head
            if(is_readable($uploads['basedir'] .'/formidable/css/formidablepro.css')){
                if(is_ssl() and !preg_match('/^https:\/\/.*\..*$/', $uploads['baseurl']))
                    $uploads['baseurl'] = str_replace('http://', 'https://', $uploads['baseurl']);
                $css_file['formidable'] = $uploads['baseurl'] .'/formidable/css/formidablepro.css';
            }else
                $css_file['formidable'] = admin_url('admin-ajax.php') . '?action=frmpro_css';
        }

        if(isset($frm_vars['datepicker_loaded']) and !empty($frm_vars['datepicker_loaded']))
            wp_enqueue_style('jquery-theme');

        return $css_file;
    }
    
    public static function add_field_link($field_type, $id, $field_key){
        return "<a href=\"javascript:add_frm_field_link($id,'$field_key');\">$field_type</a>";
    }
    
    public static function drag_field_class($class){
        return ' class="field_type_list"';
    }
    
    public static function formidable_shortcode_atts($atts, $all_atts){
        global $frm_vars, $frmdb;
        $frm_vars['readonly'] = $atts['readonly'];
        $frm_vars['editing_entry'] = false;
        
        if(!is_array($atts['fields']))
            $frm_vars['show_fields'] = explode(',', $atts['fields']);
        else
            $frm_vars['show_fields'] = array();
            
        if(!empty($atts['exclude_fields'])){
            global $wpdb;
            if(!is_array($atts['exclude_fields']))
                $atts['exclude_fields'] = explode(',', $atts['exclude_fields']);
            
            $atts['exclude_fields'] = array_filter( $atts['exclude_fields'], 'sanitize_key' );
            
            $frm_vars['show_fields'] = $wpdb->get_col("SELECT id FROM $frmdb->fields WHERE form_id=". (int)$atts['id'] ." AND id NOT in ('". implode("','", $atts['exclude_fields']) ."') AND field_key NOT in ('". implode("','", $atts['exclude_fields']) ."')");
        }
            
        if($atts['entry_id'] == 'last'){
            global $frm_entry_meta;
            $user_ID = get_current_user_id();
            if($user_ID){
                $where_meta = array('form_id' => $atts['id'], 'user_id' => $user_ID);
                $frm_vars['editing_entry'] = $frmdb->get_var($frmdb->entries, $where_meta, 'id', 'created_at DESC');
            }
        }else if($atts['entry_id']){
            $frm_vars['editing_entry'] = $atts['entry_id'];
        }
        
        foreach($atts as $unset => $val){
            if ( is_array($all_atts) && isset($all_atts[$unset]) ) {
                unset($all_atts[$unset]);
            }
            unset($unset);
            unset($val);
        }
        
        if ( is_array($all_atts) ){
            foreach($all_atts as $att => $val){
                $_GET[$att] = urlencode($val);
                unset($att);
                unset($val);
            }
        }
    }
    
    public static function form_fields_class($class, $values){
        global $frm_page_num;
        if($frm_page_num)
            $class .= ' frm_page_num_'. $frm_page_num;

        return $class;
    }
    
    public static function form_hidden_fields($form){
        if(is_user_logged_in() and isset($form->options['save_draft']) and $form->options['save_draft'] == 1)
            echo '<input type="hidden" name="frm_saving_draft" class="frm_saving_draft" value="" />';
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
        global $frm_vars;
        if(isset($frm_vars['next_page'][$form->id])){ 
            $submit = $frm_vars['next_page'][$form->id];
            if(is_object($submit))
                $submit = $submit->name;
        }
        return $submit;
    }
    
    public static function replace_shortcodes($html, $form, $values=array()){
        preg_match_all("/\[(if )?(deletelink|back_label|back_hook|back_button|draft_label|save_draft|draft_hook)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?/s", $html, $shortcodes, PREG_PATTERN_ORDER);
        
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
                    global $frm_vars;
                    if ( !$frm_vars['prev_page'] || !is_array($frm_vars['prev_page']) || !isset($frm_vars['prev_page'][$form->id]) || empty($frm_vars['prev_page'][$form->id]) ) {
                        unset($replace_with);
                    } else {
                        $classes = apply_filters('frm_back_button_class', array(), $form);
                        if ( !empty($classes) ) {
                            $html = str_replace('class="frm_prev_page', 'class="frm_prev_page '. implode(' ', $classes), $html);
                        }
                        
                        $html = str_replace('[/if back_button]', '', $html);
                    }
                break;
                case 'draft_label':
                    $replace_with = __('Save Draft', 'formidable');
                break;
                case 'save_draft':
                    if(!is_user_logged_in() or !isset($form->options['save_draft']) or $form->options['save_draft'] != 1 or (isset($values['is_draft']) and !$values['is_draft'])){
                        //remove button if user is not logged in, drafts are not allowed, or editing an entry that is not a draft
                        unset($replace_with);
                    }else{
                        $html = str_replace('[/if save_draft]', '', $html);
                    }
                break;
                case 'draft_hook':
                    $replace_with = apply_filters('frm_draft_button_action', '', $form);
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
    
    public static function include_logic_row($atts) {
        $defaults = array(
            'meta_name' => '',
            'condition' => array(
                'hide_field'        => '',
                'hide_field_cond'   => '==',
                'hide_opt'          => '',
            ),
            'key' => '', 'type' => 'form',
            'form_id' => 0, 'id' => '' ,
            'name' => '', 'names' => array(),
            'showlast' => '', 'onchange' => '',
        );
        
        extract(wp_parse_args($atts, $defaults));
        
        if ( empty($id) ) {
            $id = 'frm_logic_'. $key .'_'. $meta_name;
        }
        
        if ( empty($name) ) {
            $name = 'notification['. $key .'][conditions]['. $meta_name .']';
        }
        
        if ( empty($names) ) {
            $names = array(
                'hide_field' => $name .'[hide_field]',
                'hide_field_cond' => $name .'[hide_field_cond]',
                'hide_opt' => $name .'[hide_opt]',
            );
        }
        
        if ( $onchange == '' ) {
            $onchange = "frmGetFieldValues(this.value,'$key','$meta_name','". (isset($field['type']) ? $field['type'] : '') ."','". $names['hide_opt'] ."')";
        }
        
        $frm_field = new FrmField();
        $form_fields = $frm_field->getAll( array('fi.form_id' => (int)$form_id), 'field_order' );
        
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/_logic_row.php');
    }
    
    public static function _logic_row(){
	    global $frm_field;
	    
	    $meta_name = FrmAppHelper::get_param('meta_name');
	    $form_id = FrmAppHelper::get_param('form_id');
	    $key = FrmAppHelper::get_param('email_id');
        
        $frm_form = new FrmForm();
        $form = $frm_form->getOne($form_id);
        $form->options = maybe_unserialize($form->options);
        $notification = (isset($form->options['notification']) and isset($form->options['notification'][$key])) ? $form->options['notification'][$key] : array();
        
        if(!isset($notification['conditions']))
            $notification['conditions'] = array();
        
        if(isset($notification['conditions'][$meta_name]))
            $condition = $notification['conditions'][$meta_name];
        else
            $condition = array('hide_field_cond' => '==', 'hide_field' => '');
            
        if(!isset($condition['hide_field_cond']))
            $condition['hide_field_cond'] = '==';
        
        self::include_logic_row(array(
            'form_id' => $form->id,
            'form' => $form,
            'meta_name' => $meta_name,
            'condition' => $condition,
            'key' => $key,
        ));
        
        die();
	}
	
	public static function get_email_html(){
	    echo FrmProEntriesController::show_entry_shortcode(array('form_id' => $_POST['form_id'], 'default_email' => true, 'plain_text' => $_POST['plain_text']));
	    die();
	}
	
	public static function setup_new_vars($values) {
	    return FrmProFormsHelper::setup_new_vars($values);
	}
	
	public static function setup_edit_vars($values) {
	    return FrmProFormsHelper::setup_edit_vars($values);
	}
	
	/* Trigger model actions */
	public static function update_options($options, $values){
        $frmpro_form = new FrmProForm();
        return $frmpro_form->update_options($options, $values);
    }
    
    public static function update_form_field_options($field_options, $field, $values){        
        $frmpro_form = new FrmProForm();
        return $frmpro_form->update_form_field_options($field_options, $field, $values);
    }
    
    public static function update($id, $values){
        $frmpro_form = new FrmProForm();
        $frmpro_form->update($id, $values);
    }
    
    public static function after_duplicate($new_opts, $id) {
        $frmpro_form = new FrmProForm();
        return $frmpro_form->after_duplicate($new_opts, $id);
    }

    public static function validate( $errors, $values ){
        $frmpro_form = new FrmProForm();
        return $frmpro_form->validate( $errors, $values );
    }
}
