<?php
/**
 * @package Formidable
 */
 
class FrmProAppController{
    public static function load_hooks(){
        add_action('init', 'FrmProAppController::create_taxonomies', 0 );
        add_action('frm_column_header', 'FrmProAppController::insert_header_checkbox');
        add_action('frm_first_col', 'FrmProAppController::insert_item_checkbox');
        add_action('frm_before_table', 'FrmProAppController::add_bulk_actions');
        add_filter('widget_text', 'FrmProAppController::widget_text_filter', 8 );
        add_action('frm_after_install', 'FrmProAppController::install');
        add_action('frm_after_uninstall', 'FrmProAppController::uninstall');
        
        add_action('wp_ajax_frmpro_css', 'FrmProAppController::load_css');
        add_action('wp_ajax_nopriv_frmpro_css', 'FrmProAppController::load_css');
        
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
    
    public static function widget_text_filter( $content ){
    	$regex = '/\[\s*(display-frm-data|frm-stats|frm-graph|frm-entry-links|formresults|frm-search)\s+.*\]/';
    	return preg_replace_callback( $regex, 'FrmAppController::widget_text_filter_callback', $content );
    }
    
    public static function load_css(){
        global $frmpro_settings;
        if(!is_admin())
            $use_saved = true;
        
        include(FrmAppHelper::plugin_path() .'/pro/css/custom_theme.css.php');
        die();
    }
    
    public static function install(){
        $frmprodb = new FrmProDb();
        $frmprodb->upgrade();
    }
    
    public static function uninstall(){
        $frmprodb = new FrmProDb();
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
