<script>
	load_fuel_report();
	
	$('#start_date_filter').datepicker({ showAnim: 'blind' });
	$('#end_date_filter').datepicker({ showAnim: 'blind' });
		
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
	<?=form_open('reports/load_fuel_report',$attributes);?>
		<br>
		<span style="font-weight:bold;">Drivers</span>
		<hr/>
		<?php echo form_dropdown('main_driver_filter_dropdown',$main_driver_dropdown_options,"All",'id="main_driver_filter_dropdown" style="" class="left_bar_input" onchange="load_fuel_report()"');?>
		<br>
		<br>
		<span style="font-weight:bold;">Equipment</span>
		<hr/>
		<?php echo form_dropdown('truck_filter_dropdown',$truck_dropdown_options,"All",'id="truck_filter_dropdown" style="" class="left_bar_input" onchange="load_fuel_report()"');?>
		<br>
		<br>
		<span style="font-weight:bold;">Dates</span>
		<hr/>
		<input type="text" id="start_date_filter" name="start_date_filter" onchange="load_fuel_report()" placeholder="Start Date"/>
		<br>
		<input type="text" id="end_date_filter" name="end_date_filter" onchange="load_fuel_report()" placeholder="End Date"/>
		<br>
		<br>
	</form>
</div>