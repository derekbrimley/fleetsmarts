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
		
		<?php include("commissions/commissions_script.php"); ?>

	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" style="width:175px">
				<span class="heading">Settlement Type</span>
				<hr/>
				<div class="left_bar_link_div" style="font-weight:bold;" onclick="" >
					Fleet Manager
				</div>
				<div class="left_bar_link_div" style="" onclick="" >
					Driver
				</div>
				<br>
				<span class="heading">Filters</span>
				<hr/>
				<div id="filter_list" class="scrollable_div">
					<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
					<?=form_open('logs/load_log',$attributes);?>
						<br>
						<span style="font-weight:bold;">Load</span>
						<hr/>
						<input type="text" id="load_filter" name="load_filter" onchange="load_list()" placeholder="Load Number"/>
						<br>
						<br>
						<span style="font-weight:bold;">Fleet Manager</span>
						<hr/>
						<?php echo form_dropdown('fm_filter_dropdown',$fleet_manager_dropdown_options,"All",'id="fm_filter_dropdown" style="" class="left_bar_input" onchange="load_list()"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Client</span>
						<hr/>
						<?php echo form_dropdown('client_filter_dropdown',$main_driver_dropdown_options,"All",'id="client_filter_dropdown" style="" class="left_bar_input" onchange="load_list()"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Drop Dates</span>
						<hr/>
						<input type="text" id="start_date_filter" name="start_date_filter" onchange="load_list()" placeholder="Start Date"/>
						<br>
						<input type="text" id="end_date_filter" name="end_date_filter" onchange="load_list()" placeholder="End Date"/>
						<br>
						<br>
						<span style="font-weight:bold;">Settlement Status</span>
						<hr/>
						<a style="margin-right:9px;" href="javascript:clear_events();">Clear All</a>|<a href="javascript:select_all_events();">Select All</a>
						<br>
						<br>
						<table>
							<tr style="height:25px;">
								<td style="width:20px; vertical-align:middle;">
									<input id="in_transit_cb" name="in_transit_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_list()">
								</td>
								<td style="width:30px; vertical-align:middle;">
									<img style='height:16px; position:relative; bottom:0px; margin-left:5px; margin-right:5px;' src="/images/in_transit.png" />
								</td>
								<td style="vertical-align:middle;">
									In Transit
								</td>
							</tr>
							<tr style="height:25px;">
								<td style="vertical-align:middle;">
									<input id="pending_funding_cb" name="pending_funding_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_list()">
								</td>
								<td style="vertical-align:middle;">
									<img style='height:16px; position:relative; bottom:0px; margin-left:5px; margin-right:5px;' src="/images/pending_funding.png"/>
								</td>
								<td style="vertical-align:middle;">
									Pending Funding
								</td>
							</tr>
							<tr style="height:25px;">
								<td style="vertical-align:middle;">
									<input id="pending_settlement_cb" name="pending_settlement_cb" type="checkbox" checked style="position:relative; bottom:1px;" onclick="load_list()">
								</td>
								<td style="vertical-align:middle;">
									<img style='height:16px; position:relative; bottom:0px; margin-left:5px; margin-right:5px;' src="/images/pending_settlement.png"/>
								</td>
								<td style="vertical-align:middle;">
									Pending Approval
								</td>
							</tr>
							<tr style="height:25px;">
								<td style="vertical-align:middle;">
									<input id="closed_cb" name="closed_cb" type="checkbox" style="position:relative; bottom:1px;" onclick="load_list()">
								</td>
								<td style="vertical-align:middle;">
									<img style='height:16px; position:relative; bottom:0px; margin-left:5px; margin-right:5px;' src="/images/closed.png"/>
								</td>
								<td style="vertical-align:middle;">
									Closed
								</td>
							</tr>
						</table>
						<input type="hidden" id="get_in_transit" name="get_in_transit" value="">
						<input type="hidden" id="get_pending_funding" name="get_pending_funding" value="">
						<input type="hidden" id="get_pending_settlement" name="get_pending_settlement" value="">
						<input type="hidden" id="get_closed" name="get_closed" value="">
					</form>
				</div>
			</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header">
					<span style="font-weight:bold;">Commissions</span>
					<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px;" />
				</div>
			</div>
			
		</div>
	</body>
	
	<div id="log_event_dialog" title="Log New Event">
		<!-- AJAX GOES HERE !-->
	</div>
	
</html>
<div id="fm_settlement_details" title="Fleet Manager Settlement" style="display:none;">
	<!-- AJAX GOES HERE !-->
</div>