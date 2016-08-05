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

<span style="font-weight:bold;">Dates</span>
<hr/>
<input class="left_bar_input" type="text" id="after_date_filter" name="after_date_filter" onchange="load_po_report()" placeholder="After"/>
<br>
<input class="left_bar_input" type="text" id="before_date_filter" name="before_date_filter" onchange="load_po_report()" placeholder="Before"/>
<br>
<br>
<span style="font-weight:bold;">Issuer</span>
<hr/>
<?php echo form_dropdown('issuer_sidebar_dropdown',$issuer_sidebar_options,'All','onChange="load_po_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Owner</span>
<hr/>
<?php echo form_dropdown('bill_owner_sidebar_dropdown',$bill_owner_sidebar_options,'All','onChange="load_po_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Category</span>
<hr/>
<?php echo form_dropdown('category_dropdown',$category_sidebar_options,"All",'id="category_dropdown" onChange="load_po_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Approver</span>
<hr/>
<?php echo form_dropdown('approver_dropdown',$approver_sidebar_options,"All",'id="approver_dropdown" onChange="load_po_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">Account</span>
<hr/>
<?php echo form_dropdown('account_dropdown',$account_sidebar_options,"All",'id="account_dropdown" onChange="load_po_report()" class="left_bar_input"');?>
<br>
<br>
<span style="font-weight:bold;">PO Status</span>
<hr/>
<?php
	$options = array(
			"All" => "All",
			"Approved" => "Approved",
			"Unapproved" => "Unapproved",
			);
?>
<?php echo form_dropdown('status_dropdown',$options,"All",'id="status_dropdown" onChange="load_po_report()" class="left_bar_input"');?>


