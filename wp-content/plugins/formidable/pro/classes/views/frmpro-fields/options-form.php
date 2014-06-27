<?php if($display['type'] == 'radio' or $display['type'] == 'checkbox'){ ?>
<tr><td><label><?php _e('Alignment', 'formidable') ?></label></td>
    <td>
        <select name="field_options[align_<?php echo $field['id'] ?>]">
            <option value="block" <?php selected($field['align'], 'block') ?>><?php _e('Multiple Rows', 'formidable'); ?></option>
            <option value="inline" <?php selected($field['align'], 'inline') ?>><?php _e('Single Row', 'formidable'); ?></option>
        </select>
    </td>
</tr>
<?php } 

if(in_array($display['type'], array('radio', 'checkbox', 'select')) and (!isset($field['post_field']) or ($field['post_field'] != 'post_category' and $field['post_field'] != 'post_status'))){ ?>
<tr><td><label><?php _e('Separate values', 'formidable'); ?></label> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('Add a separate value to use for calculations, email routing, saving to the database, and many other uses. The option values are saved while the option labels are shown in the form.', 'formidable') ?>" ></span></td>
    <td><label for="separate_value_<?php echo $field['id'] ?>"><input type="checkbox" name="field_options[separate_value_<?php echo $field['id'] ?>]" id="separate_value_<?php echo $field['id'] ?>" value="1" <?php checked($field['separate_value'], 1) ?> onclick="frmSeparateValue(<?php echo $field['id'] ?>)" /> <?php _e('Use separate values', 'formidable'); ?></label></td>
</tr>
<?php 
}

if ( in_array($field['type'], array('radio', 'checkbox', 'select', 'scale', 'user_id', 'data', 'file')) ) { ?>
<tr><td><?php _e('Dynamic Default Value', 'formidable'); ?> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('If your radio, checkbox, dropdown, or user ID field needs a dynamic default value like [get param=whatever], insert it in the field options. If using a GET or POST value, it must match one of the options in the field in order for that option to be selected. Data from entries fields require the ID of the linked entry.', 'formidable') ?>" ></span></td>
    <td><input type="text" name="field_options[dyn_default_value_<?php echo $field['id'] ?>]" id="dyn_default_value_<?php echo $field['id'] ?>" value="<?php echo esc_attr($field['dyn_default_value']) ?>" class="dyn_default_value frm_long_input" /></td>
</tr>
<?php 
}

if ($field['type'] == 'data'){
        global $frm_field;
        $frm_form = new FrmForm();
        $form_list = $frm_form->getAll(array('status' => 'published', 'is_template' => 0), 'name');
        $selected_field = '';
        $current_field_id = $field['id'];
        if (isset($field['form_select']) && is_numeric($field['form_select'])){
            $selected_field = $frm_field->getOne($field['form_select']);
            if ( $selected_field ) {
                $fields = $frm_field->getAll(array('fi.form_id' => $selected_field->form_id));
            } else {
                $selected_field = '';
            }
        }else if(isset($field['form_select'])){
            $selected_field = $field['form_select'];
        }
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/dynamic-options.php');
        unset($current_field_id);
}

if ($display['type'] == 'select' or $field['type'] == 'data'){ ?>
<tr id="frm_multiple_cont_<?php echo $field['id'] ?>" <?php echo ($field['type'] == 'data' and (!isset($field['data_type']) or $field['data_type'] != 'select')) ? ' class="frm_hidden"' : ''; ?>>
    <td><?php _e('Multiple select', 'formidable') ?></label></td>
    <td><label for="multiple_<?php echo $field['id'] ?>"><input type="checkbox" name="field_options[multiple_<?php echo $field['id'] ?>]" id="multiple_<?php echo $field['id'] ?>" value="1" <?php echo (isset($field['multiple']) and $field['multiple'])? 'checked="checked"':''; ?> />
    <?php _e('enable multiselect', 'formidable') ?></label>
    <div style="padding-top:4px;">
    <label for="autocom_<?php echo $field['id'] ?>"><input type="checkbox" name="field_options[autocom_<?php echo $field['id'] ?>]" id="autocom_<?php echo $field['id'] ?>" value="1" <?php echo (isset($field['autocom']) and $field['autocom'])? 'checked="checked"':''; ?> /> 
    <?php _e('enable autocomplete', 'formidable') ?></label>
    </div>
    </td>
</tr>
<?php
}elseif ($display['type'] == 'divider'){ ?>
<tr><td colspan="2"><label for="slide_<?php echo $field['id'] ?>"><input type="checkbox" name="field_options[slide_<?php echo $field['id'] ?>]" id="slide_<?php echo $field['id'] ?>" value="1" <?php echo ($field['slide']) ? 'checked="checked"' : ''; ?> /> <?php _e('Make this section collapsible', 'formidable') ?></label></td>
</tr>
<?php
}else if($field['type'] == 'date'){ ?>
    <tr><td><label><?php _e('Calendar Localization', 'formidable') ?></label></td>
    <td>    
    <select name="field_options[locale_<?php echo $field['id'] ?>]">
        <?php foreach($locales as $locale_key => $locale){
            $selected = (isset($field['locale']) && $field['locale'] == $locale_key)? ' selected="selected"':''; ?>
            <option value="<?php echo $locale_key ?>"<?php echo $selected; ?>><?php echo $locale ?></option>
        <?php } ?>
    </select>
    </td>
    </tr>
<tr><td><label><?php _e('Year Range', 'formidable') ?></label> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('Use four digit years or +/- years to make it dynamic. For example, use -5 for the start year and +5 for the end year.', 'formidable') ?>" ></span>
    </td>   
    <td>
    <span><?php _e('Start Year', 'formidable') ?></span>
    <input type="text" name="field_options[start_year_<?php echo $field['id'] ?>]" value="<?php echo isset($field['start_year']) ? $field['start_year'] : ''; ?>" size="4"/>
    
    <span><?php _e('End Year', 'formidable') ?></span> 
    <input type="text" name="field_options[end_year_<?php echo $field['id'] ?>]" value="<?php echo isset($field['end_year']) ? $field['end_year'] : ''; ?>" size="4"/>
    </td>
</tr>
<?php }else if($field['type'] == 'time'){ ?>
<tr><td><label><?php _e('Clock Settings', 'formidable') ?></label></td>
    <td>
        <select name="field_options[clock_<?php echo $field['id'] ?>]">
            <option value="12" <?php selected($field['clock'], 12) ?>>12</option>
            <option value="24" <?php selected($field['clock'], 24) ?>>24</option>
        </select> <span class="howto" style="padding-right:10px;"><?php _e('hour clock', 'formidable') ?></span>

        <input type="text" name="field_options[step_<?php echo $field['id'] ?>]" value="<?php echo esc_attr($field['step']); ?>" size="3" />
        <span class="howto" style="padding-right:10px;"><?php _e('minute step', 'formidable') ?></span> 
        
        <input type="text" name="field_options[start_time_<?php echo $field['id'] ?>]" id="start_time_<?php echo $field['id'] ?>" value="<?php echo esc_attr($field['start_time']) ?>" size="5"/>
        <span class="howto" style="padding-right:10px;"><?php _e('start time', 'formidable') ?></span> 
        
        <input type="text" name="field_options[end_time_<?php echo $field['id'] ?>]" id="end_time_<?php echo $field['id'] ?>" value="<?php echo esc_attr($field['end_time']) ?>" size="5"/>
        <span class="howto"><?php _e('end time', 'formidable') ?></span>
    </td>
</tr>
<?php }else if($field['type'] == 'file'){ ?>
    <tr><td><label><?php _e('Multiple files', 'formidable') ?></label></td>
        <td><label for="multiple_<?php echo $field['id'] ?>"><input type="checkbox" name="field_options[multiple_<?php echo $field['id'] ?>]" id="multiple_<?php echo $field['id'] ?>" value="1" <?php echo (isset($field['multiple']) and $field['multiple'])? 'checked="checked"':''; ?> /> 
        <?php _e('allow multiple files to be uploaded to this field', 'formidable') ?></label></td>
    </tr>
    <tr><td><label><?php _e('Email Attachment', 'formidable') ?></label></td>
        <td><label for="attach_<?php echo $field['id'] ?>"><input type="checkbox" id="attach_<?php echo $field['id'] ?>" name="field_options[attach_<?php echo $field['id'] ?>]" value="1" <?php echo (isset($field['attach']) and $field['attach'])? 'checked="checked"':''; ?> /> <?php _e('attach this file to the email notification', 'formidable') ?></label></td>
    </tr>
    <?php if($mimes){ ?>
    <tr><td><label><?php _e('Allowed file types', 'formidable') ?></label></td>
        <td>
            <label for="restrict_<?php echo $field['id'] ?>_0"><input type="radio" name="field_options[restrict_<?php echo $field['id'] ?>]" id="restrict_<?php echo $field['id'] ?>_0" value="0" <?php FrmAppHelper::checked($field['restrict'], 0); ?> onclick="frm_show_div('restrict_box_<?php echo $field['id'] ?>',this.value,1,'.')" /> <?php _e('All types', 'formidable') ?></label>
            <label for="restrict_<?php echo $field['id'] ?>_1"><input type="radio" name="field_options[restrict_<?php echo $field['id'] ?>]" id="restrict_<?php echo $field['id'] ?>_1" value="1" <?php FrmAppHelper::checked($field['restrict'], 1); ?> onclick="frm_show_div('restrict_box_<?php echo $field['id'] ?>',this.value,1,'.')" /> <?php _e('Specify allowed types', 'formidable') ?></label>
            <label for="check_all_ftypes_<?php echo $field['id'] ?>" class="restrict_box_<?php echo $field['id'] ?> <?php echo ($field['restrict'] == 1) ? '' : 'frm_hidden'; ?>"><input type="checkbox" id="check_all_ftypes_<?php echo $field['id'] ?>" onclick="frmCheckAll(this.checked,'field_options[ftypes_<?php echo $field['id'] ?>]')" /> <span class="howto"><?php _e('Check All', 'formidable') ?></span></label>
            
            <div class="restrict_box_<?php echo $field['id'] . ($field['restrict'] == 1 ? '' : ' frm_hidden'); ?>">
            <div class="frm_field_opts_list" style="width:100%;">
                <div class="alignleft" style="width:33% !important">
                    <?php 
                    $mcount = count($mimes);
                    $third = ceil($mcount/3);
                    $c = 0;
                    if ( !isset($field['ftypes']) ) {
                        $field['ftypes'] = array();
                    }
                    
                    foreach($mimes as $ext_preg => $mime){ 
                        if($c == $third or (($c/2) == $third)){ ?>
                    </div>
                    <div class="alignleft" style="width:33% !important">
                    <?php } ?>
                    <label for="ftypes_<?php echo $field['id'] ?>_<?php echo sanitize_key($ext_preg) ?>"><input type="checkbox" id="ftypes_<?php echo $field['id'] ?>_<?php echo sanitize_key($ext_preg) ?>" name="field_options[ftypes_<?php echo $field['id'] ?>][<?php echo $ext_preg ?>]" value="<?php echo $mime ?>" <?php FrmAppHelper::checked($field['ftypes'], $mime); ?> /> <span class="howto"><?php echo str_replace('|', ', ', $ext_preg); ?></span></label><br/>
                    <?php 
                        $c++;
                        unset($ext_preg);
                        unset($mime);
                    } 
                    unset($c);
                    unset($mcount);
                    unset($third);
                    ?>
                </div>
            </div>
            </div>
        </td>
    </tr>
    <?php } ?>
<?php }else if($field['type'] == 'number' and $frm_settings->use_html){ ?>
    <tr><td width="150px"><label><?php _e('Number Range', 'formidable') ?> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('Browsers that support the HTML5 number field require a number range to determine the numbers seen when clicking the arrows next to the field.', 'formidable') ?>" ></span></label></td>
        <td><input type="text" name="field_options[minnum_<?php echo $field['id'] ?>]" value="<?php echo esc_attr($field['minnum']); ?>" size="5" /> <span class="howto"><?php echo _e('minimum', 'formidable') ?></span>
        <input type="text" name="field_options[maxnum_<?php echo $field['id'] ?>]" value="<?php echo esc_attr($field['maxnum']); ?>" size="5" /> <span class="howto"><?php _e('maximum', 'formidable') ?></span>
        <input type="text" name="field_options[step_<?php echo $field['id'] ?>]" value="<?php echo esc_attr($field['step']); ?>" size="5" /> <span class="howto"><?php _e('step', 'formidable') ?></span></td>
    </tr>
<?php }else if($field['type'] == 'scale'){ ?>
    <tr><td><label><?php _e('Range', 'formidable') ?></label></td>
        <td>
            <select name="field_options[minnum_<?php echo $field['id'] ?>]">
                <?php for( $i=0; $i<10; $i++ ){ 
                    $selected = (isset($field['minnum']) && $field['minnum'] == $i)? ' selected="selected"':''; ?>
                <option value="<?php echo $i ?>"<?php echo $selected; ?>><?php echo $i ?></option>
                <?php } ?>
            </select> <?php _e('to', 'formidable') ?>
            <select name="field_options[maxnum_<?php echo $field['id'] ?>]">
                <?php for( $i=1; $i<=20; $i++ ){ 
                    $selected = (isset($field['maxnum']) && $field['maxnum'] == $i)? ' selected="selected"':''; ?>
                <option value="<?php echo $i ?>"<?php echo $selected; ?>><?php echo $i ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr><td><label><?php _e('Stars', 'formidable') ?></label></td>
        <td><label for="star_<?php echo $field['id'] ?>"><input type="checkbox" value="1" name="field_options[star_<?php echo $field['id'] ?>]" id="star_<?php echo $field['id'] ?>" <?php checked((isset($field['star']) ? $field['star'] : 0), 1) ?> />
            <?php _e('Show options as stars', 'formidable') ?>
        </td>
    </tr>
<?php } else if ( $field['type'] == 'rte' ) { ?>
<tr><td><?php _e('Rich Text Editor', 'formidable') ?></td>
<td>
    <select name="field_options[rte_<?php echo $field['id'] ?>]">
        <option value="nicedit" <?php selected($field['rte'], 'nicedit') ?>>NicEdit</option>
        <option value="mce" <?php selected($field['rte'], 'mce') ?>>Tiny MCE</option>
    </select>
</td>
</tr>
<?php }else if($field['type'] == 'html'){ ?>
<tr><td colspan="2"><?php _e('Content', 'formidable') ?><br/>
<textarea name="field_options[description_<?php echo $field['id'] ?>]" style="width:98%;" rows="8"><?php 
if(isset($field['stop_filter']) and $field['stop_filter'])
    echo $field['description'];
else
    echo FrmAppHelper::esc_textarea($field['description']);
?></textarea>
</td>
</tr>
<?php }else if($field['type'] == 'form'){ ?>
<tr><td><?php _e('Insert Form', 'formidable') ?></td>    
<td><?php FrmFormsHelper::forms_dropdown('field_options[form_select_'. $field['id'] .']', $field['form_select'], true); ?></td>

<tr><td><?php _e('Maximum Duplication', 'formidable') ?></td>    
<td><input type="text" name="field_options[duplication_<?php $field['id'] ?>]" value="<?php echo esc_attr($field['duplication']) ?>" size="3"/> <span class="howto"><?php _e('The number of times the end user is allowed to duplicate this section of fields in one entry', 'formidable') ?></span></td>
</tr>
<?php }else if($field['type'] == 'phone'){ ?>
<tr>
<td><label><?php _e('Format', 'formidable') ?></label>
<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e('Insert the format you would like to accept. Use a regular expression starting with ^ or an exact format like (999)999-9999.', 'formidable') ?>" ></span>
</td>
<td><input type="text" class="frm_long_input" value="<?php echo esc_attr($field['format']) ?>" name="field_options[format_<?php echo $field['id'] ?>]" />
</td>
</tr>
<?php } 

if(!in_array($field['type'], array('break', 'hidden', 'user_id', 'divider', 'html', 'form'))){ ?>
<tr>
<td><label><?php _e('Visibility', 'formidable') ?></label>
<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e('Determines who can see this field. The selected user role and higher user roles will be able to see this field. The only exception is logged-out users. Only logged-out users will be able to see the field if that option is selected.', 'formidable') ?>" ></span>
</td>
<td>
<?php 
    if($field['admin_only'] == 1) $field['admin_only'] = 'administrator'; 
    else if(empty($field['admin_only'])) $field['admin_only'] = '';
    
    if(!isset($frm_vars['editable_roles']) or !$frm_vars['editable_roles'])
        $frm_vars['editable_roles'] = get_editable_roles();
?>

<select name="field_options[admin_only_<?php echo $field['id'] ?>]">
    <option value=""><?php _e('Everyone', 'formidable') ?></option>
    <?php foreach($frm_vars['editable_roles'] as $role => $details){ ?>
        <option value="<?php echo esc_attr($role) ?>" <?php echo ($field['admin_only'] == $role) ? ' selected="selected"' : ''; ?>><?php echo translate_user_role($details['name'] ) ?> </option>
    <?php
            unset($role);
            unset($details);
        } ?>
    <option value="loggedin" <?php echo ($field['admin_only'] == 'loggedin') ? ' selected="selected"' : ''; ?>><?php _e('Logged-in Users', 'formidable') ?></option>
    <option value="loggedout" <?php echo ($field['admin_only'] == 'loggedout') ? ' selected="selected"' : ''; ?>><?php _e('Logged-out Users', 'formidable'); ?></option>
</select>
</td>
</tr>
<?php 
}

if(in_array($field['type'], array('text', 'number', 'textarea', 'hidden'))){ ?>
<tr><td><?php _e('Calculations', 'formidable') ?></td>
    <td><label for="use_calc_<?php echo $field['id'] ?>"><input type="checkbox" value="1" name="field_options[use_calc_<?php echo $field['id'] ?>]" <?php checked($field['use_calc'], 1) ?> class="use_calc" id="use_calc_<?php echo $field['id'] ?>" onchange="frm_show_div('frm_calc_opts<?php echo $field['id'] ?>',this.checked,true,'#')" /> 
        <?php _e('Calculate the default value for this field', 'formidable') ?></label>
        <div id="frm_calc_opts<?php echo $field['id'] ?>" <?php if(!$field['use_calc']) echo 'class="frm_hidden"'; ?>>
            <select class="frm_shortcode_select frm_insert_val" data-target="frm_calc_<?php echo $field['id'] ?>">
                <option value="">&mdash; <?php _e('Select a value to insert into the box below', 'formidable') ?> &mdash;</option>
            </select><br/>
            <input type="text" value="<?php echo esc_attr($field['calc']) ?>" id="frm_calc_<?php echo $field['id'] ?>" name="field_options[calc_<?php echo $field['id'] ?>]" class="frm_long_input"/>
        </div>
    </td>
</tr>

<?php }

if (!in_array($field['type'], array('hidden', 'user_id'))){ ?>
<tr><td><?php _e('Conditional Logic', 'formidable'); ?></td>
    <td>
    <a id="logic_<?php echo $field['id'] ?>" class="frm_add_logic_row frm_add_logic_link <?php echo (!empty($field['hide_field']) and (count($field['hide_field']) > 1 or reset($field['hide_field']) != '')) ? ' frm_hidden' : ''; ?>"><?php _e('Use Conditional Logic', 'formidable') ?></a>
    <div class="frm_logic_rows<?php echo (!empty($field['hide_field']) and (count($field['hide_field']) > 1 or reset($field['hide_field']) != '')) ? '' : ' frm_hidden'; ?>">
        <div id="frm_logic_row_<?php echo $field['id'] ?>">
        <select name="field_options[show_hide_<?php echo $field['id'] ?>]">
            <option value="show" <?php selected($field['show_hide'], 'show') ?>><?php echo ($field['type'] == 'break') ? __('Do not skip', 'formidable') : __('Show', 'formidable'); ?></option>
            <option value="hide" <?php selected($field['show_hide'], 'hide') ?>><?php echo ($field['type'] == 'break') ? __('Skip', 'formidable') : __('Hide', 'formidable'); ?></option>
        </select>
        
<?php $all_select = 
'<select name="field_options[any_all_'. $field['id'] .']">'.
    '<option value="any" '. selected($field['any_all'], 'any', false) .'>'. __('any', 'formidable') .'</option>'.
    '<option value="all" '. selected($field['any_all'], 'all', false) .'>'. __('all', 'formidable') .'</option>'.
'</select>';

    echo ($field['type'] == 'break') ?  sprintf(__('next page if %s of the following match:', 'formidable'), $all_select) : sprintf(__('this field if %s of the following match:', 'formidable'), $all_select);
    unset($all_select);
    
            if(!empty($field['hide_field'])){ 
                foreach($field['hide_field'] as $meta_name => $hide_field){
                    include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/_logic_row.php');
                }
            }
        ?>
        </div>
    </div>
    
    
    </td>
</tr>
<?php } ?>