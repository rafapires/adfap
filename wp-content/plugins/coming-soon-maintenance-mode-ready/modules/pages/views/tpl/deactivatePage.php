<?php
	$title = 'Ready! Coming Soon - plugin deactivation';
?>
<html>
    <head>
        <title><?php langCsp::_e( $title )?></title>
    </head>
    <body>
<div style="position: fixed; margin-left: 40%; margin-right: auto; text-align: center; background-color: #fdf5ce; padding: 10px; margin-top: 10%;">
    <div><?php langCsp::_e( $title )?></div>
    <?php echo htmlCsp::formStart('deactivatePlugin', array('action' => $this->REQUEST_URI, 'method' => $this->REQUEST_METHOD))?>
    <?php
        $formData = array();
        switch($this->REQUEST_METHOD) {
            case 'GET':
                $formData = $this->GET;
                break;
            case 'POST':
                $formData = $this->POST;
                break;
        }
        foreach($formData as $key => $val) {
            if(is_array($val)) {
                foreach($val as $subKey => $subVal) {
                    echo htmlCsp::hidden($key. '['. $subKey. ']', array('value' => $subVal));
                }
            } else
                echo htmlCsp::hidden($key, array('value' => $val));
        }
    ?>
        <table width="100%">
            <tr>
                <td><?php langCsp::_e('Delete Plugin Data (options, setup data, database tables, etc.)')?>:</td>
                <td><?php echo htmlCsp::radiobuttons('deleteOptions', array('options' => array('No', 'Yes')))?></td>
            </tr>
        </table>
    <?php echo htmlCsp::submit('toeGo', array('value' => langCsp::_('Done')))?>
    <?php echo htmlCsp::formEnd()?>
    </div>
</body>
</html>