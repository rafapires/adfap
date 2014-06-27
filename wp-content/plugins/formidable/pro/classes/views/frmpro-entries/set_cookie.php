<script type="text/javascript">
jQuery(document).ready(function($){
    jQuery.ajax({type:"POST",url:"<?php echo admin_url( 'admin-ajax.php' ); ?>",
    data:"action=frm_entries_ajax_set_cookie&entry_id=<?php echo $entry_id; ?>&form_id=<?php echo $form_id; ?>"
    });
});    
</script>