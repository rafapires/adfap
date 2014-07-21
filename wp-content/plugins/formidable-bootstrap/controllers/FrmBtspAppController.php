<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

if(class_exists('FrmBtspAppController'))
    return;

class FrmBtspAppController{
    function FrmBtspAppController(){
        add_action('admin_init', 'FrmBtspAppController::include_updater', 1);
        add_action('frm_field_options_form', 'FrmBtspAppController::field_options', 10, 3);
        add_filter('frm_default_field_opts', 'FrmBtspAppController::default_field_opts', 10, 3);
        
        add_action('frm_form_classes', 'FrmBtspAppController::form_class');
        add_action('wp_enqueue_scripts', 'FrmBtspAppController::front_head');
        add_action('wp_footer', 'FrmBtspAppController::enqueue_footer_js', 1);
        add_filter('frm_checkbox_class', 'FrmBtspAppController::inline_class', 10, 2);
        add_filter('frm_radio_class', 'FrmBtspAppController::inline_class', 10, 2);
        add_filter('frm_form_replace_shortcodes', 'FrmBtspAppController::form_html', 10, 2);
        add_filter('frm_before_replace_shortcodes', 'FrmBtspAppController::field_html', 30, 2);
        
        add_filter('frm_field_classes', 'FrmBtspAppController::field_classes', 10, 2);
        add_filter('frm_submit_button_class', 'FrmBtspAppController::submit_button');
        add_filter('frm_back_button_class', 'FrmBtspAppController::back_button');
        
        add_filter('frm_ul_pagination_class', 'FrmBtspAppController::pagination_class');
    }
    
    public static function path(){
        return dirname(dirname(__FILE__));
    }
    
    public static function include_updater(){
        include(self::path() .'/models/FrmBtspUpdate.php');
        $update = new FrmBtspUpdate();
    }
    
    public static function field_options($field, $display, $values){
        $default = array('prepend' => '', 'append' => '');
        if(empty($field['btsp']) or !is_array($field['btsp'])){
            $field['btsp'] = $default;
        }else{
            foreach($default as $k => $v){
                if(!isset($field['btsp'][$k]))
                    $field['btsp'][$k] = $v;
                unset($k);
                unset($v);
            }
        }
        
        include(self::path() .'/views/field_options.php');
    }
    
    public static function default_field_opts($opts, $values, $field){
        $opts['btsp'] = '';
        return $opts;
    }
    
    public static function form_class($form){
        //echo ' form-inline';
    }
    
    public static function front_head(){
        if(is_admin() and !defined('DOING_AJAX'))
            return;
        
        wp_enqueue_style('bootstrap', get_template_directory_uri().'/style.css');
    }
    
    public static function enqueue_footer_js(){
        global $frm_forms_loaded, $frm_vars;
        
        if((!is_array($frm_vars) or !isset($frm_vars['forms_loaded']) or empty($frm_vars['forms_loaded'])) and empty($frm_forms_loaded))
            return;
            
        if(!defined('DOING_AJAX') or (is_array($frm_vars) and isset($frm_vars['preview']) and $frm_vars['preview']))
            wp_enqueue_script('frmbtsp', plugins_url('js/frmbtsp.js', dirname(__FILE__)));
    }
    
    public static function inline_class($class, $field){
        $type = $field['type'];
        if($field['type'] == 'data')
            $type = $field['data_type'];
        
        if(isset($field['align']) and $field['align'] == 'inline')
            $class .= ' '. $type .'-inline';
            
        $class .= ' '. $type;
        
        return $class;
    }
    
    public static function form_html($html, $form){
        $html = str_replace('frm_submit', 'frm_submit form-group', $html);
        return $html;
    }
    
    public static function field_html($html, $field){
        $class = '[required_class] form-group';
        if(isset($field['btsp']) and !empty($field['btsp']) and is_array($field['btsp'])){
            
            if((isset($field['btsp']['prepend']) and !empty($field['btsp']['prepend'])) or (isset($field['btsp']['append']) and !empty($field['btsp']['append']))){
                $class .= ' input-group';
                
                if(!empty($field['btsp']['prepend']))
                    $html = str_replace('[input', '<span class="input-group-addon">'. $field['btsp']['prepend'] .'</span> [input', $html);
                if(!empty($field['btsp']['append']))
                    $html = str_replace('[input]', '[input] <span class="input-group-addon">'. $field['btsp']['append'] .'</span>', $html);
            }
        }
        
        $html = str_replace('frm_primary_label', 'frm_primary_label control-label', $html);
        $html = str_replace('frm_description', 'frm_description help-block', $html);
        
        $html = str_replace('[required_class]', $class, $html);
        return $html;
    }
    
    public static function field_classes($class, $field){
        if(!in_array($field['type'], array('radio', 'checkbox', 'data', 'file', 'scale')))
            $class .= ' form-control';
        
        return $class;
    }
    
    public static function submit_button($class){
        $class[] = 'btn btn-default';
        return $class;
    }
    
    public static function back_button($class){
        $class[] = 'btn';
        return $class;
    }
    
    public static function pagination_class($class){
        if(is_array($class))
            $class[] = 'pagination';
        else
            $class .= ' pagination';
        return $class;
    }
    
}