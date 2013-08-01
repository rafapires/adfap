<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
	<head>
		<?php dispatcherCsp::doAction('tplHeaderBegin')?>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <meta name="keywords" content="<?php echo frameCsp::_()->getModule('options')->getController()->getModel()->get('meta_keywords')?>">
        <meta name="description" content="<?php echo frameCsp::_()->getModule('options')->getController()->getModel()->get('meta_description')?>">
        <title><?php echo frameCsp::_()->getModule('options')->getController()->getModel()->get('meta_title') ?></title>
		<?php if(!empty($this->styles)) { ?>
			<?php foreach($this->styles as $s) { ?>
				<link rel="stylesheet" type="text/css" href="<?php echo $s;?>" />
			<?php }?>
		<?php }?>
				
		<?php echo $this->initJsVars;?>
				
		<?php if(!empty($this->scripts)) { ?>
			<?php foreach($this->scripts as $s) { ?>
				<script type="text/javascript" src="<?php echo $s;?>"></script>
			<?php }?>
		<?php }?>
		<?php dispatcherCsp::doAction('tplHeaderEnd')?>
	</head>
	<body>
        <?php dispatcherCsp::doAction('tplBodyBegin')?>
        <?php 
            // background setup
            switch(frameCsp::_()->getModule('options')->get('bg_type')) {
                case 'image':
                    switch(frameCsp::_()->getModule('options')->get('bg_img_show_type')) {
                        case 'stretch':
                            echo '<img src="'.frameCsp::_()->getModule('options')->getBgImgFullPath().'" id="fullScreenBg" alt="" />';
                        break;
                        case 'center':
                            echo '<style type="text/css">body {background:url('.frameCsp::_()->getModule('options')->getBgImgFullPath().') center center no-repeat;}</style>';
                        break;
                        case 'tile':
                            echo '<style type="text/css">body {background:url('.frameCsp::_()->getModule('options')->getBgImgFullPath().');}</style>';
                        break;
                    }
                break;
                case 'color':
                    echo '<style type="text/css">body {background:'.frameCsp::_()->getModule('options')->get('bg_color').'}</style>';
                break;
                case 'standart':
                    echo '<img src="'.$this->getModule()->getModPath().'css/img/standart_bg.jpg" id="fullScreenBg" alt="" />';
                break;
            }
        ?>
    
        <div id="wrap">
        <!-- Main Page -->
        <div id="main">
              <!-- Header -->
              <header>
                <?php if(!empty($this->logoPath)) { ?>
                    <div id="logo" class="cspHtmlLogo">
                        <img src="<?php echo $this->logoPath?>" />
                    </div>
                <?php } ?><!-- Set your logo  -->
              </header>
              <span class="topborder overlay"></span>
              
              <!-- Additional content -->
              <div id="content">
                <?php if(!empty($this->messages)) { ?>
                    <?php foreach($this->messages as $m) { ?>
                        <div class="toeSuccessMsg"><?php echo $m?></div>
                    <?php }?>
                <?php }?>
                <?php if(!empty($this->errors)) { ?>
                    <?php foreach($this->errors as $e) { ?>
                        <div class="toeErrorMsg"><?php echo $e?></div>
                    <?php }?>
                <?php }?>    
                  
                <!-- Widget: Title / Subtitle -->
                <section>
                    <?php if(!empty($this->msgTitle)) {?>
                    <h1 class="title cspTextInfoTitle" style="<?php echo $this->msgTitleStyle?>"><?php echo $this->msgTitle;?></h1>
                    <?php }?>
                </section>
                <!-- Widget: Title / Subtitle end -->
                  
                <!-- Widget: Description -->
                <section class="description">
                    <?php if(!empty($this->msgText)) {?>
                    <div style="<?php echo $this->msgTextStyle?>" class="description_content cspTextDescription"><?php echo nl2br($this->msgText);?></div>
                    <?php }?>
                </section>		  
                <!-- Widget: Description end -->
                  
                <!-- Widget: News / Contact -->
                <section class="interact">
                  
                    <!-- Newsletter form -->
                    <div class="col first ">
                        <div class="subscribe cspSubscribeForm">
                            <?php echo $this->subscribeForm;?>
                        </div>
                    </div>
                    <!-- Newsletter form end -->
                              
                    <!-- Contact / Social -->
                    <div class="col last">
                        <div class="contact">
                            <h3><?php langCsp::_e('Find us online'); ?></h3>
                            <p><?php langCsp::_e('Find us online or drop us a line'); ?></p>

                            <div class="findus">
                                <?php echo $this->socIcons;?>
                            </div>
                        </div>
                    </div>
                    <!-- Contact / Social end -->
                        
                    <div class="clearfix"></div>
                </section>		  
                <!-- Widget: News / Contact end -->
                <div class="clearfix"></div>
            </div>
            <!-- Content end -->
        </div> 
        </div>
        <!-- Main Page end -->
              
        <?php dispatcherCsp::doAction('tplBodyEnd')?>
	</body>
</html>
