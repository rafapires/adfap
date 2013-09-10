<input type="hidden" name="frm_save_display_nonce" value="<?php echo wp_create_nonce('frm_save_display'); ?>" />

<table class="form-table">     
    
    <tr class="form-field" id="order_by_container">
        <th valign="top" scope="row"><?php _e('Order', 'formidable'); ?> </th>
        <td>
            <select id="order_by" name="options[order_by]">
                <option value=""></option>
                <option value="rand" <?php selected($post->frm_order_by, 'rand') ?>><?php _e('Random', 'formidable') ?></option>
                <option value="created_at" <?php selected($post->frm_order_by, 'created_at') ?>><?php _e('Entry creation date', 'formidable') ?></option>
                <option value="updated_at" <?php selected($post->frm_order_by, 'updated_at') ?>><?php _e('Entry update date', 'formidable') ?></option>
                <?php if (is_numeric($post->frm_form_id)) FrmProFieldsHelper::get_field_options($post->frm_form_id, $post->frm_order_by); ?>
            </select>    

            <select id="order" name="options[order]">
                <option value=""></option>
                <option value="ASC" <?php selected($post->frm_order, 'ASC') ?>><?php _e('Ascending', 'formidable'); ?></option>
                <option value="DESC" <?php selected($post->frm_order, 'DESC') ?>><?php _e('Descending', 'formidable'); ?> &nbsp;</option>
            </select>    
        </td>
    </tr>
    
    <tr class="form-field" id="where_container">
        <th valign="top" scope="row"><?php _e('Filter Entries', 'formidable'); ?> 
            <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('Narrow down which entries will be used.', 'formidable') ?>" />
        </th>
        <td>
            <div id="frm_where_options" class="tagchecklist" style="padding-bottom:8px;">
            <?php
              if(count($post->frm_where) > 0){
                foreach($post->frm_where as $where_key => $where_field){
                    if(isset($post->frm_where_is[$where_key]) and isset($post->frm_where_val[$where_key]))
                        $this->add_where_row($where_key, $post->frm_form_id, $where_field, $post->frm_where_is[$where_key], $post->frm_where_val[$where_key]);
                }
              }

            ?>
            </div>
            <p><a href="javascript:frm_add_where_row();" class="button">+ <?php _e('Add', 'formidable') ?></a></p>
        </td>
    </tr>
    
    <tr class="limit_container">
        <th valign="top" scope="row"><?php _e('Limit', 'formidable'); ?> 
            <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('If you don’t want all your entries displayed, you can insert the number limit here. Leave blank if you’d like all entries shown.', 'formidable') ?>" />
        </th>
        <td>
            <input type="text" id="limit" name="options[limit]" value="<?php echo esc_attr($post->frm_limit) ?>" size="4" />
    
            <div style="text-align:right;display:inline;">
            <div style="width:200px;display:inline-block;"><?php _e('Page Size', 'formidable'); ?>
            <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('The number of entries to show per page. Leave blank to not use pagination.', 'formidable') ?>" /></div>

            <input type="text" id="limit" name="options[page_size]" value="<?php echo esc_attr($post->frm_page_size) ?>" size="4" />
            </div>
        </td>
    </tr>
    
    <tr class="form-field">
        <th valign="top" scope="row"><?php _e('Message if nothing to display', 'formidable'); ?></th>
        <td>
            <textarea id="empty_msg" name="options[empty_msg]" style="width:98%"><?php echo FrmAppHelper::esc_textarea($post->frm_empty_msg) ?></textarea>
        </td>
    </tr>
    
    <tr>
        <th valign="top"><?php _e('Insert display', 'formidable'); ?></th>
        <td>
        <p>
            <select id="insert_loc" name="insert_loc" onchange="frm_show_loc(this.value)">
                <option value="none" <?php selected($post->frm_insert_loc, 'none') ?>><?php _e('Don\'t insert automatically', 'formidable') ?></option>
                <option value="after" <?php selected($post->frm_insert_loc, 'after') ?>><?php _e('After page content', 'formidable') ?></option>
                <option value="before" <?php selected($post->frm_insert_loc, 'before') ?>><?php _e('Before page content', 'formidable') ?></option>
                <option value="replace" <?php selected($post->frm_insert_loc, 'replace') ?>><?php _e('Replace page content', 'formidable') ?></option>
            </select>

            <span id="post_select_container">
                <?php _e('on page', 'formidable'); ?>
                <?php FrmAppHelper::wp_pages_dropdown( 'post_id', $post->frm_post_id, 35 ); ?>
                <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('If you would like the content to be inserted automatically, you must then select the page in which to insert it.', 'formidable') ?>" />
            </span>
            <?php if($post->frm_insert_loc != 'none' and is_numeric($post->frm_post_id)){ ?>
            <a href="<?php echo get_permalink($post->frm_post_id) ?>" target="_blank" class="button-secondary"><?php _e('View Post', 'formidable') ?></a>
            <?php } ?>
        </p>
        
        <p><?php _e('Insert position', 'formidable'); ?> <img src="<?php echo FRM_IMAGES_URL ?>/tooltip.png" alt="?" class="frm_help" title="<?php _e('If the custom display doesn\'t show automatically when it should, insert a higher number here.', 'formidable') ?>" />
            <input type="number" id="insert_pos" name="options[insert_pos]" min="1" max="15" step="1" value="<?php echo esc_attr($post->frm_insert_pos) ?>" style="width:30px;float:none;"/> 
        </p>
        </td>
    </tr>
    <?php if (IS_WPMU){
        if (FrmAppHelper::is_super_admin()){ ?>    
        <tr class="form-field">
            <th valign="top" scope="row"><?php _e('Copy', 'formidable'); ?></th>
            <td>
                <input type="checkbox" id="copy" name="options[copy]" value="1" <?php checked($post->frm_copy, 1) ?> />
                <?php _e('Copy these display settings to other blogs when Formidable Pro is activated. <br/>Note: Use only field keys in the content box(es) above.', 'formidable') ?>
            </td>
        </tr>
        <?php }else if ($post->frm_copy){ ?>
        <input type="hidden" id="copy" name="options[copy]" value="1" />
        <?php }
    } ?>

</table>
