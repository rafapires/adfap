<?php

class FrmProAppHelper{
    
    public static function jquery_themes(){
        $themes = array(
            'ui-lightness'  => 'UI Lightness',
            'ui-darkness'   => 'UI Darkness',
            'smoothness'    => 'Smoothness',
            'start'         => 'Start',
            'redmond'       => 'Redmond',
            'sunny'         => 'Sunny',
            'overcast'      => 'Overcast',
            'le-frog'       => 'Le Frog',
            'flick'         => 'Flick',
            'pepper-grinder'=> 'Pepper Grinder',
            'eggplant'      => 'Eggplant',
            'dark-hive'     => 'Dark Hive',
            'cupertino'     => 'Cupertino',
            'south-street'  => 'South Street',
            'blitzer'       => 'Blitzer',
            'humanity'      => 'Humanity',
            'hot-sneaks'    => 'Hot Sneaks',
            'excite-bike'   => 'Excite Bike',
            'vader'         => 'Vader',
            'dot-luv'       => 'Dot Luv',
            'mint-choc'     => 'Mint Choc',
            'black-tie'     => 'Black Tie',
            'trontastic'    => 'Trontastic',
            'swanky-purse'  => 'Swanky Purse'
        );
        
        $themes = apply_filters('frm_jquery_themes', $themes);
        return $themes;
    }
    
    public static function jquery_css_url($theme_css){
        if($theme_css == -1)
            return;

        $uploads = wp_upload_dir();
        if(!$theme_css or $theme_css == '' or $theme_css == 'ui-lightness'){
            $css_file = FrmAppHelper::plugin_url() . '/css/ui-lightness/jquery-ui.css';
        }else if(preg_match('/^http.?:\/\/.*\..*$/', $theme_css)){
            $css_file = $theme_css;
        }else{
            $file_path = '/formidable/css/'. $theme_css . '/jquery-ui.css';
            if(file_exists($uploads['basedir'] . $file_path)){
                if(is_ssl() and !preg_match('/^https:\/\/.*\..*$/', $uploads['baseurl']))
                    $uploads['baseurl'] = str_replace('http://', 'https://', $uploads['baseurl']);
                $css_file = $uploads['baseurl'] . $file_path;
            }else{
                $css_file = 'http'. (is_ssl() ? 's' : '') .'://ajax.googleapis.com/ajax/libs/jqueryui/1.7.3/themes/'. $theme_css . '/jquery-ui.css';
            }
        }
        
        return $css_file;
    }
    
    public static function datepicker_version(){
        $jq = FrmAppHelper::script_version('jquery');
	    
	    $new_ver = true;
	    if($jq){
	        $new_ver = ((float)$jq >= 1.5) ? true : false;
        }else{
            global $wp_version;
            $new_ver = ($wp_version >= 3.2) ? true : false;
        }
        
        return ($new_ver) ? '' : '.1.7.3';
    }
    
    public static function get_user_id_param($user_id){
        if ( !$user_id || empty($user_id) || is_numeric($user_id) ) {
            return $user_id;
        }
        
        if($user_id == 'current'){
            $user_ID = get_current_user_id();
            $user_id = $user_ID;
        }else{
            if ( is_email($user_id) ) {
                $user = get_user_by('email', $user_id);
            } else {
                $user = get_user_by('login', $user_id);
            }
            
            if ( $user ) {
                $user_id = $user->ID;
            }
            unset($user);
        }
        
        return $user_id;
    }
    
    public static function get_formatted_time($date, $date_format=false, $time_format=false){
        if(empty($date))
            return $date;
        
        if(!$date_format)
            $date_format = get_option('date_format');

        if (preg_match('/^\d{1-2}\/\d{1-2}\/\d{4}$/', $date)){ 
            global $frmpro_settings;
            $date = FrmProAppHelper::convert_date($date, $frmpro_settings->date_format, 'Y-m-d');
        }
        
        $do_time = (date('H:i:s', strtotime($date)) == '00:00:00') ? false : true;   
        
        $date = get_date_from_gmt($date);

        $formatted = date_i18n($date_format, strtotime($date));
        
        if($do_time){
            
            if(!$time_format)
                $time_format = get_option('time_format');
            
            $trimmed_format = trim($time_format);
            if($time_format and !empty($trimmed_format))
                $formatted .= ' '. __('at', 'formidable') .' '. date_i18n($time_format, strtotime($date));
        }
        
        return $formatted;
    }
    
    public static function human_time_diff( $from, $to = '' ) {
    	if ( empty($to) )
    		$to = time();

    	// Array of time period chunks
    	$chunks = array(
    		array( 60 * 60 * 24 * 365 , __( 'year', 'formidable' ), __( 'years', 'formidable' ) ),
    		array( 60 * 60 * 24 * 30 , __( 'month', 'formidable' ), __( 'months', 'formidable' ) ),
    		array( 60 * 60 * 24 * 7, __( 'week', 'formidable' ), __( 'weeks', 'formidable' ) ),
    		array( 60 * 60 * 24 , __( 'day', 'formidable' ), __( 'days', 'formidable' ) ),
    		array( 60 * 60 , __( 'hour', 'formidable' ), __( 'hours', 'formidable' ) ),
    		array( 60 , __( 'minute', 'formidable' ), __( 'minutes', 'formidable' ) ),
    		array( 1, __( 'second', 'formidable' ), __( 'seconds', 'formidable' ) )
    	);

    	// Difference in seconds
    	$diff = (int) ($to - $from);

    	// Something went wrong with date calculation and we ended up with a negative date.
    	if ( $diff < 1)
    		return '0 ' . __( 'seconds', 'formidable' );

    	/**
    	 * We only want to output one chunks of time here, eg:
    	 * x years
    	 * xx months
    	 * so there's only one bit of calculation below:
    	 */

    	//Step one: the first chunk
    	for ( $i = 0, $j = count($chunks); $i < $j; $i++) {
    		$seconds = $chunks[$i][0];

    		// Finding the biggest chunk (if the chunk fits, break)
    		if ( ( $count = floor($diff / $seconds) ) != 0 )
    			break;
    	}

    	// Set output var
    	$output = ( 1 == $count ) ? '1 '. $chunks[$i][1] : $count . ' ' . $chunks[$i][2];


    	if ( !(int)trim($output) )
    		$output = '0 ' . __( 'seconds', 'formidable' );

    	return $output;
    }
    
    public static function convert_date($date_str, $from_format, $to_format){
        $base_struc     = preg_split("/[\/|.| |-]/", $from_format);
        $date_str_parts = preg_split("/[\/|.| |-]/", $date_str );

        $date_elements = array();

        $p_keys = array_keys( $base_struc );
        foreach ( $p_keys as $p_key ){
            if ( !empty( $date_str_parts[$p_key] ))
                $date_elements[$base_struc[$p_key]] = $date_str_parts[$p_key];
            else
                return false;
        }

        if(is_numeric($date_elements['m']))
            $dummy_ts = mktime(0, 0, 0, $date_elements['m'], (isset($date_elements['j']) ? $date_elements['j'] : $date_elements['d']), (isset($date_elements['Y']) ? $date_elements['Y'] : $date_elements['y']) );
        else
            $dummy_ts = strtotime($date_str);

        return date( $to_format, $dummy_ts );
    }
    
    public static function get_edit_link($id){
        global $current_user;

    	$output = '';
    	if($current_user && $current_user->wp_capabilities['administrator'] == 1) 
    		$output = "<a href='". admin_url() ."?page=formidable-entries&frm_action=edit&id={$id}'>". __('Edit') ."</a>";
    	
    	return $output;
    }
    
    public static function rewriting_on(){
      $permalink_structure = get_option('permalink_structure');

      return ($permalink_structure and !empty($permalink_structure));
    }
    
    public static function current_url() {
        $pageURL = 'http';
        if (is_ssl()) $pageURL .= "s";
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80")
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        else
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            
        return $pageURL;
    }
    
    public static function get_permalink_pre_slug_uri(){
      preg_match('#^([^%]*?)%#', get_option('permalink_structure'), $struct);
      return $struct[1];
    }
    
    //Bulk Actions
    public static function header_checkbox(){ ?>
<input type="checkbox" name="check-all" class="select-all-item-action-checkboxes" value="" /> &nbsp;
<?php    
    }
    

    public static function item_checkbox($id){ ?>
<input type="checkbox" name="item-action[]" class="item-action-checkbox" value="<?php echo $id; ?>" /> &nbsp;
<?php    
    }
    
    public static function bulk_actions($footer){ 
        $name = (!$footer) ? '' : '2'; ?>
        <div class="alignleft actions">
        <select name="bulkaction<?php echo $name ?>" id="bulkaction<?php echo $name ?>">
            <option value="-1"><?php _e('Bulk Actions', 'formidable') ?></option>
            <option value="delete"><?php _e('Delete') ?></option>
            <?php if(isset($_GET) and isset($_GET['page']) and $_GET['page'] == 'formidable-entries'){ ?>
            <option value="csv"><?php _e('Export to CSV', 'formidable') ?></option>
            <?php } ?>
        </select>
        <input type="submit" value="<?php _e('Apply', 'formidable') ?>" id="doaction" class="button-secondary action"/>
        </div>
    <?php    
    }
    
    public static function get_shortcodes($content, $form_id){
        global $frm_field;
        $fields = $frm_field->getAll("fi.type not in ('divider','captcha','break','html') and fi.form_id=".$form_id);
        
        $tagregexp = 'editlink|siteurl|sitename|id|key|post[-|_]id|ip|created[-|_]at|updated[-|_]at|updated[-|_]by';
        foreach ($fields as $field)
            $tagregexp .= '|'. $field->id . '|'. $field->field_key;

        preg_match_all("/\[(if )?($tagregexp)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?/s", $content, $matches, PREG_PATTERN_ORDER);

        return $matches;
    }
    
    public static function get_custom_post_types(){
        $custom_posts = get_post_types(array(), 'object');
        foreach (array('revision', 'attachment', 'nav_menu_item') as $unset) {
            unset($custom_posts[$unset]);
        }
        return $custom_posts;
    }
    
    public static function get_custom_taxonomy($post_type, $field){
        $taxonomies = get_object_taxonomies($post_type);
        if(!$taxonomies){
            return false;
        }else{
            $field = (array)$field;
            if(!isset($field['taxonomy'])){
                $field['field_options'] = maybe_unserialize($field['field_options']);
                $field['taxonomy'] = $field['field_options']['taxonomy'];
            }
            
            if(isset($field['taxonomy']) and in_array($field['taxonomy'], $taxonomies))
                return $field['taxonomy'];
            else if($post_type == 'post')
                return 'category';
            else
                return reset($taxonomies);
        }
    }
    
    public static function sort_by_array($array, $order_array){
        $array = (array)$array;
        $order_array = (array)$order_array;
        $ordered = array();
        foreach($order_array as $key){
            if(array_key_exists($key, $array)){
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        return $ordered + $array;
    }
    
    
    public static function reset_keys($arr){
        $new_arr = array();
        if(empty($arr))
            return $new_arr;
            
        foreach($arr as $val){
            $new_arr[] = $val;
            unset($val);
        }
        return $new_arr;
    }
    
    public static function filter_where($entry_ids, $args){
        global $wpdb, $frmdb, $frm_entry_meta, $frm_field;
        
        $defaults = array(
            'where_opt' => false, 'where_is' => '=', 'where_val' => '', 
            'form_id' => false, 'form_posts' => array(), 'after_where' => false,
            'display' => false, 'drafts' => 0
        );
        
        extract(wp_parse_args($args, $defaults));
        
        $form_id = (int)$form_id;
        if(!$form_id or !$where_opt or !is_numeric($where_opt))
            return $entry_ids;
           
                   
        $where_field = $frm_field->getOne($where_opt);
        if(!$where_field)
            return $entry_ids;
                
        if($where_val == 'NOW')
            $where_val = date_i18n('Y-m-d', strtotime(current_time('mysql')));
    
        if($where_field->type == 'date' and !empty($where_val))
            $where_val = date('Y-m-d', strtotime($where_val));
        else if($where_is == '=' and $where_val != '' and ($where_field->type == 'checkbox' or ($where_field->type == 'select' and isset($where_field->field_options['multiple']) and $where_field->field_options['multiple']) or ($where_field->type == 'data' and $where_field->field_options['data_type'] == 'checkbox' and is_numeric($where_val))))
            $where_is =  'LIKE';
  
        $field_options = maybe_unserialize($where_field->field_options);

        if($where_field->form_id != $form_id){
            //TODO: get linked entry IDs and get entries where data field value(s) in linked entry IDs
        }
        
        $temp_where_is = str_replace(array('!', 'not '), '', $where_is);

        //get values that aren't blank and then remove them from entry list
        if($where_val == '' and $temp_where_is == '=')
            $temp_where_is = '!=';

        
		$orig_where_val = $where_val;
		if($where_is == 'LIKE' or $where_is == 'not LIKE'){
             //add extra slashes to match values that are escaped in the database
            $where_val_esc = "'%". str_replace('\\', '\\\\\\\\\\', esc_sql(like_escape($where_val))) ."%'";
            $where_val = "'%". esc_sql(like_escape($where_val)) ."%'";
        }else if(!strpos($where_is, 'in')){
            $where_val_esc = "'". str_replace('\\', '\\\\\\', esc_sql($where_val)) ."'";
            $where_val = "'". esc_sql($where_val) ."'";
        }

        //Filter by DFE text 
		if ( $where_field->type == 'data' && !is_numeric($where_val) && $orig_where_val != '' && (!isset($field_options['post_field']) || $field_options['post_field'] != 'post_category')){			
			//Get entry IDs by DFE text
			if ($where_is == 'LIKE' or $where_is == 'not LIKE'){
				$linked_id = $frm_entry_meta->search_entry_metas($orig_where_val, $where_field->field_options['form_select'], $temp_where_is);
			}else{
				$linked_id = $wpdb->get_col($wpdb->prepare("SELECT item_id FROM $frmdb->entry_metas WHERE field_id=%d AND meta_value $temp_where_is %s", $where_field->field_options['form_select'], $orig_where_val));
				}

			//If text doesn't return any entry IDs, get entry IDs from entry key
			if(!$linked_id){
				$linked_field = $frm_field->getOne($where_field->field_options['form_select']);
				$linked_id = $wpdb->get_col("SELECT id FROM $frmdb->entries WHERE form_id={$linked_field->form_id} AND item_key $temp_where_is $where_val");
			}

			//Change $where_val to linked entry IDs
            if($linked_id){
				$linked_id = (array)$linked_id;
                if($where_field->field_options['data_type'] == 'checkbox'){
					$where_val = "'%". implode("%' OR meta_value LIKE '%", $linked_id) ."%'";
					if ($where_is == '!=' or $where_is == 'not LIKE')
						$temp_where_is = 'LIKE';
					else if ($where_is == '=' or $where_is == 'LIKE')
						$where_is = $temp_where_is = 'LIKE';
				}else{
                    $where_is = $temp_where_is = (strpos($where_is, '!') === false and strpos($where_is, 'not') === false) ? ' in ' : ' not in ';
                    $where_val = '('. implode(',', $linked_id) .')';	
                }
				unset($where_val_esc);
            }
            unset($linked_id);
        }
    
        $where_statement = "(meta_value ". ( in_array($where_field->type, array('number', 'scale')) ? ' +0 ' : '') . $temp_where_is ." ". $where_val ." ";
        if(isset($where_val_esc) and $where_val_esc != $where_val)
            $where_statement .= " OR meta_value ". ( in_array($where_field->type, array('number', 'scale')) ? ' +0 ' : '') . $temp_where_is ." ". $where_val_esc;
        
        $where_statement .= ") and fi.id=". (int)$where_opt;
        $where_statement = apply_filters('frm_where_filter', $where_statement, $args);
		
        $new_ids = $frm_entry_meta->getEntryIds($where_statement, '', '', true, $drafts);
        
        if ($where_is != $temp_where_is)
            $new_ids = array_diff($entry_ids, $new_ids);
            
        unset($temp_where_is);
            
        if(!empty($form_posts)){ //if there are posts linked to entries for this form  
            if(isset($field_options['post_field']) and in_array($field_options['post_field'], array('post_category', 'post_custom', 'post_status', 'post_content', 'post_excerpt', 'post_title', 'post_name', 'post_date'))){
                $post_ids = array();
                foreach($form_posts as $form_post){
                    $post_ids[$form_post->post_id] = $form_post->id;
                    if(!in_array($form_post->id, $new_ids))
                        $new_ids[] = $form_post->id;
                }

                if(!empty($post_ids)){ 
                    if($field_options['post_field'] == 'post_category'){
                        $add_posts = $remove_posts = false;
                        //check categories

                        $temp_where_is = str_replace(array('!', 'not '), '', $where_is);
                                
                        $join_with = ' OR ';
                        $t_where = "t.term_id {$temp_where_is} {$where_val}";
                        $t_where .= " {$join_with} t.slug {$temp_where_is} {$where_val}";
                        $t_where .= " {$join_with} t.name {$temp_where_is} {$where_val}";
                        unset($temp_where_is);
                            
                        $query = "SELECT tr.object_id FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy = '{$field_options['taxonomy']}' AND ({$t_where}) AND tr.object_id IN (". implode(',', array_keys($post_ids)) .")";
                        $add_posts = $wpdb->get_col($query);

                        if ($where_is == '!=' or $where_is == 'not LIKE'){
                            $remove_posts = $add_posts;
                            $add_posts = false;
                        }else if(!$add_posts){
                            return array();
                        }
                    }else if($field_options['post_field'] == 'post_custom' and $field_options['custom_field'] != ''){
                        //check custom fields
                        $add_posts = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE post_id in (". implode(',', array_keys($post_ids)) .") AND meta_key='".$field_options['custom_field']."' AND meta_value ". ( in_array($where_field->type, array('number', 'scale')) ? ' +0 ' : ''). $where_is." ".$where_val);
                    }else{ //if field is post field
                        $add_posts = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE ID in (". implode(',', array_keys($post_ids)) .") AND ".$field_options['post_field'] .( in_array($where_field->type, array('number', 'scale')) ? ' +0 ' : ' '). $where_is." ".$where_val);
                    }
                        
                    if($add_posts and !empty($add_posts)){
                        $new_ids = array();
                        foreach($add_posts as $add_post){
                            if(!in_array($post_ids[$add_post], $new_ids))
                                $new_ids[] = $post_ids[$add_post];
                        }
                    }

                    if(isset($remove_posts)){
                        if(!empty($remove_posts)){
                            foreach($remove_posts as $remove_post){
                                $key = array_search($post_ids[$remove_post], $new_ids);
                                if($key and $new_ids[$key] == $post_ids[$remove_post])
                                    unset($new_ids[$key]);

                                unset($key);
                            }
                        }
                        unset($remove_posts);
                    }else if(!$add_posts){
                        $new_ids = array();
                    }
                }
            }
        }   

        if($after_where)
            $entry_ids = array_intersect($new_ids, $entry_ids); //only use entries that are found with all wheres
        else
            $entry_ids = $new_ids;
        
        return $entry_ids;
    }
    
    public static function get_current_form_id(){
        global $frm_vars;
        
        $form_id = 0;
        if(isset($frm_vars['current_form']) and $frm_vars['current_form'])
            $form_id = $frm_vars['current_form']->id;
        
        if(!$form_id)
            $form_id = FrmAppHelper::get_param('form', false);
            
        if(!$form_id){
            $frm_form = new FrmForm();
            $frm_vars['current_form'] = $frm_form->getAll("is_template=0 AND (status is NULL OR status = '' OR status = 'published')", ' ORDER BY name', ' LIMIT 1');
            $form_id = (isset($frm_vars['current_form']) and $frm_vars['current_form']) ? $frm_vars['current_form']->id : 0;
        }
        return $form_id;
    }
    
    //Let WordPress process the uploads
    public static function upload_file($field_id){
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        $media_ids = $errors = array();
        add_filter('upload_dir', array('FrmProAppHelper', 'upload_dir'));
        
        if(is_array($_FILES[$field_id]['name'])){
            foreach($_FILES[$field_id]['name'] as $k => $n){
                if(empty($n))
                    continue;
                    
                $f_id = $field_id . $k;
                $_FILES[$f_id] = array(
                    'name'  => $n,
                    'type'  => $_FILES[$field_id]['type'][$k],
                    'tmp_name' => $_FILES[$field_id]['tmp_name'][$k],
                    'error' => $_FILES[$field_id]['error'][$k],
                    'size'  => $_FILES[$field_id]['size'][$k]
                );
                
                unset($k);
                unset($n);
                
                $media_id = media_handle_upload($f_id, 0);
                if (is_numeric($media_id))
                    $media_ids[] = $media_id;
                else
                    $errors[] = $media_id;
            }
        }else{
            $media_id = media_handle_upload($field_id, 0);
            if (is_numeric($media_id))
                $media_ids[] = $media_id;
            else
                $errors[] = $media_id;
        }
        
        unset($media_id);
        
        if(empty($media_ids))
            return $errors;
        
        remove_filter('upload_dir', array('FrmProAppHelper', 'upload_dir'));
        
        if(count($media_ids) == 1)
            $media_ids = reset($media_ids);
        
        return $media_ids;
    }
  
    //Upload files into "formidable" subdirectory
    public static function upload_dir($uploads){
        $relative_path = apply_filters('frm_upload_folder', 'formidable');
        $relative_path = untrailingslashit($relative_path);
        
        if(!empty($relative_path)){
            $uploads['path'] = $uploads['basedir'] .'/'. $relative_path;
            $uploads['url'] = $uploads['baseurl'] .'/'. $relative_path;
            $uploads['subdir'] = '/'. $relative_path;
        }

        return $uploads;
    }
    
    public static function get_rand($length){
        $all_g = "ABCDEFGHIJKLMNOPQRSTWXZ";
        $pass = "";
        srand((double)microtime()*1000000);
        for($i=0;$i<$length;$i++) {
            srand((double)microtime()*1000000);
            $pass .= $all_g[ rand(0, strlen($all_g) - 1) ];
        }
        return $pass;
    }
    
    //check if an array is multidimensional
    public static function is_multi($a){
        foreach($a as $v){
            if(is_array($v)) return true;
        }
        return false;
    }
    
    public static function import_csv($path, $form_id, $field_ids, $entry_key=0, $start_row=2, $del=',', $max=250) {
        _deprecated_function( __FUNCTION__, '1.07.05', 'FrmProXMLHelper::import_csv()' );
        include_once(FrmAppHelper::plugin_path() .'/pro/classes/helpers/FrmProXMLHelper.php');
        return FrmProXMLHelper::import_csv($path, $form_id, $field_ids, $entry_key, $start_row, $del, $max);
    }
}
