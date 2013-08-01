<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	postboxes.add_postbox_toggles(pagenow);
});
// -->
</script>
<form id="cspAdminTemplateOptionsForm">
	<div>
		<?php echo htmlCsp::inputButton(array('value' => langCsp::_('Сhoose Preset template'), 'attrs' => 'class="cspSetTemplateOptionButton button button-primary button-large"')); ?>
	</div>
	<div class="wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div id="postbox-container-1" class="postbox-container" style="width: 100%;">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<?php if(!empty($this->tplOptsData)) { ?>
					<?php $i = 1;?>
					<?php foreach($this->tplOptsData as $optData) { ?>
						<div id="id<?php echo $i;?>" class="postbox cspAdminTemplateOptRow" style="display: block">
							<div class="handlediv" title="<?php langCsp::_e( 'Click to toggle' )?>"><br></div>
							<h3 class="hndle"><?php langCsp::_e( $optData['title'] )?></h3>
							<div class="inside">
								<?php echo $optData['content']?>
							</div>
						</div>
						<?php $i++;?>
					<?php }?>
				<?php }?>
				</div>
			</div>
			<div>
				<?php echo htmlCsp::hidden('reqType', array('value' => 'ajax'))?>
				<?php echo htmlCsp::hidden('page', array('value' => 'options'))?>
				<?php echo htmlCsp::hidden('action', array('value' => 'saveGroup'))?>
				<?php echo htmlCsp::submit('saveAll', array('value' => langCsp::_('Save All Changes'), 'attrs' => 'class="button button-primary button-large"'))?>
			</div>
			<div id="cspAdminTemplateOptionsMsg"></div>
		</div>
	</div>
</form>