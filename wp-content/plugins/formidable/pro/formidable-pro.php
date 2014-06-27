<?php 
/**
 * @package Formidable
 */

require($frm_path .'/pro/classes/controllers/FrmUpdatesController.php');
global $frm_update;
$frm_update  = new FrmUpdatesController();

$frm_vars['pro_is_authorized'] = $frm_update->pro_is_authorized();

require($frm_path .'/pro/classes/controllers/FrmProSettingsController.php');
$obj = new FrmProSettingsController();

if(!$frm_vars['pro_is_authorized'])
    return;


require($frm_path .'/pro/classes/models/FrmProSettings.php');

global $frmpro_settings;

$frmpro_settings = get_option('frmpro_options');

// If unserializing didn't work
if ( !is_object($frmpro_settings) ) {
    if ( $frmpro_settings ) { //workaround for W3 total cache conflict
        $frmpro_settings = unserialize(serialize($frmpro_settings));
    } else {
        $frmpro_settings = new FrmProSettings();
    }
    update_option('frmpro_options', $frmpro_settings);
}

$frmpro_settings->set_default_options();

global $frm_input_masks;
$frm_input_masks = array();

global $frm_settings, $frm_vars;
if((!is_admin() or defined('DOING_AJAX')) and $frm_settings->jquery_css)
    $frm_vars['datepicker_loaded'] = true;

$frm_vars['next_page'] = $frm_vars['prev_page'] = array();
$frm_vars['pro_is_installed'] = true;
   
require($frm_path .'/pro/classes/models/FrmProDb.php');
require($frm_path .'/pro/classes/models/FrmProDisplay.php');
require($frm_path .'/pro/classes/models/FrmProEntry.php');
require($frm_path .'/pro/classes/models/FrmProEntryMeta.php');
require($frm_path .'/pro/classes/models/FrmProField.php');
require($frm_path .'/pro/classes/models/FrmProForm.php');
require($frm_path .'/pro/classes/models/FrmProNotification.php');

global $frmpro_display;
$frmpro_display = new FrmProDisplay();

$obj = new FrmProNotification();

// Instansiate Controllers
require($frm_path .'/pro/classes/controllers/FrmProAppController.php');
require($frm_path .'/pro/classes/controllers/FrmProDisplaysController.php');
require($frm_path .'/pro/classes/controllers/FrmProEntriesController.php');
require($frm_path .'/pro/classes/controllers/FrmProFieldsController.php');
require($frm_path .'/pro/classes/controllers/FrmProFormsController.php');
require($frm_path .'/pro/classes/controllers/FrmProStatisticsController.php');


FrmProAppController::load_hooks();
FrmProDisplaysController::load_hooks();
FrmProEntriesController::load_hooks();
FrmProFieldsController::load_hooks();
FrmProFormsController::load_hooks();
FrmProStatisticsController::load_hooks();

FrmProSettingsController::load_hooks();

if(is_admin()){
    require($frm_path .'/pro/classes/controllers/FrmProXMLController.php');
    FrmProXMLController::load_hooks();
}

if (is_multisite()){
//Models
require($frm_path .'/pro/classes/models/FrmProCopy.php');
$obj = new FrmProCopy();
 
//Add options to copy forms and displays
require($frm_path .'/pro/classes/controllers/FrmProCopiesController.php');
FrmProCopiesController::load_hooks();
}

unset($obj);

// Instansiate Helpers
require($frm_path .'/pro/classes/helpers/FrmProAppHelper.php');
require($frm_path .'/pro/classes/helpers/FrmProDisplaysHelper.php');
require($frm_path .'/pro/classes/helpers/FrmProEntriesHelper.php');
require($frm_path .'/pro/classes/helpers/FrmProEntryMetaHelper.php');
require($frm_path .'/pro/classes/helpers/FrmProFieldsHelper.php');
require($frm_path .'/pro/classes/helpers/FrmProFormsHelper.php');

FrmProFieldsHelper::load_hooks();

