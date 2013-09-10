<?php 
if ($field['type'] == 'hidden'){
    global $frmpro_field;
    $frm_action = (isset($_GET) and isset($_GET['frm_action'])) ? 'frm_action' : 'action';
    if (is_admin() and (!isset($_GET[$frm_action]) or $_GET[$frm_action] != 'new') and $frmpro_field->on_current_page($field['id'])){ ?>
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
    global $user_ID;
    echo '<input type="hidden" id="field_'. $field['field_key'] .'" name="'. $field_name .'" value="'. esc_attr((is_numeric($field['value'])) ? $field['value'] : ($user_ID ? $user_ID : '' )) .'"/>'."\n";

}else if ($field['type'] == 'break'){   
    global $frm_prev_page;

    if (isset($frm_prev_page[$field['form_id']]) and $frm_prev_page[$field['form_id']] == $field['field_order']){ 
        echo FrmFieldsHelper::replace_shortcodes($field['custom_html'], $field, array(), $form); ?>
<input type="hidden" name="frm_next_page" class="frm_next_page" id="frm_next_p_<?php echo isset($frm_prev_page[$field['form_id']]) ? $frm_prev_page[$field['form_id']] : 0; ?>" value="" />
<?php
    }else{ ?>
<input type="hidden" name="frm_page_order_<?php echo $field['form_id'] ?>" value="<?php echo esc_attr($field['field_order']); ?>" />
<?php    
    } 
}