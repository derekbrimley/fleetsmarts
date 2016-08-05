<script>
	//$('#start_date_filter').datepicker({ showAnim: 'blind' });
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
	<?=form_open('reports/load_fuel_report',$attributes);?>
		<br>
		<span style="font-weight:bold;">Drivers</span>
		<hr/>
		<?php echo form_dropdown('driver_company_id',$client_options,"Select",'id="driver_company_id" style="" class="left_bar_input" onchange="load_fleetprotect_account_report()"');?>
	</form>
</div>