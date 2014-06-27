<?php
/**
 * @package Formidable
 */
 
class FrmProDisplaysController{

    public static function load_hooks() {
        add_action('init', 'FrmProDisplaysController::register_post_types', 0);
        add_action('admin_menu', 'FrmProDisplaysController::menu', 13);
        add_filter('admin_head-post.php', 'FrmProDisplaysController::highlight_menu' );
        add_filter('admin_head-post-new.php', 'FrmProDisplaysController::highlight_menu' );
        add_action('restrict_manage_posts', 'FrmProDisplaysController::switch_form_box');
        add_filter('parse_query', 'FrmProDisplaysController::filter_forms' );
        add_filter('views_edit-frm_display', 'FrmProDisplaysController::add_form_nav' );
        add_filter('post_row_actions', 'FrmProDisplaysController::post_row_actions', 10, 2 );
        //add_filter('bulk_actions-edit-frm_display', 'FrmProDisplaysController::add_bulk_actions' );
        
        add_filter('default_content', 'FrmProDisplaysController::default_content', 10, 2 );
    	add_filter('default_title',   'FrmProDisplaysController::default_title', 10, 2 );
    	add_filter('default_excerpt', 'FrmProDisplaysController::default_title', 10, 2 );
        
        add_action('post_submitbox_misc_actions', 'FrmProDisplaysController::submitbox_actions');
        add_action('add_meta_boxes', 'FrmProDisplaysController::add_meta_boxes', 10, 2);
        add_action('save_post', 'FrmProDisplaysController::save_post');
        add_action('before_delete_post', 'FrmProDisplaysController::before_delete_post');
        
        add_filter('the_content', 'FrmProDisplaysController::get_content', 8);
        add_action('wp_ajax_frm_get_cd_tags_box', 'FrmProDisplaysController::get_tags_box');
        add_action('wp_ajax_frm_get_date_field_select', 'FrmProDisplaysController::get_date_field_select' );
		add_action('wp_ajax_frm_add_order_row', 'FrmProDisplaysController::get_order_row');
        add_action('wp_ajax_frm_add_where_row', 'FrmProDisplaysController::get_where_row');
        add_action('wp_ajax_frm_add_where_options', 'FrmProDisplaysController::get_where_options');
        add_filter('frm_before_display_content', 'FrmProDisplaysController::calendar_header', 10, 3);
        add_filter('frm_display_entries_content', 'FrmProDisplaysController::build_calendar', 10, 5);
        add_filter('frm_after_display_content', 'FrmProDisplaysController::calendar_footer', 10, 3);
        add_filter('frm_before_display_content', 'FrmProDisplaysController::filter_after_content', 10, 4);
        add_filter('frm_after_content', 'FrmProDisplaysController::filter_after_content', 10, 4);
        
        //Shortcodes
        add_shortcode('display-frm-data', 'FrmProDisplaysController::get_shortcode', 1);
    }
    
    public static function register_post_types(){
        register_post_type('frm_display', array(
            'label' => __('Views', 'formidable'),
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
                'name' => __('Views', 'formidable'),
                'singular_name' => __('View', 'formidable'),
                'menu_name' => __('View', 'formidable'),
                'edit' => __('Edit'),
                'search_items' => __('Search', 'formidable'),
                'not_found' => __('No Views Found.', 'formidable'),
                'add_new_item' => __('Add New View', 'formidable'),
                'edit_item' => __('Edit View', 'formidable')
            )
        ) );
    }
    
    public static function menu(){
        global $frm_settings;
        
        add_submenu_page('formidable', 'Formidable | '. __('Views', 'formidable'), __('Views', 'formidable'), 'frm_edit_displays', 'edit.php?post_type=frm_display');
        
        add_filter('manage_edit-frm_display_columns', 'FrmProDisplaysController::manage_columns');
        add_filter('manage_edit-frm_display_sortable_columns', 'FrmProDisplaysController::sortable_columns');
        add_filter('get_user_option_manageedit-frm_displaycolumnshidden', 'FrmProDisplaysController::hidden_columns');
        add_action('manage_frm_display_posts_custom_column', 'FrmProDisplaysController::manage_custom_columns', 10, 2);
    }
    
    public static function highlight_menu(){
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
    
    public static function switch_form_box(){
        global $post_type_object;
        if(!$post_type_object or $post_type_object->name != 'frm_display')
            return;
        $form_id = (isset($_GET['form'])) ? $_GET['form'] : '';
        echo FrmFormsHelper::forms_dropdown( 'form', $form_id, __('View all forms', 'formidable'));
    }
    
    public static function filter_forms($query){
        global $pagenow;

        if(!is_admin() or $pagenow != 'edit.php' or !isset($_GET['post_type']) or $_GET['post_type'] != 'frm_display')
            return $query;
            
        if(isset($_REQUEST['form']) and is_numeric($_REQUEST['form'])){
            $query->query_vars['meta_key'] = 'frm_form_id';
            $query->query_vars['meta_value'] = (int)$_REQUEST['form'];
        }
        
        return $query;
    }
    
    public static function add_form_nav($views){
        global $pagenow;
        
        if(!is_admin() or $pagenow != 'edit.php' or !isset($_GET['post_type']) or $_GET['post_type'] != 'frm_display')
            return $views;
          
        $form = (isset($_REQUEST['form']) and is_numeric($_REQUEST['form'])) ? $_REQUEST['form'] : false;
        if($form){ 
			FrmAppController::get_form_nav($form, true);
			echo '<div class="clear"></div>';
		}
        return $views;
		
    }
    
    public static function post_row_actions($actions, $post){
        if($post->post_type == 'frm_display'){
            $actions['duplicate'] = '<a href="'. admin_url('post-new.php?post_type=frm_display&amp;copy_id='. $post->ID) .'" title="'. esc_attr( __( 'Duplicate', 'formidable' ) ) .'">'. __( 'Duplicate', 'formidable' ) .'</a>';
        }
        return $actions;
    }

    public static function create_from_template($path){
        global $frmpro_display;
        $templates = glob($path."/*.php");
        
        for($i = count($templates) - 1; $i >= 0; $i--){
            $filename = str_replace('.php', '', str_replace($path.'/', '', $templates[$i]));
            $display = get_page_by_path($filename, OBJECT, 'frm_display');
            
            $values = FrmProDisplaysHelper::setup_new_vars();
            $values['display_key'] = $filename;
            
            include($templates[$i]);
        }
    }
    
    public static function bulk_actions($action=''){    
        if($bulkaction == 'export'){
            $items = $_REQUEST['item-action'];
            self::export_xml($items);
        }
    }
    
    public static function export_xml(){
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
    	include(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/xml.php');
    	die();
    }
    
    public static function manage_columns($columns){
        unset($columns['title']);
        unset($columns['date']);
        
        $columns['id'] = 'ID';
        $columns['title'] = __('Name');
        $columns['description'] = __('Description');
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
    
    public static function sortable_columns($columns) {
        $columns['name'] = 'name';
        $columns['shortcode'] = 'ID';
        
        //$columns['description'] = 'excerpt';
        //$columns['content'] = 'content';
        
        return $columns;
    }
    
    public static function hidden_columns($result){
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
    
    public static function manage_custom_columns($column_name, $id){
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
			    $frm_form = new FrmForm();
			    $form_id = get_post_meta($id, 'frm_'. $column_name, true);
			    $form = $frm_form->getName($form_id);
			    unset($frm_form);
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
			    
			    $val = '<input type="text" readonly="true" class="frm_select_box" value="'. esc_attr($code) .'" />';
		        break;
			default:
			    $val = $column_name;
			break;
		}
		
        echo $val;
    }
    
    public static function submitbox_actions(){
        global $post;
        if($post->post_type != 'frm_display')
            return;
        
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/submitbox_actions.php');
    }
    
    public static function default_content($content, $post){
        if($post->post_type == 'frm_display' and isset($_GET) and isset($_GET['copy_id'])){
            global $frmpro_display, $copy_display;
            $copy_display = $frmpro_display->getOne($_GET['copy_id']);
            if($copy_display)
                $content = $copy_display->post_content;
        }
        return $content;
    }
    
    public static function default_title($title, $post){
        if($post->post_type == 'frm_display' and isset($_GET) and isset($_GET['copy_id'])){
            global $copy_display;
            if($copy_display)
                $title = $copy_display->post_title;
        }
        return $title;
    }
    
    public static function default_excerpt($excerpt, $post){
        if($post->post_type == 'frm_display' and isset($_GET) and isset($_GET['copy_id'])){
            global $copy_display;
            if($copy_display)
                $excerpt = $copy_display->post_excerpt;
        }
        return $excerpt;
    }
    
    public static function add_meta_boxes($post_type, $post=false){
        if($post_type != 'frm_display')
            return;
            
        add_meta_box('frm_form_disp_type', __('Basic Settings', 'formidable'), 'FrmProDisplaysController::mb_form_disp_type', 'frm_display', 'normal', 'high');
        add_meta_box('frm_dyncontent', __('Content', 'formidable'), 'FrmProDisplaysController::mb_dyncontent', 'frm_display', 'normal', 'high');
        add_meta_box('frm_excerpt', __('Description'), 'FrmProDisplaysController::mb_excerpt', 'frm_display', 'normal', 'high');
        add_meta_box('frm_advanced', __('Advanced Settings', 'formidable'), 'FrmProDisplaysController::mb_advanced', 'frm_display', 'advanced');
        
        
        add_meta_box('frm_adv_info', __('Customization', 'formidable'), 'FrmProDisplaysController::mb_adv_info', 'frm_display', 'side', 'low');
    }
    
    public static function save_post($post_id){
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
    
    public static function before_delete_post($post_id){
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
    public static function mb_dyncontent($post){
        global $copy_display;
        if($copy_display and isset($_GET) and isset($_GET['copy_id']))
            $post = $copy_display;
        
        $post = FrmProDisplaysHelper::setup_edit_vars($post);
        $editor_args = array();
        if ( $post->frm_no_rt ){
            $editor_args['teeny'] = true;
            $editor_args['tinymce'] = false;
        }
        
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/mb_dyncontent.php');
    }
    
    public static function mb_excerpt($post){
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/mb_excerpt.php');
        
        //add form nav via javascript
        $form = get_post_meta($post->ID, 'frm_form_id', true);
        if($form){
            echo '<div id="frm_nav_container" style="display:none;">';
            FrmAppController::get_form_nav($form, true);
			echo '<div class="clear"></div>';
            echo '</div>';
            echo '<script type="text/javascript">jQuery(document).ready(function($){ $(".wrap h2:first").after( $("#frm_nav_container").show());})</script>'; 
        }
    }
    
    public static function mb_form_disp_type($post){
        global $frmpro_settings, $copy_display;
        if($copy_display and isset($_GET) and isset($_GET['copy_id']))
            $post = $copy_display;
            
        $post = FrmProDisplaysHelper::setup_edit_vars($post);
        
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/mb_form_disp_type.php');
    }
    
    public static function mb_advanced($post){
        global $copy_display;
        if($copy_display and isset($_GET) and isset($_GET['copy_id']))
            $post = $copy_display;
            
        $post = FrmProDisplaysHelper::setup_edit_vars($post);

        include(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/mb_advanced.php');
    }
    
    public static function mb_adv_info($post){
        global $copy_display;
        if($copy_display and isset($_GET) and isset($_GET['copy_id']))
            $post = $copy_display;
            
        $post = FrmProDisplaysHelper::setup_edit_vars($post);
        self::mb_tags_box($post->frm_form_id);
    }
    
    public static function mb_tags_box($form_id){
        global $frm_field;
        
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
            'decimal=2 dec_point=&#34.&#34 thousands_sep=&#34,&#34' => __('# Format', 'formidable'),
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
        
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/shared/mb_adv_info.php');
    }
    
    public static function get_tags_box(){
        self::mb_tags_box($_POST['form_id']);
        die();
    }
    
    /* FRONT END */
    
    public static function get_content($content){
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
            return self::get_display_data($post, $content, false, array('filter' => true)); 
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
                    $entry = $wpdb->get_row($wpdb->prepare("SELECT id, item_key FROM {$wpdb->prefix}frm_items WHERE post_id=%d", $post->ID));
                    if(!$entry)
                        return $content;

                    $entry_id = $entry->id;

                    if(in_array($display->frm_show_count, array('dynamic', 'calendar')) and $display->frm_type == 'display_key')
                        $entry_id = $entry->item_key;
                }
                    
                
                $frm_displayed[] = $display->ID; 
                $content = self::get_display_data($display, $content, $entry_id, array('filter' => true)); 
            }   
        }

        return $content;
    }
    
	public static function get_order_row(){
        self::add_order_row($_POST['order_key'], $_POST['form_id']);
        die();
    }
	
    public static function add_order_row($order_key='', $form_id='', $order_by='', $order=''){
        $order_key = (int)$order_key;
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/order_row.php');
    }
    
    public static function get_where_row(){
        self::add_where_row($_POST['where_key'], $_POST['form_id']);
        die();
    }
    
    public static function add_where_row($where_key='', $form_id='', $where_field='', $where_is='', $where_val=''){
        $where_key = (int)$where_key;
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/where_row.php');
    }
    
    public static function get_where_options(){
        self::add_where_options($_POST['field_id'],$_POST['where_key']);
        die();
    }
    
    public static function add_where_options($field_id, $where_key, $where_val=''){
        global $frm_field;
        if ( is_numeric($field_id) ) {
            $field = $frm_field->getOne($field_id);
        }
        
        require(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/where_options.php');
    }
    
    public static function calendar_header($content, $display, $show = 'one'){
        if ( $display->frm_show_count != 'calendar' || $show == 'one' ) {
            return $content;
        }
        
        global $frm_vars, $wp_locale;
        $frm_vars['load_css'] = true;
        
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
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/calendar-header.php');
        $content .= ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    public static function build_calendar($new_content, $entries, $shortcodes, $display, $show = 'one'){
        if ( ! $display || $display->frm_show_count != 'calendar' || $show == 'one') {
            return $new_content;
        }
        
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
        
        $daily_entries = array();
        
        if ( isset($display->frm_date_field_id) && is_numeric($display->frm_date_field_id) ) {
            $field = $frm_field->getOne($display->frm_date_field_id);
        }
            
        if ( isset($display->frm_edate_field_id) && is_numeric($display->frm_edate_field_id) ) {
            $efield = $frm_field->getOne($display->frm_edate_field_id);
        } else {
            $efield = false;
        }
        
        foreach ($entries as $entry){
            if ( isset($display->frm_date_field_id) && is_numeric($display->frm_date_field_id) ) {
                if ( isset($entry->metas) ) {
                    $date = isset($entry->metas[$display->frm_date_field_id]) ? $entry->metas[$display->frm_date_field_id] : false;
                } else {
                    $date = $frm_entry_meta->get_entry_meta_by_field($entry->id, $display->frm_date_field_id);
                }
                
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
            
            if ( isset($i18n) && $i18n ) {
                $date = get_date_from_gmt($date);
                $date = date_i18n('Y-m-d', strtotime($date));
            } else {
                $date = date('Y-m-d', strtotime($date));
            }
                
            unset($i18n);
            $dates = array($date);
            
            if ( isset($display->frm_edate_field_id) && !empty($display->frm_edate_field_id) ) {
                if(is_numeric($display->frm_edate_field_id) and $efield){
                    $edate = FrmProEntryMetaHelper::get_post_or_meta_value($entry, $efield);
                    
                    if ( $efield && $efield->type == 'number' && is_numeric($edate) ) {
                        $edate = date('Y-m-d', strtotime('+'. ($edate - 1) .' days', strtotime($date)));
                    }
                }else if($display->frm_edate_field_id == 'updated_at'){
                    $edate = get_date_from_gmt($entry->updated_at);
                    $edate = date_i18n('Y-m-d', strtotime($edate));
                }else{
                    $edate = get_date_from_gmt($entry->created_at);
                    $edate = date_i18n('Y-m-d', strtotime($edate));
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
			
			//Recurring events
			if ( isset($display->frm_repeat_event_field_id) && is_numeric($display->frm_repeat_event_field_id) ) {
                if ( isset($entry->metas) ) {//When is $entry->metas not set? Is it when posts are created?
                    $repeat_period = isset($entry->metas[$display->frm_repeat_event_field_id]) ? $entry->metas[$display->frm_repeat_event_field_id] : false;
					$stop_repeat = isset($entry->metas[$display->frm_repeat_edate_field_id]) ? $entry->metas[$display->frm_repeat_edate_field_id] : false;
                } else { //Test this else section
					$repeat_period = $frm_entry_meta->get_entry_meta_by_field($entry->id, $display->frm_repeat_event_field_id);
					$stop_repeat = $frm_entry_meta->get_entry_meta_by_field($entry->id, $display->frm_repeat_edate_field_id);
                }
				
				//If site is not set to English, convert day(s), week(s), month(s), and year(s) (in repeat_period string) to English
				//Check for a few common repeat periods like daily, weekly, monthly, and yearly as well
				$t_strings = array(__('day', 'formidable'), __('days', 'formidable'), __('daily', 'formidable'),__('week', 'formidable'), __('weeks', 'formidable'), __('weekly', 'formidable'), __('month', 'formidable'), __('months', 'formidable'), __('monthly', 'formidable'), __('year', 'formidable'), __('years', 'formidable'), __('yearly', 'formidable'));
				$t_strings = apply_filters('frm_recurring_strings', $t_strings, $display);
				$e_strings = array('day', 'days', '1 day', 'week', 'weeks', '1 week', 'month', 'months', '1 month', 'year', 'years', '1 year');
				if ( $t_strings != $e_strings ) {
					$repeat_period = str_ireplace($t_strings, $e_strings, $repeat_period);
				}
				unset($t_strings,$e_strings);
				
				//Filter for repeat_period
				$repeat_period = apply_filters('frm_repeat_period', $repeat_period, $display);
				
				//If repeat period is set and is valid
				if ( !empty($repeat_period) && is_numeric(strtotime($repeat_period)) ){
					
					//Set up start date to minimize dates array - is this necessary? What is the best way to do this?
					//Switch start date to same date of current year OR if repeat_period is weekly, start date should be same day of the week, current year. What is start date if +3 days (or something like that)
					
					//Set up end date to minimize dates array - allow for no end repeat field set, nothing selected for end, or any date
					if ( isset($display->frm_repeat_edate_field_id) && !empty($stop_repeat) ) {//If field is selected for recurring end date and the date is not empty
						$stop_repeat = ( !empty($stop_repeat)? strtotime($stop_repeat) : strtotime($repeat_forever));
					} else {
						if ( isset($_GET['frmcal-month']) && isset($_GET['frmcal-year']) ) {
							$cal_date = strtotime($_GET['frmcal-year']. '-' . $_GET['frmcal-month'] . '-01');
							$stop_repeat = strtotime('+1 month', $cal_date);//Repeat until next viewable month
						} else {
							$stop_repeat = strtotime('+1 month');//Repeat until next viewable month
						}
					}
					$temp_dates = array();
					
					foreach ( $dates as $d ) {
						for ($i = strtotime($d); $i <= $stop_repeat; $i = strtotime($repeat_period, $i)) {
							$temp_dates[] = date('Y-m-d', $i);
						}
						unset($d);
					}
					$dates = $temp_dates;
					unset($repeat_period, $to_date, $start_date, $stop_repeat, $temp_dates);
				}
			}
            
            $dates = apply_filters('frm_show_entry_dates', $dates, $entry);
            
            for ($i=0; $i<($maxday+$startday); $i++){
                $day = $i - $startday + 1;

                if(in_array(date('Y-m-d', strtotime("$year-$month-$day")), $dates)){
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
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/calendar.php');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    public static function calendar_footer($content, $display, $show='one'){
        if($display->frm_show_count != 'calendar' or $show == 'one') return $content;
        
        ob_start();
        include(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/calendar-footer.php');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    public static function get_date_field_select(){
		if ( is_numeric($_POST['form_id']) ) {
		    $post = new stdClass();
		    $post->frm_form_id = (int) $_POST['form_id'];
		    $post->frm_edate_field_id = $post->frm_date_field_id = '';
		    $post->frm_repeat_event_field_id = $post->frm_repeat_edate_field_id = '';
		    include(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/_calendar_options.php');
		}

        die();
    }
    
    public static function get_params(){
        $values = array();
        foreach (array('template' => 0, 'id' => '', 'paged' => 1, 'form' => '', 'search' => '', 'sort' => '', 'sdir' => '') as $var => $default)
            $values[$var] = FrmAppHelper::get_param($var, $default);

        return $values;
    }
    
    /* Shortcodes */
    public static function get_shortcode($atts){
        global $frmpro_display;
        $defaults = array(
            'id' => '', 'entry_id' => '', 'filter' => false, 
            'user_id' => false, 'limit' => '', 'page_size' => '', 
            'order_by' => '', 'order' => '', 'get' => '', 'get_value' => '',
            'drafts' => false,
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
            return FrmProDisplaysController::get_display_data($display, '', $entry_id, compact('filter', 'user_id', 'limit', 'page_size', 'order_by', 'order', 'drafts')); 
        else
            return __('There are no views with that ID', 'formidable');
    }
    
    public static function custom_display($id){
        global $frmpro_display;
        if ($display = $frmpro_display->getOne($id))    
            return self::get_display_data($display);
    }
    
    public static function get_display_data($display, $content='', $entry_id=false, $extra_atts=array()){
        global $frmpro_display, $frm_entry, $frmpro_settings, $frm_entry_meta, $frm_vars, $post;
        
        $frm_vars['forms_loaded'][] = true;

        if(!isset($display->frm_form_id))
            $display = FrmProDisplaysHelper::setup_edit_vars($display, false);

        if(!isset($display->frm_form_id) or empty($display->frm_form_id))
            return $content;
        
        // check if entry needs to be deleted before loading entries
        if ( FrmAppHelper::get_param('frm_action') == 'destroy' && isset( $_GET['entry'] ) ) {
            $message = '<div class="with_frm_style"><div class="frm_message">'. FrmProEntriesController::ajax_destroy( $display->frm_form_id, false, false ) .'</div></div>';
            unset( $_GET['entry'] );
        }
            
        //for backwards compatability
        $display->id = $display->frm_old_id;
        $display->display_key = $display->post_name;
        
        $defaults = array(
        	'filter' => false, 'user_id' => '', 'limit' => '',
        	'page_size' => '', 'order_by' => '', 'order' => ''
        );

        extract(wp_parse_args( $extra_atts, $defaults ));

        //if (FrmProAppHelper::rewriting_on() && $frmpro_settings->permalinks )
        //    self::parse_pretty_entry_url();
   
        if ($display->frm_show_count == 'one' and is_numeric($display->frm_entry_id) and $display->frm_entry_id > 0 and !$entry_id)
            $entry_id = $display->frm_entry_id;
        
        $entry = false;

        $show = 'all';
        
        global $wpdb, $frmpro_entry;
        
        $where = $wpdb->prepare('it.form_id=%d', $display->frm_form_id);
        
        if (in_array($display->frm_show_count, array('dynamic', 'calendar', 'one'))){
            $one_param = (isset($_GET['entry'])) ? $_GET['entry'] : $entry_id;
            $get_param = (isset($_GET[$display->frm_param])) ? $_GET[$display->frm_param] : (($display->frm_show_count == 'one') ? $one_param : $entry_id);
            unset($one_param);
            
            if ($get_param){
                if(($display->frm_type == 'id' or $display->frm_show_count == 'one') and is_numeric($get_param))
                    $where .= $wpdb->prepare(' AND it.id=%d', $get_param);
                else
                    $where .= $wpdb->prepare(' AND it.item_key=%s', $get_param);
                
                $entry = $frm_entry->getAll($where, '', 1, 0);
                if($entry)
                    $entry = reset($entry);
                    
                if($entry and $entry->post_id){
                    //redirect to single post page if this entry is a post
                    if(in_the_loop() and $display->frm_show_count != 'one' and !is_single($entry->post_id) and $post->ID != $entry->post_id){
                        $this_post = get_post($entry->post_id);
                        if(in_array($this_post->post_status, array('publish', 'private')))
                            die(FrmAppHelper::js_redirect(get_permalink($entry->post_id)));
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
        $is_draft = ( isset($drafts) && !empty($drafts) ) ? 1 : 0;
        
        if ($entry and $entry->form_id == $display->frm_form_id){
            $form_posts = $wpdb->get_results($wpdb->prepare("SELECT id, post_id FROM {$wpdb->prefix}frm_items WHERE form_id=%d and post_id>%d AND is_draft=%d AND id=%d", $display->frm_form_id, 1, $is_draft, $entry->id));
            $entry_ids = array($entry->id);
        }else{
            $form_posts = $wpdb->get_results($wpdb->prepare("SELECT id, post_id FROM {$wpdb->prefix}frm_items WHERE form_id=%d and post_id>%d AND is_draft=%d", $display->frm_form_id, 1, $is_draft));
            $entry_ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM {$wpdb->prefix}frm_items WHERE form_id=%d and is_draft=%d", $display->frm_form_id, $is_draft));
        }
        
		$empty_msg = (isset($display->frm_empty_msg) and !empty($display->frm_empty_msg)) ? '<div class="frm_no_entries">' . FrmProFieldsHelper::get_default_value($display->frm_empty_msg, false, true, true) . '</div>' : '';
        
        if ( isset( $message ) ) {
            // if an entry was deleted above, show a message
            $empty_msg = $message . $empty_msg;
        }
            
        $after_where = false;
            
        if($user_id and !empty($user_id)){
            $user_id = FrmProAppHelper::get_user_id_param($user_id);
            $uid_used = false;
        }
		
		if ( isset( $display->frm_where ) && !empty( $display->frm_where ) && (!$entry || !$post || $post->ID != $entry->post_id ) ) { 
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
                            $where_val = get_current_user_id();
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
                            'after_where' => $after_where, 'display' => $display, 'drafts' => $is_draft
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
                        
                        if(strpos($display->frm_where_is[$where_key], 'LIKE') === false)
                            $where_val = date('Y-m-d H:i:s', strtotime($where_val));
                        
                        $where .= " and it.{$where_opt} ". $display->frm_where_is[$where_key];
                        if(strpos($display->frm_where_is[$where_key], 'in'))
                            $where .= " $where_val";
                        else if(strpos($display->frm_where_is[$where_key], 'LIKE') !== false)
                            $where .= " '%". esc_sql(like_escape($where_val)) ."%'";
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
            
            if ( $entry_id ) {
                $where .= $wpdb->prepare( is_numeric($entry_id) ? " and it.id=%d" : " and it.item_key=%s", $entry_id);
            }

            $where .= $wpdb->prepare(" and is_draft=%d", $is_draft);
            
            if($show == 'one'){
                $limit = ' LIMIT 1';    
            }else if (isset($_GET['frm_cat']) and isset($_GET['frm_cat_id'])){
                //Get fields with specified field value 'frm_cat' = field key/id, 'frm_cat_id' = order position of selected option
                global $frm_field;
                if ($cat_field = $frm_field->getOne($_GET['frm_cat'])){
                    $categories = maybe_unserialize($cat_field->options);

                    if (isset($categories[$_GET['frm_cat_id']])){
                        $cat_entry_ids = $frm_entry_meta->getEntryIds(array('meta_value' => $categories[$_GET['frm_cat_id']], 'fi.field_key' => $_GET['frm_cat']));
                        if ($cat_entry_ids)
                            $where .= " and it.id in (". implode(',', $cat_entry_ids) .")";
                        else
                            $where .= " and it.id=0";
                    }
                }
            }           
            
			if (!empty($limit) and is_numeric($limit))
               $display->frm_limit = (int)$limit;
               
			if (is_numeric($display->frm_limit)){
               $num_limit = (int)$display->frm_limit;
               $limit = ' LIMIT '. $display->frm_limit;
			}
           
			if (!empty($order_by)){
            	$display->frm_order_by = explode(',', $order_by);
            	$order_by = '';
			}

            if (!empty($order)){
                $display->frm_order = explode(',', $order);
			}
			unset($order);
			

            if ( !empty($page_size) && is_numeric($page_size) ) {
                $display->frm_page_size = (int)$page_size;
            }
            
            // if limit is lower than page size, ignore the page size
            if ( isset($num_limit) && $display->frm_page_size > $num_limit ) {
                $display->frm_page_size = '';
            }
            
            if (isset($display->frm_page_size) and is_numeric($display->frm_page_size)){
                $page_param = ($_GET and isset($_GET['frm-page-'. $display->ID])) ? 'frm-page-'. $display->ID : 'frm-page';
                $current_page = (int)FrmAppHelper::get_param($page_param, 1);  
                $record_where = ($where == $wpdb->prepare('it.form_id=%d', $display->frm_form_id)) ? $display->frm_form_id : $where;
                $record_count = $frm_entry->getRecordCount($record_where);
                if(isset($num_limit) and ($record_count > (int)$num_limit))
                    $record_count = (int)$num_limit;
                
                $page_count = $frm_entry->getPageCount($display->frm_page_size, $record_count);
				
				//Get a page of entries
				$entries = $frmpro_entry->get_view_page($current_page, $display->frm_page_size, $where, array(
					'order_by_array' => $display->frm_order_by, 'order_array' => $display->frm_order, 'posts' => $form_posts
				));
                
                $page_last_record = FrmAppHelper::getLastRecordNum($record_count, $current_page, $display->frm_page_size);
                $page_first_record = FrmAppHelper::getFirstRecordNum($record_count, $current_page, $display->frm_page_size);
                if($page_count > 1){
                    $page_param = 'frm-page-'. $display->ID;
                    $pagination = FrmProDisplaysController::get_pagination_file(FrmAppHelper::plugin_path() .'/pro/classes/views/displays/pagination.php', compact('current_page', 'record_count', 'page_count', 'page_last_record', 'page_first_record', 'page_param'));
                }
            }else{
				//Get all entries
				$entries = $frmpro_entry->get_view_results($where, array(
					'order_by_array' => $display->frm_order_by, 'order_array' => $display->frm_order, 'limit' => $limit, 'posts' => $form_posts
				));
            }
            
            $total_count = count($entries);
            $sc_atts = array();
            if(isset($record_count))
                $sc_atts['record_count'] = $record_count;
            else
                $sc_atts['record_count'] = $total_count;
                
            $display_content = '';
            if ( isset( $message ) ) {
                // if an entry was deleted above, show a message
                $display_content .= $message;
            }
            
            if($show == 'all')
                $display_content .= isset($display->frm_before_content) ? $display->frm_before_content : '';
            
            if ( !isset($entry_ids) || empty($entry_ids) ) {
                $entry_ids = array_keys($entries);
            }
            
            $display_content = apply_filters('frm_before_display_content', $display_content, $display, $show, array('total_count' => $total_count, 'record_count' => $sc_atts['record_count'], 'entry_ids' => $entry_ids));
            
            $filtered_content = apply_filters('frm_display_entries_content', $new_content, $entries, $shortcodes, $display, $show, $sc_atts);
            
            if($filtered_content != $new_content){
                $display_content .= $filtered_content;
            }else{
                $odd = 'odd';
                $count = 0;
                if(!empty($entries)){
                    foreach ($entries as $entry){
                        $count++; //TODO: use the count with conditionals
                        $display_content .= apply_filters('frm_display_entry_content', $new_content, $entry, $shortcodes, $display, $show, $odd, array('count' => $count, 'total_count' => $total_count, 'record_count' => $sc_atts['record_count'], 'pagination' => $pagination, 'entry_ids' => $entry_ids));
                        $odd = ($odd == 'odd') ? 'even' : 'odd';
                        unset($entry);
                    }
                    unset($count);
                }else{
                    if($post->post_type == 'frm_display' and in_the_loop())
                        $display_content = '';
                    
                    if ( !isset($message) || FrmAppHelper::get_param('frm_action') != 'destroy' ) {
                        $display_content .= $empty_msg;
                    }
                }
            }
        
        if ( isset( $message ) ) {
            unset( $message );
        }
            
        if($show == 'all')
            $display_content .= isset($display->frm_after_content) ? apply_filters('frm_after_content', $display->frm_after_content, $display, $show, array('total_count' => $total_count, 'record_count' => $sc_atts['record_count'], 'entry_ids' => $entry_ids)) : '';
        
        if(!isset($sc_atts))
            $sc_atts = array('record_count' => 0);
        
        if(!isset($total_count))
            $total_count = 0;
        
        $display_content .= apply_filters('frm_after_display_content', $pagination, $display, $show, array('total_count' => $total_count, 'record_count' => $sc_atts['record_count'], 'entry_ids' => $entry_ids ));
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
    
    public static function parse_pretty_entry_url(){
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
    
    public static function get_pagination_file($filename, $atts){
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

    public static function filter_after_content($content, $display, $show, $atts){
        $content = str_replace('[entry_count]', $atts['record_count'], $content);
        return $content;
    }
}
