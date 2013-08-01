<?php echo htmlCsp::checkbox('csp_sub_send_notif', array('checked' => 1, 'attrs' => 'id="cspSubSendNotif"'))?>
&nbsp;&nbsp;
<label for="cspSubSendNotif"><?php langCsp::_e(array('Notify subscribers about new', $this->post->post_type))?></label>
<br />
<i><?php langCsp::_e(array('This will send message only if', $this->post->post_type, 'will be published now.'))?></i>