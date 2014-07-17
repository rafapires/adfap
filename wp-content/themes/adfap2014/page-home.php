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
		<h1 class="text-center">Administramos seu bem como se fosse nosso</h1>

	</section>
	<div id="destaque" class="seta_destaque">
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
	</div>
	</div>
<?php
/* conteúdo dinamico */



?>
	<section class="seta_blog seta_footer" id="blog-home">
		<div class="container">
			<H2 class="text-center">BLOG</H2>
			<div class="row">
				<?php
				$args = array(	'posts_per_page' 	=> '8',
								'post_type'			=> 'post',
								'order'				=> 'ASC'
						);
				query_posts($args);
				while ( have_posts() ) : the_post(); ?>
					<div class="col-sm-4 col-md-3">
						<div class="thumbnail">
							<div class="blog-thumb">
								<a href="<?php the_permalink(); ?>" >
								<?php the_post_thumbnail(); ?>
								<img src="http://revistaimoveis.zap.com.br/imoveis/2010/06/cnt_ext_246359ok.jpg" class="img-responsive" alt="titulo 1">
								</a>
							</div>
							<div class="caption">
								<a href="<?php the_permalink(); ?>" class="text-branco">
									<h2><?php the_title(); ?></h2>
								</a>
								<p class="content clearfix"><?php echo substr(get_the_excerpt(),0,140) ; ?></p>
								<div id="blog-home-foot">
									<div class="blog-home-coment">
										<span class="glyphicon glyphicon-comment text-branco"></span>
										<span class="badge cor">
										<?php comments_number( '0', '1', '%' ); ?>
										</span>
									</div>
									<div class="blog-home-leia">
										<a href="<?php the_permalink(); ?>" class="text-branco"><span class="glyphicon glyphicon-eye-open"></span><strong> Leia</strong></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		</div>
	</section>
<?php
get_footer();
?>