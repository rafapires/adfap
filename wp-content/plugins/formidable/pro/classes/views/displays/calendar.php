<?php

for ($i=$week_begins; $i<($maxday+$startday); $i++){
    $pos = $i % 7;
    $end_tr = false;
    if($pos == $week_begins ) echo "<tr>\n";
    
    $day = $i - $startday + 1;
    
    //add classes for the day
    $day_class = '';
    
    //check for today
    if(isset($today) and $day == $today)
        $day_class .= ' frmcal-today';
        
    if(($pos == $week_begins) or ($pos == $week_ends))
        $day_class .= ' frmcal-week-end';
    
?>   
<td<?php echo (!empty($day_class)) ? ' class="'. $day_class .'"' : ''; ?>><div class="frmcal_date"><?php 
echo (isset($day_names[$i]) ? $day_names[$i] .' ' : '');
unset($day_class);
  
    if(($i < $startday)){
        echo '</div>';
    }else{ 
        ?><div class="frmcal_num"><?php echo $day ?></div></div> <div class="frmcal-content">
<?php
        if(isset($daily_entries) and isset($daily_entres[$i]) and !empty($daily_entres[$i])){
            foreach($daily_entres[$i] as $entry){
                if(isset($used_entries) and isset($used_entries[$entry->id])){
                    echo '<div class="frm_cal_multi_'. $entry->id .'">'. $used_entries[$entry->id] .'</div>';
                }else{
                    echo $this_content = apply_filters('frm_display_entry_content', $new_content, $entry, $shortcodes, $display, $show);
                
                    if(isset($used_entries))
                        $used_entries[$entry->id] = $this_content;
                    unset($this_content);
                }
            }
        } 
    }
    ?></div>
</td>
<?php
    if($pos == $week_ends ){
        $end_tr = true;
        echo "</tr>\n";
    }
}

$pos++;
if($pos == 7) $pos = 0;
if($pos != ($week_begins)){
    if($pos>$week_begins)
        $week_begins = $week_begins+7;
    for ($e=$pos; $e<$week_begins; $e++)
        echo "<td></td>\n";
}

if(!$end_tr)
    echo '</tr>';
