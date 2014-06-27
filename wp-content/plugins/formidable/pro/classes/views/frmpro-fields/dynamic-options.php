<?php if ($form_list){ ?>
<tr><td><?php _e('Import Data from', 'formidable') ?></td>
<td><select name="frm_options_field_<?php echo $field['id'] ?>" id="frm_options_field_<?php echo $field['id'] ?>" onchange="frmGetFieldSelection(this.value,<?php echo $field['id']; ?>)">  
    <option value="">&mdash; <?php _e('Select Form', 'formidable') ?> &mdash;</option>
    <option value="taxonomy" <?php if(!is_object($selected_field)) selected($selected_field, 'taxonomy') ?>><?php _e('Use a Category/Taxonomy', 'formidable') ?></option>
    <?php foreach ($form_list as $form_opts){
    $selected = (is_object($selected_field) and $form_opts->id == $selected_field->form_id) ? ' selected="selected"' : ''; ?>
    <option value="<?php echo $form_opts->id ?>"<?php echo $selected ?>><?php echo FrmAppHelper::truncate($form_opts->name, 30) ?></option>
    <?php } ?>
</select>

<span id="frm_show_selected_fields_<?php echo $field['id'] ?>">
    <?php if (is_object($selected_field)) require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/field-selection.php');
        else if($selected_field == 'taxonomy') include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/data_cat_selected.php');
    ?>
</span>
</td>
</tr>

<tr><td><label><?php _e('Display as', 'formidable') ?></label></td>
    <td><select name="field_options[data_type_<?php echo $field['id'] ?>]" onchange="frmToggleMultSel(this.value,<?php echo $field['id'] ?>)">
        <option value="data"><?php _e('Just show it', 'formidable') ?></option>
        <?php foreach(array('select', 'checkbox', 'radio') as $display_opt){ 
            $selected = (isset($field['data_type']) && $field['data_type'] == $display_opt) ? ' selected="selected"':''; ?>
        <option value="<?php echo $display_opt ?>"<?php echo $selected; ?>><?php echo $frm_field_selection[$display_opt] ?></option>
        <?php } ?>
        </select>
    </td>
</tr>

<tr><td><?php _e('Entries', 'formidable') ?></td> 
    <td><label for="restrict_<?php echo $field['id'] ?>"><input type="checkbox" name="field_options[restrict_<?php echo $field['id'] ?>]" id="restrict_<?php echo $field['id'] ?>" value="1" <?php echo ($field['restrict'] == 1) ? 'checked="checked"' : ''; ?>/> <?php _e('Limit selection choices to those created by the user filling out this form', 'formidable') ?></label></td>
</tr>
<?php } ?>