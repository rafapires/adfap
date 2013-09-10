<div id="taxonomy-linkcategory" class="categorydiv">
	<ul id="category-tabs" class="category-tabs frm-category-tabs">
		<li class="tabs" ><a href="#frm-insert-fields-box" id="frm_insert_fields_tab"><?php _e( 'Insert Fields', 'formidable' ); ?></a></li>
		<li class="hide-if-no-js"><a href="#frm-conditionals"><?php _e( 'Conditionals', 'formidable' ); ?></a></li>
		<li class="hide-if-no-js"><a href="#frm-adv-info-tab"><?php _e( 'Advanced', 'formidable' ); ?></a></li>
		<?php if($settings_tab){ ?>
		<li id="frm_html_tab" class="hide-if-no-js" style="display:none;"><a href="#frm-html-tags" id="frm_html_tags_tab" ><?php _e( 'HTML Tags', 'formidable' ); ?></a></li>
		<?php } ?>
	</ul>

	<div id="frm-insert-fields-box" class="tabs-panel" style="max-height:none;padding-right:0;">
	    <ul class="subsubsub" style="float:right;margin:0;">
            <li><a class="current frmids" onclick="frmToggleKeyID('frmids');"><?php _e('IDs', 'formidable') ?></a> |</li>
            <li><a class="frmkeys" onclick="frmToggleKeyID('frmkeys');"><?php _e('Keys', 'formidable') ?></a></li>
        </ul>
        <ul class="alignleft" style="margin:5px 0 0;"><li><?php _e('Fields from your form', 'formidable') ?>:</li></ul>
        <ul class="frm_code_list frm_full_width" style="clear:both;max-height:150px;overflow:auto;">
        <?php
            if(!empty($fields)){
                global $frmdb;
                $linked_forms[] = array();

                foreach($fields as $f){ 
                    if(in_array($f->type, array('divider', 'captcha', 'break', 'html')))
                        continue;

                    $f->field_options = maybe_unserialize($f->field_options);
                    if($f->type == 'data' and (!isset($f->field_options['data_type']) or $f->field_options['data_type'] == 'data' or $f->field_options['data_type'] == ''))
                        continue;

                FrmAppHelper::insert_opt_html(array(
                    'id' => $f->id, 'key' => $f->field_key, 'name' => $f->name, 'type' => $f->type
                ));

        	    if($f->type == 'data'){ //get all fields from linked form
                    if (isset($f->field_options['form_select']) && is_numeric($f->field_options['form_select'])){
                        $linked_form = $frmdb->get_var($frmdb->fields, array('id' => $f->field_options['form_select']), 'form_id');
                        if(!in_array($linked_form, $linked_forms)){
                            $linked_forms[] = $linked_form;
                            $linked_fields = $frm_field->getAll("fi.type not in ('divider','captcha','break','html') and fi.form_id =". (int)$linked_form);
                            $ldfe = '';
                            if($linked_fields){ 
                                foreach ($linked_fields as $linked_field){ 
                                    FrmAppHelper::insert_opt_html(array('id' => $f->id ." show=". $linked_field->id, 'key' => $f->field_key ." show=". $linked_field->field_key, 'name' => $linked_field->name, 'type' => $linked_field->type));

                                    $ldfe = $linked_field->id;
                                    unset($linked_field);
                                } 
                            }
                        } 
                    }
                    $dfe = $f->id;
        	    }       
                unset($f);
                }
            } ?>
        </ul>

        <?php _e('Helpers', 'formidable') ?>:
        <ul class="frm_code_list">
        <?php
        $col = 'one';
        $entry_shortcodes = array('id' => __('Entry ID', 'formidable'), 
            'key' => __('Entry Key', 'formidable'),
            'post_id' => __('Post ID', 'formidable'),
            'ip' => __('User IP', 'formidable'),
            'created-at' => __('Entry created at', 'formidable'),
            'updated-at' => __('Entry updated at', 'formidable'),
            '' => '',
            'siteurl' => __('Site URL', 'formidable'),
            'sitename' => __('Site Name', 'formidable')
        );

        if($settings_tab){
            $entry_shortcodes['default-message'] = __('Default Message', 'formidable');
            $entry_shortcodes['default-html'] = __('Default HTML', 'formidable');
            $entry_shortcodes['default-plain'] = __('Default Plain', 'formidable');
        }else{
            $entry_shortcodes['detaillink'] = __('Single Entry Link', 'formidable');
            $entry_shortcodes['editlink location=&#34;front&#34; label=&#34;Edit&#34; page_id=4'] = __('Edit Entry Link', 'formidable');
            $entry_shortcodes['evenodd'] = __('Rotate even/odd', 'formidable');
        }


        foreach($entry_shortcodes as $skey => $sname){
             if(empty($skey)){
                 $col = 'one';
                 echo '<li class="clear" style="display:block;height:10px;"></li>';
                 continue;
            }
        ?>
        <li class="frm_col_<?php echo $col ?>">
            <a class="frmbutton button <?php 
            echo ($skey == 'siteurl' or $skey == 'sitename') ? 'show_before_content show_after_content' : '';
            echo (strpos($skey, 'default-') === 0) ? 'hide_frm_not_email_subject' : '';
            ?>" onclick="frmInsertFieldCode(jQuery(this),'<?php echo $skey ?>');return false;" href="#"><?php echo $sname ?></a>
        </li>
        <?php
            $col = ($col == 'one') ? 'two' : 'one';
            unset($skey);
            unset($sname);
        }
        ?>
        </ul>

        <script type="text/javascript">jQuery(document).ready(function($){ $('.frm_code_list .frmkeys').hide(); });</script>
	</div>

	<div id="frm-conditionals" class="tabs-panel" style="display:none;max-height:none;padding-right:0;">
	    <ul class="subsubsub" style="float:right;margin:0;">
	        <li><a class="current frmids" onclick="frmToggleKeyID('frmids');"><?php _e('IDs', 'formidable') ?></a> |</li>
	        <li><a class="frmkeys" onclick="frmToggleKeyID('frmkeys');"><?php _e('Keys', 'formidable') ?></a></li>
	    </ul>
	    <ul class="alignleft" style="margin:5px 0 0;"><li><?php _e('Fields from your form', 'formidable') ?>:</li></ul>
	    <ul class="frm_code_list frm_full_width" style="clear:both;max-height:150px;overflow:auto;">
		    <?php if(!empty($fields)){
		        foreach($fields as $f){
                    if(in_array($f->type, array('divider','captcha','break','html')) or ($f->type == 'data' and (!isset($f->field_options['data_type']) or $f->field_options['data_type'] == 'data' or $f->field_options['data_type'] == '')))
                        continue;
                ?>
                <li>
                    <a class="frmids alignright" onclick="frmInsertFieldCode(jQuery(this),'if <?php echo $f->id ?>]<?php _e('Conditional text here', 'formidable') ?>[/if <?php echo $f->id ?>');return false;" href="#">[if <?php echo $f->id ?>]</a>
                	<a class="frmkeys alignright" onclick="frmInsertFieldCode(jQuery(this),'if <?php echo $f->field_key ?>]something[/if <?php echo $f->field_key ?>');return false;" href="#">[if <?php echo FrmAppHelper::truncate($f->field_key, 10) ?>]</a>
                	<a onclick="frmInsertFieldCode(jQuery(this),'<?php echo $f->id ?>');return false;" href="#"><?php echo FrmAppHelper::truncate($f->name, 60) ?></a>
                </li>
                <?php
                
                if($f->type == 'user_id')
                    $uid = $f;
                else if($f->type == 'file')
                    $file = $f;
                    
		        unset($f);
		        }
		    } ?>
        </ul>
        
        <p class="howto"><?php _e('Click a button below to insert sample logic into your custom display', 'formidable') ?></p>
        <ul class="frm_code_list">
        <?php
        $col = 'one';
        foreach($cond_shortcodes as $skey => $sname){
	    ?>
	    <li class="frm_col_<?php echo $col ?>">
	        <a class="frmbutton button" onclick="frmInsertFieldCode(jQuery(this),'if 125 <?php echo $skey ?>][/if 125');return false;" href="#"><?php echo $sname ?></a>
	    </li>
	    <?php
	        $col = ($col == 'one') ? 'two' : 'one';
	        unset($skey);
	        unset($sname);
	    }
        ?>
        </ul>
        
	</div>
	
	<div id="frm-adv-info-tab" class="tabs-panel" style="display:none;max-height:355px;">
		<ul class="frm_code_list">
        <?php
        $col = 'one';
        foreach($adv_shortcodes as $skey => $sname){
	    ?>
	    <li class="frm_col_<?php echo $col ?>">
	        <a class="frmbutton button <?php echo is_array($sname) ? 'frm_help' : ''; ?>" onclick="frmInsertFieldCode(jQuery(this),'125 <?php echo $skey ?>');return false;" href="#" <?php echo is_array($sname) ? 'title="'. $sname['title'] .'"' : ''; ?>><?php echo is_array($sname) ? $sname['label'] : $sname; ?></a>
	    </li>
	    <?php
	        $col = ($col == 'one') ? 'two' : 'one';
	        unset($skey);
	        unset($sname);
	    }
        ?>
        <?php if(isset($file)){ ?>
        <li class="frm_col_<?php echo $col ?>">
	        <a class="frmbutton button" onclick="frmInsertFieldCode(jQuery(this),'<?php echo $file->id ?> size=thumbnail html=1');return false;" href="#"><?php _e('Image Size', 'formidable') ?></a>
	    </li>
	    <li class="frm_col_<?php echo $col = (($col == 'one') ? 'two' : 'one') ?>">
	        <a class="frmbutton button" onclick="frmInsertFieldCode(jQuery(this),'<?php echo $file->id ?> show=id');return false;" href="#"><?php _e('Image ID', 'formidable') ?></a>
	    </li>
	    <li class="frm_col_<?php echo $col = (($col == 'one') ? 'two' : 'one') ?>">
	        <a class="frmbutton button" onclick="frmInsertFieldCode(jQuery(this),'<?php echo $file->id ?> show=label');return false;" href="#"><?php _e('Image Name', 'formidable') ?></a>
	    </li>
	    <?php } ?>
        </ul>

        <?php if(isset($uid)){ 
            $col = 'one'; ?>
        <div class="clear"></div>
        <p class="howto"><?php _e('Insert user information', 'formidable') ?></p>    
        <ul class="frm_code_list">
        <?php foreach($user_fields as $uk => $uf){ ?>
            <li class="frm_col_<?php echo $col ?>">
                <a class="frmbutton button" onclick="frmInsertFieldCode(jQuery(this),'<?php echo $uid->id .' show=&#34;'. $uk .'&#34;' ?>');return false;" href="#"><?php echo $uf ?></a>
    	    </li>
        <?php 
            $col = ($col == 'one') ? 'two' : 'one';   
            unset($uf);
            unset($uk);
        } 
        unset($uid); ?>
        </ul>
        <?php } 
        
        if(isset($dfe)){ ?>
            
        <div class="clear"></div>
        <p class="howto"><?php _e('Data From Entries options', 'formidable') ?></p>
            <ul class="frm_code_list">
        	    <li class="frm_col_one">
                    <a class="frmbutton button" onclick="frmInsertFieldCode(jQuery(this),'<?php echo $dfe .' show=&#34;created-at&#34;' ?>');return false;" href="#"><?php _e('Creation Date', 'formidable')?></a>
        	    </li>
        	    <li class="frm_col_two">
                    <a class="frmbutton button" onclick="frmInsertFieldCode(jQuery(this),'<?php echo $dfe .' show=&#34;'. $ldfe .'&#34;' ?>');return false;" href="#"><?php _e('Field From Entry', 'formidable')?></a>
        	    </li>
            </ul>
        <?php } ?>

	</div>

    <?php if($settings_tab)
            include(FRM_VIEWS_PATH.'/frm-forms/mb_html_tab.php'); ?>
</div>

<?php if(defined('DOING_AJAX')){ ?>
<script type="text/javascript">
jQuery(document).ready(function($){
jQuery('.categorydiv .category-tabs a').click(function(){
var t = $(this).attr('href');
$(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
$(t).show().siblings('.tabs-panel').hide();
return false;
});
});
</script>
<?php } ?>