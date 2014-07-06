<?php
/*
Template Name: ADM
*/
get_header();
?>

<section id="esquerda" class="col-xp-12 col-sm-2">
<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
	<a class="logo center-block" href="#">
  		<img src="<?php bloginfo('template_url'); ?>/img/logo-web-hi.png" class="img-responsive center-block">
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
			<li class="active"><a hreh='#'>home</a></li>
			<li><a href="#">item 1</a></li>
			<li><a href="#">item 2</a></li>
			<li><a href="#">item 3</a></li>
		</ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>



</section>
<section id="meio" class="col-sm-7">
meio
</section>
<section id='direita' class='col-sm-3'>
direita
</section>




<? get_footer(); ?>
