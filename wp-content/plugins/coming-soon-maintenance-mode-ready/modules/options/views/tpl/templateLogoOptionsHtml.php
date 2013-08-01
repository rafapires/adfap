<div class="cspLeftCol">
<?php echo htmlCsp::ajaxfile('logo_image', array(
	'url' => uriCsp::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'options', 'action' => 'saveLogoImg', 'reqType' => 'ajax')), 
	'buttonName' => 'Select Logo image', 
	'responseType' => 'json',
	'onSubmit' => 'toeOptLogoImgOnSubmitNewFile',
	'onComplete' => 'toeOptLogoImgCompleteSubmitNewFile',
))?>
<div id="cspOptLogoImgkMsg"></div>
<br />
<img id="cspOptLogoImgPrev" 
		src="<?php echo $this->optModel->isEmpty('logo_image') 
		? '' 
		: frameCsp::_()->getModule('options')->getLogoImgFullPath()?>" 
style="max-width: 200px;" />
</div>
<div class="cspRightCol">
    <div class="cspTip cspTipArrowLeft nomargin">
        <?php langCsp::_e('Choose your logo, you can use png, jpg or gif image file.')?>
        <span class="cspTipCorner"></span>
    </div>
    <br />
    <div class="cspTip cspTipArrowDown nomargin">
        <?php langCsp::_e('You can use default logo, your own or disable it. To disable logo on Coming Soon page click "Remove image" button bellow.')?>
        <span class="cspTipCorner"></span>
    </div> <br /> 
    
    <?php echo htmlCsp::button(array('value' => langCsp::_('Remove image'), 'attrs' => 'id="cspLogoRemove" class="button button-large" style="width:100%;"'))?>
    <?php echo htmlCsp::button(array('value' => langCsp::_('Set default'), 'attrs' => 'id="cspLogoSetDefault" class="button button-large" style="width:100%;"'))?>
    <div id="cspAdminOptLogoDefaultMsg"></div>
</div>
<div class="clearfix"></div>