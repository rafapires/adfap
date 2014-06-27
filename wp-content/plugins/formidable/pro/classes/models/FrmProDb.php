<?php
class FrmProDb{
    
    function upgrade(){
        global $wpdb, $frmdb;
        $db_version = FrmAppHelper::$pro_db_version; // this is the version of the database we're moving to
        $old_db_version = get_option('frmpro_db_version');

        if ($db_version == $old_db_version) {
            return;
        }
            
        // update rewrite rules for views
        flush_rewrite_rules();
        
        if ( $old_db_version ) {
            if ( $db_version >= 3 && $old_db_version < 3 ) { //migrate hidden field data into the parent field
                global $frm_field;
                $wpdb->update( $frmdb->fields, array('type' => 'scale'), array('type' => '10radio') );
                $fields = $frm_field->getAll();
                foreach($fields as $field){
                    $field->field_options = maybe_unserialize($field->field_options);
                    if(isset($field->field_options['hide_field']) and is_numeric($field->field_options['hide_field']) and
                        ((isset($field->field_options['hide_opt']) and !empty($field->field_options['hide_opt'])) or
                        (isset($field->field_options['form_select']) and !empty($field->field_options['form_select'])))){
                        global $frm_field;
                        //save hidden fields to parent field
                        $parent_field = $frm_field->getOne($field->field_options['hide_field']);
                        if($parent_field){
                            $parent_options = maybe_unserialize($parent_field->field_options);
                            if(!isset($parent_options['dependent_fields']))
                                $parent_options['dependent_fields'] = array();
                            else{
                              foreach($parent_options['dependent_fields'] as $child_id => $child_opt){
                                  if(empty($child_opt) or $child_opt == ''){
                                      unset($parent_options['dependent_fields'][$child_id]);
                                  }else if($child_id != $field->id){
                                      //check to make sure this field is still dependent
                                      $check_field = $frm_field->getOne($child_id);
                                      $check_options = maybe_unserialize($check_field->field_options);
                                      if(!is_numeric($check_options['hide_field']) or $check_options['hide_field'] != $parent_field->id or (empty($check_options['hide_opt']) and empty($check_options['form_select'])))
                                         unset($parent_options['dependent_fields'][$child_id]); 
                                  }
                              }
                            }
                          
                            $dep_fields = array();
                            if($field->type == 'data' and isset($field->field_options['form_select']) and is_numeric($field_options['form_select'])){
                              $dep_fields[] = $field->field_options['form_select'];
                              $dep_fields[] = $field->field_options['data_type'];
                            }else if(isset($field->field_options['hide_opt']) and !empty($field->field_options['hide_opt']))
                              $dep_fields[] = $field->field_options['hide_opt'];
                              
                            if(!empty($dep_fields))
                              $parent_options['dependent_fields'][$field->id] = $dep_fields;
                          
                            $frm_field->update($parent_field->id, array('field_options' => $parent_options));
                          
                        }
                    }
                }
            }
            
            if ( $db_version >= 16 and $old_db_version < 16 ) { //migrate table into wp_posts
                $display_posts = array();
                if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}frm_display'" ) ) { //only migrate if table exists
                    $dis = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}frm_display");
                } else {
                    $dis = array();
                }
                
                foreach($dis as $d){
                    $post = array(
                        'post_title'      => $d->name,
                        'post_content'    => $d->content,
                        'post_date'       => $d->created_at,
                        'post_excerpt'    => $d->description,
                        'post_name'       => $d->display_key,
                        'post_status'     => 'publish',
                        'post_type'       => 'frm_display'
                    );
                    $post_ID = wp_insert_post( $post );
                    unset($post);
                    
                    update_post_meta($post_ID, 'frm_old_id', $d->id);
                  
                    if(!isset($d->show_count) or empty($d->show_count))
                        $d->show_count = 'none';
                        
                    foreach(array(
                        'dyncontent', 'param', 'form_id', 'post_id', 'entry_id', 
                        'param', 'type', 'show_count', 'insert_loc'
                        ) as $f){
                        update_post_meta($post_ID, 'frm_'. $f, $d->{$f});
                        unset($f);
                    }
                    
                    $d->options = maybe_unserialize($d->options);
                    update_post_meta($post_ID, 'frm_options', $d->options);
                    
                    if(isset($d->options['insert_loc']) and $d->options['insert_loc'] != 'none' and is_numeric($d->options['post_id']) and !isset($display_posts[$d->options['post_id']]))
                        $display_posts[$d->options['post_id']] = $post_ID;
                  
                    unset($d);
                    unset($post_ID);
                }
                unset($dis);
                
                global $frmdb, $frmpro_display;
                //get all post_ids from frm_entries
                $entry_posts = $wpdb->get_results("SELECT id, post_id, form_id FROM $frmdb->entries WHERE post_id > 0");
                $form_display = array();
                foreach($entry_posts as $ep){
                    if(isset($form_display[$ep->form_id])){
                        $display_posts[$ep->post_id] = $form_display[$ep->form_id];
                    }else{
                        $d = $frmpro_display->get_auto_custom_display(array('post_id' => $ep->post_id, 'form_id' => $ep->form_id, 'entry_id' => $ep->id));
                        $display_posts[$ep->post_id] = $form_display[$ep->form_id] = ($d ? $d->ID : 0);
                        unset($d);
                    }
                     
                    unset($ep);
                }
                unset($form_display);
                
                foreach($display_posts as $post_ID => $d){
                    if($d)
                        update_post_meta($post_ID, 'frm_display_id', $d);
                    unset($d);
                    unset($post_ID);
                }
                unset($display_posts);
            }
        
            if ( $db_version >= 17 && $old_db_version < 17 ) {
                $frm_form = new FrmForm();
                //migrate "allow one per field" into "unique"
                $form = $frm_form->getAll();
                $field_ids = array();
                foreach($form as $f){
                    if (isset($f->options['single_entry']) and $f->options['single_entry'] and is_numeric($f->options['single_entry_type'])){
                        $f->options['single_entry'] = 0;
                        $wpdb->update( $wpdb->prefix .'frm_forms', array('options' => serialize($f->options)), array( 'id' => $f->id ) );
                        $field_ids[] = $f->options['single_entry_type'];
                    }
                    unset($f);
                }
                
                if(!empty($field_ids)){
                    $fields = $wpdb->get_results("SELECT id, field_options FROM $frmdb->fields WHERE id in (". implode(',', $field_ids) .")");
                    foreach($fields as $f){
                        $opts = maybe_unserialize($f->field_options);
                        $opts['unique'] = 1;
                        $wpdb->update( $frmdb->fields, array('field_options' => serialize($opts)), array( 'id' => $f->id ) );
                        unset($f);
                    }
                }
            }
            
            if ( $db_version >= 25 && $old_db_version < 25) {
                // let's remove the old displays now
                $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}frm_display");
            }
        }
        
        /**** ADD DEFAULT TEMPLATES ****/
        if ( class_exists('FrmXMLController') ) {
            FrmXMLController::add_default_templates();
        }
        
        update_option('frmpro_db_version', $db_version);
          
        global $frmpro_settings;
        $frmpro_settings->store(); //update the styling settings
    }
    
    function uninstall(){
        if ( !current_user_can('administrator') ) {
            global $frm_settings;
            wp_die($frm_settings->admin_permission);
        }
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}frm_display");
        delete_option('frmpro_options');
        delete_option('frmpro_db_version');
        delete_option('frm_usloc_options'); //locations
        
        global $frm_update;
        delete_option($frm_update->pro_last_checked_store);
        delete_option($frm_update->pro_auth_store);
        delete_option($frm_update->pro_cred_store);
    }

}
