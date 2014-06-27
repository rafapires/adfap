<?php

class FrmProEntryMetaHelper{
    
    public static function email_value($value, $meta, $entry){
        global $frm_field, $frm_entry;
        
        if($entry->id != $meta->item_id)
            $entry = $frm_entry->getOne($meta->item_id);
        
        $field = $frm_field->getOne($meta->field_id);
        if(!$field)
            return $value;
            
        $field->field_options = maybe_unserialize($field->field_options);
        
        if(isset($field->field_options['post_field']) and $field->field_options['post_field']){
            $value = FrmProEntryMetaHelper::get_post_or_meta_value($entry, $field, array('truncate' => true));
            $value = maybe_unserialize($value);
        }
        
        switch($field->type){
            case 'user_id':
                $value = FrmProFieldsHelper::get_display_name($value);
                break;
            case 'data':
                if (is_array($value)){
                    $new_value = array();
                    foreach($value as $val)
                        $new_value[] = FrmProFieldsHelper::get_data_value($val, $field);
                    $value = $new_value;
                }else{
                    $value = FrmProFieldsHelper::get_data_value($value, $field);
                }
                break;
            case 'file':
                $value = FrmProFieldsHelper::get_file_name($value);
                break;
            case 'date':
                $value = FrmProFieldsHelper::get_date($value);
        }
        
        if (is_array($value)){
            $new_value = '';
            foreach($value as $val){
                if (is_array($val))
                    $new_value .= implode(', ', $val) . "\n";
            }
            if ($new_value != '')
                $value = $new_value;
        }
        
        return $value;
    }
    
    public static function display_value($value, $field, $atts=array()){
        global $wpdb, $frm_field;
        
        $defaults = array(
            'type' => '', 'show_icon' => true, 'show_filename' => true, 
            'truncate' => false, 'sep' => ', ', 'post_id' => 0, 'form_id' => $field->form_id,
            'field' => $field, 'keepjs' => 0
        );
        
        $atts = wp_parse_args( $atts, $defaults );
        $field->field_options = maybe_unserialize($field->field_options);
        
        if(!isset($field->field_options['post_field']))
            $field->field_options['post_field'] = '';
        
        if(!isset($field->field_options['custom_field']))
            $field->field_options['custom_field'] = '';
               
        if($atts['post_id'] and ($field->field_options['post_field'] or $atts['type'] == 'tag')){
            $atts['pre_truncate'] = $atts['truncate'];
            $atts['truncate'] = true;
            $atts['exclude_cat'] = isset($field->field_options['exclude_cat']) ? $field->field_options['exclude_cat'] : 0;
                
            $value = FrmProEntryMetaHelper::get_post_value($atts['post_id'], $field->field_options['post_field'], $field->field_options['custom_field'], $atts);
            $atts['truncate'] = $atts['pre_truncate'];
        }
            
        if ($value == '') return $value;
        
        $value = maybe_unserialize($value);
        $value = apply_filters('frm_display_value_custom', $value, $field, $atts);
        
        $new_value = '';
        
        if (is_array($value)){
            foreach($value as $val){
                if (is_array($val)){ //TODO: add options for display (li or ,)
                    $new_value .= implode($atts['sep'], $val);
                    if($atts['type'] != 'data')
                        $new_value .= "<br/>";
                }
                unset($val);
            }
        }

        if (!empty($new_value))
            $value = $new_value;
        else if (is_array($value))
            $value = implode($atts['sep'], $value);

        if ($atts['truncate'] and $atts['type'] != 'image')
            $value = FrmAppHelper::truncate($value, 50);

        if ($atts['type'] == 'image'){
            $value = '<img src="'.$value.'" height="50px" alt="" />';
        }else if ($atts['type'] == 'user_id'){
            $value = FrmProFieldsHelper::get_display_name($value);
        }else if ($atts['type'] == 'file'){
            $old_value = explode(', ', $value);
            $value = '';
            foreach($old_value as $mid){
                $value .= '<div class="frm_file_container">';
                if ($atts['show_icon']){
                    $img = FrmProFieldsHelper::get_file_icon($mid);
                    $value .= $img;
                    if($atts['show_filename'] and $img and preg_match("/wp-includes\/images\/crystal/", $img)){
                        //prevent two filenames
                        $atts['show_filename'] = $show_filename = false;
                    }
                    
                    unset($img);
                }

                if ($atts['show_icon'] and $atts['show_filename'])
                    $value .= '<br/>';

                if ($atts['show_filename'])
                    $value .= FrmProFieldsHelper::get_file_name($mid);
                    
                if(isset($show_filename)){                    
                    //if skipped filename, show it for the next file
                    $atts['show_filename'] = true;
                    unset($show_filename);
                }
                    
                
                $value .= '</div>';
            }
        }else if ($atts['type'] == 'date'){
            $value = FrmProFieldsHelper::get_date($value);
        }else if ($atts['type'] == 'data'){
            if(!is_numeric($value)){
                $value = explode($atts['sep'], $value);
                if(is_array($value)){
                    $new_value = '';
                    foreach($value as $entry_id){
                        if(!empty($new_value))
                            $new_value .= $atts['sep'];
                            
                        if(is_numeric($entry_id)){
                            $dval = FrmProFieldsHelper::get_data_value($entry_id, $field, $atts);
                            if(is_array($dval))
                                $dval = implode($atts['sep'], $dval);
                            $new_value .= $dval;
                        }else{
                            $new_value .= $entry_id;
                        }
                    }
                    $value = $new_value;
                }
            }else{
                //replace item id with specified field
                $new_value = FrmProFieldsHelper::get_data_value($value, $field, $atts);
                
                if($field->field_options['data_type'] == 'data' or $field->field_options['data_type'] == ''){
                    $linked_field = $frm_field->getOne($field->field_options['form_select']);
                    if($linked_field->type == 'file'){
                        $old_value = explode(', ', $new_value);
                        $new_value = '';
                        foreach($old_value as $v){
                            $new_value .= '<img src="'. $v .'" height="50px" alt="" />';
                            if ($atts['show_filename'])
                                $new_value .= '<br/>'. $v;
                            unset($v);
                        }
                    }else{
                        $new_value = $value;
                    }
                }
                
                $value = $new_value;
            }
        }
        
        if ( !$atts['keepjs'] ) {
            $value = wp_kses_post($value);
        }

        return apply_filters('frm_display_value', $value, $field, $atts);
    }
    
    public static function get_post_or_meta_value($entry, $field, $atts=array()){
        global $frm_entry_meta;
        
        if(!is_object($entry)){
            global $frm_entry;
            $entry = $frm_entry->getOne($entry);
        }
        if(empty($entry) or empty($field))
            return '';
        
        if($entry->post_id){
            if(!isset($field->field_options['custom_field']))
                $field->field_options['custom_field'] = '';
            
            if(!isset($field->field_options['post_field']))
                $field->field_options['post_field'] = '';
              
            $links = true;
            if(isset($atts['links']))
                $links = $atts['links'];
                
            if($field->type == 'tag' or $field->field_options['post_field']){
                $post_args = array('type' => $field->type, 'form_id' => $field->form_id, 'field' => $field, 'links' => $links, 'exclude_cat' => $field->field_options['exclude_cat']);
                foreach(array('show', 'truncate', 'sep') as $p){
                    if(isset($atts[$p]))
                        $post_args[$p] = $atts[$p];
                }

                $value = FrmProEntryMetaHelper::get_post_value($entry->post_id, $field->field_options['post_field'], $field->field_options['custom_field'], $post_args);               
                unset($post_args);
            }else{
                $value = $frm_entry_meta->get_entry_meta_by_field($entry->id, $field->id);
            }
        }else{
            $value = $frm_entry_meta->get_entry_meta_by_field($entry->id, $field->id);
            
            if(($field->type == 'tag' or (isset($field->field_options['post_field']) and $field->field_options['post_field'] == 'post_category')) and !empty($value)){
                $value = maybe_unserialize($value);

                $new_value = array();
                foreach((array)$value as $tax_id){
                    if(is_numeric($tax_id)){
                        $cat = $term = get_term( $tax_id, $field->field_options['taxonomy'] );
                        $new_value[] = ($cat) ? $cat->name : $tax_id;
                        unset($cat);
                    }else{
                        $new_value[] = $tax_id;
                    }
                }

                $value = $new_value;
            }
        }

        return $value;
    }
    
    public static function get_post_value($post_id, $post_field, $custom_field, $atts){
        if(!$post_id) return '';
        $post = get_post($post_id);
        if(!$post) return '';
        
        $defaults = array(
            'sep' => ', ', 'truncate' => true, 'form_id' => false, 
            'field' => array(), 'links' => false, 'show' => ''
        );
        
		$atts = wp_parse_args( $atts, $defaults );

        $value = ''; 
        if ($atts['type'] == 'tag'){
            if(isset($atts['field']->field_options)){
                $field_options = maybe_unserialize($atts['field']->field_options);
                $tax = isset($field_options['taxonomy']) ? $field_options['taxonomy'] : 'frm_tag';

            
                if($tags = get_the_terms($post_id, $tax)){
                    $names = array();
                    foreach($tags as $tag){
                        $tag_name = $tag->name;
                        if($atts['links']){
                            $tag_name = '<a href="' . esc_attr( get_term_link($tag, $tax) ) . '" title="' . esc_attr( sprintf(__( 'View all posts filed under %s', 'formidable' ), $tag_name) ) . '">'. $tag_name . '</a>';
                        }
                        $names[] = $tag_name;
                    }
                    $value = implode($atts['sep'], $names);
                }
            }
        }else{
            if($post_field == 'post_custom'){ //get custom post field value
                $value = get_post_meta($post_id, $custom_field, true);
            }else if($post_field == 'post_category'){
                if($atts['form_id']){
                    $post_type = FrmProFormsHelper::post_type($atts['form_id']);
                    $taxonomy = FrmProAppHelper::get_custom_taxonomy($post_type, $atts['field']);
                }else{
                    $taxonomy = 'category';
                }
                
                $categories = get_the_terms( $post_id, $taxonomy );

                $names = array();
                $cat_ids = array();
                if($categories){
                    foreach($categories as $cat){
                        if(isset($atts['exclude_cat']) and in_array($cat->term_id, (array)$atts['exclude_cat']))
                            continue;
                            
                        $cat_name = $cat->name;
                        if($atts['links']){
                            $cat_name = '<a href="' . esc_attr( get_term_link($cat, $taxonomy) ) . '" title="' . esc_attr( sprintf(__( 'View all posts filed under %s', 'formidable' ), $cat_name) ) . '">'. $cat_name . '</a>';
                        }
                        
                        $names[] = $cat_name;
                        $cat_ids[] = $cat->term_id;
                    }
                }
            
                if($atts['show'] == 'id')
                    $value = implode($atts['sep'], $cat_ids);
                else if($atts['truncate'])
                    $value = implode($atts['sep'], $names);
                else
                    $value = $cat_ids;
            }else{
                $post = (array)$post;
                $value = $post[$post_field];
            }
        }
        return $value;
    }
    
    public static function set_post_fields($field, $value, $errors = null) {
        $field->field_options = maybe_unserialize($field->field_options);
        
        if ( !isset($field->field_options['post_field']) || $field->field_options['post_field'] == '' ) {
            if ( isset($errors) ) {
                return $errors;
            }
            
            return;
        }
        
        
        if ( $field->type == 'file' ) {
            global $frm_vars;
            if ( !isset($frm_vars['media_id']) ) {
                $frm_vars['media_id'] = array();
            }
            
            $frm_vars['media_id'][$field->id] = $value;
        }
        
        global $frmpro_settings;
            
        if ( $value && !empty($value) && isset($field->field_options['unique']) && $field->field_options['unique'] ) {
            global $frmdb;
            
            $entry_id = (isset($_POST) && isset($_POST['id'])) ? $_POST['id'] : false;
            $post_id = $entry_id  ? $frmdb->get_var($frmdb->entries, array('id' => $entry_id), 'post_id') : false;
            
            if ( isset($errors) && FrmProEntryMetaHelper::post_value_exists($field->field_options['post_field'], $value, $post_id, $field->field_options['custom_field']) ) {
                $errors['field'. $field->id] = FrmProFieldsHelper::get_error_msg($field, 'unique_msg');
            }
                
            unset($entry_id);
            unset($post_id);
        }
        
        if ( $field->field_options['post_field'] == 'post_custom' ) {
            if ( $field->type == 'date' and !preg_match('/^\d{4}-\d{2}-\d{2}/', trim($value)) ) {
                $value = FrmProAppHelper::convert_date($value, $frmpro_settings->date_format, 'Y-m-d');
            }
                
            $_POST['frm_wp_post_custom'][$field->id.'='.$field->field_options['custom_field']] = $value;
            
            if ( isset($errors) ) {
                return $errors;
            }
            return;
        }
        
        if ( $field->field_options['post_field'] == 'post_date' ) {
            if ( !preg_match('/^\d{4}-\d{2}-\d{2}/', trim($value)) ) {
                $value = FrmProAppHelper::convert_date($value, $frmpro_settings->date_format, 'Y-m-d H:i:s');
            }
        } else if ( $field->type != 'tag' && $field->field_options['post_field'] == 'post_category' ) {
            $value = (array) $value;
            if ( isset($field->field_options['taxonomy']) && $field->field_options['taxonomy'] != 'category' ) {
                $new_value = array();
                foreach ( $value as $val ) {
                    if ( $val == 0 ) {
                        continue;
                    }
                    
                    $term = get_term($val, $field->field_options['taxonomy']);

                    if ( !isset($term->errors) ) {
                        $new_value[$val] = $term->name;
                    } else {
                        $new_value[$val] = $val;
                    } 
                }
                
                if ( !isset($_POST['frm_tax_input']) ) {
                    $_POST['frm_tax_input'] = array();
                }

                if ( isset($_POST['frm_tax_input'][$field->field_options['taxonomy']]) ) {
                    foreach ( $new_value as $new_key => $new_name ) {
                        $_POST['frm_tax_input'][$field->field_options['taxonomy']][$new_key] = $new_name;
                    }
                } else {
                    $_POST['frm_tax_input'][$field->field_options['taxonomy']] = $new_value;
                }
            } else {
                $_POST['frm_wp_post'][$field->id .'='. $field->field_options['post_field']] = $value;
            }
            
        } else if ( $field->type == 'tag' && $field->field_options['post_field'] == 'post_category' ) {
            $value = trim($value);
            $value = array_map('trim', explode(',', $value));
            
            $tax_type = (isset($field->field_options['taxonomy']) && !empty($field->field_options['taxonomy'])) ? $field->field_options['taxonomy'] : 'frm_tag';

            if ( !isset($_POST['frm_tax_input']) ) {
                $_POST['frm_tax_input'] = array();
            }
            
            if ( is_taxonomy_hierarchical($tax_type) ) {
                //create the term or check to see if it exists
                $terms = array();
                foreach ( $value as $v ) {
                    $term_id = term_exists($v, $tax_type);

                    if ( !$term_id ) {
                        $term_id = wp_insert_term($v, $tax_type);
                    }
                    
                    if ( $term_id && is_array($term_id) )  {
                        $term_id = $term_id['term_id'];
                    }
                    
                    if ( is_numeric($term_id) ) {
                        $terms[$term_id] = $v;
                    }

                    unset($term_id);
                    unset($v);
                }
                
                $value = $terms;
                unset($terms);
            }
            
            if ( !isset($_POST['frm_tax_input'][$tax_type]) ) {
                $_POST['frm_tax_input'][$tax_type] = (array) $value;
            } else {
                $_POST['frm_tax_input'][$tax_type] += (array)$value;
            }
        }

    	if ( $field->field_options['post_field'] != 'post_category' ) {
            $_POST['frm_wp_post'][$field->id.'='.$field->field_options['post_field']] = $value;
        }
        
        if ( isset($errors) ) {
            return $errors;
        }
    }
    
    public static function meta_through_join($hide_field, $selected_field, $observed_field_val) {
        if ( !is_numeric($observed_field_val) && !is_array($observed_field_val) ) {
            return array();
        }
        
        global $frm_field, $frm_entry_meta;
        
        $observed_info = $frm_field->getOne($hide_field);
        
        if ( $selected_field ) {
            $join_fields = $frm_field->getAll(array('fi.form_id' => $selected_field->form_id, 'type' => 'data'));
        }
        
        if ( isset($join_fields) && $join_fields ) {
            foreach ( $join_fields as $jf ) {
                if ( isset($jf->field_options['form_select']) && isset($observed_info->field_options['form_select']) && $jf->field_options['form_select'] == $observed_info->field_options['form_select'] ) {
                    $join_field = $jf->id;
                }
            }
            
            if ( isset($join_field) ) {
                $observed_field_val = array_filter( (array) $observed_field_val);
                $query = "(it.meta_value in (". implode(',', $observed_field_val) .")";
                foreach ( $observed_field_val as $obs_val ) {
                    $query .= " or it.meta_value LIKE '%s:". strlen($obs_val). ":\"". $obs_val ."\"%'";
                }
                
                $query .= ") and field_id =". (int)$join_field;
                $entry_ids = $frm_entry_meta->getEntryIds($query);
            }
        }
        
        if ( isset($entry_ids) && !empty($entry_ids) ) {
            $metas = $frm_entry_meta->getAll("item_id in (".implode(',', $entry_ids).") and field_id=". $selected_field->id, ' ORDER BY meta_value');
        } else {
            $metas = array();
        }
            
        return $metas;
    }
    
    public static function value_exists($field_id, $value, $entry_id = false) {
        global $wpdb;
        if ( is_object($field_id) ) {
            $field_id = $field->id;
        }
        
        $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}frm_item_metas WHERE meta_value=%s AND field_id=%d", $value, $field_id);
        if ( $entry_id ) {
            $query .= $wpdb->prepare(" AND item_id != %d", $entry_id);
        }
        
        return $wpdb->get_var($query);
    }
    
    public static function post_value_exists($post_field, $value, $post_id, $custom_field = '') {
        global $wpdb;
        if ( $post_field == 'post_custom' ) {
            $query = $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta pm LEFT JOIN $wpdb->posts p ON (p.ID=pm.post_id) WHERE meta_value=%s and meta_key=%s", $value, $custom_field);
            if($post_id and is_numeric($post_id))
                $query .= $wpdb->prepare(" and post_id != %d", $post_id);
        } else {
            $query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE $post_field=%s", $value);
            if ( $post_id && is_numeric($post_id) ) {
                $query .= $wpdb->prepare(" and ID != %d", $post_id);
            }
        }
        $query .= " and post_status in ('publish','draft','pending','future')";

        return $wpdb->get_var($query);
    }
    
    public static function &get_max($field) {
        global $wpdb, $frmdb;
        
        if ( !is_object($field) ) {
            global $frm_field;
            $field = $frm_field->getOne($field);
        }
        
        if ( !$field ) {
            return;
        }
            
        $max = $wpdb->get_var($wpdb->prepare("SELECT meta_value +0 as odr FROM {$wpdb->prefix}frm_item_metas WHERE field_id=%d ORDER BY odr DESC LIMIT 1", $field->id));
        
        if ( isset($field->field_options['post_field']) && $field->field_options['post_field'] == 'post_custom' ) {
            $post_max = $wpdb->get_var($wpdb->prepare("SELECT meta_value +0 as odr FROM $wpdb->postmeta WHERE meta_key= %s ORDER BY odr DESC LIMIT 1", $field->field_options['custom_field']));
            if ( $post_max && (float) $post_max > (float) $max ) {
                $max = $post_max;
            }
        }
        
        return $max;
    }

}
