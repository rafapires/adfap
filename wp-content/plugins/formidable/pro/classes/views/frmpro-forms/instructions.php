<div id="frm-dynamic-values" class="tabs-panel" style="display:none;max-height:none;">
	<p class="howto"><?php _e('Add dynamic default values as default text to fields in your form', 'formidable') ?>
    <ul class="frm_code_list" style="margin-bottom:0;">
        <?php 
        $col = 'one';
        foreach ($tags as $tag => $label){
            if(is_array($label)){
                $title = (isset($label['title'])) ? $label['title'] : '';
                $label = (isset($label['label'])) ? $label['label'] : reset($label);
            }else{
                $title = '';
            }
           
        ?>
            <li class="frm_col_<?php echo $col ?>">
                <a class="frmbutton button show_dyn_default_value frm_insert_code<?php if(!empty($title)) echo ' frm_help'; ?>" data-code="<?php echo esc_attr($tag) ?>" href="javascript:void(0)" <?php if(!empty($title)){ ?>title="<?php echo esc_attr($title); ?>"<?php } ?>><?php echo $label ?></a>
            </li>
        <?php 
            $col = ($col == 'one') ? 'two' : 'one';
            unset($tag);
            unset($label);
        } ?>
    </ul>
</div>
