<?php 
remove_filter( 'the_content', 'wpautop' );
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 150, 150, true ); 
add_filter('show_admin_bar', '__return_false');

register_nav_menus( array (
		'main-menu' => 'Menu Principal',
		'foot-menu'	=> 'Footer Menu',
		'blog-menu' => 'Blog Menu'
		) );




function wpbootstrap_scripts_with_jquery()
{
	// Register the script like this for a theme:
	wp_register_script( 'custom-script', get_template_directory_uri() . '/js/bootstrap.js', array( 'jquery' ) );
	// For either a plugin or a theme, you can then enqueue the script:
	wp_enqueue_script( 'custom-script' );
}
add_action( 'wp_enqueue_scripts', 'wpbootstrap_scripts_with_jquery' );


?>
