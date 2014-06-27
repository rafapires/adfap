<?php

class FrmProSettingsController{
    function __construct(){
        add_action('frm_before_settings', 'FrmProSettingsController::license_box', 1);
    }
    
    public static function load_hooks(){
        add_filter('frm_add_settings_section', 'FrmProSettingsController::add_styling_tab');
        add_action('frm_style_general_settings', 'FrmProSettingsController::general_style_settings');
        add_action('frm_settings_form', 'FrmProSettingsController::more_settings', 1);
        add_action('admin_init',  'FrmProSettingsController::admin_init');
        add_action('frm_update_settings',  'FrmProSettingsController::update');
        add_action('frm_store_settings', 'FrmProSettingsController::store');
        add_action('wp_ajax_frm_settings_reset', 'FrmProSettingsController::reset_styling');
    }
    
    public static function license_box(){
        global $frm_update;
        $a = isset($_GET['t']) ? $_GET['t'] : 'general_settings';
        remove_action('frm_before_settings', 'FrmSettingsController::license_box');
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/settings/license_box.php');
    }
    
    public static function add_styling_tab($tabs){
        $tabs['styling'] = array(
            'name' => __('Form Styling', 'formidable'), 'class' => 'FrmProSettingsController', 'function' => 'styling_tab'
        );
        return $tabs;
    }
    
    public static function styling_tab(){
        global $frmpro_settings, $frm_settings;
        
        $jquery_themes = FrmProAppHelper::jquery_themes();
        $a = isset($_GET['t']) ? $_GET['t'] : 'general_settings';
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/settings/styling_tab.php');
    }
    
    public static function general_style_settings($frm_settings){
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/settings/general_style.php');
    }
    
    public static function more_settings($frm_settings){
        global $frmpro_settings;
        
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/settings/form.php');
    }
    
    public static function admin_init(){
        global $frm_settings;
        if(isset($_GET) and isset($_GET['page']) and $_GET['page'] == 'formidable-settings')
            wp_enqueue_script('jquery-ui-datepicker');
        add_action('admin_head-'. sanitize_title($frm_settings->menu) .'_page_formidable-settings', 'FrmProSettingsController::head');
    }
    
    public static function head(){
        FrmSettingsController::route('stop_load');
        wp_enqueue_script('jquery-frm-themepicker');
      ?>
<link type="text/css" rel="stylesheet" href="http<?php echo is_ssl() ? 's' : ''; ?>://ajax.googleapis.com/ajax/libs/jqueryui/1.7.3/themes/base/ui.all.css" />
<link href="<?php echo admin_url('admin-ajax.php') ?>?action=frmpro_css" type="text/css" rel="Stylesheet" class="frm-custom-theme"/>
<?php
        require(FrmAppHelper::plugin_path() .'/classes/views/shared/head.php');
    }

    public static function update($params){
        global $frmpro_settings;
        $frmpro_settings->update($params);
    }

    public static function store(){
        global $frmpro_settings;
        $frmpro_settings->store();
    }
    
    public static function reset_styling(){
        global $frmpro_settings;
        $defaults = $frmpro_settings->default_options();
        
        $exclude = array('custom_css', 'already_submitted', 'rte_off', 'csv_format');
        foreach($exclude as $e){
            unset($defaults[$e]);
            unset($e);
        }
        
        echo json_encode($defaults);
        die();
    }
}
