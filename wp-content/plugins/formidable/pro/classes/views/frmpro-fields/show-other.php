<?php
FrmProFieldsHelper::set_field_js($field, (isset($entry_id) ? $entry_id : 0));
if ($field['type'] == 'hidden'){
    if ( is_admin() && !defined('DOING_AJAX') && (!isset($args['action']) || $args['action'] != 'create') && FrmProFieldsHelper::field_on_current_page($field['id']) ) { ?>
<div id="frm_field_<?php $field['id'] ?>_container" class="frm_form_field form-field frm_top_container">
<label class="frm_primary_label"><?php echo $field['name'] ?>:</label> <?php echo $field['value']; ?>
</div>
<?php } 

if (is_array($field['value'])){
    foreach ($field['value'] as $k => $checked){ 
        $checked = apply_filters('frm_hidden_value', $checked, $field); ?>
<input type="hidden" name="<?php echo $field_name ?>[<?php echo $k ?>]" value="<?php echo esc_attr($checked) ?>" <?php do_action('frm_field_input_html', $field) ?> />
<?php   unset($k);
        unset($checked);
    }
}else{ ?>
<input type="hidden" id="field_<?php echo $field['field_key'] ?>" name="<?php echo $field_name ?>" value="<?php echo esc_attr($field['value']) ?>" <?php do_action('frm_field_input_html', $field) ?> />
<?php
} 

}else if ($field['type'] == 'user_id'){
    $user_ID = get_current_user_id();
    $value = ( is_numeric($field['value']) || ( is_admin() && !defined('DOING_AJAX') && $_POST && isset($_POST['item_meta'][$field['id']]) ) || (isset($args['action']) && $args['action'] == 'update') ) ? $field['value'] : ($user_ID ? $user_ID : '' );
    echo '<input type="hidden" id="field_'. $field['field_key'] .'" name="'. $field_name .'" value="'. esc_attr($value) .'"/>'."\n";
    unset($value);
    
}else if ($field['type'] == 'break'){   
    global $frm_vars;

    if (isset($frm_vars['prev_page'][$field['form_id']]) and $frm_vars['prev_page'][$field['form_id']] == $field['field_order']){ 
        echo FrmFieldsHelper::replace_shortcodes($field['custom_html'], $field, array(), $form); ?>
<input type="hidden" name="frm_next_page" class="frm_next_page" id="frm_next_p_<?php echo isset($frm_vars['prev_page'][$field['form_id']]) ? $frm_vars['prev_page'][$field['form_id']] : 0; ?>" value="" />
<?php
    }else{ ?>
<input type="hidden" name="frm_page_order_<?php echo $field['form_id'] ?>" value="<?php echo esc_attr($field['field_order']); ?>" />
<?php    
    } 
}