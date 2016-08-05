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
		
		<?php include("performance/performance_script.php"); ?>

	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" style="width:175px">
				<span class="heading">Filters</span>
				<hr/>
				<div id="filter_list" class="scrollable_div">
					<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
					<?=form_open('logs/load_log',$attributes);?>
						<br>
						<span style="font-weight:bold;">Fleet Manager</span>
						<hr/>
						<?php echo form_dropdown('fm_filter_dropdown',$fleet_manager_dropdown_options,"All",'id="fm_filter_dropdown" style="" class="left_bar_input" onchange="load_list()"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Driver Manager</span>
						<hr/>
						<?php echo form_dropdown('dm_filter_dropdown',$driver_manager_dropdown_options,"All",'id="dm_filter_dropdown" style="" class="left_bar_input" onchange="load_list()"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Truck</span>
						<hr/>
						<?php echo form_dropdown('truck_filter_dropdown',$truck_dropdown_options,"All",'id="truck_filter_dropdown" style="" class="left_bar_input" onchange="load_list()"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Dates</span>
						<hr/>
						<input type="text" id="start_date_filter" name="start_date_filter" class="left_bar_input" onchange="load_list()" placeholder="After"/>
						<br>
						<input type="text" id="end_date_filter" name="end_date_filter" class="left_bar_input" onchange="load_list()" placeholder="Before"/>
					</form>
				</div>
			</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header" style="">
					<span style="font-weight:bold;">Performance</span>
					<div style="float:right; width:25px;">
						<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
						<img id="refresh_list" name="refresh_list" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_list()" />
					</div>
				</div>
			</div>
			
		</div>
	</body>
</html>
