<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
	<head>
		<?php dispatcherCsp::doAction('tplHeaderBegin')?>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=9" />
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
	<body class="full-background">
        <?php dispatcherCsp::doAction('tplBodyBegin')?>
        <?php if(!empty($this->logoPath)) { ?>
            <div class="logo cspHtmlLogo">
                <img src="<?php echo $this->logoPath?>" />
            </div>
        <?php } ?>
        
        <div id="top">
            <div class="container">
                <?php if(!empty($this->msgTitle)) {?>
                <h1 style="<?php echo $this->msgTitleStyle?>" class="cspTextInfoTitle"><?php echo $this->msgTitle;?></h1>
                <?php }?>
            </div>
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
        </div>
        
        <?php 
            // background setup
            switch(frameCsp::_()->getModule('options')->get('bg_type')) {
                case 'image':
                    switch(frameCsp::_()->getModule('options')->get('bg_img_show_type')) {
                        case 'stretch':
                            echo '<style type="text/css">body.full-background {background: url('.frameCsp::_()->getModule('options')->getBgImgFullPath().') no-repeat center center fixed; 	-webkit-background-size: cover;	-moz-background-size: cover; -o-background-size: cover;	background-size: cover;} </style>';
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
                    echo '<style type="text/css">body.full-background {background: url('.frameCsp::_()->getModule('options')->getBgImgFullPath().') no-repeat center center fixed; 	-webkit-background-size: cover;	-moz-background-size: cover; -o-background-size: cover;	background-size: cover;} </style>';
                break;
            }
        ?>

        <div id="wrapper">
            <div class="container">
                <div id="timer">
                    <div id="timerBlock" class="cspTextInfo">
                        <?php if(!empty($this->msgText)) {?>
                        <div style="<?php echo $this->msgTextStyle?>" class="cspTextDescription"><?php echo nl2br($this->msgText);?></div>
                        <?php }?>
                    </div>
                </div>
                <div class="cspSubscribeForm"><?php echo $this->subscribeForm;?></div>

                <div class="divide"></div>

                <div id="footer">
                    <div class="rightSide">
                        <div class="cspSocialIcons"><?php echo $this->socIcons;?></div>
                    </div>

                    <div class="divide visible-phone"></div>

                    <div class="leftSide">© <?php echo date("Y"); ?> <a href="http://readyshoppingcart.com" target="blank_">Ready! Shopping cart</a>. <?php echo langCsp::_('All Rights Reserved'); ?>.</div>
                </div>
            </div>
        </div>
        
        <?php dispatcherCsp::doAction('tplBodyEnd')?>
	</body>
</html>
