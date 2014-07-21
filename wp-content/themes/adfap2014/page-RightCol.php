<?php
/*
Template Name: RightCol
*/
$meta = get_post_meta( get_the_ID() );
get_header($meta['page-group'][0]);
?>

<section id="meio" class="col-sm-7 col-sm-offset-2">
<?php
	if  ( have_posts() ) :
		while ( have_posts() ) : the_post();
			the_title('<h1>','</h1>');
			the_content();
		endwhile;
	else:
		echo "não achou pagina";
	endif;
?>

</section>
<section id='direita' class='col-sm-3'>
	<h3 class="sub-titulo">Blogs</h3>
	<ul class="nav nav-pills nav-stacked">
<?php
	$args = array(
	    'numberposts'	=> 4,
	    'offset'		=> 0,
	    'category' 		=> 3,
	    'orderby' 		=> 'post_date',
	    'order' 		=> 'DESC',
	    'post_type' => 'post',
	    'post_status' => 'publish',
	    'suppress_filters' => true );
	$recent_posts = wp_get_recent_posts ($args, ARRAY_A);

	$count_img = rand(1,9);

	foreach ($recent_posts as $post) {
		//verifica sé há thumbnail
		if ( $count_img >= 9 ) {
			$count_img = 1;
		}else{
			$count_img++;
		}
		if ( has_post_thumbnail($post["ID"]) ) {
			$img_post = get_the_post_thumbnail($post["ID"], 'thumbnail' , array('class' => 'thumbnail pull-left'));
		}
		else {
			$img_post = '<img src="http://lorempixel.com/50/50/city/'.$count_img.'" class="thumbnail pull-left wp-post-image" alt="random image" />';
		}
	?>
		<li>
			<a href="<?php echo get_permalink($post["ID"]); ?>">
				<?php echo $img_post; ?>
				<h3><?php echo esc_attr($post["post_title"]); ?></h3>
			</a>
		</li>
		<?php		
	}

	?>
	</ul>
</section>




<? get_footer(); ?>
