<div id="frm_logic_<?php echo $field['id'] ?>_<?php echo $meta_name ?>" class="frm_logic_row">
<select name="field_options[hide_field_<?php echo $field['id'] ?>][]" onchange="frmGetFieldValues(this.value,<?php echo $field['id'] ?>,<?php echo $meta_name ?>,'<?php echo $field['type'] ?>')">
    <option value="">&mdash; <?php _e('Select Field', 'formidable') ?> &mdash;</option>
    <?php 
    $sel = false;
    foreach ($form_fields as $ff){ 
        if($ff->id == $field['id'] or in_array($ff->type, array('captcha', 'divider', 'break', 'file', 'rte', 'date', 'html')) or ($ff->type == 'data' and (!isset($ff->field_options['data_type']) or $ff->field_options['data_type'] == 'data' or $ff->field_options['data_type'] == '')))
            continue;
        
        $selected = ($ff->id == $hide_field) ? ' selected="selected"' : '';
        if(!empty($selected))
            $sel = true;
    ?>
    <option value="<?php echo $ff->id ?>"<?php echo $selected ?>><?php echo FrmAppHelper::truncate($ff->name, 24); ?></option>
    <?php } ?>
</select> 
<?php 
if($hide_field and !$sel){ 
//remove conditional logic if the field doesn't exist ?>
<script type="text/javascript">jQuery(document).ready(function($){ frm_remove_tag('#frm_logic_<?php echo $field['id'] ?>_<?php echo $meta_name ?>'); });</script>
<?php    
}
_e('is', 'formidable'); 
$field['hide_field_cond'][$meta_name] = htmlspecialchars_decode($field['hide_field_cond'][$meta_name]); ?>

<select name="field_options[hide_field_cond_<?php echo $field['id'] ?>][]">
    <option value="==" <?php selected($field['hide_field_cond'][$meta_name], '==') ?>><?php _e('equal to', 'formidable') ?></option>
    <option value="!=" <?php selected($field['hide_field_cond'][$meta_name], '!=') ?>><?php _e('NOT equal to', 'formidable') ?> &nbsp;</option>
    <option value=">" <?php selected($field['hide_field_cond'][$meta_name], '>') ?>><?php _e('greater than', 'formidable') ?></option>
    <option value="<" <?php selected($field['hide_field_cond'][$meta_name], '<') ?>><?php _e('less than', 'formidable') ?></option>
    <option value="LIKE" <?php selected($field['hide_field_cond'][$meta_name], 'LIKE') ?>><?php _e('like', 'formidable') ?></option>
    <option value="not LIKE" <?php selected($field['hide_field_cond'][$meta_name], 'not LIKE') ?>><?php _e('not like', 'formidable') ?> &nbsp;</option>
</select>

<span id="frm_show_selected_values_<?php echo $field['id']; ?>_<?php echo $meta_name ?>">
<?php 
    if ($hide_field and is_numeric($hide_field)){
        global $frm_field;
        $new_field = $frm_field->getOne($hide_field);
        $field_type = $field['type'];
    }
    $current_field_id = $field['id'];
        
    require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/field-values.php');
?>
</span>
<a class="frm_remove_tag frm_icon_font" data-removeid="frm_logic_<?php echo $field['id'] ?>_<?php echo $meta_name ?>"></a>
<a class="frm_add_tag frm_icon_font frm_add_logic_row"></a>
</div>