<script>
	$("#people_list_div").height($("#body").height() - 549);
</script>
<span class="heading">Drivers</span>
<hr/>
<br>
<span class="secondary_heading">Driver Search</span>
<hr/>
<input id="type_dropdown" name="type_dropdown" type="hidden" value="<?=$type?>"/>
<input type="text" id="driver_search" name="driver_search" placeholder="Search by name" class="left_bar_input" onkeydown="Javascript: if (event.keyCode==13) load_people_list('Main Driver');"/>
<br>
<br>
<span class="secondary_heading">Driver Status</span>
<hr/>
<?php echo form_dropdown('driver_status_dropdown',$driver_status_options,$status,'id="driver_status_dropdown" onchange="load_people_list(\'Main Driver\')" class="left_bar_input"');?>
<?php //echo form_dropdown('applicant_status_dropdown',$apllicant_status_options,$status,'id="applicant_status_dropdown" onchange="load_people_list(\'Driver\')" class="left_bar_input" style="display:none;"');?>
<br>