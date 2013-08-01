<?php if(!empty($this->tplModules)) { ?>
	<?php foreach($this->tplModules as $tplMod) { ?>
	<div class="cspAdminTemplateShell cspAdminTemplateShell-<?php echo $tplMod->getCode()?>">
		<a href="#" onclick="return setTemplateOptionCsp('<?php echo $tplMod->getCode()?>');"><?php echo htmlCsp::img( $tplMod->getPrevImgPath(), false, array('attrs' => 'class="cspAdminTemplateImgPrev"'));?></a>
		<br />
		<a href="#" onclick="return setTemplateOptionCsp('<?php echo $tplMod->getCode()?>');"><?php echo $tplMod->getLabel()?></a>
		<div class="cspAdminTemplateSaveMsg"></div>
	</div>
	<?php } ?>
	<div style="clear: both;"></div>
<?php } else { ?>
	<?php lang::_e('No template modules were found'); ?>
<?php }?>