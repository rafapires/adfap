<?php $sanitized_name = sanitize_title_with_dashes($custom_data['meta_name']); ?>
<div id="frm_postmeta_<?php echo $sanitized_name ?>" class="frm_postmeta_row">
    <label class="frm_left_label" style="width:60px;min-width:0;"><?php _e('Name') ?></label>
    <?php
    if(isset($cf_keys) && $echo && $custom_data['meta_name'] != '' && !in_array($custom_data['meta_name'], (array)$cf_keys)){
        $cf_keys[] = $custom_data['meta_name'];
    }
    
    if(!isset($cf_keys) || empty($cf_keys)){ ?>
    <input type="text" value="<?php echo $echo ? esc_attr($custom_data['meta_name']) : '' ?>" name="options[post_custom_fields][<?php echo $sanitized_name ?>][meta_name]" class="frm_enternew" />
    <?php }else{ ?>
    <select name="options[post_custom_fields][<?php echo $sanitized_name ?>][meta_name]" class="frm_cancelnew">
        <option value=""><?php _e( '&mdash; Select &mdash;' ); ?></option>
        <?php
        foreach ( $cf_keys as $cf_key ) { ?>
    	<option value="<?php echo esc_attr($cf_key) ?>"><?php echo esc_html($cf_key) ?></option>
    	<?php
    		unset($cf_key);
    	}
        ?>
    </select>
    <input type="text" class="hide-if-js frm_enternew" name="options[post_custom_fields][<?php echo $sanitized_name ?>][custom_meta_name]" value="" />
    <?php } ?>
    &nbsp;
    <?php _e('Form Field', 'formidable') ?>
    <select name="options[post_custom_fields][<?php echo $sanitized_name ?>][field_id]" class="frm_single_post_field">
        <option value="">&mdash; <?php echo _e('Select Field', 'formidable') ?> &mdash;</option>
        <?php 
        if(!empty($values['fields'])){
            if(!isset($custom_data['field_id']))
                $custom_data['field_id'] = '';
                
        foreach($values['fields'] as $fo){
            $fo = (array)$fo;
            if(!in_array($fo['type'], array('divider', 'html', 'break', 'captcha'))){ ?>
        <option value="<?php echo $fo['id'] ?>" <?php selected($custom_data['field_id'], $fo['id']) ?>><?php echo FrmAppHelper::truncate($fo['name'], 50) ?></option>
        <?php
            }
            unset($fo);
        }
        } ?>
    </select>
    <a class="frm_remove_tag frm_icon_font" data-removeid="frm_postmeta_<?php echo $sanitized_name ?>"></a>
    <a class="frm_add_tag frm_icon_font frm_add_postmeta_row"></a>
    
    <?php if(isset($cf_keys) && !empty($cf_keys)){ ?>
    <div class="clear"></div>
    <div style="float:left;margin-left:60px;">
    <a class="hide-if-no-js frm_toggle_cf_opts">
        <span class="frm_enternew"><?php _e('Enter new'); ?></span>
        <span class="frm_cancelnew frm_hidden"><?php _e('Cancel', 'formidable'); ?></span>
    </a>
    </div>
    <div class="clear"></div>
    <?php } ?>
</div>
<?php unset($sanitized_name); ?>