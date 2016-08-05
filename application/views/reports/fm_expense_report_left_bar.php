<script>
	$('#start_date_filter').datepicker({ showAnim: 'blind' });
	$('#end_date_filter').datepicker({ showAnim: 'blind' });
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
	<?=form_open('reports/load_fuel_report',$attributes);?>
		<br>
		<span style="font-weight:bold;">Fleet Manager</span>
		<hr/>
		<?php echo form_dropdown('fleet_managers_dropdown',$fleet_managers_dropdown_options,"Select",'id="fleet_managers_dropdown" style="" class="left_bar_input" onchange="load_fm_expense_report()"');?>
		<br>
		<br>
		<span style="font-weight:bold;">Date</span>
		<hr/>
		<input type="text" id="start_date_filter" name="start_date_filter" onchange="load_fm_expense_report()" placeholder="After"/>
		<br>
		<input type="text" id="end_date_filter" name="end_date_filter" onchange="load_fm_expense_report()" placeholder="Before"/>
		<br>
	</form>
</div>