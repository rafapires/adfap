<div class="general_settings metabox-holder tabs-panel" style="min-height:0px;border-bottom:none;display:<?php echo ($a == 'general_settings') ? 'block' : 'none'; ?>;">
<?php if (!is_multisite() or is_super_admin() or !get_site_option($frm_update->pro_wpmu_store)){ ?>
    <div class="postbox">
        <h3 class="hndle manage-menus"><div id="icon-ms-admin" class="icon32 frm_postbox_icon"><br/></div> <?php _e('Formidable Pro License', 'formidable')?></h3>
        <div class="inside">
            <?php $frm_update->pro_cred_form(); ?>
        </div>
    </div>
<?php } ?>
</div>