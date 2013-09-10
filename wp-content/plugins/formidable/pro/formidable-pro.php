<?php 
/**
 * @package Formidable
 */
 
define('FRMPRO_PATH', FRM_PATH .'/pro');
define('FRMPRO_VIEWS_PATH', FRMPRO_PATH .'/classes/views');
define('FRMPRO_IMAGES_URL', FRM_URL .'/pro/images');

require_once(FRMPRO_PATH .'/classes/models/FrmProSettings.php');

global $frmpro_settings;

$frmpro_settings = get_transient('frmpro_options');
if(!is_object($frmpro_settings)){
    if($frmpro_settings){ //workaround for W3 total cache conflict
        $frmpro_settings = unserialize(serialize($frmpro_settings));
    }else{
        $frmpro_settings = get_option('frmpro_options');

        // If unserializing didn't work
        if(!is_object($frmpro_settings)){
            if($frmpro_settings) //workaround for W3 total cache conflict
                $frmpro_settings = unserialize(serialize($frmpro_settings));
            else
                $frmpro_settings = new FrmProSettings();
            update_option('frmpro_options', $frmpro_settings);
            set_transient('frmpro_options', $frmpro_settings);
        }
    }
}
$frmpro_settings = get_option('frmpro_options');

// If unserializing didn't work
if(!is_object($frmpro_settings)){
    if($frmpro_settings) //workaround for W3 total cache conflict
        $frmpro_settings = unserialize(serialize($frmpro_settings));
    else
        $frmpro_settings = new FrmProSettings();
    update_option('frmpro_options', $frmpro_settings);
}

$frmpro_settings->set_default_options();

global $frm_readonly;
$frm_readonly = false;

global $frm_show_fields, $frm_rte_loaded, $frm_datepicker_loaded;
global $frm_timepicker_loaded, $frm_hidden_fields, $frm_calc_fields, $frm_input_masks;
$frm_show_fields = $frm_rte_loaded = $frm_datepicker_loaded = $frm_timepicker_loaded = array();
$frm_hidden_fields = $frm_calc_fields = $frm_input_masks = array();

global $frm_settings;
if(!is_admin() and $frm_settings->jquery_css)
    $frm_datepicker_loaded = true;
    
require_once(FRMPRO_PATH .'/classes/models/FrmProDb.php');
require_once(FRMPRO_PATH .'/classes/models/FrmProDisplay.php');
require_once(FRMPRO_PATH .'/classes/models/FrmProEntry.php');
require_once(FRMPRO_PATH .'/classes/models/FrmProEntryMeta.php');
require_once(FRMPRO_PATH .'/classes/models/FrmProField.php');
require_once(FRMPRO_PATH .'/classes/models/FrmProForm.php');
require_once(FRMPRO_PATH .'/classes/models/FrmProNotification.php');

global $frmprodb;
global $frmpro_display;
global $frmpro_entry;
global $frmpro_entry_meta;
global $frmpro_field;
global $frmpro_form;
global $frmpro_notification;

$frmprodb           = new FrmProDb();
$frmpro_display     = new FrmProDisplay();
$frmpro_entry       = new FrmProEntry();
$frmpro_entry_meta  = new FrmProEntryMeta();
$frmpro_field       = new FrmProField();
$frmpro_form        = new FrmProForm();
$frmpro_notification = new FrmProNotification();

// Instansiate Controllers
require_once(FRMPRO_PATH .'/classes/controllers/FrmProAppController.php');
require_once(FRMPRO_PATH .'/classes/controllers/FrmProDisplaysController.php');
require_once(FRMPRO_PATH .'/classes/controllers/FrmProEntriesController.php');
require_once(FRMPRO_PATH .'/classes/controllers/FrmProFieldsController.php');
require_once(FRMPRO_PATH .'/classes/controllers/FrmProFormsController.php');
require_once(FRMPRO_PATH .'/classes/controllers/FrmProSettingsController.php');
require_once(FRMPRO_PATH .'/classes/controllers/FrmProStatisticsController.php');

global $frmpro_fields_controller;
global $frmpro_forms_controller;
global $frmpro_settings_controller;
global $frmpro_statistics_controller;

$obj = new FrmProAppController();
$obj = new FrmProDisplaysController();
$obj = new FrmProEntriesController();
$frmpro_fields_controller      = new FrmProFieldsController();
$frmpro_forms_controller       = new FrmProFormsController();
$frmpro_settings_controller    = new FrmProSettingsController();
$frmpro_statistics_controller  = new FrmProStatisticsController();

if (IS_WPMU){
//Models
require_once(FRMPRO_PATH .'/classes/models/FrmProCopy.php');
global $frmpro_copy;
$frmpro_copy = new FrmProCopy();
    
//Add options to copy forms and displays
require_once(FRMPRO_PATH .'/classes/controllers/FrmProCopiesController.php');
global $frmpro_copies_controller;
$frmpro_copies_controller = new FrmProCopiesController();
}

// Instansiate Helpers
require_once(FRMPRO_PATH .'/classes/helpers/FrmProAppHelper.php');
require_once(FRMPRO_PATH .'/classes/helpers/FrmProDisplaysHelper.php');
require_once(FRMPRO_PATH .'/classes/helpers/FrmProEntriesHelper.php');
require_once(FRMPRO_PATH .'/classes/helpers/FrmProEntryMetaHelper.php');
require_once(FRMPRO_PATH .'/classes/helpers/FrmProFieldsHelper.php');
require_once(FRMPRO_PATH .'/classes/helpers/FrmProFormsHelper.php');

$obj = new FrmProAppHelper();
$obj = new FrmProDisplaysHelper();
$obj = new FrmProEntriesHelper();
$obj = new FrmProEntryMetaHelper();
$obj = new FrmProFieldsHelper();
$obj = new FrmProFormsHelper();

global $frm_next_page, $frm_prev_page;
$frm_next_page = $frm_prev_page = array();

global $frm_media_id;
$frm_media_id = array();

// Register Widgets
if(class_exists('WP_Widget')){
    // Include Widgets
    require_once(FRMPRO_PATH.'/classes/widgets/FrmListEntries.php');
    //require_once(FRMPRO_PATH.'/classes/widgets/FrmPollResults.php');
    
    add_action('widgets_init', create_function('', 'return register_widget("FrmListEntries");'));
    //add_action('widgets_init', create_function('', 'return register_widget("FrmPollResults");'));
}
