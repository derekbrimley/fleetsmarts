<script>
</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
	<?=form_open('reports/load_missing_paperwork_report',$attributes);?>
		<br>
		<span style="font-weight:bold;">Fleet Manager</span>
		<hr/>
		<?php echo form_dropdown('fleet_managers_dropdown',$fleet_managers_dropdown_options,"Select",'id="fleet_managers_dropdown" style="" class="left_bar_input" onchange="load_missing_paperwork_report()"');?>
		<br>
		<br>
		<span style="font-weight:bold;">Driver</span>
		<hr/>
		<?php echo form_dropdown('driver_dropdown',$driver_dd_options,"Select",'id="driver_dropdown" style="" class="left_bar_input" onchange="load_missing_paperwork_report()"');?>
		<br>
		<br>
		<span style="font-weight:bold;">Digital Status</span>
		<hr/>
		<?php $options = array(
			'All' => 'All Statuses',
			'Missing'    => 'Missing',
			'Received'  => 'Received',
			); 
		?>
		<?php echo form_dropdown('digital_status_dropdown',$options,"All",'id="digital_status_dropdown" style="" class="left_bar_input" onchange="load_missing_paperwork_report()"');?>
		<br>
		<br>
		<span style="font-weight:bold;">HC Status</span>
		<hr/>
		<?php $options = array(
			'All' => 'All Statuses',
			'Missing'    => 'Missing',
			'Received'  => 'Received',
			); 
		?>
		<?php echo form_dropdown('hc_status_dropdown',$options,"All",'id="hc_status_dropdown" style="" class="left_bar_input" onchange="load_missing_paperwork_report()"');?>
		<br>
	</form>
</div>