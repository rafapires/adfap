<p>
<label><span><?php _e('Before Content', 'formidable'); ?></span> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('This content will not be repeated. This would be a good place to put any HTML table tags.', 'formidable') ?>" /> (<?php _e('optional', 'formidable') ?>)
<textarea id="before_content" name="options[before_content]" rows="3" style="width:98%"><?php echo FrmAppHelper::esc_textarea($post->frm_before_content) ?></textarea>
</label>
</p>


<div>
<label><span><?php _e('Content', 'formidable'); ?></span> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('The HTML for your page. If \'All Entries\' is selected above, this content will be repeated for each entry. The field ID and Key work synonymously, although there are times one choice may be better. If you are panning to copy your custom display settings to other blogs, use the Key since they will be copied and the ids may differ from blog to blog.', 'formidable') ?>" /></label>


<div id="<?php echo (user_can_richedit()) ? 'postdivrich' : 'postdiv'; ?>" class="postarea frm_full_rte">
<?php 
if(function_exists('wp_editor'))
    wp_editor($post->post_content, 'content');
else
    the_editor($post->post_content, 'content', 'title', false); 
?>
</div>
</div>
    

<p>
<label><span><?php _e('After Content', 'formidable'); ?></span> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('This content will not be repeated. This would be a good place to close any HTML tags from the Before Content field.', 'formidable') ?>" /> (<?php _e('optional', 'formidable') ?>)
<textarea id="after_content" name="options[after_content]" rows="3" style="width:98%"><?php echo FrmAppHelper::esc_textarea($post->frm_after_content) ?></textarea>
</label>
</p>

    
<div class="hide_dyncontent">
    <label><span><?php _e('Dynamic Content', 'formidable'); ?></span> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php printf(__('The HTML for the entry on the dynamic page. This content will NOT be repeated, and will only show when the %1$s is clicked.', 'formidable'), '[detaillink]') ?>" /></label>
        
    <?php 
    if(function_exists('wp_editor')){
        wp_editor($post->frm_dyncontent, 'dyncontent' );
    }else{
    ?>
    <textarea id="dyncontent" name="dyncontent" rows="15" style="width:98%"><?php echo FrmAppHelper::esc_textarea($post->frm_dyncontent) ?></textarea>
    <?php 
    } ?>
</div>