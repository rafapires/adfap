<?php
/*
Template Name: proposta
*/
get_header();
?>

<section id="esquerda" class="col-xp-12 col-sm-2">
<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
	<a class="logo center-block" href="#">
  		<img src="<?php bloginfo('template_url'); ?>/img/logo-web-hi.png" class="img-responsive center-block">
		<h1>Administradora de Condomínio</h1>
	</a>
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav nav-pills nav-stacked">
			<li><a hreh='#'>Administração</a></li>
			<li><a href="#">Diferencial</a></li>
			<li><a href="#">Faça um simulação</a></li>
			<li class="active"><a href="#">Solicite um proposta</a></li>
		</ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>



</section>
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

<? get_footer(); ?>
