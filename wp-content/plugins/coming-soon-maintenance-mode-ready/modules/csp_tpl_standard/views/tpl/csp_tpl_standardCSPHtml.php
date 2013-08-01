<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
	<head>
		<?php dispatcherCsp::doAction('tplHeaderBegin')?>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
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
		<!-- <style type="text/css">
			html { <?php //echo $this->bgCssAttrs;?> }
		</style> -->
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
    
        <div class="cspHtmlContainerWrapper">
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
            <?php if(!empty($this->logoPath)) { ?>
                <div class="cspHtmlLogo">
                    <img src="<?php echo $this->logoPath?>" />
                </div>
            <?php } ?>
            <div class="cspTextInfo">
                <?php if(!empty($this->msgTitle)) {?>
                <h2 style="<?php echo $this->msgTitleStyle?>" class="cspTextInfoTitle"><?php echo $this->msgTitle;?></h2>
                <?php }?>
                <?php if(!empty($this->msgText)) {?>
                <div style="<?php echo $this->msgTextStyle?>" class="cspTextDescription"><?php echo nl2br($this->msgText);?></div>
                <?php }?>
            </div>
            <div style="clear: both;"></div>
            
            <?php if($this->subscribeForm): ?>
            <div class="cspSubscribeForm"><?php echo $this->subscribeForm;?></div>
            <?php endif; ?>
            
            <?php if($this->socIcons): ?>
            <div class="cspSocialIcons"><?php echo $this->socIcons;?></div>
            <?php endif; ?>
        </div>
        <?php dispatcherCsp::doAction('tplBodyEnd')?>
	</body>
</html>
