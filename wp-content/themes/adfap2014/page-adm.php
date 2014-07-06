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
<section id="meio" class="col-sm-7 col-sm-offset-2">
	<h1>Administradora de Condomínio</h1>
	<!-- conteúdo -->

	<strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.
	<h2>Header Level 2</h2>
	<ol>
		<li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
		<li>Aliquam tincidunt mauris eu risus.</li>
	</ol>
	<pre><code>
	#header h1 a { 
		display: block; 
		width: 300px; 
		height: 80px; 
	}
	</code></pre>
	Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, commodo vitae, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. Donec non enim in turpis pulvinar facilisis. Ut felis. Praesent dapibus, neque id cursus faucibus, tortor neque egestas augue, eu vulputate magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus, metus

	<img src="http://lorempixum.com/300/200" alt="Random image courtesy of LoremPixum.com" />
	<h2>Header Level 2</h2>
	<ol>
		<li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
		<li>Aliquam tincidunt mauris eu risus.</li>
	</ol>
	<pre><code>
	#header h1 a { 
		display: block; 
		width: 300px; 
		height: 80px; 
	}
	</code></pre>
	<pre><code>
	#header h1 a { 
		display: block; 
		width: 300px; 
		height: 80px; 
	}
	</code></pre>


</section>
<section id='direita' class='col-sm-3'>
	<h2 class="sub-titulo">Blogs</h2>
	<ul class="nav nav-pills nav-stacked">
		<li>
			<a href="#">
				<img src="http://lorempixel.com/40/40/city/9/" class="thumbnail pull-left">
				<h2>Lorem ipsum dolor sit amet orci aliquam.</h2>
			</a>
		</li>
		<li>
			<a href="#">
				<img src="http://lorempixel.com/40/40/city/9/" class="thumbnail pull-left">
				<h2>Lorem ipsum dolor sit amet orci aliquam.</h2>
			</a>
		</li>
		<li>
			<a href="#">
				<img src="http://lorempixel.com/40/40/city/9/" class="thumbnail pull-left">
				<h2>Lorem ipsum dolor sit amet orci aliquam.</h2>
			</a>
		</li>
		<li>
			<a href="#">
				<img src="http://lorempixel.com/40/40/city/9/" class="thumbnail pull-left">
				<h2>Lorem ipsum dolor sit amet orci aliquam.</h2>
			</a>
		</li>

	</ul>
</section>




<? get_footer(); ?>
