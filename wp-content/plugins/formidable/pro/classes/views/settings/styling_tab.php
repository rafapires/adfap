<table class="form-table">
    <tr class="form-field">
        <td width="250px">
            <?php include(FrmAppHelper::plugin_path() .'/pro/classes/views/settings/formroller.php'); ?>
        </td>
        <td class="frm_white_bg frm_border_light">
            <div class="frm_forms with_frm_style">
            <div class="frm_form_fields frm_sample_form">
            <fieldset>
              
            <div class="frm_error_style"> 
                <strong><?php echo __('SAMPLE:', 'formidable') .'</strong> '. $frm_settings->invalid_msg ?>
            </div>
            
            <div id="message" class="frm_message"><strong><?php echo __('SAMPLE:', 'formidable') .'</strong> '. $frm_settings->success_msg ?></div>
            <?php $pos_class = ($frmpro_settings->position == 'none') ? 'frm_top_container' : 'frm_'. $frmpro_settings->position .'_container' ?>
            <div class="frm_form_field frm_first_half form-field <?php echo $pos_class ?>">
            <label class="frm_primary_label"><?php _e('Text field', 'formidable') ?> <span class="frm_required">*</span></label>   
            <input type="text" value="<?php echo esc_attr( __('This is sample text', 'formidable')) ?>"/>
            <div class="frm_description"><?php _e('A field with a description', 'formidable') ?></div>
            </div>
            
            <div class="frm_form_field frm_last_half form-field frm_focus_field <?php echo $pos_class ?>">
            <label class="frm_primary_label"><?php _e('Text field in active state', 'formidable') ?> <span class="frm_required">*</span></label>   
            <input type="text" value="<?php echo esc_attr( __('Active state will be seen when the field is clicked', 'formidable')) ?>" />
            </div>
            
            <div class="frm_form_field form-field frm_blank_field <?php echo $pos_class ?>">
            <label class="frm_primary_label"><?php _e('Text field with error', 'formidable') ?> <span class="frm_required">*</span></label>   
            <input type="text" value="<?php echo esc_attr( __('This is sample text', 'formidable')) ?>"/>
            <div class="frm_error"><?php echo $frm_settings->blank_msg ?></div>
            </div>
            
            <div class="frm_form_field form-field frm_first_half <?php echo $pos_class ?>">
            <label class="frm_primary_label"><?php _e('File Upload', 'formidable') ?></label>   
            <input type="file"/>
            </div>

            <div class="frm_form_field form-field frm_last_half <?php echo $pos_class ?>">
            <label class="frm_primary_label"><?php _e('Drop-down Select', 'formidable') ?></label>   
            <select>
                <option value=""></option>
                <option value=""><?php _e('An Option', 'formidable') ?></option>
            </select>
            </div>
            
            <div class="frm_form_field form-field frm_first_half <?php echo $pos_class ?>">
                <label class="frm_primary_label"><?php _e('Radio Buttons', 'formidable') ?></label>
                <div class="frm_radio"><input type="radio" /><label><?php _e('Option 1', 'formidable') ?></label></div>
                <div class="frm_radio"><input type="radio" /><label><?php _e('Option 2', 'formidable') ?></label></div>
            </div>
            
            <div class="frm_form_field form-field frm_last_half <?php echo $pos_class ?>">
                <label class="frm_primary_label"><?php _e('Check Boxes', 'formidable') ?></label>
                <div class="frm_checkbox"><label><input type="checkbox" /><?php _e('Option 1', 'formidable') ?></label></div>
                <div class="frm_checkbox"><label><input type="checkbox" /><?php _e('Option 2', 'formidable') ?></label></div>
            </div>
            
            <div class="frm_form_field form-field <?php echo $pos_class ?>">
                <label class="frm_primary_label"><?php _e('Text Area', 'formidable') ?></label>   
                <textarea></textarea>
                <div class="frm_description"><?php _e('Another field with a description', 'formidable') ?></div>
            </div>
            
            <div id="datepicker_sample" style="margin-bottom:<?php echo $frmpro_settings->field_margin ?>;"></div>
            
            </fieldset>
            </div>
            
            <div class="frm_submit">
            <input type="submit" disabled="disabled" style="opacity:1;" value="<?php echo esc_attr( __('Submit', 'formidable')) ?>" />
            </div>
            </div>
        </td>    
    </tr> 
</table>