<table width="100%">
	<tr class="cspHeadCells">
		<td width="50%">
			<?php echo htmlCsp::radiobutton('opt_values[bg_type]', array('value' => 'color', 'attrs' => 'id="cspBgTypeColor"', 'checked' => ($this->optModel->get('bg_type') == 'color')))?>
			<label for="cspBgTypeColor" class="button button-large"><?php langCsp::_e('Color')?></label>            
		</td>
		<td width="50%">
			<?php echo htmlCsp::radiobutton('opt_values[bg_type]', array('value' => 'image', 'attrs' => 'id="cspBgTypeImage"', 'checked' => ($this->optModel->get('bg_type') == 'image')))?>
			<label for="cspBgTypeImage" class="button button-large"><?php langCsp::_e('Image')?></label>
		</td>
	</tr>
	<tr class="cspBodyCells">
		<td id="cspBgTypeColor-selection" colspan="2">
            <?php langCsp::_e('Select Color:')?>
			<?php echo htmlCsp::colorpicker('opt_values[bg_color]', array('value' => $this->optModel->get('bg_color')))?>
			<br />
			<?php echo htmlCsp::button(array('value' => langCsp::_('Set default'), 'attrs' => 'id="cspColorBgSetDefault"'))?>
			<div id="cspAdminOptColorDefaultMsg"></div>
		</td>
		<td id="cspBgTypeImage-selection" colspan="2">
            <div class="cspLeftCol">
                <?php echo htmlCsp::ajaxfile('bg_image', array(
                    'url' => uriCsp::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'options', 'action' => 'saveBgImg', 'reqType' => 'ajax')), 
                    'buttonName' => 'Select Background image', 
                    'responseType' => 'json',
                    'attrs' => 'class="button button-large"',
                    'onSubmit' => 'toeOptImgOnSubmitNewFile',
                    'onComplete' => 'toeOptImgCompleteSubmitNewFile',
                ))?>
                <div id="cspOptImgkMsg"></div>            
                <br />
                <img id="cspOptBgImgPrev" src="<?php echo $this->optModel->isEmpty('bg_image') ? '' : frameCsp::_()->getModule('options')->getBgImgFullPath()?>" style="max-width: 200px;" />
			</div>
            <div class="cspRightCol">
                <div class="cspBgImgShowTypeWrapper">
                    <?php echo htmlCsp::radiobutton('opt_values[bg_img_show_type]', array('value' => 'stretch', 'attrs' => 'id="cspBgImgShowType-stretch"', 'checked' => ($this->optModel->get('bg_img_show_type') == 'stretch')))?>
                    <label for="cspBgImgShowType-stretch" class="button button-large"><?php langCsp::_e('Stretch')?></label>
                    <?php echo htmlCsp::radiobutton('opt_values[bg_img_show_type]', array('value' => 'center', 'attrs' => 'id="cspBgImgShowType-center"', 'checked' => ($this->optModel->get('bg_img_show_type') == 'center')))?>
                    <label for="cspBgImgShowType-center" class="button button-large"><?php langCsp::_e('Center')?></label>
                    <?php echo htmlCsp::radiobutton('opt_values[bg_img_show_type]', array('value' => 'tile', 'attrs' => 'id="cspBgImgShowType-tile"', 'checked' => ($this->optModel->get('bg_img_show_type') == 'tile')))?>
                    <label for="cspBgImgShowType-tile" class="button button-large"><?php langCsp::_e('Tile')?></label>
                </div>
                <div class="cspTip cspTipArrowUp">
                    <?php langCsp::_e('Choose a one of way how to display the site background.')?>
                </div>
                <?php echo htmlCsp::button(array('value' => langCsp::_('Remove image'), 'attrs' => 'id="cspImgBgRemove" class="button button-large" style="width:100%;"'))?>
				<?php echo htmlCsp::button(array('value' => langCsp::_('Set default'), 'attrs' => 'id="cspImgBgSetDefault" class="button button-large" style="width:100%;"'))?>
				<div id="cspAdminOptImgBgDefaultMsg"></div>
            </div>
		</td>
	</tr>
</table>