<?php 
remove_filter( 'the_content', 'wpautop' );
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 150, 150, true ); 
add_filter('show_admin_bar', '__return_false');

register_nav_menus( array (
		'main-menu' => 'Menu Principal',
		'foot-menu'	=> 'Footer Menu',
		'blog-menu' => 'Blog Menu',
		'page-adm'	=> 'Administradora de Condomínios',
		'page-imob'	=>	'Imobiliária'
		) );




function wpbootstrap_scripts_with_jquery()
{
	// Register the script like this for a theme:
	wp_register_script( 'custom-script', get_template_directory_uri() . '/js/bootstrap.js', array( 'jquery' ) );
	// For either a plugin or a theme, you can then enqueue the script:
	wp_enqueue_script( 'custom-script' );
}
add_action( 'wp_enqueue_scripts', 'wpbootstrap_scripts_with_jquery' );

// ######## FORMIDABLE CUSTOMIZATIONS #############
add_filter('frm_validate_field_entry', 'your_custom_validation', 20, 3);
 function your_custom_validation($errors, $field, $value){
   if ($field->id == 157){ //change 157 to the ID of the confirmation field (second field)
    $first_value = $_POST['item_meta'][156]; //change 156 to the ID of the first field
   
    if ( $first_value != $value && !empty($value) ) {
      $errors['field'. $field->id] = 'Este email não confere com o digitado.';//Customize your error message
    }else{
      $_POST['item_meta'][$field->id] = ''; //if it matches, this clears the second field so it won't be saved
    }
 }
 return $errors;
 }
?>
