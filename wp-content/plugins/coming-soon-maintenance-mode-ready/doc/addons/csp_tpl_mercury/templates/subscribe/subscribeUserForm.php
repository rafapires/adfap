<h3><?php echo langCsp::_('Get our news'); ?></h3>
<p><?php echo langCsp::_('Enter your mail to be notified when more info is available'); ?></p>
<form action="" method="post" id="cspSubscribeForm">
    <?php echo htmlCsp::text('email', array('attrs' => 'id="subscribe_email"'))?>
		<?php echo htmlCsp::hidden('reqType', array('value' => 'ajax'))?>
		<?php echo htmlCsp::hidden('page', array('value' => 'subscribe'))?>
		<?php echo htmlCsp::hidden('action', array('value' => 'create'))?>
		<?php echo htmlCsp::submit('create', array('value' => langCsp::_('Send'), 'attrs' => 'class="transition"'))?>
	<div id="cspSubscribeCreateMsg"></div>
</form>
