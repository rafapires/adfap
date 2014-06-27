<div class="frm_form_fields">
<?php
global $frm_vars;
if(!isset($frm_vars['star_loaded']) or !is_array($frm_vars['star_loaded']))
    $frm_vars['star_loaded'] = array();
if(!$frm_vars['forms_loaded'] or empty($frm_vars['forms_loaded']))
    $frm_vars['forms_loaded'][] = true;

$rand = FrmProAppHelper::get_rand(3);
$name = $field->id . $rand;
if(in_array($name, $frm_vars['star_loaded'])){
    $rand = FrmProAppHelper::get_rand(3);
    $name = $field->id . $rand;
}
$frm_vars['star_loaded'][] = $name;   

$field->options = maybe_unserialize($field->options);
$max = max($field->options);

$d = 0;
if($stat != floor($stat)){
    $stat = round($stat, 2);
    list($n, $d) = explode('.', $stat);
    if ($d < 25) {
        $d = 0;
    } else if ( $d < 75 ) {
        $d = 5;
    } else {
        $d = 0;
        $n++;
    }
    
    $stat = (float) ($n .'.'. $d);
}

for($i=1; $i<=$max; $i++){
    // check if this is a half
    $class = ( $d && ($i-1) == $n ) ? ' frm_half_star' : '';
    
    $checked = (round($stat) == $i) ? 'checked="checked"' : '';
    ?><input type="radio" name="item_meta[<?php echo $name ?>]" value="<?php echo $i; ?>" <?php echo $checked ?> class="star<?php echo $class ?>" disabled="disabled" style="display:none;"/><?php 
} ?>
</div>