<span class="heading">Filters</span>
<hr/>
<div id="filter_list" class="scrollable_div">
	<form id="filter_form">
		<br>
		<span style="font-weight:bold;">Driver</span>
		<hr/>
		<?php echo form_dropdown('clients_dropdown_options',$clients_dropdown_options,"Select",'id="clients_dropdown_options" style="" class="left_bar_input" onchange="load_driver_hold_report()"');?>
	</form>
</div>