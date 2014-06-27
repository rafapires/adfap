
<script type="text/javascript">
window.onload=function(){location.href="<?php echo admin_url('admin-ajax.php') ?>?action=frm_<?php echo $controller ?>_xml&ids=<?php echo $ids; if(isset($is_template)){ ?>&is_template=<?php echo $is_template; } ?>";} 
</script>