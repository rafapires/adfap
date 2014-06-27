<?php
if ($field['data_type'] == 'select'){ 
    if(!empty($field['options'])){ 
        if ( $field['read_only'] && (!isset($frm_vars['readonly']) || $frm_vars['readonly'] != 'disabled') && (!current_user_can('frm_edit_entries') || !is_admin() || defined('DOING_AJAX')) ) { ?>
<select disabled="disabled" <?php do_action('frm_field_input_html', $field) ?>>
<?php   }else{ ?>
<select name="<?php echo $field_name ?>" id="field_<?php echo $field['field_key'] ?>" <?php do_action('frm_field_input_html', $field) ?>>
<?php   }

        if ($field['options']){
            foreach ($field['options'] as $opt_key => $opt){ 
$selected = ($field['value'] == $opt_key or in_array($opt_key, (array)$field['value'])) ? ' selected="selected"' : ''; ?>
<option value="<?php echo $opt_key ?>"<?php echo $selected ?>><?php echo ($opt == '') ? ' ' : $opt; ?></option>
<?php       }
        } ?>
</select>
<?php 
    }
    
    if ( (empty($field['options']) || ($field['read_only'] && (!isset($frm_vars['readonly']) || $frm_vars['readonly'] != 'disabled') && (!current_user_can('frm_edit_entries') || !is_admin() || defined('DOING_AJAX')))) && !empty($field['value']) ) { 
        if(is_array($field['value'])){ 
            foreach($field['value'] as $v){ ?>
<input name="<?php echo $field_name ?>" id="field_<?php echo $field['field_key'] ?>" type="hidden" value="<?php echo esc_attr($v) ?>" />
<?php
                unset($v);
            }   
        }else{ ?>
<input name="<?php echo $field_name ?>" id="field_<?php echo $field['field_key'] ?>" type="hidden" value="<?php echo esc_attr($field['value']) ?>" />
<?php   }
    }
}else if ($field['data_type'] == 'data' && is_numeric($field['hide_opt']) && is_numeric($field['form_select'])){ 
    global $frm_entry_meta;
    echo $value = $frm_entry_meta->get_entry_meta_by_field($field['hide_opt'], $field['form_select']); ?>
    <input type="hidden" value="<?php echo esc_attr($value) ?>" name="item_meta[<?php echo $field['id'] ?>]" />
<?php }else if ($field['data_type'] == 'data' && is_numeric($field['hide_field']) && is_numeric($field['form_select'])){
    global $frm_entry_meta; 
    if (isset($_POST) && isset($_POST['item_meta']))
        $observed_field_val = $_POST['item_meta'][$field['hide_field']]; 
    else if(isset($_GET) && isset($_GET['id']))
        $observed_field_val = $frm_entry_meta->get_entry_meta_by_field($_GET['id'], $field['hide_field']);
    
    if(isset($observed_field_val) and is_numeric($observed_field_val)) 
        $value = $frm_entry_meta->get_entry_meta_by_field($observed_field_val, $field['form_select']);
    else
        $value = '';
?>
<p><?php echo $value ?></p>
<input type="hidden" value="<?php echo esc_attr($value) ?>" name="item_meta[<?php echo $field['id'] ?>]" />
<?php }else if ($field['data_type'] == 'data' and !is_array($field['value'])){ ?>
<p><?php echo $field['value']; ?></p>
<input type="hidden" value="<?php echo esc_attr($field['value']) ?>" name="item_meta[<?php echo $field['id'] ?>]" />
<?php }else if ($field['data_type'] == 'text' && is_numeric($field['form_select'])){ 
    global $frm_entry_meta; 
    if (isset($_POST) && isset($_POST['item_meta']))
        $observed_field_val = $_POST['item_meta'][$field['hide_field']]; 
    else if(isset($_GET) && isset($_GET['id']))
        $observed_field_val = $frm_entry_meta->get_entry_meta_by_field($_GET['id'], $field['hide_field']);
    
    if(isset($observed_field_val) and is_numeric($observed_field_val)) 
        $value = $frm_entry_meta->get_entry_meta_by_field($observed_field_val, $field['form_select']);
    else
        $value = '';
?>
<input type="text" value="<?php echo esc_attr($value) ?>" name="item_meta[<?php echo $field['id'] ?>]" />

<?php 
}else if ($field['data_type'] == 'checkbox'){ 
    $checked_values = $field['value'];

    if (!empty($field['options'])){
        foreach ($field['options'] as $opt_key => $opt){
            $checked = ((!is_array($field['value']) && $field['value'] == $opt_key ) || (is_array($field['value']) && in_array($opt_key, $field['value'])))?' checked="true"' : ''; ?>
<div class="<?php echo apply_filters('frm_checkbox_class', 'frm_checkbox', $field, $opt_key)?>"><label for="field_<?php echo $field['id'] ?>-<?php echo $opt_key ?>"><input type="checkbox" name="<?php echo $field_name ?>[]"  id="field_<?php echo $field['id'] ?>-<?php echo $opt_key ?>" value="<?php echo $opt_key ?>" <?php echo $checked ?> <?php do_action('frm_field_input_html', $field) ?> /> <?php echo $opt ?></label></div>
<?php   }
    }else if(!empty($field['value'])){
        foreach((array)$field['value'] as $v){ ?>
<input name="<?php echo $field_name ?>[]" type="hidden" value="<?php echo esc_attr($v) ?>" />
<?php   }
    }//else echo 'There are no options'; 

}else if ($field['data_type'] == 'radio'){
    if(!empty($field['options'])){
        foreach ($field['options'] as $opt_key => $opt){ 
            $checked = ($field['value'] == $opt_key) ? ' checked="checked"' : ''; ?>
<div class="<?php echo apply_filters('frm_radio_class', 'frm_radio', $field, $opt_key) ?>"><label for="field_<?php echo $field['id'] ?>-<?php echo $opt_key ?>"><input type="radio" name="<?php echo $field_name ?>" id="field_<?php echo $field['id'] ?>-<?php echo $opt_key ?>" value="<?php echo $opt_key ?>" <?php echo $checked; ?> <?php do_action('frm_field_input_html', $field) ?> /> <?php echo $opt ?></label></div>
        <?php }
    }//else echo 'There are no options'; ?> 
<?php }