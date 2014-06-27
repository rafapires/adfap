<?php

class FrmProEntriesController{
    
    public static function load_hooks() {    
        add_action('admin_menu', 'FrmProEntriesController::menu', 11);
        add_filter('contextual_help', 'FrmProEntriesController::contextual_help', 10, 3 );
        add_action('admin_init', 'FrmProEntriesController::admin_js', 1);
        add_action('init', 'FrmProEntriesController::register_scripts');
        add_action('wp_enqueue_scripts', 'FrmProEntriesController::add_js');
        add_action('wp_footer', 'FrmProEntriesController::enqueue_footer_js', 1);
        add_action('wp_footer', 'FrmProEntriesController::footer_js', 20);
        add_action('admin_footer', 'FrmProEntriesController::enqueue_footer_js', 1);
        add_action('admin_footer', 'FrmProEntriesController::footer_js', 20);
        add_filter('frm_data_sort', 'FrmProEntriesController::data_sort', 20, 2);
        add_action('widgets_init', 'FrmProEntriesController::register_widgets' );
        add_filter('update_user_metadata', 'FrmProEntriesController::check_hidden_cols', 10, 5);
        add_action('updated_user_meta', 'FrmProEntriesController::update_hidden_cols', 10, 4);
        add_filter('set-screen-option', 'FrmProEntriesController::save_per_page', 10, 3);
        add_filter('frm_continue_to_new', 'FrmProEntriesController::maybe_editing', 10, 3);
        add_filter('frm_update_entry', 'FrmProEntriesController::check_draft_status', 10, 2);
        add_action('frm_after_create_entry', 'FrmProEntriesController::remove_draft_hooks', 1);
        add_action('frm_process_entry', 'FrmProEntriesController::process_update_entry', 10, 4);
        add_action('frm_display_form_action', 'FrmProEntriesController::edit_update_form', 10, 5);
        add_action('frm_submit_button_action', 'FrmProEntriesController::ajax_submit_button', 10, 2);
        add_filter('frm_success_filter', 'FrmProEntriesController::get_confirmation_method', 10, 3);
        add_action('frm_success_action', 'FrmProEntriesController::confirmation', 10, 5);
        add_action('deleted_post', 'FrmProEntriesController::delete_entry');
        add_action('trashed_post', 'FrmProEntriesController::trashed_post');
        add_action('untrashed_post', 'FrmProEntriesController::trashed_post');
        add_action('add_meta_boxes', 'FrmProEntriesController::create_entry_from_post_box', 10, 2);
        add_action('wp_ajax_frm_create_post_entry', 'FrmProEntriesController::create_post_entry');
        
        add_action('wp_ajax_frm_entries_csv', 'FrmProEntriesController::csv');
        add_action('wp_ajax_nopriv_frm_entries_csv', 'FrmProEntriesController::csv');
        
        add_filter('frmpro_fields_replace_shortcodes', 'FrmProEntriesController::filter_shortcode_value', 10, 4);
        add_filter('frm_display_value_custom', 'FrmProEntriesController::filter_display_value', 10, 3);
        
        //Shortcodes
        add_shortcode('formresults', 'FrmProEntriesController::get_form_results');
        add_shortcode('frm-search', 'FrmProEntriesController::get_search');
        add_shortcode('frm-entry-links', 'FrmProEntriesController::entry_link_shortcode');
        add_shortcode('frm-entry-edit-link', 'FrmProEntriesController::entry_edit_link');
        add_shortcode('frm-entry-update-field', 'FrmProEntriesController::entry_update_field');
        add_shortcode('frm-entry-delete-link', 'FrmProEntriesController::entry_delete_link');
        add_shortcode('frm-field-value', 'FrmProEntriesController::get_field_value_shortcode');
        add_shortcode('frm-show-entry', 'FrmProEntriesController::show_entry_shortcode');
		add_shortcode('frm-alt-color','FrmProEntriesController::change_row_color');
        
        add_action('frm_after_create_entry', 'FrmProEntriesController::maybe_set_cookie', 20, 2);
        add_action('wp_ajax_nopriv_frm_entries_ajax_set_cookie', 'FrmProEntriesController::ajax_set_cookie');
        add_action('wp_ajax_frm_entries_ajax_set_cookie', 'FrmProEntriesController::ajax_set_cookie');
        
        add_action('wp_ajax_frm_entries_create', 'FrmProEntriesController::ajax_create');
        add_action('wp_ajax_nopriv_frm_entries_create', 'FrmProEntriesController::ajax_create');
        add_action('wp_ajax_frm_entries_update', 'FrmProEntriesController::ajax_update');
        add_action('wp_ajax_nopriv_frm_entries_update', 'FrmProEntriesController::ajax_update');
        add_action('wp_ajax_frm_entries_destroy', 'FrmProEntriesController::wp_ajax_destroy');
        add_action('wp_ajax_nopriv_frm_entries_destroy', 'FrmProEntriesController::wp_ajax_destroy');
        add_action('wp_ajax_frm_entries_edit_entry_ajax', 'FrmProEntriesController::edit_entry_ajax');
        add_action('wp_ajax_nopriv_frm_entries_edit_entry_ajax', 'FrmProEntriesController::edit_entry_ajax');
        add_action('wp_ajax_frm_entries_update_field_ajax', 'FrmProEntriesController::update_field_ajax');
        add_action('wp_ajax_nopriv_frm_entries_update_field_ajax', 'FrmProEntriesController::update_field_ajax');
        add_action('wp_ajax_frm_entries_send_email', 'FrmProEntriesController::send_email');
        add_action('wp_ajax_nopriv_frm_entries_send_email', 'FrmProEntriesController::send_email');
        //add_action('wp_ajax_frm_forms_xml', 'FrmProFormsController::export_xml_direct');
        
        add_filter('frm_redirect_url', 'FrmProEntriesController::redirect_url');
        add_filter('frm_show_new_entry_page', 'FrmProEntriesController::allow_form_edit', 10, 2);
        add_filter('frm_setup_edit_entry_vars', 'FrmProEntriesController::setup_edit_vars', 10, 2);
        add_filter('frm_email_value', 'FrmProEntriesController::email_value', 10, 3);
        
        // Trigger entry model
        add_filter('frm_validate_entry', 'FrmProEntriesController::pre_validate', 15, 2);
        add_action('frm_validate_form_creation', 'FrmProEntriesController::validate', 10, 5);
        add_action('frm_after_create_entry', 'FrmProEntriesController::create_post', 40, 2);
        add_action('frm_after_update_entry', 'FrmProEntriesController::update_post', 40, 2);
        add_action('frm_before_destroy_entry', 'FrmProEntriesController::destroy_post', 10, 2);
        
        // Trigger entry meta model
        add_filter('frm_add_entry_meta', 'FrmProEntriesController::before_create_meta');
        add_action('frm_after_create_entry', 'FrmProEntriesController::create_meta', 10);
        add_filter('frm_update_entry_meta', 'FrmProEntriesController::before_update_meta');
        add_action('frm_after_update_entry', 'FrmProEntriesController::create_meta');
        add_filter('frm_validate_field_entry', 'FrmProEntriesController::validate_meta', 10, 2);
    }
    
    public static function menu(){
        global $frm_settings;
        if ( current_user_can('administrator') && !current_user_can('frm_view_entries') ) {
            global $wp_roles;
            $frm_roles = FrmAppHelper::frm_capabilities();
            foreach($frm_roles as $frm_role => $frm_role_description){
                if(!in_array($frm_role, array('frm_view_forms', 'frm_edit_forms', 'frm_delete_forms', 'frm_change_settings')))
                    $wp_roles->add_cap( 'administrator', $frm_role );
            }
        }
        add_submenu_page('formidable', $frm_settings->menu .' | '. __('Entries', 'formidable'), __('Entries', 'formidable'), 'frm_view_entries', 'formidable-entries', 'FrmProEntriesController::route');
        
        if(!isset($_GET['frm_action']) or !in_array($_GET['frm_action'], array('edit', 'show'))){
            add_filter('manage_'. sanitize_title($frm_settings->menu) .'_page_formidable-entries_columns', 'FrmProEntriesController::manage_columns');
            add_filter('manage_'. sanitize_title($frm_settings->menu) .'_page_formidable-entries_sortable_columns', 'FrmProEntriesController::sortable_columns');
            add_filter('get_user_option_manage'. sanitize_title($frm_settings->menu) .'_page_formidable-entriescolumnshidden', 'FrmProEntriesController::hidden_columns');
        }
        //add_filter( 'bulk_actions-' . sanitize_title($frm_settings->menu) .'_page_formidable-entries', 'FrmProEntriesController::bulk_action_options');
        add_action('admin_head-'. sanitize_title($frm_settings->menu) .'_page_formidable-entries', 'FrmProEntriesController::head');
    }
    
    public static function contextual_help($help, $screen_id, $screen){
        // Only add to certain screens. add_help_tab was introduced in WordPress 3.3
        if ( $screen_id != 'formidable_page_formidable-entries' or ! method_exists( $screen, 'add_help_tab' ) )
            return $help;
            
        if (!isset($_GET) or !isset($_GET['page']) or $_GET['page'] != 'formidable-entries' or (isset($_GET['frm_action']) and $_GET['frm_action'] != 'list'))
            return $help;

        $screen->add_help_tab( array(
            'id'      => 'formidable-entries-tab',
            'title'   => __( 'Overview', 'formidable' ),
            'content' => '<p>' . __('This screen provides access to all of your posts. You can customize the display of this screen to suit your workflow.', 'formidable') .'</p>
            <p>'. __('Hovering over a row in the posts list will display action links that allow you to manage your entry.', 'formidable') . '</p>',
        ));
        
        $screen->set_help_sidebar(
    	    '<p><strong>' . __('For more information:', 'formidable') . '</strong></p>' .
    	    '<p><a href="http://formidablepro.com/knowledgebase/manage-entries-from-the-back-end/" target="_blank">' . __('Documentation on Entries', 'formidable') . '</a></p>' .
    	    '<p><a href="http://formidablepro.com/help-topics/" target="_blank">' . __('Support', 'formidable') . '</a></p>'
    	);
    	
        return $help;
    }
    
    public static function head(){
        global $frmpro_settings;
        if($frmpro_settings->theme_css == -1)
            return;
        
        $css_file = array(FrmProAppHelper::jquery_css_url($frmpro_settings->theme_css));
        
        require(FrmAppHelper::plugin_path() .'/classes/views/shared/head.php');
    }
    
    public static function admin_js(){
        if (isset($_GET) and isset($_GET['page']) and ($_GET['page'] == 'formidable-entries' or $_GET['page'] == 'formidable-entry-templates' or $_GET['page'] == 'formidable-import')){
            
        	if($_GET['page'] == 'formidable-entries'){
        	    wp_enqueue_script('jquery-ui-datepicker');
        	    
        	    global $frm_settings;
                if($frm_settings->accordion_js){
                    wp_enqueue_script('jquery-ui-widget');
                    wp_enqueue_script('jquery-ui-accordion');
                }
        	}
        }
    }
    
    public static function remove_fullscreen($init){
        if(isset($init['plugins'])){
            $init['plugins'] = str_replace('wpfullscreen,', '', $init['plugins']);
            $init['plugins'] = str_replace('fullscreen,', '', $init['plugins']);
        }
        return $init;
    }
    
    public static function register_scripts(){
        global $wp_scripts, $frmpro_settings, $frm_settings;
        wp_register_script('jquery-frm-rating', FrmAppHelper::plugin_url() . '/pro/js/jquery.rating.min.js', array('jquery'), '4.11', true);
        wp_register_script('jquery-maskedinput', FrmAppHelper::plugin_url() . '/pro/js/jquery.maskedinput.min.js', array('jquery'), '1.3', true);
        wp_register_script('nicedit', FrmAppHelper::plugin_url() . '/pro/js/nicedit.js', array(), '1', true);
        if($frmpro_settings->theme_css != -1)
            wp_register_style('jquery-theme', FrmProAppHelper::jquery_css_url($frmpro_settings->theme_css), array(), FrmAppHelper::plugin_version());
        wp_register_script('jquery-chosen', FrmAppHelper::plugin_url() .'/pro/js/chosen.jquery.min.js', array('jquery'), '0.9.12', true);

        //jquery-ui-datepicker registered in WP 3.3
        if(!isset($wp_scripts->registered) or !isset( $wp_scripts->registered['jquery-ui-datepicker'])){
            $date_ver = FrmProAppHelper::datepicker_version();
            wp_register_script('jquery-ui-datepicker', FrmAppHelper::plugin_url() . '/pro/js/jquery.ui.datepicker'. $date_ver .'.js', array('jquery', 'jquery-ui-core'), empty($date_ver) ? '1.8.16' : trim($date_ver, '.'), true);
        }
        
        if($frm_settings->accordion_js and (!isset($wp_scripts->registered) or !isset( $wp_scripts->registered['jquery-ui-accordion']))){
            wp_register_script('jquery-ui-accordion', FrmAppHelper::plugin_url().'/pro/js/jquery.ui.accordion.js', array('jquery', 'jquery-ui-core'), '1.8.16', true);
        }
    }
    
    public static function add_js(){
        if(is_admin() and !defined('DOING_AJAX'))
            return;
         
        wp_enqueue_script('jquery-ui-core');
        
        global $frm_settings;
        if($frm_settings->accordion_js){
            wp_enqueue_script('jquery-ui-widget');
            wp_enqueue_script('jquery-ui-accordion');
        }
    }
    
    public static function enqueue_footer_js(){
        global $frm_vars, $frm_input_masks;
        
        if(empty($frm_vars['forms_loaded']))
            return;
        
        $scripts = array();
        if(!defined('DOING_AJAX') or (isset($frm_vars['preview']) and $frm_vars['preview']))
            $scripts[] = 'formidable';
        
        if(isset($frm_vars['recap_script']) and $frm_vars['recap_script'])
            $scripts[] = 'recaptcha-ajax';
            
        $styles = array();
        
        if(isset($frm_vars['rte_loaded']) && !empty($frm_vars['rte_loaded']))
            $scripts[] = 'nicedit';
        
        if ( isset($frm_vars['tinymce_loaded']) && $frm_vars['tinymce_loaded'] ) {
            _WP_Editors::enqueue_scripts();
        }

        if(isset($frm_vars['datepicker_loaded']) and !empty($frm_vars['datepicker_loaded'])){
            if(is_array($frm_vars['datepicker_loaded'])){
                foreach($frm_vars['datepicker_loaded'] as $fid => $o){
                    if(!$o)
                        unset($frm_vars['datepicker_loaded'][$fid]);
                    unset($fid);
                    unset($o);
                }
            }
            
            if(!empty($frm_vars['datepicker_loaded'])){
                $scripts[] = 'jquery-ui-datepicker';
                $styles[] = 'jquery-theme';
            }
        }
        
        if(isset($frm_vars['chosen_loaded']) and $frm_vars['chosen_loaded'])
            $scripts[] = 'jquery-chosen';

        if(isset($frm_vars['star_loaded']) and !empty($frm_vars['star_loaded'])){ 
            $scripts[] = 'jquery-frm-rating';
            wp_enqueue_style( 'dashicons' );
            
            global $frm_settings;
            if((!isset($frm_vars['css_loaded']) || !$frm_vars['css_loaded']) && $frm_settings->load_style != 'none'){
                $styles[] = 'formidable';
                $frm_vars['css_loaded'] = true;
            }
        }
        
        $frm_input_masks = apply_filters('frm_input_masks', $frm_input_masks, $frm_vars['forms_loaded']);
        foreach((array)$frm_input_masks as $fid => $o){
            if(!$o)
                unset($frm_input_masks[$fid]);
            unset($fid);
            unset($o);
        }
        
        if(!empty($frm_input_masks))
            $scripts[] = 'jquery-maskedinput';
        
        if(!empty($scripts))
            FrmAppHelper::load_scripts($scripts);
        
        if(!empty($styles))
            FrmAppHelper::load_styles($styles);
        
        unset($scripts);
    }
    
    public static function footer_js(){
        global $frm_vars, $frm_input_masks;
        
        if(empty($frm_vars['forms_loaded']))
            return;
            
        $form_ids = '';
        foreach($frm_vars['forms_loaded'] as $form){
            if(!is_object($form))
                continue;
                
            if($form_ids != '')
                $form_ids .= ',';
            $form_ids .= '#form_'. $form->form_key;
        }
        
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/footer_js.php');
    }
    
    public static function data_sort($options, $atts){
        natcasesort($options); //TODO: add sorting options
        return $options;
    }
    
    public static function register_widgets() {
        include_once(FrmAppHelper::plugin_path() .'/pro/classes/widgets/FrmListEntries.php');
        register_widget('FrmListEntries');
    }
    
    /* Back End CRUD */
    public static function show($id = false){
        if(!current_user_can('frm_view_entries'))
            wp_die('You are not allowed to view entries');
            
        global $frm_entry, $frm_field, $frm_entry_meta;
        if(!$id)
            $id = FrmAppHelper::get_param('id');
        if(!$id)
            $id = FrmAppHelper::get_param('item_id');
        
        $entry = $frm_entry->getOne($id, true);
        $data = maybe_unserialize($entry->description);
        if(!is_array($data) or !isset($data['referrer']))
            $data = array('referrer' => $data);
        

        $fields = $frm_field->getAll(array('fi.form_id' => (int)$entry->form_id), 'field_order');
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $show_comments = true;
        
        $user_ID = get_current_user_id();
        if(isset($_POST) and isset($_POST['frm_comment']) and !empty($_POST['frm_comment'])){
            $frm_entry_meta->add_entry_meta($_POST['item_id'], 0, '', array(
                'comment' => $_POST['frm_comment'], 'user_id' => $user_ID
            ));
            //send email notifications
        }
        
        if($show_comments){
            $comments = $frm_entry_meta->getAll("item_id=$id and field_id=0", ' ORDER BY it.created_at ASC', '', true);
            $to_emails = apply_filters('frm_to_email', array(), $entry, $entry->form_id);
        }
            
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/show.php');
    }
    
    public static function list_entries(){
        $params = self::get_params();
        return self::display_list($params);
    }
    
    public static function new_entry(){
        $frm_form = new FrmForm();
        if($form_id = FrmAppHelper::get_param('form')){
            $form = $frm_form->getOne($form_id);
            self::get_new_vars('', $form); 
        }else
             include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/new-selection.php'); 
    }
    
    public static function create(){
        global $frm_entry;
        
        $params = self::get_params();
        if($params['form']){
            $frm_form = new FrmForm();
            $form = $frm_form->getOne($params['form']);
        }
        
        $errors = $frm_entry->validate($_POST);
        
        if( count($errors) > 0 ){
            self::get_new_vars($errors, $form);
        }else{
            if ((isset($_POST['frm_page_order_'. $form->id]) or FrmProFormsHelper::going_to_prev($form->id)) and !FrmProFormsHelper::saving_draft($form->id)){
                self::get_new_vars('', $form);
            }else{
                $_SERVER['REQUEST_URI'] = str_replace('&frm_action=new', '', $_SERVER['REQUEST_URI']);
                
                global $frm_vars;
                if(!isset($frm_vars['created_entries'][$form->id]) or !$frm_vars['created_entries'][$form->id])
                    $frm_vars['created_entries'][$form->id] = array();
                
                if(!isset($frm_vars['created_entries'][$_POST['form_id']]['entry_id']))
                    $record = $frm_vars['created_entries'][$form->id]['entry_id'] = $frm_entry->create( $_POST );
                
                if ($record){
                    if(FrmProFormsHelper::saving_draft($form->id))
                        $message = __('Draft was Successfully Created', 'formidable');
                    else
                        $message = __('Entry was Successfully Created', 'formidable');
                    
                    self::get_edit_vars($record, $errors, $message);
                }else{
                    self::get_new_vars($errors, $form);
                }
            }
        }
    }
    
    public static function edit(){
        $id = FrmAppHelper::get_param('id');
        return self::get_edit_vars($id);
    }
    
    public static function update(){
        global $frm_entry;
        $message = '';
        
        $errors = $frm_entry->validate($_POST);
        $id = FrmAppHelper::get_param('id');
        
        if( empty($errors) ){
            if (isset($_POST['form_id']) and (isset($_POST['frm_page_order_'. $_POST['form_id']]) or FrmProFormsHelper::going_to_prev($_POST['form_id'])) and !FrmProFormsHelper::saving_draft($_POST['form_id'])){
                return self::get_edit_vars($id);
            }else{
                $record = $frm_entry->update( $id, $_POST );
                if(isset($_POST['form_id']) and FrmProFormsHelper::saving_draft($_POST['form_id']))
                    $message = __('Draft was Successfully Updated', 'formidable');
                else
                    $message = __('Entry was Successfully Updated', 'formidable');
                
                $message .= '<br/> <a href="?page=formidable-entries&form='. $_POST['form_id'] .'">&larr; '. __('Back to Entries', 'formidable') .'</a>';
            }
        }
        
        return self::get_edit_vars($id, $errors, $message);
    }
    
    public static function duplicate(){
        global $frm_entry;
        
        $params = self::get_params();
        if($params['form']){
            $frm_form = new FrmForm();
            $form = $frm_form->getOne($params['form']);
            unset($frm_form);
        }
        
        $message = $errors = '';
        $record = $frm_entry->duplicate( $params['id'] );
        if ($record)
            $message = __('Entry was Successfully Duplicated', 'formidable');
        else
            $errors = __('There was a problem duplicating that entry', 'formidable');
        
        if(!empty($errors))
            return self::display_list($params, $errors);
        else
            return self::get_edit_vars($record, '', $message);
    }
    
    public static function destroy(){
        if(!current_user_can('frm_delete_entries')){
            global $frm_settings;
            wp_die($frm_settings->admin_permission);
        }
        
        global $frm_entry;
        $params = self::get_params();
        if($params['form']){
            $frm_form = new FrmForm();
            $form = $frm_form->getOne($params['form']);
        }
            
        if(isset($params['keep_post']) and $params['keep_post']){
            //unlink entry from post
            global $wpdb, $frmdb;
            $wpdb->update( $frmdb->entries, array('post_id' => ''), array('id' => $params['id']) );
        }
        
        $message = '';    
        if ($frm_entry->destroy( $params['id'] ))
            $message = __('Entry was Successfully Destroyed', 'formidable');
        self::display_list($params, $message);
    }
    
    public static function destroy_all(){
        if(!current_user_can('frm_delete_entries')){
            global $frm_settings;
            wp_die($frm_settings->admin_permission);
        }
        
        global $frm_entry, $wpdb;
        $params = self::get_params();
        $message = '';    
        $errors = array();
        if($params['form']){
            $frm_form = new FrmForm();
            $form = $frm_form->getOne($params['form']);
            $entry_ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM {$wpdb->prefix}frm_items WHERE form_id=%d", $form->id));
            
            if ( isset($form->options['create_post']) && $form->options['create_post'] ) {
                // this action takes a while, so only trigger it if there are posts to delete
                foreach($entry_ids as $entry_id){
                    do_action('frm_before_destroy_entry', $entry_id);
                    unset($entry_id);
                }
            }
            
            $wpdb->query($wpdb->prepare("DELETE em.* FROM {$wpdb->prefix}frm_item_metas as em INNER JOIN {$wpdb->prefix}frm_items as e on (em.item_id=e.id) and form_id=%d", $form->id));
            $results = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}frm_items WHERE form_id=%d", $form->id));
            if($results)
                $message = __('Entries were Successfully Destroyed', 'formidable');
        }else{
            $errors = __('No entries were specified', 'formidable');
        }
        self::display_list($params, $message, $errors);
    }
    
    public static function bulk_actions($action='list-form'){
        global $frm_entry, $frm_settings;
        $params = self::get_params();
        $errors = array();
        $bulkaction = '-1';
        
        if($action == 'list-form'){
            if($_REQUEST['bulkaction'] != '-1')
                $bulkaction = $_REQUEST['bulkaction'];
            else if($_POST['bulkaction2'] != '-1')
                $bulkaction = $_REQUEST['bulkaction2'];
        }else{
            $bulkaction = str_replace('bulk_', '', $action);
        }

        $items = FrmAppHelper::get_param('item-action', '');
        if (empty($items)){
            $errors[] = __('No entries were specified', 'formidable');
        }else{
            if(!is_array($items))
                $items = explode(',', $items);
                
            if($bulkaction == 'delete'){
                if(!current_user_can('frm_delete_entries')){
                    $errors[] = $frm_settings->admin_permission;
                }else{
                    if(is_array($items)){
                        foreach($items as $item_id)
                            $frm_entry->destroy($item_id);
                    }
                }
            }else if($bulkaction == 'export'){
                $controller = 'items';
                $ids = $items;
                $ids = implode(',', $ids);
                include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/xml.php');
            }else if($bulkaction == 'csv'){
                if(!current_user_can('frm_view_entries'))
                    wp_die($frm_settings->admin_permission);

                $frm_form = new FrmForm();
                $form_id = $params['form'];
                if($form_id){
                    $form = $frm_form->getOne($form_id);
                }else{
                    $form = $frm_form->getAll("is_template=0 AND (status is NULL OR status = '' OR status = 'published')", ' ORDER BY name', ' LIMIT 1');
                    if($form)
                        $form_id = $form->id;
                    else
                        $errors[] = __('No form was found', 'formidable');
                }
                
                if($form_id and is_array($items)){
                    echo '<script type="text/javascript">window.onload=function(){location.href="'. admin_url( 'admin-ajax.php' ) .'?form='. $form_id .'&action=frm_entries_csv&item_id='. implode(',', $items) .'";}</script>';
                }
            }
        }
        self::display_list($params, '', $errors);
    }
    
    /* Front End CRUD */
    
    //Determine if this is a new entry or if we're editing an old one
    public static function maybe_editing($continue, $form_id, $action = 'new') {
        $form_submitted = FrmAppHelper::get_param('form_id');
        if ( $action == 'new' || $action == 'preview' ) {
            $continue = true;
        } else {
            $continue = ( is_numeric($form_submitted) && (int) $form_id != (int) $form_submitted ) ? true : false;
        }
        
        return $continue;
    }
    
    public static function check_draft_status($values, $id) {
        if ( FrmProEntriesHelper::get_field('is_draft', $id) || $values['is_draft'] ) {
            //remove update hooks if submitting for the first time or is still draft
            remove_action('frm_after_update_entry', 'FrmProEntriesController::update_post', 40, 2);
            remove_action('frm_after_update_entry', 'FrmProNotification::entry_updated', 41, 2);
        }
        
        //if entry was not previously draft or continues to be draft
        if ( !FrmProEntriesHelper::get_field('is_draft', $id) || $values['is_draft'] ) {
            return $values;
        }
        
        //add the create hooks since the entry is switching draft status
        add_action('frm_after_update_entry', 'FrmProEntriesController::add_published_hooks', 2, 2);
        
        //change created timestamp
        $values['created_at'] = $values['updated_at'];
        
        return $values;
    }
    
    public static function remove_draft_hooks($entry_id) {
        if ( !FrmProEntriesHelper::get_field('is_draft', $entry_id) ) {
            return;
        }
        
        //remove hooks if saving as draft
        remove_action('frm_after_create_entry', 'FrmProEntriesController::set_cookie', 20, 2);
        remove_action('frm_after_create_entry', 'FrmProEntriesController::create_post', 40, 2);
        remove_action('frm_after_create_entry', 'FrmProNotification::entry_created', 41, 2);
        remove_action('frm_after_create_entry', 'FrmProNotification::autoresponder', 41, 2);
    }
    
    //add the create hooks since the entry is switching draft status
    public static function add_published_hooks($entry_id, $form_id) {
        do_action('frm_after_create_entry', $entry_id, $form_id);
        do_action('frm_after_create_entry_'. $form_id, $entry_id);
        remove_action('frm_after_create_entry', 'FrmProNotification::entry_created', 41, 2);
        remove_action('frm_after_create_entry', 'FrmProNotification::autoresponder', 41, 2);
        remove_action('frm_after_update_entry', 'FrmProEntriesController::add_published_hooks', 2, 2);
    }

    public static function process_update_entry($params, $errors, $form, $args){
        global $frm_entry, $frm_vars;
        
        if($params['action'] == 'update' && isset($frm_vars['saved_entries']) && in_array((int)$params['id'], (array)$frm_vars['saved_entries']))
            return;
        
        if($params['action'] == 'create' and isset($frm_vars['created_entries'][$form->id]) and isset($frm_vars['created_entries'][$form->id]['entry_id']) and is_numeric($frm_vars['created_entries'][$form->id]['entry_id'])){
            $entry_id = $params['id'] = $frm_vars['created_entries'][$form->id]['entry_id'];
            
            FrmProEntriesController::set_cookie($entry_id, $form->id);
            
            $conf_method = apply_filters('frm_success_filter', 'message', $form, $form->options, $params['action']);
            if ($conf_method != 'redirect')
                return;
            
            $success_args = array('action' => $params['action']);
            
            if(isset($args['ajax']))
                $success_args['ajax'] = $args['ajax'];
            do_action('frm_success_action', $conf_method, $form, $form->options, $params['id'], $success_args);
        }else if ($params['action'] == 'update'){
            if(isset($frm_vars['saved_entries']) && in_array((int)$params['id'], (array)$frm_vars['saved_entries'])){
                if(isset($_POST['item_meta']))
                    unset($_POST['item_meta']);

                add_filter('frm_continue_to_new', '__return_'. ($continue ? 'true' : 'false'), 15);
                return;
            }
            
            //don't update if there are validation errors
            if (!empty($errors))
                return;
            
            //check if user is allowed to update
            if ( !FrmProEntriesHelper::user_can_edit( (int) $params['id'], $form ) ) {
                global $frm_settings;
                wp_die(do_shortcode($frm_settings->login_msg));
            }
            
            //update, but don't check for confirmation if saving draft
            if(FrmProFormsHelper::saving_draft($form->id)){
                $frm_entry->update( $params['id'], $_POST );
                return;
            }
            
            //don't update if going back
            if (isset($_POST['frm_page_order_'. $form->id]) or FrmProFormsHelper::going_to_prev($form->id))
                return;
            
            $frm_entry->update( $params['id'], $_POST );
            
            
            $success_args = array('action' => $params['action']);
            if ( $params['action'] != 'create' && FrmProEntriesHelper::is_new_entry($params['id']) ) {
                $success_args['action'] = 'create';
            }
            
            //check confirmation method 
            $conf_method = apply_filters('frm_success_filter', 'message', $form, $success_args['action']);

            if ($conf_method != 'redirect')
                return;
            
            if(isset($args['ajax']))
                $success_args['ajax'] = $args['ajax'];
                    
            do_action('frm_success_action', $conf_method, $form, $form->options, $params['id'], $success_args);
            
        }else if ($params['action'] == 'destroy'){
            //if the user who created the entry is deleting it
            self::ajax_destroy($form->id, false, false);
        }
    }
        
    public static function edit_update_form($params, $fields, $form, $title, $description){
        global $frmdb, $wpdb, $frm_entry, $frm_entry_meta, $frmpro_settings, $frm_vars;
        
        $message = '';
        $continue = true;
        $user_ID = get_current_user_id();
        
        if ($params['action'] == 'edit'){
            $entry_key = FrmAppHelper::get_param('entry');
            
            $where = $wpdb->prepare("it.form_id=%d", $form->id);            
            
            if($entry_key){
                $where .= $wpdb->prepare(' AND (it.id=%d OR it.item_key=%s)', $entry_key, $entry_key);
                $in_form = $wpdb->get_var("SELECT id FROM $frmdb->entries it WHERE $where");
                if(!$in_form){
                    $entry_key = false;
                    $where = $wpdb->prepare("it.form_id=%d", $form->id);
                }
                unset($in_form);
            }
            
            $entry_key = esc_sql($entry_key);
            $entry = FrmProEntriesHelper::user_can_edit($entry_key, $form);
            unset($entry_key);
            
            if($entry and !is_array($entry))
                $entry = $frm_entry->getAll( $where, '', 1, true);
            
            if ($entry and !empty($entry)){
                $entry = reset($entry);
                $frm_vars['editing_entry'] = $entry->id;
                self::show_responses($entry, $fields, $form, $title, $description);
                $continue = false;
            }
        }else if ($params['action'] == 'update' and ($params['posted_form_id'] == $form->id)){
            $errors = (isset($frm_vars['created_entries'][$form->id])) ? $frm_vars['created_entries'][$form->id]['errors'] : false;

            if (empty($errors)){
                $saving_draft = FrmProFormsHelper::saving_draft($form->id);
                if ( ( !isset($_POST['frm_page_order_'. $form->id]) && !FrmProFormsHelper::going_to_prev($form->id) ) || $saving_draft ) {
                    $success_args = array('action' => $params['action']);
                    if ( FrmProEntriesHelper::is_new_entry($params['id']) ) {
                        $success_args['action'] = 'create';
                    }
                    
                    //check confirmation method 
                    $conf_method = apply_filters('frm_success_filter', 'message', $form, $success_args['action']);
                    
                    if ($conf_method == 'message'){
                        $message = self::confirmation($conf_method, $form, $form->options, $params['id'], $success_args);
                    }else{
                        do_action('frm_success_action', $conf_method, $form, $form->options, $params['id'], $success_args);
                        add_filter('frm_continue_to_new', '__return_false', 15);
                        return;
                    }
                }
            }else{
                $fields = FrmFieldsHelper::get_form_fields($form->id, true);
            }
            
            self::show_responses($params['id'], $fields, $form, $title, $description, $message, $errors);
            $continue = false;
            
        }else if ($params['action'] == 'destroy'){
            //if the user who created the entry is deleting it
            $message = self::ajax_destroy($form->id, false);
        }else if(isset($frm_vars['editing_entry']) && $frm_vars['editing_entry']){
            if(is_numeric($frm_vars['editing_entry'])){
                $entry_id = $frm_vars['editing_entry']; //get entry from shortcode
            }else{
                $entry_ids = $wpdb->get_col("SELECT id FROM $frmdb->entries WHERE user_id='$user_ID' and form_id='$form->id'");
                
                if (isset($entry_ids) and !empty($entry_ids)){
                    $where_options = $frm_vars['editing_entry'];
                    if(!empty($where_options))
                        $where_options .= ' and ';
                    $where_options .= "it.item_id in (".implode(',', $entry_ids).")";
                    
                    $get_meta = $frm_entry_meta->getAll($where_options, ' ORDER BY it.created_at DESC', ' LIMIT 1');
                    $entry_id = ($get_meta) ? $get_meta->item_id : false;
                }
            }

            if(isset($entry_id) and $entry_id){
                if($form->editable and ((isset($form->options['open_editable']) and $form->options['open_editable']) or !isset($form->options['open_editable'])) and isset($form->options['open_editable_role']) and FrmAppHelper::user_has_permission($form->options['open_editable_role']))
                    $meta = true;
                else
                    $meta = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}frm_items WHERE user_id=%d AND id=%d AND form_id=%d", $user_ID, $entry_id, $form->id ));

                if($meta){
                    $frm_vars['editing_entry'] = $entry_id;
                    self::show_responses($entry_id, $fields, $form, $title, $description);
                    $continue = false;
                }
            }
        }else{
            //check to see if use is allowed to create another entry
            $can_submit = true;
            if (isset($form->options['single_entry']) and $form->options['single_entry']){
                if ($form->options['single_entry_type'] == 'cookie' and isset($_COOKIE['frm_form'. $form->id . '_' . COOKIEHASH])){
                    $can_submit = false;
                }else if ($form->options['single_entry_type'] == 'ip'){
                    $prev_entry = $frm_entry->getAll(array('it.form_id' => $form->id, 'it.ip' => $_SERVER['REMOTE_ADDR']), '', 1);
                    if ($prev_entry)
                        $can_submit = false;
                }else if ($form->options['single_entry_type'] == 'user' and !$form->editable and $user_ID){
                    $meta = $frmdb->get_var($frmdb->entries, array('user_id' => $user_ID, 'form_id' => $form->id ));
                    if ($meta)
                        $can_submit = false;
                }else if (isset($form->options['save_draft']) and $form->options['save_draft'] == 1 and $user_ID){
                    $where = $wpdb->prepare('user_id=%d AND form_id=%d', $user_ID, $form->id);
                    if ( $form->options['single_entry_type'] != 'user' ) {
                        $where .= $wpdb->prepare(' AND is_draft=%d', 1);
                    }
                    $meta = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}frm_items WHERE $where");
                    if ($meta)
                        $can_submit = false;
                }
                
                if (!$can_submit){
                    echo $frmpro_settings->already_submitted;//TODO: DO SOMETHING IF USER CANNOT RESUBMIT FORM
                    $continue = false;
                }
            }
        }

        add_filter('frm_continue_to_new', '__return_'. ($continue ? 'true' : 'false'), 15);
    }

    public static function show_responses($id, $fields, $form, $title=false, $description=false, $message='', $errors=array()){
        global $frm_field, $frm_entry, $frm_entry_meta;
        global $frmpro_settings, $frm_vars;

        if(is_object($id)){
            $item = $id;
            $id = $item->id;
        }else{
            $item = $frm_entry->getOne($id, true);
        }

        $frm_vars['editing_entry'] = $item->id;
        $values = FrmAppHelper::setup_edit_vars($item, 'entries', $fields);
        
        if($values['custom_style']) $frm_vars['load_css'] = true;
        $show_form = true;
        $edit_create = $item->is_draft ? (isset($values['submit_value']) ? $values['submit_value'] : $frmpro_settings->submit_value) : (isset($values['edit_value']) ? $values['edit_value'] : $frmpro_settings->update_value);
        $submit = (isset($frm_vars['next_page'][$form->id])) ? $frm_vars['next_page'][$form->id] : $edit_create;
        unset($edit_create);
        
        if(is_object($submit))
            $submit = $submit->name;
        
        if(!isset($frm_vars['prev_page'][$form->id]) and isset($_POST['item_meta']) and empty($errors) and $form->id == FrmAppHelper::get_param('form_id')){
            $show_form = (isset($form->options['show_form'])) ? $form->options['show_form'] : true;
            if(FrmProFormsHelper::saving_draft($form->id) or FrmProFormsHelper::going_to_prev($form->id)){
                $show_form = true;
            }else{
                $success_args = array('action' => 'update');
                if ( FrmProEntriesHelper::is_new_entry($id) ) {
                    $success_args['action'] = 'create';
                }
                
                $conf_method = apply_filters('frm_success_filter', 'message', $form, $success_args['action']);
                
                if ( $conf_method != 'message' ) {
                    do_action('frm_success_action', $conf_method, $form, $form->options, $id, $success_args);
                }
            }
        }else if(isset($frm_vars['prev_page'][$form->id]) or !empty($errors)){
            $jump_to_form = true;
        }

        $user_ID = get_current_user_id();
        
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/edit-front.php');
        add_filter('frm_continue_to_new', 'FrmProEntriesController::maybe_editing', 10, 3);
    }
    
    public static function ajax_submit_button($form, $action='create'){
        global $frm_vars;
        
        if(isset($frm_vars['novalidate']) and $frm_vars['novalidate'])
            echo ' formnovalidate="formnovalidate"';
    }
    
    public static function get_confirmation_method($method, $form, $action = 'create') {
        $opt = ( $action == 'update' ) ? 'edit_action' : 'success_action';
        $method = ( isset($form->options[$opt]) && !empty($form->options[$opt]) ) ? $form->options[$opt] : $method;
        if ( $method != 'message' && FrmProFormsHelper::saving_draft($form->id) ) {
            $method = 'message';
        }
        return $method;
    }
    
    public static function confirmation($method, $form, $form_options, $entry_id, $args=array()){
        $opt = ( !isset($args['action']) || $args['action'] == 'create' ) ? 'success' : 'edit';
        if ( $method == 'page' && is_numeric($form_options[$opt .'_page_id']) ) {
            global $post;
            if ( !$post || $form_options[$opt .'_page_id'] != $post->ID ) {
                $page = get_post($form_options[$opt .'_page_id']);
                $old_post = $post;
                $post = $page;
                $content = apply_filters('frm_content', $page->post_content, $form, $entry_id);
                echo apply_filters('the_content', $content);
                $post = $old_post;
            }
        }else if($method == 'redirect'){
            global $frm_vars;
            
            add_filter('frm_use_wpautop', '__return_false');
            $success_url = apply_filters('frm_content', trim($form_options[$opt .'_url']), $form, $entry_id);
            $success_msg = isset($form_options[$opt .'_msg']) ? $form_options[$opt .'_msg'] : __('Please wait while you are redirected.', 'formidable');
            
            $redirect_msg = '<div class="with_frm_style"><div class="frm-redirect-msg frm_message">'. $success_msg .'<br/>'.
                sprintf(__('%1$sClick here%2$s if you are not automatically redirected.', 'formidable'), '<a href="'. esc_url($success_url) .'">', '</a>') .
                '</div></div>';
               
            $redirect_msg = apply_filters('frm_redirect_msg', $redirect_msg, array(
                'entry_id' => $entry_id, 'form_id' => $form->id, 'form' => $form
            ));
            
            $args['id'] = $entry_id;
            //delete the entry on frm_redirect_url hook
            $success_url = apply_filters('frm_redirect_url', $success_url, $form, $args);
            
            if((!defined('DOING_AJAX') or (isset($frm_vars['preview']) and $frm_vars['preview'])) and !headers_sent()){
                wp_redirect( $success_url );
                die();
            }else if(isset($args['ajax']) and $args['ajax'] and defined('DOING_AJAX') and (!isset($frm_vars['preview']) or !$frm_vars['preview'])){
                echo json_encode(array('redirect' => $success_url));
                die();
            }
            
            add_filter('frm_use_wpautop', '__return_true');
            
            $response = $redirect_msg;

            $response .= "<script type='text/javascript'>jQuery(document).ready(function(){ setTimeout(window.location='". $success_url ."', 8000); });</script>";
            
            if(headers_sent()){
                echo $response;
            }else{
                wp_redirect( $success_url );
                die();
            }
        } else {
            global $frmpro_settings, $frm_settings;
            $msg = ( $opt == 'edit' ) ? $frmpro_settings->edit_msg : $frm_settings->success_msg;
            $message = isset($form->options[$opt .'_msg']) ? $form->options[$opt .'_msg'] : $msg;
            if ( FrmProFormsHelper::saving_draft($form->id) ) {
                $message = isset($form->options['draft_msg']) ? $form->options['draft_msg'] : $frmpro_settings->draft_msg;
            }
            
            $message = apply_filters('frm_content', $message, $form);
            $message = '<div class="frm_message" id="message">'. wpautop(do_shortcode($message)) .'</div>';
            return $message;
        }
    }
    
    public static function delete_entry($post_id){
        global $frmdb;
        $entry = $frmdb->get_one_record($frmdb->entries, array('post_id' => $post_id), 'id');
        if($entry){
            global $frm_entry;
            $frm_entry->destroy($entry->id);
        }
    }
    
    public static function trashed_post($post_id){
        global $frmpro_display;
        $form_id = get_post_meta($post_id, 'frm_form_id', true);
        $display = $frmpro_display->get_auto_custom_display(array('form_id' => $form_id));
        if($display)
            update_post_meta($post_id, 'frm_display_id', $display->ID);
        else
            delete_post_meta($post_id, 'frm_display_id');
    }
    
    public static function create_entry_from_post_box($post_type, $post=false){
        if(!$post or !isset($post->ID) or $post_type == 'attachment' or $post_type == 'link')
            return;
            
        global $frmdb, $wpdb, $frm_vars;
        
        //don't show the meta box if there is already an entry for this post
        $post_entry = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}frm_items WHERE post_id=". $post->ID);
        if($post_entry) 
            return;
        
        //don't show meta box if no forms are set up to create this post type
        $forms = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}frm_forms where options LIKE '%s:9:\"post_type\";s:". strlen($post_type) .":\"". $post_type ."\";%' AND options LIKE '%s:11:\"create_post\";s:1:\"1\";%'");
        if(!$forms)
            return;
            
        $frm_vars['post_forms'] = $forms;
        
        if(current_user_can('frm_create_entries'))
            add_meta_box('frm_create_entry', __('Create Entry in Form', 'formidable'), 'FrmProEntriesController::render_meta_box_content', null, 'side');
    }
    
    public static function render_meta_box_content($post){
        global $frm_vars;
        $count = isset($frm_vars['post_forms']) ? count($frm_vars['post_forms']) : 0;
        $i = 1;
        
        echo '<p>';
        foreach((array)$frm_vars['post_forms'] as $form){
            if($i != 1)
                echo ' | ';
            
            $i++;
            echo '<a href="javascript:frm_create_post_entry('. $form->id .','. $post->ID .')">'. FrmAppHelper::truncate($form->name, 15) .'</a>';
            unset($form);
        }
        unset($i);
        echo '</p>';
        
        
        echo "<script type='text/javascript'>function frm_create_post_entry(id,post_id){
jQuery('#frm_create_entry p').replaceWith('<img src=\"". FrmAppHelper::plugin_url() ."/images/wpspin_light.gif\" alt=\"". __('Loading&hellip;') ."\" />');
jQuery.ajax({type:'POST',url:'". admin_url('admin-ajax.php') ."',data:'action=frm_create_post_entry&id='+id+'&post_id='+post_id,
success:function(msg){jQuery('#frm_create_entry').fadeOut('slow');}
});
};</script>";
    }
    
    public static function create_post_entry($id=false, $post_id=false){
        if(!$id)
            $id = $_POST['id'];
            
        if(!$post_id)
            $post_id = $_POST['post_id'];
        
        if(!is_numeric($id) or !is_numeric($post_id))
            return;
        
        $post = get_post($post_id);
        
        global $wpdb;
        $values = array(
            'description' => __('Copied from Post', 'formidable'),
            'form_id' => $id,
            'created_at' => $post->post_date_gmt,
            'name' => $post->post_title,
            'item_key' => FrmAppHelper::get_unique_key($post->post_name, $wpdb->prefix .'frm_items', 'item_key'),
            'user_id' => $post->post_author,
            'post_id' => $post->ID
        );
        
        $results = $wpdb->insert( $wpdb->prefix .'frm_items', $values );
        unset($values);
        
        if(!$results)
            die();
        
        $entry_id = $wpdb->insert_id;
        $frm_field = new FrmField();
        $user_id_field = $frm_field->getAll(array('fi.type' => 'user_id', 'fi.form_id' => $id), '', 1);
        unset($frm_field);
        
        if($user_id_field){
            $new_values = array(
                'meta_value' => $post->post_author,
                'item_id' => $entry_id,
                'field_id' => $user_id_field->id,
                'created_at' => current_time('mysql', 1)
            );
                
            $wpdb->insert( $wpdb->prefix .'frm_item_metas', $new_values );
        }
            
        global $frmpro_display;
        $display = $frmpro_display->get_auto_custom_display(array('form_id' => $id, 'entry_id' => $entry_id));
        if($display)
            update_post_meta($post->ID, 'frm_display_id', $display->ID);
        
        die();
    }

    /* Export to CSV */    
    public static function csv($form_id=false, $search = '', $fid = ''){
        if(!$form_id){
            $form_id = FrmAppHelper::get_param('form');
            $search = FrmAppHelper::get_param(isset($_REQUEST['s']) ? 's' : 'search');
            $fid = FrmAppHelper::get_param('fid');
        }
        
        if(!current_user_can('frm_view_entries')){
            global $frm_settings;
            wp_die($frm_settings->admin_permission);
        }

        if( !ini_get('safe_mode') ){
            set_time_limit(0); //Remove time limit to execute this function
            ini_set('memory_limit', '256M');
        }

        global $current_user, $frm_field, $frm_entry, $frm_entry_meta, $wpdb, $frmdb, $frmpro_settings;
        
        $frm_form = new FrmForm();
        $form = $frm_form->getOne($form_id);
        unset($frm_form);
        
        $form_name = sanitize_title_with_dashes($form->name);
        $form_cols = $frm_field->getAll("fi.type not in ('divider', 'captcha', 'break', 'html') and fi.form_id=". $form->id, 'field_order ASC');
        $item_id = FrmAppHelper::get_param('item_id', false);
        $where_clause = $wpdb->prepare("form_id=%d", $form_id);
        
        if($item_id){
            $where_clause .= " and id in (";
            $item_ids = explode(',', $item_id);
            $where_clause .= implode(',', array_filter( $item_ids, 'is_numeric' ));
            unset($item_ids);
            
            $where_clause .= ")";
        }else if(!empty($search)){
            $where_clause = self::get_search_str($where_clause, $search, $form_id, $fid);
        }
          
        $where_clause = apply_filters('frm_csv_where', $where_clause, compact('form_id'));

        $entry_ids = $wpdb->get_col("SELECT id FROM {$wpdb->prefix}frm_items it WHERE $where_clause");
        unset($where_clause);
        
        $comment_count = $wpdb->get_var("SELECT COUNT(*) FROM $frmdb->entry_metas WHERE item_id in (". implode($entry_ids, ',') .") and field_id=0 GROUP BY item_id ORDER BY count(*) DESC LIMIT 1");

        $filename = apply_filters('frm_csv_filename', date("ymdHis",time()) . '_' . $form_name . '_formidable_entries.csv', $form);
        $wp_date_format = apply_filters('frm_csv_date_format', 'Y-m-d H:i:s');
        $charset = get_option('blog_charset');
        
        $to_encoding = isset($_POST['csv_format']) ? $_POST['csv_format'] : $frmpro_settings->csv_format;
        $line_break = apply_filters('frm_csv_line_break', 'return');
        $sep = apply_filters('frm_csv_sep', ', ');
        
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/csv.php');
        die();
    }

    /* Display in Back End */
    
    public static function manage_columns($columns){
        global $frm_vars;
        $form_id = FrmProAppHelper::get_current_form_id();
        
        $columns['cb'] = '<input type="checkbox" />';
        $columns[$form_id .'_id'] = 'ID';
        $columns[$form_id .'_item_key'] = __('Entry Key', 'formidable');
        
        $frm_field = new FrmField();
        $form_cols = $frm_field->getAll("fi.type not in ('divider', 'captcha', 'break', 'html') and fi.form_id=". (int)$form_id, 'field_order ASC');
        
        foreach($form_cols as $form_col){
            if(isset($form_col->field_options['separate_value']) and $form_col->field_options['separate_value'])
                $columns[$form_id .'_frmsep_'. $form_col->field_key] = FrmAppHelper::truncate($form_col->name, 35);
            $columns[$form_id .'_'. $form_col->field_key] = FrmAppHelper::truncate($form_col->name, 35);
        }

        $columns[$form_id .'_post_id'] = __('Post', 'formidable');
        $columns[$form_id .'_created_at'] = __('Entry creation date', 'formidable');
        $columns[$form_id .'_updated_at'] = __('Entry update date', 'formidable');
        $columns[$form_id .'_ip'] = 'IP';
        $columns[$form_id .'_is_draft'] = __('Draft', 'formidable');
        
        //TODO: allow custom order of columns
        
        $frm_vars['cols'] = $columns;
        
        if (isset($_GET['page']) and $_GET['page'] == 'formidable-entries' and (!isset($_GET['frm_action']) or $_GET['frm_action'] == 'list' or $_GET['frm_action'] == 'destroy'))
            add_screen_option( 'per_page', array('label' => __('Entries', 'formidable'), 'default' => 20, 'option' => 'formidable_page_formidable_entries_per_page') );
        
        return $columns;
    }
    
    public static function check_hidden_cols($check, $object_id, $meta_key, $meta_value, $prev_value){
        global $frm_settings;
        if($meta_key != 'manage'.  sanitize_title($frm_settings->menu) .'_page_formidable-entriescolumnshidden' or $meta_value == $prev_value)
            return $check;
        
        if ( empty($prev_value) )
    		$prev_value = get_metadata('user', $object_id, $meta_key, true);
    		
        global $frm_vars;
        $frm_vars['prev_hidden_cols'] = (isset($frm_vars['prev_hidden_cols']) and $frm_vars['prev_hidden_cols']) ? false : $prev_value; //add a check so we don't create a loop

        return $check;
    }
    
    //add hidden columns back from other forms
    public static function update_hidden_cols($meta_id, $object_id, $meta_key, $meta_value ){
        global $frm_settings;

        if($meta_key != 'manage'.  sanitize_title($frm_settings->menu) .'_page_formidable-entriescolumnshidden')
            return;
            
        global $frm_vars;
        if(!isset($frm_vars['prev_hidden_cols']) or !$frm_vars['prev_hidden_cols'])
            return; //don't continue if there's no previous value
        
        foreach($meta_value as $mk => $mv){
            //remove blank values
            if(empty($mv))
                unset($meta_value[$mk]);
        }
        
        $cur_form_prefix = reset($meta_value);
        $cur_form_prefix = explode('_', $cur_form_prefix);
        $cur_form_prefix = $cur_form_prefix[0];
        $save = false;

        foreach((array)$frm_vars['prev_hidden_cols'] as $prev_hidden){
            if(empty($prev_hidden) or in_array($prev_hidden, $meta_value)) //don't add blank cols or process included cols
                continue;
            
            $form_prefix = explode('_', $prev_hidden);
            $form_prefix = $form_prefix[0];
            if($form_prefix == $cur_form_prefix) //don't add back columns that are meant to be hidden
                continue;
            
            $meta_value[] = $prev_hidden;
            $save = true;
            unset($form_prefix);
        }
        
        if($save){
            $user = wp_get_current_user();
            update_user_option($user->ID, 'manage'.  sanitize_title($frm_settings->menu) .'_page_formidable-entriescolumnshidden', $meta_value, true);
        }
    }
    
    public static function save_per_page($save, $option, $value){
        if($option == 'formidable_page_formidable_entries_per_page')
            $save = (int)$value;
        return $save;
    }
    
    public static function sortable_columns(){
        $form_id = FrmProAppHelper::get_current_form_id();
        
        $frm_field = new FrmField();
        $fields = $frm_field->getAll( array('fi.form_id' => $form_id) );
        unset($frm_field);
		
        $columns = array(
            $form_id .'_id'         => 'id',
            $form_id .'_created_at' => 'created_at',
            $form_id .'_updated_at' => 'updated_at',
            $form_id .'_ip'         => 'ip',
            $form_id .'_item_key'   => 'item_key',
            $form_id .'_is_draft'   => 'is_draft'
        );
		
        foreach ( $fields as $field ) {
		    if ( $field->type != 'checkbox' && (!isset($field->field_options['post_field']) || $field->field_options['post_field'] == '')) { // Can't sort on checkboxes because they are stored serialized, or post fields
			    $columns[ $form_id .'_'. $field->field_key ] = 'meta_'. $field->id;
		    }
        }
		
        return $columns;
    }
    
    public static function hidden_columns($result){
        global $frm_vars;

        $form_id = FrmProAppHelper::get_current_form_id();
        
        $return = false;
        foreach((array)$result as $r){
            if(!empty($r)){
                $form_prefix = explode('_', $r);
                $form_prefix = $form_prefix[0];
                
                if((int)$form_prefix == (int)$form_id){
                    $return = true;
                    break;
                }
                
                unset($form_prefix);
            }
        }
        
        if($return)
            return $result;
 
        $i = isset($frm_vars['cols']) ? count($frm_vars['cols']) : 0;
        $max_columns = 8;
        if($i <= $max_columns)
            return $result;
        
        global $frm_vars;
        if(isset($frm_vars['current_form']) and $frm_vars['current_form'])
            $frm_vars['current_form']->options = maybe_unserialize($frm_vars['current_form']->options);
        
        if(isset($frm_vars['current_form']) and $frm_vars['current_form'] and isset($frm_vars['current_form']->options['hidden_cols']) and !empty($frm_vars['current_form']->options['hidden_cols'])){
            $result = $frm_vars['current_form']->options['hidden_cols'];
        }else{
            $cols = $frm_vars['cols'];
            $cols = array_reverse($cols, true);

            $result[] = $form_id .'_id';
            $i--;
                
            $result[] = $form_id .'_item_key';
            $i--;
                
            foreach($cols as $col_key => $col){
                if($i > $max_columns)
                    $result[] = $col_key; //remove some columns by default
                $i--;
                unset($col_key);
                unset($col);
            }
        }
        
        return $result;
    }
    
    public static function display_list($params=array(), $message='', $errors=array()){
        global $wpdb, $frmdb, $frm_entry, $frm_entry_meta, $frm_field, $frm_vars;
        
        if(empty($params))
            $params = self::get_params();
   
        $errors = array();
        
        $frm_form = new FrmForm();
        $form_select = $frm_form->getAll("is_template=0 AND (status is NULL OR status = '' OR status = 'published')", ' ORDER BY name');

        if($params['form'])
            $form = $frm_form->getOne($params['form']);
        else
            $form = (isset($form_select[0])) ? $form_select[0] : 0;
        
        if($form){
            $params['form'] = $form->id;
            $frm_vars['current_form'] = $form;
	        $where_clause = " it.form_id=$form->id";
        }else{
            $where_clause = '';
		}
        
        require(FrmAppHelper::plugin_path() .'/pro/classes/helpers/FrmProListHelper.php');

        $wp_list_table = new FrmProListHelper(array('singular' => 'entry', 'plural' => 'entries', 'table_name' => $frmdb->entries, 'page_name' => 'entries', 'params' => $params));

        $pagenum = $wp_list_table->get_pagenum();

        $wp_list_table->prepare_items();

        $total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );
        if ( $pagenum > $total_pages && $total_pages > 0 ) {
            $url = add_query_arg( 'paged', $total_pages );
            if ( headers_sent() ) {
                echo FrmAppHelper::js_redirect($url);
            } else {
                wp_redirect($url);
            }
            die();
        }
        
        if ( empty($message) && isset($_GET['import-message']) ) {
            $message = __('Your import is complete', 'formidable');
        }

        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/list.php');
    }
    
    public static function get_search_str($where_clause='', $search_str, $form_id=false, $fid=false) {
        global $frm_entry_meta, $wpdb;
        
        $where_item = '';
        $join = ' (';
        if ( !is_array($search_str) ) {
            $search_str = explode(" ", $search_str);
        }
        
        foreach ( $search_str as $search_param ) {
            $unescaped_search_param = $search_param;
            $search_param = esc_sql( like_escape( $search_param ) );
			
            if ( !is_numeric($fid) ) {
                $where_item .= (empty($where_item)) ? ' (' : ' OR';
                    
                if ( in_array($fid, array('created_at', 'user_id', 'updated_at', 'id')) ) {
                    if ( $fid == 'user_id' && !is_numeric($search_param) ) {
                        $search_param = FrmProAppHelper::get_user_id_param($unescaped_search_param);
                    }
                    
                    $where_item .= $wpdb->prepare(" it.{$fid} like %s", '%'. $search_param .'%');
                } else {
                    $where_item .= $wpdb->prepare(' it.name like %s OR it.item_key like %s OR it.description like %s OR it.created_at like %s', '%'. $search_param .'%', '%'. $search_param .'%', '%'. $search_param .'%', '%'. $search_param .'%');
                }
            }
                
            if ( empty($fid) || is_numeric($fid) ) {
                $where_entries = $wpdb->prepare('(meta_value LIKE %s', '%'. $search_param .'%');
                if ( $data_fields = FrmProFormsHelper::has_field('data', $form_id, false) ) {
                    $df_form_ids = array();

                    //search the joined entry too
                    foreach ( (array) $data_fields as $df ) {
                        //don't check if a different field is selected
                        if ( is_numeric($fid) && (int) $fid != $df->id ) {
                            continue;
                        }
                        
                        $df->field_options = maybe_unserialize($df->field_options);
                        if ( isset($df->field_options['form_select']) && is_numeric($df->field_options['form_select']) ) {
                            $df_form_ids[] = $df->field_options['form_select'];
                        }

                        unset($df);
                    }
                    unset($data_fields);
                    
                    if ( !empty($df_form_ids) ) {
                        $data_form_ids = $wpdb->get_col("SELECT form_id FROM {$wpdb->prefix}frm_fields WHERE id in (". implode(',', $df_form_ids).")");
                    }
                    unset($df_form_ids);
                        
                    if ( isset($data_form_ids) && $data_form_ids ) {
                        $data_entry_ids = $frm_entry_meta->getEntryIds("fi.form_id in (". implode(',', $data_form_ids).") and meta_value LIKE '%". $search_param ."%'");
                        if ( !empty($data_entry_ids) ) {
                            $where_entries .= " OR meta_value in (".implode(',', $data_entry_ids).")";
                        }
                    }
                    unset($data_form_ids);
                }
                
                $where_entries .= ")";
                
                if ( is_numeric($fid) ) {
                    $where_entries .= $wpdb->prepare(' AND field_id=%d', $fid);
                }
                
                if ( is_admin() && isset($_GET) && isset($_GET['page']) && $_GET['page'] == 'formidable-entries' ) {
                    $include_drafts = true;
                } else {
                    $include_drafts = false;
                }
                
                $meta_ids = $frm_entry_meta->getEntryIds($where_entries, '', '', true, $include_drafts);
                
                if ( !empty($where_clause) ) {
                    $where_clause .= " AND" . $join;
                    if ( !empty($join) ){
                        $join = '';
                    }
                }
                
                if ( !empty($meta_ids) ) {
                    $where_clause .= " it.id in (".implode(',', $meta_ids).")";
                } else {
                    $where_clause .= " it.id=0";
                }
            }
        }
        
        if ( !empty($where_item) ) {
            $where_item .= ')';
            if ( !empty($where_clause) ) {
                $where_clause .= empty($fid) ? ' OR' : ' AND';
            }
            $where_clause .= $where_item;
        }
        
        if ( empty($join) ) {
            $where_clause .= ')';
        }
        
        return $where_clause;
    }

    public static function get_new_vars($errors = array(), $form = false, $message = ''){
        global $frm_field, $frm_entry, $frm_settings, $frm_vars;
        $description = true;
        $title = false;
        $form = apply_filters('frm_pre_display_form', $form);
        $fields = FrmFieldsHelper::get_form_fields($form->id, !empty($errors));
        $values = $fields ? FrmEntriesHelper::setup_new_vars($fields, $form) : array();
        $submit = (isset($frm_vars['next_page'][$form->id])) ? $frm_vars['next_page'][$form->id] : (isset($values['submit_value']) ? $values['submit_value'] : $frm_settings->submit_value); 
        if(is_object($submit))
            $submit = $submit->name; 
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/new.php');
    }

    private static function get_edit_vars($id, $errors = '', $message= ''){
        if(!current_user_can('frm_edit_entries'))
            return self::show($id);

        global $frm_entry, $frm_field, $frmpro_settings, $frm_vars;
        $description = true;
        $title = false;
        $record = $frm_entry->getOne( $id, true );
        $frm_vars['editing_entry'] = $id;
        
        $frm_form = new FrmForm();
        $form = $frm_form->getOne($record->form_id);
        $form = apply_filters('frm_pre_display_form', $form);
        
        $fields = FrmFieldsHelper::get_form_fields($form->id, !empty($errors));
        $values = FrmAppHelper::setup_edit_vars($record, 'entries', $fields);
        $edit_create = ($record->is_draft) ? (isset($values['submit_value']) ? $values['submit_value'] : $frmpro_settings->submit_value) : (isset($values['edit_value']) ? $values['edit_value'] : $frmpro_settings->update_value);
        $submit = (isset($frm_vars['next_page'][$form->id])) ? $frm_vars['next_page'][$form->id] : $edit_create;
        unset($edit_create);
        
        if(is_object($submit))
            $submit = $submit->name;
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/edit.php');
    }
    
    public static function get_params($form=null){
        if(!$form){
            $frm_form = new FrmForm();
            $form = $frm_form->getAll("is_template=0 AND (status is NULL OR status = '' OR status = 'published')", ' ORDER BY name', ' LIMIT 1');
        }
        
        $values = array();
        foreach (array('id' => '', 'form_name' => '', 'paged' => 1, 'form' => (($form) ? $form->id : 0), 'field_id' => '', 'search' => '', 'sort' => '', 'sdir' => '', 'fid' => '', 'keep_post' => '') as $var => $default)
            $values[$var] = FrmAppHelper::get_param($var, $default);

        return $values;
    }
    
    public static function &filter_shortcode_value($value, $tag, $atts, $field){            
        if(isset($atts['striphtml']) and $atts['striphtml'])
            $value = wp_kses($value, array());
        
        if ( !isset($atts['keepjs']) || !$atts['keepjs'] ) {
            if ( is_array($value) ) {
                foreach ( $value as $k => $v ) {
                    $value[$k] = wp_kses_post($v);
                    unset($k);
                    unset($v);
                }
            } else {
                $value = wp_kses_post($value);
            }   
        }
        
        if(isset($atts['show']) and $atts['show'] == 'value')
            return $value;
               
        $value = self::filter_display_value($value, $field, $atts);
        return $value;
    }
    
    public static function &filter_display_value($value, $field, $atts=array()){
        $value = FrmEntriesController::filter_display_value($value, $field, $atts);
        return $value;
    }

    public static function route(){
        $action = FrmAppHelper::get_param('frm_action');
        
        if($action == 'show')
            return self::show();
        else if($action == 'new')
            return self::new_entry();
        else if($action == 'create')
            return self::create();
        else if($action == 'edit')
            return self::edit();
        else if($action == 'update')
            return self::update();
        else if($action == 'duplicate')
            return self::duplicate();
        else if($action == 'destroy')
            return self::destroy();
        else if($action == 'destroy_all')
            return self::destroy_all();
        else if($action == 'list-form')
            return self::bulk_actions($action);
        else{
            $action = FrmAppHelper::get_param('action');
            if($action == -1)
                $action = FrmAppHelper::get_param('action2');
            
            if(strpos($action, 'bulk_') === 0){
                if(isset($_GET) and isset($_GET['action']))
                    $_SERVER['REQUEST_URI'] = str_replace('&action='.$_GET['action'], '', $_SERVER['REQUEST_URI']);
                if(isset($_GET) and isset($_GET['action2']))
                    $_SERVER['REQUEST_URI'] = str_replace('&action='.$_GET['action2'], '', $_SERVER['REQUEST_URI']);
                    
                return self::bulk_actions($action);
            }else{
                return self::display_list();
            }
        }
    }
    
    public static function get_form_results($atts){
        extract( shortcode_atts( array(
            'id' => false, 'cols' => 99, 'style' => true,
            'fields' => false, 'clickable' => false, 'user_id' => false,
            'google' => false, 'pagesize' => 20, 'sort' => true,
            'edit_link' => false, 'delete_link' => false, 'page_id' => false,
            'no_entries' => __('No Entries Found', 'formidable'),
            'confirm' =>  __('Are you sure you want to delete that entry?', 'formidable'),
			'drafts' => '0',
        ), $atts) );
        if (!$id) return;
        
        global $frm_field, $frm_entry, $frm_entry_meta, $frmpro_settings;
        $frm_form = new FrmForm();
        $form = $frm_form->getOne($id);
        if (!$form) return;
        $where = "fi.type not in ('divider', 'captcha', 'break', 'html') and fi.form_id=". (int)$form->id;
        if($fields){
            $fields = explode(',', $fields);
            $f_list = array();
            foreach($fields as $k => $f){
                $f = trim($f);
                $fields[$k] = $f;
                $f_list[] = esc_sql(like_escape($f)); 
                unset($k);
                unset($f);
            }
            if(count($fields) == 1 and in_array('id', $fields))
                $where .= ''; //don't search fields if only field id
            else
                $where .= " and (fi.id in ('". implode("','", $f_list)  ."') or fi.field_key in ('". implode("','", $f_list) ."'))";
            
        }
        $fields = (array)$fields;
        
        $form_cols = $frm_field->getAll($where, 'field_order ASC', $cols);
        unset($where);
		
		//If delete_link is set and frm_action is set to destroy, check if entry should be deleted when page is loaded
		if ( $delete_link && isset($_GET['frm_action']) && $_GET['frm_action'] == 'destroy' ) {
			$delete_message = FrmProEntriesController::ajax_destroy(false, false, false);
		}	
        
		//Set up WHERE for getting entries. Get entries for the specified form and only get drafts if user includes drafts=1
		global $wpdb;
		$where = $wpdb->prepare('it.form_id=%d AND it.is_draft=%d', $form->id, $drafts);

        if($user_id)
            $where .= ' AND user_id='. (int)FrmProAppHelper::get_user_id_param($user_id);
            
        $s = FrmAppHelper::get_param('frm_search', false);
        if ($s){
            $new_ids = FrmProEntriesHelper::get_search_ids($s, $form->id);
            $where .= ' AND it.id in ('. implode(',', $new_ids) .')';
        }
        
        if(isset($new_ids) and empty($new_ids))
            $entries = false;
        else
            $entries = $frm_entry->getAll($where, '', '', true, false);
      
        if ( $edit_link ) {
            $anchor = '';
            if ( !$page_id ) {
                global $post;
                $page_id = $post->ID;
                $anchor = '#form_'. $form->form_key;
            }
            if ( $edit_link === '1' ) {
                $edit_link = __('Edit', 'formidable');
			}        
            $permalink = get_permalink($page_id);
        }

		//If delete_link is set, set the delete link text
		if ( $delete_link === '1' ) {
			$delete_link = __('Delete', 'formidable');
		}
        
        if($style){
            global $frm_vars;
            $frm_vars['load_css'] = true;
        }
        
        $filename = 'table';
        if($google){
            global $frm_google_chart;
            $filename = 'google_table';
            $options = array();
            
            if($pagesize)
                $options = array('page' => 'enable', 'pageSize' => (int)$pagesize);
                
            $options['allowHtml'] = true;
            $options['sort'] = ($sort) ? 'enable' : 'disable';
            
            if($style)
                $options['cssClassNames'] = array('oddTableRow' => 'frm_even');
        }
        
        ob_start();
		if ( isset($delete_message) ) {
			echo '<div class="'. ($style ? ' with_frm_style': '') . '"><div class="frm_message">'. $delete_message .'</div></div>';
			unset ($delete_message);
		}
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/'. $filename .'.php');
        $contents = ob_get_contents();
        ob_end_clean();
        
        if(!$google and $clickable)
            $contents = make_clickable($contents);
        return $contents;
    }
    
    public static function get_search($atts){
        extract(shortcode_atts(array('id' => false, 'post_id' => '', 'label' => __('Search', 'formidable')), $atts));
        //if (!$id) return;
        if($post_id == ''){
            global $post;
            if($post)
                $post_id = $post->ID;
        }
        
        if($post_id != '')
            $action_link = get_permalink($post_id);
        else
            $action_link = '';
        
        ob_start();
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/search.php');
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    
    public static function entry_link_shortcode($atts){
        global $frm_entry, $frm_entry_meta, $post;
        extract(shortcode_atts(array(
            'id' => false, 'field_key' => 'created_at', 'type' => 'list', 'logged_in' => true, 
            'edit' => true, 'class' => '', 'link_type' => 'page', 'blank_label' => '', 
            'param_name' => 'entry', 'param_value' => 'key', 'page_id' => false, 'show_delete' => false,
            'confirm' => __('Are you sure you want to delete that entry?', 'formidable'), 
        ), $atts));
        
        $user_ID = get_current_user_id();
        if (!$id or ($logged_in && !$user_ID)) return;
        $id = (int)$id;
        if($show_delete === 1) $show_delete = __('Delete');
        $atts['label'] = $show_delete;
        $s = FrmAppHelper::get_param('frm_search', false);
        
        $action = (isset($_GET) and isset($_GET['frm_action'])) ? 'frm_action' : 'action';
        if($action == 'destroy'){
            $delete_id = (isset($_GET['entry'])) ? $_GET['entry'] : false;
            if($delete_id){
                $entry = $frm_entry->getOne($delete_id);
                if ( $entry && FrmProEntriesHelper::user_can_delete($delete_id) ) {
                    $frm_entry->destroy( $entry->id );
                }
                unset($entry);
            }
            unset($delete_id);
        }

        if($s)
            $entry_ids = FrmProEntriesHelper::get_search_ids($s, $id);
        else
            $entry_ids = $frm_entry_meta->getEntryIds(array('fi.form_id' => (int)$id));
        
        if ($entry_ids){
            $id_list = implode(',', $entry_ids);
            $order = ($type == 'collapse') ? ' ORDER BY it.created_at DESC' : '';
            
            $where = "it.id in ($id_list)";
            if ($logged_in)
                $where .= " and it.form_id='". $id ."' and it.user_id='". (int)$user_ID ."'";
            
            $entries = $frm_entry->getAll($where, $order, '', true);
        }

        if (!empty($entries)){
            if ($type == 'list'){
                $content = "<ul class='frm_entry_ul $class'>\n";
            }else if($type == 'collapse'){
                wp_enqueue_style('jquery-theme');
                wp_enqueue_script('jquery-ui-core');
                $content = '<div class="frm_collapse">';
                $year = $month = '';
                $prev_year = $prev_month = false;
            }else{
                $content = "<select id='frm_select_form_$id' name='frm_select_form_$id' class='$class' onchange='location=this.options[this.selectedIndex].value;'>\n <option value='". get_permalink($post->ID) ."'>$blank_label</option>\n";
            }
            
            global $frm_field;
            if($field_key != 'created_at')
                $field = $frm_field->getOne($field_key);
            
            if($page_id)
                $permalink = get_permalink($page_id);
            else
                $permalink = get_permalink($post->ID);
            
            foreach ($entries as $entry){
                if(isset($_GET) and isset($_GET[$action]) and $_GET[$action] == 'destroy'){
                    if(isset($_GET['entry']) and ($_GET['entry'] == $entry->item_key or $_GET['entry'] == $entry->id))
                        continue;
                }
                
                if($entry->post_id){
                    global $wpdb;
                    $post_status = $wpdb->get_var("SELECT post_status FROM $wpdb->posts WHERE ID=".$entry->post_id);
                    if($post_status != 'publish')
                        continue;
                }
                $value = '';
                $meta = false;
                if ($field_key && $field_key != 'created_at'){
                    if($entry->post_id and (($field and $field->field_options['post_field']) or $field->type == 'tag'))
                        $value = FrmProEntryMetaHelper::get_post_value($entry->post_id, $field->field_options['post_field'], $field->field_options['custom_field'], array('type' => $field->type, 'form_id' => $field->form_id, 'field' => $field));
                    else
                        $meta = isset($entry->metas[$field_key]) ? $entry->metas[$field_key] : '';
                }else
                    $meta = reset($entry->metas);
                
                $value = ($field_key == 'created_at' or !isset($meta) or !$meta) ? $value : (is_object($meta) ? $meta->meta_value : $meta);
                
                if(empty($value))
                    $value = date_i18n(get_option('date_format'), strtotime($entry->created_at));
                else
                    $value = FrmProEntryMetaHelper::display_value($value, $field, array('type' => $field->type, 'show_filename' => false));
                
                if($param_value == 'key')
                    $args = array($param_name => $entry->item_key);
                else
                    $args = array($param_name => $entry->id);
                    
                if ($edit)
                    $args['frm_action'] = 'edit';
                
                if ($link_type == 'scroll'){
                    $link = '#'.$entry->item_key;
                }else if ($link_type == 'admin'){
                    $link = add_query_arg($args, $_SERVER['REQUEST_URI']);
                }else{
                    $link = add_query_arg($args, $permalink);
                }
                
                unset($args);
                
                $current = (isset($_GET['entry']) && $_GET['entry'] == $entry->item_key) ? true : false;
                if ($type == 'list'){
                    $content .= "<li><a href='$link'>". $value ."</a>";
                    if ( $show_delete && isset($permalink) && FrmProEntriesHelper::user_can_delete($entry) ) {
                        $content .= " <a href='". add_query_arg(array('frm_action' => 'destroy', 'entry' => $entry->id), $permalink) ."' class='frm_delete_list' onclick='return confirm(\"". $confirm ."\")'>$show_delete</a>\n";
                    }
                    $content .= "</li>\n";
                }else if($type == 'collapse'){
                    $new_year = strftime('%G', strtotime($entry->created_at));
                    $new_month = strftime('%B', strtotime($entry->created_at));
                    if ($new_year != $year){
                        if($prev_year){
                            if($prev_month) $content .= '</ul></div>';
                            $content .= '</div>';
                            $prev_month = false;
                        }
                        $style = ($prev_year) ? " style='display:none'" : '';
                        $triangle = ($prev_year) ? "e" : "s";
                        $content .= "\n<div class='frm_year_heading frm_year_heading_$id'>
                            <span class='ui-icon ui-icon-triangle-1-$triangle'></span>\n
                            <a>$new_year</a></div>\n
                            <div class='frm_toggle_container' $style>\n";
                        $prev_year = true;
                    }
                    if ($new_month != $month){
                        if($prev_month)
                            $content .= '</ul></div>';
                        $style = ($prev_month) ? " style='display:none'" : '';
                        $triangle = ($prev_month) ? "e" : "s";
                        $content .= "<div class='frm_month_heading frm_month_heading_$id'>
                            <span class='ui-icon ui-icon-triangle-1-$triangle'></span>\n
                            <a>$new_month</a>\n</div>\n
                            <div class='frm_toggle_container frm_month_listing' $style><ul>\n";
                        $prev_month = true;
                    }
                    $content .= "<li><a href='$link'>". $value ."</a>";
                    
                    if ( $show_delete && isset($permalink) && FrmProEntriesHelper::user_can_delete($entry) ) {
                        $content .= " <a href='". add_query_arg(array('frm_action' => 'destroy', 'entry' => $entry->id), $permalink) ."' class='frm_delete_list' onclick='return confirm(\"". $confirm ."\")'>$show_delete</a>\n";
                    }
                    $content .= "</li>\n";
                    $year = $new_year;
                    $month = $new_month;
                }else{
                    $selected = $current ? ' selected="selected"' : '';
                    $content .= "<option value='$link'$selected>" . esc_attr($value) . "</option>\n";
                }
            }

            if ($type == 'list')
                $content .= "</ul>\n";
            else if($type == 'collapse'){
                if($prev_year) $content .= '</div>';
                if($prev_month) $content .= '</ul></div>';
                $content .= '</div>';
                $content .= "<script type='text/javascript'>jQuery(document).ready(function($){ $('.frm_month_heading_". $id . ", .frm_year_heading_". $id ."').toggle(function(){ $(this).children('.ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s'); $(this).children('.ui-icon-triangle-1-s').removeClass('ui-icon-triangle-1-e'); $(this).next('.frm_toggle_container').fadeIn('slow');},function(){ $(this).children('.ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e'); $(this).children('.ui-icon-triangle-1-e').removeClass('ui-icon-triangle-1-s'); $(this).next('.frm_toggle_container').hide();});})</script>\n";
            }else{
                $content .= "</select>\n";
                if($show_delete and isset($_GET) and isset($_GET['entry']) and $_GET['entry'])
                    $content .= " <a href='".add_query_arg(array('frm_action' => 'destroy', 'entry' => $_GET['entry']), $permalink) ."' class='frm_delete_list' onclick='return confirm(\"". $confirm ."\")'>$show_delete</a>\n";
            }
            
        }else
            $content = '';
        
        return $content;
    }
    
    public static function entry_edit_link($atts){
        global $post, $frm_vars, $frmdb;
        extract(shortcode_atts(array(
            'id' => (isset($frm_vars['editing_entry']) ? $frm_vars['editing_entry'] : false), 
            'label' => __('Edit'), 'cancel' => __('Cancel', 'formidable'), 
            'class' => '', 'page_id' => (($post) ? $post->ID : 0), 'html_id' => false,
            'prefix' => '', 'form_id' => false
        ), $atts));

        $link = '';
        $entry_id = ($id and is_numeric($id)) ? $id : FrmAppHelper::get_param('entry', false);
            
        if(!$entry_id or empty($entry_id)){
            if($id == 'current'){
                if(isset($frm_vars['editing_entry']) && $frm_vars['editing_entry'] && is_numeric($frm_vars['editing_entry']))
                    $entry_id = $frm_vars['editing_entry'];
                else if($post)
                    $entry_id = $frmdb->get_var($frmdb->entries, array('post_id' => $post->ID), 'id');
            }
        }
        
        if(!$entry_id or empty($entry_id))     
            return '';
            
        if(!$form_id)
            $form_id = (int)$frmdb->get_var($frmdb->entries, array('id' => $entry_id), 'form_id');
        
        //if user is not allowed to edit, then don't show the link
        if ( !FrmProEntriesHelper::user_can_edit($entry_id, $form_id) ) {
            return $link;
        }
            
        if(empty($prefix)){
           $link = add_query_arg(array('frm_action' => 'edit', 'entry' => $entry_id), get_permalink($page_id));
           
           if($label)
               $link = '<a href="'. $link .'" class="'. $class.'">'. $label .'</a>';
               
           return $link;
        }
        
        
        
        $action = (isset($_POST) && isset($_POST['frm_action'])) ? 'frm_action' : 'action';
        if (isset($_POST) and isset($_POST[$action]) and ($_POST[$action] =='update') and isset($_POST['form_id']) and ($_POST['form_id'] == $form_id) and isset($_POST['id']) and ($_POST['id'] == $entry_id)){
            $errors = (isset($frm_vars['created_entries'][$form_id]) and isset($frm_vars['created_entries'][$form_id]['errors'])) ? $frm_vars['created_entries'][$form_id]['errors'] : array();
            
            if(!empty($errors))
                return FrmFormsController::get_form_shortcode(array('id' => $form_id, 'entry_id' => $entry_id));
            
            $link .= "<script type='text/javascript'>window.onload= function(){var frm_pos=jQuery('#". $prefix . $entry_id ."').offset();window.scrollTo(frm_pos.left,frm_pos.top);}</script>";
        }

            
        if(!$html_id)
            $html_id = "frm_edit_{$entry_id}";
          
        $frm_vars['forms_loaded'][] = true;  
        $link .= "<a href='javascript:frmEditEntry($entry_id,\"$prefix\",$page_id,$form_id,\"". htmlspecialchars($cancel) ."\",\"$class\")' class='frm_edit_link $class' id='$html_id'>$label</a>\n";

        return $link;
    }
    
    public static function entry_update_field($atts){
        global $frm_vars, $post, $frmdb, $frm_update_link, $frm_field;
        
        extract(shortcode_atts(array(
            'id' => (isset($frm_vars['editing_entry']) ? $frm_vars['editing_entry'] : false),
            'field_id' => false, 'form_id' => false, 
            'label' => 'Update', 'class' => '', 'value' => '', 'message' => ''
        ), $atts));
        
        $link = '';
        $entry_id = (int)($id and is_numeric($id)) ? $id : FrmAppHelper::get_param('entry', false);
        
        if(!$entry_id or empty($entry_id))
            return;
            
        if(!$form_id)
            $form_id = (int)$frmdb->get_var($frmdb->entries, array('id' => $entry_id), 'form_id');
        
        if ( !FrmProEntriesHelper::user_can_edit($entry_id, $form_id) ) {
            return;
        }
        
        $field = $frm_field->getOne($field_id);
        if(!$field)
            return;
            
        if(!is_numeric($field_id))
            $field_id = $field->id;
        
        //check if current value is equal to new value
        $link = $current_val = FrmProEntryMetaHelper::get_post_or_meta_value($entry_id, $field);
        if($current_val == $value)
            return;
            
        if(!$frm_update_link)
            $frm_update_link = array();
            
        $num = isset($frm_update_link[$entry_id .'-'. $field_id]) ? $frm_update_link[$entry_id .'-'. $field_id] : 0;
        $num = (int)$num + 1;
        $frm_update_link[$entry_id .'-'. $field_id] = $num;
        
        $link = '<a href="#" onclick="frmUpdateField('. $entry_id .','. $field_id .',\''. $value .'\',\''. $message .'\','. $num .');return false;" id="frm_update_field_'. $entry_id .'_'. $field_id .'_'. $num .'" class="frm_update_field_link '. $class .'">'. $label .'</a>';
        
        return $link;
    }
    
    public static function entry_delete_link($atts){
        global $post, $frm_vars;
        extract(shortcode_atts(array(
            'id' => (isset($frm_vars['editing_entry']) ? $frm_vars['editing_entry'] : false), 'label' => __('Delete'), 
            'confirm' => __('Are you sure you want to delete that entry?', 'formidable'), 
            'class' => '', 'page_id' => (($post) ? $post->ID : 0), 'html_id' => false, 'prefix' => ''
        ), $atts));
        
        $entry_id = ($id and is_numeric($id)) ? $id : ((is_admin() and !defined('DOING_AJAX')) ? FrmAppHelper::get_param('id', false) : FrmAppHelper::get_param('entry', false));
        
        if(empty($entry_id))
            return '';
            
        // Check if user has permission to delete before showing link
        if ( !FrmProEntriesHelper::user_can_delete($entry_id) ) {
            return '';
        }
        
        $frm_vars['forms_loaded'][] = true;
        
        if(!empty($prefix)){
            if(!$html_id)
                $html_id = "frm_delete_{$entry_id}";
            
            $link = "<a href='javascript:frmDeleteEntry($entry_id,\"$prefix\")' class='frm_delete_link $class' id='$html_id' onclick='return confirm(\"". $confirm ."\")'>$label</a>\n";
            return $link;
        }
        
        $link = '';
        
        // Delete entry now
        $action = FrmAppHelper::get_param('frm_action');
        if($action == 'destroy'){
            $entry_key = FrmAppHelper::get_param('entry');
            if(is_numeric($entry_key) and $entry_key == $entry_id){
                $link = FrmProEntriesController::ajax_destroy(false, false, false);
                if(!empty($link)){
                    $new_link = '<div class="frm_message">'. $link .'</div>';
                    if(empty($label))
                       return;
                       
                    if($link == __('Your entry was successfully deleted', 'formidable'))
                        return $new_link;
                    else
                        $link = $new_link;
                        
                    unset($new_link);
                }
            }
        }
                   
        if(empty($label)){
            $link .= add_query_arg(array('frm_action' => 'destroy', 'entry' => $entry_id), get_permalink($page_id));
        }else{
            $link .= "<a href='". add_query_arg(array('frm_action' => 'destroy', 'entry' => $entry_id), get_permalink($page_id)) ."' class='$class' onclick='return confirm(\"". $confirm ."\")'>$label</a>\n";
        }
            
        return $link;
    }
    
    public static function get_field_value_shortcode($atts){
        extract(shortcode_atts(array(
            'entry_id' => false, 'field_id' => false, 'user_id' => false,
            'ip' => false, 'show' => '', 'format' => '',
        ), $atts));
        
        if ( !$field_id  ) {
            return __('You are missing options in your shortcode. field_id is required.', 'formidable');
        }
            
        global $frm_field, $wpdb, $frmdb;
        
        $field = $frm_field->getOne($field_id);
        if(!$field)
            return '';
            
        $query = $wpdb->prepare("SELECT post_id, id FROM $frmdb->entries WHERE form_id=%d", $field->form_id);
        if ( $user_id ) {
            // make sure we are not getting entries for logged-out users
            $query .= $wpdb->prepare(' AND user_id=%d AND user_id > 0', (int) FrmProAppHelper::get_user_id_param($user_id));
        }
        
        if ( $entry_id ) {
            if ( !is_numeric($entry_id) ) {
                $entry_id = isset($_GET[$entry_id]) ? $_GET[$entry_id] : $entry_id;
            }
            
            if ( (int) $entry_id < 1 ) {
                // don't run the sql query if we know there will be no results
                return;
            }
            
            $query .= $wpdb->prepare(' AND id=%d', (int)$entry_id);
        }
        
        if ( $ip ) {
            $query .= $wpdb->prepare(' AND ip=%s', ($ip == true) ? $_SERVER['REMOTE_ADDR'] : $ip);
        }
        $query .= " ORDER BY created_at DESC LIMIT 1";
        $entry = $wpdb->get_row($query);
        if(!$entry)
            return;
        
        $value = FrmProEntryMetaHelper::get_post_or_meta_value($entry, $field, $atts);
        $atts['type'] = $field->type;
        $atts['post_id'] = $entry->post_id;
        $atts['entry_id'] = $entry->id;
        if(!isset($atts['show_filename']))
            $atts['show_filename'] = false;
            
        if(isset($show) and !empty($show)){
            $atts['show'] = $show;
            $value = FrmProFieldsHelper::get_display_value($value, $field, $atts);
        }else{
            $value = FrmProEntryMetaHelper::display_value($value, $field, $atts);
        }
        
        return $value;
    }
    
    public static function show_entry_shortcode($atts){
        $content = FrmEntriesController::show_entry_shortcode($atts);
        return $content;
    }

	/* Alternate Row Color for Default HTML */
	public static function change_row_color($atts){
		global $frm_email_col, $frmpro_settings;
		if($frm_email_col){
			$alt_color = "background-color:#{$frmpro_settings->bg_color_active};";
			$frm_email_col = false;
		}else{
			$alt_color = "background-color:#{$frmpro_settings->bg_color};";
			$frm_email_col = true;
		}
		return $alt_color;
	}
    
    public static function maybe_set_cookie($entry_id, $form_id) {
        if ( defined('WP_IMPORTING') || defined('DOING_AJAX') ) {
            return;
        }

        if ( isset($_POST) && isset($_POST['frm_skip_cookie']) ) {
            self::set_cookie($entry_id, $form_id);
            return;
        }
        
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/set_cookie.php');
    }
        
    /* AJAX */
    public static function ajax_set_cookie(){
        self::set_cookie();
        die();
    }
    
    public static function set_cookie($entry_id=false, $form_id=false){
        if ( headers_sent() ) {
            return;
        }
        
        if(!$entry_id)
            $entry_id = FrmAppHelper::get_param('entry_id');
        
        if(!$form_id)    
            $form_id = FrmAppHelper::get_param('form_id');
            
        $frm_form = new FrmForm();
        $form = $frm_form->getOne($form_id);
        $form->options = maybe_unserialize($form->options);
        $expiration = (isset($form->options['cookie_expiration'])) ? ((float)$form->options['cookie_expiration'] *60*60) : 30000000; 
        $expiration = apply_filters('frm_cookie_expiration', $expiration, $form_id, $entry_id);
        setcookie('frm_form'.$form_id.'_' . COOKIEHASH, current_time('mysql', 1), time() + $expiration, COOKIEPATH, COOKIE_DOMAIN);
    }
    
    public static function ajax_create(){
        global $frm_entry;
        
        $frm_form = new FrmForm();
        $form = $frm_form->getOne($_POST['form_id']);
        if(!$form){
            echo false;
            die();
        }
            
        $no_ajax_fields = array('file');
        $errors = $frm_entry->validate($_POST, $no_ajax_fields);
        
        if(empty($errors)){
            global $wpdb;
            
            $where = $wpdb->prepare("form_id=%d", $form->id);
            if ( isset($_POST['frm_page_order_'. $form->id]) ) {
                $where .= $wpdb->prepare(" AND field_order < %d", $_POST['frm_page_order_'. $form->id]);
            }
                
            $ajax = (isset($form->options['ajax_submit'])) ? $form->options['ajax_submit'] : 0;
            //ajax submit if no file, rte, captcha
            if($ajax){
                $no_ajax = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}frm_fields WHERE type in ('". implode("','", $no_ajax_fields) ."') AND {$where} LIMIT 1");
                if($no_ajax)
                    $ajax = false;
            }

            if($ajax){
                global $frm_vars;
                $frm_vars['ajax'] = true;
                $frm_vars['css_loaded'] = true;
                
                if ( (!isset($_POST['frm_page_order_'. $form->id]) && !FrmProFormsHelper::going_to_prev($form->id)) || FrmProFormsHelper::saving_draft($form->id) ) {
                    $processed = true;
                    FrmEntriesController::process_entry($errors, true);
                }
                
                echo FrmFormsController::show_form($form->id);
                
                // trigger the footer scripts if there is a form to show
                if ( $errors || !isset($form->options['show_form']) || $form->options['show_form'] || !isset($processed) ) {
                    self::register_scripts();
                    
                    FrmProEntriesController::enqueue_footer_js();
                    
                    wp_deregister_script('formidable');
                    
                    global $wp_scripts, $wp_styles;
                    foreach ( array('jquery', 'jquery-ui-core', 'jquery-migrate', 'thickbox') as $s ) {
                        if ( isset($wp_scripts->registered[$s]) ) {
                            $wp_scripts->done[] = $s;
                        }
                        unset($s);
                    }
                    
                    foreach ( $wp_styles->registered as $s => $info ) {
                        if ( $s != 'jquery-theme' ) {
                            $wp_styles->done[] = $s;
                        }
                        
                        unset($s);
                    }
                    
                    wp_print_footer_scripts();
                    
                    FrmProEntriesController::footer_js();
                }
            } else {
                echo false;
            }
        }else{
            $errors = str_replace('"', '&quot;', $errors);
            $obj = array();
            foreach($errors as $field => $error){
                $field_id = str_replace('field', '', $field);
                $obj[$field_id] = $error;
            }
            echo json_encode($obj);
        }
        
        die();
    }
    
    public static function ajax_update(){
        return self::ajax_create();
    }
    
    public static function wp_ajax_destroy(){
        self::ajax_destroy();
        die();
    }
    
    public static function ajax_destroy($form_id = false, $ajax = true, $echo = true) {
        global $wpdb, $frm_vars;
        
        $entry_key = FrmAppHelper::get_param('entry');
        if ( ! $form_id ) {
            $form_id = FrmAppHelper::get_param('form_id');
        }
        
        if ( ! $entry_key ) {
            return;
        }
        
        if ( isset( $frm_vars['deleted_entries'] ) && is_array( $frm_vars['deleted_entries'] ) && in_array( $entry_key, $frm_vars['deleted_entries'] ) ) {
            return;
        }
        
        if ( is_numeric( $entry_key ) ) {
            $where = $wpdb->prepare('id = %d', $entry_key);
        } else {
            $where = $wpdb->prepare('item_key = %s', $entry_key);
        }
        
        $entry = $wpdb->get_row("SELECT id, form_id, is_draft, user_id FROM {$wpdb->prefix}frm_items WHERE $where");
        unset( $where );
        
        if ( ! $entry || ( $form_id && $entry->form_id != (int) $form_id ) ) {
            return;
        }
        
        if ( !FrmProEntriesHelper::user_can_delete($entry) ) {
            $message = __( 'There was an error deleting that entry', 'formidable' );
            if ( $echo )
                echo '<div class="frm_message">'. $message .'</div>';
            return;
        }
        
        $entry_id = $entry->id;
        
        global $frm_entry;
        $frm_entry->destroy( $entry_id );
        
        if ( ! isset( $frm_vars['deleted_entries'] ) || empty( $frm_vars['deleted_entries'] ) ) {
            $frm_vars['deleted_entries'] = array();
        }
        $frm_vars['deleted_entries'][] = $entry_id;
            
        if ( $ajax && $echo ) {
            echo $message = 'success';
        } else if ( ! $ajax ) {
			$message = apply_filters('frm_delete_message', __( 'Your entry was successfully deleted', 'formidable' ), $entry);
                
            if ( $echo ) {
                echo '<div class="frm_message">'. $message .'</div>';
            }
        }
        
        return $message;
    }
    
    public static function edit_entry_ajax(){
        $id = FrmAppHelper::get_param('id');
        $entry_id = FrmAppHelper::get_param('entry_id', false); 
        $post_id = FrmAppHelper::get_param('post_id', false);
        
        global $frm_vars;
        $frm_vars['ajax_edit'] = ($entry_id) ? $entry_id : true;
        $_GET['entry'] = $entry_id;
        
        if($post_id and is_numeric($post_id)){
            global $post;
            if(!$post)
                $post = get_post($post_id);
        }
        
        FrmAppHelper::load_scripts(array('formidable') );
        
        echo "<script type='text/javascript'>
/*<![CDATA[*/
jQuery(document).ready(function($){
$('#frm_form_". $id ."_container .frm-show-form').submit(window.frmOnSubmit);
});
/*]]>*/
</script>";
        echo FrmFormsController::get_form_shortcode(compact('id', 'entry_id'));

        $frm_vars['ajax_edit'] = false;
        //if(!isset($_POST) or (!isset($_POST['action']) and !isset($_POST['frm_action])))
        //    echo FrmProEntriesController::footer_js();
        
        die();
    }
    
    public static function update_field_ajax(){
        $entry_id = FrmAppHelper::get_param('entry_id');
        $field_id = FrmAppHelper::get_param('field_id');
        $value = FrmAppHelper::get_param('value');
        
        global $wpdb, $frm_field, $frm_entry_meta;
        
        $entry_id = (int)$entry_id;
        
        if(!$entry_id)
            die();
           
        $where = $wpdb->prepare( is_numeric($field_id) ? "fi.id=%d" : "field_key=%s", $field_id);
            
        $field = $frm_field->getAll($where, '', ' LIMIT 1');
    
        if ( !$field || !FrmProEntriesHelper::user_can_edit($entry_id, $field->form_id) ) {
            die();
        }
        
        $post_id = false;
        if ( isset($field->field_options['post_field']) && !empty($field->field_options['post_field']) ) {
            $post_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$wpdb->prefix}frm_items WHERE id=%d", $entry_id ));
        }
        
        $updated = false;
        if(!$post_id){
            $updated = $frm_entry_meta->update_entry_meta($entry_id, $field_id, $meta_key = null, $value);
            
            if ( !$updated ) {
                $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}frm_item_metas WHERE item_id = %d and field_id = %d", $entry_id, $field_id));
                $updated = $frm_entry_meta->add_entry_meta($entry_id, $field_id, '', $value);
            }
            wp_cache_delete( $entry_id, 'frm_entry');
        }else{
            switch($field->field_options['post_field']){
                case 'post_custom':
                    $updated = update_post_meta($post_id, $field->field_options['custom_field'], maybe_serialize($value));
                break;
                case 'post_category':
                    $taxonomy = (isset($field->field_options['taxonomy']) and !empty($field->field_options['taxonomy'])) ? $field->field_options['taxonomy'] : 'category';
                    $updated = wp_set_post_terms( $post_id, $value, $taxonomy );
                break;
                default:
                    $post = get_post($post_id, ARRAY_A);
                    $post[$field->field_options['post_field']] = maybe_serialize($value);
                    $updated = wp_insert_post( $post );
                break;
            }
        }
        
        if ( $updated ) {
            // set updated_at time
            $wpdb->update( $wpdb->prefix .'frm_items', 
                array('updated_at' => current_time('mysql', 1), 'updated_by' => get_current_user_id()), 
                array('id' => $entry_id) 
            );
        }
        
        do_action('frm_after_update_field', compact('entry_id', 'field_id', 'value'));
        die($updated);
    }
    
    public static function send_email(){
        if(current_user_can('frm_view_forms') or current_user_can('frm_edit_forms') or current_user_can('frm_edit_entries')){
            $entry_id = FrmAppHelper::get_param('entry_id');
            $form_id = FrmAppHelper::get_param('form_id');
            $type = FrmAppHelper::get_param('type');
            
            if($type == 'autoresponder')
                $sent_to = FrmProNotification::autoresponder($entry_id, $form_id);
            else
                $sent_to = FrmProNotification::entry_created($entry_id, $form_id);
            
            if(is_array($sent_to))
                $sent_to = implode(', ', $sent_to);
                
            printf(__('Email Resent to %s', 'formidable'), $sent_to);
        }else{
            _e('Email Resent to No one! You do not have permission', 'formidable');
        }
        die();
    }
    
    public static function export_xml_direct(){
        FrmProXMLController::export_xml_direct('entries', FrmAppHelper::get_param('ids'));
        die();
    }
    
    public static function redirect_url($url){
        $url = str_replace(array(' ', '[', ']', '|', '@'), array('%20', '%5B', '%5D', '%7C', '%40'), $url);
        return $url;
    }
    
    public static function allow_form_edit($action, $form) {
        return FrmProEntriesHelper::allow_form_edit($action, $form);
    }
    
    public static function setup_edit_vars($values, $record = false) {
        return FrmProEntriesHelper::setup_edit_vars($values, $record);
    }
    
    public static function email_value($value, $meta, $entry) {
        return FrmProEntryMetaHelper::email_value($value, $meta, $entry);
    }
    
    /* Trigger model actions */
    
    public static function pre_validate($errors, $values) {
        global $frmpro_entry;
        return $frmpro_entry->pre_validate($errors, $values);
    }
    
    public static function validate($params, $fields, $form, $title, $description) {
        global $frmpro_entry;
        $frmpro_entry->validate($params, $fields, $form, $title, $description);
    }
    
    public static function create_post($entry_id, $form_id) {
        global $frmpro_entry;
        $frmpro_entry->create_post($entry_id, $form_id);
    }
    
    public static function update_post($entry_id, $form_id) {
        global $frmpro_entry;
        $frmpro_entry->update_post($entry_id, $form_id);
    }
    
    public static function destroy_post($entry_id, $entry = false) {
        global $frmpro_entry;
        $frmpro_entry->destroy_post($entry_id, $entry);
    }
    
    /* Trigger entry meta model actions */
    
    public static function before_create_meta($values) {
        $frmpro_entry_meta = new FrmProEntryMeta();
        return $frmpro_entry_meta->before_save($values);
    }
    
    public static function create_meta($entry) {
        $frmpro_entry_meta = new FrmProEntryMeta();
        $frmpro_entry_meta->create($entry);
    }
    
    public static function before_update_meta($values) {
        $frmpro_entry_meta = new FrmProEntryMeta();
        return $frmpro_entry_meta->before_save($values);
    }
    
    public static function validate_meta($errors, $field) {
        $frmpro_entry_meta = new FrmProEntryMeta();
        return $frmpro_entry_meta->validate($errors, $field);
    }
}
