<?php
/*
Template Name: full
*/
$meta = get_post_meta( get_the_ID() );
get_header($meta['page-group'][0]);


?>


<section id="meio" class="col-sm-10 col-sm-offset-2 proposta">
<?php
	if  ( have_posts() ) :
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
	else:
		echo "não achou pagina";
	endif;
?>
</section>

<?php get_footer(); ?>
