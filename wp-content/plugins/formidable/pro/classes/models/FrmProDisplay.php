<?php
class FrmProDisplay{

    function duplicate( $id, $copy_keys=false, $blog_id=false ){
        global $wpdb;

        $values = $this->getOne( $id, $blog_id, true );
        
        if(!$values or !is_numeric($values->frm_form_id))
            return false;
            
        $new_values = array();
        foreach(array('post_name', 'post_title', 'post_excerpt', 'post_content', 'post_status', 'post_type') as $k){
            $new_values[$k] = $values->{$k};
            unset($k);
        }
        
        $meta = array();
        foreach(array('form_id', 'entry_id', 'post_id', 'dyncontent', 'param', 'type', 'show_count', 'insert_loc') as $k){
            $meta[$k] = $values->{'frm_'. $k};
            unset($k);
        }
        
        $default = FrmProDisplaysHelper::get_default_opts();
        $meta['options'] = array();
        foreach($default as $k => $v){
            if(isset($meta[$k]))
                continue;
                
            $meta['options'][$k] = $values->{'frm_'. $k};
            unset($k);
            unset($v);
        }
        $meta['options']['copy'] = false;
        
        if ($blog_id){
            $frm_form = new FrmForm();
            $old_form = $frm_form->getOne($values->frm_form_id, $blog_id);
            $new_form = $frm_form->getOne($old_form->form_key);
            $meta['form_id'] = $new_form->id;
        }else{    
            $meta['form_id'] = $values->form_id;
        }

        $post_ID = wp_insert_post( $new_values );
        
        $new_values = array_merge((array)$new_values, $meta);

        $this->update($post_ID, $new_values);
        
        return $post_ID;
    }

    function update( $id, $values ){
        $new_values = array();
        $new_values['frm_param'] = isset($values['param']) ? sanitize_title_with_dashes($values['param']) : '';

        $fields = array('dyncontent', 'insert_loc', 'type', 'show_count', 'form_id', 'entry_id', 'post_id');
        foreach ($fields as $field){
            if(isset($values[$field]))
                $new_values['frm_'. $field] = $values[$field];
        }
            
        if (isset($values['options'])){
            $new_values['frm_options'] = array();
            foreach ($values['options'] as $key => $value)
                $new_values['frm_options'][$key] = $value;
        }

        foreach($new_values as $key => $val){
            update_post_meta($id, $key, $val);
            unset($key);
            unset($val);
        }
        
        if(!isset($new_values['frm_form_id']) or empty($new_values['frm_form_id']))
            return;
            
        global $wpdb, $frmdb;
        
        //update 'frm_display_id' post metas for automatically used views
        $posts = $wpdb->get_col("SELECT post_id FROM $frmdb->entries WHERE post_id > 0 and form_id=". (int)$new_values['frm_form_id']);
        $first_post = $posts ? reset($posts) : false;
        $qualified = $this->get_auto_custom_display(array('form_id' => $new_values['frm_form_id'], 'post_id' => $first_post));
        
        if(!$qualified){
            //delete any post meta for this display if no qualified displays
            $wpdb->delete($wpdb->postmeta, array('meta_key' => 'frm_display_id', 'meta_value' => $id));
        }else if($qualified->ID == $id){
            //this display is qualified
            if($posts){
                foreach($posts as $p){
                    update_post_meta($p, 'frm_display_id', $id);
                    unset($p);
                }
            }else{
                $wpdb->delete($wpdb->postmeta, array('meta_key' => 'frm_display_id', 'meta_value' => $id));
            }            
        }else{
            //this view is not qualified, so set any posts to the next qualified view
            $update_display_posts = $wpdb->query("UPDATE $wpdb->postmeta SET meta_value=$qualified->ID WHERE meta_key='frm_display_id' AND meta_value=$id");
        }
        
        //update post meta of post selected for auto insertion
        if(isset($new_values['frm_insert_loc']) and $new_values['frm_insert_loc'] != 'none' and isset($new_values['frm_post_id']) and (int)$new_values['frm_post_id'])
            update_post_meta($new_values['frm_post_id'], 'frm_display_id', $id);
            
    }
    
    function getOne( $id, $blog_id=false, $get_meta=false, $atts=array() ){
        global $wpdb;

        if ($blog_id and is_multisite())
            switch_to_blog($blog_id);
            
        if (!is_numeric($id)){
            $id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = %s AND post_status != %s", $id, 'frm_display', 'trash' ) );
            
            if (is_multisite() and empty($id))
                return false;
        }
        
        $post = get_post($id);
        if(!$post or $post->post_type != 'frm_display' or $post->post_status == 'trash'){
            $args = array(
                'post_type' => 'frm_display', 
                'meta_key' => 'frm_old_id',
                'meta_value' => $id,
                'numberposts' => 1,
                'post_status' => 'publish'
            );
            $posts = get_posts($args);
            
            if($posts)
                $post = reset($posts);
        }
        
        if($post and $post->post_status == 'trash')
            return false;
            
        if($post and $get_meta){
            $check_post = (isset($atts['check_post'])) ? $atts['check_post'] : true;
            $post = FrmProDisplaysHelper::setup_edit_vars($post, $check_post);
        }
        
        if ($blog_id and is_multisite())
            restore_current_blog();

        return $post;
    }

    function getAll( $where = '', $order_by = 'post_date', $limit = 99 ){
        if(!is_numeric($limit))
            $limit = (int)$limit;
        
        $query = array(
            'numberposts'   => $limit,
            'orber_by'      => $order_by,
            'post_type'     => 'frm_display'
        );
        
        $results = get_posts($query);
        return $results;
    }
    
    /**
     * Check for a qualified view.
     * Qualified:   1. set to show calendar or dynamic
     *              2. published
     *              3. form has posts/entry is linked to a post
     */
    function get_auto_custom_display($args){
        $defaults = array('post_id' => false, 'form_id' => false, 'entry_id' => false);
        extract(wp_parse_args( $args, $defaults )); 
        
        global $wpdb, $frmdb;
        
        if($form_id){
            $display_ids = $wpdb->get_col("SELECT post_ID FROM $wpdb->postmeta WHERE meta_key='frm_form_id' AND meta_value=". (int)$form_id);
            
            if(!$display_ids)
                return false;
                
            if(!$post_id and !$entry_id){
                //does form have posts?
                $entry_id = $wpdb->get_var("SELECT post_id FROM $frmdb->entries WHERE form_id=". (int)$form_id);
            }
        }
        
        if($post_id and !$entry_id){
            //is post linked to an entry?
            $entry_id = $wpdb->get_var("SELECT id FROM $frmdb->entries WHERE post_id=". (int)$post_id);
            
            //is post selected for auto-insertion?
            if(!$entry_id){
                $query = "SELECT post_ID FROM $wpdb->postmeta WHERE meta_key='frm_post_id' AND meta_value='". (int)$post_id ."'";
                if(isset($display_ids))
                    $query .= " AND post_ID in (". implode(',', $display_ids) .")";
                $display_ids = $wpdb->get_col($query);
                
                if(!$display_ids)
                    return false;
            }
        }
        
        //this post does not have an auto display
        if(!$entry_id)
            return false;
            
        $query = "SELECT p.* FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm ON (p.ID = pm.post_ID) WHERE pm.meta_key='frm_show_count' AND post_type='frm_display' AND pm.meta_value in ('dynamic','calendar','single') AND p.post_status='publish' ";
        
        if(isset($display_ids))
            $query .= "AND p.ID in (". implode(',', $display_ids) .") ";
            
        $query .= "ORDER BY p.ID ASC LIMIT 1";
        
        $display = $wpdb->get_row($query);
                
        return $display;
    }
    
    public static function get_form_custom_display($form_id){
        global $wpdb;
        
        $display_ids = $wpdb->get_col("SELECT post_ID FROM $wpdb->postmeta WHERE meta_key='frm_form_id' AND meta_value=". (int)$form_id);
        
        if(!$display_ids)
            return false;
            
        $query = "SELECT p.* FROM $wpdb->posts p LEFT JOIN $wpdb->postmeta pm ON (p.ID = pm.post_ID) WHERE pm.meta_key='frm_show_count' AND post_type='frm_display' AND pm.meta_value in ('dynamic','calendar','single') AND p.post_status='publish' AND p.ID in (". implode(',', $display_ids) .") ORDER BY p.ID ASC LIMIT 1";
        
        $display = $wpdb->get_row($query);
                
        return $display;
    }

    function validate( $values ){
        $errors = array();

        if( $values['post_title'] == '' )
            $errors[] = __('Name cannot be blank', 'formidable');
            
        if( $values['excerpt'] == __('This is not displayed anywhere, but is just for your reference. (optional)', 'formidable' ))
            $_POST['excerpt'] = '';
        
        if( $values['content'] == '' )
            $errors[] = __('Content cannot be blank', 'formidable');
        
        if ($values['insert_loc'] != 'none' && $values['post_id'] == '')
            $errors[] = __('Page cannot be blank if you want the content inserted automatically', 'formidable');
            
        if (!empty($values['options']['limit']) && !is_numeric($values['options']['limit']))
            $errors[] = __('Limit must be a number', 'formidable');
        
        if ($values['show_count'] == 'dynamic'){
            if ($values['dyncontent'] == '')
                $errors[] = __('Dynamic Content cannot be blank', 'formidable');
            
            if( !FrmProAppHelper::rewriting_on() ){
                if ($values['param'] == '')
                     $errors[] = __('Parameter Name cannot be blank if content is dynamic', 'formidable');

                 if ($values['type'] == '')
                     $errors[] = __('Parameter Value cannot be blank if content is dynamic', 'formidable');
            }else{
                if ($values['type'] == '')
                     $errors[] = __('Detail Link cannot be blank if content is dynamic', 'formidable');
            }
        }
        
        if(isset($values['options']['where'])){
            $_POST['options']['where'] = FrmProAppHelper::reset_keys($values['options']['where']);
            $_POST['options']['where_is'] = FrmProAppHelper::reset_keys($values['options']['where_is']);
            $_POST['options']['where_val'] = FrmProAppHelper::reset_keys($values['options']['where_val']);
        }
        
        return $errors;
    }

}
