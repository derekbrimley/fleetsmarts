<script>

</script>

<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
	<?=form_open('reports/load_fuel_report',$attributes);?>
		<br>
		<span style="font-weight:bold;">Fleet Manager</span>
		<hr/>
		<?php echo form_dropdown('fleet_managers_dropdown',$fleet_managers_dropdown_options,"Select",'id="fleet_managers_dropdown" style="" class="left_bar_input" onchange="load_dm_report()"');?>
		<br>
	</form>
</div>