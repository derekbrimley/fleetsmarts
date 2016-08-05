<span class="heading">Filters</span>
<hr/>
<form id="trailer_filter_form" name="trailer_filter_form">
	<?php $options = array(
		'All Statuses'  	=> 'All Statuses',
		'On the road'  	=> 'On the road',
		'In the shop'  => 'In the shop',
		'Subtruck'  => 'Subtruck',
		'Returned'  => 'Returned',
		); ?>
	<?php echo form_dropdown('trailer_status',$options,"On the road",'id="trailer_status" class="left_bar_input"  onchange="load_trailer_list()"');?>
	<br><br>
	<?php echo form_dropdown('vendor_dropdown_options',$vendor_dropdown_options,"All",'id="vendor_dropdown_options" style="" class="left_bar_input" onchange="load_trailer_list()"');?>
</form>
<br><br>