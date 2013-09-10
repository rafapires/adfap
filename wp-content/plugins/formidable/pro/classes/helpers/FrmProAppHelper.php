<?php

class FrmProAppHelper{
    
    public static function jquery_themes(){
        return array(
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
    }
    
    public static function jquery_css_url($theme_css){
        if($theme_css == -1)
            return;

        $uploads = wp_upload_dir();
        if(!$theme_css or $theme_css == '' or $theme_css == 'ui-lightness'){
            $css_file = FRM_URL . '/css/ui-lightness/jquery-ui.css';
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
        if($user_id and !empty($user_id) and !is_numeric($user_id)){
            if($user_id == 'current'){
                global $user_ID;
                $user_id = $user_ID;
            }else{
                if(function_exists('get_user_by'))
                    $user = get_user_by('login', $user_id);
                else
                    $user = get_userdatabylogin($user_id);
                if($user)
                    $user_id = $user->ID;
                unset($user);
            }
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
    
    public static function human_time_diff($from, $to=''){
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
    	if ( 0 > $diff )
    		return '';

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
        global $current_user, $frm_siteurl;

    	$output = '';
    	if($current_user && $current_user->wp_capabilities['administrator'] == 1) 
    		$output = "<a href='{$frm_siteurl}/wp-admin/admin.php?page=formidable-entries&frm_action=edit&id={$id}'>". __('Edit', 'formidable') ."</a>";
    	
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
            <option value="delete"><?php _e('Delete', 'formidable') ?></option>
            <option value="export"><?php _e('Export to XML', 'formidable') ?></option>
            <?php if(isset($_GET) and isset($_GET['page']) and $_GET['page'] == 'formidable-entries'){ ?>
            <option value="csv"><?php _e('Export to CSV', 'formidable') ?></option>
            <?php } ?>
        </select>
        <input type="submit" value="<?php _e('Apply', 'formidable') ?>" id="doaction" class="button-secondary action"/>
        </div>
    <?php    
    }
    
    public static function search_form($sort_str, $sdir_str, $search_str, $fid=false){
        if(isset($_GET['page']) and $_GET['page'] == 'formidable-entries'){
            global $frm_form, $frm_field;
            if(isset($_GET['form']))
                $form = $frm_form->getOne($_GET['form']);
            else
                $form = $frm_form->getAll("is_template=0 AND (status is NULL OR status = '' OR status = 'published')", ' ORDER BY name', ' LIMIT 1');
            if($form)
                $field_list = $frm_field->getAll("fi.type not in ('divider','captcha','break','html') and fi.form_id=". $form->id, 'field_order');
        }
            ?>
        <div id="search_pane" style="float: right;">
            <form method="post" >
                <p class="search-box">
                    <input type="hidden" name="paged" id="paged" value="1" />
                    <input type="hidden" name="sort" id="sort" value="<?php echo esc_attr($sort_str); ?>" />
                    <input type="hidden" name="sdir" id="sort" value="<?php echo esc_attr($sdir_str); ?>" />
                    <?php if(isset($field_list) and !empty($field_list)){ ?>
                    <select name="fid">
                        <option value="">- <?php _e('All Fields', 'formidable') ?> -</option>
                        <option value="created_at" <?php selected($fid, 'created_at') ?>><?php _e('Entry creation date', 'formidable') ?></option>
                        <?php foreach($field_list as $f){ ?>
                        <option value="<?php echo $f->id ?>" <?php selected($fid, $f->id) ?>><?php echo FrmAppHelper::truncate($f->name, 30);  ?></option>
                        <?php } ?>
                    </select>
                    <?php } ?>
                    <input type="text" name="search" id="search" value="<?php echo esc_attr($search_str); ?>"/>
                    <input type="submit" class="button" value="<?php _e('Search', 'formidable') ?>"/>
                    <?php if(!empty($search_str)){ ?>
                    or <a href=""><?php _e('Reset', 'formidable') ?></a>
                    <?php } ?>
                </p>
            </form>
        </div>
    <?php
    }
    
    public static function get_shortcodes($content, $form_id){
        global $frm_field;
        $fields = $frm_field->getAll("fi.type not in ('divider','captcha','break','html') and fi.form_id=".$form_id);
        
        $tagregexp = 'editlink|siteurl|sitename|id|key|post_id|ip|created-at|updated-at';
        foreach ($fields as $field)
            $tagregexp .= '|'. $field->id . '|'. $field->field_key;

        preg_match_all("/\[(if )?($tagregexp)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?/s", $content, $matches, PREG_PATTERN_ORDER);

        return $matches;
    }
    
    public static function get_custom_post_types(){
        if(function_exists('get_post_types')){
            $custom_posts = get_post_types(array(), 'object');
            foreach(array('revision', 'attachment', 'nav_menu_item') as $unset)
                unset($custom_posts[$unset]);
            return $custom_posts;
        }
        return false;
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
            'display' => false
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
        else if($where_is == '=' and ($where_field->type == 'checkbox' or ($where_field->type == 'data' and $where_field->field_options['data_type'] == 'checkbox')))
            $where_is =  'LIKE';
            
        $field_options = maybe_unserialize($where_field->field_options);
            
        if($where_field->form_id != $form_id){
            //TODO: get linked entry IDs and get entries where data field value(s) in linked entry IDs
        }
            
        if($where_field->type == 'data' and !is_numeric($where_val)){
            $linked_id = $frmdb->get_var($frmdb->entries, array('item_key' => $where_val));
            if($linked_id)
                $where_val = $linked_id;
            unset($linked_id);
        }
              
        $temp_where_is = str_replace(array('!', 'not '), '', $where_is);
            
        //get values that aren't blank and then remove them from entry list
        if($where_val == '' and $temp_where_is == '=')
            $temp_where_is = '!=';
            
        if($where_is == 'LIKE' or $where_is == 'not LIKE')
            $where_val = "'%". esc_sql(like_escape($where_val)) ."%'";
        else if(!strpos($where_is, 'in'))
            $where_val = "'". esc_sql($where_val) ."'";
                
        $where_statement = "meta_value ". ($where_field->type == 'number' ? ' +0 ' : '') . $temp_where_is ." ". $where_val ." and fi.id='$where_opt'";
        $where_statement = apply_filters('frm_where_filter', $where_statement, $args);
            
        $new_ids = $frm_entry_meta->getEntryIds($where_statement);
            
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
                        $add_posts = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE post_id in (". implode(',', array_keys($post_ids)) .") AND meta_key='".$field_options['custom_field']."' AND meta_value ". ($where_field->type == 'number' ? ' +0 ' : ''). $where_is." ".$where_val);
                    }else{ //if field is post field
                        $add_posts = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE ID in (". implode(',', array_keys($post_ids)) .") AND ".$field_options['post_field'] .($where_field->type == 'number' ? ' +0 ' : ' '). $where_is." ".$where_val);
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
        global $frm_current_form;
        
        $form_id = 0;
        if($frm_current_form)
            $form_id = $frm_current_form->id;
        
        if(!$form_id)
            $form_id = FrmAppHelper::get_param('form', false);
            
        if(!$form_id){
            global $frm_form;
            $frm_current_form = $frm_form->getAll("is_template=0 AND (status is NULL OR status = '' OR status = 'published')", ' ORDER BY name', ' LIMIT 1');
            $form_id = $frm_current_form ? $frm_current_form->id : 0;
        }
        return $form_id;
    }
    
    public static function export_xml($type, $args = array() ) {
    	global $wpdb, $frmdb, $frmprodb;
	    
	    if(!is_array($type)){
	        $table = ($type == 'items') ? $frmdb->entries : (($type == 'displays') ? $frmprodb->{$type} : $frmdb->{$type});
	    }
	        
	    $defaults = array('is_template' => false, 'ids' => false);
	    $args = wp_parse_args( $args, $defaults );

	    $where = $join = '';
    	
        if(function_exists('sanitize_key'))
    	    $sitename = sanitize_key( get_bloginfo( 'name' ) );
    	else
    	    $sitename = sanitize_title_with_dashes( get_bloginfo( 'name' ) );
    	    
    	if ( ! empty($sitename) ) $sitename .= '.';
    	$filename = $sitename . 'formidable.' . date( 'Y-m-d' ) . '.xml';

    	header( 'Content-Description: File Transfer' );
    	header( 'Content-Disposition: attachment; filename=' . $filename );
    	header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );


        if($type == 'forms'){
            $where .= $wpdb->prepare( "{$table}.is_template = %d AND {$table}.status != 'draft'" , $args['is_template'] );
    	}else if($type == 'entries'){
    	    //$join = "INNER JOIN {$frmdb->entry_metas} ON ({$frmdb->entries}.id = {$frmdb->entry_metas}.item_id)";
    	}else if($type == 'displays'){
    	    
    	}
    	
    	if(is_array($args['ids']))
	        $args['ids'] = implode(',', $args['ids']);
	        
    	if (isset($table) and $args['ids']){
    	    if(!empty($where))
    	        $where .= " AND ";
    	    
    	    //$ids = array_fill( 0, count($args['ids']), '%s' );
    		$where .= "{$table}.id IN (". $args['ids'] .")";
        }

        echo '<?xml version="1.0" encoding="'. get_bloginfo('charset') .'" ?>'."\n";
        echo "<formidable>\n";
        if(is_array($type)){
            foreach($type as $tb_type){
                $table = ($tb_type == 'items') ? $frmdb->entries :  (($tb_type == 'displays') ? $frmprodb->{$tb_type} : $frmdb->{$tb_type});
                
                $where = '';
                if($tb_type == 'forms'){
                    //add forms
                    $where = $wpdb->prepare( "{$table}.is_template = %d AND {$table}.status != 'draft'" , $args['is_template'] );
                    if ( $args['ids'] )
                	    $where .= " AND {$table}.id IN (". $args['ids'] .")";

                }else if(($tb_type == 'items' or $tb_type == 'displays') and $args['ids'])
                    $where = "{$table}.form_id IN (". $args['ids'] .")"; 
                
                if(!empty($where))
                    $where = " WHERE ". $where;
                $records = $wpdb->get_results( "SELECT * FROM {$table}$where" );
        	    FrmProAppHelper::get_xml_for_type($tb_type, $records);

    	    }
        }else{
    	    $records = $wpdb->get_results( "SELECT * FROM {$table} $join WHERE $where" );
    	    FrmProAppHelper::get_xml_for_type($type, $records);
    	}
        echo "</formidable>\n";
    }
    
    public static function get_xml_for_type($type, $records){
        global $frmdb, $wpdb;
        
        echo "<$type>\n";
        $padding = "  ";
        foreach($records as $record){
            $singular = trim($type, 's');
            $object_key = $singular .'_key';
            echo $padding. "<$singular>\n";
            $padding .= "  ";
            echo $padding."<". $singular ."_key><![CDATA[". $record->{$object_key} ."]]></". $singular."_key>\n";
            
            foreach(array('id', 'name', 'description', 'options', 'logged_in', 'editable', 'is_template', 'default_template', 'form_id', 'entry_id', 'post_id', 'ip', 'created_at') as $col){
                if(isset($record->{$col})){
                    $col_val = maybe_unserialize($record->{$col});
                    FrmProAppHelper::xml_item($col_val, $col, $padding);
                }
            }

            if($type == 'forms'){
                global $frm_field;
                $fields = $frm_field->getAll(array('fi.form_id' => $record->id), 'field_order');
                //$fields = $wpdb->get_results("SELECT * FROM {$frmdb->fields} WHERE form_id={$record->id} ORDER BY field_order");
                if(!empty($fields)){
                    echo $padding."<fields>\n";
                    $padding .= "  ";
                    foreach($fields as $field){ 
                        echo $padding."<field>\n";
                        foreach(array('id', 'field_key', 'required', 'name', 'description', 'field_order', 'type', 'default_value', 'options', 'field_options', 'form_id') as $col){
                            if(isset($field->{$col})){
                                $col_val = maybe_unserialize($field->{$col});
                                FrmProAppHelper::xml_item($col_val, $col, $padding);
                            }
                        }
                        echo $padding."</field>\n";
                    } 
                    $padding = "  ";
                    echo $padding."</fields>\n";
                }
            }else if($type == 'items'){
                $metas = $wpdb->get_results("SELECT * FROM {$frmdb->entry_metas} WHERE item_id={$record->id}");
                if($metas){
                    echo $padding."<item_meta>\n";
                    foreach($metas as $meta){
                        echo $padding."<meta>\n";
                        echo $padding."  <field_id>$meta->field_id</field_id>\n";
                        echo $padding."  <item_id>$meta->item_id</item_id>\n";
                        //$meta_values = maybe_unserialize($meta->meta_value);
                        //if(is_array($meta_values)){ 
                        //    foreach($meta_values as $meta_key => $meta_value)
                        //        echo $padding."<meta_value type=\"$meta_key\"><![CDATA[$meta_value]]></meta_value>\n";
                        //}else
                            echo $padding."  <meta_value><![CDATA[$meta->meta_value]]></meta_value>\n";
                        echo $padding."</meta>\n";
                    }
                    echo $padding."</item_meta>\n";
                }
            }
            $padding = "  ";
            echo $padding."</$singular>\n";
        }
        echo "</$type>\n";
    }
    
    public static function xml_item($col_val, $col, $padding){
        if(is_array($col_val)){
            $singular = trim($col, 's');
            echo $padding."<$col>\n";
            foreach($col_val as $col_key => $colv){
                self::get_xml_line_item(compact('xml', 'singular', 'col_key', 'colv', 'padding', 'col'));
                unset($col_key);
                unset($colv);
            }
            echo $padding."</$col>\n";
        }else{
            echo $padding."<$col>". ((is_numeric($col_val)) ? $col_val : "<![CDATA[$col_val]]>") ."</$col>\n";
        }
    }
    
    public static function get_xml_line_item($args){
        extract($args);
        $padding .= "  ";
        if(is_array($colv)){
            $col_val = $colv;
            $old_col_key = $col_key;
            $old_singular = $singular;
            if(is_numeric($col_key))
                echo $padding ."<$singular>\n";
            else if(is_numeric($singular))
                echo $padding ."<$col_key>\n";
            else
                echo $padding ."<$singular type=\"$col_key\">\n";
            $old_padding = $padding;
            $padding .= "  ";
            $singular = $col_key;
            //if(is_numeric($singular)) 
            //    $singular = trim($singular, 's');
            foreach($col_val as $col_key => $colv){
                self::get_xml_line_item(compact('xml', 'singular', 'col_key', 'colv', 'padding', 'col'));
                unset($col_key);
                unset($colv);
            }
            $padding = $old_padding;

            if(is_numeric($old_singular))
                echo $padding."</$old_col_key>\n";
            else
                echo $padding."</$old_singular>\n";
        }else{
            if(is_numeric($singular))
                echo $padding."<$col_key>". 
                    ((is_numeric($colv)) ? $colv : "<![CDATA[$colv]]>") .
                    "</$col_key>\n";
            else
                echo $padding."<$singular type=\"$col_key\">". 
                    ((is_numeric($colv)) ? $colv : "<![CDATA[$colv]]>") .
                    "</$singular>\n";
        }
    }
    
    public static function import_xml($content){
        $xml = FrmProAppHelper::xml2ary($content);  
        $to_import = array();
        
        foreach($xml['formidable'] as $xmls){
            foreach($xmls as $type => $xml_content){
            $to_import[$type] = array();

            foreach($xmls[$type]['_c'] as $xml_vars){
                if(!isset($xml_vars[0]))
                    $xml_vars[] = $xml_vars;
                 
                foreach($xml_vars as $xml_var){   
                    $new_item = array();
                    if(!isset($xml_var['_c']))
                        $xml_var['_c'] = $xml_var;
                    
                    foreach($xml_var['_c'] as $var_key => $var){
                        if(isset($var['_v'])){
                            $new_item[$var_key] = $var['_v'];
                        }else{
                            $new_item[$var_key] = array();
                            
                            if($var_key == 'fields' or $var_key == 'item_meta'){
                                foreach($var['_c'] as $v){
                                    foreach($v as $v1){
                                        if(isset($v1['_c'])){
                                            $new_join = array();
                                            foreach($v1['_c'] as $v_key => $v2){
                                                if(isset($v2['_v'])){
                                                    $new_join[$v_key] = $v2['_v'];
                                                }else{
                                                    $new_join[$v_key] = FrmProAppHelper::xml_array_to_frm($v2);
                                                }
                                            }
                                            $new_item[$var_key][] = $new_join;
                                        }
                                    }
                                }
                            }else{
                                $new_item[$var_key] = FrmProAppHelper::xml_array_to_frm($var, $new_item[$var_key]);
                            }

                        }
                    }
                    
                    $to_import[$type][] = $new_item;

                }
                }
            }
        }

        //now add $to_import to the db
        foreach($to_import as $import_type => $datas){
            foreach($datas as $data){
                echo '<h1>'.$import_type.'</h1>';
                if($import_type == 'forms'){
                    $print = $data;
                    unset($print['fields']);
                    echo '<br/><br/>FORM: '; print_r($print);
                    //FrmForm::create( $data );
                    //now add fields
                    foreach($data['fields'] as $field_data){
                        //echo '<br/><br/>FIELD: '; print_r($field_data);
                        //FrmFields::create( $field_data, false );
                    }
                }else if($import_type == 'items'){
                    //echo '<br/><br/>ENTRY: '; print_r($data);
                    //FrmEntry::create( $data );
                }else if($import_type == 'displays'){
                    //echo '<br/><br/>DISPLAY: '; print_r($data);
                    //FrmProDisplay::create( $data );
                }
            }
        }
    }
    
    public static function xml_array_to_frm($var, $new=array()){
        foreach($var['_c'] as $v){
            foreach($v as $var_key => $v1){
                //echo '<br/>START: '. $var_key .' '; print_r($v1);
                if(isset($v1['_a']) and isset($v1['_a']['type']) and isset($v1['_v'])){
                    $new[$v1['_a']['type']] = $v1['_v'];
                }else if(isset($v1['_a']) and isset($v1['_a']['type']) and isset($v1['_c']) and is_array($v1['_c'])){
                    $new[$v1['_a']['type']] = array();
                    foreach($v1['_c'] as $v1ck => $v1c){
                        if(isset($v1c['_a']) and $v1ck == $v1['_a']['type'] and isset($v1c['_v'])){
                            $new[$v1['_a']['type']][$v1c['_a']['type']] = $v1c['_v'];
                        }else if(is_array($v1c)){
                            foreach($v1c as $v2ck => $v2c){
                                if(isset($v2c['_c'])){
                                    $new[$v1['_a']['type']] = self::xml_array_to_frm($v2c, $new[$v1['_a']['type']]);
                                }else{
                                    echo '<br/> nested array '.$v2ck; print_r($v2c);
                                }
                                unset($v2ck);
                                unset($v2c);
                            }
                            echo '<br/>final '.print_r($new[$v1['_a']['type']]);
                        }else{
                            echo '<br/> nested array '; print_r($v1c);
                            $new[$v1['_a']['type']] = self::xml_array_to_frm($v1);
                            echo '<br/>final '. print_r($new[$v1['_a']['type']]);
                        }
                        unset($v1ck);
                        unset($v1c);
                    }
                }else if($var_key == '_v' and !is_array($v1)){
                    $new = $v1;
                }else if(is_array($v1)){
                    foreach($v1 as $v1ck => $v1c){
                        echo '<br/>'. $v1c;
                        $new[$v1ck] = $v1c;
                        unset($v1ck);
                        unset($v1c);
                    }
                }else{
                    echo '<br/> OPTS? '.$var_key; print_r($v1);
                    /*
                    <option type="also_email_to">
                        <also_email_to type="0">4968</also_email_to>
                    </option>

                    
                     Array ( 
                        [_a] => Array ( 
                            [type] => also_email_to 
                        ) 
                        [_c] => Array ( 
                            [also_email_to] => Array ( 
                                [_a] => Array ( 
                                    [type] => 0 
                                ) 
                                [_v] => 4968 
                            ) 
                        ) 
                    )
                    
                <option type="hrs_list">
                    <hrs_list type="twitter_account">
                        <twitter_account type="tag">
                            <tag type="0"><![CDATA[]]></tag>
                        </twitter_account>
                        <twitter_account type="location">
                            <location type="0"><![CDATA[Business]]></location>
                        </twitter_account>
                    </hrs_list>
                </option>
                    
                    ( [_a] => Array ( [type] => hrs_list ) 
                    [_c] => Array ( 
                        [hrs_list] => Array ( 
                            [3] => Array ( 
                                [_a] => Array ( [type] => company_name ) 
                                [_c] => Array ( 
                                    [company_name] => Array ( 
                                        [_a] => Array ( [type] => tag ) 
                                        [_v] => 
                                    ) 
                                ) 
                            ) 
                            [4] => Array ( 
                                [_a] => Array ( [type] => email_address ) 
                                [_c] => Array ( 
                                    [email_address] => Array ( 
                                        [0] => Array ( 
                                            [_a] => Array ( [type] => tag ) 
                                            [_c] => Array ( 
                                                [tag] => Array ( 
                                                    [_a] => Array ( [type] => 0 ) 
                                                    [_v] => 
                                                ) 
                                            ) 
                                        ) 
                                        [1] => Array ( 
                                            [_a] => Array ( [type] => location ) 
                                            [_c] => Array ( 
                                                [location] => Array ( 
                                                    [_a] => Array ( [type] => 0 ) 
                                                    [_v] => Work 
                                                ) 
                                            ) 
                                        ) 
                                    ) 
                                ) 
                            ) 
                    
                    Array ( 
                        [conditions] => Array ( 
                            [0] => Array ( [_a] => Array ( [type] => send_stop ) [_v] => send ) 
                            [1] => Array ( [_a] => Array ( [type] => any_all ) [_v] => all ) 
                            [2] => Array ( [_c] => Array ( [hide_field] => Array ( [_v] => 11752 ) [hide_field_cond] => Array ( [_v] => != ) [hide_opt] => Array ( [_v] => 4 ) ) ) 
                            [3] => Array ( [_c] => Array ( [hide_field] => Array ( [_v] => 11752 ) [hide_field_cond] => Array ( [_v] => != ) [hide_opt] => Array ( [_v] => ) ) ) 
                        ) 
                    ) 
                    */
                }
                unset($var_key);
                unset($v1);
            }
        }

        return $new;
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
    
    
    // XML to Array
    public static function xml2ary(&$string) {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parse_into_struct($parser, $string, $vals, $index);
        xml_parser_free($parser);

        $mnary=array();
        $ary=&$mnary;
        foreach ($vals as $r) {
            $t=$r['tag'];
            if ($r['type']=='open') {
                if (isset($ary[$t])) {
                    if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
                    $cv=&$ary[$t][count($ary[$t])-1];
                } else $cv=&$ary[$t];
                if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
                $cv['_c']=array();
                $cv['_c']['_p']=&$ary;
                $ary=&$cv['_c'];

            } elseif ($r['type']=='complete') {
                if (isset($ary[$t])) { // same as open
                    if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
                    $cv=&$ary[$t][count($ary[$t])-1];
                } else $cv=&$ary[$t];
                if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
                $cv['_v']=(isset($r['value']) ? $r['value'] : '');

            } elseif ($r['type']=='close') {
                $ary=&$ary['_p'];
            }
        }    

        FrmProAppHelper::_del_p($mnary);
        return $mnary;
    }

    // _Internal: Remove recursion in result array
    public static function _del_p(&$ary) {
        foreach ($ary as $k=>$v) {
            if ($k==='_p') unset($ary[$k]);
            elseif (is_array($ary[$k])) FrmProAppHelper::_del_p($ary[$k]);
        }
    }

    // Array to XML
    public static function ary2xml($cary, $d=0, $forcetag='') {
        $res=array();
        foreach ($cary as $tag=>$r) {
            if (isset($r[0])) {
                $res[]=self::ary2xml($r, $d, $tag);
            } else {
                if ($forcetag) 
                    $tag = $forcetag;
                $sp = str_repeat("\t", $d);
                $res[] = "$sp<$tag";
                if (isset($r['_a'])) {
                    foreach ($r['_a'] as $at=>$av) $res[]=" $at=\"$av\"";
                }
                $res[] = ">". ((isset($r['_c'])) ? "\n" : '');
                if (isset($r['_c']))
                    $res[] = self::ary2xml($r['_c'], $d+1);
                else if (isset($r['_v'])) 
                    $res[] = $r['_v'];
                $res[] = (isset($r['_c']) ? $sp : '')."</$tag>\n";
            }

        }
        return implode('', $res);
    }

    // Insert element into array
    public static function ins2ary(&$ary, $element, $pos=0) {
        $ar1 = array_slice($ary, 0, $pos); 
        $ar1[] = $element;
        $ary = array_merge($ar1, array_slice($ary, $pos));
    }
    
    public static function import_csv($path, $form_id, $field_ids, $entry_key=0, $start_row=2, $del=','){
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
                    //if($data[$key] == ''){ 
                    //    error_log($row .' key:'. $key .' $data[$key] empty'); 
                    //    return $row-1;
                    //}
                    
                    if(is_numeric($field_id)){
                        if(isset($importing_fields[$field_id])){
                            $field = $importing_fields[$field_id];
                        }else{
                            $field = $frm_field->getOne($field_id);
                            $importing_fields[$field_id] = $field;
                        }
                        
                        $values['item_meta'][$field_id] = apply_filters('frm_import_val', $data[$key], $field);
                        
                        if($field->type == 'user_id'){
                            $values['item_meta'][$field_id] = trim($values['item_meta'][$field_id]);
                            if(!is_numeric($values['item_meta'][$field_id])){
                                if(!isset($user_array)){
                                     $users = $wpdb->get_results("SELECT ID, user_login, display_name FROM {$wpdb->users} ORDER BY display_name ASC");
                                     $user_array = array();
                                     foreach($users as $user){
                                         $ukey = (!empty($user->display_name)) ? $user->display_name : $user->user_login;
                                         $user_array[$ukey] = $user->ID;
                                         if($ukey != $user->user_login)
                                            $user_array[$user->user_login] = $user->ID;
                                         unset($ukey);
                                         unset($user);
                                     }
                                     unset($users);
                                 }
                                 
                                 if(isset($user_array[$values['item_meta'][$field_id]]))
                                     $values['item_meta'][$field_id] = (int)$user_array[$values['item_meta'][$field_id]];
                            }
                                
                            $values['frm_user_id'] = $values['item_meta'][$field_id];
                        }else if(($field->type == 'checkbox' or ($field->type == 'select' and isset($field->field_options['multiple']) and $field->field_options['multiple'])) and !empty($values['item_meta'][$field_id])){
                            if(!in_array($values['item_meta'][$field_id], (array)$field->options)){
                                $checked = maybe_unserialize($values['item_meta'][$field_id]);
                                if(!is_array($checked))
                                    $checked = explode(',', $checked);
                                    
                                if($checked and count($checked) > 1)
                                    $values['item_meta'][$field_id] = array_map('trim', $checked);
                            }
                        }else if($field->type == 'data'){
                            $field->field_options = maybe_unserialize($field->field_options);
                            if($field->field_options['data_type'] != 'data'){
                                $new_id = $wpdb->get_var($wpdb->prepare(
                                    "SELECT item_id FROM $frmdb->entry_metas WHERE field_id=%d and meta_value=%s", 
                                    $field->field_options['form_select'],
                                    $values['item_meta'][$field_id]
                                ));
                                
                                if($new_id and is_numeric($new_id))
                                    $values['item_meta'][$field_id] = $new_id;
                                unset($new_id);
                            }
                        }else if($field->type == 'file'){
                            $values['item_meta'][$field_id] = explode(',', $values['item_meta'][$field_id]);
                            foreach($values['item_meta'][$field_id] as $pos => $m){
                                $m = trim($m);
                                if(!is_numeric($m)){
                                    //get the ID from the URL if on this site
                                    $m = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE guid='%s';", $m ));
                                }
                                
                                if(!is_numeric($m))
                                    unset($values['item_meta'][$field_id][$pos]);
                                else
                                    $values['item_meta'][$field_id][$pos] = $m;
                                
                                unset($pos);
                                unset($m);
                            }
                        }
                        
                        if(isset($_POST['item_meta'][$field_id]) and ($field->type == 'checkbox' or ($field->type == 'data' and $field->field_options['data_type'] != 'checkbox'))){
                            if(empty($values['item_meta'][$field_id])){
                                $values['item_meta'][$field_id] = $_POST['item_meta'][$field_id];
                            }else if(!empty($_POST['item_meta'][$field_id])){
                                $values['item_meta'][$field_id] = array_merge((array)$_POST['item_meta'][$field_id], (array)$values['item_meta'][$field_id]);
                            }
                        }
                        
                        $_POST['item_meta'][$field_id] = $values['item_meta'][$field_id];
                        FrmProEntryMeta::set_post_fields($field, $values['item_meta'][$field_id]);
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
                   
               if(isset($values['created_at']))
                   $values['created_at'] = date('Y-m-d H:i:s', strtotime($values['created_at']));
                   
               if(isset($values['updated_at']))
                   $values['updated_at'] = date('Y-m-d H:i:s', strtotime($values['updated_at']));
            

               $created = $frm_entry->create($values);
               unset($_POST);
               unset($values);
               unset($created);
               
               if(($row-$start_row) >= 250){ //change max rows here
                   fclose($f);
                   return $row;
               }
           }
           fclose($f);
           return $row;
        }
    }
    
    
    public static function csvstring_to_array(&$string, $csv_del=',', $CSV_ENCLOSURE='"', $CSV_LINEBREAK="\n") {
        $o = array();

        $cnt = strlen($string);
        $esc = $escesc = false;
        $num = $i = 0;
        while ($i < $cnt){
            $s = $string[$i];

            if ($s == $CSV_LINEBREAK){
                if ($esc){
                    $o[$num] .= $s;
                }else{
                    $i++;
                    break;
                }
            }else if ($s == $csv_del){
                if ($esc){
                    $o[$num] .= $s;
                }else{
                    $num++;
                    $esc = $escesc = false;
                }
            }else if ($s == $CSV_ENCLOSURE){
                if ($escesc){
                    $o[$num] .= $CSV_ENCLOSURE;
                    $escesc = false;
                }

                if ($esc){
                    $esc = false;
                    $escesc = true;
                }else{
                    $esc = true;
                    $escesc = false;
                }
            }else{
                if ($escesc){
                    $o[$num] .= $CSV_ENCLOSURE;
                    $escesc = false;
                }

                $o[$num] .= $s;
            }

            $i++;
        }

        //  $string = substr($string, $i);

        return $o;
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
}
