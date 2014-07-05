<html lang="pt-br"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php bloginfo('template_url'); ?>/img/favicon.ico">

    <title>Starter Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->

    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style.css" type="text/css" media="screen" />
    <link href='http://fonts.googleapis.com/css?family=Ubuntu|Source+Code+Pro|Raleway' rel='stylesheet' type='text/css'>
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
  <meta name="chromesniffer" id="chromesniffer_meta" content="{&quot;jQuery&quot;:&quot;1.11.1&quot;}"><script type="text/javascript" src="chrome-extension://homgcnaoacgigpkkljjjekpignblkeae/detector.js"></script></head>
  <body cz-shortcut-listen="true">
  <?php wp_head(); ?>
    <div id="<?php echo $post->post_name; ?>" <?php post_class('container-fluid'); ?>>