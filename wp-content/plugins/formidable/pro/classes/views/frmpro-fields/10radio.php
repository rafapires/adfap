<?php 
if ( is_array($field['options']) ) {
    if ( !isset($field['value']) ) {
        $field['value'] = maybe_unserialize($field['default_value']);
    }
    
    $star = (isset($field['star']) && $field['star']);  
    foreach ( $field['options'] as $opt_key => $opt ) {
        $opt = apply_filters('frm_field_label_seen', $opt, $opt_key, $field);
        $checked = $field['value'] == $opt ? 'checked="true"' : '';
        $last =  end($field['options']) == $opt ? ' frm_last' : '';
        
?>
<?php 
if ( !$star ) { 
    ?><div class="frm_scale <?php echo $last ?>"><label for="field_<?php echo $field['id']?>-<?php echo $opt_key ?>"><?php 
} ?>
<input type="radio" name="<?php echo $field_name ?>" id="field_<?php echo $field['id'] ?>-<?php echo $opt_key ?>" value="<?php echo esc_attr($opt) ?>" <?php echo $checked ?> <?php do_action('frm_field_input_html', $field) ?> />
<?php if ( !$star ) {
    ?> <?php echo $opt ?></label>
</div>   
<?php } 

} 
} ?>   
<div style="clear:both;"></div>