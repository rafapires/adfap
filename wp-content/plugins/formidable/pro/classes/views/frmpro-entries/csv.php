<?php

header('Content-Description: File Transfer');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Content-Type: text/csv; charset=' . $charset, true);
header('Expires: '. gmdate("D, d M Y H:i:s", mktime(date('H')+2, date('i'), date('s'), date('m'), date('d'), date('Y'))) .' GMT');
header('Last-Modified: '. gmdate('D, d M Y H:i:s') .' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

//if BOM
//echo chr(239) . chr(187) . chr(191);

foreach ($form_cols as $col){
    if(isset($col->field_options['separate_value']) and $col->field_options['separate_value'] and !in_array($col->type, array('user_id', 'file', 'data', 'date')))
        echo '"'. str_replace('"', '""', FrmProEntriesHelper::encode_value(strip_tags($col->name .' '. __('(label)', 'formidable')), $charset, $to_encoding)) .'",';
    
    echo '"'. FrmProEntriesHelper::encode_value(strip_tags($col->name), $charset, $to_encoding) .'",';
}

if($comment_count){
    for ($i=0; $i<$comment_count; $i++){
        echo '"'. FrmProEntriesHelper::encode_value(__('Comment', 'formidable'), $charset, $to_encoding) .'",';
        echo '"'. FrmProEntriesHelper::encode_value(__('Comment User', 'formidable'), $charset, $to_encoding) .'",';
        echo '"'. FrmProEntriesHelper::encode_value(__('Comment Date', 'formidable'), $charset, $to_encoding) .'",';
    }
    unset($i);
}
   
echo '"'. __('Timestamp', 'formidable') .'","'. __('Last Updated', 'formidable') .'","'. __('Created By', 'formidable') .'","'. __('Updated By', 'formidable') .'","'. __('Draft', 'formidable') .'","IP","ID","Key"'."\n";

// fetch 20 posts at a time rather than loading the entire table into memory
while ( $next_set = array_splice( $entry_ids, 0, 20 ) ) {
    $where = 'id IN (' . join( ',', $next_set ) . ')';
    $entries = $frm_entry->getAll($where, '', '', true, false);
    
foreach($entries as $entry){
    foreach ($form_cols as $col){
        $field_value = isset($entry->metas[$col->id]) ? $entry->metas[$col->id] : false;
        
        if(!$field_value and $entry->post_id){
            $col->field_options = maybe_unserialize($col->field_options);
            if ( $col->type == 'tag' || (isset($col->field_options['post_field']) && $col->field_options['post_field']) ) {
                $field_value = FrmProEntryMetaHelper::get_post_value($entry->post_id, $col->field_options['post_field'], $col->field_options['custom_field'], 
                array(
                    'truncate' => (($col->field_options['post_field'] == 'post_category') ? true : false), 
                    'form_id' => $entry->form_id, 'field' => $col, 'type' => $col->type, 
                    'exclude_cat' => (isset($col->field_options['exclude_cat']) ? $col->field_options['exclude_cat'] : 0),
                    'sep' => $sep,
                ));
            }
        }
          
        if (in_array($col->type, array('user_id', 'file', 'date', 'data'))){
            $field_value = FrmProFieldsHelper::get_export_val($field_value, $col);
        }else{
            if(isset($col->field_options['separate_value']) and $col->field_options['separate_value']){
                $sep_value = FrmProEntryMetaHelper::display_value($field_value, $col, array(
                    'type' => $col->type, 'post_id' => $entry->post_id, 'show_icon' => false,
                    'entry_id' => $entry->id, 'sep' => $sep,
                ));
                if ( is_array($sep_value) ) {
                    $sep_value = implode($sep, $sep_value);
                }
                
                $sep_value = FrmProEntriesHelper::encode_value($sep_value, $charset, $to_encoding);
                $sep_value = str_replace('"', '""', $sep_value); //escape for CSV files.
                if ( $line_break != 'return' ) {
                    $sep_value = str_replace(array("\r\n", "\r", "\n"), $line_break, $sep_value);
                }
                echo "\"$sep_value\",";
                unset($sep_value);
            }
            
            $checked_values = maybe_unserialize($field_value);
            $checked_values = apply_filters('frm_csv_value', $checked_values, array('field' => $col));
            
            if (is_array($checked_values)){
                $field_value = implode($sep, $checked_values);
            }else{
                $field_value = $checked_values;
            }
        }
        
        $field_value = FrmProEntriesHelper::encode_value($field_value, $charset, $to_encoding);
        $field_value = str_replace('"', '""', $field_value); //escape for CSV files.
        if ( $line_break != 'return' ) {
            $field_value = str_replace(array("\r\n", "\r", "\n"), $line_break, $field_value);
        }
        
        echo "\"$field_value\",";
            
        unset($col);
        unset($field_value);
    }
    
    $comments = $frm_entry_meta->getAll("item_id=". (int)$entry->id ." and field_id=0", ' ORDER BY it.created_at ASC');
    $place_holder = $comment_count;
    if($comments){
        foreach($comments as $comment){
            $c = maybe_unserialize($comment->meta_value);
            if(!isset($c['comment']))
                continue;
            
            $place_holder--;
            $co = FrmProEntriesHelper::encode_value($c['comment'], $charset, $to_encoding);
            echo "\"$co\",";
            unset($co);
            
            $v = FrmProEntriesHelper::encode_value(FrmProFieldsHelper::get_display_name($c['user_id'], 'user_login'), $charset, $to_encoding);
            unset($c);
            echo "\"$v\",";
            
            $v = FrmProEntriesHelper::encode_value(FrmProAppHelper::get_formatted_time($comment->created_at, $wp_date_format, ' '), $charset, $to_encoding);
            echo "\"$v\",";
            unset($v);
        }
    }
    
    if($place_holder){
        for ($i=0; $i<$place_holder; $i++){
            echo '"","","",';
        }
        unset($i);
    }
    unset($place_holder);
    
    $formatted_date = FrmProAppHelper::get_formatted_time($entry->created_at, $wp_date_format, ' ');
    echo "\"{$formatted_date}\",";
    
    $formatted_date = FrmProAppHelper::get_formatted_time($entry->updated_at, $wp_date_format, ' ');
    echo "\"{$formatted_date}\",";
    unset($formatted_date);
    
    echo '"'. FrmProEntriesHelper::encode_value(FrmProFieldsHelper::get_display_name($entry->user_id, 'user_login'), $charset, $to_encoding) .'",';
    echo '"'. FrmProEntriesHelper::encode_value(FrmProFieldsHelper::get_display_name($entry->updated_by, 'user_login'), $charset, $to_encoding) .'",';
    
    echo '"'. ( $entry->is_draft ? '1' : '0' ) .'",';
    echo "\"{$entry->ip}\",";
    echo "\"{$entry->id}\",";
    echo "\"{$entry->item_key}\"\n";
    unset($entry);
    
}
unset($entries);
}