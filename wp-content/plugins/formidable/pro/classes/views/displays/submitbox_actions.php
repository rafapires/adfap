<div class="misc-pub-section">
	<span id="frm_shortcode"><span class="frm-buttons-icon wp-media-buttons-icon"></span> <?php _e('View', 'formidable') ?> <strong><?php _e('Shortcodes', 'formidable') ?></strong></span>
    <a href="#edit_frm_shortcode" class="edit-frm_shortcode hide-if-no-js" tabindex='4'><?php _e('Show', 'formidable') ?></a>
    <div id="frm_shortcodediv" class="hide-if-js">
        <p class="howto"><?php _e('Insert on a page, post, or text widget', 'formidable') ?>:</p>
    	<p><input type="text" style="width:98%;" readonly="true" class="frm_select_box" value='[display-frm-data id=<?php echo (isset($post->ID)) ? $post->ID : __('Save to get ID', 'formidable') ?> filter=1]' />
    	<?php if(isset($post->post_name) and !empty($post->post_name) and ($post->post_name != $post->ID)){ ?>
    	<input type="text" style="width:98%;margin-top:4px;" readonly="true" class="frm_select_box" value='[display-frm-data id=<?php echo (isset($post->post_name) and $post->post_name != '') ? $post->post_name : '??' ?> filter=1]' />
    	<?php } ?>
    	</p>
    	
    	<p class="howto"><?php _e('Insert in a template', 'formidable') ?>:</p>
    	<p><input type="text" style="font-size:10px;width:98%;font-weight:normal" readonly="true" class="frm_select_box" value="&lt;?php echo FrmProDisplaysController::get_shortcode(array('id' => <?php echo (isset($post->ID)) ? $post->ID : '??' ?>)) ?&gt;" /></p>
    	
        <p><a href="#edit_frm_shortcode" class="cancel-frm_shortcode hide-if-no-js"><?php _e('Hide', 'formidable'); ?></a></p>
    </div>
</div>

<style type="text/css">#frm_shortcode .frm-buttons-icon{margin:0;}</style>