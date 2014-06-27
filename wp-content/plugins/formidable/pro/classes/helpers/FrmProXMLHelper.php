<?php

class FrmProXMLHelper{
    
    public static function import_xml_views($views, $imported) {
        global $frm_duplicate_ids;
        
        $imported['posts'] = array();
        
        foreach ( $views as $item ) {
			$post = array(
				'post_title'    => (string) $item->title,
				'post_name'     => (string) $item->post_name,
				'post_type'     => (string) $item->post_type,
				'post_password' => (string) $item->post_password,
				'guid'          => (string) $item->guid,
				'post_status'   => (string) $item->status,
				'post_author'   => FrmProAppHelper::get_user_id_param( (string) $item->post_author ),
				'post_id'       => (int) $item->post_id,
				'post_parent'   => (int) $item->post_parent,
				'menu_order'    => (int) $item->menu_order,
				'post_content'  => FrmFieldsHelper::switch_field_ids((string) $item->content),
				'post_excerpt'  => FrmFieldsHelper::switch_field_ids((string) $item->excerpt),
				'is_sticky'     => (string) $item->is_sticky,
				'comment_status' => (string) $item->comment_status,
				'post_date'     => (string) $item->post_date,
				'post_date_gmt' => (string) $item->post_date_gmt,
				'ping_status'   => (string) $item->ping_status,
			);

			if ( isset($item->attachment_url) ) {
				$post['attachment_url'] = (string) $item->attachment_url;
			}
            
            $post['postmeta'] = array();
            
			foreach ( $item->postmeta as $meta ) {
			    $m = array(
					'key'   => (string) $meta->meta_key,
					'value' => (string) $meta->meta_value
				);
				
				//switch old form and field ids to new ones
				if ( $m['key'] == 'frm_form_id' && isset($imported['forms'][ (int) $meta->meta_value]) ) {
				    $m['value'] = $imported['forms'][ (int) $meta->meta_value];
				} else {
				    $m['value'] = FrmAppHelper::maybe_json_decode($m['value'], true);
        		    
        		    if ( !empty($frm_duplicate_ids) ) {
        		        
        		        if ( $m['key'] == 'frm_dyncontent' ) {
        		            $m['value'] = FrmFieldsHelper::switch_field_ids($m['value']);
            		    } else if ( $m['key'] == 'frm_options' ) {
            		        
            		        if ( isset($m['value']['date_field_id']) && is_numeric($m['value']['date_field_id']) && isset($frm_duplicate_ids[$m['value']['date_field_id']]) ) {
            		            $m['value']['date_field_id'] = $frm_duplicate_ids[$m['value']['date_field_id']];
            		        }
            		        
            		        if ( isset($m['value']['edate_field_id']) && is_numeric($m['value']['edate_field_id']) && isset($frm_duplicate_ids[$m['value']['edate_field_id']]) ) {
            		            $m['value']['edate_field_id'] = $frm_duplicate_ids[$m['value']['edate_field_id']];
            		        }
            		        
            		        if ( isset($m['value']['order_by']) && !empty($m['value']['order_by']) ) {
            		            if ( is_numeric($m['value']['order_by']) && isset($frm_duplicate_ids[$m['value']['order_by']]) ) {
            		                $m['value']['order_by'] = $frm_duplicate_ids[$m['value']['order_by']];
            		            } else if ( is_array($m['value']['order_by']) ) {
            		                
            		                foreach ( $m['value']['order_by'] as $mk => $mv ) {
            		                    if ( isset($frm_duplicate_ids[$mv]) ) {
            		                        $m['value']['order_by'][$mk] = $frm_duplicate_ids[$mv];
            		                    }
            		                    unset($mk);
            		                    unset($mv);
            		                }
            		                
            		            }
            		        }
            		        
            		        if ( isset($m['value']['where']) && !empty($m['value']['where']) ) {
            		            foreach ( (array) $m['value']['where'] as $mk => $mv ) {
        		                    if ( isset($frm_duplicate_ids[$mv]) ) {
        		                        $m['value']['where'][$mk] = $frm_duplicate_ids[$mv];
        		                    }
        		                    unset($mk);
        		                    unset($mv);
        		                }
            		        }
            		        
            		    }
        		    }
				}
				if ( !is_array($m['value']) ) {
				    $m['value'] = FrmAppHelper::maybe_json_decode($m['value']);
				}
				
				$post['postmeta'][(string) $meta->meta_key] = $m['value'];
				unset($m);
				unset($meta);
			}
			
			//Add terms
			$post['tax_input'] = array();
			foreach ( $item->category as $c ) {
				$att = $c->attributes();
				if ( isset( $att['nicename'] ) ){
				    $taxonomy = (string) $att['domain'];
				    if ( is_taxonomy_hierarchical($taxonomy) ) {
				        $name = (string) $att['nicename'];
				        $h_term = get_term_by('slug', $name, $taxonomy);
				        if ( $h_term ) {
				            $name = $h_term->term_id;
				        }
				        unset($h_term);
				    } else {
				        $name = (string) $c;
				    }
				    
				    if ( !isset($post['tax_input'][$taxonomy]) ) {
				        $post['tax_input'][$taxonomy] = array();
				    }
				    
				    $post['tax_input'][$taxonomy][] = $name;
				    unset($name);
				}
			}
			
			unset($item);
			
			// edit view if the key and created time match
			$old_id = $post['post_id'];
			$match_by =  array(
			    'post_type'     => $post['post_type'],
			    'name'          => $post['post_name'],
			    'post_status'   => $post['post_status'],
			    'posts_per_page' => 1,
			);
			
			if ( in_array($post['post_status'], array('trash', 'draft')) ) {
			    $match_by['include'] = $post['post_id'];
			    unset($match_by['name']);
			}
			
			$editing = get_posts($match_by);
			
            if ( !empty($editing) && current($editing)->post_date == $post['post_date'] ) {
                $post['ID'] = current($editing)->ID;
            }
            
            unset($editing);
            
            //create post
            $post_id = wp_insert_post( $post );
            
            if ( !is_numeric($post_id) ) {
                continue;
            }
            
            foreach ( $post['postmeta'] as $k => $v ) {
                if ( '_edit_last' == $k ) {
                    $v = FrmProAppHelper::get_user_id_param($v);
                } else if ( '_thumbnail_id' == $k ) {
                    //change the attachment ID
                    $v = self::get_file_id($v);
                }
                
                $u = update_post_meta($post_id, $k, $v);
                
                unset($k);
                unset($v);
            }
            
            if ( isset($post['ID']) ) {
                $imported['updated'][ ($post['post_type'] == 'frm_display' ? 'views' : 'posts') ]++;
            } else {
                $imported['imported'][ ($post['post_type'] == 'frm_display' ? 'views' : 'posts') ]++;
            }
            
            unset($post);
            
			$imported['posts'][ (int) $old_id] = $post_id;
		}
		
		return $imported;
    }
    
    public static function import_xml_entries($entries, $imported) {
        global $frm_duplicate_ids, $wpdb, $frm_field;
        
        $frm_entry = new FrmEntry();
        $saved_entries = array();
        
	    foreach ( $entries as $item ) {
	        $entry = array(
	            'id'            => (int) $item->id,
		        'item_key'      => (string) $item->item_key,
		        'name'          => (string) $item->name,
		        'description'   => FrmAppHelper::maybe_json_decode((string) $item->description),
		        'ip'            => (string) $item->ip,
		        'form_id'       => ( isset($imported['forms'][ (int) $item->form_id] ) ? $imported['forms'][ (int) $item->form_id] : (int) $item->form_id),
		        'post_id'       => ( isset($imported['posts'][ (int) $item->post_id] ) ? $imported['posts'][ (int) $item->post_id] : (int) $item->post_id),
		        'user_id'       => FrmProAppHelper::get_user_id_param( (string) $item->user_id ),
		        'parent_item_id' => (int) $item->parent_item_id,
		        'is_draft'      => (int) $item->is_draft,
		        'updated_by'    => FrmProAppHelper::get_user_id_param( (string) $item->updated_by ),
		        'created_at'    => (string) $item->created_at,
		        'updated_at'    => (string) $item->updated_at,
	        );
	        
	        $metas = array();
    		foreach ( $item->item_meta as $meta ) {
    		    $field_id = (int) $meta->field_id;
    		    if ( is_array($frm_duplicate_ids) && isset($frm_duplicate_ids[$field_id] ) ) {
    		        $field_id = $frm_duplicate_ids[$field_id];
    		    }
    		    $field = $frm_field->getOne($field_id);
    		    
    		    if ( !$field ) {
    		        continue;
    		    }
    		    
    		    $metas[$field_id] = FrmAppHelper::maybe_json_decode((string) $meta->meta_value);
    		    
    		    $metas[$field_id] = apply_filters('frm_import_val', $metas[$field_id], $field);
    		    
    		    switch ( $field->type ) {
		            case 'user_id':
		                $metas[$field_id] = FrmProAppHelper::get_user_id_param($metas[$field_id]);
		                if ( $metas[$field_id] && is_numeric($metas[$field_id]) ) {
		                    $entry['frm_user_id'] = $metas[$field_id];
		                }
		            break;
		            case 'file':
		                $metas[$field_id] = self::get_file_id($metas[$field_id]);
		            break;
		            case 'date':
		                $metas[$field_id] = self::get_date($metas[$field_id]);
		            break;
		            case 'data':
		                $metas[$field_id] = self::get_dfe_id($metas[$field_id], $field, $saved_entries);
		            break;
		            case 'select':
		            case 'checkbox':
		                $metas[$field_id] = self::get_multi_opts($metas[$field_id], $field);
		            break;
    		        
    		    }
    		    unset($field);
    		    
    		    unset($meta);
    		}
    		
    		unset($item);
    		
            $entry['item_meta'] = $metas;
            unset($metas);
            
            // edit entry if the key and created time match
            $editing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}frm_items WHERE item_key=%s AND created_at=%s",
                $entry['item_key'], date('Y-m-d H:i:s', strtotime($entry['created_at']))
            ));
            
            if ( $editing ) {
                $frm_entry->update($entry['id'], $entry);
                $imported['updated']['items']++;
                $saved_entries[$entry['id']] = $entry['id'];
            } else if ( $e = $frm_entry->create($entry) ) {
                $saved_entries[$entry['id']] = $e;
                $imported['imported']['items']++;
            }
		    
		    unset($entry);
	    }
	    
	    unset($entries);
	    
	    return $imported;
    }
	
	public static function import_csv($path, $form_id, $field_ids, $entry_key=0, $start_row=2, $del=',', $max=250){
        global $importing_fields, $wpdb;
        if(!defined('WP_IMPORTING'))
            define('WP_IMPORTING', true);

        $form_id = (int)$form_id;
        if(!$form_id)
            return $start_row;
         
        if(!$importing_fields)
            $importing_fields = array();
        
        if( !ini_get('safe_mode') )
            set_time_limit(0); //Remove time limit to execute this function
        
        if ($f = fopen($path, "r")) {
            unset($path);
            global $frm_entry, $frmdb, $frm_field;
            $row = 0;
            //setlocale(LC_ALL, get_locale());
            
            while (($data = fgetcsv($f, 100000, $del)) !== FALSE) {
                $row++;
                if($start_row > $row) continue;
                
                $values = array('form_id' => $form_id);
                $values['item_meta'] = array();
                foreach($field_ids as $key => $field_id){
                    $data[$key] = (isset($data[$key])) ? $data[$key] : '';
                    
                    if(is_numeric($field_id)){
                        if(isset($importing_fields[$field_id])){
                            $field = $importing_fields[$field_id];
                        }else{
                            $field = $frm_field->getOne($field_id);
                            $importing_fields[$field_id] = $field;
                        }
                        
                        $values['item_meta'][$field_id] = apply_filters('frm_import_val', $data[$key], $field);
                        
                        switch ($field->type ) {
                            case 'user_id':
                                $values['item_meta'][$field_id] = FrmProAppHelper::get_user_id_param(trim($values['item_meta'][$field_id]));
                                $_POST['frm_user_id'] = $values['frm_user_id'] = $values['item_meta'][$field_id];
                            break;
                            case 'checkbox':
                            case 'select':
                                $values['item_meta'][$field_id] = self::get_multi_opts($values['item_meta'][$field_id], $field);
                            break;
                            case 'data':
                                $values['item_meta'][$field_id] = self::get_dfe_id($values['item_meta'][$field_id], $field);
                            break;
                            case 'file':
                                $values['item_meta'][$field_id] = self::get_file_id($values['item_meta'][$field_id]);
                            break;
                            case 'date':
                                $values['item_meta'][$field_id] = self::get_date($values['item_meta'][$field_id]);
                            break;
                        }
                        
                        if(isset($_POST['item_meta'][$field_id]) and ($field->type == 'checkbox' or ($field->type == 'data' and $field->field_options['data_type'] != 'checkbox'))){
                            if(empty($values['item_meta'][$field_id])){
                                $values['item_meta'][$field_id] = $_POST['item_meta'][$field_id];
                            }else if(!empty($_POST['item_meta'][$field_id])){
                                $values['item_meta'][$field_id] = array_merge((array)$_POST['item_meta'][$field_id], (array)$values['item_meta'][$field_id]);
                            }
                        }
                        
                        $_POST['item_meta'][$field_id] = $values['item_meta'][$field_id];
                        
                        FrmProEntryMetaHelper::set_post_fields($field, $values['item_meta'][$field_id]);
                        unset($field);    
                    }else if(is_array($field_id)){
                        $field_type = isset($field_id['type']) ? $field_id['type'] : false;
                        $linked = isset($field_id['linked']) ? $field_id['linked'] : false;
                        $field_id = $field_id['field_id'];

                        if($field_type == 'data'){
                            if($linked){
                                $entry_id = $frmdb->get_var($frmdb->entry_metas, array('meta_value' => $data[$key], 'field_id' => $linked), 'item_id');
                            }else{
                                //get entry id of entry with item_key == $data[$key]
                                $entry_id = $frmdb->get_var($frmdb->entries, array('item_key' => $data[$key]));
                            }

                            if($entry_id)
                                $values['item_meta'][$field_id] = $entry_id;
                        }
                        unset($field_type);
                        unset($linked);
                    }else{
                        $values[$field_id] = $data[$key];
                    }
                    
                }
               
                if(!isset($values['item_key']) or empty($values['item_key']))
                    $values['item_key'] = $data[$entry_key];
                   
                if(isset($values['created_at']) or isset($values['updated_at'])){
                    $offset = get_option('gmt_offset') *60*60;
                    if(isset($values['created_at']))
                        $values['created_at'] = date('Y-m-d H:i:s', (strtotime($values['created_at']) - $offset));
                    
                    if(isset($values['updated_at']))
                        $values['updated_at'] = date('Y-m-d H:i:s', (strtotime($values['updated_at']) - $offset));
                }
               
                if ( isset($values['user_id']) ) {
                    $values['user_id'] = FrmProAppHelper::get_user_id_param($values['user_id']);
                }
               
                if( isset($values['updated_by']) ) {
                    $values['updated_by'] = FrmProAppHelper::get_user_id_param($values['updated_by']);
                }
                
                if( isset($values['is_draft']) ) {
                    $values['is_draft'] = (int) $values['is_draft'];
                }
                
                $editing = false;
                if ( isset($values['id']) && $values['item_key'] ) {
                    //check for updating by entry ID
                    $editing = $wpdb->get_var($wpdb->prepare(
                        "SELECT id FROM {$wpdb->prefix}frm_items WHERE form_id=%d AND id=%d", $values['form_id'], $values['id']
                    ));
                }
                
                if ( $editing ) {
                    $created = $frm_entry->update($values['id'], $values);
                } else {
                    $created = $frm_entry->create($values);
                }
                
                unset($_POST);
                unset($values);
                unset($created);
               
                if ( ($row - $start_row) >= $max ) {
                    fclose($f);
                    return $row;
                }
            }
            fclose($f);
            return $row;
        }
    }

    public static function get_file_id($value) {
        global $wpdb;
        
        if ( !is_array($value ) ) {
            $value = explode(',', $value);
        }
        
        foreach ( (array) $value as $pos => $m) {
            $m = trim($m);
            if (empty($m) ) {
                continue;
            }
            
            if ( !is_numeric($m) ) {
                //get the ID from the URL if on this site
                $m = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE guid='%s';", $m ));
            }
            
            if ( !is_numeric($m) ) {
                unset($value[$pos]);
            } else {
                $value[$pos] = $m;
            }
            
            unset($pos);
            unset($m);
        }
        
        return $value;
    }
    
    public static function get_date($value) {
        if ( !empty($value) ){
            $value = date('Y-m-d', strtotime($value));
        }
        
        return $value;
    }
    
    public static function get_multi_opts($value, $field) {
        
        if ( !$field || empty($value) || in_array($value, (array) $field->options ) ) {
            return $value;
        }
        
        if ( $field->type != 'checkbox' && $field->type != 'select' ) {
            return $value;
        }
        
        if ( $field->type == 'select' && ( !isset($field->field_options['multiple']) || !$field->field_options['multiple'] ) ) {
            return $value;
        }
        
        $checked = is_array($value) ? $value : maybe_unserialize($value);
            
        if ( !is_array($checked) ) {
            $checked = explode(',', $checked);
        }
                
        if ( $checked && count($checked) > 1 ) {
            $value = array_map('trim', $checked);
        }
        
        unset($checked);
        
        return $value;
    }
    
    public static function get_dfe_id($value, $field, $ids = array() ) {
        global $wpdb;
        
        if ( !$field || !isset($field->field_options['data_type']) || $field->field_options['data_type'] == 'data' ) {
            return $value;
        }
        
        if ( !empty($ids) && is_numeric($value) && isset($ids[$value]) ) {
            // the entry was just imported, so we have the id
            return $ids[$value];
        }
        
        if ( !is_array($value) ){
            $new_id = $wpdb->get_var($wpdb->prepare(
                "SELECT item_id FROM {$wpdb->prefix}frm_item_metas WHERE field_id=%d and meta_value=%s", 
                $field->field_options['form_select'], $value
            ));

            if ( $new_id && is_numeric($new_id) ) {
                return $new_id;
            }

            unset($new_id);
        }
        
        if ( !is_array($value) && strpos($value, ',') ) {
            $checked = maybe_unserialize($value);
            
            if ( !is_array($checked) ) {
                $checked = explode(',', $checked);
            }
        } else {
            $checked = $value;
        }
        
        if ( !$checked || !is_array($checked) ) {
            return $value;
        }
        
        $value = array_map('trim', $checked);
                
        foreach ( $value as $dfe_k => $dfe_id ) {
            $new_id = $wpdb->get_var($wpdb->prepare(
                "SELECT item_id FROM {$wpdb->prefix}frm_item_metas WHERE field_id=%d and meta_value=%s", 
                $field->field_options['form_select'], $dfe_id
            ));
            
            if ( $new_id ) {
                $value[$dfe_k] = $new_id;
            }
            unset($new_id);
        }
        
        unset($checked);
        
        return $value;
    }
}
