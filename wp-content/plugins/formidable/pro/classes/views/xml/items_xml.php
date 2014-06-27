<?php 

if ( !$item_ids )
    return;
$item_form_id = 0;

// fetch 20 posts at a time rather than loading the entire table into memory
while ( $next_set = array_splice( $item_ids, 0, 20 ) ) {
$where = 'WHERE id IN (' . join( ',', $next_set ) . ')';
$entries = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}frm_items $where" );
unset($where);

// Begin Loop
foreach ( $entries as $entry ) {
    if($item_form_id != $entry->form_id){
        $fields = $frm_field->getAll(array('fi.form_id' => $entry->form_id), 'field_order');
        $item_form_id = $entry->form_id;
    }
?>
	<item>
		<id><?php echo $entry->id ?></id>
		<item_key><?php echo FrmXMLHelper::cdata($entry->item_key) ?></item_key>
		<name><?php echo FrmXMLHelper::cdata($entry->name) ?></name>
		<description><?php echo FrmXMLHelper::cdata($entry->description) ?></description>
		<created_at><?php echo $entry->created_at ?></created_at>
		<updated_at><?php echo $entry->updated_at ?></updated_at>
		<form_id><?php echo $entry->form_id ?></form_id>
		<post_id><?php echo $entry->post_id ?></post_id>
		<ip><?php echo $entry->ip ?></ip>
		<is_draft><?php echo $entry->is_draft ?></is_draft>
		<user_id><?php echo FrmXMLHelper::cdata(FrmProFieldsHelper::get_display_name($entry->user_id, 'user_login')); ?></user_id>
		<updated_by><?php echo FrmXMLHelper::cdata(FrmProFieldsHelper::get_display_name($entry->updated_by, 'user_login')); ?></updated_by>
        <parent_item_id><?php echo $entry->parent_item_id ?></parent_item_id>
        
<?php
		$metas = $wpdb->get_results( $wpdb->prepare("SELECT meta_value, field_id FROM {$wpdb->prefix}frm_item_metas WHERE item_id=%d", $entry->id ));
		foreach ( $metas as $meta ){ ?>
		<item_meta>
		    <field_id><?php echo $meta->field_id ?></field_id>
		    <meta_value><?php
		        if(isset($fields[$meta->field_id]))
		            $meta->meta_value = FrmProFieldsHelper::get_export_val($meta->meta_value, $fields[$meta->field_id]);
		        
		        echo FrmXMLHelper::cdata($meta->meta_value);
		        
		        unset($meta);
		    ?></meta_value>
		</item_meta>
<?php   } ?>
	</item>
<?php
    unset($metas);
    
    if(!empty($entry->post_id)){
        $old_ids = $item_ids;
        $item_ids = array($entry->post_id);
        include(dirname(__FILE__) .'/views_xml.php');
        $item_ids = $old_ids;
    }
    
    unset($entry);
}
}

if(isset($fields))
    unset($fields);
?>