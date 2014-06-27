<div id="form_entries_page" class="wrap">
    <div class="frmicon icon32"><br/></div>
    <h2><?php _e('Edit Entry', 'formidable') ?>
        <?php if(current_user_can('frm_create_entries')){ ?>
        <a href="?page=formidable-entries&amp;frm_action=new&amp;form=<?php echo $form->id; ?>" class="add-new-h2"><?php _e('Add New', 'formidable') ?></a>
        <?php } ?>
    </h2>
	<?php if($form) FrmAppController::get_form_nav($form->id, true); ?>
	<div class="clear"></div>
	<?php include(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/errors.php'); ?>
	<div class="frm_forms<?php echo ($values['custom_style']) ? ' with_frm_style' : ''; ?>" id="frm_form_<?php echo $form->id ?>_container">
        <form enctype="multipart/form-data" method="post" id="form_<?php echo $form->form_key ?>" class="frm-show-form">
        <?php
        if(version_compare( $GLOBALS['wp_version'], '3.3.3', '<')){ ?>
        <div id="poststuff" class="metabox-holder has-right-sidebar">
        <?php   
            require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/sidebar-edit.php'); 
        }else{ ?>
        <div id="poststuff" style="padding-top:0;">
        <?php } ?>
        
        <div id="post-body" class="metabox-holder columns-2">
        <div id="post-body-content">
        <?php 
        $form_action = 'update';
        require(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/form.php'); 
		?>
        
        <p>
        <?php echo FrmProFormsHelper::get_prev_button($form, 'button-secondary'); ?>
        <input class="button-primary" type="submit" value="<?php echo esc_attr($submit) ?>" <?php do_action('frm_submit_button_action', $form, $form_action); ?> /> 
        <?php _e('or', 'formidable') ?> 
        <a class="button-secondary cancel" href="?page=formidable-entries"><?php _e('Cancel', 'formidable') ?></a>
        <?php
        //if($values['is_draft'])
        //    echo FrmProFormsHelper::get_draft_link($form);
        ?>
        </p>
        </div>
        
        <?php
            if(version_compare( $GLOBALS['wp_version'], '3.3.2', '>'))
                require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/sidebar-edit.php'); 
        ?>
        </div>
        </div>
        </form>

        </div>
    </div>