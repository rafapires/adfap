<form action="" method="post" id="cspSubscribeForm">
	<?php echo htmlCsp::text('email', array('attrs' => 'id="subscribe_email" class="showinput span3"'))?>
		<?php echo htmlCsp::hidden('reqType', array('value' => 'ajax'))?>
		<?php echo htmlCsp::hidden('page', array('value' => 'subscribe'))?>
		<?php echo htmlCsp::hidden('action', array('value' => 'create'))?>
		<?php echo htmlCsp::submit('create', array('value' => langCsp::_('Subscribe'), 'attrs' => 'class="showinput span2"'))?>
	<div id="cspSubscribeCreateMsg"></div>
</form>
