<div id="form_entries_page" class="wrap">
    <div class="frmicon icon32"><br/></div>
    <h2><?php _e('Entries', 'formidable'); ?>
        <?php if(current_user_can('frm_create_entries')){ ?>
        <a href="?page=formidable-entries&amp;frm_action=new<?php if($form) echo '&amp;form='. $form->id; ?>" class="add-new-h2"><?php _e('Add New', 'formidable') ?></a>
        <?php } ?>
    </h2>

<?php require(FrmAppHelper::plugin_path() .'/classes/views/shared/errors.php'); 

if($form) FrmAppController::get_form_nav($form->id, true); ?>

<form id="posts-filter" method="get">
    <input type="hidden" name="page" value="formidable-entries" />
    <input type="hidden" name="form" value="<?php echo ($form) ? $form->id : ''; ?>" />
    <input type="hidden" name="frm_action" value="list" />
<?php $wp_list_table->search_box( __( 'Search', 'formidable' ), 'entry' );

$wp_list_table->display(); ?>
</form>

</div>
