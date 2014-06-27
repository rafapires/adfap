<select name="field_options[form_select_<?php echo $current_field_id ?>]">
    <option value="">&mdash; <?php _e('Select Field', 'formidable') ?> &mdash;</option>
    <?php foreach ($fields as $field_option){ ?>
    <option value="<?php echo $field_option->id ?>"<?php if(isset($selected_field) and is_object($selected_field)) selected($selected_field->id, $field_option->id) ?>><?php echo $field_option->name ?></option>
    <?php } ?>
</select>