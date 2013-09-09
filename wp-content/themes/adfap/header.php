<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>ADFAP - Adm. de bens e condominios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Le styles -->
    <link href="<?php bloginfo('stylesheet_url');?>" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->


    <?php wp_enqueue_script("jquery"); ?>
    <?php wp_head(); ?>
  </head>
  <body <?php body_class($class); ?>>
    <div id="header">
      <div class="container">
        <div class="row">
          <div class="acima-menu">
            <div class="span4">
              <a class="brand text-center" href="<?php echo site_url(); ?>"><img src="<?php bloginfo('template_url'); ?>/img/adfap-menu.png"></a>
            </div>
            <div class="slogan span3">
              <h1 class="text-center">Cuidando do seu bem como se fosse nosso</h1>
            </div>
            <div class="span5">
              <form class="form-inline pull-right" action="http://webware.com.br/bin/login.asp" method="post">
                <input type="hidden" name="urlErro" value="http://www.webware.com.br/bin/administradora/default.asp?adm=18545180&amp;msg=ERRO">
                <input type="hidden" name="a" value="18545180">
                <input type="text" name="mem" class="input-small" placeholder="Email">
                <input type="password" name="pass" class="input-small" placeholder="Senha">
                <button type="submit" name="submit" class="btn btn-small">Entrar</button>
              </form>
            </div>
          </div>
        </div>
        <div class="navbar">
          <div class="navbar-inner">
            <div class="container">
              <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </a>
              
              <div class="nav-collapse collapse navbar-responsive-collapse">

                <nav class="menu">                  <!-- Main nav -->
                  <?php wp_nav_menu(array(
                          'container'       => false,
                          'items_wrap'      => '<ul id="%1$s" class="%2$s nav">%3$s</ul>',
                          'walker'          => new twitter_bootstrap_nav_walker
                          ));
                  ?>
              </nav>  



              </div><!--/.nav-collapse -->
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div id="conteudo-central" class="container">
