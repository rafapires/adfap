<table>
	<tr>
		<td><?php langCsp::_e('Subject')?>:</td>
		<td><?php echo htmlCsp::text('email_tpl['. $this->tplData['id']. '][subject]', array('value' => $this->tplData['subject']))?></td>
	</tr>
	<tr>
		<td><?php langCsp::_e('Body')?>:</td>
		<td><?php echo htmlCsp::textarea('email_tpl['. $this->tplData['id']. '][body]', array('value' => $this->tplData['body']))?></td>
	</tr>
	<tr>
		<td><?php langCsp::_e('Available veriables')?>:</td>
		<td><?php echo $this->tplData['variables']?></td>
	</tr>
</table>