<tr valign="top">
<td colspan="2">
    <p><label><?php _e('Subject', 'formidable') ?></label><br/>
    <input type="text" name="notification[<?php echo $email_key ?>][email_subject]" class="frm_not_email_subject frm_long_input" id="email_subject_<?php echo $email_key ?>" size="55" value="<?php echo esc_attr($notification['email_subject']); ?>" /></p>
        
    <p><label><?php _e('Message', 'formidable') ?> </label><br/>
    <textarea name="notification[<?php echo $email_key ?>][email_message]" class="frm_not_email_message frm_long_input" id="email_message_<?php echo $email_key ?>" cols="50" rows="5"><?php echo FrmAppHelper::esc_textarea($notification['email_message']) ?></textarea></p>

    <h4><?php _e('Options', 'formidable') ?> </h4>
    <input type="checkbox" name="notification[<?php echo $email_key ?>][inc_user_info]" class="frm_not_inc_user_info" id="inc_user_info_<?php echo $email_key ?>" value="1" <?php checked($notification['inc_user_info'], 1); ?> /> <?php _e('Append IP Address, Browser, and Referring URL to message', 'formidable') ?>
        
    <p><input type="checkbox" name="notification[<?php echo $email_key ?>][plain_text]" value="1" <?php checked($notification['plain_text'], 1); ?> /> <?php _e('Send Emails in Plain Text', 'formidable') ?></p>

    <p><?php _e('Send this notification when entries are', 'formidable'); ?>
        <select name="notification[<?php echo $email_key ?>][update_email]">
            <option value="0"><?php _e('created', 'formidable') ?></option>
            <option value="2" <?php selected($notification['update_email'], 2); ?>><?php _e('updated', 'formidable') ?></option>
            <option value="1" <?php selected($notification['update_email'], 1); ?>><?php _e('created or updated', 'formidable') ?></option>
        </select>
    </p>
    <?php if(isset($notification['ar'])){ ?>
    <input type="hidden" name="notification[<?php echo $email_key ?>][ar]" value="1" <?php checked($notification['ar'], 1); ?> />
    <?php } 
    
    $form_fields = array();
    foreach($values['fields'] as $f){
        if(in_array($f['type'], array('select','radio','checkbox','10radio','scale','data')) or ($f['type'] == 'data' and isset($fo['data_type']) and in_array($fo['data_type'], array('select','radio','checkbox'))))
            $form_fields[] = $f;
        unset($f);
    }
    
    $show_logic = (!empty($notification['conditions']) and count($notification['conditions']) > 2) ? true : false; 
    
    if(!empty($form_fields)){ ?>
    <a href="javascript:frmToggleLogic('email_logic_<?php echo $email_key ?>')" class="button-secondary" id="email_logic_<?php echo $email_key ?>" <?php echo ($show_logic) ? ' style="display:none"' : ''; ?>><?php _e('Use Conditional Logic', 'formidable') ?></a>
    <?php }else{ ?>
    <p class="howto"><?php _e('Add a radio, dropdown, or checkbox field to your form to enable conditional logic.', 'formidable') ?>    
    <?php } ?>
    <div class="frm_logic_rows tagchecklist" <?php echo ($show_logic) ? '' : ' style="display:none"'; ?>>
        <div id="frm_logic_row_<?php echo $email_key ?>">
        <select name="notification[<?php echo $email_key ?>][conditions][send_stop]">
            <option value="send" <?php selected($notification['conditions']['send_stop'], 'send') ?>><?php _e('Send', 'formidable') ?></option>
            <option value="stop" <?php selected($notification['conditions']['send_stop'], 'stop') ?>><?php _e('Stop', 'formidable') ?></option>
        </select>
        <?php _e('this notification if', 'formidable'); ?>
        <select name="notification[<?php echo $email_key ?>][conditions][any_all]">
            <option value="any" <?php selected($notification['conditions']['any_all'], 'any') ?>><?php _e('any', 'formidable') ?></option>
            <option value="all" <?php selected($notification['conditions']['any_all'], 'all') ?>><?php _e('all', 'formidable') ?></option>
        </select>
        <?php _e('of the following match', 'formidable') ?>:
            
        <?php 

        foreach($notification['conditions'] as $meta_name => $condition){
            if(is_numeric($meta_name))
                include(FRMPRO_VIEWS_PATH .'/frmpro-forms/_logic_row.php');
            unset($meta_name);
            unset($condition);
        }
            
        ?>
        </div>
        <p><a class="button" href="javascript:frmAddFormLogicRow(<?php echo $email_key ?>,<?php echo $values['id'] ?>);">+ <?php _e('Add', 'formidable') ?></a></p>
    </div>
</td>
</tr>
