<div id="postbox-container-1" class="<?php echo FrmAppController::get_postbox_class(); ?>">
<div id="submitdiv" class="postbox">
    <h3 class="hndle"><span><?php _e('Entry Actions', 'formidable') ?></span></h3>
    <div class="inside">
        <div class="submitbox">
        <div id="minor-publishing" style="border:none;">
            <div class="misc-pub-section">
                <a href="?page=formidable-entries&amp;frm_action=duplicate&amp;form=<?php echo $entry->form_id ?>&amp;id=<?php echo $id; ?>" class="button-secondary alignright"><?php _e('Duplicate', 'formidable') ?></a>
                
                <p class="howto"><?php FrmProEntriesHelper::resend_email_links($entry->id, $entry->form_id); ?></p>
                <p class="howto"><a href="#" onclick="window.print();return false;"><?php _e('Print', 'formidable') ?></a>
                <?php do_action('frm_show_entry_publish_box', $entry); ?>
            </div>
        </div>
    	<div id="major-publishing-actions">
    	    <div id="delete-action">                	    
    	        <a class="submitdelete deletion" href="?page=formidable-entries&amp;frm_action=destroy&amp;id=<?php echo $id; ?>&amp;form=<?php echo $entry->form_id ?>" onclick="return confirm('<?php _e('Are you sure you want to delete that entry?', 'formidable') ?>');" title="<?php _e('Delete', 'formidable') ?>"><?php _e('Delete', 'formidable') ?></a>
    	        <?php if(!empty($entry->post_id)){ ?>
        	    <a class="submitdelete deletion" style="margin-left:10px;" href="?page=formidable-entries&amp;frm_action=destroy&amp;id=<?php echo $id; ?>&amp;form=<?php echo $entry->form_id ?>&amp;keep_post=1" onclick="return confirm('<?php _e('Are you sure you want to delete this entry?', 'formidable') ?>);" title="<?php _e('Delete entry but leave the post', 'formidable') ?>"><?php _e('Delete without Post', 'formidable') ?></a>
        	    <?php } ?>
    	    </div>
    	    
    	    <div id="publishing-action">
    	        <a href="<?php echo add_query_arg('frm_action', 'edit') ?>" class="button-primary"><?php _e('Edit', 'formidable') ?></a>
            </div>
            <div class="clear"></div>
        </div>
        </div>
    </div>
</div>
<?php do_action('frm_show_entry_sidebar', $entry); ?>
</div>
