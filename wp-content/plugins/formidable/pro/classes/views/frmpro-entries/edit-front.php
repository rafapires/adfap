<div class="frm_forms<?php echo ($values['custom_style']) ? ' with_frm_style' : ''; ?>" id="frm_form_<?php echo $form->id ?>_container">
<?php include(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/errors.php'); ?>

<?php if (isset($show_form) and $show_form){
if(!empty($errors) and empty($message)){ ?>
<script type="text/javascript">window.onload=function(){location.href="#frm_errors";}</script>
<?php }else if((isset($jump_to_form) and $jump_to_form) or !empty($message)){ ?>
<script type="text/javascript">jQuery(document).ready(function($){frmScrollMsg(<?php echo $form->id ?>)})</script>
<?php } ?>
<form enctype="multipart/form-data" method="post" class="frm-show-form <?php do_action('frm_form_classes', $form) ?>" id="form_<?php echo $form->form_key ?>" <?php echo ($frm_settings->use_html) ? '' : 'action=""'; ?>>
<?php $form_action = 'update';
    require(FrmAppHelper::plugin_path() .'/classes/views/frm-entries/form.php');
?>
</form>
<?php } ?>
</div>