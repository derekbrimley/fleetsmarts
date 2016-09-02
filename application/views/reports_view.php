<style>
	hr
	{
		width:156px;
		margin:0px;
		margin-top:7px;
		margin-bottom:7px;
	}
</style>
<html>
	<!-- 	TODO: 								!-->
	<!-- 	SEARCH FUNCTION 					!-->
	<!-- 	ACTIVE/INACTIVE FILTER BOX			!-->
	<!-- 	FLEET MANAGER FILTER BOX			!-->
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
		
		<?php include("reports/reports_script.php"); ?>

	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" class="scrollable_div" style="width:175px">
				<span class="heading">Reports</span>
				<hr/>
				<?php
					$options = array(
							"Select Report" => "Select Report",
							"All Drivers Report" => "All Drivers Report",
							"Arrowhead Expense Report" => "Arrowhead Expense Report",
							"Carrier-Driver Report" => "Carrier-Driver Report",
							"Check Call Report" => "Check Call Report",
							"Deduction Report" => "Deduction Report",
							"Driver Manager Report" => "Driver Manager Report",
							"Driver Accounts" => "Driver Accounts",
							"Driver Hold Report" => "Driver Hold Report",
							"DTR Export" => "DTR Export",
							"End Leg Export" => "End Leg Export",
							"Expenses" => "Expenses",
							"Financial Report" => "Financial Report",
							"FleetProtect Account" => "FleetProtect Account",
							"FM Expenses" => "FM Expenses",
							"Fuel Report" => "Fuel Report",
							"Funding Report" => "Funding Report",
							"Income Statement" => "Income Statement",
							"Missing Paperwork" => "Missing Paperwork",
							"Reefer Report" => "Reefer Report",
							"Reimbursement Report" => "Reimbursement Report",
							"Time and Attendance" => "Time and Attendance",
							"Time Clock Report" => "Time Clock Report",
							"Transactions Export" => "Transactions Export"
							);
				?>
				<?php echo form_dropdown('report_dropdown',$options,"Select Report",'id="report_dropdown" style="" class="left_bar_input" onchange="load_report()"');?>
				<br>
				<br>
				<br>
				
				<div id="report_left_bar">
					<!-- AJAX GOES HERE !-->
				</div>
				
			</div>
			
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header">
					<span style="font-weight:bold;">Reports</span>
					<div style="float:right; width:25px;">
						<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
						<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px; display:none;" onclick="" />
					</div>
				</div>
			</div>
			
		</div>
	</body>
	
	<div id="log_event_dialog" title="Log New Event">
		<!-- AJAX GOES HERE !-->
	</div>
	
</html>