<?php
/**
 * @package Formidable
 */
 
class FrmProDisplaysController{

    function FrmProDisplaysController(){
        add_action('init', array( &$this, 'register_post_types'), 0);
        add_action('admin_menu', array( &$this, 'menu' ), 21);
        add_filter('admin_head-post.php', array( &$this, 'highlight_menu' ));
        add_filter('admin_head-post-new.php', array( &$this, 'highlight_menu' ));
        add_action('restrict_manage_posts', array( &$this, 'switch_form_box'));
        add_filter('parse_query', array( &$this, 'filter_forms') );
        add_filter('views_edit-frm_display', array( &$this, 'add_form_nav') );
        add_filter('post_row_actions', array(&$this, 'post_row_actions'), 10, 2 );
        //add_filter('bulk_actions-edit-frm_display', array( &$this, 'add_bulk_actions') );
        
        add_filter('default_content', array(&$this, 'default_content'), 10, 2 );
    	add_filter('default_title',   array(&$this, 'default_title'), 10, 2 );
    	add_filter('default_excerpt', array(&$this, 'default_title'), 10, 2 );
        
        add_action('post_submitbox_misc_actions', array(&$this, 'submitbox_actions'));
        add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'), 10, 2);
        add_action('save_post', array(&$this, 'save_post'));
        add_action('before_delete_post', array(&$this, 'before_delete_post'));
        
        add_filter('the_content', array(&$this, 'get_content'), 8);
        add_action('wp_ajax_frm_get_cd_tags_box', array(&$this, 'get_tags_box'));
        add_action('wp_ajax_frm_get_entry_select', array(&$this, 'get_entry_select') );
        add_action('wp_ajax_frm_get_date_field_select', array(&$this, 'get_date_field_select') );
        add_action('wp_ajax_frm_add_where_row', array(&$this, 'get_where_row'));
        add_action('wp_ajax_frm_add_where_options', array(&$this, 'get_where_options'));
        add_filter('frm_before_display_content', array(&$this, 'calendar_header'), 10, 3);
        add_filter('frm_display_entries_content', array(&$this, 'build_calendar'), 10, 5);
        add_filter('frm_after_display_content', array(&$this, 'calendar_footer'), 10, 3);
        
        //Shortcodes
        add_shortcode('display-frm-data', array(&$this, 'get_shortcode'), 1);
    }
    
    function register_post_types(){
        register_post_type('frm_display', array(
            'label' => __('Custom Displays', 'formidable'),
            'description' => '',
            'public' => true,
            'show_ui' => true,
            'exclude_from_search' => true,
            'show_in_nav_menus' => false,
            'show_in_menu' => false,
            'menu_icon' => admin_url('images/icons32.png'),
            'capability_type' => 'page',
            'supports' => array(
                'title', 'revisions'
            ),
            'has_archive' => false,
            'labels' => array(
                'name' => __('Custom Displays', 'formidable'),
                'singular_name' => __('Custom Display', 'formidable'),
                'menu_name' => __('Custom Displays', 'formidable'),
                'edit' => __('Edit', 'formidable'),
                'search_items' => __('Search', 'formidable'),
                'not_found' => __('No Custom Displays Found.', 'formidable'),
                'edit_item' => __('Edit Custom Display', 'formidable')
            )
        ) );
    }
    
    function menu(){
        global $frm_settings;
        
        add_submenu_page('formidable', 'Formidable | '. __('Custom Displays', 'formidable'), __('Custom Displays', 'formidable'), 'frm_edit_displays', 'edit.php?post_type=frm_display');
        
        if(class_exists('WP_List_Table')){
            add_filter('manage_edit-frm_display_columns', array(&$this, 'manage_columns'));
            add_filter('manage_edit-frm_display_sortable_columns', array(&$this, 'sortable_columns'));
            add_filter('get_user_option_manageedit-frm_displaycolumnshidden', array(&$this, 'hidden_columns'));
            add_action('manage_frm_display_posts_custom_column', array(&$this, 'manage_custom_columns'), 10, 2);
        }
    }
    
    function highlight_menu(){
        global $post, $pagenow;

        if(($pagenow == 'post-new.php' and isset($_REQUEST['post_type']) and $_REQUEST['post_type'] == 'frm_display') or 
        (is_object($post) and $post->post_type == 'frm_display')){

        echo <<<HTML
<script type="text/javascript">
jQuery(document).ready(function(){
jQuery('#toplevel_page_formidable').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu wp-menu-open');
jQuery('#toplevel_page_formidable a.wp-has-submenu').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu wp-menu-open');
});
</script>
HTML;
        }
    }
    
    function switch_form_box(){
        global $post_type_object;
        if(!$post_type_object or $post_type_object->name != 'frm_display')
            return;
        $form_id = (isset($_GET['form'])) ? $_GET['form'] : '';
        echo FrmFormsHelper::forms_dropdown( 'form', $form_id, __('Show All Forms', 'formidable'));
    }
    
    function filter_forms($query){
        global $pagenow;

        if(!is_admin() or $pagenow != 'edit.php' or !isset($_GET['post_type']) or $_GET['post_type'] != 'frm_display')
            return $query;
            
        if(isset($_REQUEST['form']) and is_numeric($_REQUEST['form'])){
            $query->query_vars['meta_key'] = 'frm_form_id';
            $query->query_vars['meta_value'] = (int)$_REQUEST['form'];
        }
        
        return $query;
    }
    
    function add_form_nav($views){
        global $pagenow;
        
        if(!is_admin() or $pagenow != 'edit.php' or !isset($_GET['post_type']) or $_GET['post_type'] != 'frm_display')
            return $views;
          
        $form = (isset($_REQUEST['form']) and is_numeric($_REQUEST['form'])) ? $_REQUEST['form'] : false;
        if($form) FrmAppController::get_form_nav($form, true);
        
        return $views;
    }
    
    function post_row_actions($actions, $post){
        if($post->post_type == 'frm_display'){
            $actions['duplicate'] = '<a href="'. admin_url('post-new.php?post_type=frm_display&amp;copy_id='. $post->ID) .'" title="'. esc_attr( __( 'Duplicate', 'formidable' ) ) .'">'. __( 'Duplicate', 'formidable' ) .'</a>';
        }
        return $actions;
    }

    function create_from_template($path){
        global $frmpro_display;
        $templates = glob($path."/*.php");
        
        for($i = count($templates) - 1; $i >= 0; $i--){
            $filename = str_replace('.php', '', str_replace($path.'/', '', $templates[$i]));
            $display = get_page_by_path($filename, OBJECT, 'frm_display');
            
            $values = FrmProDisplaysHelper::setup_new_vars();
            $values['display_key'] = $filename;
            
            include_once($templates[$i]);
        }
    }
    
    function duplicate(){
        global $frmpro_display;
        
        $params = $this->get_params();
        $record = $frmpro_display->duplicate( $params['id'] );
        $message = __('Custom Display was Successfully Copied', 'formidable');
        if ($record)
            return $this->get_edit_vars($record, '', $message);
        else
            return $this->display_list($params, __('There was a problem creating new Entry Display settings.', 'formidable'));
    }
    
    function bulk_actions($action=''){    
        if($bulkaction == 'export'){
            $items = $_REQUEST['item-action'];
            export_xml($items);
        }
    }
    
    function export_xml(){
        $ids = array();
        foreach($items as $i){
            $ids[] = (int)$i;
            unset($i);
        }
        
        $ids = implode(',', $ids);
        export_wp( array('content' => 'frm_display') );
        
        $sitename = sanitize_key( get_bloginfo( 'name' ) );
    	if ( ! empty($sitename) ) $sitename .= '.';
    	$filename = $sitename . 'wordpress.' . date( 'Y-m-d' ) . '.xml';

    	header( 'Content-Description: File Transfer' );
    	header( 'Content-Disposition: attachment; filename=' . $filename );
    	header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

    	// grab a snapshot of post IDs, just in case it changes during the export
    	$post_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'frm_display' and ID in (". $ids .")");

    	add_filter( 'wxr_export_skip_postmeta', 'wxr_filter_postmeta', 10, 2 );
    	include_once(FRMPRO_VIEWS_PATH.'/displays/xml.php');
    	die();
    }
    
    function manage_columns($columns){
        unset($columns['title']);
        unset($columns['date']);
        
        $columns['id'] = 'ID';
        $columns['title'] = __('Name', 'formidable');
        $columns['description'] = __('Description', 'formidable');
        $columns['form_id'] = __('Form', 'formidable');
        $columns['show_count'] = __('Entry', 'formidable');
        $columns['post_id'] = __('Page', 'formidable');
        $columns['content'] = __('Content', 'formidable');
        $columns['dyncontent'] = __('Dynamic Content', 'formidable');
        $columns['date'] = __('Date', 'formidable');
        $columns['name'] = __('Key', 'formidable');
        $columns['old_id'] = __('Former ID', 'formidable');
        $columns['shortcode'] = __('Shortcode', 'formidable');
        
        return $columns;
    }
    
    function sortable_columns(){
        return array(
            'id'            => 'ID',
            'title'         => 'post_title',
            'description'   => 'post_excerpt',
            'name'          => 'post_name',
            'content'       => 'post_content',
            'date'          => 'post_date',
            'shortcode'     => 'ID'
        );
    }
    
    function hidden_columns($result){
        $return = false;
        foreach((array)$result as $r){
            if(!empty($r)){
                $return = true;
                break;
            }
        }
        
        if($return)
            return $result;

        $result[] = 'post_id';
        $result[] = 'content';
        $result[] = 'dyncontent';
        $result[] = 'old_id';
                
        return $result;
    }
    
    function manage_custom_columns($column_name, $id){
        $val = '';
        
        switch ( $column_name ) {
			case 'id':
			    $val = $id;
			    break;
			case 'old_id':
			    $old_id = get_post_meta($id, 'frm_old_id', true);
			    $val = ($old_id) ? $old_id : __('N/A', 'formidable');
			    break;
			case 'name':
			case 'content':
			    $post = get_post($id);
			    $val = FrmAppHelper::truncate(strip_tags($post->{"post_$column_name"}), 100);
			    break;
			case 'description':
			    $post = get_post($id);
			    $val = FrmAppHelper::truncate(strip_tags($post->post_excerpt), 100);
		        break;
			case 'show_count':
			    $val = ucwords(get_post_meta($id, 'frm_'. $column_name, true));
			    break;
			case 'dyncontent':
			    $val = FrmAppHelper::truncate(strip_tags(get_post_meta($id, 'frm_'. $column_name, true)), 100);
			    break;
			case 'form_id':
			    global $frm_form;
			    $form_id = get_post_meta($id, 'frm_'. $column_name, true);
			    $form = $frm_form->getName($form_id);
			    if($form)
			        $val = '<a href="'. admin_url('admin.php') .'?page=formidable&frm_action=edit&id='. $form_id .'">'. FrmAppHelper::truncate($form, 40) .'</a>';
				else
				    $val = '';
				break; 
			case 'post_id':
			    $insert_loc = get_post_meta($id, 'frm_insert_loc', true);
			    if(!$insert_loc or $insert_loc == 'none'){
			        $val = '';
			        break;
			    }
			        
			    $post_id = get_post_meta($id, 'frm_'. $column_name, true);
			    $auto_post = get_post($post_id);
			    if($auto_post)
			        $val = '<a href="'. admin_url('post.php') .'?post='. $post_id .'&amp;action=edit">'. FrmAppHelper::truncate($auto_post->post_title, 50) .'</a>';
			    else
			        $val = '';
			    break;
			case 'shortcode':
			    $code = "[display-frm-data id={$id} filter=1]";
			    
			    $val = "<input type='text' style='font-size:10px;width:100%;' readonly='true' onclick='this.select();' onfocus='this.select();' value='{$code}' />";
		        break;
			default:
			    $val = $column_name;
			break;
		}
		
        echo $val;
    }
    
    function submitbox_actions(){
        global $post;
        if($post->post_type != 'frm_display')
            return;
        
        include(FRMPRO_VIEWS_PATH.'/displays/submitbox_actions.php');
    }
    
    function default_content($content, $post){
        if($post->post_type == 'frm_display' and isset($_GET) and isset($_GET['copy_id'])){
            global $frmpro_display, $copy_display;
            $copy_display = $frmpro_display->getOne($_GET['copy_id']);
            if($copy_display)
                $content = $copy_display->post_content;
        }
        return $content;
    }
    
    function default_title($title, $post){
        if($post->post_type == 'frm_display' and isset($_GET) and isset($_GET['copy_id'])){
            global $copy_display;
            if($copy_display)
                $title = $copy_display->post_title;
        }
        return $title;
    }
    
    function default_excerpt($excerpt, $post){
        if($post->post_type == 'frm_display' and isset($_GET) and isset($_GET['copy_id'])){
            global $copy_display;
            if($copy_display)
                $excerpt = $copy_display->post_excerpt;
        }
        return $excerpt;
    }
    
    function add_meta_boxes($post_type, $post=false){
        if($post_type != 'frm_display')
            return;
            
        add_meta_box('frm_form_disp_type', __('Basic Settings', 'formidable'), array(&$this, 'mb_form_disp_type'), 'frm_display', 'normal', 'high');
        add_meta_box('frm_dyncontent', __('Content', 'formidable'), array(&$this, 'mb_dyncontent'), 'frm_display', 'normal', 'high');
        add_meta_box('frm_excerpt', __('Description', 'formidable'), array(&$this, 'mb_excerpt'), 'frm_display', 'normal', 'high');
        add_meta_box('frm_advanced', __('Advanced Settings', 'formidable'), array(&$this, 'mb_advanced'), 'frm_display', 'advanced');
        
        
        add_meta_box('frm_adv_info', __('Customization', 'formidable'), array(&$this, 'mb_adv_info'), 'frm_display', 'side', 'low');
    }
    
    function save_post($post_id){
        //Verify nonce
        if (empty($_POST) or (isset($_POST['frm_save_display']) and !wp_verify_nonce($_POST['frm_save_display'], 'frm_save_display_nonce')) or !isset($_POST['post_type']) or $_POST['post_type'] != 'frm_display' or (defined('DOING_AUTOSAVE') and DOING_AUTOSAVE) or !current_user_can('edit_post', $post_id))
            return;
        
        $post = get_post($post_id);
        if($post->post_status == 'inherit')
            return;

        global $frmpro_display;
        $record = $frmpro_display->update( $post_id, $_POST );
        do_action('frm_create_display', $post_id, $_POST);
    }
    
    function before_delete_post($post_id){
        $post = get_post($post_id);
        if($post->post_type != 'frm_display')
            return;
        
        global $wpdb, $frmpro_display;
        
        $used_by = $wpdb->get_col("SELECT post_ID FROM $wpdb->postmeta WHERE meta_key='frm_display_id' AND meta_value=$post_id");
        if(!$used_by)
            return;
        
        $form_id = get_post_meta($post_id, 'frm_form_id', true);
        $next_display = $frmpro_display->get_auto_custom_display(compact('form_id'));
        if($next_display and $next_display->ID){
            $wpdb->update($wpdb->postmeta, 
                array('meta_value' => $next_display->ID), 
                array('meta_key' => 'frm_display_id',  'meta_value' => $post_id)
            );
        }else{
            $wpdb->delete($wpdb->postmeta, array('meta_key' => 'frm_display_id', 'meta_value' => $post_id));
        }
    }
    
    /* META BOXES */
    function mb_dyncontent($post){
        global $copy_display;
        if($copy_display and isset($_GET) and isset($_GET['copy_id']))
            $post = $copy_display;
        
        $post = FrmProDisplaysHelper::setup_edit_vars($post);
        
        include(FRMPRO_VIEWS_PATH.'/displays/mb_dyncontent.php');
    }
    
    function mb_excerpt($post){
        include(FRMPRO_VIEWS_PATH.'/displays/mb_excerpt.php');
        
        //add form nav via javascript
        $form = get_post_meta($post->ID, 'frm_form_id', true);
        if($form){
            echo '<div id="frm_nav_container" style="display:none;">';
            FrmAppController::get_form_nav($form, true);
            echo '</div>';
            echo '<script type="text/javascript">jQuery(document).ready(function($){ $(".wrap h2").after( $("#frm_nav_container").show());})</script>'; 
        }
    }
    
    function mb_form_disp_type($post){
        global $frm_siteurl, $frmpro_settings, $copy_display;
        if($copy_display and isset($_GET) and isset($_GET['copy_id']))
            $post = $copy_display;
            
        $post = FrmProDisplaysHelper::setup_edit_vars($post);
        
        include(FRMPRO_VIEWS_PATH.'/displays/mb_form_disp_type.php');
    }
    
    function mb_advanced($post){
        global $copy_display;
        if($copy_display and isset($_GET) and isset($_GET['copy_id']))
            $post = $copy_display;
            
        $post = FrmProDisplaysHelper::setup_edit_vars($post);

        include(FRMPRO_VIEWS_PATH.'/displays/mb_advanced.php');
    }
    
    function mb_adv_info($post){
        global $copy_display;
        if($copy_display and isset($_GET) and isset($_GET['copy_id']))
            $post = $copy_display;
            
        $post = FrmProDisplaysHelper::setup_edit_vars($post);
        self::mb_tags_box($post->frm_form_id);
    }
    
    public static function mb_tags_box($form_id){
        global $frm_field, $frmdb;
        
        $fields = array();
        
        if($form_id)
            $fields = $frm_field->getAll(array('fi.form_id' => (int)$form_id), 'field_order');
        
        $linked_forms = array();
        $col = 'one';
        $settings_tab = (isset($_GET) and isset($_GET['page']) and $_GET['page'] == 'formidable') ? true : false; 

        $cond_shortcodes = array(
            'equals=&#34;something&#34;' => __('Equals', 'formidable'),
            'not_equal=&#34;something&#34;' => __('Does Not Equal', 'formidable'),
            'equals=&#34;&#34;' => __('Is Blank', 'formidable'),
            'not_equal=&#34;&#34;' => __('Is Not Blank', 'formidable'),
            'like=&#34;something&#34;' => __('Is Like', 'formidable'),
            'not_like=&#34;something&#34;' => __('Is Not Like', 'formidable'),
            'greater_than=&#34;3&#34;' => __('Greater Than', 'formidable'),
            'less_than=&#34;-1 month&#34;' => __('Less Than', 'formidable')
        );
        
        $adv_shortcodes = array(
            'sep=&#34;, &#34;' => array('label' => __('Separator', 'formidable'), 'title' => __('Use a different separator for checkbox fields', 'formidable') ),
            'clickable=1' => __('Clickable Links', 'formidable'),
            'links=0'   => array('label' => __('Remove Links', 'formidable'), 'title' => __('Removes the automatic links to category pages', 'formidable')),
            'sanitize=1' => array('label' => __('Sanitize', 'formidable'), 'title' => __('Replaces spaces with dashes and lowercases all. Use if adding an HTML class or ID', 'formidable')),
            'sanitize_url=1' => array('label' => __('Sanitize URL', 'formidable'), 'title' =>  __('Replaces all HTML entities with a URL safe string.', 'formidable')),
            'truncate=40' => array('label' => __('Truncate', 'formidable'), 'title' => __('Truncate text with a link to view more. If using Both (dynamic), the link goes to the detail page. Otherwise, it will show in-place.', 'formidable')),
            'truncate=100 more_text=&#34;More&#34;' => __('More Text', 'formidable'),
            'time_ago=1' => array('label' => __('Time Ago', 'formidable'), 'title' => __('How long ago a date was in minutes, hours, days, months, or years.', 'formidable')),
            'format=&#34;d-m-Y&#34;' => __('Date Format', 'formidable'),
            'decimal=2 dec_point=&#34.&#34 thousands_sep=&#34,&#34' => __('Number Format', 'formidable'),
            'show=&#34;field_label&#34;' => __('Field Label', 'formidable'),
            'show=&#34;value&#34;' => array('label' => __('Saved Value', 'formidable'), 'title' => __('Show the saved value for fields with separate values.', 'formidable') ),
            'wpautop=0' => array('label' => __('No Auto P', 'formidable'), 'title' => __('Do not automatically add any paragraphs or line breaks', 'formidable')),
            'striphtml=1' => array('label' => __('Remove HTML', 'formidable'), 'title' => __('Remove all HTML added into your form before display', 'formidable')),
            'keepjs=1' => array('label' => __('Keep JS', 'formidable'), 'title' => __('Javascript from your form entries are automatically removed. Add this option only if you trust those submitting entries.', 'formidable')),
        );

        // __('Leave blank instead of defaulting to User Login', 'formidable') : blank=1
        
        $user_fields = array(
            'ID' => __('User ID', 'formidable'), 'first_name' => __('First Name', 'formidable'),
            'last_name' => __('Last Name', 'formidable'), 'display_name' => __('Display Name', 'formidable'),
            'user_login' => __('User Login', 'formidable'), 'user_email' => __('Email', 'formidable'), 
            'avatar' => __('Avatar', 'formidable')
        );
        
        include(FRMPRO_VIEWS_PATH.'/shared/mb_adv_info.php');
    }
    
    function get_tags_box(){
        self::mb_tags_box($_POST['form_id']);
        die();
    }
    
    /* FRONT END */
    
    function get_content($content){
        global $post, $frmpro_display;
        if(!$post) return $content;
        
        $display = $entry_id = false;
        if($post->post_type == 'frm_display' and in_the_loop()){
            global $frm_displayed;
            if(!$frm_displayed)
                $frm_displayed = array();
                
            if(in_array($post->ID, $frm_displayed))
                return $content;
 
            $frm_displayed[] = $post->ID; 
            
            $display = FrmProDisplaysHelper::setup_edit_vars($post, false);
            return $this->get_display_data($post, $content, false, array('filter' => true)); 
        }
        
        $display_id = get_post_meta($post->ID, 'frm_display_id', true);

        if(!$display_id or (!is_single() and !is_page()))
            return $content;
        
        $display = $frmpro_display->getOne($display_id);
            
        if ($display){
            global $frm_displayed, $frm_display_position;
            
            if($post->post_type != 'frm_display')
                $display = FrmProDisplaysHelper::setup_edit_vars($display, false);
            
            if(!isset($display->frm_insert_pos))
                $display->frm_insert_pos = 1;
                
            if(!$frm_displayed)
                $frm_displayed = array();
            
            if(!$frm_display_position)
                $frm_display_position = array();
            
            if(!isset($frm_display_position[$display->ID]))
                $frm_display_position[$display->ID] = 0;
            
            $frm_display_position[$display->ID]++;
            
            //make sure this isn't loaded multiple times but still works with themes and plugins that call the_content multiple times
            if(in_the_loop() and !in_array($display->ID, (array)$frm_displayed) and $frm_display_position[$display->ID] >= (int)$display->frm_insert_pos){
                if(is_singular() and post_password_required())
                    return $content;
                    
                global $frmdb, $wpdb;

                //get the entry linked to this post
                if((is_single() or is_page()) and $post->post_type != 'frm_display' and ($display->frm_insert_loc == 'none' or ($display->frm_insert_loc != 'none' and $display->frm_post_id != $post->ID))){
                    $entry = $wpdb->get_row("SELECT id, item_key FROM $frmdb->entries WHERE post_id={$post->ID}");
                    if(!$entry)
                        return $content;

                    $entry_id = $entry->id;

                    if(in_array($display->frm_show_count, array('dynamic', 'calendar')) and $display->frm_type == 'display_key')
                        $entry_id = $entry->item_key;
                }
                    
                
                $frm_displayed[] = $display->ID; 
                $content = $this->get_display_data($display, $content, $entry_id, array('filter' => true)); 
            }   
        }

        return $content;
    }
    
    function get_where_row(){
        $this->add_where_row($_POST['where_key'], $_POST['form_id']);
        die();
    }
    
    function add_where_row($where_key='', $form_id='', $where_field='', $where_is='', $where_val=''){
        $where_key = (int)$where_key;
        require(FRMPRO_VIEWS_PATH .'/displays/where_row.php');
    }
    
    function get_where_options(){
        $this->add_where_options($_POST['field_id'],$_POST['where_key']);
        die();
    }
    
    function add_where_options($field_id, $where_key, $where_val=''){
        global $frm_field;
        if(is_numeric($field_id)){
            $field = $frm_field->getOne($field_id);
            $field->field_options = maybe_unserialize($field->field_options);
        }
        
        require(FRMPRO_VIEWS_PATH .'/displays/where_options.php');
    }
    
    function calendar_header($content, $display, $show='one'){
        if($display->frm_show_count != 'calendar' or $show == 'one') return $content;
        
        global $frm_load_css, $wp_locale;
        $frm_load_css = true;
        
        $year = FrmAppHelper::get_param('frmcal-year', date_i18n('Y')); //4 digit year
        $month = FrmAppHelper::get_param('frmcal-month', date_i18n('m')); //Numeric month without leading zeros
        
        $month_names = $wp_locale->month;
        
        $prev_year = $next_year = $year;

        $prev_month = $month-1;
        $next_month = $month+1;

        if ($prev_month == 0 ) {
            $prev_month = 12;
            $prev_year = $year - 1;
        }
        
        if ($next_month == 13 ) {
            $next_month = 1;
            $next_year = $year + 1;
        }
        
        if($next_month < 10)
            $next_month = '0'. $next_month;
        
        if($prev_month < 10)
            $prev_month = '0'. $prev_month;
        
        ob_start();
        include(FRMPRO_VIEWS_PATH.'/displays/calendar-header.php');
        $content .= ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    function build_calendar($new_content, $entries, $shortcodes, $display, $show='one'){
        if(!$display or $display->frm_show_count != 'calendar') return $new_content;
        
        global $frm_entry_meta, $wp_locale, $frm_field;

        $current_year = date_i18n('Y');
        $current_month = date_i18n('n');
        
        $year = FrmAppHelper::get_param('frmcal-year', date('Y')); //4 digit year
        $month = FrmAppHelper::get_param('frmcal-month', $current_month); //Numeric month without leading zeros
        
        $timestamp = mktime(0, 0, 0, $month, 1, $year);
        $maxday = date('t', $timestamp); //Number of days in the given month
        $this_month = getdate($timestamp);
        $startday = $this_month['wday'];
        
        // week_begins = 0 stands for Sunday
    	$week_begins = apply_filters('frm_cal_week_begins', intval(get_option('start_of_week')), $display);
    	if($week_begins > $startday)
            $startday = $startday + 7;
        
        $week_ends = 6 + (int)$week_begins;
        if($week_ends > 6)
            $week_ends = (int)$week_ends - 7;
        
        if($current_year == $year and $current_month == $month)
            $today = date_i18n('j');
        
        $show_entres = false;
        $daily_entries = array();
        
        if(isset($display->frm_date_field_id) and is_numeric($display->frm_date_field_id))
            $field = $frm_field->getOne($display->frm_date_field_id);
            
        if(isset($display->frm_edate_field_id) and is_numeric($display->frm_edate_field_id))
            $efield = $frm_field->getOne($display->frm_edate_field_id);
        else
            $efield = false;
            
        foreach ($entries as $entry){
            if(isset($display->frm_date_field_id) and is_numeric($display->frm_date_field_id)){
                if(isset($entry->metas))
                    $date = isset($entry->metas[$display->frm_date_field_id]) ? $entry->metas[$display->frm_date_field_id] : false;
                else
                    $date = $frm_entry_meta->get_entry_meta_by_field($entry->id, $display->frm_date_field_id);
                    
                if($entry->post_id and !$date){
                    if($field){
                        $field->field_options = maybe_unserialize($field->field_options);
                        if($field->field_options['post_field']){
                            $date = FrmProEntryMetaHelper::get_post_value($entry->post_id, $field->field_options['post_field'], $field->field_options['custom_field'], array('form_id' => $display->frm_form_id, 'type' => $field->type, 'field' => $field));
                        }
                    }
                }
            }else if($display->frm_date_field_id == 'updated_at'){
                $date = $entry->updated_at;
                $i18n = true;
            }else{
                $date = $entry->created_at;
                $i18n = true;
            }
            if(empty($date)) continue;
            
            if(isset($il8n) and $il8n)
                $date = date_i18n('Y-m-d', strtotime($date));
            else
                $date = date('Y-m-d', strtotime($date));
                
            unset($i18n);
            $dates = array($date);
            
            if(isset($display->frm_edate_field_id) and !empty($display->frm_edate_field_id)){
                if(is_numeric($display->frm_edate_field_id) and $efield){
                    $edate = FrmProEntryMetaHelper::get_post_or_meta_value($entry, $efield);
                    
                    if($efield and $efield->type == 'number' and is_numeric($edate))
                        $edate = date('Y-m-d', strtotime('+'. $edate .' days', strtotime($date)));
                    
                }else if($display->frm_edate_field_id == 'updated_at'){
                    $edate = date_i18n('Y-m-d', strtotime($entry->updated_at));
                }else{
                    $edate = date_i18n('Y-m-d', strtotime($entry->created_at));
                }

                if($edate and !empty($edate)){
                    $from_date = strtotime($date);                    
                    $to_date = strtotime($edate);
                    
                    if(!empty($from_date) and $from_date < $to_date){
                        for($current_ts = $from_date; $current_ts <= $to_date; $current_ts += (60*60*24))
                            $dates[] = date('Y-m-d', $current_ts);
                        unset($current_ts);
                    }
                    
                    unset($from_date);
                    unset($to_date);
                }
                unset($edate);
                
                $used_entries = array();
            }
            unset($date);
            
            $dates = apply_filters('frm_show_entry_dates', $dates, $entry);
            
            for ($i=0; $i<($maxday+$startday); $i++){
                $day = $i - $startday + 1;

                if(in_array(date('Y-m-d', strtotime("$year-$month-$day")), $dates)){
                    $show_entres = true;
                    $daily_entres[$i][] = $entry;
                }
                    
                unset($day);
            }
            unset($dates);
        }
            
        $day_names = $wp_locale->weekday_abbrev;
        $day_names = FrmProAppHelper::reset_keys($day_names); //switch keys to order
        
        if($week_begins){
            for ($i=$week_begins; $i<($week_begins+7); $i++){
                if(!isset($day_names[$i]))
                    $day_names[$i] = $day_names[$i-7];
            }
            unset($i);
        }
        
        ob_start();
        include(FRMPRO_VIEWS_PATH.'/displays/calendar.php');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    function calendar_footer($content, $display, $show='one'){
        if($display->frm_show_count != 'calendar' or $show == 'one') return $content;
        
        ob_start();
        include(FRMPRO_VIEWS_PATH.'/displays/calendar-footer.php');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    function get_entry_select(){
        echo FrmEntriesHelper::entries_dropdown($_POST['form_id'], 'entry_id');
        die();
    }
    
    function get_date_field_select(){
        if(is_numeric($_POST['form_id'])){
            echo '<option value="created_at">'. __('Entry creation date', 'formidable') .'</option>';
            echo '<option value="updated_at">'. __('Entry update date', 'formidable') .'</option>';
            FrmProFieldsHelper::get_field_options($_POST['form_id'], '', '', "'date'");
        }

        die();
    }
    
    function get_params(){
        $values = array();
        foreach (array('template' => 0, 'id' => '', 'paged' => 1, 'form' => '', 'search' => '', 'sort' => '', 'sdir' => '') as $var => $default)
            $values[$var] = FrmAppHelper::get_param($var, $default);

        return $values;
    }
    
    /* Shortcodes */
    function get_shortcode($atts){
        global $frmpro_display;
        $defaults = array(
            'id' => '', 'entry_id' => '', 'filter' => false, 
            'user_id' => false, 'limit' => '', 'page_size' => '', 
            'order_by' => '', 'order' => '', 'get' => '', 'get_value' => ''
        );
        
        extract(shortcode_atts($defaults, $atts));
        
        $display = $frmpro_display->getOne($id, false, true, array('check_post' => false));
        
        $user_id = FrmProAppHelper::get_user_id_param($user_id);
        
        if(!empty($get))
            $_GET[$get] = urlencode($get_value);
            
        foreach($defaults as $unset => $val){
            unset($atts[$unset]);
            unset($unset);
            unset($val);
        }
        
        foreach($atts as $att => $val){
            $_GET[$att] = urlencode($val);
            unset($att);
            unset($val);
        }
        
        if ($display)    
            return FrmProDisplaysController::get_display_data($display, '', $entry_id, compact('filter', 'user_id', 'limit', 'page_size', 'order_by', 'order')); 
        else
            return __('That is not a valid custom display ID', 'formidable');
    }
    
    function custom_display($id){
        global $frmpro_display;
        if ($display = $frmpro_display->getOne($id))    
            return $this->get_display_data($display);
    }
    
    function get_display_data($display, $content='', $entry_id=false, $extra_atts=array()){
        global $frmpro_display, $frm_entry, $frmpro_settings, $frm_entry_meta, $frm_forms_loaded, $post;
        
        $frm_forms_loaded[] = true;

        if(!isset($display->frm_form_id))
            $display = FrmProDisplaysHelper::setup_edit_vars($display, false);

        if(!isset($display->frm_form_id))
            return $content;
            
        //for backwards compatability
        $display->id = $display->frm_old_id;
        $display->display_key = $display->post_name;
        
        $defaults = array(
        	'filter' => false, 'user_id' => '', 'limit' => '',
        	'page_size' => '', 'order_by' => '', 'order' => ''
        );

        extract(wp_parse_args( $extra_atts, $defaults ));

        //if (FrmProAppHelper::rewriting_on() && $frmpro_settings->permalinks )
        //    $this->parse_pretty_entry_url();
   
        if ($display->frm_show_count == 'one' and is_numeric($display->frm_entry_id) and $display->frm_entry_id > 0 and !$entry_id)
            $entry_id = $display->frm_entry_id;
        
        $entry = false;

        $show = 'all';
        if (in_array($display->frm_show_count, array('dynamic', 'calendar', 'one'))){
            $one_param = (isset($_GET['entry'])) ? $_GET['entry'] : $entry_id;
            $get_param = (isset($_GET[$display->frm_param])) ? $_GET[$display->frm_param] : (($display->frm_show_count == 'one') ? $one_param : $entry_id);
            unset($one_param);
            
            if ($get_param){
                $where_entry = array('it.form_id' => (int)$display->frm_form_id);
                if(($display->frm_type == 'id' or $display->frm_show_count == 'one') and is_numeric($get_param))
                    $where_entry['it.id'] = $get_param;
                else
                    $where_entry['it.item_key'] = $get_param;
                $entry = $frm_entry->getAll($where_entry, '', 1, 0);
                if($entry)
                    $entry = reset($entry);
                    
                if($entry and $entry->post_id){
                    //redirect to single post page if this entry is a post
                    if(in_the_loop() and $display->frm_show_count != 'one' and !is_single($entry->post_id) and $post->ID != $entry->post_id){
                        $this_post = get_post($entry->post_id);
                        if(in_array($this_post->post_status, array('publish', 'private')))
                            die('<script type="text/javascript">window.location="'. get_permalink($entry->post_id) .'"</script>');
                    }
                }
            }
            unset($get_param);
        }

        if($entry and in_array($display->frm_show_count, array('dynamic', 'calendar'))){    
            $new_content = $display->frm_dyncontent;
            $show = 'one';
        }else{
            $new_content = $display->post_content;
        }
    	
        $show = ($display->frm_show_count == 'one' or ($entry_id and is_numeric($entry_id))) ? 'one' : $show;
        $shortcodes = FrmProDisplaysHelper::get_shortcodes($new_content, $display->frm_form_id); 

        //don't let page size and limit override single entry displays
        if($display->frm_show_count == 'one')
            $display->frm_page_size = $display->frm_limit = '';
            
        //don't keep current content if post type is frm_display
        if($post and $post->post_type == 'frm_display')
            $display->frm_insert_loc = '';
        
        $pagination = '';
            
        if ($entry and $entry->form_id == $display->frm_form_id){
            $display_content = FrmProFieldsHelper::replace_shortcodes($new_content, $entry, $shortcodes, $display, $show);
        }else{
            global $frmdb, $wpdb;
            
            $empty_msg = '<div class="frm_no_entries">'. (isset($display->frm_empty_msg) ? $display->frm_empty_msg : '') .'</div>';
            $display_content = '';
            if($show == 'all')
                $display_content .= isset($display->frm_before_content) ? $display->frm_before_content : '';
                
            $display_content = apply_filters('frm_before_display_content', $display_content, $display, $show);
            
            $where = 'it.form_id='. $display->frm_form_id;
            
            $form_posts = $frmdb->get_records($frmdb->entries, array('form_id' => $display->frm_form_id, 'post_id >' => 1), '', '', 'id,post_id');
            $entry_ids = $frmdb->get_col($frmdb->entries, array('form_id' => $display->frm_form_id), 'id');
            $after_where = false;
            
            if($user_id and !empty($user_id)){
                $user_id = FrmProAppHelper::get_user_id_param($user_id);
                $uid_used = false;
            }
            
            if(isset($display->frm_where) and !empty($display->frm_where)){
                $display->frm_where = apply_filters('frm_custom_where_opt', $display->frm_where, array('display' => $display, 'entry' => $entry));
                $continue = false;
                foreach($display->frm_where as $where_key => $where_opt){
                    $where_val = isset($display->frm_where_val[$where_key]) ? $display->frm_where_val[$where_key] : '';

                    if (preg_match("/\[(get|get-(.?))\b(.*?)(?:(\/))?\]/s", $where_val)){
                        $where_val = FrmProFieldsHelper::get_default_value($where_val, false, true, true);
                        //if this param doesn't exist, then don't include it
                        if($where_val == '') {
                            if(!$after_where)
                                $continue = true;
                
                            continue;
                        }
                    }else{
                        $where_val = FrmProFieldsHelper::get_default_value($where_val, false, true, true);
                    }
                    
                    $continue = false;
                    
                    if($where_val == 'current_user'){
                        if($user_id and is_numeric($user_id)){
                            $where_val = $user_id;
                            $uid_used = true;
                        }else{
                            global $user_ID;
                            $where_val = $user_ID;
                        }
                    }
                    
                    $where_val = do_shortcode($where_val);
                    
                    if(is_array($where_val) and !empty($where_val)){
                        $new_where = '(';
                        if(strpos($display->frm_where_is[$where_key], 'LIKE') !== false){
                            foreach($where_val as $w){
                                if($new_where != '(')
                                    $new_where .= ',';
                                $new_where .= "'%". esc_sql(like_escape($w)). "%'";
                                unset($w);
                            }
                        }else{
                            foreach($where_val as $w){
                                if($new_where != '(')
                                    $new_where .= ',';
                                $new_where .= "'". esc_sql($w) ."'";
                                unset($w);
                            }
                        }
                        $new_where .= ')';
                        $where_val = $new_where;
                        unset($new_where);
                        
                        if(strpos($display->frm_where_is[$where_key], '!') === false and strpos($display->frm_where_is[$where_key], 'not') === false)
                            $display->frm_where_is[$where_key] = ' in ';
                        else
                            $display->frm_where_is[$where_key] = ' not in ';
                    }
                    
                    if(is_numeric($where_opt)){
                        $filter_opts = apply_filters('frm_display_filter_opt', array(
                            'where_opt' => $where_opt, 'where_is' => $display->frm_where_is[$where_key], 
                            'where_val' => $where_val, 'form_id' => $display->frm_form_id, 'form_posts' => $form_posts, 
                            'after_where' => $after_where, 'display' => $display
                        ));
                        $entry_ids = FrmProAppHelper::filter_where($entry_ids, $filter_opts);
                        unset($filter_opts);
                        $after_where = true;
                        $continue = false;
                        if(empty($entry_ids))
                            break;
                    }else if($where_opt == 'created_at' or $where_opt == 'updated_at'){
                        if($where_val == 'NOW')
                            $where_val = current_time('mysql', 1);
                        $where_val = date('Y-m-d H:i:s', strtotime($where_val));
                        $where .= " and it.{$where_opt} ". $display->frm_where_is[$where_key];
                        if(strpos($display->frm_where_is[$where_key], 'in'))
                            $where .= " $where_val";
                        else
                            $where .= " '". esc_sql($where_val) ."'";
                        $continue = true;
                    }else if($where_opt == 'id' or $where_opt == 'item_key'){
                        $where .= " and it.{$where_opt} ". $display->frm_where_is[$where_key];
                        if(strpos($display->frm_where_is[$where_key], 'in'))
                            $where .= " $where_val";
                        else
                            $where .= " '". esc_sql($where_val) ."'";
                        
                        $continue = true;
                    }
                    
                }
                
                if(!$continue and empty($entry_ids)){ 
                    if ($display->frm_insert_loc == 'after'){
                        $content .=  $empty_msg;
                    }else if ($display->frm_insert_loc == 'before'){
                        $content = $empty_msg . $content;
                    }else{
                        if ($filter)
                            $empty_msg = apply_filters('the_content', $empty_msg);
                            
                        if($post->post_type == 'frm_display' and in_the_loop())
                            $content = '';
                            
                        $content .= $empty_msg;
                    }
                    
                    return $content;
                }
            }
            
            if($user_id and is_numeric($user_id) and !$uid_used)
                $where .= " AND it.user_id=". (int)$user_id;

            $s = FrmAppHelper::get_param('frm_search', false);
            if ($s){
                $new_ids = FrmProEntriesHelper::get_search_ids($s, $display->frm_form_id);
                
                if($after_where and isset($entry_ids) and !empty($entry_ids))
                    $entry_ids = array_intersect($new_ids, $entry_ids);
                else
                    $entry_ids = $new_ids;
                    
                if(empty($entry_ids)){
                    if($post->post_type == 'frm_display' and in_the_loop())
                        $content = '';
                        
                    return $content . ' '. $empty_msg;
                }
            }
            
            if(isset($entry_ids) and !empty($entry_ids))
                $where .= ' and it.id in ('.implode(',', $entry_ids).')';
            
            if ($entry_id)
                $where .= " and it.id in ($entry_id)";

            if($show == 'one'){
                $limit = ' LIMIT 1';    
            }else if (isset($_GET['frm_cat']) and isset($_GET['frm_cat_id'])){
                //Get fields with specified field value 'frm_cat' = field key/id, 'frm_cat_id' = order position of selected option
                global $frm_field;
                if ($cat_field = $frm_field->getOne($_GET['frm_cat'])){
                    $categories = maybe_unserialize($cat_field->options);

                    if (isset($categories[$_GET['frm_cat_id']]))
                        $cat_entry_ids = $frm_entry_meta->getEntryIds("meta_value='". $categories[$_GET['frm_cat_id']] ."' and fi.field_key='$_GET[frm_cat]'");
                    if ($cat_entry_ids)
                        $where .= " and it.id in (". implode(',', $cat_entry_ids) .")";
                }
            }
            
            
                if (!empty($limit) and is_numeric($limit))
                    $display->frm_limit = (int)$limit;
                    
                if (is_numeric($display->frm_limit)){
                    $num_limit = (int)$display->frm_limit;
                    $limit = ' LIMIT '. $display->frm_limit;
                }
                
                if (!empty($order_by)){
                    $display->frm_order_by = $order_by;
                    $order_by = '';
                }
                    
                if (!empty($order))
                    $display->frm_order = $order;
                    
                if (isset($display->frm_order_by) && $display->frm_order_by != ''){
                    $order = (isset($display->frm_order)) ? ' '. $display->frm_order : '';
                    if ($display->frm_order_by == 'rand'){
                        $order_by = ' RAND()';
                    }else if (is_numeric($display->frm_order_by)){
                        global $frm_entry_meta, $frm_field;
                        $order_field = $frm_field->getOne($display->frm_order_by);
                        
                        $meta_order = ($order_field->type == 'number') ? ' LENGTH(meta_value),' : '';
                        
                        if(isset($order_field->field_options['post_field']) and $order_field->field_options['post_field']){
                            $posts = $form_posts; //$frmdb->get_records($frmdb->entries, array('form_id' => $display->form_id, 'post_id >' => 1), '', '', 'id, post_id');
                            $linked_posts = array();
                            foreach($posts as $post_meta)
                                $linked_posts[$post_meta->post_id] = $post_meta->id;
                            
                            if($order_field->field_options['post_field'] == 'post_custom'){
                                $ordered_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='". $order_field->field_options['custom_field'] ."' AND post_id in (". implode(',', array_keys($linked_posts)).") ORDER BY meta_value". $order);
                                $metas = array();
                                foreach($ordered_ids as $ordered_id)
                                    $metas[] = array('item_id' => $linked_posts[$ordered_id]);
                                    
                            }else if($order_field->field_options['post_field'] != 'post_category'){
                                $ordered_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE ID in (". implode(',', array_keys($linked_posts)).") ORDER BY ". $order_field->field_options['post_field'] .' '. $order);
                                $metas = array();
                                foreach($ordered_ids as $ordered_id)
                                    $metas[] = array('item_id' => $linked_posts[$ordered_id]);
                            }
                        }else{
                            if($order_field->type == 'number'){
                                $query = "SELECT it.*, meta_value +0 as odr FROM $frmdb->entry_metas it LEFT OUTER JOIN $frmdb->fields fi ON it.field_id=fi.id WHERE fi.form_id=$display->frm_form_id and fi.id={$display->frm_order_by}";
                                if(isset($entry_ids) and !empty($entry_ids))
                                    $query .= " AND it.item_id in (". implode(',', $entry_ids) .")";
                                
                                $query .= " ORDER BY odr $order $limit";
                                
                                $metas = $wpdb->get_results($query);
                            
                            }else{
                                $metas = $frm_entry_meta->getAll('fi.form_id='. (int)$display->frm_form_id .' and fi.id='. $display->frm_order_by, ' ORDER BY '. $meta_order .' meta_value'.$order); //TODO: add previous $where and $limit
                            }
                        }
                    
                        
                        if (isset($metas) and is_array($metas) and !empty($metas)){                            
                            if($order_field->type == 'time' and (!isset($order_field->field_options['clock']) or
                                ($order_field->field_options['clock'] == 12))){
                                
                                $new_order = array();
                                foreach($metas as $key => $meta){
                                    $parts = str_replace(array(' PM',' AM'), '', $meta->meta_value);
                                    $parts = explode(':', $parts);
                                    if(is_array($parts)){
                                        if((preg_match('/PM/', $meta->meta_value) and ((int)$parts[0] != 12)) or 
                                            (((int)$parts[0] == 12) and preg_match('/AM/', $meta->meta_value)))
                                            $parts[0] = ((int)$parts[0] + 12);
                                    }
                                    
                                    $new_order[$key] = (int)$parts[0] . $parts[1];
                                    
                                    unset($key);
                                    unset($meta);
                                }
                                
                                //array with sorted times
                                asort($new_order);
                                
                                $final_order = array();
                                foreach($new_order as $key => $time){
                                    $final_order[] = $metas[$key];
                                    unset($key);
                                    unset($time);
                                }
                                
                                $metas = $final_order;
                                unset($final_order);
                            }
                            
                            $rev_order = ($order == 'DESC' or $order == '') ? ' ASC' : ' DESC';
                            foreach ($metas as $meta){
                                $meta = (array)$meta;
                                $order_by .= 'it.id='.$meta['item_id'] . $rev_order.', ';
                            }
                            $order_by = rtrim($order_by, ', ');  
                        }else{
                            $order = apply_filters('frm_order_display', $order, compact('metas', 'display'));
                            $order_by .= 'it.created_at'. $order;
                        }
                    }else
                        $order_by = 'it.'. $display->frm_order_by . $order;
                    $order_by = ' ORDER BY '. $order_by;
                }
            

            if(!empty($page_size) and is_numeric($page_size))
                $display->frm_page_size = (int)$page_size;

            if (isset($display->frm_page_size) && is_numeric($display->frm_page_size)){
                $current_page = FrmAppHelper::get_param('frm-page', 1);  
                $record_where = ($where == "it.form_id=$display->frm_form_id") ? $display->frm_form_id : $where;
                $record_count = $frm_entry->getRecordCount($record_where);
                if(isset($num_limit) and ($record_count > (int)$num_limit))
                    $record_count = (int)$num_limit;
                
                $page_count = $frm_entry->getPageCount($display->frm_page_size, $record_count);

                $entries = $frm_entry->getPage($current_page, $display->frm_page_size, $where, $order_by);
                $page_last_record = FrmAppHelper::getLastRecordNum($record_count, $current_page, $display->frm_page_size);
                $page_first_record = FrmAppHelper::getFirstRecordNum($record_count, $current_page, $display->frm_page_size);
                if($page_count > 1)
                    $pagination = FrmProDisplaysController::get_pagination_file(FRMPRO_VIEWS_PATH.'/displays/pagination.php', compact('current_page', 'record_count', 'page_count', 'page_last_record', 'page_first_record'));
            }else{
                $entries = $frm_entry->getAll($where, $order_by, $limit, true, false);
            }
            $sc_atts = array();
            if(isset($record_count))
                $sc_atts['record_count'] = $record_count;
            else
                $sc_atts['record_count'] = count($entries);
            
            $filtered_content = apply_filters('frm_display_entries_content', $new_content, $entries, $shortcodes, $display, $show, $sc_atts);
            $total_count = 0;
            if($filtered_content != $new_content){
                $display_content .= $filtered_content;
            }else{
                $odd = 'odd';
                $count = 0;
                if(!empty($entries)){
                    $total_count = count($entries);
                    foreach ($entries as $entry){
                        $count++; //TODO: use the count with conditionals
                        $display_content .= apply_filters('frm_display_entry_content', $new_content, $entry, $shortcodes, $display, $show, $odd, array('count' => $count, 'total_count' => $total_count, 'record_count' => $sc_atts['record_count'], 'pagination' => $pagination));
                        $odd = ($odd == 'odd') ? 'even' : 'odd';
                        unset($entry);
                    }
                    unset($count);
                }else{
                    if($post->post_type == 'frm_display' and in_the_loop())
                        $display_content = '';
                    $display_content .= $empty_msg;
                }
            }
            
            if($show == 'all')
                $display_content .= isset($display->frm_after_content) ? $display->frm_after_content : '';
        }

        if(!isset($total_count))
            $total_count = 0;
        if(!isset($sc_atts))
            $sc_atts = array('record_count' => 0);
        
        $display_content .= apply_filters('frm_after_display_content', $pagination, $display, $show, array('total_count' => $total_count, 'record_count' => $sc_atts['record_count'] ));
        unset($sc_atts);
        $display_content = FrmProFieldsHelper::get_default_value($display_content, false, true, true);

        if ($display->frm_insert_loc == 'after'){
            $content .= $display_content;
        }else if ($display->frm_insert_loc == 'before'){
            $content = $display_content . $content;
        }else{
            if ($filter)
                $display_content = apply_filters('the_content', $display_content);
            $content = $display_content;
        }
            
        return $content;
    }
    
    function parse_pretty_entry_url(){
        global $frm_entry, $wpdb, $post;

        $post_url = get_permalink($post->ID);
        $request_uri = FrmProAppHelper::current_url();
        
        $match_str = '#^'.$post_url.'(.*?)([\?/].*?)?$#';
        
        if(preg_match($match_str, $request_uri, $match_val)){
            // match short slugs (most common)
            if(isset($match_val[1]) and !empty($match_val[1]) and $frm_entry->exists($match_val[1])){
                // Artificially set the GET variable
                $_GET['entry'] = $match_val[1];
            } 
        }
    }
    
    /*
    function route(){
        $action = FrmAppHelper::get_param('frm_action');
            
        if($action =='new')
            return $this->new_form();
        else if($action == 'create')
            return $this->create();
        else if($action == 'edit')
            return $this->edit();
        else if($action == 'update')
            return $this->update();
        else if($action == 'duplicate')
            return $this->duplicate();
        else if($action == 'destroy')
            return $this->destroy();
        else if($action == 'list-form')
            return $this->bulk_actions();     
        else{
            $action = FrmAppHelper::get_param('action');
            if($action == -1)
                $action = FrmAppHelper::get_param('action2');
            
            if(strpos($action, 'bulk_') === 0){
                if(isset($_GET) and isset($_GET['action']))
                    $_SERVER['REQUEST_URI'] = str_replace('&action='.$_GET['action'], '', $_SERVER['REQUEST_URI']);
                if(isset($_GET) and isset($_GET['action2']))
                    $_SERVER['REQUEST_URI'] = str_replace('&action='.$_GET['action2'], '', $_SERVER['REQUEST_URI']);
                    
                return $this->bulk_actions($action);
            }else{
                return $this->display_list();
            }
        }
    } */
    
    function get_pagination_file($filename, $atts){
        extract($atts);
        if (is_file($filename)) {
            ob_start();
            include $filename;
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        return false;
    }

}
