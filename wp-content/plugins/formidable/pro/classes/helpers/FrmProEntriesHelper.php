<?php

class FrmProEntriesHelper{
    
    // check if form should automatically be in edit mode (limited to one, has draft)
    public static function allow_form_edit($action, $form){
        $user_ID = get_current_user_id();
        if (!$form or !$user_ID)
            return $action;
            
        if(!$form->editable)
            $action = 'new';
        
        $is_draft = false;
        if($action == 'destroy')
            return $action;
        
        if (($form->editable and (isset($form->options['single_entry']) and $form->options['single_entry'] and $form->options['single_entry_type'] == 'user') or (isset($form->options['save_draft']) and $form->options['save_draft']))){
            if($action == 'update' and ($form->id == FrmAppHelper::get_param('form_id'))){
                //don't change the action is this is the wrong form
            }else{
                global $frmdb;
                $args = array('user_id' => $user_ID, 'form_id' => $form->id);
                if(isset($form->options['save_draft']) and $form->options['save_draft'] and (!$form->editable or !isset($form->options['single_entry']) or !$form->options['single_entry'] or $form->options['single_entry_type'] != 'user'))
                    $args['is_draft'] = $is_draft = 1;
                
                $meta = $frmdb->get_var($frmdb->entries, $args);
                
                if($meta)
                    $action = 'edit';
            }
        }
       
        //do not allow editing if user does not have permission
        if ($action == 'edit' and !$is_draft){
            $entry = FrmAppHelper::get_param('entry', 0);
            if ( !FrmProEntriesHelper::user_can_edit($entry, $form) ) {
                $action = 'new';
            }
        }
        
        return $action;
    }
    
    public static function user_can_edit($entry, $form=false) {
        if ( empty($form) ) {
            if ( is_numeric($entry) ) {
                $frm_entry = new FrmEntry();
                $entry = $frm_entry->getOne($entry);
            }
            
            if ( is_object($entry) ) {
                $form = $entry->form_id;
            }
        }
        
        if ( is_numeric($form) ) {
            $frm_form = new FrmForm();
            $form = $frm_form->getOne($form);
        }
        
        $allowed = self::user_can_edit_check($entry, $form);
        return apply_filters('frm_user_can_edit', $allowed, compact('entry', 'form'));
    }
    
    public static function user_can_edit_check($entry, $form) {
        global $frm_entry, $wpdb;
        
        $user_ID = get_current_user_id();
        
        if ( !$user_ID || empty($form) ) {
            return false;
        }
        
        if ( is_object($entry) && $entry->is_draft && $entry->user_id == $user_ID ) {
            return true;
        }
        
        //if editable and user can edit someone elses entry
        if ( $entry && $form->editable && ((isset($form->options['open_editable']) && $form->options['open_editable']) || !isset($form->options['open_editable'])) && isset($form->options['open_editable_role']) && FrmAppHelper::user_has_permission($form->options['open_editable_role']) ) {
            return true;
        }
        
        $where = $wpdb->prepare('fr.id=%d', $form->id);
        
        if ( $form->editable && !empty($form->options['editable_role']) && !FrmAppHelper::user_has_permission($form->options['editable_role']) && (!isset($form->options['open_editable_role']) || $form->options['open_editable_role'] ==  '-1' || ((isset($form->options['open_editable']) && !$form->options['open_editable']) || (isset($form->options['open_editable']) && $form->options['open_editable'] && !empty($form->options['open_editable_role']) && !FrmAppHelper::user_has_permission($form->options['open_editable_role'])))) ) {
            //only allow editing of drafts
            $where .= $wpdb->prepare(" and user_id=%d and is_draft=%d", $user_ID, 1);
        }
        
        // check if this user can edit entry from another user
        if ( !$form->editable || !isset($form->options['open_editable_role']) || $form->options['open_editable_role'] == '-1' || (isset($form->options['open_editable']) && empty($form->options['open_editable'])) || !FrmAppHelper::user_has_permission($form->options['open_editable_role']) ) {            
            $where .= $wpdb->prepare(" and user_id=%d", $user_ID);
            
            if ( is_object($entry) && $entry->user_id != $user_ID ) {
                return false;
            }
            
            if ( $form->editable && !FrmAppHelper::user_has_permission($form->options['open_editable_role']) && !FrmAppHelper::user_has_permission($form->options['editable_role']) ) {
                // make sure user cannot edit their own entry, even if a higher user role can unless it's a draft
                if ( is_object($entry) && !$entry->is_draft ) {
                    return false;
                } else if ( !is_object($entry) ) {
                    $where .= ' and is_draft=1';
                }
            }
        } else if ( $form->editable && $user_ID && empty($entry) ) {
            // make sure user is editing their own draft by default, even if they have permission to edit others' entries
           $where .= $wpdb->prepare(" and user_id=%d", $user_ID);
        }
        
        if ( !$form->editable ) {
            $where .= ' and is_draft=1';

            if ( is_object($entry) && !$entry->is_draft ) {
                return false;
            }
        }
        
        // If entry object, and we made it this far, then don't do another db call
        if ( is_object($entry) ) {
            return true;
        }
        
        if ( !empty($entry) ) {
            $where .= $wpdb->prepare( is_numeric($entry) ? " and it.id=%d" : " and item_key=%s", $entry);
        }
        
        return $frm_entry->getAll( $where, ' ORDER BY created_at DESC', 1, true);
    }
    
    public static function user_can_delete($entry, $form = false) {
        if ( is_numeric($entry) ) {
            $frm_entry = new FrmEntry();
            $entry = $frm_entry->getOne($entry);
            if ( ! $entry ) {
                return false;
            }
        }
        
        if ( current_user_can('frm_delete_entries') ) {
            $allowed = true;
        } else {
            $allowed = self::user_can_edit($entry);
            if ( !empty($allowed) ) {
                $allowed = true;
            }
        }
        
        return apply_filters('frm_allow_delete', $allowed, $entry);
    }
    
    public static function allow_delete($entry){
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProEntriesHelper::user_can_delete' );
        return self::user_can_delete($entry);
    }
    
    public static function setup_edit_vars($values, $record=false){
        global $frmpro_settings;
        if(!$record){
            $frm_form = new FrmForm();
            $record = $frm_form->getOne($values['form_id']);
        }
        
        foreach (array('edit_value' => __('Update', 'formidable'), 'edit_msg' => $frmpro_settings->edit_msg) as $opt => $default){
            if (!isset($values[$opt]))
                $values[$opt] = ($_POST and isset($_POST['options'][$opt])) ? $_POST['options'][$opt] : $default;
        }
        return $values;
    }
    
    public static function resend_email_links($entry_id, $form_id){ ?>
<a href="#" onclick="frm_resend_email(<?php echo $entry_id ?>,<?php echo $form_id ?>);return false;" id="frm_resend_email" title="<?php _e('Resend Email Notifications', 'formidable') ?>"><?php _e('Resend Email Notifications', 'formidable') ?></a>
<?php
    }
    
    public static function before_table($footer, $form_id=false){
        if ( $_GET['page'] != 'formidable-entries' ) {
            return;
        }
        
        if ( $footer ) {
            if ( apply_filters('frm_show_delete_all', current_user_can('frm_edit_entries'), $form_id) ) { 
            ?><div class="frm_uninstall alignleft actions"><a href="?page=formidable-entries&amp;frm_action=destroy_all<?php echo $form_id ? '&amp;form='. $form_id : '' ?>" class="button" onclick="return confirm('<?php _e('Are you sure you want to permanently delete ALL the entries in this form?', 'formidable') ?>')"><?php _e('Delete ALL Entries', 'formidable') ?></a></div>
<?php
            }
            return;
        }
        
        $page_params = array('frm_action' => 0, 'action' => 'frm_entries_csv', 'form' => $form_id);
        
        if ( !empty( $_REQUEST['s'] ) )
            $page_params['s'] = $_REQUEST['s'];
        
        if ( !empty( $_REQUEST['search'] ) )
            $page_params['search'] = $_REQUEST['search'];

    	if ( !empty( $_REQUEST['fid'] ) )
    	    $page_params['fid'] = $_REQUEST['fid'];
    	
        ?>
        <div class="alignleft actions"><a href="<?php echo esc_url(add_query_arg($page_params, admin_url( 'admin-ajax.php' ))) ?>" class="button"><?php _e('Download CSV', 'formidable'); ?></a></div>
        <?php
    }
    
    // check if entry being updated just switched draft status
    public static function is_new_entry($entry) {
        if ( is_numeric($entry) ) {
            $frm_entry = new FrmEntry;
            $entry = $frm_entry->getOne($entry);
        }
        
        // this function will only be correct if the entry has already gone through FrmProEntriesController::check_draft_status
        if ( $entry->created_at == $entry->updated_at ) {
            return true;
        }
        
        return false;
    }
    
    public static function get_field($field = 'is_draft', $id) {
        $entry = wp_cache_get( $id, 'frm_entry' );
        if ( $entry && isset($entry->$field) ) {
            return $entry->$field;
        }
        
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT $field FROM {$wpdb->prefix}frm_items WHERE id=%d", $id));
    }
    
    public static function get_search_ids($s, $form_id){
        global $wpdb, $frmdb, $frm_entry_meta;
        
        if(empty($s)) return false;
        
		preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $s, $matches);
		$search_terms = array_map('trim', $matches[0]);
		$n = '%'; //!empty($q['exact']) ? '' : '%';
		
        $p_search = $search = '';
        $search_or = '';
        $e_ids = array();
        
        $data_field = FrmProFormsHelper::has_field('data', $form_id, false);
        
		foreach( (array) $search_terms as $term ) {
			$term = esc_sql( like_escape( $term ) );
			$p_search .= " AND (($wpdb->posts.post_title LIKE '{$n}{$term}{$n}') OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}'))";
			
			$search .= "{$search_or}meta_value LIKE '{$n}{$term}{$n}'";
            $search_or = ' OR ';
            if(is_numeric($term))
                $e_ids[] = (int)$term;
            
            if($data_field){
                $df_form_ids = array();
                
                //search the joined entry too
                foreach((array)$data_field as $df){
                    $df->field_options = maybe_unserialize($df->field_options);
                    if (is_numeric($df->field_options['form_select']))
                        $df_form_ids[] = $df->field_options['form_select'];
                    
                    unset($df);
                }
                
                global $wpdb, $frmdb;
                $data_form_ids = $wpdb->get_col("SELECT form_id FROM $frmdb->fields WHERE id in (". implode(',', $df_form_ids).")");
                unset($df_form_ids);
                
                if($data_form_ids){
                    $data_entry_ids = $frm_entry_meta->getEntryIds("fi.form_id in (". implode(',', $data_form_ids).") and meta_value LIKE '%". $term ."%'");
                    if($data_entry_ids)
                        $search .= "{$search_or}meta_value in (".implode(',', $data_entry_ids).")";
                }
                
                unset($data_form_ids);
            }
		}
		
		$p_ids = '';
		$matching_posts = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE 1=1 $p_search");
		if($matching_posts){
		    $p_ids = $wpdb->get_col("SELECT id from $frmdb->entries WHERE post_id in (". implode(',', $matching_posts) .") AND form_id=". (int)$form_id);
		    $p_ids = ($p_ids) ? " OR item_id in (". implode(',', $p_ids) .")" : '';
		}
		
		if(!empty($e_ids))
		    $p_ids .= " OR item_id in (". implode(',', $e_ids) .")";
		    
		
        return $frm_entry_meta->getEntryIds("(($search)$p_ids) and fi.form_id='$form_id'");
    }
    
    public static function encode_value($line, $from_encoding, $to_encoding){
        $convmap = false;
        
        switch($to_encoding){
            case 'macintosh':
            // this map was derived from the differences between the MacRoman and UTF-8 Charsets
            // Reference:
            //   - http://www.alanwood.net/demos/macroman.html
                $convmap = array(
                    256, 304, 0, 0xffff,
                    306, 337, 0, 0xffff,
                    340, 375, 0, 0xffff,
                    377, 401, 0, 0xffff,
                    403, 709, 0, 0xffff,
                    712, 727, 0, 0xffff,
                    734, 936, 0, 0xffff,
                    938, 959, 0, 0xffff,
                    961, 8210, 0, 0xffff,
                    8213, 8215, 0, 0xffff,
                    8219, 8219, 0, 0xffff,
                    8227, 8229, 0, 0xffff,
                    8231, 8239, 0, 0xffff,
                    8241, 8248, 0, 0xffff,
                    8251, 8259, 0, 0xffff,
                    8261, 8363, 0, 0xffff,
                    8365, 8481, 0, 0xffff,
                    8483, 8705, 0, 0xffff,
                    8707, 8709, 0, 0xffff,
                    8711, 8718, 0, 0xffff,
                    8720, 8720, 0, 0xffff,
                    8722, 8729, 0, 0xffff,
                    8731, 8733, 0, 0xffff,
                    8735, 8746, 0, 0xffff,
                    8748, 8775, 0, 0xffff,
                    8777, 8799, 0, 0xffff,
                    8801, 8803, 0, 0xffff,
                    8806, 9673, 0, 0xffff,
                    9675, 63742, 0, 0xffff,
                    63744, 64256, 0, 0xffff,
                );
            break;
            case 'ISO-8859-1':
                $convmap = array(256, 10000, 0, 0xffff);
            break;
        }
        
        if (is_array($convmap))
            $line = mb_encode_numericentity($line, $convmap, $from_encoding);
        
        if ($to_encoding != $from_encoding)
            return iconv($from_encoding, $to_encoding.'//IGNORE', $line);
        else
            return $line;
    }
}
