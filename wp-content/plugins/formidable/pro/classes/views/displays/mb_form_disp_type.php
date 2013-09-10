<table class="form-table">
    <tr class="form-field">
        <th valign="top" scope="row"><?php _e('Use Entries from Form', 'formidable'); ?></th>
        <td><?php FrmFormsHelper::forms_dropdown( 'form_id', $post->frm_form_id, true, false, "frmDisplayFormSelected(this.value)"); ?>
        </td>
    </tr>
    <tr>
        <th valign="top" scope="row"><?php _e('Display Format', 'formidable'); ?></th>
        <td>
            <fieldset>
            <p><label for="all"><input type="radio" value="all" id="all" <?php checked($post->frm_show_count, 'all') ?> name="show_count" onchange="javascript:frm_show_count(this.value)" /> <?php _e('All Entries &mdash; list all entries in the specified form', 'formidable'); ?>.</label></p>
            <p><label for="one"><input type="radio" value="one" id="one" <?php checked($post->frm_show_count, 'one') ?> name="show_count" onchange="javascript:frm_show_count(this.value)" /> <?php _e('Single Entry &mdash; display one entry', 'formidable'); ?>.</label>
                <span id="entry_select_container"><br/>
                    <?php if (is_numeric($post->frm_form_id))
                        _e('Select Entry', 'formidable'). ': '. FrmEntriesHelper::entries_dropdown($post->frm_form_id, 'entry_id', $post->frm_entry_id, true, __('The first one depending on the Order specified below', 'formidable'));
                    ?>
                </span>
            </p>
            <p><label for="dynamic"><input type="radio" value="dynamic" id="dynamic" <?php checked($post->frm_show_count, 'dynamic') ?> name="show_count" onchange="javascript:frm_show_count(this.value)" /> <?php _e('Both (Dynamic) &mdash; list the entries that will link to a single entry page', 'formidable'); ?>.</label></p>
            <p><label for="calendar"><input type="radio" value="calendar" id="calendar" <?php checked($post->frm_show_count, 'calendar') ?> name="show_count" onchange="javascript:frm_show_count(this.value)" /> <?php _e('Calendar &mdash; insert entries into a calendar', 'formidable'); ?>.</label></p>
            </fieldset>
        
            <div id="date_select_container">
                <?php _e('Date Field', 'formidable'); ?>
                <select id="date_field_id" name="options[date_field_id]">
                    <option value="created_at" <?php selected($post->frm_date_field_id, 'created_at') ?>><?php _e('Entry creation date', 'formidable') ?></option>
                    <option value="updated_at" <?php selected($post->frm_date_field_id, 'updated_at') ?>><?php _e('Entry update date', 'formidable') ?></option>
                    <?php if (is_numeric($post->frm_form_id)) FrmProFieldsHelper::get_field_options($post->frm_form_id, $post->frm_date_field_id, '', "'date'"); ?>
                </select>
                <br/>
                <?php _e('End Date or day count', 'formidable'); ?>
                <select id="date_field_id" name="options[edate_field_id]">
                    <option value=""><?php _e('No multi-day events', 'formidable') ?></option>
                    <option value="created_at" <?php selected($post->frm_edate_field_id, 'created_at') ?>><?php _e('Entry creation date', 'formidable') ?></option>
                    <option value="updated_at" <?php selected($post->frm_edate_field_id, 'updated_at') ?>><?php _e('Entry update date', 'formidable') ?></option>
                    <?php if (is_numeric($post->frm_form_id)) FrmProFieldsHelper::get_field_options($post->frm_form_id, $post->frm_edate_field_id, '', "'date','number'"); ?>
                </select>
            </div>
        </td>
    </tr>
    <tr class="hide_dyncontent">
        <th valign="top" scope="row"><?php _e('Detail Link', 'formidable'); ?> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php printf(__('Example: If parameter name is \'contact\', the url would be like %1$s/selected-page?contact=2. If this entry is linked to a post, the post permalink will be used instead.', 'formidable'), $frm_siteurl) ?>" /></th>
        <td>
            <?php if( FrmProAppHelper::rewriting_on() && $frmpro_settings->permalinks){ ?>
                <select id="type" name="type">
                    <option value="id" <?php selected($post->frm_type, 'id') ?>><?php _e('ID', 'formidable'); ?></option>
                    <option value="display_key" <?php selected($post->frm_type, 'display_key') ?>><?php _e('Key', 'formidable'); ?></option>
                </select> 
                <p class="description"><?php printf(__('Select the value that will be added onto the page URL. This will create a pretty URL like %1$s/selected-page/entry-key', 'formidable'), $frm_siteurl); ?></p>
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