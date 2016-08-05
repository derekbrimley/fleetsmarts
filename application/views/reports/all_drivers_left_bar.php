<script>
	$('#start_date_filter').datepicker({ showAnim: 'blind' });
	$('#end_date_filter').datepicker({ showAnim: 'blind' });
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	
	<form id="all_drivers_form" name="all_drivers_form">
		<br>
		<span style="font-weight:bold;">Dates</span>
		<hr/>
		<input class="left_bar_input" type="text" id="start_date_filter" name="start_date_filter" placeholder="After"/>
		<br>
		<input class="left_bar_input" type="text" id="end_date_filter" name="end_date_filter" placeholder="Before"/>
		<br><br>
		<button type="button" class="jq_button left_bar_input" onclick="load_all_drivers_report()">Get Drivers</button>
	</form>
</div>