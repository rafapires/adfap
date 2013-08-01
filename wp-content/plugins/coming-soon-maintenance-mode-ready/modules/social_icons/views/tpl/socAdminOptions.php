<form id="cspAdminSocOptionsForm" action="">
	<?php if(!empty($this->iconsList)) { ?>
	<div class="wrap">
		<div class="metabox-holder">
			<div class="postbox-container" style="width: 100%;">
				<div class="meta-box-sortables ui-sortable">
				<?php foreach($this->iconsList as $socKey => $socOpts) { ?>
					<div id="socOpts_<?php echo $socKey?>" class="postbox cspAdminTemplateOptRow cspAvoidJqueryUiStyle" style="display: block">
						<div class="handlediv" title="Click to toggle"><br></div>
						<h3 class="hndle"><?php langCsp::_e( $socOpts['label'] )?></h3>
						<div class="inside">
							<div><?php echo $socOpts['adminOptsContent']?></div>
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
		<?php echo htmlCsp::hidden('reqType', array('value' => 'ajax'))?>
		<?php echo htmlCsp::hidden('page', array('value' => 'options'))?>
		<?php echo htmlCsp::hidden('action', array('value' => 'saveGroup'))?>
		<?php echo htmlCsp::submit('saveAll', array('value' => langCsp::_('Save All Changes'), 'attrs' => 'class="button button-primary button-large"'))?>
	</div>
	<div id="cspAdmiSocOptionsMsg"></div>
</form>