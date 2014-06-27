

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

$show_logic = (!empty($notification['conditions']) and count($notification['conditions']) > 2) ? true : false; 

if(!empty($form_fields)){ ?>
    <p class="frm_add_logic_link" id="logic_link_<?php echo $email_key ?>"><a class="frm_add_form_logic" data-emailkey="<?php echo $email_key ?>" id="email_logic_<?php echo $email_key ?>" <?php echo ($show_logic) ? ' style="display:none"' : ''; ?>><?php _e('Use Conditional Logic', 'formidable') ?></a></p>
<?php } ?>
<div class="frm_logic_rows" <?php echo ($show_logic) ? '' : ' style="display:none"'; ?>>
    <h4><?php _e('Conditional Logic', 'formidable') ?></h4>
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
    if ( is_numeric($meta_name) ) {
        FrmProFormsController::include_logic_row( array(
            'meta_name' => $meta_name,
            'condition' => $condition,
            'key'       => $email_key,
            'form_id'   => $values['id'],
        ) );
    }
    
    unset($meta_name);
    unset($condition);
}
            
?>
    </div>
</div>
