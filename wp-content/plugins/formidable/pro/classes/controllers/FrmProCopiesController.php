<?php
 
class FrmProCopiesController{
    public static function load_hooks(){
        add_action('init', 'FrmProCopiesController::install');
        add_action('frm_after_install', 'FrmProCopiesController::install', 20);
        add_action('frm_after_uninstall', 'FrmProCopiesController::uninstall');
        add_action('frm_update_form', 'FrmProCopiesController::save_copied_form', 20, 2);
        add_action('frm_create_display', 'FrmProCopiesController::save_copied_display', 20, 2);
        add_action('frm_update_display', 'FrmProCopiesController::save_copied_display', 20, 2);
        add_action('frm_destroy_display', 'FrmProCopiesController::destroy_copied_display');
        add_action('frm_destroy_form', 'FrmProCopiesController::destroy_copied_form');
        add_action('delete_blog', 'FrmProCopiesController::delete_copy_rows', 20, 2 );
    }
    
    public static function install(){
        $frmpro_copy = new FrmProCopy();
        $frmpro_copy->install();
    }
    
    public static function uninstall(){
        $frmpro_copy = new FrmProCopy();
        $frmpro_copy->uninstall();
    }

    public static function save_copied_display($id, $values){
        global $wpdb, $blog_id;
        $frmpro_copy = new FrmProCopy();
        if (isset($values['options']['copy'])){
            $old_id = get_post_meta($id, 'frm_old_id', true);
            if($old_id){
                //remove old ID from copies
                $wpdb->delete($frmpro_copy->table_name, array('form_id' => $id, 'type' => 'display', 'blog_id' => $blog_id));
            }
            $created = $frmpro_copy->create(array('form_id' => $id, 'type' => 'display'));
        }else{
            $wpdb->delete($frmpro_copy->table_name, array('form_id' => $id, 'type' => 'display', 'blog_id' => $blog_id));
        }
    }
        
    public static function save_copied_form($id, $values){
        global $blog_id, $wpdb;
        $frmpro_copy = new FrmProCopy();
        if (isset($values['options']['copy']))
            $created = $frmpro_copy->create(array('form_id' => $id, 'type' => 'form'));
        else
            $wpdb->delete($frmpro_copy->table_name, array('type' => 'form', 'form_id' => $id, 'blog_id' => $blog_id));
    }
    
    public static function destroy_copied_display($id){
        global $blog_id, $wpdb;
        $frmpro_copy = new FrmProCopy();
        $copies = $frmpro_copy->getAll($wpdb->prepare("blog_id=%d and form_id=%d and type=%s", $blog_id, $id, 'display'));
        foreach ($copies as $copy){
            $frmpro_copy->destroy($copy->id);
            unset($copy);
        }
    }
    
    public static function destroy_copied_form($id){
        global $blog_id, $wpdb;
        $frmpro_copy = new FrmProCopy();
        $copies = $frmpro_copy->getAll($wpdb->prepare("blog_id=%d and form_id=%d and type=%s", $blog_id, $id, 'form'));
        foreach ($copies as $copy)
            $frmpro_copy->destroy($copy->id);
    }
    
    public static function delete_copy_rows($blog_id, $drop){
        $blog_id = (int)$blog_id;
        if(!$drop or !$blog_id)
            return;
            
        $frmpro_copy = new FrmProCopy();
        $copies = $frmpro_copy->getAll("blog_id='$blog_id'");
        foreach ($copies as $copy){
            $frmpro_copy->destroy($copy->id);
            unset($copy);
        }
    }
        
}
