<div id="cspAdminOptionsTabs">
    <h1>
        <?php langCsp::_e('Ready! Comming Soon')?>,
        <?php langCsp::_e('version')?>
        [<?php echo CSP_VERSION?>]
    </h1>
	<ul>
		<?php foreach($this->tabsData as $tId => $tData) { ?>
		<li class="<?php echo $tId?>"><a href="#<?php echo $tId?>"><?php langCsp::_e($tData['title'])?></a></li>
		<?php }?>
	</ul>
	<?php foreach($this->tabsData as $tId => $tData) { ?>
	<div id="<?php echo $tId?>"><?php echo $tData['content']?></div>
	<?php }?>
</div>
<div id="cspAdminTemplatesSelection"><?php echo $this->presetTemplatesHtml?></div>
