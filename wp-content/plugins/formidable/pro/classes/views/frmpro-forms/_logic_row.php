<div id="<?php echo $id ?>" class="frm_logic_row frm_logic_row_<?php echo $key ?>">
<select name="<?php echo $names['hide_field'] ?>" <?php if ( !empty($onchange)) { ?>onchange="<?php echo $onchange ?>"<?php } ?>>
    <option value="">&mdash; <?php _e('Select Field', 'formidable') ?> &mdash;</option>
    <?php
    foreach ( $form_fields as $ff ) {
        if ( is_array($ff) ) {
            $ff = (object)$ff;
        }
        
        if ( in_array($ff->type, array('captcha', 'divider', 'break', 'file', 'rte', 'date', 'html')) || ( $ff->type == 'data' && ( !isset($ff->field_options['data_type']) || in_array( $ff->field_options['data_type'], array( 'data', '' ))))) {
            continue;
        }
        
        $selected = ( isset($condition['hide_field']) && $ff->id == $condition['hide_field'] ) ? ' selected="selected"' : ''; ?>
    <option value="<?php echo $ff->id ?>"<?php echo $selected ?>><?php echo FrmAppHelper::truncate($ff->name, 30); ?></option>
    <?php
        unset($ff);
        } ?>
</select>
<?php _e('is', 'formidable'); ?>

<select name="<?php echo $names['hide_field_cond'] ?>">
    <option value="==" <?php selected($condition['hide_field_cond'], '==') ?>><?php _e('equal to', 'formidable') ?></option>
    <option value="!=" <?php selected($condition['hide_field_cond'], '!=') ?>><?php _e('NOT equal to', 'formidable') ?> &nbsp;</option>
    <option value=">" <?php selected($condition['hide_field_cond'], '>') ?>><?php _e('greater than', 'formidable') ?></option>
    <option value="<" <?php selected($condition['hide_field_cond'], '<') ?>><?php _e('less than', 'formidable') ?></option>
    <option value="LIKE" <?php selected($condition['hide_field_cond'], 'LIKE') ?>><?php _e('like', 'formidable') ?></option>
    <option value="not LIKE" <?php selected($condition['hide_field_cond'], 'not LIKE') ?>><?php _e('not like', 'formidable') ?> &nbsp;</option>
</select>

<span id="frm_show_selected_values_<?php echo $key ?>_<?php echo $meta_name ?>">
<?php 
    if ($condition['hide_field'] and is_numeric($condition['hide_field'])){
        global $frm_field;
        $new_field = $frm_field->getOne($condition['hide_field']);   
    }
    
    $val = isset($condition['hide_opt']) ? $condition['hide_opt'] : '';
    if(!isset($field))
        $field = array('hide_opt' => array($meta_name => $val));
    $field_name = $names['hide_opt'];
    
    require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-fields/field-values.php');
?>
</span>
<a class="frm_remove_tag frm_icon_font" data-removeid="<?php echo $id ?>" <?php echo !empty($showlast) ? 'data-showlast="'. $showlast .'"' : ''; ?>></a>
<a class="frm_add_tag frm_icon_font frm_add_<?php echo $type ?>_logic" data-emailkey="<?php echo $key ?>"></a>
</div>