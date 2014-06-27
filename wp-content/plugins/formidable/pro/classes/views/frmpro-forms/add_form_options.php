<tr><td colspan="2"><label for="ajax_submit"><input type="checkbox" name="options[ajax_submit]" id="ajax_submit" value="1"<?php echo ($values['ajax_submit']) ? ' checked="checked"' : ''; ?> /> <?php _e('Submit this form with AJAX', 'formidable') ?></label>
<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('If your form includes a file upload field, ajax submission will not be used.', 'formidable') ?>" ></span>
</td></tr>

<tr>
<td colspan="2"><label for="save_draft"><input type="checkbox" name="options[save_draft]" id="save_draft" value="1"<?php echo ($values['save_draft']) ? ' checked="checked"' : ''; ?> /> <?php _e('Allow logged-in users to save drafts', 'formidable') ?></label>
    <div class="hide_save_draft frm_indent_opt<?php echo $values['save_draft'] ? '' : ' frm_hidden'; ?>">
        <p><label for="draft_msg"><?php _e('Saved Draft Message', 'formidable') ?></label><br/>
        <textarea name="options[draft_msg]" id="draft_msg" cols="50" rows="2" class="frm_long_input"><?php echo FrmAppHelper::esc_textarea($values['draft_msg']); ?></textarea></p>
<!--
<select name="options[save_draft]" id="save_draft" class="hide_save_draft">
    <option value="0"><?php _e('No one', 'formidable') ?></option>
    <option value="1" <?php echo (($values['save_draft'] == 1) ?' selected="selected"':''); ?>><?php _e('Logged-in Users', 'formidable') ?></option>
</select>
-->
    </div>
</td>
</tr>

<tr><td colspan="2">
    <label for="logged_in"><input type="checkbox" name="logged_in" id="logged_in" value="1"<?php echo ($values['logged_in']) ? ' checked="checked"' : ''; ?> /> <?php printf(__('Limit form visibility and submission %1$sto:%2$s', 'formidable'), '<span class="hide_logged_in" '. ($values['logged_in'] ? '' : 'style="visibility:hidden;"') .'>', '</span>') ?></label> 
        
    <select name="options[logged_in_role]" id="logged_in_role" class="hide_logged_in" <?php echo $values['logged_in'] ? '' : 'style="visibility:hidden;"'; ?>>
        <option value=""><?php _e('Logged-in Users', 'formidable') ?></option>
        <?php foreach($frm_vars['editable_roles'] as $role => $details){ 
            $role_name = translate_user_role($details['name'] ); ?>
            <option value="<?php echo esc_attr($role) ?>" <?php echo (($values['logged_in_role'] == $role) ?' selected="selected"':''); ?>><?php echo $role_name ?> </option>
        <?php
                unset($role);
                unset($details);
            } ?> 
    </select>
</td>
</tr>

<tr><td colspan="2"><label for="single_entry"><input type="checkbox" name="options[single_entry]" id="single_entry" value="1"<?php echo ($values['single_entry'])?(' checked="checked"'):(''); ?> /> <?php printf(__('Limit number of form submissions %1$sto one for each:%2$s', 'formidable'), '<span class="hide_single_entry">', '</span>') ?></label>
    <select name="options[single_entry_type]" id="frm_single_entry_type" class="hide_single_entry">
        <option value="user" <?php selected($values['single_entry_type'], 'user') ?>><?php _e('Logged-in User', 'formidable') ?></option>
        <option value="ip" <?php selected($values['single_entry_type'], 'ip') ?>><?php _e('IP Address', 'formidable') ?></option>
        <option value="cookie" <?php selected($values['single_entry_type'], 'cookie') ?>><?php _e('Saved Cookie', 'formidable') ?></option>
    </select>
    
    <p id="frm_cookie_expiration" class="frm_indent_opt <?php echo ($values['single_entry'] && $values['single_entry_type'] == 'cookie') ? '' : 'frm_hidden' ?>">
        <label><?php _e('Cookie Expiration', 'formidable') ?></label>
        <input type="text" name="options[cookie_expiration]" value="<?php echo esc_attr($values['cookie_expiration']) ?>"/> <span class="howto"><?php _e('hours', 'formidable') ?></span>
    </p>
    </td>
</tr>

<tr><td colspan="2">
<label for="editable"><input type="checkbox" name="editable" id="editable" value="1"<?php echo ($values['editable']) ? ' checked="checked"' : ''; ?> /> <?php _e('Allow front-end editing of form submissions', 'formidable') ?></label>

<div class="hide_editable frm_indent_opt">    
    <p><select name="options[editable_role]" id="editable_role">
        <option value=""><?php _e('Logged-in Users', 'formidable') ?></option>
        <?php foreach($frm_vars['editable_roles'] as $role => $details){ 
            $role_name = translate_user_role($details['name'] ); ?>
            <option value="<?php echo esc_attr($role) ?>" <?php echo (($values['editable_role'] == $role) ?' selected="selected"':''); ?>><?php echo $role_name ?> </option>
        <?php   unset($role);
                unset($details);
            } ?> 
    </select>
    <label for="editable_role"><?php _e('can edit their own submission(s)', 'formidable') ?></label></p>

    <?php
    if(isset($values['open_editable']) && empty($values['open_editable']))
        $values['open_editable_role'] = '-1';
    ?>
    <p><select name="options[open_editable_role]" id="open_editable_role">
        <option value="-1"><?php _e('No one', 'formidable') ?></option>
        <option value="" <?php echo ($values['open_editable_role'] == '') ? ' selected="selected"' : ''; ?>><?php _e('Logged-in Users', 'formidable') ?></option>
        <?php foreach($frm_vars['editable_roles'] as $role => $details){ 
            $role_name = translate_user_role($details['name'] ); ?>
            <option value="<?php echo esc_attr($role) ?>" <?php echo ($values['open_editable_role'] == $role) ? ' selected="selected"' : ''; ?>><?php echo $role_name ?> </option>
        <?php   unset($role);
                unset($details);
            } ?> 
    </select> 
    <label for="open_editable_role"><?php _e('can edit responses submitted by other users', 'formidable') ?></label></p>

    <p><label><?php _e('Update Button Text', 'formidable') ?></label>
        <input type="text" name="options[edit_value]" value="<?php echo esc_attr($values['edit_value']); ?>" /></p>
    
        <p><label><?php _e('Action After Edit', 'formidable') ?></label></p>

        <label for="edit_action_message"><input type="radio" name="options[edit_action]" id="edit_action_message" value="message" <?php checked($values['edit_action'], 'message') ?> /> <?php _e('Display a Message', 'formidable') ?> </label>
        
        <label for="edit_action_page"><input type="radio" name="options[edit_action]" id="edit_action_page" value="page" <?php checked($values['edit_action'], 'page') ?> /> <?php _e('Display content from another page', 'formidable') ?> </label>
        
        <label for="edit_action_redirect"><input type="radio" name="options[edit_action]" id="edit_action_redirect" value="redirect" <?php checked($values['edit_action'], 'redirect') ?> /> <?php _e('Redirect to URL', 'formidable') ?> </label>
                
        <p class="frm_indent_opt edit_action_redirect_box edit_action_box" <?php echo ($values['edit_action'] == 'redirect') ? '' : 'style="display:none;"'; ?>>
            <input type="text" name="options[edit_url]" id="edit_url" value="<?php if (isset($values['edit_url'])) echo esc_attr($values['edit_url']); ?>" style="width:98%" placeholder="http://example.com" />
        </p>
        
        <div class="frm_indent_opt edit_action_message_box edit_action_box" <?php echo ($values['edit_action'] == 'message') ? '' : 'style="display:none;"'; ?>>
            <p><textarea name="options[edit_msg]" id="edit_msg" cols="50" rows="2" class="frm_long_input"><?php echo FrmAppHelper::esc_textarea($values['edit_msg']); ?></textarea></p>
        </div>
                
        <p class="frm_indent_opt edit_action_page_box edit_action_box" <?php echo ($values['edit_action'] == 'page') ? '' : 'style="display:none;"'; ?>>
            <label><?php _e('Use Content from Page', 'formidable') ?></label>
            <?php FrmAppHelper::wp_pages_dropdown( 'options[edit_page_id]', $values['edit_page_id'] ) ?>
        </p>
</div>
</td>
</tr>

<?php if (is_multisite()){ ?>
    <?php if (is_super_admin()){ ?>
        <tr><td colspan="2">
        <label for="copy"><input type="checkbox" name="options[copy]" id="copy" value="1" <?php echo ($values['copy'])? ' checked="checked"' : ''; ?> /> <?php _e('Copy this form to other blogs when Formidable Pro is activated', 'formidable') ?></label></td></tr>
    <?php }else if ($values['copy']){ ?>
        <input type="hidden" name="options[copy]" id="copy" value="1" />
    <?php } ?>
<?php } ?>
