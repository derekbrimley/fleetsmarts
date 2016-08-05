<html>
	<head>
		<title><?php echo $title;?></title>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
		<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>

		<?php include("billing/billing_script.php"); ?>
		
		<style>
			hr
			{
				width:156px;
				margin:0px;
				margin-top:7px;
				margin-bottom:7px;
			}
		</style>
	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" style="width:175px">
				<button class="left_bar_button jq_button" name="add_load_button" id="add_load_button" onclick='open_add_new_load_dialog()'>New Load</button>
				<br>
				<br>
				<div>
					<div id="filter_list" class="scrollable_div">
						<form id="filter_form"/>
						<span class="heading">Search</span>
							<hr/>
							<input type="text" id="search_term" name="search_term" class="left_bar_input" onchange="load_funding_report()" onkeydown="Javascript: if (event.keyCode==13) load_funding_report();" placeholder="Load Number">
							<br>
							<br>
						<span class="heading">Filters</span>
						<hr/>
							<br>
							<span style="font-weight:bold;">Funding Status</span>
							<hr/>
							<?php $options = array(
								'All' => 'All',
								'Open'    => 'Open',
								'Closed'  => 'Closed',
								); 
							?>
							<?php echo form_dropdown('closed_status_dropdown',$options,"Open",'id="closed_status_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
							<br>
							<?php $options = array(
								'All' => 'All',
								'Funded'    => 'Funded',
								'Unfunded'  => 'Unfunded',
								); 
							?>
							<?php echo form_dropdown('funding_status_dropdown',$options,"All",'id="funding_status_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
							<br>
							<?php $options = array(
								'All' => 'All',
								'Verified'    => 'Verified',
								'Unverified'  => 'Unverified',
								); 
							?>
							<?php echo form_dropdown('funding_verified_dropdown',$options,"All",'id="funding_verified_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Broker</span>
							<hr/>
							<?php echo form_dropdown('broker_dropdown',$broker_dropdown_options,"All",'id="broker_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Carrier</span>
							<hr/>
							<?php echo form_dropdown('carrier_dropdown',$billed_under_options,"All",'id="carrier_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Fleet Manager</span>
							<hr/>
							<?php echo form_dropdown('fleet_managers_dropdown',$fleet_managers_dropdown_options,"All",'id="fleet_managers_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Driver Manager</span>
							<hr/>
							<?php echo form_dropdown('driver_managers_dropdown',$dm_filter_dropdown_options,"All",'id="driver_managers_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">AR Specialist</span>
							<hr/>
							<?php echo form_dropdown('ar_specialist_dropdown',$ars_dropdown_options,"All",'id="ar_specialist_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Driver</span>
							<hr/>
							<?php echo form_dropdown('driver_dropdown',$clients_dropdown_options,"All",'id="driver_dropdown" style="" class="left_bar_input" onchange="load_funding_report()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Pushed Date</span>
							<hr/>
							<input type="text" id="push_start_date_filter" name="push_start_date_filter" class="left_bar_input datepicker" onchange="load_funding_report()" placeholder="After"/>
							<br>
							<input type="text" id="push_end_date_filter" name="push_end_date_filter" class="left_bar_input datepicker" onchange="load_funding_report()" placeholder="Before"/>
							<br>
							<br>
							<span style="font-weight:bold;">Drop Date</span>
							<hr/>
							<input type="text" id="drop_start_date_filter" name="drop_start_date_filter" class="left_bar_input" onchange="load_funding_report()" placeholder="After"/>
							<br>
							<input type="text" id="drop_end_date_filter" name="drop_end_date_filter" class="left_bar_input" onchange="load_funding_report()" placeholder="Before"/>
							<br>
							<br>
							<span style="font-weight:bold;">Billing Date</span>
							<hr/>
							<input type="text" id="billing_start_date_filter" name="billing_start_date_filter" class="left_bar_input" onchange="load_funding_report()" placeholder="After"/>
							<br>
							<input type="text" id="billing_end_date_filter" name="billing_end_date_filter" class="left_bar_input" onchange="load_funding_report()" placeholder="Before"/>
							<br>
							<br>
							<span style="font-weight:bold;">Funding Date</span>
							<hr/>
							<input type="text" id="funding_start_date_filter" name="funding_start_date_filter" class="left_bar_input" onchange="load_funding_report()" placeholder="After"/>
							<br>
							<input type="text" id="funding_end_date_filter" name="funding_end_date_filter" class="left_bar_input" onchange="load_funding_report()" placeholder="Before"/>
							<br>
							<br>
							<span style="font-weight:bold;">Hold Reason</span>
							<hr/>
							<?php echo form_dropdown('hold_reason_filter',$hold_reasons_options,"All",'id="hold_reason_filter" style="" class="left_bar_input" onchange="load_funding_report()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Billing Type</span>
							<hr/>
							<table>
								<tr style="height:25px;">
									<td style="width:20px; vertical-align:middle;">
										<input id="factor_cb" name="factor_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_funding_report()">
										<input type="hidden" id="get_factors" name="get_factors" value="">
									</td>
									<td style="vertical-align:middle;">
										Factor
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="direct_bill_cb" name="direct_bill_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_funding_report()">
										<input type="hidden" id="get_direct_bills" name="get_direct_bills" value="">
									</td>
									<td style="vertical-align:middle;">
										Direct Bill
									</td>
								</tr>
							</table>
							<br>
							<span class="heading">View</span>
							<hr/>
								<?php $options = array(
									'Invoices' => 'Invoices',
									'Updates'    => 'Updates',
									); 
								?>
								<?php echo form_dropdown('view_dropdown',$options,"Invoices",'id="view_dropdown" style="" class="left_bar_input" onchange="change_view()"');?>
								<br>
								<br>
						</form>
					</div>
				</div>
			</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header" style="">
					<span style="font-weight:bold;">Billing</span>
					<div style="float:right; width:25px;">
						<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
						<img id="refresh_list" name="refresh_list" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_funding_report()" />
					</div>
				</div>
			</div>
		</div>
	</body>
	
	<div title="Billing Notes" id="add_notes_dialog" name="add_notes_dialog" style="display:hidden;" >
		<!-- AJAX GOES HERE !-->
	</div>
	
	<div title="Attachment Upload" id="file_upload_dialog" name="file_upload_dialog" style="display:hidden;" >
		<!-- AJAX GOES HERE !-->
	</div>

	<div id="billing_checklist_dialog" title="Billing Checklist">
		<!-- AJAX GOES HERE !-->
	</div>
	
	<div id="process_audit_dialog" title="Process Audit">
		<!-- AJAX GOES HERE !-->
	</div>

</html>