<?php
	$status_options = [
		"All" => "All",
		"Complete" => "Complete",
		"Incomplete" => "Incomplete"
	];
?>
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

		<?php include("trippak/trippak_script.php"); ?>
		
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
				<br>
				<br>
				<div>
					<div id="filter_list" class="scrollable_div">
						<form id="filter_form"/>
							<span class="heading">Search</span>
								<hr/>
								<input type="text" id="search_term" name="search_term" class="left_bar_input" onchange="load_trippak_report()" onkeydown="Javascript: if (event.keyCode==13) load_trippak_report();" placeholder="Load Number">
								<br>
								<br>
							<span class="heading">Filters</span>
							<hr/>
							<br>
							<span style="font-weight:bold;">Status</span>
							<hr/>
							<?php echo form_dropdown('status_dropdown',$status_options,"All",'id="status_dropdown" style="" class="left_bar_input" onchange="load_trippak_report()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Carrier</span>
							<hr/>
							<?php echo form_dropdown('carrier_dropdown',$carrier_options,"All",'id="carrier_dropdown" style="" class="left_bar_input" onchange="load_trippak_report()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Driver</span>
							<hr/>
							<?php echo form_dropdown('driver_dropdown',$clients_dropdown_options,"All",'id="driver_dropdown" style="" class="left_bar_input" onchange="load_trippak_report()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Scan Date</span>
							<hr/>
							<input type="text" id="scan_start_date_filter" name="scan_start_date_filter" class="left_bar_input datepicker" onchange="load_trippak_report()" placeholder="After"/>
							<br>
							<input type="text" id="scan_end_date_filter" name="scan_end_date_filter" class="left_bar_input datepicker" onchange="load_trippak_report()" placeholder="Before"/>
							<br>
							<br>
						</form>
					</div>
				</div>
			</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header" style="">
					<span style="font-weight:bold;">Trippak</span>
					<div style="float:right; width:25px;">
						<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
						<img id="refresh_list" name="refresh_list" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_trippak_report()" />
					</div>
				</div>
			</div>
		</div>
	</body>

	<div title="Attachment Upload" id="file_upload_dialog" name="file_upload_dialog" style="display:hidden;" >
		<!-- AJAX GOES HERE !-->
	</div>

</html>