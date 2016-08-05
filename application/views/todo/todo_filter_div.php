<style>
	hr
	{
		width:156px;
		margin:0px;
		margin-top:7px;
		margin-bottom:7px;
	}
	
	.blue_border
	{
		box-shadow: 0 0 0 3px #6295FC inset;
	}
</style>
<script>
	$('#after_due_date_filter').datepicker({ showAnim: 'blind' });
	$('#before_due_date_filter').datepicker({ showAnim: 'blind' });
	$('#after_completion_date_filter').datepicker({ showAnim: 'blind' });
	$('#before_completion_date_filter').datepicker({ showAnim: 'blind' });
</script>

<span style="font-weight:bold;">Owner</span>
<hr/>
<?php echo form_dropdown('owner_filter_dropdown',$user_options,'All','id="owner_filter_dropdown" onChange="load_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Manager</span>
<hr/>
<?php echo form_dropdown('manager_filter_dropdown',$user_options,"All",'id="manager_filter_dropdown" onChange="load_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Type</span>
<hr/>
<?php
	$options = array(
			"All" => "All",
			"Bill" => "Bill",
			"Log" => "Log",
			"Load" => "Load",
			"PO" => "PO",
			"Ticket" => "Ticket",
			"ToDo" => "ToDo",
			);
?>
<?php echo form_dropdown('type_dropdown',$options,"All",'id="type_dropdown" onChange="load_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Due Date</span>
<hr/>
<input class="left_bar_input" type="text" id="after_due_date_filter" name="after_due_date_filter" onchange="load_report()" placeholder="After"/>
<br>
<input class="left_bar_input" type="text" id="before_due_date_filter" name="before_due_date_filter" onchange="load_report()" placeholder="Before"/>
<br>
<br>
<span style="font-weight:bold;">Completion Date</span>
<hr/>
<input class="left_bar_input" type="text" id="after_completion_date_filter" name="after_completion_date_filter" onchange="load_report()" placeholder="After"/>
<br>
<input class="left_bar_input" type="text" id="before_completion_date_filter" name="before_completion_date_filter" onchange="load_report()" placeholder="Before"/>
<br>
<br>
<span style="font-weight:bold;">ToDo Status</span>
<hr/>
<?php
	$options = array(
			"All" => "All",
			"Open" => "Open",
			"Closed" => "Closed",
			);
?>
<?php echo form_dropdown('status_dropdown',$options,"Open",'id="status_dropdown" onChange="load_report()" class="left_bar_input"');?>


