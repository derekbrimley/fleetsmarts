<span class="heading">Filters</span>
<hr/>
<form id="truck_filter_form" name="truck_filter_form">
	<?php $options = array(
		'All Statuses'  	=> 'All Statuses',
		'On the road'  	=> 'On the road',
		'In the shop'  => 'In the shop',
		'Subtruck'  => 'Subtruck',
		'Returned'  => 'Returned',
		); ?>
	<?php echo form_dropdown('truck_status',$options,"On the road",'id="truck_status" class="left_bar_input"  onchange="load_truck_list()"');?>
	<br><br>
	<?php echo form_dropdown('fm_filter_dropdown',$fleet_manager_dropdown_options,"All",'id="fm_filter_dropdown" style="" class="left_bar_input" onchange="load_truck_list()"');?>
	<br><br>
	<?php echo form_dropdown('dm_filter_dropdown',$driver_manager_dropdown_options,"All",'id="dm_filter_dropdown" style="" class="left_bar_input" onchange="load_truck_list()"');?>
	<br><br>
	<?php echo form_dropdown('vendor_dropdown_options',$vendor_dropdown_options,"All",'id="fm_filter_dropdown" style="" class="left_bar_input" onchange="load_truck_list()"');?>
</form>
<br><br>