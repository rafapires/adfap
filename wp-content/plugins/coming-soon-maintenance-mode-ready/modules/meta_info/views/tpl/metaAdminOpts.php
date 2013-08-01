<form id="cspAdminMetaOptionsForm" action="">
	<?php if(!empty($this->metaTags)) { ?>
	<div class="wrap">
		<div class="metabox-holder">
			<div class="postbox-container" style="width: 100%;">
				<div class="meta-box-sortables ui-sortable">
				<?php foreach($this->metaTags as $metaKey => $metaOpts) { ?>
				<div id="metaOpts_<?php echo $metaKey?>" class="postbox cspAdminTemplateOptRow cspAvoidJqueryUiStyle" style="display: block">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="handle"><?php langCsp::_e( $metaOpts['label'] )?></h3>
				<div class="inside">
					<div><?php echo $metaOpts['adminOptsContent']?></div>
				</div>
				</div>
				<?php }?>
				</div>
			</div>
		</div>
	</div>
	<div style="clear: both;"></div>
	<?php }?>
	<div>
		<?php 
		echo htmlCsp::hidden('reqType', array('value' => 'ajax'));
		echo htmlCsp::hidden('page', array('value' => 'options'));
		echo htmlCsp::hidden('action', array('value' => 'saveGroup'));
		echo htmlCsp::submit('saveAll', array('value' => langCsp::_('Save All Changes'), 'attrs' => 'class="button button-primary button-large"'));
		?>
	</div>
	<div id="cspAdminMetaOptionsMsg"></div>
</form>