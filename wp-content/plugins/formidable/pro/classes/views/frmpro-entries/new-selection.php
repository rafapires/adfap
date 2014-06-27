<div id="form_entries_page" class="wrap">
    <div class="frmicon icon32"><br/></div>
    <h2><?php _e('Add New Entry', 'formidable') ?></h2>

    <div id="menu-management" class="clear nav-menus-php frm-menu-boxes">
        <div class="menu-edit">
        <div id="nav-menu-header"><div class="major-publishing-actions" style="padding:8px 0;">
            <div style="font-size:15px;background:transparent;" class="search"><?php _e('Add New Entry', 'formidable') ?></div>
        </div></div>

        <form method="get">
            <div id="post-body">
            <p><?php _e('Select a form for your new entry.', 'formidable'); ?></p>
            <input type="hidden" name="frm_action" value="new" />
            <input type="hidden" name="page" value="formidable-entries" />
            <?php FrmFormsHelper::forms_dropdown('form', '', false); ?><br/>
            </div>
            <div id="nav-menu-footer">
            <div class="major-publishing-actions"><input type="submit" class="button-primary" value="<?php _e('Go', 'formidable') ?>" /></div>

            <div class="clear"></div>
            </div>
        </form>
        </div>

    </div>
    <div class="clear"></div>
</div>