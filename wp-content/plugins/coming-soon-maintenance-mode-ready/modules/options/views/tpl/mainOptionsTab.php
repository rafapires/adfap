<form class="cspNiceStyle" id="cspAdminOptionsForm">
	<table width="100%">
		<?php foreach($this->allOptions as $opt) { ?>
		<tr class="cspAdminOptionRow-<?php echo $opt['code']?> cspTblRow">
			<td><?php langCsp::_e($opt['label'])?></td>
			<td>
			<?php
				$htmltype = $opt['htmltype'];
				if($opt['code'] != 'template') {	// For template will be unique option editor
					$htmlOptions = array('value' => $opt['value'], 'attrs' => 'class="cspGeneralOptInput"');
					switch($htmltype) {
						case 'checkbox': case 'checkboxHiddenVal':
							$htmlOptions['checked'] = (bool)$opt['value'];
							break;
					}
					if(!empty($opt['params']) && is_array($opt['params'])) {
						$htmlOptions = array_merge($htmlOptions, $opt['params']);
					}
					echo htmlCsp::$htmltype('opt_values['. $opt['code']. ']', $htmlOptions);
				}
			?>
			<?php if($opt['code'] == 'template') { ?>
				<?php echo htmlCsp::inputButton(array('value' => langCsp::_('Set Template'), 'attrs' => 'class="cspGoToTemplateTabOptionButton button button-primary" code="'. $opt['code']. '"')); ?>
				<?php 
					$plTemplate = $this->optModel->get('template');		// Current plugin template
					$tplName = ($plTemplate && frameCsp::_()->getModule($plTemplate)) ? frameCsp::_()->getModule($plTemplate)->getLabel() : '';
				?>
				<div class="cspAdminTemplateSelectedName"><?php langCsp::_e($tplName)?></div>
			<?php }?>
			</td>
		</tr>
		<?php }?>
		<tr>
			<td>
				<?php echo htmlCsp::hidden('reqType', array('value' => 'ajax'))?>
				<?php echo htmlCsp::hidden('page', array('value' => 'options'))?>
				<?php echo htmlCsp::hidden('action', array('value' => 'saveMainGroup'))?>
				<?php echo htmlCsp::submit('saveAll', array('value' => langCsp::_('Save All Changes'), 'attrs' => 'class="button button-primary button-large"'))?>
			</td>
			<td id="cspAdminMainOptsMsg"></td>
		</tr>
	</table>
</form>
