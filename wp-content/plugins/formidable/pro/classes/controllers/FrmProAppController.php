<?php
/**
 * @package Formidable
 */
 
class FrmProAppController{
    function FrmProAppController(){
        add_action('init', 'FrmProAppController::create_taxonomies', 0 );
        //add_action('admin_menu', 'FrmProAppController::menu', 25);
        add_action('frm_column_header', 'FrmProAppController::insert_header_checkbox');
        add_action('frm_first_col', 'FrmProAppController::insert_item_checkbox');
        add_action('frm_before_table', 'FrmProAppController::add_bulk_actions');
        add_action('frm_before_item_nav', 'FrmProAppController::insert_search_form', 10, 4);
        add_filter('widget_text', 'FrmProAppController::widget_text_filter', 8 );
        add_action('frm_standalone_route', 'FrmProAppController::standalone_route', 10, 2);
        add_action('frm_after_install', 'FrmProAppController::install');
        add_action('frm_after_uninstall', 'FrmProAppController::uninstall');
        
        add_shortcode('frm_set_get', 'FrmProAppController::set_get');
        add_shortcode('frm-set-get', 'FrmProAppController::set_get');
    }
    
    
    public static function create_taxonomies() {
        register_taxonomy( 'frm_tag', 'formidable', array( 'hierarchical' => false,
    													//'update_count_callback' => '_update_post_term_count',
    													'labels' => array(
                                                            'name' => __('Formidable Tags', 'formidable'),
                                                            'singular_name' => __('Formidable Tag', 'formidable'),
                                                        ),
    													//'query_var' => true,
    													//'rewrite' => true,
    													'public' => true,
    													'show_ui' => true
    												) ) ;
    }
    
    public static function menu(){
        //add_submenu_page('formidable, 'Formidable | Entry Tags', '<code>Pro</code> Entry Tags', 'administrator', 'edit-tags.php&taxonomy=frm_tag', 'FrmProAppController::route');
        add_submenu_page('formidable', 'Formidable | Import/Export', 'Import/Export', 'frm_edit_forms', 'formidable-import', 'FrmProAppController::route');
    }
    
    public static function route(){
        $action = isset($_REQUEST['frm_action']) ? 'frm_action' : 'action';
        $action = FrmAppHelper::get_param($action);
        if($action=='import_xml')
            return self::import_xml();
        else
            return self::form();
    }
    
    public static function import_xml(){        
        if( isset($_FILES) and isset($_FILES['frm_import_file']) and !empty($_FILES['frm_import_file']['name']) and (int)$_FILES['frm_import_file']['size'] > 0){
            if(is_uploaded_file($_FILES['frm_import_file']['tmp_name'])){
            //$media_id = FrmProAppHelper::upload_file('frm_import_file');
            //if(is_numeric($media_id)){
                ob_start();
                readfile($_FILES['frm_import_file']['tmp_name']);
                $xml_content = ob_get_contents();
                ob_end_clean();
                FrmProAppHelper::import_xml($xml_content);
            //}else{
            //    foreach ($media_id->errors as $error)
            //        echo $error[0];
            //}
            }
        }
    }
    
    public static function form(){
        global $frm_form;
        $forms = $frm_form->getAll("is_template=0 AND (status is NULL OR status = '' OR status = 'published')", ' ORDER BY name');
        include_once(FRMPRO_VIEWS_PATH .'/shared/import_form.php');
    }
    
    
    //Bulk Actions
    public static function insert_header_checkbox(){ 
        FrmProAppHelper::header_checkbox();
    }
    

    public static function insert_item_checkbox($id){ 
        FrmProAppHelper::item_checkbox($id);
    }
    

    public static function add_bulk_actions($footer){
        FrmProAppHelper::bulk_actions($footer);
    }
    
    public static function export_xml($type=false, $ids=false){
        if(isset($_GET['page']) and preg_match('/formidable*/', $_GET['page'])){
            if(!current_user_can('frm_edit_forms')){
                global $frm_settings;
                wp_die($frm_settings->admin_permission);
            }
            
            if(!$type){
                $bulk = FrmAppHelper::get_param('action');
                if($bulk == -1)
                    $bulk = FrmAppHelper::get_param('action2');

                if(!empty($bulk) and strpos($bulk, 'bulk_') === 0){
                    $bulk = str_replace('bulk_', '', $bulk);
                }else{
                    $bulk = '-1';
                    if(isset($_POST['bulkaction']) and $_POST['bulkaction'] != '-1')
                        $bulk = $_POST['bulkaction'];
                    else if(isset($_POST['bulkaction2']) and $_POST['bulkaction2'] != '-1')
                        $bulk = $_POST['bulkaction2'];
                }
                
                if($bulk == 'export'){
                    $ids = $_REQUEST['item-action'];

                    if($_GET['page'] == 'formidable-entries')
                        $type = 'items';
                    else if($_GET['page'] == 'formidable-entry-templates')
                        $type = 'displays';
                    else
                        $type = 'forms';
                }else{ 
                    $action = isset($_REQUEST['frm_action']) ? 'frm_action' : 'action';
                    $action = FrmAppHelper::get_param($action);
                    
                    if(isset($_POST['frm_export_forms']))
                        $ids = $_POST['frm_export_forms'];
                }
            }
            
            if(!$type) return;
            
            if(is_array($ids))
                $ids = implode(',', $ids);

            FrmProAppHelper::export_xml($type, compact('ids'));
            die();
        }
    }
    
    public static function export_xml_direct($controller='forms', $ids=false){
        if(!current_user_can('frm_edit_forms')){
            global $frm_settings;
            wp_die($frm_settings->admin_permission);
        }
        $is_template = FrmAppHelper::get_param('is_template', false);
        FrmProAppHelper::export_xml($controller, compact('ids', 'is_template'));
        die();
    }
    
    public static function insert_search_form($sort_str, $sdir_str, $search_str, $fid=false){ 
        FrmProAppHelper::search_form($sort_str, $sdir_str, $search_str, $fid);
    }
    
    public static function widget_text_filter( $content ){
    	$regex = '/\[\s*(display-frm-data|frm-stats|frm-graph|frm-entry-links|formresults|frm-search)\s+.*\]/';
    	return preg_replace_callback( $regex, 'FrmAppController::widget_text_filter_callback', $content );
    }
    
    public static function standalone_route($controller, $action){
        if ($controller == 'fields'){
            if(!defined('DOING_AJAX'))
                define('DOING_AJAX', true);
        }else if ($controller == 'entries'){
            if ($action == 'csv'){
                $s = isset($_REQUEST['s']) ? 's' : 'search';
                FrmProEntriesController::csv(FrmAppHelper::get_param('form'), FrmAppHelper::get_param($s), FrmAppHelper::get_param('fid'));
                unset($s);
            }else if(!defined('DOING_AJAX')){
                define('DOING_AJAX', true);
            }
        }else if($controller == 'settings'){
            global $frmpro_settings;
            if(!is_admin())
                $use_saved = true;
            
            include(FRMPRO_PATH .'/css/custom_theme.css.php');
        }
        
        if($action == 'xml')
            self::export_xml_direct(FrmAppHelper::get_param('controller'), FrmAppHelper::get_param('ids'));
    }
    
    public static function install(){
        global $frmprodb;
        $frmprodb->upgrade();
    }
    
    public static function uninstall(){
        global $frmprodb;
        $frmprodb->uninstall();
    }
    
    public static function set_get($atts){
        foreach($atts as $att => $val){
            $_GET[$att] = $val;
            unset($att);
            unset($val);
        }
    }
    
}
