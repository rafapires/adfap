<table class="form-table">
        <tr>
            <td colspan="2"><label for="create_post"><input type="checkbox" name="options[create_post]" id="create_post" value="1" <?php checked($values['create_post'], 1); ?> onclick="frm_show_div('frm_hide_post',this.checked,1,'.')"/> <?php _e('Create a WordPress post, page, or custom post type with this form', 'formidable') ?></label></td>
        </tr>
        
        <tr class="frm_hide_post" <?php echo $hide_post = ($values['create_post']) ? '' : 'style="display:none;"'; ?>>
            <td class="frm_left_label"><label><?php _e('Post Type', 'formidable') ?></label>
                <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php _e('To setup a new custom post type, install and setup a plugin like \'Custom Post Type UI\', then return to this page to select your new custom post type.', 'formidable') ?>" ></span>
            </td>
            <td>
            <select name="options[post_type]">
                <?php foreach($post_types as $post_key => $post_type){ 
                        if($post_key == 'frm_display')
                            continue; ?>
                    <option value="<?php echo $post_key ?>" <?php selected($values['post_type'], $post_key) ?>><?php echo $post_type->label ?></option>
<?php
                        unset($post_type);
                    }

                unset($post_types); 
                
                ?>
            </select>
        </td></tr>
        <?php
        $values['post_category'] = $values['post_custom_fields'] = array();
        if(empty($values['post_category']) and !empty($values['fields'])){
            foreach($values['fields'] as $fo_key => $fo){
                if($fo['post_field'] == 'post_category'){
                    if(!isset($fo['taxonomy']) or $fo['taxonomy'] == '')
                        $fo['taxonomy'] = 'post_category';

                    $tax_count = FrmProFormsHelper::get_taxonomy_count($fo['taxonomy'], $values['post_category']);

                    $values['post_category'][$fo['taxonomy'] .$tax_count] = array('field_id' => $fo['id'], 'exclude_cat' => $fo['exclude_cat'], 'meta_name' => $fo['taxonomy']);
                    unset($tax_count);
                }else if($fo['post_field'] == 'post_custom' and !array_key_exists($fo['custom_field'], $values['post_custom_fields'])){
                    $values['post_custom_fields'][$fo['custom_field']] = array('field_id' => $fo['id'], 'meta_name' => $fo['custom_field']);
                }
                unset($fo_key);
                unset($fo);
            }
        }
        ?> 
        <tr class="frm_hide_post" <?php echo $hide_post ?>>
            <td><label><?php _e('Post Title', 'formidable') ?> <span class="frm_required">*</span></label></td>
            <td><select name="options[post_title]" class="frm_single_post_field">
                <option value="">&mdash; <?php echo _e('Select Field', 'formidable') ?> &mdash;</option>
                <?php $post_key = 'post_title'; 
                $post_field = array('text', 'email', 'url', 'radio', 'checkbox', 'select', 'scale', 'number', 'phone', 'time', 'hidden');
                include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/_post_field_options.php');
                unset($post_field); ?>
                </select>    
            </td>
        </tr>
        
        <tr class="frm_hide_post" <?php echo $hide_post ?>>
            <td><label><?php _e('Post Content', 'formidable') ?></label></td>
            <td><select name="options[post_content]" class="frm_single_post_field">
                <option value="">&mdash; <?php echo _e('Select Field', 'formidable') ?> &mdash;</option>
                <?php $post_key = 'post_content'; include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/_post_field_options.php'); ?>
                </select>
            </td>   
        </tr>
        <tr class="frm_hide_post" <?php echo $hide_post ?>>
            <td colspan="2"><label><?php _e('Customize Content', 'formidable') ?></label>
                <span class="frm_help frm_icon_font frm_tooltip_icon"title="<?php _e('The content shown on your single post page. If nothing is entered here, the regular post content will be used.', 'formidable') ?>" ></span><br/>
                <?php if($display){ ?>
                <input type="hidden" value="<?php echo $display->ID ?>" name="frm_display_id" />
                <textarea id="frm_dyncontent" name="frm_<?php echo $display->frm_show_count == 'single' ? 'single_' : 'dyn' ?>content" rows="10" style="width:98%"><?php echo FrmAppHelper::esc_textarea($display->frm_show_count == 'single' ? $display->post_content : $display->frm_dyncontent) ?></textarea>
                <?php }else{ ?>
                <textarea id="frm_dyncontent" name="frm_dyncontent" rows="10" style="width:98%"></textarea>
                <?php } ?> 
                <p class="howto"><?php _e('Editing this box will update your existing view or create a new one.', 'formidable') ?></p>
            </td>
        </tr>
        
        <tr class="frm_hide_post" <?php echo $hide_post ?>>
            <td><label><?php _e('Excerpt', 'formidable') ?></label></td>
            <td><select name="options[post_excerpt]" class="frm_single_post_field">
                <option value=""><?php echo _e('None', 'formidable') ?></option>
                <?php $post_key = 'post_excerpt'; include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/_post_field_options.php'); ?>
                </select>    
            </td>
        </tr>
        
        <tr class="frm_hide_post" <?php echo $hide_post ?>>
            <td><label><?php _e('Post Password', 'formidable') ?></label></td>
            <td><select name="options[post_password]" class="frm_single_post_field">
                <option value=""><?php echo _e('None', 'formidable') ?></option>
                <?php $post_key = 'post_password'; include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/_post_field_options.php'); ?>
                </select>    
            </td>
        </tr>
        
        <tr class="frm_hide_post" <?php echo $hide_post ?>>
            <td><label><?php _e('Slug', 'formidable') ?></label></td>
            <td><select name="options[post_name]" class="frm_single_post_field">
                <option value=""><?php echo _e('Automatically Generate from Post Title', 'formidable') ?></option>
                <?php $post_key = 'post_name'; include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/_post_field_options.php'); ?>
                </select>    
            </td>
        </tr>
        
        <tr class="frm_hide_post" <?php echo $hide_post ?>>
            <td><label><?php _e('Post Date', 'formidable') ?></label></td>
            <td><select name="options[post_date]" class="frm_single_post_field">
                <option value=""><?php echo _e('Use the Date Published', 'formidable') ?></option>
                <?php $post_key = 'post_date'; $post_field = array('date');
                    include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/_post_field_options.php'); ?>
                </select>    
            </td>
        </tr>
        
        <tr class="frm_hide_post" <?php echo $hide_post ?>>
            <td><label><?php _e('Post Status', 'formidable') ?></label></td>
            <td><select name="options[post_status]" class="frm_single_post_field">
                <option value=""><?php echo _e('Create Draft', 'formidable') ?></option>
                <option value="publish" <?php selected($values['post_status'], 'publish') ?>><?php echo _e('Automatically Publish', 'formidable') ?></option>
                <option value="dropdown"><?php echo _e('Create New Dropdown Field', 'formidable') ?></option>
                <?php $post_key = 'post_status'; $post_field = array('select', 'radio', 'hidden');
                    include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/_post_field_options.php'); ?>
                </select>    
            </td>
        </tr>
        
        <?php 
            unset($post_field);
            unset($post_key);
        ?>


        <tr class="frm_hide_post" <?php echo $hide_post ?>>
            <td colspan="2">
                <h4><?php _e('Taxonomies/Categories', 'formidable') ?> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e('Select the field(s) from your form that you would like to populate with your categories, tags, or other taxonomies.', 'formidable');
?>" ></span></h4>
                <div id="frm_posttax_rows" style="padding-bottom:8px;">
                <?php 
                $tax_key = 0;
                foreach($values['post_category'] as $field_vars){
                    include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/_post_taxonomy_row.php');
                    $tax_key++;
                    unset($field_vars);
                }
                ?>
                </div>
                <p><a class="frm_add_posttax_row button">+ <?php _e('Add') ?></a></p>


                <h4><?php _e('Custom Fields', 'formidable') ?> <span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e('To set the featured image, use \'_thumbnail_id\' as the custom field name.', 'formidable');
?>" ></span></h4>

                <div id="frm_postmeta_rows">
                <?php
                foreach($values['post_custom_fields'] as $custom_data){
                    include(FrmAppHelper::plugin_path() .'/pro/classes/views/frmpro-forms/_custom_field_row.php');
                    unset($custom_data);
                }
                ?>
                </div>
                <p><a class="frm_add_postmeta_row button" <?php echo (empty($values['post_custom_fields']) ? '' : 'style="display:none;"') ?>>+ <?php _e('Add') ?></a></p>
            </td>
        </tr>

</table>