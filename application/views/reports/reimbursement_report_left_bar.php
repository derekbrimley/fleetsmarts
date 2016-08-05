<script>
	load_reimbursement_report();
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
	<?=form_open('reports/load_fuel_report',$attributes);?>
		<br>
		<span style="font-weight:bold;">Drivers</span>
		<hr/>
		<?php echo form_dropdown('driver_filter_dropdown',$driver_dropdown_options,"All",'id="driver_filter_dropdown" style="" class="left_bar_input" onchange="load_reimbursement_report()"');?>
		<br>
	</form>
</div>