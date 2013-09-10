<?php 
if(!isset($saving))
    header("Content-type: text/css");

if (isset($_GET['frm_weight'])){
    $form_width = $_GET['frm_form_width'];
    $form_align = $_GET['frm_form_align'];
    $fieldset = $_GET['frm_fieldset'];
    $fieldset_color = $_GET['frm_fieldset_color'];
    $fieldset_padding = $_GET['frm_fieldset_padding'];
    
    $font = stripslashes($_GET['frm_font']);
    $font_size = $_GET['frm_font_size'];
    $label_color = $_GET['frm_label_color'];
    $weight = $_GET['frm_weight']; 
    $position = $_GET['frm_position'];
    $align = $_GET['frm_align'];
    $width = $_GET['frm_width'];
    $required_color = $_GET['frm_required_color'];
    $required_weight = $_GET['frm_required_weight'];
    
    $description_font = stripslashes($_GET['frm_description_font']);
    $description_font_size = $_GET['frm_description_font_size'];
    $description_color = $_GET['frm_description_color'];
    $description_weight = $_GET['frm_description_weight'];
    $description_style = $_GET['frm_description_style']; 
    $description_align = $_GET['frm_description_align'];
    
    $field_font_size = $_GET['frm_field_font_size'];
    $field_width = $_GET['frm_field_width'];
    $auto_width = isset($_GET['frm_auto_width']) ? $_GET['frm_auto_width'] : 0;
    $field_pad = $_GET['frm_field_pad'];
    $field_margin = $_GET['frm_field_margin'];
    
    $text_color = $_GET['frm_text_color'];
    $bg_color = $_GET['frm_bg_color'];
    $border_color = $_GET['frm_border_color'];
    $field_border_width = $_GET['frm_field_border_width'];
    $field_border_style = $_GET['frm_field_border_style'];
    
    //$bg_color_hv = $_GET['frm_bg_color_hv'];
    //$border_color_hv = $_GET['frm_border_color_hv'];
    
    $bg_color_active = $_GET['frm_bg_color_active'];
    $border_color_active = $_GET['frm_border_color_active'];
    
    $text_color_error = $_GET['frm_text_color_error'];
    $bg_color_error = $_GET['frm_bg_color_error'];
    $border_color_error = $_GET['frm_border_color_error'];
    $border_width_error = $_GET['frm_border_width_error'];
    $border_style_error = $_GET['frm_border_style_error'];
    
    $radio_align = $_GET['frm_radio_align'];
    $check_align = $_GET['frm_check_align'];
    $check_font = stripslashes($_GET['frm_check_font']);
    $check_font_size = $_GET['frm_check_font_size'];
    $check_label_color = $_GET['frm_check_label_color'];
    $check_weight = $_GET['frm_check_weight'];
    
    $submit_style = isset($_GET['frm_submit_style']) ? $_GET['frm_submit_style'] : 0;
    $submit_font_size = $_GET['frm_submit_font_size'].' !important';
    $submit_width = $_GET['frm_submit_width'];
    $submit_height = $_GET['frm_submit_height'];
    $submit_bg_color = $_GET['frm_submit_bg_color'];
    $submit_bg_color2 = $_GET['frm_submit_bg_color2'];
    $submit_bg_img = $_GET['frm_submit_bg_img'];
    $submit_border_color = $_GET['frm_submit_border_color'];
    $submit_border_width = $_GET['frm_submit_border_width'];
    $submit_text_color = $_GET['frm_submit_text_color'];
    $submit_weight = $_GET['frm_submit_weight'];
    $submit_border_radius = $_GET['frm_submit_border_radius'];
    $submit_margin = $_GET['frm_submit_margin'];
    $submit_padding = $_GET['frm_submit_padding'].' !important';
    $submit_shadow_color = $_GET['frm_submit_shadow_color'];
    
    $border_radius = $_GET['frm_border_radius'];
    
    $error_bg = $_GET['frm_error_bg'];
    $error_border = $_GET['frm_error_border'];
    $error_text = $_GET['frm_error_text'];
    $error_font_size = $_GET['frm_error_font_size'];
    
    $success_bg_color = $_GET['frm_success_bg_color'];
    $success_border_color = $_GET['frm_success_border_color'];
    $success_text_color = $_GET['frm_success_text_color'];
    $success_font_size = $_GET['frm_success_font_size'];
    
    $custom_css = $_GET['frm_custom_css'];
}else{    
    if(isset($use_saved) and $use_saved){
        $css = get_transient('frmpro_css');
        if($css){
            echo $css;
            die();
        }
    }

    global $frmpro_settings;
    extract((array)$frmpro_settings);
}
$label_margin = (int)$width + 15; ?>
.frm_forms.with_frm_style{max-width:<?php echo $form_width ?>;}
.with_frm_style, .with_frm_style form{text-align:<?php echo $form_align ?>;}
.with_frm_style fieldset{border:<?php echo $fieldset ?> solid #<?php echo $fieldset_color ?>;margin:0;padding:<?php echo $fieldset_padding ?>;}
.with_frm_style label.frm_primary_label{font-family:<?php echo stripslashes($font) ?>;font-size:<?php echo $font_size ?>;color:#<?php echo $label_color ?>;font-weight:<?php echo $weight ?>;text-align:<?php echo $align ?>;margin:0;padding:0;width:auto;display:block;}
.with_frm_style .form-field{margin-bottom:<?php echo $field_margin ?>;}
.with_frm_style .form-field.frm_col_field{clear:none;float:left;margin-right:20px;}
.with_frm_style p.description, .with_frm_style div.description, .with_frm_style div.frm_description, .with_frm_style .frm_error{margin:0;padding:0;font-family:<?php echo stripslashes($description_font) ?>;font-size:<?php echo
$description_font_size ?>;color:#<?php echo $description_color ?>;font-weight:<?php echo $description_weight ?>;text-align:<?php echo $description_align ?>;font-style:<?php echo $description_style ?>;max-width:100%;}
.with_frm_style .frm_left_container p.description, .with_frm_style .frm_left_container div.description, .with_frm_style .frm_left_container div.frm_description, .with_frm_style .frm_left_container .frm_error{margin-left:<?php echo $label_margin ?>px;}
.with_frm_style .form-field.frm_col_field div.frm_description{width:<?php echo ($field_width == '') ? 'auto' : $field_width ?>;max-width:100%;}
.with_frm_style .frm_left_container .attachment-thumbnail{clear:both;margin-left:<?php echo $label_margin ?>px;}
.with_frm_style .frm_right_container p.description, .with_frm_style .frm_right_container div.description, .with_frm_style .frm_right_container div.frm_description, .with_frm_style .frm_right_container .frm_error{margin-right:<?php echo $label_margin ?>px;}
.with_frm_style label.frm_primary_label{max-width:100%;}
.with_frm_style .frm_top_container label.frm_primary_label, .with_frm_style .frm_hidden_container label.frm_primary_label, .with_frm_style .frm_pos_top{display:block;float:none;width:auto;}
.with_frm_style .frm_inline_container label.frm_primary_label, .with_frm_style .frm_inline_container .frm_opt_container {display:inline;margin-right:10px;}
.with_frm_style .frm_left_container label.frm_primary_label{display:inline;float:left;margin-right:10px;width:<?php echo $width; ?>;}
.with_frm_style .frm_right_container label.frm_primary_label, .with_frm_style .frm_pos_right{display:inline;float:right;margin-left:10px;width:<?php echo $width; ?>;}
.with_frm_style .frm_none_container label.frm_primary_label, .with_frm_style .frm_pos_none{display:none;}
.with_frm_style .frm_hidden_container label.frm_primary_label, .with_frm_style .frm_pos_hidden{visibility:hidden;}
.with_frm_style .frm_10radio{margin-right:10px;text-align:center;float:left;}
.with_frm_style .frm_required{color:#<?php echo $required_color; ?>;font-weight:<?php echo $required_weight; ?>;}
.with_frm_style input.required{color:inherit;}
.with_frm_style input[type=text], .with_frm_style input[type=password], .with_frm_style input[type=email], .with_frm_style input[type=number], .with_frm_style input[type=url], .with_frm_style input[type=tel], .with_frm_style select, .with_frm_style textarea, #content .with_frm_style input:not([type=submit]), #content .with_frm_style select, #content .with_frm_style textarea, .with_frm_style .chzn-container{font-family:<?php echo stripslashes($font) ?>;font-size:<?php echo $field_font_size ?>;margin-bottom:0;}
.with_frm_style input[type=text], .with_frm_style input[type=password], .with_frm_style input[type=email], .with_frm_style input[type=number], .with_frm_style input[type=url], .with_frm_style input[type=tel], .with_frm_style select, .with_frm_style textarea, .frm_form_fields_style, .with_frm_style .frm_scroll_box .frm_opt_container, .frm_form_fields_active_style, .frm_form_fields_error_style, .with_frm_style .chzn-container-multi .chzn-choices, .with_frm_style .chzn-container-single .chzn-single{color:#<?php echo $text_color ?>;background-color:#<?php echo $bg_color ?>;border-color:#<?php echo $border_color ?>;border-width:<?php echo $field_border_width ?>;border-style:<?php echo $field_border_style ?>;-moz-border-radius:<?php echo $border_radius ?>;-webkit-border-radius:<?php echo $border_radius ?>;border-radius:<?php echo $border_radius ?>;width:<?php echo ($field_width == '') ? 'auto' : $field_width ?>;max-width:100%;font-size:<?php echo $field_font_size ?>;padding:<?php echo $field_pad ?>;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;}
.with_frm_style input[type=file]{color:#<?php echo $text_color ?>;}
.with_frm_style .frm_default, .with_frm_style .chzn-container-multi .chzn-choices .search-field .default, .with_frm_style .chzn-container-single .chzn-default{color:#<?php echo $text_color ?>;font-style:italic;}
.with_frm_style select{width:<?php echo ($auto_width) ? 'auto' : $field_width ?>;max-width:100%;}
.with_frm_style input[type=radio], .with_frm_style input[type=checkbox]{width:auto;border:none;background:transparent;padding:0;}
.with_frm_style .frm_catlevel_2, .with_frm_style .frm_catlevel_3, .with_frm_style .frm_catlevel_4, .with_frm_style .frm_catlevel_5{margin-left:18px;}
.with_frm_style .wp-editor-wrap{width:<?php echo $field_width ?>;max-width:100%;}
.with_frm_style .quicktags-toolbar input{font-size:12px !important;}
.with_frm_style .wp-editor-container textarea{border:none;}
.with_frm_style .mceIframeContainer{background-color:#<?php echo $bg_color ?>;}
.with_frm_style .nicEdit-selectTxt{line-height:14px;}
.with_frm_style .nicEdit-panelContain{border-color:#<?php echo $border_color ?> !important;}
.with_frm_style .nicEdit-main{margin:0 !important;padding:4px;width:auto !important;outline:none;color:#<?php echo $text_color ?>;background-color:#<?php echo $bg_color ?>;border-color:#<?php echo $border_color ?> !important;border-width:1px;border-style:<?php echo $field_border_style ?>;border-top:none;}
.with_frm_style input.auto_width, .with_frm_style select.auto_width, .with_frm_style textarea.auto_width{width:auto;}
.with_frm_style input[disabled], .with_frm_style select[disabled], .with_frm_style textarea[disabled], .with_frm_style input[readonly], .with_frm_style select[readonly], .with_frm_style textarea[readonly]{opacity:.5;filter:alpha(opacity=50);}
.frm_set_select .with_frm_style select, .frm_set_select .with_frm_style select.auto_width{width:100%;}
.with_frm_style .form-field input:focus, .with_frm_style select:focus, .with_frm_style textarea:focus, .with_frm_style .frm_focus_field input[type=text], .with_frm_style .frm_focus_field input[type=password], .with_frm_style .frm_focus_field input[type=email], .with_frm_style .frm_focus_field input[type=number], .with_frm_style .frm_focus_field input[type=url], .with_frm_style .frm_focus_field input[type=tel], .frm_form_fields_active_style, .with_frm_style .chzn-container-active .chzn-choices{background-color:#<?php echo $bg_color_active ?>;border-color:#<?php echo $border_color_active ?>;}
<?php if(!$submit_style){ ?>
.with_frm_style .frm_submit input[type=submit], .frm_form_submit_style{width:<?php echo ($submit_width == '') ? 'auto' : $submit_width ?>;font-family:<?php echo stripslashes($font) ?>;font-size:<?php echo $submit_font_size; ?>;height:<?php echo $submit_height ?>;text-align:center;background:#<?php echo $submit_bg_color ?> url(<?php echo $submit_bg_img ?>);border-width:<?php echo $submit_border_width ?>;border-color:#<?php echo $submit_border_color ?>;border-style:solid;color:#<?php echo $submit_text_color ?>;cursor:pointer;font-weight:<?php echo $submit_weight ?>;-moz-border-radius:<?php echo $submit_border_radius ?>;-webkit-border-radius:<?php echo $submit_border_radius ?>;border-radius:<?php echo $submit_border_radius ?>;text-shadow:none;padding:<?php echo $submit_padding ?>;-moz-box-sizing:content-box;box-sizing:content-box;-ms-box-sizing:content-box;<?php if(empty($submit_bg_img)){ ?>filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#<?php echo $submit_bg_color ?>', endColorstr='#<?php echo $submit_bg_color2 ?>');background:-webkit-gradient(linear, left top, left bottom, from(#<?php echo $submit_bg_color ?>), to(#<?php echo $submit_bg_color2 ?>));background:-moz-linear-gradient(top, #<?php echo $submit_bg_color ?>, #<?php echo $submit_bg_color2 ?>);<?php } ?>-moz-box-shadow:1px 2px 3px #<?php echo $submit_shadow_color; ?>;-webkit-box-shadow:1px 2px 3px #<?php echo $submit_shadow_color; ?>;box-shadow:1px 2px 3px #<?php echo $submit_shadow_color; ?>;-ms-filter:"progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=135, Color='#<?php echo $submit_shadow_color; ?>')";filter:progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=135, Color='#<?php echo $submit_shadow_color; ?>');}
.with_frm_style p.submit, .with_frm_style div.frm_submit{padding-top:<?php echo $submit_margin ?>;padding-bottom:<?php echo $submit_margin ?>}
<?php if(empty($submit_bg_img)){ ?>.with_frm_style .frm_submit input[type=submit]:focus{filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#<?php echo $submit_bg_color2 ?>', endColorstr='#<?php echo $submit_bg_color ?>');background:-webkit-gradient(linear, left top, left bottom, from(#<?php echo $submit_bg_color2 ?>), to(#<?php echo $submit_bg_color ?>));background:-moz-linear-gradient(top, #<?php echo $submit_bg_color2 ?>, #<?php echo $submit_bg_color ?>);}
<?php } 
} ?>
.frm_ajax_loading{visibility:hidden;}
.frm_form_submit_style{height:auto;}
.with_frm_style .frm_radio{display:<?php echo $radio_align ?>;}
.with_frm_style .frm_left_container .frm_radio{margin<?php echo ($radio_align == 'block') ? "-left:{$label_margin}px;" : ':0'; ?>}
.with_frm_style .frm_right_container .frm_radio{margin<?php echo ($radio_align == 'block') ? "-right:{$label_margin}px;" : ':0'; ?>}
.with_frm_style .horizontal_radio .frm_radio {margin:0;margin-right: 5px;}
.with_frm_style .frm_checkbox{display:<?php echo $check_align ?>;}
.with_frm_style .frm_left_container .frm_checkbox{margin<?php echo ($check_align == 'block') ? "-left:{$label_margin}px;" : ':0'; ?>}
.with_frm_style .frm_right_container .frm_checkbox{margin<?php echo ($check_align == 'block') ? "-right:{$label_margin}px;" : ':0'; ?>}
.with_frm_style .horizontal_radio .frm_checkbox {margin:0;margin-right: 5px;}
.with_frm_style .vertical_radio .frm_checkbox, .with_frm_style .vertical_radio .frm_radio, .vertical_radio .frm_catlevel_1{display:block;}
.with_frm_style .horizontal_radio .frm_checkbox, .with_frm_style .horizontal_radio .frm_radio, .horizontal_radio .frm_catlevel_1{display:inline;}
.with_frm_style .frm_radio label, .with_frm_style .frm_checkbox label{font-family:<?php echo stripslashes($check_font) ?>;font-size:<?php echo $check_font_size ?>;color:#<?php echo $check_label_color ?>;font-weight:<?php echo $check_weight ?>;display:inline;}
.with_frm_style .frm_radio label .frm_file_container, .with_frm_style .frm_checkbox label .frm_file_container{display:inline-block;margin:5px;vertical-align:middle;}
.with_frm_style .frm_radio input[type=radio], .with_frm_style .frm_checkbox input[type=checkbox]{margin-right:5px;width:auto;}
.with_frm_style input[type=radio],.with_frm_style input[type=checkbox]{width:auto;}
.with_frm_style .frm_blank_field input[type=text], .with_frm_style .frm_blank_field input[type=password], .with_frm_style .frm_blank_field input[type=url], .with_frm_style .frm_blank_field input[type=tel], .with_frm_style .frm_blank_field input[type=number], .with_frm_style .frm_blank_field input[type=email], .with_frm_style .frm_blank_field textarea, .with_frm_style .frm_blank_field select, .frm_form_fields_error_style, .with_frm_style .frm_blank_field #recaptcha_area, .with_frm_style .frm_blank_field .chzn-container-multi .chzn-choices{color:#<?php echo $text_color_error ?>;background-color:#<?php echo $bg_color_error ?>;border-color:#<?php echo $border_color_error ?>;border-width:<?php echo $border_width_error ?>;border-style:<?php echo $border_style_error ?>;}
.with_frm_style :invalid, .with_frm_style :-moz-submit-invalid, .with_frm_style :-moz-ui-invalid {box-shadow:none;}
.with_frm_style .frm_error{font-weight:<?php echo $weight ?>;}
.with_frm_style .frm_blank_field label, .with_frm_style .frm_error{color:#<?php echo $border_color_error ?>;}
.with_frm_style .frm_error_style{background-color:#<?php echo $error_bg ?>;border:2px solid #<?php echo $error_border ?>;color:#<?php echo $error_text ?>;font-size:<?php echo $error_font_size ?>;margin:0;margin-bottom:<?php echo $field_margin ?>;padding:5px 10px;}
.with_frm_style .frm_error_style img{padding-right:10px;vertical-align:middle;border:none;}
.with_frm_style .frm_trigger{cursor:pointer;}
.with_frm_style .frm_message, .frm_success_style{border:1px solid #<?php echo $success_border_color ?>;background-color:#<?php echo $success_bg_color ?>;color:#<?php echo $success_text_color ?>;}
.with_frm_style .frm_message{padding:5px 10px;margin:5px 0 15px;font-size:<?php echo $success_font_size ?>;}
.frm_form_fields_style, .frm_form_fields_active_style, .frm_form_fields_error_style, .frm_form_submit_style{width:auto;}
.with_frm_style .frm_trigger span{float:left;}
.with_frm_style table.frm-grid, #content .with_frm_style table.frm-grid{border-collapse:collapse;border:none;}
.with_frm_style .frm-grid td, .frm-grid th{padding:5px;border-width:1px;border-style:solid;border-color:#<?php echo $border_color ?>;border-top:none;border-left:none;border-right:none;}
.form_results.with_frm_style{border-color:<?php echo $field_border_width ?> solid #<?php echo $border_color ?>;}
.form_results.with_frm_style tr td{text-align:left;color:#<?php echo $text_color ?>;padding:7px 9px;border-top:<?php echo $field_border_width ?> solid #<?php echo $border_color ?>;}
.form_results.with_frm_style tr.frm_even, .frm-grid .frm_even{background-color:#<?php echo $bg_color ?>;}
.form_results.with_frm_style tr.frm_odd, .frm-grid .frm_odd{background-color:#<?php echo $bg_color_active ?>;}
.with_frm_style .frm_uploaded_files{padding:5px 0;}
.with_frm_style .frm_file_names{display:block;}
#frm_loading{display:none;position:fixed;top:0;left:0;width:100%;height:100%;}
#frm_loading h3{font-weight:bold;padding-bottom:15px;}
#frm_loading_content{position:fixed;top:20%;left:33%;width:33%;text-align:center;color:#<?php echo $text_color ?>;background:#<?php echo $bg_color_active ?>;border:2px solid #<?php echo $border_color_active ?>;padding:30px;font-weight:bold;}
.frmcal{padding-top:30px;}
.frmcal-title{font-size:116%;}
.frmcal table.frmcal-calendar{border-collapse:collapse;margin-top:20px;color:#<?php echo $text_color ?>;}
.frmcal table.frmcal-calendar, .frmcal table.frmcal-calendar tbody tr td{border:1px solid #<?php echo $border_color ?>;}
.frmcal table.frmcal-calendar, .frmcal, .frmcal-header{width:100%;}
.frmcal-header{text-align:center;}
.frmcal-prev{margin-right:10px;}
.frmcal-prev, .frmcal-dropdown{float:left;}
.frmcal-dropdown{margin-left:5px;}
.frmcal-next{float:right;}
.frmcal table.frmcal-calendar thead tr th{text-align:center;padding:2px 4px;}
.frmcal table.frmcal-calendar tbody tr td{height:110px;width:14.28%;vertical-align:top;padding:0 !important;color:#<?php echo $text_color ?>;font-size:12px;}
table.frmcal-calendar .frmcal_date{background-color:#<?php echo $bg_color ?>;padding:0 5px;text-align:right;-moz-box-shadow:0 2px 5px #<?php echo $border_color ?>;-webkit-box-shadow:0 2px 5px #<?php echo $border_color ?>;box-shadow:0 2px 5px #<?php echo $border_color ?>;-ms-filter:"progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=180, Color='#<?php echo $border_color ?>')";filter:progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=180, Color='#<?php echo $border_color ?>');}
table.frmcal-calendar .frmcal-today .frmcal_date{background-color:#<?php echo $bg_color_active ?>;padding:0 5px;text-align:right;-moz-box-shadow:0 2px 5px #<?php echo $border_color_active ?>;-webkit-box-shadow:0 2px 5px #<?php echo $border_color_active ?>;box-shadow:0 2px 5px #<?php echo $border_color_active ?>;-ms-filter:"progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=180, Color='#<?php echo $border_color_active ?>')";filter:progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=180, Color='#<?php echo $border_color_active ?>');}
.frmcal_num{display:inline;}
.frmcal-content{padding:2px 4px;}
.frm-loading-img{background:url(<?php echo FRM_IMAGES_URL ?>/ajax_loader.gif) no-repeat center center;padding:6px 12px;}
#ui-datepicker-div{display:none;z-index:999 !important;}
.frm_form_fields div.rating-cancel, .frm_form_fields div.star-rating{float:left;width:17px;height:15px;text-indent:-999em;cursor:pointer;display:block;background:transparent;overflow:hidden;}
.frm_form_fields div.rating-cancel a{background:url(<?php echo FRMPRO_IMAGES_URL ?>/delete.png) no-repeat 0 -16px;}
.frm_form_fields div.star-rating, .frm_form_fields div.star-rating a{background:url(<?php echo FRMPRO_IMAGES_URL ?>/star.png) no-repeat 0 0px;}
.frm_form_fields div.rating-cancel a, .frm_form_fields div.star-rating a{display:block;width:16px;height:100%;background-position:0 0px;border:0;}
.frm_form_fields div.star-rating-on a{background-position:0 -16px!important;}
.frm_form_fields div.star-rating-hover a{background-position:0 -32px;}
.frm_form_fields div.rating-cancel.star-rating-hover a{background-position:0 -16px;}
.frm_form_fields div.star-rating-readonly a{cursor:default !important;}
.frm_form_fields div.star-rating{background:transparent!important;overflow:hidden!important;}
.with_frm_style .frm_form_field{clear:both;}
.frm_form_field.frm_third, .frm_form_field.frm_fourth, .frm_form_field.frm_fifth, .frm_form_field.frm_inline{clear:none;float:left;}
.frm_form_field.frm_left_half, .frm_form_field.frm_left_third, .frm_form_field.frm_left_two_thirds, .frm_form_field.frm_left_fourth, .frm_form_field.frm_left_fifth, .frm_form_field.frm_left_inline, .frm_form_field.frm_first_half, .frm_form_field.frm_first_third, .frm_form_field.frm_first_two_thirds, .frm_form_field.frm_first_fourth, .frm_form_field.frm_first_fifth, .frm_form_field.frm_first_inline{clear:left;float:left;}
.frm_form_field.frm_right_half, .frm_form_field.frm_right_third, .frm_form_field.frm_right_two_thirds, .frm_form_field.frm_right_fourth, .frm_form_field.frm_right_fifth, .frm_form_field.frm_right_inline, .frm_form_field.frm_last_half, .frm_form_field.frm_last_third, .frm_form_field.frm_last_two_thirds, .frm_form_field.frm_last_fourth, .frm_form_field.frm_last_fifth, .frm_form_field.frm_last_inline{clear:none;float:right;}
.frm_form_field.frm_left_half, .frm_form_field.frm_right_half, .frm_form_field.frm_first_half, .frm_form_field.frm_last_half{width:48%;}
.frm_form_field.frm_left_half, .frm_form_field.frm_first_half{margin-right:4%;}
.frm_form_field.frm_left_half.frm_left_container .frm_primary_label, .frm_form_field.frm_right_half.frm_left_container .frm_primary_label, .frm_form_field.frm_left_half.frm_right_container .frm_primary_label, .frm_form_field.frm_right_half.frm_right_container .frm_primary_label, .frm_form_field.frm_first_half.frm_left_container .frm_primary_label, .frm_form_field.frm_last_half.frm_left_container .frm_primary_label, .frm_form_field.frm_first_half.frm_right_container .frm_primary_label, .frm_form_field.frm_last_half.frm_right_container .frm_primary_label{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;max-width:25%;margin-right:0;margin-left:0;}
.frm_form_field.frm_left_half.frm_left_container .frm_primary_label, .frm_form_field.frm_right_half.frm_left_container .frm_primary_label, .frm_form_field.frm_first_half.frm_left_container .frm_primary_label, .frm_form_field.frm_last_half.frm_left_container .frm_primary_label{padding-right:10px;}
.frm_form_field.frm_left_half.frm_right_container .frm_primary_label, .frm_form_field.frm_right_half.frm_right_container .frm_primary_label, .frm_form_field.frm_first_half.frm_right_container .frm_primary_label, .frm_form_field.frm_last_half.frm_right_container .frm_primary_label{padding-left:10px;}
.frm_form_field.frm_left_container input, .frm_form_field.frm_left_container select, .frm_form_field.frm_left_container textarea, .frm_form_field.frm_right_container input, .frm_form_field.frm_right_container select, .frm_form_field.frm_right_container textarea{max-width:75%;}
.frm_form_field.frm_left_third, .frm_form_field.frm_third, .frm_form_field.frm_right_third, .frm_form_field.frm_first_third, .frm_form_field.frm_last_third{width:30%;}
.frm_form_field.frm_left_two_thirds, .frm_form_field.frm_right_two_thirds, .frm_form_field.frm_first_two_thirds, .frm_form_field.frm_last_two_thirds {width:65%;}
.frm_form_field.frm_left_third, .frm_form_field.frm_first_third, .frm_form_field.frm_third, .frm_form_field.frm_left_two_thirds, .frm_form_field.frm_first_two_thirds {margin-right:5%;}
.frm_form_field.frm_left_fourth, .frm_form_field.frm_fourth, .frm_form_field.frm_right_fourth, .frm_form_field.frm_first_fourth, .frm_form_field.frm_last_fourth{width:22%;}
.frm_form_field.frm_left_fourth, .frm_form_field.frm_fourth, .frm_form_field.frm_first_fourth{margin-right:4%;}
.frm_form_field.frm_left_fifth, .frm_form_field.frm_fifth, .frm_form_field.frm_right_fifth, .frm_form_field.frm_first_fifth, .frm_form_field.frm_last_fifth{width:16%;}
.frm_form_field.frm_left_fifth, .frm_form_field.frm_fifth, .frm_form_field.frm_first_fifth{margin-right:5%;}
.frm_form_field.frm_left_inline, .frm_form_field.frm_first_inline, .frm_form_field.frm_inline, .frm_form_field.frm_right_inline, .frm_form_field.frm_last_inline{width:auto;}
.frm_form_field.frm_left_inline, .frm_form_field.frm_first_inline, .frm_form_field.frm_inline{margin-right:4%;}
.frm_full, .frm_full .wp-editor-wrap, .frm_full input, .frm_full select, .frm_full textarea{width:100% !important;}
.frm_full .wp-editor-wrap input{width:auto !important;}
.frm_grid, .frm_grid_first, .frm_grid_odd{clear:both;margin-bottom:0 !important;padding:5px;border-width:1px;border-style:solid;border-color:#<?php echo $border_color ?>;border-left:none;border-right:none;}
.frm_grid, .frm_grid_odd{border-top:none;}
.frm_grid:after, .frm_grid_first:after, .frm_grid_odd:after{visibility:hidden;display:block;font-size:0;content:" ";clear:both;height:0;}
.frm_grid_first{margin-top:20px;}
.frm_grid_first, .frm_grid_odd{background-color:#<?php echo $bg_color ?>;}
.frm_grid{background-color:#<?php echo $bg_color_active ?>;}
.frm_grid .frm_primary_label, .frm_grid_first .frm_primary_label, .frm_grid_odd .frm_primary_label, .frm_grid .frm_radio, .frm_grid_first .frm_radio, .frm_grid_odd .frm_radio, .frm_grid .frm_checkbox, .frm_grid_first .frm_checkbox, .frm_grid_odd .frm_checkbox{float:left !important;display:block;}
.frm_grid_first .frm_radio label, .frm_grid .frm_radio label, .frm_grid_odd .frm_radio label, .frm_grid_first .frm_checkbox label, .frm_grid .frm_checkbox label, .frm_grid_odd .frm_checkbox label {display:none;}
.frm_grid .frm_radio, .frm_grid_first .frm_radio, .frm_grid_odd .frm_radio, .frm_grid .frm_checkbox, .frm_grid_first .frm_checkbox, .frm_grid_odd .frm_checkbox{display:inline; text-align:right;}
.frm_grid_2 .frm_radio, .frm_grid_2 .frm_checkbox, .frm_grid_2 label.frm_primary_label{width:48% !important;}
.frm_grid_2 .frm_radio, .frm_grid_2 .frm_checkbox{margin-right:4%;}
.frm_grid_3 .frm_radio, .frm_grid_3 .frm_checkbox, .frm_grid_3 label.frm_primary_label{width:30% !important;}
.frm_grid_3 .frm_radio, .frm_grid_3 .frm_checkbox{margin-right:3%;}
.frm_grid_4 .frm_radio, .frm_grid_4 .frm_checkbox{width:20% !important;}
.frm_grid_4 label.frm_primary_label{width:28% !important;}
.frm_grid_4 .frm_radio, .frm_grid_4 .frm_checkbox{margin-right:4%;}
.frm_grid_5 label.frm_primary_label, .frm_grid_7 label.frm_primary_label{width:24% !important;}
.frm_grid_5 .frm_radio, .frm_grid_5 .frm_checkbox{width:17% !important;margin-right:2%;}
.frm_grid_6 label.frm_primary_label{width:25% !important;}
.frm_grid_6 .frm_radio, .frm_grid_6 .frm_checkbox{width:14% !important;margin-right:1%;}
.frm_grid_7 label.frm_primary_label{width:22% !important;}
.frm_grid_7 .frm_radio, .frm_grid_7 .frm_checkbox{width:12% !important;margin-right:1%;}
.frm_grid_8 label.frm_primary_label{width:23% !important;}
.frm_grid_8 .frm_radio, .frm_grid_8 .frm_checkbox{width:10% !important;margin-right:1%;}
.with_frm_style .frm_inline_container.frm_grid_first label.frm_primary_label, .with_frm_style .frm_inline_container.frm_grid label.frm_primary_label, .with_frm_style .frm_inline_container.frm_grid_odd label.frm_primary_label, .with_frm_style .frm_inline_container.frm_grid_first .frm_opt_container, .with_frm_style .frm_inline_container.frm_grid .frm_opt_container, .with_frm_style .frm_inline_container.frm_grid_odd .frm_opt_container {margin-right:0;}
.frm_form_field.frm_two_col .frm_radio, .frm_form_field.frm_three_col .frm_radio, .frm_form_field.frm_four_col .frm_radio, .frm_form_field.frm_two_col .frm_checkbox, .frm_form_field.frm_three_col .frm_checkbox, .frm_form_field.frm_four_col .frm_checkbox {float:left;}
.frm_form_field.frm_two_col .frm_radio, .frm_form_field.frm_two_col .frm_checkbox{width:48%;margin-right:2%;}
.frm_form_field.frm_three_col .frm_radio, .frm_form_field.frm_three_col .frm_checkbox{width:31%;margin-right:2%;}
.frm_form_field.frm_four_col .frm_radio, .frm_form_field.frm_four_col .frm_checkbox{width:22%;margin-right:3%;}
.frm_form_field.frm_scroll_box .frm_opt_container{height:100px;overflow:auto;}
.frm_form_field.frm_two_col .frm_opt_container:after, .frm_form_field.frm_three_col .frm_opt_container:after, .frm_form_field.frm_four_col .frm_opt_container:after{content:".";display:block;clear:both;visibility:hidden;line-height:0;height:0;}
.frm_form_field.frm_total input, .frm_form_field.frm_total textarea{opacity: 1; background-color:transparent; border: none; font-weight:bold; -moz-box-shadow:none;-webkit-box-shadow:none;box-shadow:none;}

.with_frm_style .chzn-container{font-size:<?php echo $field_font_size ?>;position:relative;display:inline-block;zoom:1;
*display: inline;}
.with_frm_style .chzn-container .chzn-drop{
background:#fff;
  border:1px solid #aaa;
  border-top:0;position:absolute;top:29px;left:0;
  -webkit-box-shadow: 0 4px 5px rgba(0,0,0,.15);
  -moz-box-shadow   : 0 4px 5px rgba(0,0,0,.15);
  box-shadow        : 0 4px 5px rgba(0,0,0,.15);
  z-index:1010;
}
.with_frm_style .chzn-container-single .chzn-single{
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#eeeeee', GradientType=0 );   
  background-image: -webkit-gradient(linear, 0 0, 0 100%, color-stop(20%, #ffffff), color-stop(50%, #f6f6f6), color-stop(52%, #eeeeee), color-stop(100%, #f4f4f4));
  background-image: -webkit-linear-gradient(top, #ffffff 20%, #f6f6f6 50%, #eeeeee 52%, #f4f4f4 100%);
  background-image: -moz-linear-gradient(top, #ffffff 20%, #f6f6f6 50%, #eeeeee 52%, #f4f4f4 100%);
  background-image: -o-linear-gradient(top, #ffffff 20%, #f6f6f6 50%, #eeeeee 52%, #f4f4f4 100%);
  background-image: linear-gradient(#ffffff 20%, #f6f6f6 50%, #eeeeee 52%, #f4f4f4 100%); 
  -moz-background-clip:padding;-webkit-background-clip:padding-box;background-clip:padding-box;
  -webkit-box-shadow: 0 0 3px #ffffff inset, 0 1px 1px rgba(0,0,0,0.1);
  -moz-box-shadow   : 0 0 3px #ffffff inset, 0 1px 1px rgba(0,0,0,0.1);
  box-shadow        : 0 0 3px #ffffff inset, 0 1px 1px rgba(0,0,0,0.1);
  display:block;overflow:hidden;white-space:nowrap;position:relative;height:23px;line-height:24px;padding:0 0 0 8px;
  text-decoration:none;}
.with_frm_style .chzn-container-single .chzn-single span{margin-right:26px;display:block;overflow:hidden;white-space:nowrap;-o-text-overflow:ellipsis;-ms-text-overflow:ellipsis;text-overflow:ellipsis;}
.with_frm_style .chzn-container-single .chzn-single abbr{display:block;position:absolute;right:26px;top:6px;width:12px;height:12px;font-size:1px;background:url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') -42px 1px no-repeat;}
.with_frm_style .chzn-container-single .chzn-single abbr:hover{background-position:-42px -10px;}
.with_frm_style .chzn-container-single.chzn-disabled .chzn-single abbr:hover{background-position:-42px -10px;}
.with_frm_style .chzn-container-single .chzn-single div{position:absolute;right:0;top:0;display:block;height:100%;width:18px;}
.with_frm_style .chzn-container-single .chzn-single div b{background:url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat 0px 2px;display:block;width:100%;height:100%;}
.with_frm_style .chzn-container-single .chzn-search{padding:3px 4px;position:relative;margin:0;white-space:nowrap;z-index:1010;}
.with_frm_style .chzn-container-single .chzn-search input{background:#fff url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat 100% -20px;background:url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat 100% -20px, -webkit-gradient(linear, 0 0, 0 100%, color-stop(1%, #eeeeee), color-stop(15%, #ffffff));background:url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat 100% -20px, -webkit-linear-gradient(top, #eeeeee 1%, #ffffff 15%);background:url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat 100% -20px, -moz-linear-gradient(top, #eeeeee 1%, #ffffff 15%);background:url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat 100% -20px, -o-linear-gradient(top, #eeeeee 1%, #ffffff 15%);background:url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat 100% -20px, linear-gradient(#eeeeee 1%, #ffffff 15%);margin:1px 0;padding:4px 20px 4px 5px;outline:0;
  border: 1px solid #aaa;}
.with_frm_style .chzn-container-single .chzn-drop{-webkit-border-radius:0 0 4px 4px;-moz-border-radius:0 0 4px 4px;border-radius:0 0 4px 4px;-moz-background-clip:padding;-webkit-background-clip:padding-box;background-clip:padding-box;}
.with_frm_style .chzn-container-single-nosearch .chzn-search input{position: absolute;left:-9000px;}
.with_frm_style .chzn-container-multi .chzn-choices{margin:0;cursor:text;overflow:hidden;height:auto !important;height:1%;position:relative;}
.with_frm_style .chzn-container-multi .chzn-choices li{float: left;list-style: none;}
.with_frm_style .chzn-container-multi .chzn-choices .search-field{white-space:nowrap;margin:0;padding:2px 0 0 5px;}
.with_frm_style .chzn-container-multi .chzn-choices .search-field input{
  color: #666;
  background:transparent !important;border:0 !important;
  font-size:100%;height:15px;margin:1px 0;outline:0;-webkit-box-shadow: none;-moz-box-shadow:none;box-shadow:none;padding:0;}
.with_frm_style .chzn-container-multi .chzn-choices .search-choice{-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;-moz-background-clip:padding;-webkit-background-clip:padding-box;background-clip:padding-box;
  background-color: #e4e4e4;
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f4f4f4', endColorstr='#eeeeee', GradientType=0 ); 
  background-image: -webkit-gradient(linear, 0 0, 0 100%, color-stop(20%, #f4f4f4), color-stop(50%, #f0f0f0), color-stop(52%, #e8e8e8), color-stop(100%, #eeeeee));
  background-image: -webkit-linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);
  background-image: -moz-linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);
  background-image: -o-linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);
  background-image: linear-gradient(#f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%); 
  -webkit-box-shadow: 0 0 2px #ffffff inset, 0 1px 0 rgba(0,0,0,0.05);
  -moz-box-shadow   : 0 0 2px #ffffff inset, 0 1px 0 rgba(0,0,0,0.05);
  box-shadow        : 0 0 2px #ffffff inset, 0 1px 0 rgba(0,0,0,0.05);
  color:#333;
  border: 1px solid #aaaaaa;
  line-height:13px;padding:3px 20px 3px 5px;margin:0 0 0 5px;position:relative;cursor:default;}
.with_frm_style .chzn-container-multi .chzn-choices .search-choice.search-choice-disabled{
  background-color: #e4e4e4;
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f4f4f4', endColorstr='#eeeeee', GradientType=0 );
  background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, color-stop(20%, #f4f4f4), color-stop(50%, #f0f0f0), color-stop(52%, #e8e8e8), color-stop(100%, #eeeeee));
  background-image: -webkit-linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);
  background-image: -moz-linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);
  background-image: -o-linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);
  background-image: -ms-linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);
  background-image: linear-gradient(top, #f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eeeeee 100%);
  color: #666;
  border: 1px solid #cccccc;
  padding-right:5px;}
.with_frm_style .chzn-container-multi .chzn-choices .search-choice-focus{background: #d4d4d4;}
.with_frm_style .chzn-container-multi .chzn-choices .search-choice .search-choice-close{display:block;position:absolute;right:3px;top:4px;width:12px;height:12px;font-size:1px;background: url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') -42px 1px no-repeat;}
.with_frm_style .chzn-container-multi .chzn-choices .search-choice .search-choice-close:hover{background-position:-42px -10px;}
.with_frm_style .chzn-container-multi .chzn-choices .search-choice-focus .search-choice-close{background-position:-42px -10px;}
.with_frm_style .chzn-container .chzn-results {margin:0 4px 4px 0;max-height:240px;padding:0 0 0 4px;position:relative;overflow-x:hidden;overflow-y:auto;-webkit-overflow-scrolling:touch;}
.with_frm_style .chzn-container-multi .chzn-results{margin:-1px 0 0;padding:0;}
.with_frm_style .chzn-container .chzn-results li{display:none;line-height:15px;padding:5px 6px;margin:0;list-style:none;}
.with_frm_style .chzn-container .chzn-results .active-result{cursor:pointer;display:list-item;}
.with_frm_style .chzn-container .chzn-results .highlighted{
  background-color:#3875d7;
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#3875d7', endColorstr='#2a62bc', GradientType=0 );  
  background-image:-webkit-gradient(linear, 0 0, 0 100%, color-stop(20%, #3875d7), color-stop(90%, #2a62bc));
  background-image:-webkit-linear-gradient(top, #3875d7 20%, #2a62bc 90%);
  background-image:-moz-linear-gradient(top, #3875d7 20%, #2a62bc 90%);
  background-image:-o-linear-gradient(top, #3875d7 20%, #2a62bc 90%);
  background-image:linear-gradient(#3875d7 20%, #2a62bc 90%);
  color:#fff;
}
.with_frm_style .chzn-container .chzn-results li em{
  background:#feffde;
  font-style:normal;
}
.with_frm_style .chzn-container .chzn-results .highlighted em{background:transparent;}
.with_frm_style .chzn-container .chzn-results .no-results{
  background:#f4f4f4;
  display:list-item;}
.with_frm_style .chzn-container .chzn-results .group-result{cursor:default;
  color:#999;
  font-weight:bold;}
.with_frm_style .chzn-container .chzn-results .group-option{padding-left: 15px;}
.with_frm_style .chzn-container-multi .chzn-drop .result-selected{display: none;}
.with_frm_style .chzn-container .chzn-results-scroll{
  background: white;
margin:0 4px;position:absolute;text-align:center;width: 321px;z-index: 1;}
.with_frm_style .chzn-container .chzn-results-scroll span{display:inline-block;height:17px;text-indent:-5000px;width:9px;}
.with_frm_style .chzn-container .chzn-results-scroll-down{bottom:0;}
.with_frm_style .chzn-container .chzn-results-scroll-down span{background: url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat -4px -3px;}
.with_frm_style .chzn-container .chzn-results-scroll-up span{background: url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat -22px -3px;}
.with_frm_style .chzn-container-active .chzn-single{
  -webkit-box-shadow:0 0 5px rgba(0,0,0,.3);
  -moz-box-shadow:0 0 5px rgba(0,0,0,.3);
  box-shadow:0 0 5px rgba(0,0,0,.3);
border:1px solid #<?php echo $border_color_active ?>;}
.with_frm_style .chzn-container-active .chzn-single-with-drop{
  border:1px solid #aaa;
  -webkit-box-shadow:0 1px 0 #fff inset;
  -moz-box-shadow:0 1px 0 #fff inset;
  box-shadow:0 1px 0 #fff inset;
  background-color:#eee;
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#eeeeee', endColorstr='#ffffff', GradientType=0 );
  background-image: -webkit-gradient(linear, 0 0, 0 100%, color-stop(20%, #eeeeee), color-stop(80%, #ffffff));
  background-image: -webkit-linear-gradient(top, #eeeeee 20%, #ffffff 80%);
  background-image: -moz-linear-gradient(top, #eeeeee 20%, #ffffff 80%);
  background-image: -o-linear-gradient(top, #eeeeee 20%, #ffffff 80%);
  background-image: linear-gradient(#eeeeee 20%, #ffffff 80%);
  -webkit-border-bottom-left-radius:0;-webkit-border-bottom-right-radius:0;-moz-border-radius-bottomleft:0;-moz-border-radius-bottomright:0;border-bottom-left-radius:0;border-bottom-right-radius:0;}
.with_frm_style .chzn-container-active .chzn-single-with-drop div{background:transparent;border-left:none;}
.with_frm_style .chzn-container-active .chzn-single-with-drop div b{background-position:-18px 2px;}
.with_frm_style .chzn-container-active .chzn-choices .search-field input{color: #111 !important;}
.with_frm_style .chzn-disabled{cursor:default;opacity:0.5 !important;}
.with_frm_style .chzn-disabled .chzn-single{cursor:default;}
.with_frm_style .chzn-disabled .chzn-choices .search-choice .search-choice-close{cursor:default;}
.with_frm_style .chzn-rtl{text-align:right;}
.with_frm_style .chzn-rtl .chzn-single{padding:0 8px 0 0;overflow:visible;}
.with_frm_style .chzn-rtl .chzn-single span{margin-left:26px;margin-right:0;direction:rtl;}
.with_frm_style .chzn-rtl .chzn-single div{left:3px;right:auto;}
.with_frm_style .chzn-rtl .chzn-single abbr{left:26px;right:auto;}
.with_frm_style .chzn-rtl .chzn-choices .search-field input{direction:rtl;}
.with_frm_style .chzn-rtl .chzn-choices li{float:right;}
.with_frm_style .chzn-rtl .chzn-choices .search-choice{padding:3px 5px 3px 19px;margin:3px 5px 3px 0;}
.with_frm_style .chzn-rtl .chzn-choices .search-choice .search-choice-close{left:4px;right:auto;}
.with_frm_style .chzn-rtl.chzn-container-single .chzn-results{margin: 0 0 4px 4px;padding:0 4px 0 0;}
.with_frm_style .chzn-rtl .chzn-results .group-option{padding-left:0;padding-right:15px;}
.with_frm_style .chzn-rtl.chzn-container-active .chzn-single-with-drop div{border-right:none;}
.with_frm_style .chzn-rtl .chzn-search input{background:#fff url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat -30px -20px;background:url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat -30px -20px, -webkit-gradient(linear, 0 0, 0 100%, color-stop(1%, #eeeeee), color-stop(15%, #ffffff));background:url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat -30px -20px, -webkit-linear-gradient(top, #eeeeee 1%, #ffffff 15%);  background:url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat -30px -20px, -moz-linear-gradient(top, #eeeeee 1%, #ffffff 15%);background:url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat -30px -20px, -o-linear-gradient(top, #eeeeee 1%, #ffffff 15%);background:url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite.png') no-repeat -30px -20px, linear-gradient(#eeeeee 1%, #ffffff 15%);padding:4px 5px 4px 20px;direction:rtl;}
.chzn-container-single.chzn-rtl .chzn-single div b{background-position:6px 2px;}
.with_frm_style .chzn-container-single.chzn-rtl .chzn-single-with-drop div b{background-position:-12px 2px;}
@media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min-resolution: 144dpi){
.with_frm_style .chzn-rtl .chzn-search input, .with_frm_style .chzn-container-single .chzn-single abbr, .with_frm_style .chzn-container-single .chzn-single div b, .with_frm_style .chzn-container-single .chzn-search input, .with_frm_style .chzn-container-multi .chzn-choices .search-choice .search-choice-close, .with_frm_style .chzn-container .chzn-results-scroll-down span, .with_frm_style .chzn-container .chzn-results-scroll-up span{background-image: url('<?php echo FRMPRO_IMAGES_URL ?>/chosen-sprite2x.png') !important;background-repeat:no-repeat !important;background-size:52px 37px !important;}
}
<?php echo $custom_css; ?>