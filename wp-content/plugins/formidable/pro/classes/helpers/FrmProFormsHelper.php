<?php

class FrmProFormsHelper{
    
    public static function setup_new_vars($values){
        
        foreach ( self::get_default_opts() as $var => $default ) {
            $values[$var] = FrmAppHelper::get_param($var, $default);
        }
        return $values;
    }
    
    public static function setup_edit_vars($values){
        global $frmpro_settings;
        
        $frm_form = new FrmForm();
        $record = $frm_form->getOne($values['id']);
        foreach (array('logged_in' => $record->logged_in, 'editable' => $record->editable) as $var => $default)
            $values[$var] = FrmAppHelper::get_param($var, $default);

        foreach (FrmProFormsHelper::get_default_opts() as $opt => $default){
            if (!isset($values[$opt]))
                $values[$opt] = ($_POST and isset($_POST['options'][$opt])) ? $_POST['options'][$opt] : $default;

            if($opt == 'notification'){
                foreach($values['notification'] as $key => $arr){
                    foreach($default[0] as $k => $v){
                        //migrate into new email format
                        if (!isset($values[$opt][$key][$k]))
                            $values[$opt][$key][$k] = ($_POST and isset($_POST[$opt][$key][$k])) ? $_POST[$opt][$key][$k] : (isset($values[$k]) ? $values[$k] : $v);

                        if($k == 'update_email' and is_array($values[$opt][$key][$k]))
                            $values[$opt][$key][$k] = reset($values[$opt][$key][$k]);
                            
                        unset($k);
                        unset($v);
                    }
                    
                    if ( isset($values[$opt][$key]['also_email_to']) ) {
                        $values[$opt][$key]['also_email_to'] = (array)$values[$opt][$key]['also_email_to'];
                        foreach((array)$values[$opt][$key]['also_email_to'] as $e){
                            if(is_numeric($e)){
                                $values[$opt][$key]['email_to'] .= ', ['. $e .']';
                            }else if(preg_match('/|/', $e)){
                                $email_fields = explode('|', $e);
                                if(!empty($email_fields[0]))
                                    $values[$opt][$key]['email_to'] .= ', ['. $email_fields[0] .' show='. $email_fields[1] .']';
                                unset($email_fields);
                            }
                            unset($e);
                        }
                    }
                    
                    unset($key);
                    unset($arr);
                }
            }
            unset($opt);
            unset($default);
        }
        
        //migrate autoresponder data to notification array
        if(isset($values['auto_responder']) and $values['auto_responder']){
            if(!isset($values['notification']))
                $values['notification'] = array();
            
            $email = array('ar' => true);   
            $upload_defaults = FrmProFormsHelper::get_default_notification_opts();
            foreach($upload_defaults as $opt => $default){
                if(!isset($email[$opt]))
                    $email[$opt] = (isset($values['ar_'. $opt])) ? $values['ar_'. $opt] : $default;
                if($opt == 'email_to' and !empty($email[$opt])){
                    if(is_numeric($email[$opt])){
                        $email[$opt] = '['. $email[$opt] .']';
                    }else if(preg_match('/|/', $email[$opt])){
                        $email_fields = explode('|', $email[$opt]);
                        $email[$opt] = '['. $email_fields[0] .' show='. $email_fields[1] .']';
                        unset($email_fields);
                    }
                }
                
                if($opt == 'reply_to' or $opt == 'reply_to_name'){
                    if(!empty($email[$opt]) and !is_numeric($email[$opt])){
                        $email['cust_'.$opt] = $email[$opt];
                        $email[$opt] = 'custom';
                    }
                }
                    
                unset($opt);
                unset($default);
            }
            
            $values['notification'][] = $email;
            unset($email);
        }

        return $values;
    }
    
    public static function get_default_opts(){
        global $frmpro_settings;
        
        return array(
            'edit_value' => $frmpro_settings->update_value, 'edit_msg' => $frmpro_settings->edit_msg,
            'edit_action' => 'message', 'edit_url' => '', 'edit_page_id' => 0,
            'logged_in' => 0, 'logged_in_role' => '', 'editable' => 0, 'save_draft' => 0,
            'draft_msg' => __('Your draft has been saved.', 'formidable'),
            'editable_role' => '', 'open_editable_role' => '-1', 
            'copy' => 0, 'single_entry' => 0, 'single_entry_type' => 'user', 
            'success_page_id' => '', 'success_url' => '', 'ajax_submit' => 0, 
            'create_post' => 0, 'cookie_expiration' => 8000,
            'post_type' => 'post', 'post_category' => array(), 'post_content' => '', 
            'post_excerpt' => '', 'post_title' => '', 'post_name' => '', 'post_date' => '',
            'post_status' => '', 'post_custom_fields' => array(), 'post_password' => '',
            'notification' => array(0 => FrmProFormsHelper::get_default_notification_opts())
        );
        
        /*
        Old emailer values for reference
        'auto_responder' => 0, 
        'ar_email_to' => '', 'ar_reply_to' => get_option('admin_email'), 'ar_reply_to_name' => get_option('blogname'),
        'ar_plain_text' => 0, 'ar_update_email' => 0,
        'ar_email_subject' => '', 'ar_email_message' => '', 
        */
    }
    
    public static function get_default_notification_opts(){
        global $frm_settings;
        
        return array(
            'email_to' => $frm_settings->email_to, 'reply_to' => '', 'reply_to_name' => '',
            'cust_reply_to' => '', 'cust_reply_to_name' => '',
            'plain_text' => 0, 'update_email' => 0,
            'email_subject' => '', 'email_message' => '[default-message]', 
            'inc_user_info' => 0, //'ar' => 0,
            'conditions' => array('send_stop' => '', 'any_all' => '')
        );
    }
    
    public static function get_taxonomy_count($taxonomy, $post_categories, $tax_count=0){
        if(isset($post_categories[$taxonomy . $tax_count])){
            $tax_count++;
            $tax_count = FrmProFormsHelper::get_taxonomy_count($taxonomy, $post_categories, $tax_count);
        }
        return $tax_count;
    }
    
    public static function going_to_prev($form_id){
        $back = false;
        if($_POST and isset($_POST['frm_next_page']) and $_POST['frm_next_page'] != ''){
            $prev_page = FrmAppHelper::get_param('frm_page_order_'. $form_id, false);
            if(!$prev_page or ($_POST['frm_next_page'] < $prev_page))
                $back = true; //no errors if going back a page
        }
        return $back;
    }
    
    public static function get_prev_button($form, $class=''){
        $html = '[if back_button]<input type="submit" value="[back_label]" name="frm_prev_page" formnovalidate="formnovalidate" class="frm_prev_page '. $class .'" [back_hook] />[/if back_button]';
        $html = FrmProFormsController::replace_shortcodes($html, $form);
        if(strpos($html, '[if back_button]') !== false)
            $html = preg_replace('/(\[if\s+back_button\])(.*?)(\[\/if\s+back_button\])/mis', '', $html);
        return $html;
    }
    
    // check if this entry is currently being saved as a draft
    public static function saving_draft($form_id){
        $saving = ($_POST and isset($_POST['frm_saving_draft']) and $_POST['frm_saving_draft'] == '1' and is_user_logged_in()) ? true : false;
        return $saving;
    }
    
    public static function get_draft_button($form, $class='', $html=false){
        if(!$html)
            $html = '[if save_draft]<input type="submit" value="[draft_label]" name="frm_save_draft" formnovalidate="formnovalidate" class="frm_save_draft '. $class .'" [draft_hook] />[/if save_draft]';
        
        $html = FrmProFormsController::replace_shortcodes($html, $form);
        if(strpos($html, '[if save_draft]') !== false)
            $html = preg_replace('/(\[if\s+save_draft\])(.*?)(\[\/if\s+save_draft\])/mis', '', $html);
        return $html;
    }
    
    public static function get_draft_link($form){
        $html = self::get_draft_button($form, '', FrmFormsHelper::get_draft_link());
        return $html;
    }
    
    public static function has_field($type, $form_id, $single = true) {
        global $wpdb;
        
        $frm_field = new FrmField();
        
        if ( $single ) {
            $included = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}frm_fields WHERE form_id=%d AND type=%s", $form_id, $type));
            if ( $included ) {
                $included = $frm_field->getOne($included);
            }
        } else {
            $included = $frm_field->getAll( array('type' => $type, 'fi.form_id' => $form_id) );
        }
        
        return $included;
    }
    
    public static function &post_type($form) {
        if ( is_numeric($form) ) {
            $frm_form = new FrmForm();
            $form = $frm_form->getOne($form);
        }
        
        if ( is_object($form) ) {
            $type = isset($form->options['post_type']) ? $form->options['post_type'] : 'post';
        } else {
            $form = (array) $form;
            $type = isset($form['post_type']) ? $form['post_type'] : 'post';
        }
        
        return $type;
    }
    
    public static function hex2rgb($hex) {
        $hex = str_replace('#', '', $hex);
        
        if ( strlen($hex) == 3 ) {
            $r = hexdec( substr($hex,0,1).substr($hex,0,1) );
            $g = hexdec( substr($hex,1,1).substr($hex,1,1) );
            $b = hexdec( substr($hex,2,1).substr($hex,2,1) );
        } else {
            $r = hexdec( substr($hex,0,2) );
            $g = hexdec( substr($hex,2,2) );
            $b = hexdec( substr($hex,4,2) );
        }
        $rgb = array($r, $g, $b);
        return implode(',', $rgb); // returns the rgb values separated by commas
        //return $rgb; // returns an array with the rgb values
    }

}
