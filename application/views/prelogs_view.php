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
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
		<?php include("prelogs/prelogs_script.php"); ?>

	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" style="width:175px">
				<button class='left_bar_button jq_button' id="log_entry" onclick="">Pre-Log Event</button>
				<br>
				<br>
				<span class="heading">Filters</span>
				<hr/>
				<div id="filter_list" class="scrollable_div">
					<form id="filter_form">
						<br>
						<span style="font-weight:bold;">Equipment</span>
						<hr/>
						<?php echo form_dropdown('truck_filter_dropdown',$truck_dropdown_options,"All",'id="truck_filter_dropdown" style="" class="left_bar_input" onchange="load_log_list()"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Dates</span>
						<hr/>
						<input style="width:156px;" type="text" id="start_date_filter" name="start_date_filter" onchange="load_log_list()" placeholder="After"/>
						<br>
						<input style="width:156px;" type="text" id="end_date_filter" name="end_date_filter" onchange="load_log_list()" placeholder="Before"/>
						<br>
						<br>
					</form>
				</div>
			</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header">
					<span style="font-weight:bold;">Event Log</span>
					<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" height="20px" style="float:right; height:20px; padding-top:5px;" />
				</div>
			</div>
			
		</div>
	</body>
	
	<div id="log_event_dialog" title="Log New Event">
		<!-- AJAX GOES HERE !-->
	</div>
	
	<div title="Attachment Upload" id="file_upload_dialog" name="file_upload_dialog" style="display:hidden;" >
		<!-- AJAX GOES HERE !-->
	</div>
	
</html>