<table class="form-table">
    <tr class="form-field">
        <td class="frm_left_label"><?php _e('Use Entries from Form', 'formidable'); ?></td>
        <td><?php FrmFormsHelper::forms_dropdown( 'form_id', $post->frm_form_id, true, false, "frmDisplayFormSelected(this.value)"); ?>
        </td>
    </tr>
    <tr>
        <td><?php _e('View Format', 'formidable'); ?></td>
        <td>
            <fieldset>
            <p><label for="all"><input type="radio" value="all" id="all" <?php checked($post->frm_show_count, 'all') ?> name="show_count" onchange="javascript:frm_show_count(this.value)" /> <?php _e('All Entries &mdash; list all entries in the specified form', 'formidable'); ?>.</label></p>
            <p><label for="one"><input type="radio" value="one" id="one" <?php checked($post->frm_show_count, 'one') ?> name="show_count" onchange="javascript:frm_show_count(this.value)" /> <?php _e('Single Entry &mdash; display one entry', 'formidable'); ?>.</label>
            </p>
            <p><label for="dynamic"><input type="radio" value="dynamic" id="dynamic" <?php checked($post->frm_show_count, 'dynamic') ?> name="show_count" onchange="javascript:frm_show_count(this.value)" /> <?php _e('Both (Dynamic) &mdash; list the entries that will link to a single entry page', 'formidable'); ?>.</label></p>
            <p><label for="calendar"><input type="radio" value="calendar" id="calendar" <?php checked($post->frm_show_count, 'calendar') ?> name="show_count" onchange="javascript:frm_show_count(this.value)" /> <?php _e('Calendar &mdash; insert entries into a calendar', 'formidable'); ?>.</label></p>
            </fieldset>
        
            <div id="date_select_container" class="frm_indent_opt <?php echo ($post->frm_show_count == 'calendar') ? '' : 'frm_hidden'; ?>">
                <?php include(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/_calendar_options.php'); ?>
            </div>
        </td>
    </tr>
    <tr class="hide_dyncontent">
        <td><?php _e('Detail Link', 'formidable'); ?> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php printf(__('Example: If parameter name is \'contact\', the url would be like %1$s/selected-page?contact=2. If this entry is linked to a post, the post permalink will be used instead.', 'formidable'), FrmAppHelper::site_url()) ?>" ></span></td>
        <td>
            <?php if( FrmProAppHelper::rewriting_on() && $frmpro_settings->permalinks){ ?>
                <select id="type" name="type">
                    <option value="id" <?php selected($post->frm_type, 'id') ?>><?php _e('ID', 'formidable'); ?></option>
                    <option value="display_key" <?php selected($post->frm_type, 'display_key') ?>><?php _e('Key', 'formidable'); ?></option>
                </select> 
                <p class="description"><?php printf(__('Select the value that will be added onto the page URL. This will create a pretty URL like %1$s/selected-page/entry-key', 'formidable'), FrmAppHelper::site_url()); ?></p>
            <?php }else{ ?>
                <?php _e('Parameter Name', 'formidable'); ?>: 
                <input type="text" id="param" name="param" value="<?php echo esc_attr($post->frm_param) ?>">

                <?php _e('Parameter Value', 'formidable'); ?>:
                <select id="type" name="type">
                    <option value="id" <?php selected($post->frm_type, 'id') ?>><?php _e('ID', 'formidable'); ?></option>
                    <option value="display_key" <?php selected($post->frm_type, 'display_key') ?>><?php _e('Key', 'formidable'); ?></option>
                </select>
            <?php } ?>
        </td>
    </tr>
</table>