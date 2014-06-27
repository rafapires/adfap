<?php
if (in_array($field['type'], array('tag', 'date'))){ ?>
<input type="text" id="field_<?php echo $field['field_key'] ?>" name="<?php echo $field_name ?>" value="<?php echo esc_attr($field['value']) ?>" <?php do_action('frm_field_input_html', $field) ?>/>
<?php 
if ($field['type'] == 'date' && (!isset($field['read_only']) || !$field['read_only'] || ($field['read_only'] && isset($frm_vars['readonly']) && $frm_vars['readonly'] == 'disabled' ))){
    if(!isset($frm_vars['datepicker_loaded']) or !is_array($frm_vars['datepicker_loaded'])) 
        $frm_vars['datepicker_loaded'] = array();
    if(!isset($frm_vars['datepicker_loaded']['field_'. $field['field_key']]))
        $frm_vars['datepicker_loaded']['field_'. $field['field_key']] = true;
    FrmProFieldsHelper::set_field_js($field, (isset($entry_id) ? $entry_id : 0));
}

}else if($field['type'] == 'time'){
if($field['unique']){
    if(!isset($frm_vars['timepicker_loaded']) or !is_array($frm_vars['timepicker_loaded'])) 
        $frm_vars['timepicker_loaded'] = array();
    if(!isset($frm_vars['timepicker_loaded']['field_'. $field['field_key']]))
        $frm_vars['timepicker_loaded']['field_'. $field['field_key']] = true;
}

if(isset($field['options']['H'])){
if(!empty($field['value']) and !is_array($field['value'])){
    $h = explode(':', $field['value']);
    $m = explode(' ', $h[1]);
    $h = reset($h);
    $a = isset($m[1]) ? $m[1] : '';
    $m = reset($m);
}else if(is_array($field['value'])){
    $h = isset($field['value']['H']) ? $field['value']['H'] : '';
    $m = isset($field['value']['m']) ? $field['value']['m'] : '';
    $a = isset($field['value']['A']) ? $field['value']['A'] : '';
}else{
    $h = $m = $a = '';
} ?>
<select name="<?php echo $field_name ?>[H]" id="field_<?php echo $field['field_key'] ?>_H" <?php do_action('frm_field_input_html', $field) ?>>
    <?php foreach($field['options']['H'] as $hour){ ?>
        <option value="<?php echo $hour ?>" <?php selected($h, $hour) ?>><?php echo $hour ?></option>
    <?php } ?>
</select> :
<select name="<?php echo $field_name ?>[m]" id="field_<?php echo $field['field_key'] ?>_m" <?php do_action('frm_field_input_html', $field) ?>>
    <?php foreach($field['options']['m'] as $min){ ?>
        <option value="<?php echo $min ?>" <?php selected($m, $min) ?>><?php echo $min ?></option>
    <?php } ?>
</select>
<?php if(isset($field['options']['A'])){ ?>
<select name="<?php echo $field_name ?>[A]" id="field_<?php echo $field['field_key'] ?>_A" <?php do_action('frm_field_input_html', $field) ?>>
    <?php foreach($field['options']['A'] as $am){ ?>
        <option value="<?php echo $am ?>" <?php selected($a, $am) ?>><?php echo $am ?></option>
    <?php } ?>
</select>
<?php
}
}else{ ?>
<select name="<?php echo $field_name ?>" id="field_<?php echo $field['field_key'] ?>" <?php do_action('frm_field_input_html', $field) ?>>
    <?php foreach($field['options'] as $t){ ?>
        <option value="<?php echo $t ?>" <?php selected($field['value'], $t) ?>><?php echo $t ?></option>
    <?php } ?>
</select>
<?php    
}

}else if(in_array($field['type'], array('email', 'url', 'number', 'password', 'phone', 'range'))){ 
    $field['type'] = ($field['type'] == 'phone') ? 'tel' : $field['type']; ?>
<input type="<?php echo ($frm_settings->use_html or $field['type'] == 'password') ? $field['type'] : 'text'; ?>" id="field_<?php echo $field['field_key'] ?>" name="<?php echo $field_name ?>" value="<?php echo esc_attr($field['value']) ?>" <?php do_action('frm_field_input_html', $field) ?>/>
<?php
$field['type'] = ($field['type'] == 'tel') ? 'phone' : $field['type'];

if($field['type'] == 'phone' and isset($field['format']) and !empty($field['format']) and strpos($field['format'], '^') !== 0){
    global $frm_input_masks;
    if(!isset($frm_input_masks[$field['id']]))
        $frm_input_masks[$field['id']] = preg_replace('/\d/', '9', $field['format']);
}

}else if ($field['type'] == 'image'){?>
<input type="<?php echo ($frm_settings->use_html) ? 'url' : 'text'; ?>" id="field_<?php echo $field['field_key'] ?>" name="<?php echo $field_name ?>" value="<?php echo esc_attr($field['value']) ?>" <?php do_action('frm_field_input_html', $field) ?>/>
<?php if ($field['value']){ ?><img src="<?php echo $field['value'] ?>" height="50px" /><?php }
    
}else if ($field['type'] == 'scale'){
    require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/10radio.php');
    if(isset($field['star']) and $field['star']){
        if(!isset($frm_vars['star_loaded']) or !is_array($frm_vars['star_loaded']))
            $frm_vars['star_loaded'] = array(true);
    }
}else if ($field['type'] == 'rte' and is_admin() and !defined('DOING_AJAX')){ ?>
<div id="<?php echo (user_can_richedit()) ? 'postdivrich' : 'postdiv'; ?>" class="postarea frm_full_rte">
<?php
wp_editor(str_replace('&quot;', '"', $field['value']), 'field_'. $field['field_key'], 
    array('dfw' => true, 'textarea_name' => $field_name)
); ?>
</div>      
<?php

}else if ($field['type'] == 'rte'){
    
    if ( (!isset($frm_vars['ajax_edit']) || !$frm_vars['ajax_edit']) && isset($field['rte']) && $field['rte'] == 'mce' ) {
        $e_args = array('media_buttons' => false, 'textarea_name' => $field_name);
        if($field['max'])
            $e_args['textarea_rows'] = $field['max'];
        if($field['size']){ ?>
<style type="text/css">#wp-field_<?php echo $field['field_key'] ?>-wrap{width:<?php echo (int)((int)$field['size'] * 8.6) ?>px;}</style><?php
        }
        
        wp_editor(str_replace('&quot;', '"', $field['value']), 'field_'. $field['field_key'] . ((isset($frm_vars['ajax_edit']) and $frm_vars['ajax_edit']) ? $frm_vars['ajax_edit'] : '' ),  $e_args);
        if ( defined('DOING_AJAX') && (!isset($frm_vars['tinymce_loaded']) || !$frm_vars['tinymce_loaded'])) {
            add_action( 'wp_print_footer_scripts', '_WP_Editors::editor_js', 50 );
			add_action( 'wp_footer', '_WP_Editors::enqueue_scripts', 1 );
			$frm_vars['tinymce_loaded'] = true;
        }
        unset($e_args);
    }else{ ?>
<textarea name="<?php echo $field_name ?>" id="field_<?php echo $field['field_key'] ?>" <?php if ($field['size']){ ?>cols="<?php echo $field['size'] ?>"<?php } ?> style="height:<?php echo ($field['max']) ? ((int)$field['max'] * 17) : 125 ?>px;<?php if (!$field['size']){ ?>width:<?php echo $frmpro_settings->field_width; } ?>" <?php do_action('frm_field_input_html', $field) ?>><?php echo FrmAppHelper::esc_textarea($field['value']) ?></textarea>
<?php
if(!FrmProFieldsHelper::mobile_check()){
    if(!isset($frm_vars['rte_loaded']))
        $frm_vars['rte_loaded'] = array();
    $frm_vars['rte_loaded'][] = 'field_'. $field['field_key'];
}
    }
}else if ($field['type'] == 'file'){
    
    if(isset($field['read_only']) && $field['read_only'] && (!isset($frm_vars['readonly']) || $frm_vars['readonly'] != 'disabled') ){
        foreach ( (array) maybe_unserialize($field['value']) as $media_id ) {
            if ( !is_numeric($media_id) ) {
                continue;
            }
?>
<input type="hidden" name="<?php echo $field_name; if(isset($field['multiple']) and $field['multiple']) echo '[]'; ?>" value="<?php echo esc_attr($media_id) ?>" />
<div class="frm_file_icon"><?php echo FrmProFieldsHelper::get_file_icon($media_id); ?></div>
<?php
        }
    }else if(isset($field['multiple']) and $field['multiple']){
		$media_ids = maybe_unserialize($field['value']);
		if ( !is_array($media_ids) && strpos($media_ids, ',') ) {
			$media_ids = explode(',', $media_ids);
		}
		
		foreach((array)$media_ids as $media_id){
			$media_id = trim($media_id);
            if(!is_numeric($media_id))
                continue;
            
            $media_id = (int) $media_id;
?>
<div id="frm_uploaded_<?php echo $media_id ?>" class="frm_uploaded_files">
<input type="hidden" name="<?php echo $field_name ?>[]" value="<?php echo esc_attr($media_id) ?>" />
<div class="frm_file_icon"><?php echo FrmProFieldsHelper::get_file_icon($media_id); ?></div>
<a class="frm_remove_link"><?php _e('Remove', 'formidable') ?></a>
</div>
<?php
		unset($media_id);
	}
unset($media_ids);
if(empty($field_value)){ ?>
<input type="hidden" name="<?php echo $field_name ?>[]" value="" />
<?php } ?>
<input type="file" multiple="multiple" name="file<?php echo $field['id'] ?>[]" id="field_<?php echo $field['field_key'] ?>" <?php do_action('frm_field_input_html', $field) ?> onchange="frmNextUpload(jQuery(this),<?php echo $field['id'] ?>)" />
<?php
    }else{ ?>
<input type="file" name="file<?php echo $field['id'] ?>" id="field_<?php echo $field['field_key'] ?>" <?php do_action('frm_field_input_html', $field) ?> /><br/>
<input type="hidden" name="<?php echo $field_name ?>" value="<?php echo esc_attr(is_array($field['value']) ? reset($field['value']) : $field['value']) ?>" />
<?php echo FrmProFieldsHelper::get_file_icon($field['value']);      
    }

include_once(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/loading.php');

}else if ($field['type'] == 'data'){ ?>
<div id="frm_data_field_<?php echo $field['id'] ?>_container">
<?php require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/data-options.php'); ?>
</div>
<?php

}else if($field['type'] == 'form'){
    echo 'FRONT FORM';
} ?>