<div id="form_reports_page" class="wrap frm_charts">
    <div class="frmicon icon32"><br/></div>
    <h2><?php _e('Reports', 'formidable') ?></h2>
	
	<?php if (!isset($form)){ ?>
        <div id="menu-management" class="nav-menus-php frm-menu-boxes">
            <div class="menu-edit">
            <div id="nav-menu-header"><div class="major-publishing-actions" style="padding:8px 0;">
                <div style="font-size:15px;background:transparent;" class="search"><?php _e('Go to Report', 'formidable') ?></div>
            </div></div>

            <form method="get">
                <div id="post-body">
                <p><?php _e('Select a report to view.', 'formidable'); ?></p>
                <input type="hidden" name="frm_action" value="show" />
                <input type="hidden" name="page" value="formidable-reports" />
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
    <?php }else{
            FrmAppController::get_form_nav($form->id, true);
            $class = 'odd';
        ?>
        <div class="clear"></div>
        <div class="frm_white_bg">
        <?php
        if(isset($data['time']))
                echo $data['time'];
        
        foreach ($fields as $field){ 
            $total = FrmProFieldsHelper::get_field_stats($field->id, 'count'); 
            if(!$total)
                continue;
            ?>
            <div style="margin-top:25px;" class="pg_<?php echo $class ?>">
            <div class="alignleft"><?php echo $data[$field->id] ?></div>
            <div style="padding:10px; margin-top:40px;">
                <p><?php _e('Response Count', 'formidable') ?>: <?php echo FrmProFieldsHelper::get_field_stats($field->id, 'count'); ?></p>
            <?php if(in_array($field->type, array('number', 'hidden'))){ ?>
            <p><?php _e('Total', 'formidable') ?>: <?php echo $total; ?></p>
            <p><?php _e('Average', 'formidable') ?>: <?php echo FrmProFieldsHelper::get_field_stats($field->id, 'average'); ?></p>
            <p><?php _e('Median', 'formidable') ?>: <?php echo FrmProFieldsHelper::get_field_stats($field->id, 'median'); ?></p>
            <?php }else if($field->type == 'user_id'){ 
                $user_ids = $wpdb->get_col("SELECT ID FROM $wpdb->users ORDER BY display_name ASC");
                $submitted_user_ids = $frm_entry_meta->get_entry_metas_for_field($field->id, '', '', array('unique' => true));
                $not_submitted = array_diff($user_ids, $submitted_user_ids); ?>
            <p><?php _e('Percent of users submitted', 'formidable') ?>: <?php echo round((count($submitted_user_ids) / count($user_ids)) *100, 2) ?>%</p>
            <form action="<?php echo admin_url('user-edit.php') ?>" method="get">
            <p><?php _e('Users with no entry', 'formidable') ?>:<br/>
                <?php wp_dropdown_users(array('include' => $not_submitted, 'name' => 'user_id')) ?> <input type="submit" name="Go" value="<?php _e('View Profile', 'formidable') ?>" class="button-secondary" /></p>
            </form>
            <?php } ?>
            </div>
            <div class="clear"></div>
            </div>
        <?php
            $class = ($class == 'odd') ? 'even' : 'odd';
            unset($field); 
        } 
            
        if(isset($data['month']))
            echo $data['month'];
    } ?>
        </div>
</div>
