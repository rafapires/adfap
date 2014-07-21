<?php
/*
Template Name: HOME
*/
$meta = get_post_meta( get_the_ID() );
get_header('home');

$pagina_adm = get_page_by_title('Administradora de condomínio');
$pagina_imob = get_page_by_title('Imobiliária');

?>
	<section class="seta_header">
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3">
				<img src="<?php bloginfo('template_url'); ?>/img/logo-web-hi.png" class="img-responsive center-block">
			</div>
		</div>
		<h2 class="text-center">Administramos seu bem como se fosse nosso</h2>

	</section>
	<section id="destaque" class="seta_destaque">
	<div class="container">
		<div class="col-xs-6">
				<a href="<?php echo get_permalink($pagina_adm->ID); ?>">
			<div class="thumbnail">
					<h1 class="text-center">Administradora de condomínios</h1>
					<div class="row">
					<div class="col-xs-5 text-center">
					<img src="<?php bloginfo('template_url'); ?>/img/predio.png" alt="administradora de condominios" class="img-circle">
					</div>
					<div class="col-xs-7">
					<p>Um jeito diferente de administrar seu condomínio com foco na total transparencia e na harmonia social.</p>
					</div>
					</div>
			</div>
				</a>
		</div>
		<div class="col-xs-6">
				<a href="<?php echo get_permalink($pagina_imob->ID); ?>">
			<div class="thumbnail">
					<h1 class="text-center">Imobiliária</h1>
					<div class="row">
					<div class="col-xs-5 text-center">
					<img src="<?php bloginfo('template_url'); ?>/img/casa.png" alt="administradora de condominios" class="img-circle">
					</div>
					<div class="col-xs-7">
					<p>Achamos o imóvel perfeito pra você que procura alugar ou comprar, e pra você que procura vender ou alugar encontramos o melhor cliente com toda a segurança e descomplicação.</p>
					</div>
					</div>
			</div>
				</a>
		</div>
	</section>
	<section class="seta_blog seta_footer" id="blog-home">
		<div class="container">
			<H2 class="text-center">BLOG</H2>
			<div class="row">
				<?php
				$args = array(
						    'numberposts'	=> 8,
						    'offset'		=> 0,
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
							$img_post = get_the_post_thumbnail($post["ID"], "full", array('class'=>'img-responsive'));
						}
						else {
							$img_post = '<img src="http://lorempixel.com/230/150/city/'.$count_img.'" class="img-responsive" alt="random image" />';
						}
					 ?>
					<div class="col-sm-4 col-md-3">
						<div class="thumbnail">
							<a href="<?php echo get_permalink($post["ID"]); ?>" >
								<div class="blog-thumb">
									<?php echo $img_post; ?>
								</div>
								<h2><?php echo esc_attr($post["post_title"]); ?></h2>
								<p class="content clearfix"><?php echo substr(strip_tags($post["post_content"],"<style>"),0,140) ; ?></p>
							</a>
							<div class="row">
								<div class="col-xs-6 link text-center">
									<a href="<?php echo get_permalink($post["ID"]); ?>/#comentarios" >
										<span class="glyphicon glyphicon-comment text-branco"></span>
										<span class="badge cor">
										<?php comments_number( '0', '1', '%' ); ?>
										</span>
									</a>
								</div>
								<div class="col-xs-6 link text-center">
									<a href="<?php echo get_permalink($post["ID"]); ?>" >
										<span class="glyphicon glyphicon-eye-open"></span>
										<strong> Leia</strong>
									</a>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</section>
	</div>
<?php
get_footer();
?>