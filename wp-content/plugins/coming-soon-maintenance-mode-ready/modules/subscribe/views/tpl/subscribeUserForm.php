<form actiom="" method="post" id="cspSubscribeForm">
	<label for=""><?php langCsp::_e('Enter your email to subscribe')?></label>:
	<?php echo htmlCsp::text('email')?><br />
	<div>
		<?php echo htmlCsp::hidden('reqType', array('value' => 'ajax'))?>
		<?php echo htmlCsp::hidden('page', array('value' => 'subscribe'))?>
		<?php echo htmlCsp::hidden('action', array('value' => 'create'))?>
		<?php echo htmlCsp::submit('create', array('value' => langCsp::_('Subscribe')))?>
	</div>
	<div id="cspSubscribeCreateMsg"></div>
</form>
