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
		
		<?php include("logs/logs_script.php"); ?>

	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" style="width:175px">
				<button class='left_bar_button jq_button' id="log_entry" onclick="">Log Event</button>
				<br>
				<br>
				<span class="heading">Filters</span>
				<hr/>
					<div id="filter_list" class="scrollable_div">
						<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
						<?=form_open('logs/load_log',$attributes);?>
							<br>
							<span style="font-weight:bold;">Drivers</span>
							<hr/>
							<?php echo form_dropdown('main_driver_filter_dropdown',$main_driver_dropdown_options,"All",'id="main_driver_filter_dropdown" style="" class="left_bar_input" onchange="load_log_list()"');?>
							<br>
							<?php echo form_dropdown('codriver_filter_dropdown',$codriver_dropdown_options,"All",'id="codriver_filter_dropdown" style="" class="left_bar_input" onchange="load_log_list()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Equipment</span>
							<hr/>
							<?php echo form_dropdown('truck_filter_dropdown',$truck_dropdown_options,"All",'id="truck_filter_dropdown" style="" class="left_bar_input" onchange="load_log_list()"');?>
							<br>
							<?php echo form_dropdown('trailer_filter_dropdown',$trailer_dropdown_options,"All",'id="trailer_filter_dropdown" style="" class="left_bar_input" onchange="load_log_list()"');?>
							<br>
							<br>
							<span style="font-weight:bold;">Dates</span>
							<hr/>
							<input style="width:156px;" type="text" id="start_date_filter" name="start_date_filter" onchange="load_log_list()" placeholder="After"/>
							<br>
							<input style="width:156px;" type="text" id="end_date_filter" name="end_date_filter" onchange="load_log_list()" placeholder="Before"/>
							<br>
							<br>
							<span style="font-weight:bold;">Load</span>
							<hr/>
							<input style="width:156px;" type="text" id="load_filter" name="load_filter" onchange="load_log_list()" placeholder="Load Number"/>
							<br>
							<br>
							<span style="font-weight:bold;">Event Type</span>
							<hr/>
							<a style="margin-right:9px;" href="javascript:clear_events();">Clear All</a>|<a href="javascript:select_all_events();">Select All</a>
							<br>
							<br>
							<table>
								<tr style="height:25px;">
									<td style="width:20px; vertical-align:middle;">
										<input id="pick_cb" name="pick_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="width:30px; vertical-align:middle;">
										<img style='height:13px; position:relative; bottom:2px; margin-left:5px; margin-right:5px;' src="/images/log_pick.png" />
									</td>
									<td style="vertical-align:middle;">
										Pick
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="drop_cb" name="drop_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:13px; position:relative; bottom:2px; margin-left:5px; margin-right:5px;' src="/images/log_drop.png"/>
									</td>
									<td style="vertical-align:middle;">
										Drop
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="fuel_fill_cb" name="fuel_fill_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:16px; position:relative; bottom:0px; margin-left:5px; margin-right:5px;' src="/images/log_fuel_fill.png"/>
									</td>
									<td style="vertical-align:middle;">
										Fuel Fill
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="fuel_partial_cb" name="fuel_partial_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:16px; position:relative; bottom:0px; margin-left:5px; margin-right:5px;' src="/images/log_fuel_partial.png"/>
									</td>
									<td style="vertical-align:middle;">
										Fuel Partial
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="checkpoint_cb" name="checkpoint_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:16px; position:relative; left:2px; margin-left:5px; margin-right:5px;' src="/images/log_checkpoint.png"/>
									</td>
									<td style="vertical-align:middle;">
										Checkpoint
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="driver_in_cb" name="driver_in_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:18px; position:relative; left:2px; margin-left:5px; margin-right:5px;' src="/images/driver_in.png"/>
									</td>
									<td style="vertical-align:middle;">
										Driver In
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="driver_out_cb" name="driver_out_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:18px; position:relative; left:2px; margin-left:5px; margin-right:5px;' src="/images/driver_out.png"/>
									</td>
									<td style="vertical-align:middle;">
										Driver Out
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="pick_trailer_cb" name="pick_trailer_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:15px; position:relative; right:2px; margin-left:5px; margin-right:5px;' src="/images/pick_trailer.png"/>
									</td>
									<td style="vertical-align:middle;">
										Pick Trailer
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="drop_trailer_cb" name="drop_trailer_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:13px; position:relative; right:3px; margin-left:5px; margin-right:5px;' src="/images/drop_trailer.png"/>
									</td>
									<td style="vertical-align:middle;">
										Drop Trailer
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="check_call_cb" name="check_call_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:18px; position:relative; left:2px; bottom:2px; margin-left:5px; margin-right:5px;' src="/images/log_check_call.png"/>
									</td>
									<td style="vertical-align:middle;">
										Check Call
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="dry_service_cb" name="dry_service_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:20px; position:relative; bottom:2px; margin-left:5px; margin-right:5px;' src="/images/log_service.png"/>
									</td>
									<td style="vertical-align:middle;">
										Dry Service
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="wet_service_cb" name="wet_service_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:20px; position:relative; bottom:2px; margin-left:5px; margin-right:5px;' src="/images/log_service.png"/>
									<td style="vertical-align:middle;">
										Wet Service
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="shift_report_cb" name="shift_report_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:14px; position:relative; top:0px; margin-left:5px; margin-right:5px;' src="/images/log_shift_report.png"/>
									</td>
									<td style="vertical-align:middle;">
										Shift Report
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="end_leg_cb" name="end_leg_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:15px; position:relative; bottom:1px; left:1px; margin-left:5px; margin-right:5px;' src="/images/log_end_leg.png"/>
									</td>
									<td style="vertical-align:middle;">
										End Leg
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="end_week_cb" name="end_week_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:14px; position:relative; bottom:1px; left:1px; margin-left:5px; margin-right:5px;' src="/images/end_week.png"/>
									</td>
									<td style="vertical-align:middle;">
										End Week
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="geopoint_cb" name="geopoint_cb" type="checkbox"  style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:14px; position:relative; bottom:1px; left:1px; margin-left:5px; margin-right:5px;' src="/images/geopoint_icon.png"/>
									</td>
									<td style="vertical-align:middle;">
										Geopoint
									</td>
								</tr>
								<tr style="height:25px;">
									<td style="vertical-align:middle;">
										<input id="geopoint_stop_cb" name="geopoint_stop_cb" type="checkbox"  style="position:relative; bottom:1px;" onclick="load_log_list()">
									</td>
									<td style="vertical-align:middle;">
										<img style='height:14px; position:relative; bottom:1px; left:1px; margin-left:5px; margin-right:5px;' src="/images/geopoint_stop_icon.png"/>
									</td>
									<td style="vertical-align:middle;">
										Geopoint
									</td>
								</tr>
							</table>
						<input type="hidden" id="get_picks" name="get_picks" value="">
						<input type="hidden" id="get_drops" name="get_drops" value="">
						<input type="hidden" id="get_fuel_fills" name="get_fuel_fills" value="">
						<input type="hidden" id="get_fuel_partials" name="get_fuel_partials" value="">
						<input type="hidden" id="get_checkpoints" name="get_checkpoints" value="">
						<input type="hidden" id="get_driver_ins" name="get_driver_ins" value="">
						<input type="hidden" id="get_driver_outs" name="get_driver_outs" value="">
						<input type="hidden" id="get_pick_trailers" name="get_pick_trailers" value="">
						<input type="hidden" id="get_drop_trailers" name="get_drop_trailers" value="">
						<input type="hidden" id="get_check_calls" name="get_check_calls" value="">
						<input type="hidden" id="get_dry_services" name="get_dry_services" value="">
						<input type="hidden" id="get_wet_services" name="get_wet_services" value="">
						<input type="hidden" id="get_shift_reports" name="get_shift_reports" value="">
						<input type="hidden" id="get_end_legs" name="get_end_legs" value="">
						<input type="hidden" id="get_end_weeks" name="get_end_weeks" value="">
						<input type="hidden" id="get_geopoints" name="get_geopoints" value="">
						<input type="hidden" id="get_geopoints_stop" name="get_geopoints_stop" value="">
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