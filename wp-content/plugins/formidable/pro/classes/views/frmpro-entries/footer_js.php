
<script type="text/javascript">
/*<![CDATA[*/
<?php
if(isset($frm_vars['tinymce_loaded']) and $frm_vars['tinymce_loaded'] === true)
    echo 'var ajaxurl="'. admin_url( 'admin-ajax.php', 'relative' ) .'";'."\n";

if(isset($frm_vars['rules']) and !empty($frm_vars['rules']))
    echo "__FRMRULES=". json_encode($frm_vars['rules']) .";\n";

if(isset($frm_vars['recaptcha_loaded']) and $frm_vars['recaptcha_loaded'])
    echo $frm_vars['recaptcha_loaded'];

if(isset($frm_vars['rte_loaded']) && !empty($frm_vars['rte_loaded'])){
    foreach((array)$frm_vars['rte_loaded'] as $rte_field_id){ ?>
new nicEditor({fullPanel:true,iconsPath:'<?php echo FrmAppHelper::plugin_url() ?>/pro/images/nicEditIcons.gif'<?php do_action('frm_rte_js', $rte_field_id) ?>}).panelInstance('<?php echo $rte_field_id ?>',{hasPanel:true});
<?php }
}

?>
jQuery(document).ready(function($){
<?php if((!defined('DOING_AJAX') or (isset($frm_vars['preview']) and $frm_vars['preview'])) and (!is_admin() or !isset($_GET) or !isset($_GET['page']) or $_GET['page'] != 'formidable-entries')){ ?>
$(document).off('submit.formidable','.frm-show-form');$(document).on('submit.formidable','.frm-show-form',frmOnSubmit);
<?php }

if(isset($frm_vars['chosen_loaded']) and $frm_vars['chosen_loaded']){ ?>
$('.frm_chzn').chosen({<?php echo apply_filters('frm_chosen_js', 'allow_single_deselect:true') ?>});<?php 
}

if((isset($frm_vars['hidden_fields']) and !empty($frm_vars['hidden_fields'])) 
or (isset($frm_vars['datepicker_loaded']) and !empty($frm_vars['datepicker_loaded']) and is_array($frm_vars['datepicker_loaded']))
or (isset($load_lang) and !empty($load_lang)) or (isset($frm_vars['timepicker_loaded']) and !empty($frm_vars['timepicker_loaded'])) or (isset($frm_vars['calc_fields']) and !empty($frm_vars['calc_fields']))){
if(isset($frm_vars['hidden_fields']) and !empty($frm_vars['hidden_fields'])){
    global $frm_field;
    $hideme = array();
    foreach((array)$frm_vars['hidden_fields'] as $field){
        foreach($field['hide_field'] as $i => $hide_field){
            if(!is_numeric($hide_field))
                continue;
            
            $observed_field = $frm_field->getOne($hide_field);
            
            if($observed_field){
                if (isset($field['hide_opt'][$i])){
                    if(in_array($observed_field->id, $hideme))
                        continue;
                    
                    $hideme[] = $observed_field->id;
                    
                    if($observed_field->type == 'data'){
                        if ($field['hide_opt'][$i] != '' and in_array($observed_field->field_options['data_type'], array('radio', 'checkbox', 'select'))){
                            ?>$('#frm_field_<?php echo $field['id'] ?>_container').hide();<?php
                        }else if($field['hide_opt'][$i] == '' and ($field['data_type'] == '' or $field['data_type'] == 'data')){
                            $observed_options = maybe_unserialize($observed_field->field_options);
                            if (in_array($observed_options['data_type'], array('checkbox', 'select'))){ ?>
$('#frm_field_<?php echo $field['id'] ?>_container').hide();<?php
                            }
                            unset($observed_options);
                        }
                    }else{
                        ?>$('#frm_field_<?php echo $field['id'] ?>_container').hide();<?php
                    }
                }else if ($observed_field->type == 'data' and ($field['data_type'] == '' or $field['data_type'] == 'data')){
                    $observed_options = maybe_unserialize($observed_field->field_options);
                    if (in_array($observed_options['data_type'], array('checkbox', 'select'))){ ?>
$('#frm_field_<?php echo $field['id'] ?>_container').hide();<?php
                    }
                    unset($observed_options);
                }
                ?>frmCheckDependent('und',<?php echo $observed_field->id ?>);<?php
            }
            unset($observed_field);
            unset($i);
            unset($hide_field);
        }
        unset($field);
    }
    unset($hideme);
}

if(isset($frm_vars['datepicker_loaded']) and !empty($frm_vars['datepicker_loaded']) and is_array($frm_vars['datepicker_loaded'])){
    global $frmpro_settings; 
    $load_lang = array();
    reset($frm_vars['datepicker_loaded']);
    $datepicker = key($frm_vars['datepicker_loaded']); 

foreach($frm_vars['datepicker_loaded'] as $date_field_id => $options){ ?>
$(document).on('focusin','#<?php echo $date_field_id ?>', function(){
$.datepicker.setDefaults($.datepicker.regional['']);
$(this).datepicker($.extend($.datepicker.regional['<?php echo $options['locale'] ?>'], {dateFormat:'<?php echo $frmpro_settings->cal_date_format ?>',changeMonth:true,changeYear:true,yearRange:'<?php echo $options['start_year'] .':'. $options['end_year'] ?>'<?php do_action('frm_date_field_js', $date_field_id, $options)?>}));
});
<?php 
if(!empty($options['locale'])) $load_lang[] = $options['locale'];
}

if(isset($frm_vars['timepicker_loaded']) and !empty($frm_vars['timepicker_loaded'])){
    foreach($frm_vars['timepicker_loaded'] as $time_field_id => $options){
        if($options and isset($datepicker)){ ?>
$("#<?php echo $datepicker ?>").change(function(){
var e=$(this).parents('form:first').find('input[name="id"]');
jQuery.ajax({
type:'POST',url:'<?php echo admin_url( 'admin-ajax.php' ) ?>',dataType:'json',
data:'action=frm_fields_ajax_time_options&time_field=<?php echo $time_field_id ?>&date_field=<?php echo $datepicker ?>&entry_id='+(e?e.val():'')+'&date='+$(this).val(),
success:function(opts){
    $('#<?php echo $time_field_id ?>').find('option').removeAttr('disabled');
    if(opts && opts!=''){for(var opt in opts){$('#<?php echo $time_field_id ?>').find('option[value="'+opt+'"]').attr('disabled', 'disabled');}}
}
});
});
<?php }
    }
    unset($datepicker);
}
}

if(isset($frm_vars['calc_fields']) and !empty($frm_vars['calc_fields'])){ 
global $frmdb, $frm_field; 

foreach($frm_vars['calc_fields'] as $result => $calc){ 
    preg_match_all("/\[(.?)\b(.*?)(?:(\/))?\]/s", $calc, $matches, PREG_PATTERN_ORDER);
    
    //if (!isset($matches[0])) return $value;
    $field_keys = $calc_fields = array();
    
    foreach ($matches[0] as $match_key => $val){
        $val = trim(trim($val, '['), ']');
        $calc_fields[$val] = $frm_field->getOne($val); //get field
        if ( !$calc_fields[$val] ) {
            unset($calc_fields[$val]);
            continue;
        }
        
        if ( $calc_fields[$val] && in_array($calc_fields[$val]->type, array('radio', 'scale', 'checkbox')) ) {
            $field_keys[$calc_fields[$val]->id] = 'input[name^="item_meta['. $calc_fields[$val]->id .']"]';
        } else {
            $field_keys[$calc_fields[$val]->id] = ($calc_fields[$val]) ? '#field_'. $calc_fields[$val]->field_key : '#field_'. $val;
        }
        
        $calc = str_replace($matches[0][$match_key], 'vals[\''.$calc_fields[$val]->id.'\']', $calc);
    }

//$(' echo implode(",", $field_keys) ').change(function(e){    
?>
$(document).on('change','<?php echo implode(",", $field_keys) ?>',function(e){
if(e.frmTriggered && e.frmTriggered == '<?php echo $result ?>'){return false;}
var vals=new Array();
<?php 

    foreach ( $calc_fields as $calc_field ) {
        if ( $calc_field->type == 'checkbox' ) {
?>$('<?php echo $field_keys[$calc_field->id] ?>:checked, <?php echo $field_keys[$calc_field->id] ?>[type=hidden]').each(function(){ 
if(isNaN(vals['<?php echo $calc_field->id ?>'])){vals['<?php echo $calc_field->id ?>']=0;}
var n=parseFloat($(this).val().match(/-?\d*(\.\d*)?$/));if(isNaN(n))n=0;
vals['<?php echo $calc_field->id ?>'] += n; });
<?php
        } else if ( $calc_field->type == 'date' ) {
            global $frmpro_settings;
?>var d=$.datepicker.parseDate('<?php echo $frmpro_settings->cal_date_format ?>', $('<?php echo $field_keys[$calc_field->id]; ?>').val());
if(d!=null){vals['<?php echo $calc_field->id ?>']=Math.ceil(d/(1000*60*60*24));}
<?php
        } else {
?>vals['<?php echo $calc_field->id ?>']=$('<?php 
echo $field_keys[$calc_field->id]; 
if(in_array($calc_field->type, array("radio", "scale")))
    echo ":checked, ". $field_keys[$calc_field->id] ."[type=hidden]";
else if($calc_field->type == "select")
    echo " option:selected, ". $field_keys[$calc_field->id] .":hidden";
?>').val();
if(typeof(vals['<?php echo $calc_field->id ?>'])=='undefined'){vals['<?php echo $calc_field->id ?>']=0;}else{ vals['<?php echo $calc_field->id ?>']=parseFloat(vals['<?php echo $calc_field->id ?>'].match(/-?\d*(\.\d*)?$/)); }
<?php
        } 
?>if(isNaN(vals['<?php echo $calc_field->id ?>'])){vals['<?php echo $calc_field->id ?>']=0;}<?php

    }
?>var total=parseFloat(<?php echo $calc ?>);if(isNaN(total)){total=0;}
$("#field_<?php echo $result ?>").val(total).trigger({type:'change',frmTriggered:'<?php echo $result ?>',selfTriggered:true});
});<?php

    if ( !isset($frm_vars['triggered']) ) {
        $frm_vars['triggered'] = array();
    }

    if ( !in_array(reset($field_keys), $frm_vars['triggered']) ) {
        $frm_vars['triggered'][] = reset($field_keys);

        // initialize claculations on page load
        ?>$('<?php echo reset($field_keys) ?>').trigger({type:'change',selfTriggered:true});<?php
    }
}
}
} 

if(!empty($frm_input_masks)){
    foreach((array)$frm_input_masks as $f_key => $mask){
        if(!$mask)
            continue;
        
?>$(document).on('focusin','<?php echo is_numeric($f_key) ? 'input[name="item_meta['. $f_key .']"]' : '#field_'. $f_key; ?>', function(){ $(this).mask("<?php echo $mask ?>"); });
<?php   
        unset($f_key);
        unset($mask);
    }
}

?>
});
<?php if(isset($load_lang) and !empty($load_lang)){ ?>
var frmJsHost=(("https:"==document.location.protocol)?"https://":"http://");
<?php foreach($load_lang as $lang){ ?>
document.write(unescape("%3Cscript src='"+frmJsHost+"ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/i18n/jquery.ui.datepicker-<?php echo $lang ?>.js' type='text/javascript'%3E%3C/script%3E"));
<?php }
} ?>
/*]]>*/
</script>
