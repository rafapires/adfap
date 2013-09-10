<div id="postbox-container-1" class="<?php echo FrmAppController::get_postbox_class(); ?>">
    <div id="submitdiv" class="postbox ">
    <h3 class="hndle"><span><?php _e('Shortcode Options', 'formidable') ?></span></h3>
    <div class="inside">
        <div class="submitbox">

    	<div id="major-publishing-actions">
    	    <div id="delete-action">
    	    <?php if(isset($id)){ ?>
    	    <a class="submitdelete deletion" href="?page=formidable-entry-templates&amp;frm_action=destroy&amp;id=<?php echo $id; ?>" onclick="return confirm('<?php printf(__('Are you sure you want to delete your %1$s display?', 'formidable'), esc_attr(stripslashes($values['name']))) ?>);" title="<?php _e('Delete', 'formidable') ?>"><?php _e('Delete', 'formidable') ?></a>
    	    <?php }else{ ?>
    	    <a class="submitdelete deletion" href="?page=formidable-entry-templates"><?php _e('Cancel', 'formidable') ?></a>
    	    <?php } ?>
    	    </div>
    	    <div id="publishing-action">
            <input type="submit" value="<?php echo esc_attr($submit) ?>" class="button-primary" />
            </div>
            <div class="clear"></div>
        </div>
        </div>
    </div>
    </div>
    
    <div id="frm_form_tags"><?php include(FRMPRO_VIEWS_PATH .'/displays/tags.php'); ?></div>
</div>