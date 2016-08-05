<style>
	hr
	{
		width:156px;
		margin:0px;
		margin-top:7px;
		margin-bottom:7px;
	}
	
	.blue_border
	{
		box-shadow: 0 0 0 3px #6295FC inset;
	}
</style>
<script>
	$('#after_date_filter').datepicker({ showAnim: 'blind' });
	$('#before_date_filter').datepicker({ showAnim: 'blind' });
	
	
	
</script>
<span class="" style="font-weight:bold;">Transaction Type</span>
<hr/>
<?php
	$options = array(
			"All" => "All",
			"Expense" => "Expenses",
			"Revenue" => "Revenues",
			"Transfer" => "Transfers",
			);
?>
<?php echo form_dropdown('expense_type_dropdown',$options,"Expense",'id="expense_type_dropdown" style="" class="left_bar_input" onchange="load_report()"');?>
<br>
<br>
<br>

<span style="font-weight:bold;">Issuer</span>
<hr/>
<?php echo form_dropdown('issuer_sidebar_dropdown',$issuer_sidebar_options,'All','onChange="load_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Owner</span>
<hr/>
<?php echo form_dropdown('bill_owner_sidebar_dropdown',$bill_owner_sidebar_options,'All','onChange="load_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Category</span>
<hr/>
<?php 
	$options = array(
			'All' => 'All',
			'Personal Advance' => 'Personal Advance',
			'Pay Out' => 'Pay Out',
			'Tires' => 'Tires',
			'Overdue Service'    => 'Overdue Service',
			'Other Client Damage' => 'Other Client Damage',
			'Driver Equipment'    => 'Driver Equipment',
			'Hotel'    => 'Hotel',
			'Travel'    => 'Travel',
			'Recruiting'    => 'Recruiting',
			'Tolls'    => 'Tolls',
			'Fuel Permit'    => 'Fuel Permit',
			'Lumper'    => 'Lumper',
			'Randy'    => 'Randy',
			'Trailer Maintenance'    => 'Trailer Maintenance',
			'Office Expense'    => 'Office Expense',
			'GPS'    => 'GPS',
			'Phones'    => 'Phones',
			'Other BE'    => 'Other BE',
			'Unassigned' => 'Unassigned',
	); 
?>
<?php echo form_dropdown('category_dropdown',$category_sidebar_options,"All",'id="category_dropdown" onChange="load_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Dates</span>
<hr/>
<input class="left_bar_input" type="text" id="after_date_filter" name="after_date_filter" onchange="load_report()" placeholder="After"/>
<br>
<input class="left_bar_input" type="text" id="before_date_filter" name="before_date_filter" onchange="load_report()" placeholder="Before"/>
<br>
<br>
<br>
<span style="font-weight:bold;">Locked</span>
<hr/>
<?php 
$options = array(
		'All' => 'All',
		'Locked' => 'Locked',
		'Unlocked' => 'Unlocked',
); 
?>
<?php echo form_dropdown('locked_dropdown',$options,"All",'id="locked_dropdown" onChange="load_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Recorded</span>
<hr/>
<?php 
$options = array(
		'All' => 'All',
		'Unrecorded' => 'Unrecorded',
		'Recorded' => 'Recorded',
); 
?>
<?php echo form_dropdown('recorded_dropdown',$options,"Unrecorded",'id="recorded_dropdown" onChange="load_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Source</span>
<hr/>
<a style="margin-right:9px;" href="javascript:clear_sources();">Clear All</a>|<a href="javascript:select_all_sources();">Select All</a>
<br>
<br>
<table>
	<?php foreach($source_accounts_options as $source_account): ?>
		<tr style="height:25px;">
			<td style="width:20px; vertical-align:middle;">
				<input id="<?=$source_account["account_id"]?>_cb" name="<?=$source_account["account_id"]?>_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_report()">
				<input type="hidden" id="get_<?=$source_account["account_id"]?>" name="get_<?=$source_account["account_id"]?>" value="">
			</td>
			<td style="vertical-align:middle; max-width:145px;" class="ellipsis" title="<?=$source_account["account_name"]?>">
				<?=$source_account["account_name"]?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>


