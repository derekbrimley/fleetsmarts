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
		
		<?php include("invoices/invoices_script.php"); ?>

		<script>
		</script>
	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" style="width:175px">
				<button class='left_bar_button jq_button' id="log_entry" onclick="$('#create_new_invoice').dialog('open');">New Invoice</button>
				<br>
				<br>
				<span style="font-weight:bold;" class="heading">Filters</span>
				<hr/>
				<div style="margin-top:15px;" id="scrollable_filter_div"  class="scrollable_div">
					<?php $attributes = array('name'=>'filter_form','id'=>'filter_form')?>
					<?=form_open('invoices/load_report',$attributes);?>
						<span style="font-weight:bold;">Payee</span>
						<hr/>
						<?php echo form_dropdown('business_user',$business_users_options,'All','id="business_user" onChange="business_user_selected()" class="left_bar_input"');?>
						<br>
						<br>
						<div id="relationship_dropdown_div">
							<span style="font-weight:bold;">Customer</span>
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
								"Revenue Generated"	=>	"Revenue Generated",
								"Deposit Receivable"	=>	"Deposit Receivable",
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
							<?php echo form_dropdown('invoice_status',$options,'Open','id="invoice_status" onChange="load_report()" class="left_bar_input"');?>
						<br>
						<br>
					</form>
				</div>
			</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header">
					<span style="font-weight:bold;">Invoices</span>
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
	
	<div id="create_new_invoice" title="Create New Invoice" style="display:none;">
		<div id="pre_new_invoice_div">
			<?php $attributes = array('id' => 'customer_vendor_selection_form'); ?>
			<?=form_open('invoice/customer_vendor_selected',$attributes)?>
				<table style="font-size:14px; width:400px; margin:auto; margin-top:20px;">
					<tr>
						<td style="width:180px;">Business User</td>
						<td>
							<?php echo form_dropdown('business_user_id',$business_users_options,'All','id="business_user_id" onChange="new_invoice_business_user_selected()" class="left_bar_input"');?>
						</td>
					</tr>
				</table>
				<div id="member_selection_table" style="font-size:14px; color:black; width:400px; margin:auto;">
					<!--AJAX GOES HERE !-->
				</div>
				<table style="font-size:14px; width:400px; margin:auto;">
					<tr id="payment_method_row" style="display:none;">
						<td style="width:180px;">Payment Method</td>
						<td>
							<?php $options = array(
								'Select'  	=> 'Select' ,
								'FleetProtect'  => 'FleetProtect',
								'Next Settlement'  => 'Next Settlement',
								); ?>
							<?php echo form_dropdown('payment_method',$options,"Select",'id="payment_method" onChange="payment_method_selected()" class="left_bar_input"');?>
						</td>
					</tr>
					<tr id="member_invoice_type_row" style="display:none;">
						<td>Invoice Type</td>
						<td>
							<?php $options = array(
								'Select'  	=> 'Select' ,
								'Revenue Generated'  => 'Revenue Generated',
								'Request Deposit'  => 'Request Deposit',
								); ?>
							<?php echo form_dropdown('new_member_invoice_type',$options,"Select",'id="new_member_invoice_type" onChange="member_invoice_type_selected()" class="left_bar_input"');?>
						</td>
					</tr>
					<tr id="invoice_type_row" style="display:none;">
						<td style="width:180px;">Invoice Type</td>
						<td>
							<?php $options = array(
								'Select'  	=> 'Select' ,
								'Revenue Generated'  => 'Revenue Generated',
								'Request Deposit'  => 'Request Deposit',
								); ?>
							<?php echo form_dropdown('new_invoice_type',$options,"Select",'id="new_invoice_type" onChange="load_customer_vendor_selection_div()" class="left_bar_input"');?>
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
	
</html>