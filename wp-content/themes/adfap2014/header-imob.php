<!DOCTYPE html>

<html lang="pt-br"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php bloginfo('template_url'); ?>/img/favicon.ico">

    <title>ADFAP | Imobiliária</title>

    <!-- Bootstrap core CSS -->

    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style.css" type="text/css" media="all" />
    <link href='http://fonts.googleapis.com/css?family=Ubuntu|Source+Code+Pro|Raleway' rel='stylesheet' type='text/css' media="all">
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]
    <script src="http://getbootstrap.com/assets/js/ie-emulation-modes-warning.js"></script>-->

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug 
    <script src="http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  <meta name="chromesniffer" id="chromesniffer_meta" content="{&quot;jQuery&quot;:&quot;1.11.1&quot;}"><script type="text/javascript" src="chrome-extension://homgcnaoacgigpkkljjjekpignblkeae/detector.js"></script>
  <?php wp_enqueue_script("jquery"); ?>
  <?php wp_head(); ?>
  </head>
    <body cz-shortcut-listen="true">

    <div id="<?php echo $post->post_name; ?>" <?php post_class('container-fluid'); ?>>

    <section id="esquerda" class="col-xp-12 col-sm-2">
<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
  <a class="logo center-block" href="<?php echo site_url(); ?>">
      <img src="<?php bloginfo('template_url'); ?>/img/logo-web-hi.png" class="img-responsive center-block">
    <h1>Imobiliária</h1>
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
    <?php
      wp_nav_menu(
        array(
          'theme_location'  =>  'page-imob',
          'container'       =>  false,
          'items_wrap'      =>  '<ul id="%1$s" class="%2$s nav nav-pills nav-stacked">%3$s</ul>'
          )
        );
    ?>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>



</section>