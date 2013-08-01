<form action="" method="post" id="cspSubscribeForm">
	<div class="cspFormHint"><?php langCsp::_e('Enter your email to subscribe')?><span class="cspFormHintCorner"></span></div>
	<?php echo htmlCsp::text('email', array('attrs' => 'id="subscribe_email"'))?>
		<?php echo htmlCsp::hidden('reqType', array('value' => 'ajax'))?>
		<?php echo htmlCsp::hidden('page', array('value' => 'subscribe'))?>
		<?php echo htmlCsp::hidden('action', array('value' => 'create'))?>
		<?php echo htmlCsp::submit('create', array('value' => langCsp::_('Subscribe'), 'attrs' => 'id="cspSubscribeButton"'))?>
	<div id="cspSubscribeCreateMsg"></div>
</form>
