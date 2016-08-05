<?php
	date_default_timezone_set('America/Denver');
	$today_date = date("m/d/y");
?>

<script>
	$('#ins_snapshot_date').datepicker({ showAnim: 'blind' });
</script>

<span class="heading">Group By</span>
<hr/>
<div id="policy_left_bar_link_div" class="left_bar_link_div left_bar_link_selected" style="" onclick="insurance_group_by_item_selected('policies')">
	Policies
</div>
<div id="unit_left_bar_link_div" class="left_bar_link_div" style="" onclick="insurance_group_by_item_selected('units')">
	Units
</div>
<br>
<br>
<span class="heading">Filter</span>
<hr/>
<form id="insurance_filter_form" name="insurance_filter_form">
	<input type="hidden" id="group_by_selection" name="group_by_selection" value="policies"/>
	<input type="text" id="ins_snapshot_date" name="ins_snapshot_date" style="width:156px; height:25px; text-align:center;" value="<?=$today_date?>" onchange="load_ins_by_unit_summary()"  placeholder="Date"/>
</form>
<br><br>