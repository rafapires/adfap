<h4 class="cspTitle"><?php langCsp::_e('Title')?>:</h4>
<?php echo htmlCsp::text('opt_values[msg_title]', array('value' => $this->optModel->get('msg_title')))?>
<div class="cspLeftCol">
    <?php langCsp::_e('Select color')?>:
    <?php echo htmlCsp::colorpicker('opt_values[msg_title_color]', array('value' => $this->optModel->get('msg_title_color')))?>
</div>
<div class="cspRightCol">
    <?php langCsp::_e('Select font')?>:
    <?php echo htmlCsp::fontsList('opt_values[msg_title_font]', array('value' => $this->optModel->get('msg_title_font')));?>
</div>
<div class="clearfix"></div>
<div class="clearfix">
	<?php echo htmlCsp::button(array('value' => langCsp::_('Set default'), 'attrs' => 'id="cspMsgTitleSetDefault"'))?>
	<div id="cspAdminOptMsgTitleDefaultMsg"></div>
</div>
<div class="clearfix"></div>
<br />
<h4 class="cspTitle"><?php langCsp::_e('Text')?>:</h4>
<?php echo htmlCsp::textarea('opt_values[msg_text]', array('value' => $this->optModel->get('msg_text')))?>
<div class="cspLeftCol">
    <?php langCsp::_e('Select color')?>:
    <?php echo htmlCsp::colorpicker('opt_values[msg_text_color]', array('value' => $this->optModel->get('msg_text_color')))?>
</div>
<div class="cspRightCol">
    <?php langCsp::_e('Select font')?>:
    <?php echo htmlCsp::fontsList('opt_values[msg_text_font]', array('value' => $this->optModel->get('msg_text_font')));?>
</div>
<div class="clearfix"></div>
<div class="clearfix">
	<?php echo htmlCsp::button(array('value' => langCsp::_('Set default'), 'attrs' => 'id="cspMsgTextSetDefault"'))?>
	<div id="cspAdminOptMsgTextDefaultMsg"></div>
</div>
<div class="clearfix"></div>