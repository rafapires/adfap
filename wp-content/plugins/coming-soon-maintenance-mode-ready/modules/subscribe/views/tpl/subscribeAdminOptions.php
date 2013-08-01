<div class="wrap">
	<div class="metabox-holder">
		<div class="postbox-container" style="width: 100%;">
			<div class="meta-box-sortables ui-sortable">
				<div id="idCspMainSubOpts" class="postbox cspAdminTemplateOptRow cspAvoidJqueryUiStyle" style="display: block">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><?php langCsp::_e( 'Main Subscribe Settings' )?></h3>
					<div class="inside">
						<form id="cspSubAdminOptsForm" action="">
							<?php echo htmlCsp::checkbox('sub_enable_checkbox', array('attrs' => 'id="cspSubEnableOpt"', 'checked' => ($this->optModel->get('sub_enable') == 1)))?>
							<?php echo htmlCsp::hidden('opt_values[sub_enable]', array('value' => $this->optModel->get('sub_enable')))?>
							<label for="cspSubEnableOpt" class="button button-large"><?php langCsp::_e('Enable')?></label>
							<br />
							<br />
							<label for="cspSubAdminEmailOpt"><?php langCsp::_e('Email notification about new subscriber')?></label>: 
							<?php echo htmlCsp::text('opt_values[sub_admin_email]', array('attrs' => 'id="cspSubAdminEmailOpt"', 'value' => $this->optModel->get('sub_admin_email')))?>

							<?php if(!empty($this->emailEditTpls)) { ?>
							<div class="wrap">
								<div class="metabox-holder">
									<div class="postbox-container" style="width: 100%;">
										<div class="meta-box-sortables ui-sortable">
										<?php foreach($this->emailEditTpls as $tpl) { ?>
											<div id="idCspMainSubOpts" class="postbox cspAdminTemplateOptRow" style="display: block">
												<div class="handlediv" title="Click to toggle"><br></div>
												<h3 class="hndle"><?php langCsp::_e( $tpl['label'] )?></h3>
												<div class="inside"><?php echo $tpl['content'];?></div>
											</div>
										<?php }?>
										</div>
									</div>
								</div>
							</div>
							<?php }?>
							<div>
								<?php echo htmlCsp::hidden('reqType', array('value' => 'ajax'))?>
								<?php echo htmlCsp::hidden('page', array('value' => 'options'))?>
								<?php echo htmlCsp::hidden('action', array('value' => 'saveSubscriptionGroup'))?>
								<?php echo htmlCsp::submit('saveAll', array('value' => langCsp::_('Save All Changes'), 'attrs' => 'class="button button-primary button-large"'))?>
							</div>
							<div id="cspAdminSubOptionsMsg"></div>
						</form>
					</div>
				</div>
				<div id="idCspSubscribers" class="postbox cspAdminTemplateOptRow" style="display: block">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><?php langCsp::_e( 'Subscribers' )?></h3>
					<div class="inside">
						<table id="cspAdminSubersTable" width="100%">
							<tr class="cspTblHeader">
								<td><?php langCsp::_e('Email')?></td>
								<td><?php langCsp::_e('Status')?></td>
								<td><?php langCsp::_e('Remove')?></td>
							</tr>
							<tr class="cspExample cspTblRow" style="display: none;">
								<td class="email"></td>
								<td>
									<a href="#" onclick="cspSubscrbChangeStatus(this); return false;" class="status cspStatusIndicator" valueTo="class"></a>
								</td>
								<td>
									<a href="#" onclick="cspSubscrbRemove(this); return false;"><?php echo htmlCsp::img('cross.gif')?></a>
									<?php echo htmlCsp::hidden('id', array('attrs' => 'class="id" valueTo="value"'))?>
								</td>
							</tr>
						</table>
						<div id="cspAdminSubersPaging"></div>
						<div id="cspAdminSubersMsg"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div style="clear: both;"></div>