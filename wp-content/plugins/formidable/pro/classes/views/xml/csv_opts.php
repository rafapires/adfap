<div class="show_csv">
    <p><label class="frm_left_label"><?php _e('CSV Delimiter', 'formidable'); ?></label>
        <input type="text" name="csv_del" value="<?php echo esc_attr($csv_del) ?>" />
    </p>

    <p><label class="frm_left_label"><?php _e('Import Into Form', 'formidable'); ?></label>
        <select name="form_id">
        <?php foreach($forms as $form){
            if ( $form->is_template ) {
                continue;
            }
        ?>
            <option value="<?php echo $form->id ?>"><?php echo ($form->name == '') ? '(no title)' : $form->name ?></option>
        <?php } ?>
        </select>
    </p>
    <p class="howto"><?php _e('Note: Only entries can by imported via CSV.', 'formidable') ?></p>
</div>