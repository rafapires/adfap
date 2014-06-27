<?php
class FrmProEntry{
    
    function frmpro_editing($continue, $form_id, $action='new'){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntriesController::maybe_editing');
        return FrmProEntriesController::maybe_editing($continue, $form_id, $action);
    }
    
    function user_can_edit($entry, $form=false){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntriesHelper::user_can_edit' );
        return FrmProEntriesHelper::user_can_edit($entry, $form);
    }
    
    function user_can_edit_check($entry, $form){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntriesHelper::user_can_edit_check' );
        return FrmProEntriesHelper::user_can_edit_check($entry, $form);
    }
    
    function user_can_delete($entry, $form = false) {
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntriesHelper::user_can_delete' );
        return FrmProEntriesHelper::user_can_delete($entry, $form);
    }
    
    function get_tagged_entries($term_ids, $args = array()){
        return get_objects_in_term( $term_ids, 'frm_tag', $args );
    }
    
    function get_entry_tags($entry_ids, $args = array()){
        return wp_get_object_terms( $entry_ids, 'frm_tag', $args );
    }
    
    function get_related_entries($entry_id){
        $term_ids = FrmProEntry::get_entry_tags($entry_id, array('fields' => 'ids'));
        $entry_ids = FrmProEntry::get_tagged_entries($term_ids);
        foreach ($entry_ids as $key => $id){
            if ($id == $entry_id)
                unset($entry_ids[$key]);
        }
        return $entry_ids;
    }

    function pre_validate($errors, $values){
        global $frm_entry_meta, $frm_entry, $frmdb, $frmpro_settings, $frm_vars;
        
        $user_ID = get_current_user_id();
        $params = (isset($frm_vars['form_params']) && is_array($frm_vars['form_params']) && isset($frm_vars['form_params'][$values['form_id']])) ? $frm_vars['form_params'][$values['form_id']] : FrmEntriesController::get_params($values['form_id']);
        
        if($params['action'] != 'create'){
            if(FrmProFormsHelper::going_to_prev($values['form_id'])){
                add_filter('frm_continue_to_create', '__return_false');
                $errors = array();
            }else if(FrmProFormsHelper::saving_draft($values['form_id'])){
                //$errors = array();
            }
            return $errors;
        }
        
        $frm_form = new FrmForm();
        $form = $frm_form->getOne($values['form_id']);
        $form_options = maybe_unserialize($form->options);
        
        $can_submit = true;
        if (isset($form_options['single_entry']) and $form_options['single_entry']){
            if ($form_options['single_entry_type'] == 'cookie' and isset($_COOKIE['frm_form'. $form->id . '_' . COOKIEHASH])){
                $can_submit = false;
            }else if ($form_options['single_entry_type'] == 'ip'){
                $prev_entry = $frm_entry->getAll(array('it.ip' => $_SERVER['REMOTE_ADDR']), '', 1);
                if ($prev_entry)
                    $can_submit = false;
            }else if (($form_options['single_entry_type'] == 'user' or (isset($form->options['save_draft']) and $form->options['save_draft'] == 1)) and !$form->editable){
                if($user_ID){
                    $args = array('user_id' => $user_ID, 'form_id' => $form->id);
                    if($form_options['single_entry_type'] != 'user')
                        $args['is_draft'] = 1;
                    $meta = $frmdb->get_var($frmdb->entries, $args);
                    unset($args);
                }
                
                if (isset($meta) and $meta)
                    $can_submit = false;
            }
            
            if (!$can_submit){
                $k = is_numeric($form_options['single_entry_type']) ? 'field'. $form_options['single_entry_type'] : 'single_entry';
                $errors[$k] = $frmpro_settings->already_submitted;
                add_filter('frm_continue_to_create', '__return_false');
                return $errors;
            }
        }
        unset($can_submit);
        
        if ((($_POST and isset($_POST['frm_page_order_'. $form->id])) or FrmProFormsHelper::going_to_prev($form->id)) and !FrmProFormsHelper::saving_draft($form->id)){
            add_filter('frm_continue_to_create', '__return_false');
        }else if ($form->editable and isset($form_options['single_entry']) and $form_options['single_entry'] and $form_options['single_entry_type'] == 'user' and $user_ID and (!is_admin() or defined('DOING_AJAX'))){
            $meta = $frmdb->get_var($frmdb->entries, array('user_id' => $user_ID, 'form_id' => $form->id));
            
            if($meta){
                $errors['single_entry'] = $frmpro_settings->already_submitted;
                add_filter('frm_continue_to_create', '__return_false');
            }
        }
        
        if(FrmProFormsHelper::going_to_prev($values['form_id']))
            $errors = array();
        
        return $errors;
    }
        
    function validate($params, $fields, $form, $title, $description){
        global $frm_entry, $frm_settings, $frm_vars;
        
        if ((($_POST and isset($_POST['frm_page_order_'. $form->id])) or FrmProFormsHelper::going_to_prev($form->id)) and !FrmProFormsHelper::saving_draft($form->id)){
            $errors = '';
            $fields = FrmFieldsHelper::get_form_fields($form->id);
            $form_name = $form->name;
            $submit = isset($form->options['submit_value']) ? $form->options['submit_value'] : $frm_settings->submit_value;
            $values = $fields ? FrmEntriesHelper::setup_new_vars($fields, $form) : array();
            require(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/new.php');
            add_filter('frm_continue_to_create', '__return_false');
        }else if ($form->editable and isset($form->options['single_entry']) and $form->options['single_entry'] and $form->options['single_entry_type'] == 'user'){
            
            $user_ID = get_current_user_id();
            if($user_ID){
                $entry = $frm_entry->getAll(array('it.user_id' => $user_ID, 'it.form_id' => $form->id), '', 1, true);
                if($entry)
                    $entry = reset($entry);
            }else{
                $entry = false;
            }
            
            if ($entry and !empty($entry) and (!isset($frm_vars['created_entries'][$form->id]) or !isset($frm_vars['created_entries'][$form->id]['entry_id']) or $entry->id != $frm_vars['created_entries'][$form->id]['entry_id'])){
                FrmProEntriesController::show_responses($entry, $fields, $form, $title, $description);
            }else{
                $record = $frm_vars['created_entries'][$form->id]['entry_id'];
                $saved_message = isset($form->options['success_msg']) ? $form->options['success_msg'] : $frm_settings->success_msg;
                if(FrmProFormsHelper::saving_draft($form->id)){
                    global $frmpro_settings;
                    $saved_message = isset($form->options['draft_msg']) ? $form->options['draft_msg'] : $frmpro_settings->draft_msg;
                }
                $saved_message = apply_filters('frm_content', $saved_message, $form, ($record ? $record : false));
                $message = wpautop(do_shortcode($record ? $saved_message : $frm_settings->failed_msg));
                $message = '<div class="frm_message" id="message">'. $message .'</div>';
                
                FrmProEntriesController::show_responses($record, $fields, $form, $title, $description, $message, '', $form->options);
            }
            add_filter('frm_continue_to_create', '__return_false');
        }else if(FrmProFormsHelper::saving_draft($form->id)){
            global $frmpro_settings;
            
            $record = (isset($frm_vars['created_entries']) and isset($frm_vars['created_entries'][$form->id])) ? $frm_vars['created_entries'][$form->id]['entry_id'] : 0;
            if($record){
                $saved_message = isset($form->options['draft_msg']) ? $form->options['draft_msg'] : $frmpro_settings->draft_msg;
                $saved_message = apply_filters('frm_content', $saved_message, $form, $record);
                $message = '<div class="frm_message" id="message">'. wpautop(do_shortcode($saved_message)) .'</div>';

                FrmProEntriesController::show_responses($record, $fields, $form, $title, $description, $message, '', $form->options);
                add_filter('frm_continue_to_create', '__return_false');
            }
        }
    }
    
    function set_cookie($entry_id, $form_id){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntriesController::maybe_set_cookie');
        return FrmProEntriesController::maybe_set_cookie($entry_id, $form_id);
    }
    
    function update_post($entry_id, $form_id){
        if ( !isset($_POST['frm_wp_post']) ) {
            return;
        }
        
        $post_id = FrmProEntriesHelper::get_field('post_id', $entry_id);
        if ( $post_id ) {
            $post = get_post($post_id, ARRAY_A);
            unset($post['post_content']);
            $this->insert_post($entry_id, $post, true, $form_id);
        } else {
            $this->create_post($entry_id, $form_id);
        }
    }
    
    function create_post($entry_id, $form_id){
        if ( !isset($_POST['frm_wp_post']) ) {
            return;
        }
        
        global $wpdb, $frmdb, $frmpro_display;
        $post_id = NULL;
        
        $post = array(
            'post_type' => FrmProFormsHelper::post_type($form_id),
        );

        if ( isset($_POST['frm_user_id']) && is_numeric($_POST['frm_user_id']) ) {
            $post['post_author'] = $_POST['frm_user_id'];
        }
            
        $status = false;
        foreach ( $_POST['frm_wp_post'] as $post_data => $value ) {
            if ( $status ) {
                continue;
            }
            
            $post_data = explode('=', $post_data);
                
            if ( $post_data[1] == 'post_status' ) {
                $status = true;
            }
        }
        
        if ( !$status ) {
            $form_options = $frmdb->get_var($wpdb->prefix .'frm_forms', array('id' => $form_id), 'options');
            $form_options = maybe_unserialize($form_options);
            if ( isset($form_options['post_status']) && $form_options['post_status'] == 'publish' ) {
                $post['post_status'] = 'publish';
            }
        }
        
        //check for auto view and set frm_display_id
        $display = $frmpro_display->get_auto_custom_display(compact('form_id', 'entry_id'));
        if ( $display ) {
            $_POST['frm_wp_post_custom']['=frm_display_id'] = $display->ID;
        }
        
        $post_id = $this->insert_post($entry_id, $post, false, $form_id);
    }
    
    function insert_post($entry_id, $post, $editing=false, $form_id=false){
        $field_ids = $new_post = array();
        
        foreach($_POST['frm_wp_post'] as $post_data => $value){
            $post_data = explode('=', $post_data);
            $field_ids[] = (int) $post_data[0];
            
            if(isset($new_post[$post_data[1]]))
                $value = array_merge((array)$value, (array)$new_post[$post_data[1]]);
            
            $post[$post_data[1]] = $new_post[$post_data[1]] = $value;
            //delete the entry meta below so it won't be stored twice
        }
        
        //if empty post content and auto display, then save compiled post content
        $display_id = ($editing) ? get_post_meta($post['ID'], 'frm_display_id', true) : (isset($_POST['frm_wp_post_custom']['=frm_display_id']) ? $_POST['frm_wp_post_custom']['=frm_display_id'] : 0);
        
        if(!isset($post['post_content']) and $display_id){
            $dyn_content = get_post_meta($display_id, 'frm_dyncontent', true);
            $post['post_content'] = apply_filters('frm_content', $dyn_content, $form_id, $entry_id);
        }
        
        if ( isset($post['post_date']) && !empty($post['post_date']) && ( !isset($post['post_date_gmt']) || $post['post_date_gmt'] == '0000-00-00 00:00:00' ) ) {
            // set post date gmt if post date is set
            $post['post_date_gmt'] = get_gmt_from_date($post['post_date']);
		}
        
        $post_ID = wp_insert_post( $post );
    	
    	if ( is_wp_error( $post_ID ) or empty($post_ID))
    	    return;
    	
    	// Add taxonomies after save in case user doesn't have permissions
    	if(isset($_POST['frm_tax_input']) ){
            foreach ($_POST['frm_tax_input'] as $taxonomy => $tags ) {
                if ( is_taxonomy_hierarchical($taxonomy) )
    				$tags = array_keys($tags);
    			
                wp_set_post_terms( $post_ID, $tags, $taxonomy );
    			
    			unset($taxonomy);
    			unset($tags);
    		}
        }
    	
    	global $frm_entry_meta, $user_ID, $frm_vars, $wpdb;

    	$exclude_attached = array();
    	if(isset($frm_vars['media_id']) and !empty($frm_vars['media_id'])){
    	    global $wpdb;
    	    //link the uploads to the post
    	    foreach((array)$frm_vars['media_id'] as $media_id){
    	        $exclude_attached = array_merge($exclude_attached, (array)$media_id);
    	        
    	        if(is_array($media_id)){
    	            $attach_string = implode( ',', array_filter($media_id) );
    	            if ( !empty($attach_string) ){
    				    $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_parent = %d WHERE post_type = %s AND ID IN ( $attach_string )", $post_ID, 'attachment' ) );
    				
    	                foreach($media_id as $m){
    	                    clean_attachment_cache( $m );
    	                    unset($m);
    	                }
    	            }
    	        }else{
    	            $wpdb->update( $wpdb->posts, array('post_parent' => $post_ID), array( 'ID' => $media_id, 'post_type' => 'attachment' ) );
    	            clean_attachment_cache( $media_id );
    	        }
    	    }
    	}

    	if($editing and count($_FILES) > 0){
    	    global $wpdb;
    	    $args = array( 
    	        'post_type' => 'attachment', 'numberposts' => -1, 
    	        'post_status' => null, 'post_parent' => $post_ID, 
    	        'exclude' => $exclude_attached
    	    ); 

            //unattach files from this post
            $attachments = get_posts( $args );
            foreach($attachments as $attachment)
                $wpdb->update( $wpdb->posts, array('post_parent' => null), array( 'ID' => $attachment->ID ) );
    	}

    	if(isset($_POST['frm_wp_post_custom'])){
        	foreach($_POST['frm_wp_post_custom'] as $post_data => $value){
        	    $post_data = explode('=', $post_data);
                $field_id = $post_data[0];

                if($value == '')
                    delete_post_meta($post_ID, $post_data[1]);
                else
                    update_post_meta($post_ID, $post_data[1], $value);
            	$frm_entry_meta->delete_entry_meta($entry_id, $field_id);
            	
            	unset($post_data);
            	unset($value);
            }
        }
        
        if ( !$editing ) {
            //save post_id with the entry
            if ( $wpdb->update( $wpdb->prefix .'frm_items', array('post_id' => $post_ID), array( 'id' => $entry_id ) ) ) {
                wp_cache_delete( $entry_id, 'frm_entry' );
            }
        }
        
        if(isset($dyn_content)){
            $new_content = apply_filters('frm_content', $dyn_content, $form_id, $entry_id);
            if($new_content != $post['post_content']){
                global $wpdb;
                $wpdb->update( $wpdb->posts, array( 'post_content' => $new_content ), array('ID' => $post_ID) );
            }
        }
        
        // delete entry meta so it won't be duplicated
        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}frm_item_metas WHERE item_id=%d AND field_id", $entry_id) . " IN (". implode(',', $field_ids) .")");
        
    	update_post_meta( $post_ID, '_edit_last', $user_ID );
    	return $post_ID;
    }
    
    function destroy_post($entry_id, $entry) {
        if ( $entry ) {
            $post_id = $entry->post_id;
        } else {
            global $wpdb;
            $post_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$wpdb->prefix}frm_items WHERE id=%d", $entry_id));
        }
        
        if ( $post_id ) {
            wp_delete_post($post_id);
        }
    }
    
    function create_comment($entry_id, $form_id){
        $comment_post_ID = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;

        $post = get_post($comment_post_ID);

        if ( empty($post->comment_status) )
        	return;

        // get_post_status() will get the parent status for attachments.
        $status = get_post_status($post);

        $status_obj = get_post_status_object($status);

        if ( !comments_open($comment_post_ID) ) {
        	do_action('comment_closed', $comment_post_ID);
        	//wp_die( __('Sorry, comments are closed for this item.') );
        	return;
        } elseif ( 'trash' == $status ) {
        	do_action('comment_on_trash', $comment_post_ID);
        	return;
        } elseif ( !$status_obj->public && !$status_obj->private ) {
        	do_action('comment_on_draft', $comment_post_ID);
        	return;
        } elseif ( post_password_required($comment_post_ID) ) {
        	do_action('comment_on_password_protected', $comment_post_ID);
        	return;
        } else {
        	do_action('pre_comment_on_post', $comment_post_ID);
        }

        $comment_content      = ( isset($_POST['comment']) ) ? trim($_POST['comment']) : '';

        // If the user is logged in
        $user_ID = get_current_user_id();
        if ( $user_ID ) {
            global $current_user;
        
        	$display_name = (!empty( $current_user->display_name )) ? $current_user->display_name : $current_user->user_login;
        	$comment_author       = $display_name;
        	$comment_author_email = ''; //get email from field
        	$comment_author_url   = $user->user_url;
        }else{
            $comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : '';
            $comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : '';
            $comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : '';
        }

        $comment_type = '';

        if (!$user_ID and get_option('require_name_email') and (6 > strlen($comment_author_email) || $comment_author == '') )
        		return;

        if ( $comment_content == '')
        	return;


        $commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'user_ID');

        $comment_id = wp_new_comment( $commentdata );
 
    }
    
    // check if entry being updated just switched draft status
    public function is_new_entry($entry) {
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntriesController::is_new_entry');
        return FrmProEntriesHelper::is_new_entry($entry);
    }
    
    public function check_draft_status($values, $id){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntriesController::check_draft_status');
        return FrmProEntriesController::check_draft_status($values, $id);
    }
    
    function get_field($field='is_draft', $id){
        $entry = wp_cache_get( $id, 'frm_entry' );
        if($entry)
            return $entry->{$field};
        
        global $wpdb, $frmdb;
        return $wpdb->get_var($wpdb->prepare("SELECT $field FROM $frmdb->entries WHERE id=%d", $id));
    }
	
	//If page size is set for views, only get the current page of entries
	function get_view_page( $current_p, $p_size, $where, $args ){
		//Make sure values are ints for use in DB call
		$current_p = (int) $current_p;
		$p_size = (int) $p_size;
		
		//Calculate end_index and start_index
        $end_index = $current_p * $p_size;
        $start_index = $end_index - $p_size;
		
		//Set limit and pass it to get_view_results
		$args['limit'] = " LIMIT $start_index,$p_size";
		$results = $this->get_view_results($where, $args);
		
        return $results;
    }
	
	//Jamie's new function for returning ordered entries for Views
    function get_view_results($where, $args){
        global $wpdb;
		
		$defaults = array(
			'order_by_array' => array(), 'order_array' => array(),
			'limit' 	=> '', 'posts' => array(), 'meta' => 'get_meta',
		);
		
		extract(wp_parse_args($args, $defaults));
		
		if ( !empty($order_by_array) ) {//If order is set

			//Set number of fields to zero initially
			$numbers = 0;
			
			//Remove other ordering fields if created_at or updated_at is selected for first ordering field
			if ( reset($order_by_array) == 'created_at' || reset($order_by_array) == 'updated_at' ) {
				foreach ( $order_by_array as $o_key => $order_by_field ) {
					if ( is_numeric($order_by_field) ) {
						unset($order_by_array[$o_key]);
						unset($order_array[$o_key]);
					}
				}
			} else {
			//Get number of fields in $order_by_array - this will not include created_at, updated_at, or random
				foreach ( $order_by_array as $order_by_field ) {
					if ( is_numeric($order_by_field) ) {
						$numbers++;
					}
				}
			}
			
		    if ( in_array('rand', $order_by_array) ) { //If random is set, set the order to random
				$query_1 = "SELECT it.id, it.item_key, it.name, it.ip, it.form_id, it.post_id, it.user_id, it.updated_by,
	            it.created_at, it.updated_at, it.is_draft FROM {$wpdb->prefix}frm_items it";
				$query_2 = " WHERE ";
				$query_3 = " ORDER BY RAND()";
				
		    } else if ( $numbers > 0 ) { //If ordering by at least one field (not just created_at or updated_at)
		        global $frm_entry_meta, $frm_field;

				$order_fields = array();
				foreach ( $order_by_array as $o_key => $order_by_field ) {
					if ( is_numeric($order_by_field) ) {
						$order_fields[$o_key] = $frm_field->getOne($order_by_field);
					} else {
						$order_fields[$o_key] = $order_by_field;
					}
				}

				//Get all post IDs for this form
	            $linked_posts = array();
	           	foreach($posts as $post_meta)
	            	$linked_posts[$post_meta->post_id] = $post_meta->id;


				$query_1 = '';
				foreach($order_fields as $o_key => $o_field){
					if(empty($query_1)){
						$query_1 = "SELECT it.id, it.item_key, it.name, it.ip, it.form_id, it.post_id, it.user_id, it.updated_by,
                it.created_at, it.updated_at, it.is_draft FROM {$wpdb->prefix}frm_items it";
						if(isset($o_field->field_options['post_field']) and $o_field->field_options['post_field']){//if field is some type of post field
							if($o_field->field_options['post_field'] == 'post_custom'){//if field is custom field					
								$query_1 .= " LEFT JOIN {$wpdb->postmeta} pm$o_key ON pm$o_key.post_id=it.post_id AND pm$o_key.meta_key='". $o_field->field_options['custom_field']."' ";
								$query_2 = "WHERE ";//pm$o_key.post_id in (". implode(',', array_keys($linked_posts)).") AND ";
								$query_3 = " ORDER BY CASE when pm$o_key.meta_value IS NULL THEN 1 ELSE 0 END, pm$o_key.meta_value {$order_array[$o_key]}, ";
							}else if($o_field->field_options['post_field'] != 'post_category'){//if field is a non-category post field
								$query_1 .= " INNER JOIN {$wpdb->posts} p$o_key ON p$o_key.ID=it.post_id ";
								$query_2 = "WHERE p$o_key.ID in (". implode(',', array_keys($linked_posts)).") AND ";
								$query_3 = " ORDER BY CASE p$o_key.".$o_field->field_options['post_field']." WHEN '' THEN 1 ELSE 0 END, p$o_key.".$o_field->field_options['post_field']." {$order_array[$o_key]}, ";
							} /*else { //First order field is a category field
								$query_1 .= " INNER JOIN {$wpdb->prefix}term_relationships tr ON it.post_id=tr.object_id 
											INNER JOIN {$wpdb->prefix}term_taxonomy tt ON tr.term_taxonomy_id=tt.term_taxonomy_id
											INNER JOIN {$wpdb->prefix}terms t ON tt.term_id=t.term_id ";
								$query_2 = "WHERE it.post_id in (". implode(',', array_keys($linked_posts)).") 
											AND tt.taxonomy='{$o_field->field_options['taxonomy']}' 
											AND t.term_id NOT in (" . implode(',', $o_field->field_options['exclude_cat']) . ") AND ";
								$query_3 = " ORDER BY t.name {$order_array[$o_key]}, ";
							}*/
						}else{//if field is a normal, non-post field
							//Meta value is only necessary for time field reordering and only if time field is first ordering field
							$query_1 .= " LEFT JOIN {$wpdb->prefix}frm_item_metas em$o_key ON em$o_key.item_id=it.id AND em$o_key.field_id=$o_field->id ";
							$query_2 = "WHERE ";
							$query_3 = " ORDER BY CASE when em$o_key.meta_value IS NULL THEN 1 ELSE 0 END, em$o_key.meta_value".( in_array($o_field->type, array('number', 'scale')) ? ' +0 ' : '')." {$order_array[$o_key]}, ";
							//Check if time field (for time field ordering)
							if ( $o_field->type == 'time' ) { $time_field = $o_field; }
						}								
					}else{
						if(isset($o_field->field_options['post_field']) and $o_field->field_options['post_field']){
							if($o_field->field_options['post_field'] == 'post_custom'){//if ordering by a custom field									
								$query_1 .= "LEFT JOIN {$wpdb->postmeta} pm$o_key ON pm$o_key.post_id=it.post_id AND pm$o_key.meta_key='". $o_field->field_options['custom_field']."' ";
								$query_3 .= "CASE when pm$o_key.meta_value IS NULL THEN 1 ELSE 0 END, pm$o_key.meta_value {$order_array[$o_key]}, ";
							}else if($o_field->field_options['post_field'] != 'post_category'){//if ordering by a non-category post field
								$query_1 .= "LEFT JOIN {$wpdb->posts} p$o_key ON p$o_key.ID=it.post_id ";
								$query_3 .= "CASE p$o_key.".$o_field->field_options['post_field']." WHEN '' THEN 1 ELSE 0 END, p$o_key.".$o_field->field_options['post_field']." {$order_array[$o_key]}, ";
							} /*else {//if ordering by a category field
								$query_1 .= "LEFT JOIN (SELECT tr$o_key.object_id as object_id, t$o_key.name as name FROM {$wpdb->prefix}term_relationships tr$o_key
											INNER JOIN {$wpdb->prefix}term_taxonomy tt$o_key ON tr$o_key.term_taxonomy_id=tt$o_key.term_taxonomy_id 
											INNER JOIN {$wpdb->prefix}terms t$o_key ON tt$o_key.term_id=t$o_key.term_id 
											WHERE tr$o_key.object_id in (". implode(',', array_keys($linked_posts)).") AND tt$o_key.taxonomy='{$o_field->field_options['taxonomy']}' AND t$o_key.term_id NOT in (" . implode(',', $o_field->field_options['exclude_cat']) . "))
											 as temp$o_key ON it.post_id=temp$o_key.object_id ";
								$query_3 .= "temp$o_key.name {$order_array[$o_key]}, ";
							}*/
						}else{
							if(is_numeric($order_by_array[$o_key])){//if ordering by a normal, non-post field
								$query_1 .= "LEFT JOIN {$wpdb->prefix}frm_item_metas em$o_key ON em$o_key.item_id=it.id AND em$o_key.field_id={$o_field->id} ";
								$query_3 .= "CASE when em$o_key.meta_value IS NULL THEN 1 ELSE 0 END, em$o_key.meta_value".( in_array($o_field->type, array('number', 'scale')) ? ' +0 ' : '')." {$order_array[$o_key]}, ";
							}else{//if ordering by created at or updated at
								$query_3 .= "it.".$o_field." ".$order_array[$o_key].", ";
							}					
						}								
					}
					unset($o_field);
				}
			} else {//If ordering by creation date and/or update date without any fields
				$query_1 = "SELECT it.id, it.item_key, it.name, it.ip, it.form_id, it.post_id, it.user_id, it.updated_by,
                it.created_at, it.updated_at, it.is_draft FROM {$wpdb->prefix}frm_items it";
				$query_2 = " WHERE ";
				$query_3 = " ORDER BY";
				
				foreach ( $order_by_array as $o_key => $order_by ) {
				    if ( empty($order_by) ) {
				        continue;
				    }
					
					$query_3 .= " it." . $order_by . " " . $order_array[$o_key] . ", ";
					unset($order_by);
				}
			} 
		} else { //If no order is set
			$query_1 = "SELECT it.id, it.item_key, it.name, it.ip, it.form_id, it.post_id, it.user_id, it.updated_by,
            it.created_at, it.updated_at, it.is_draft FROM {$wpdb->prefix}frm_items it";
			$query_2 = " WHERE ";
			$query_3 = " ORDER BY it.created_at ASC";
		}
		$query_3 = rtrim($query_3, ', ');
		$query = $query_1 . $query_2 . $where . $query_3 . $limit;
        $entries = $wpdb->get_results($query, OBJECT_K);
		
		unset($query, $query_1, $query_2, $query_3, $where, $limit);
		
		//If meta is not needed or if there aren't any entries, end function
        if ( $meta != 'get_meta' || !$entries ) {
			return stripslashes_deep($entries);
		}
		
		//Get metas
		$get_entry_ids = array_keys($entries);
		foreach ( $get_entry_ids as $k => $e ) {
			if ( wp_cache_get($e, 'frm_entry') ) {
				unset($get_entry_ids[$k]);
			}
			unset($k, $e);
		}
		
		if ( empty($get_entry_ids) ) {
			return stripslashes_deep($entries);
		}
		
        $meta_where = "item_id in (". implode(',', array_filter($get_entry_ids, 'is_numeric')) .")";
        
        $query = "SELECT item_id, meta_value, field_id, field_key FROM {$wpdb->prefix}frm_item_metas it 
            LEFT OUTER JOIN {$wpdb->prefix}frm_fields fi ON it.field_id=fi.id 
            WHERE $meta_where and field_id != 0";
        
        $metas = $wpdb->get_results($query);
        unset($query);
		
        if ( $metas ) {
            foreach ( $metas as $m_key => $meta_val ) {
                if ( !isset($entries[$meta_val->item_id]) ) {
                    continue;
				}   
                if ( !isset($entries[$meta_val->item_id]->metas) ) {
                    $entries[$meta_val->item_id]->metas = array();
				}
                    
				$entries[$meta_val->item_id]->metas[$meta_val->field_id] = maybe_unserialize($meta_val->meta_value);
				unset($m_key, $meta_val);
            }
            
			//Cache each entry
            foreach ( $entries as $entry ) {
                wp_cache_set( $entry->id, $entry, 'frm_entry');
                unset($entry);
            }
        }
		
		//Reorder entries if 12 hour time field is selected for first ordering field. If the $time_field variable is set, this means the first ordering field is a time field.
		if ( isset($time_field) && ( !isset($time_field->field_options['clock']) || ($time_field->field_options['clock'] == 12) ) && is_array($entries) && !empty($entries) ) {
	
			//Reorder entries
        	$new_order = array();
			$empty_times = array();
			foreach ( $entries as $e_key => $entry ) {
				if ( !isset($entry->metas[$time_field->id]) ) {
					$empty_times[$e_key] = '';
					continue;
				}
            	$parts = str_replace(array(' PM',' AM'), '', $entry->metas[$time_field->id]);
            	$parts = explode(':', $parts);
            	if ( is_array($parts) ) {
                	if ( ( preg_match('/PM/', $entry->metas[$time_field->id]) && ((int)$parts[0] != 12) ) || 
                    ( ((int)$parts[0] == 12) && preg_match('/AM/', $entry->metas[$time_field->id]) ) )
                    	$parts[0] = ((int)$parts[0] + 12);
            	}

            	$new_order[$e_key] = (int)$parts[0] . $parts[1];

            	unset($e_key);
            	unset($entry);
			}

        	//array with sorted times
        	asort($new_order);

			$new_order = $new_order + $empty_times;

        	$final_order = array();
        	foreach ( $new_order as $key => $time ) {
            	$final_order[] = $entries[$key];
            	unset($key, $time);
        	}

        	$entries = $final_order;
        	unset($final_order);
		}
		unset($order_by_array, $order_array, $first_order_field);
        
        return stripslashes_deep($entries);
    }
}
