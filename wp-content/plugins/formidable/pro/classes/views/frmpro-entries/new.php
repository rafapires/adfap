<div id="form_entries_page" class="wrap">
    <div class="frmicon icon32"><br/></div>
    <h2 style="height:34px;"><?php _e('Add New Entry', 'formidable'); ?></h2>
    
    <?php if(empty($values)){ ?>
    <div class="frm_forms with_frm_style" id="frm_form_<?php echo $form->id ?>_container">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
            <?php FrmAppController::get_form_nav($form->id, true); ?>
            <p class="clear frm_error_style"><strong><?php _e('Oops!', 'formidable') ?></strong> <?php printf(__('You did not add any fields to your form. %1$sGo back%2$s and add some.', 'formidable'), '<br/><a href="'. admin_url('?page=formidable&frm_action=edit&id='. $form->id) .'">', '</a>') ?></p>
            </div>
            <?php include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/sidebar-new.php'); ?>
            </div>
        </div>
    </div>
    <?php   
            return;
        } ?>
    <div class="frm_forms<?php echo ($values['custom_style']) ? ' with_frm_style' : ''; ?>" id="frm_form_<?php echo $form->id ?>_container">
        <?php if($form) FrmAppController::get_form_nav($form->id, true); ?>
		<div class="clear"></div>
		<?php include(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/errors.php'); ?>

        <form enctype="multipart/form-data" method="post" id="form_<?php echo $form->form_key ?>" class="frm-show-form">
        <?php
        if(version_compare( $GLOBALS['wp_version'], '3.3.3', '<')){ ?>
        <div id="poststuff" class="metabox-holder has-right-sidebar">
        <?php   
            require(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/sidebar-new.php'); 
        }else{ ?>
        <div id="poststuff">
        <?php } ?>
            
            <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
            <?php 
			$form_action = 'create'; 
			require(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/form.php'); 
			?>
            
            <p>
                <?php echo FrmProFormsHelper::get_prev_button($form, 'button-secondary'); ?>
                <input class="button-primary" type="submit" value="<?php echo esc_attr($submit) ?>" <?php do_action('frm_submit_button_action', $form, $form_action); ?> /> 
                <?php _e('or', 'formidable') ?>
                <a class="button-secondary cancel" href="?page=formidable-entries"><?php _e('Cancel', 'formidable') ?></a>
                <?php echo FrmProFormsHelper::get_draft_link($form); ?>
            </p>
            </div>
            <?php
                if(version_compare( $GLOBALS['wp_version'], '3.3.2', '>'))
                    include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-entries/sidebar-new.php'); 
            ?>
            </div>
            </div>
        </form>
    </div>

</div>