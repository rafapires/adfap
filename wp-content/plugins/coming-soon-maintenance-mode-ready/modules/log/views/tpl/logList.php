<script type="text/javascript">
// <!--
var toeAllLogData = <?php echo utilsCsp::jsonEncode($this->logs)?>;
jQuery(document).ready(function(){
   jQuery('#toeLogTabs').tabs(); 
   jQuery('.toeLogRow').click(function(){
       var type = str_replace(jQuery(this).parents('div.toeLogTab:first').attr('id'), 'toeLogs', '');
       var id = parseInt(jQuery(this).find('td:first').html());
       if(typeof(toeAllLogData[type]) != undefined && typeof(toeAllLogData[type][id]) != undefined) {
           var logContent = '<table>';
           if(typeof(toeAllLogData[type][id]['data']) == 'object') {
               for(var key in toeAllLogData[type][id]['data']) {
                   logContent += '<tr><td valign="top">'+ key+ '</td><td valign="top">'+ toeAllLogData[type][id]['data'][key]+ '</td></tr>';
               }
           } else if(typeof(toeAllLogData[type][id]['data']) == 'string') {
                logContent += '<tr><td valign="top">data</td><td valign="top">'+ toeAllLogData[type][id]['data']+ '</td></tr>';
           }
           logContent += '</table>';
           subScreen.show(logContent);
       }
       //alert(type);
   });
});
// -->
</script>
<div id="toeLogTabs">
    <ul>
        <?php foreach($this->logTypes as $type => $tInfo) {?>
            <li><a href="#toeLogs<?php echo $type?>"><?php langCsp::_e($tInfo['label'])?></a></li>
        <?php }?>
    </ul>
    <?php foreach($this->logTypes as $type => $tInfo) {?>
    <div id="toeLogs<?php echo $type?>" class="toeLogTab">
        <table width="100%">
            <tr class="toe_admin_row_header">
                <td><?php langCsp::_e('Log ID')?></td>
                <td><?php langCsp::_e('Log Date')?></td>
            </tr>
		<?php if(!empty($this->logs[$type])) {?>
			<?php foreach($this->logs[$type] as $l) { ?>
            <tr class="toe_admin_row toeLogRow">
                <td><?php echo $l['id']?></td>
                <td><?php echo date(CSP_DATE_FORMAT_HIS, $l['date_created'])?></td>
            </tr>
			<?php } ?>
		<?php }?>
        </table>
    </div>
    <?php }?>
</div>