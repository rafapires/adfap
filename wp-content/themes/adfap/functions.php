<?php 
// add_action ( 'init','sa_create_custom_posts' );
add_theme_support( 'post-thumbnails' ); 
register_nav_menu( 'main-menu', 'Menu Principal' );
remove_filter( 'the_content', 'wpautop' );


function wpbootstrap_scripts_with_jquery()
{
	// Register the script like this for a theme:
	wp_register_script( 'custom-script', get_template_directory_uri() . '/js/bootstrap.js', array( 'jquery' ) );
	// For either a plugin or a theme, you can then enqueue the script:
	wp_enqueue_script( 'custom-script' );
}
add_action( 'wp_enqueue_scripts', 'wpbootstrap_scripts_with_jquery' );

class twitter_bootstrap_nav_walker extends Walker_Nav_Menu {

	function start_lvl( &$output, $depth ) {
		$indent = str_repeat( "\t", $depth );
		$output	.= "\n$indent<ul class=\"dropdown-menu\">\n";
	}

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		if (strcasecmp($item->title, 'divider')) {
			$class_names = $value = '';
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = ($item->current) ? 'active' : '';
			$classes[] = 'menu-item-' . $item->ID;
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

			if ($args->has_children && $depth > 0) {
				$class_names .= ' dropdown-submenu';
			} else if($args->has_children && $depth === 0) {
				$class_names .= ' dropdown';
			}

			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $value . $class_names .'>';

			$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
			$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
			$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';
			$attributes .= ($args->has_children) ? ' data-toggle="dropdown" data-target="#" class="dropdown-toggle"' : '';

			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'>';
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= ($args->has_children && $depth == 0) ? ' <span class="caret"></span></a>' : '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		} else {
			$output .= $indent . '<li class="divider">';
		}
	}

	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

	if ( !$element ) {
	return;
	}

	$id_field = $this->db_fields['id'];

	if ( is_array( $args[0] ) )
	$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
	else if ( is_object( $args[0] ) )
	$args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
	$cb_args = array_merge( array(&$output, $element, $depth), $args);
	call_user_func_array(array(&$this, 'start_el'), $cb_args);

	$id = $element->$id_field;

	if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

	foreach( $children_elements[ $id ] as $child ){

	if ( !isset($newlevel) ) {
	$newlevel = true;

	$cb_args = array_merge( array(&$output, $depth), $args);
	call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
	}
	$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
	}
	unset( $children_elements[ $id ] );
	}

	if ( isset($newlevel) && $newlevel ){

	$cb_args = array_merge( array(&$output, $depth), $args);
	call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
	}
	$cb_args = array_merge( array(&$output, $element, $depth), $args);
	call_user_func_array(array(&$this, 'end_el'), $cb_args);
	}
}

?>