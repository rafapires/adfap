<p><label class="frm_left_label"><?php _e('Edit Message', 'formidable'); ?> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('The default message seen when after an entry is updated.', 'formidable') ?>" ></span></label>
    <input type="text" id="frm_edit_msg" name="frm_edit_msg" class="frm_with_left_label" value="<?php echo esc_attr($frmpro_settings->edit_msg) ?>" />
</p>

<p><label class="frm_left_label"><?php _e('Update Button', 'formidable'); ?> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('The label on the submit button when editing and entry.', 'formidable') ?>" ></span></label>
    <input type="text" id="frm_update_value" name="frm_update_value" class="frm_with_left_label" value="<?php echo esc_attr($frmpro_settings->update_value) ?>" />
</p>


<p><label class="frm_left_label"><?php _e('Login Message', 'formidable'); ?> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('The message seen when a user who is not logged-in views a form only logged-in users can submit.', 'formidable') ?>" ></span></label>
    <input type="text" id="frm_login_msg" name="frm_login_msg" class="frm_with_left_label" value="<?php echo esc_attr($frm_settings->login_msg) ?>" /></p>

<p><label class="frm_left_label"><?php _e('Previously Submitted Message', 'formidable'); ?> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('The message seen when a user attempts to submit a form for a second time if submissions are limited.', 'formidable') ?>" ></span></label>
        <input type="text" id="frm_already_submitted" name="frm_already_submitted" class="frm_with_left_label" value="<?php echo esc_attr($frmpro_settings->already_submitted) ?>" />
</p>
<div class="clear"></div>

<div class="menu-settings">
<h3 class="frm_no_bg"><?php _e('Miscellaneous', 'formidable')?></h3>

<p><label class="frm_left_label"><?php _e('Keys', 'formidable'); ?> </label>
        <label for="frm_lock_keys"><input type="checkbox" value="1" id="frm_lock_keys" name="frm_lock_keys" <?php checked($frm_settings->lock_keys, 1) ?> />
        <?php _e("Hide field and entry keys to prevent them from being edited. Uncheck this box to edit the saved keys for use in your template.", 'formidable'); ?></label>
</p>

<!--
    <?php _e('Visual Text Editor', 'formidable'); ?>
    <p>
        <label for="frm_rte_off"><input type="checkbox" value="1" id="frm_rte_off" name="frm_rte_off" <?php checked($frmpro_settings->rte_off, 1) ?> />
        <?php _e('Turn off the visual editor when building views.', 'formidable'); ?></label>
    </p>
-->

<p><label class="frm_left_label"><?php _e('Date Format', 'formidable'); ?> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('Change the format of the date used in the date field.', 'formidable') ?>" ></span></label>
        <?php $formats = array('m/d/Y', 'd/m/Y', 'd.m.Y', 'j-m-Y', 'j/m/y', 'Y/m/d', 'Y-m-d'); ?>
        <select name="frm_date_format">
            <?php foreach($formats as $f){ ?>
            <option value="<?php echo esc_attr($f) ?>" <?php selected($frmpro_settings->date_format, $f); ?>><?php echo date($f); ?></option>
            <?php } ?>
        </select>
</p>

<p><label class="frm_left_label"><?php _e('CSV Export Format', 'formidable'); ?> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('If your CSV special characters are not working correctly, try a different formatting option.', 'formidable') ?>" ></span></label>
        <select name="frm_csv_format">
            <option value="UTF-8" <?php selected($frmpro_settings->csv_format, 'UTF-8') ?>>UTF-8</option>
            <option value="ISO-8859-1" <?php selected($frmpro_settings->csv_format, 'ISO-8859-1'); ?>>ISO-8859-1</option>
            <option value="windows-1256" <?php selected($frmpro_settings->csv_format, 'windows-1256'); ?>>windows-1256</option>
            <option value="windows-1251" <?php selected($frmpro_settings->csv_format, 'windows-1251'); ?>>windows-1251</option>
            <option value="macintosh" <?php selected($frmpro_settings->csv_format, 'macintosh'); ?>><?php _e('Macintosh', 'formidable') ?></option>
        </select>
</p>
</div>
<!--
    <td><?php _e('Pretty Permalinks', 'formidable'); ?></td>
    <td>
        <label><input type="checkbox" value="1" id="frm_permalinks" name="frm_permalinks" <?php //checked($frmpro_settings->permalinks, 1) ?>>
        <?php _e('Use pretty permalinks for entry detail links', 'formidable'); ?></label>
        <p class="description">If displaying your data on your site, would you like your permalinks to be pretty? <small>NOTE: This will not work if you are using the WordPress default permalinks.</small></p>
    </td>
-->
<input type="hidden" id="frm_permalinks" name="frm_permalinks" value="0" />
