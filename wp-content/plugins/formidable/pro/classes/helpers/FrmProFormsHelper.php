<?php

class FrmProFormsHelper{
    function FrmProFormsHelper(){
        add_filter('frm_setup_new_form_vars', array(&$this, 'setup_new_vars'));
        add_filter('frm_setup_edit_form_vars', array(&$this, 'setup_edit_vars'));
    }
    
    public static function setup_new_vars($values){
        
        foreach (FrmProFormsHelper::get_default_opts() as $var => $default)
            $values[$var] = FrmAppHelper::get_param($var, $default);
        return $values;
    }
    
    public static function setup_edit_vars($values){
        global $frm_form, $frmpro_settings;
        
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
            'logged_in' => 0, 'logged_in_role' => '', 'editable' => 0, 
            'editable_role' => '', 'open_editable' => 0, 'open_editable_role' => '', 
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
            'plain_text' => 0, 'also_email_to' => array(), 'update_email' => 0,
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
        $html = '[if back_button]<input type="submit" value="[back_label]" name="frm_prev_page" formnovalidate="formnovalidate" class="'. $class .'" [back_hook] />[/if back_button]';
        $html = FrmProFormsController::replace_shortcodes($html, $form);
        if(strpos($html, '[if back_button]') !== false)
            $html = preg_replace('/(\[if\s+back_button\])(.*?)(\[\/if\s+back_button\])/mis', '', $html);
        return $html;
    }
}
