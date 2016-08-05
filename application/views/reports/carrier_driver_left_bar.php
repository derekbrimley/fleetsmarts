<script>
	$('#start_date_filter').datepicker({ showAnim: 'blind' });
	$('#end_date_filter').datepicker({ showAnim: 'blind' });
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
	<?=form_open('reports/load_carrier_driver_report',$attributes);?>
		<br>
		<span style="font-weight:bold;">Carrier</span>
		<hr/>
		<?php echo form_dropdown('carrier_dropdown',$carriers_dropdown_options,"Select",'id="carrier_dropdown" style="" class="left_bar_input" onchange="load_carrier_driver_report()"');?>
		<br>
		<br>
		<span style="font-weight:bold;">Date Range</span>
		<hr/>
		<input class="left_bar_input" type="text" id="start_date_filter" name="start_date_filter" onchange="load_carrier_driver_report()" placeholder="After"/>
		<br>
		<input class="left_bar_input" type="text" id="end_date_filter" name="end_date_filter" onchange="load_carrier_driver_report()" placeholder="Before"/>
		<br>
	</form>
</div>