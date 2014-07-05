<?php
/*
Template Name: ADM
*/
get_header();
?>

<section id="menu" class="col-lg-2">

<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Brand</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Link</a></li>
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li>
      </ul>
      <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

</section>


<section id="conteudo" class="col-xs-7 col-xs-offset-2">
	<h1>Administradora de Condomínio</h1>

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

<section id="side-bar" class="col-xs-3">
	<section id="2-via" class="row">
		<button class="btn btn-lg center-block"><a href="#">2ª Via boleto</a></button>

	</section>
	<section id="list-blog" class="row">
		<h2>Leia também</h2>
		<div class="list-group">
			<a href="#" class="list-group-item">
				<img src="http://lorempixel.com/80/80/city/" class="img-rounded pull-left">
				<h3>Lorem ipsum dolor sit amet orci aliquam.</h3>
			</a>
			<a href="#" class="list-group-item">
				<img src="http://lorempixel.com/80/80/city/" class="img-rounded pull-left">
				<h3>Lorem ipsum dolor sit amet orci aliquam.</h3>
			</a>
			<a href="#" class="list-group-item">
				<img src="http://lorempixel.com/80/80/city/" class="img-rounded pull-left">
				<h3>Lorem ipsum dolor sit amet orci aliquam.</h3>
			</a>
			<a href="#" class="list-group-item">
				<img src="http://lorempixel.com/80/80/city/" class="img-rounded pull-left">
				<h3>Lorem ipsum dolor sit amet orci aliquam.</h3>
			</a>
		</div>
	</section>
</section>

<? get_footer(); ?>
