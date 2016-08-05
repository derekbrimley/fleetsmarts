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

<html>
	<head>
		<title><?php echo $title;?></title>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
		<link href='http://fonts.googleapis.com/css?family=Homemade+Apple' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
		
		<?php include("bills/bills_script.php"); ?>

		<script>
		</script>
	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" style="width:175px">
				<button class='left_bar_button jq_button' id="log_entry" onclick="$('#create_new_invoice').dialog('open');">New Bill</button>
				<br>
				<br>
				<span style="font-weight:bold;" class="heading">View</span>
				<hr/>
				<div id="old_bills" class="left_bar_link_div" style="with:145px; font-weight:bold;" onclick="load_bills_view('Old Bills')">
					Current Bills
				</div>
				<div id="new_bills" class="left_bar_link_div" style="with:145px;" onclick="load_bills_view('New Bills')">
					Incoming Bills
				</div>
				<br>
				<div id="filter_div">
					<span style="font-weight:bold;" class="heading">Filters</span>
					<hr/>
					<div style="margin-top:15px;" id="scrollable_filter_div"  class="scrollable_div">
						<?php $attributes = array('name'=>'filter_form','id'=>'filter_form')?>
						<?=form_open('invoices/load_report',$attributes);?>
							<span style="font-weight:bold;">Payer</span>
							<hr/>
							<?php echo form_dropdown('business_user',$business_users_options,'All','id="business_user" onChange="business_user_selected()" class="left_bar_input"');?>
							<br>
							<br>
							<div id="relationship_dropdown_div">
								<span style="font-weight:bold;">Vendor</span>
								<hr/>
								<?php
									$options = array(
										"All"	=> 	"All",
									);
								?>
								<?php echo form_dropdown('relationship_id',$options,'All','id="relationship_id" onChange="relationship_filter_selected()" class="left_bar_input"');?>
								<br>
								<br>
							</div>
							<div id="account_dropdown_div">
								<span style="font-weight:bold;">Account</span>
								<hr/>
								<?php
									$options = array(
										"All"	=> 	"All",
									);
								?>
								<?php echo form_dropdown('relationship_account_id',$options,'All','id="relationship_account_id" onChange="load_report()" class="left_bar_input"');?>
								<br>
								<br>
							</div>
							<span style="font-weight:bold;">Invoice Type</span>
							<hr/>
							<?php
								$options = array(
									"All"	=> 	"All",
									"Expense Incurred"	=>	"Expense Incurred",
									"Deposit Payable"	=>	"Deposit Payable",
								);
							?>
							<?php echo form_dropdown('invoice_type',$options,'All','id="invoice_type" onChange="business_user_selected()" class="left_bar_input"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Dates</span>
							<hr/>
							<input class="left_bar_input" type="text" id="after_date_filter" name="after_date_filter" onchange="business_user_selected()" placeholder="After"/>
							<br>
							<input class="left_bar_input" type="text" id="before_date_filter" name="before_date_filter" onchange="business_user_selected()" placeholder="Before"/>
							<br>
							<br>
							<span style="font-weight:bold;">Status</span>
							<hr/>
							<?php
								$options = array(
									"All"	=> 	"All",
									"Open"	=>	"Open",
									"Closed"	=>	"Closed",
								);
							?>
							<?php echo form_dropdown('bill_status',$options,'Open','id="bill_status" onChange="load_report()" class="left_bar_input"');?>
							<br>
							<br>
						</form>
						<span style="font-weight:bold;" class="heading">Actions</span>
						<hr/>
						<div>
							<button disabled id="view_payment_button" style="height:25px;" class='left_bar_button jq_button_disabled' onclick="$('#view_payment').dialog('open');">View Payment</button>
						</div>
					</div>
				</div>
				<div id="new_bills_filter_div" style="display:none;">
					<span style="font-weight:bold;" class="heading">Filters</span>
					<hr/>
					<div style="margin-top:15px;" id="scrollable_filter_div"  class="scrollable_div">
						<?php $attributes = array('name'=>'new_bills_filter_form','id'=>'new_bills_filter_form')?>
						<?=form_open('invoices/load_report',$attributes);?>
							<span style="font-weight:bold;">Payer</span>
							<hr/>
							<?php echo form_dropdown('payer_id',$business_users_options,'All','id="payer_id" onChange="load_new_bills_report()" class="left_bar_input"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Sent From</span>
							<hr/>
							<?php echo form_dropdown('sent_from_id',$business_users_options,'All','id="sent_from_id" onChange="load_new_bills_report()" class="left_bar_input"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Dates</span>
							<hr/>
							<input class="left_bar_input" type="text" id="new_bill_after_date_filter" name="new_bill_after_date_filter" onchange="load_new_bills_report()" placeholder="After"/>
							<br>
							<input class="left_bar_input" type="text" id="new_bill_before_date_filter" name="new_bill_before_date_filter" onchange="load_new_bills_report()" placeholder="Before"/>
							<br>
							<br>
						</form>
					</div>
				</div>
			</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header">
					<span style="font-weight:bold;">Bills</span>
					<div style="float:right; width:25px;">
						<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
						<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_report()" />
					</div>
				</div>
				<!-- REPORT GOES HERE !-->
			</div>
			
		</div>
	</body>
	
	<div id="add_invoice_notes" title="Add Invoice Note" style="padding:10px; display:none;">
		<div>
			<span id="notes_header" style="font-weight:bold;">Invoice Notes</span>
			<br>
			<br>
			<div id="invoice_notes_ajax_div" style="height:215px; overflow:auto">
				<!-- AJAX WILL POPULATE THIS !-->
			</div>
		</div>
		<div style="position:absolute; bottom:0">
			<?php $attributes = array('name'=>'add_invoice_note_form','id'=>'add_invoice_note_form', )?>
			<?=form_open('invoice/add_invoice_note/',$attributes);?>
				Add Note:
				<input type="hidden" id="invoice_id" name="invoice_id">
				<textarea style="width:400px;" rows="3" id="new_note" name="new_note"></textarea>
			</form>
		</div>
	</div>
	
	<input type="hidden" id="bill_holder_payer_id" name="bill_holder_payer_id" value=""/>
		
	<div id="create_new_invoice" title="New Bill" style="display:none;">
		<div id="pre_new_invoice_div" name="pre_new_invoice_div">
			<?php $attributes = array('id' => 'customer_vendor_selection_form'); ?>
			<?=form_open('invoice/customer_vendor_selected',$attributes)?>
				<input type="hidden" id="bill_holder_id" name="bill_holder_id" value=""/>
				<table style="font-size:14px; width:400px; margin:auto; margin-top:20px;">
					<tr id="payer_row">
						<td style="width:180px;">Payer</td>
						<td>
							<?php echo form_dropdown('business_user_id',$business_users_options,'Select','id="business_user_id" onChange="new_invoice_business_user_selected()" class="left_bar_input"');?>
						</td>
					</tr>
				</table>
				<div id="member_selection_table" style="color:black; font-size:14px; width:400px; margin:auto;">
					<!--AJAX GOES HERE !-->
				</div>
				<table style="font-size:14px; width:400px; margin:auto;">
					<tr id="bill_type_row" style="display:none;">
						<td style="width:180px;">Bill Type</td>
						<td>
							<?php $options = array(
								'Select'  	=> 'Select' ,
								'Business Expense'  => 'Business Expense',
								'Deposit Requested'  => 'Deposit Requested',
								'Ticket Expense'  => 'Ticket Expense',
								); ?>
							<?php echo form_dropdown('bill_type',$options,"Select",'id="bill_type" onChange="bill_type_selected()" class="left_bar_input"');?>
						</td>
					</tr>
					<tr id="new_bill_ticket_row" style="display:none;">
						<td style="width:180px;">Ticket</td>
						<td>
							<?php echo form_dropdown('new_bill_ticket',$ticket_options,"Select",'id="new_bill_ticket" onChange="load_customer_vendor_selection_div()" class="left_bar_input"');?>
						</td>
					</tr>
					<tr id="payment_method_row" style="display:none;">
						<td style="width:180px;">Payment Method</td>
						<td>
							<?php $options = array(
								'Select'  	=> 'Select' ,
								'FleetProtect'  => 'FleetProtect',
								'Next Settlement'  => 'Next Settlement',
								'Dispatch Expense'  => 'Dispatch Expense',
								); ?>
							<?php echo form_dropdown('payment_method',$options,"Select",'id="payment_method" onChange="load_customer_vendor_selection_div()" class="left_bar_input"');?>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div id="new_relationship_selection_div">
			<!-- AJAX GOES HERE!-->
		</div>
		<div id="success_div" style="font-size:14px; text-align:center; margin-top:25px; display:none;">
			Creating New Invoice!
		</div>
	</div>
	
	<div id="view_payment" title="Payment View" style="display:none;">
		<!-- AJAX GOES HERE!-->
	</div>
	
	
	<div id="new_bill_dialog" title="Enter New Bill" style="display:none;">
		<!-- AJAX GOES HERE!-->
	</div>
</html>