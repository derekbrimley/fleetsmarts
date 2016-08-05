<div id="left_bar">
	<button class='left_bar_button jq_button' id="new_equipment" onclick="$('#new_equipment_dialog').dialog('open');">New Equipment</button>
	<button class='left_bar_button jq_button' id="new_quote_button" onclick="$('#new_quote_dialog').dialog('open');" style="display:none;">New Policy</button>
	<br>
	<br>
	<div id="scrollable_left_bar" class="scrollable_div" style="overflow-x:hidden;width:165px;">
		<span class="heading">View Type</span>
		<hr/>
		<div id="truck_left_bar_link_div" class="left_bar_link_div" style="" onclick="load_trucks()">
			Trucks
		</div>
		<div id="trailer_left_bar_link_div" class="left_bar_link_div" style="" onclick="load_trailers()">
			Trailers
		</div>
		<div id="insurance_left_bar_link_div" class="left_bar_link_div" style="" onclick="load_insurance()">
			Insurance
		</div>
		<br>
		<br>
		<div id="filter_div">
			<!-- AJAX WILL GO HERE !-->
			
		</div>
		<div id="equipment_list_div">
			<!-- AJAX WILL GO HERE !-->
		</div>
	</div>
</div>

