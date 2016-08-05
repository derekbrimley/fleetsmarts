<span class="heading"><?=$heading?></span>
<hr/>
<?php echo form_dropdown('status_dropdown',$status_options,$status,'id="status_dropdown" onchange="load_people_list(\''.$people_type.'\')" class="left_bar_input"');?>
<br>