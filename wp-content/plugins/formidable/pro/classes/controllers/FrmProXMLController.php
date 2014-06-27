<?php
if ( !defined('ABSPATH') ) die('You are not allowed to call this page directly.');

class FrmProXMLController{
    public static function load_hooks(){
        add_filter('frm_default_templates_files', 'FrmProXMLController::import_default_templates');
        add_filter('frm_xml_route', 'FrmProXMLController::route', 10, 2 );
        add_filter('frm_importing_xml', 'FrmProXMLController::importing_xml', 10, 2 );
        
        add_filter('frm_upload_instructions1', 'FrmProXMLController::csv_instructions_1');
        add_filter('frm_upload_instructions2', 'FrmProXMLController::csv_instructions_2');
        add_action('frm_csv_opts', 'FrmProXMLController::csv_opts');
        add_filter('frm_xml_export_types', 'FrmProXMLController::xml_export_types');
        add_filter('frm_export_formats', 'FrmProXMLController::export_formats');
        add_action('frm_before_import_csv', 'FrmProXMLController::map_csv_fields');
        add_action('frm_export_format_csv', 'FrmProXMLController::export_csv');
        
        add_action('wp_ajax_frm_import_csv', 'FrmProXMLController::import_csv_entries');
    }
    
    public static function import_default_templates($files) {
        $files[] = FrmAppHelper::plugin_path() .'/pro/classes/views/xml/default-templates.xml';
        return $files;
    }
    
    public static function route($continue, $action) {
        if ( $action == 'import_csv' ) {
            self::import_csv();
            $continue = false;
        }
        return $continue;
    }
    
    public static function importing_xml($imported, $xml) {
        if ( !isset($xml->view) && !isset($xml->item) ) {
            return $imported;
        }
        
        $append = array(
            'views' => 0, 'posts' => 0, 'items' => 0,
        );
        $imported['updated'] = array_merge($imported['updated'], $append);
        $imported['imported'] = array_merge($imported['imported'], $append);
        unset($append);
        
        include_once(FrmAppHelper::plugin_path() .'/pro/classes/helpers/FrmProXMLHelper.php');
	    
	    // grab posts/views
		if ( isset($xml->view) ) {
		    $imported = FrmProXMLHelper::import_xml_views($xml->view, $imported);
		    unset($xml->view);
	    }
	    
	    // get entries
	    if ( isset($xml->item) ) {
            $imported = FrmProXMLHelper::import_xml_entries($xml->item, $imported);
	        unset($xml->item);
	    }
        
        return $imported;
    }
    
    public static function csv_instructions_1(){
        return __('Upload your Formidable XML or CSV file to import forms, entries, and views into this site. <br/><strong>Note: If your imported form/entry/view key and creation date match an item on your site, that item will be updated. You cannot undo this action.</strong>', 'formidable');
    }
    
    public static function csv_instructions_2(){
        return __('Choose a Formidable XML or any CSV file', 'formidable');
    }
    
    public static function csv_opts($forms) {
        $csv_del = FrmAppHelper::get_param('csv_del', ',');
        $form_id = FrmAppHelper::get_param('form_id');
        
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/xml/csv_opts.php');
    }
    
    public static function xml_export_types($types) {
        $types['items'] = __('Entries', 'formidable');
        $types['views'] = __('Views', 'formidable');
        
        return $types;
    }
    
    public static function export_formats($formats) {
        $formats['csv'] = array('name' => 'CSV', 'support' => 'items', 'count' => 'single');
        $formats['xml']['support'] = 'forms|items|views';
        
        return $formats;
    }
    
    public static function export_csv($atts) {        
        $form_ids = $atts['ids'];
        if ( empty($form_ids) ) {
            wp_die(__('Please select a form', 'formidable'));
        }
        FrmProEntriesController::csv(reset($form_ids));
    }
    
    // map fields from csv
    public static function map_csv_fields() {
        $name = 'frm_import_file';
        
        if ( !isset($_FILES) || !isset($_FILES[$name]) || empty($_FILES[$name]['name']) || (int)$_FILES[$name]['size'] < 1) {
            return;
        }
        
        $file = $_FILES[$name]['tmp_name'];
        
        // check if file was uploaded
        if ( !is_uploaded_file($file) ) {
            return;
        }
        
        if ( empty($_POST['form_id']) ) {
            $errors = array(__('All Fields are required', 'formidable'));
            FrmXMLController::form($errors);
            return;
        }
        
        //upload
        $media_id = (isset($_POST[$name]) && !empty($_POST[$name]) && is_numeric($_POST[$name])) ? $_POST[$name] : FrmProAppHelper::upload_file($name);
        if ($media_id && !is_wp_error($media_id)) {
            $filename = get_attached_file($media_id);
        }
        
        $row = 1;
        $headers = $example = '';
        $csv_del = FrmAppHelper::get_param('csv_del', ',');
        $form_id = FrmAppHelper::get_param('form_id');
        
        setlocale(LC_ALL, get_locale());
        if (($f = fopen($filename, "r")) !== FALSE) {
            $row = 0;
            while (($data = fgetcsv($f, 100000, $csv_del)) !== FALSE) {
            //while (($raw_data = fgets($f, 100000))){
                $row++;
                if($row == 1)
                    $headers = $data;
                else if($row == 2)
                    $example = $data;
                else
                    continue;
            }
            fclose($f);
        } else {
            $errors = array(__('CSV cannot be opened.', 'formidable'));
            FrmXMLController::form($errors);
            return;
        }
        
        $frm_field = new FrmField();
        $fields = $frm_field->getAll(array('fi.form_id' => (int)$form_id), 'field_order');
        
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/xml/map_csv_fields.php');
    }
    
    public static function import_csv() {
        //Import csv to entries
        $import_count = 250;
        $media_id = FrmAppHelper::get_param('frm_import_file');
        $current_path = get_attached_file($media_id);
        $row = FrmAppHelper::get_param('row');
        $csv_del = FrmAppHelper::get_param('csv_del', ',');
        $form_id = FrmAppHelper::get_param('form_id');
        
        $opts = get_option('frm_import_options');
        
        $left = ($opts && isset($opts[$media_id])) ? ((int)$row - (int)$opts[$media_id]['imported'] - 1) : ($row-1);
        if ( $row < 300 && (!isset($opts[$media_id]) || $opts[$media_id]['imported'] < 300) ) {
            // if the total number of rows is less than 250
            $import_count = ceil($left/2);
        }
        
        if ( $import_count > $left ) {
            $import_count = $left;
        }
        
        $mapping = FrmAppHelper::get_param('data_array');
        $url_vars = "&csv_del=". urlencode($csv_del) ."&form_id={$form_id}&frm_import_file={$media_id}&row={$row}&max={$import_count}";
        
        foreach($mapping as $mkey => $map)
            $url_vars .= "&data_array[$mkey]=$map";
        
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/xml/import_csv.php');
    }
    
    public static function import_csv_entries() {
        if ( !current_user_can('frm_create_entries') ) {
            global $frm_settings;
            wp_die($frm_settings->admin_permission);
        }
        
        extract($_POST);
        
        $opts = get_option('frm_import_options');
        if ( !$opts ) {
            $opts = array();
        }
        
        $current_path = get_attached_file($frm_import_file);
        $start_row = isset($opts[$frm_import_file]) ? $opts[$frm_import_file]['imported'] : 1;
        
        include_once(FrmAppHelper::plugin_path() .'/pro/classes/helpers/FrmProXMLHelper.php');
        $imported = FrmProXMLHelper::import_csv($current_path, $form_id, $data_array, 0, $start_row+1, $csv_del, $max);
        
        $opts[$frm_import_file] = compact('row', 'imported');
        echo $remaining = ((int)$row - (int)$imported);
        
        if (!$remaining)
            unset($opts[$frm_import_file]);
            
        update_option('frm_import_options', $opts);
        
        die();
    }
    
}