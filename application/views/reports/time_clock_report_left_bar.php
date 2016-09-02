<script>
	$('#start_date_filter').datepicker({ showAnim: 'blind' });
	$('#end_date_filter').datepicker({ showAnim: 'blind' });
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	
	<form id="filter_form" name="filter_form">
		<br>
		<span style="font-weight:bold;">User</span>
		<hr/>
		<?php echo form_dropdown('user_filter',$user_sidebar_options,"Select",'id="user_filter" class="left_bar_input" onchange="load_time_clock_report()"') ?>
		<br>
		<br>
		<span style="font-weight:bold;">Dates</span>
		<hr/>
		<input class="left_bar_input" type="text" id="start_date_filter" name="start_date_filter" placeholder="After" onchange="load_time_clock_report()"/>
		<br>
		<input class="left_bar_input" type="text" id="end_date_filter" name="end_date_filter" placeholder="Before" onchange="load_time_clock_report()"/>
		<br><br>
	</form>
</div>