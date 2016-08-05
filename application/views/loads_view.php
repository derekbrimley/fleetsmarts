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
		

		<?php include("loads/loads_script.php"); ?>
		<?php include("loads/goalpoints_script.php"); ?>
		
		<script type="text/javascript">
			$('#drop_start_date_filter').datepicker({ showAnim: 'blind' });
			$('#drop_end_date_filter').datepicker({ showAnim: 'blind' });
		</script>
		
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
				<div id="filter_list" class="scrollable_div">
					<?php $attributes = array('name'=>'filter_form','id'=>'filter_form', )?>
					<?=form_open('logs/load_log',$attributes);?>
						<span class="heading">Search</span>
						<hr/>
						<input type="text" id="search_term" name="search_term" class="left_bar_input" onchange="load_list()" onkeydown="Javascript: if (event.keyCode==13) load_list();" placeholder="Load Number">
						<br>
						<br>
						<span class="heading">Filters</span>
						<hr/>
						<br>
						<span style="font-weight:bold;">Fleet Manager</span>
						<hr/>
						<?php echo form_dropdown('fleet_managers_dropdown',$fleet_managers_dropdown_options,"All",'id="fleet_managers_dropdown" style="" class="left_bar_input" onchange="load_list()"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Driver Manager</span>
						<hr/>
						<?php echo form_dropdown('dm_filter_dropdown',$dm_filter_dropdown_options,"All",'id="dm_filter_dropdown" style="" class="left_bar_input" onchange="load_list()"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Carrier</span>
						<hr/>
						<?php echo form_dropdown('carrier_filter',$billed_under_options,"All",'id="carrier_filter" style="" class="left_bar_input" onchange="load_list()"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Broker</span>
						<hr/>
						<?php echo form_dropdown('broker_dropdown',$broker_dropdown_options,"All",'id="broker_dropdown" style="" class="left_bar_input" onchange="load_list()"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Drop Date</span>
						<hr/>
						<input type="text" id="drop_start_date_filter" name="drop_start_date_filter" class="left_bar_input datepicker" onchange="load_list()" placeholder="After"/>
						<br>
						<input type="text" id="drop_end_date_filter" name="drop_end_date_filter" class="left_bar_input datepicker" onchange="load_list()" placeholder="Before"/>
						<br>
						<br>
						<span style="font-weight:bold;">Truck</span>
						<hr/>
						<?php echo form_dropdown('truck_filter_dropdown',$truck_dropdown_options,"All",'id="truck_filter_dropdown" style="" class="left_bar_input" onchange="load_list()"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Trailer</span>
						<hr/>
						<?php echo form_dropdown('trailer_filter_dropdown',$trailer_dropdown_options,"All",'id="trailer_filter_dropdown" style="" class="left_bar_input" onchange="load_list()"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Load Status</span>
						<hr/>
						<?php $options = array(
							'All' => 'All',
							'active' => 'Active',
							'booked'    => 'Booked',
							'in_transit'  => 'In Transit',
							'dropped'  => 'Dropped',
							); 
						?>
						<?php echo form_dropdown('load_status_dropdown',$options,"active",'id="load_status_dropdown" style="" class="left_bar_input" onchange="load_list()"');?>
						<br>
						<br>
					</form>
				</div>
				<a style="margin-left:0px; color:blue; text-decoration:underline" class="link" href="https://docs.google.com/document/d/1-aYGqYn5Yabq1A2sw9nyy3VZbq8Rfv7XIVWm1iaCz4A/edit?usp=sharing" target="_blank">Load Process</a>
			</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header" style="">
					<span style="font-weight:bold;">Loads</span>
					<div style="float:right; width:25px;">
						<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
						<img id="refresh_list" name="refresh_list" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_list()" />
					</div>
				</div>
			</div>
		</div>
	</body>
	
	<div id="add_load_dialog" title="15 Second New Load Window" style="display:none;">
		<!--AJAX HERE !-->
	</div>
	
	<div id="cancel_load_reason_dialog" title="Cancel this Load?" style="display:none;">
		<form id="cancel_load_form" name="cancel_load_form">
			<input type="hidden" name="cancelled_load_id" id="cancelled_load_id" value="">
			<div style="margin:20px;">
				<div id="cancel_load_text_div">
					Are you sure you want to cancel this load?
				</div>
				<br>
				Reason:
				<input type="text" id="load_cancel_reason" name="load_cancel_reason" style="width:400px; margin-left:10px;"/>
			</div>
		</form>
	</div>
	
	<div id="mark_goalpoint_complete_dialog" title="Mark Goalpoint Complete" style="display:none;">
		<!--AJAX HERE !-->
	</div>
	
	<div id="rate_con_received_dialog" title="Load Information - Rate Con Received" style="display:none;">
		<div id='rate_con_received_div' style="margin:20px;">
			<!--AJAX HERE !-->
		</div>
	</div>
	
	<div id="load_dispatch_dialog" title="Load Dispatch" style="display:none;">
		<!--AJAX HERE !-->
	</div>
	
	<div id="add_notes" title="Add Note" style="padding:10px; display:none;">
        <div>
            <span id="notes_header" style="font-weight:bold;font-size:14px;">Load Notes</span>
            <br><br>
            <div id="notes_ajax_div" style="height:215px; overflow:auto; font-size:12px;">
                <!-- AJAX WILL POPULATE THIS !-->
            </div>
        </div>
        <div style="position:absolute; bottom:0">
            <?php $attributes = array('name'=>'add_note_form','id'=>'add_note_form', )?>
            <?=form_open('leads/save_note/',$attributes);?>
                <div style="font-size:14px;">Add Note:</div>
                <input type="hidden" id="row_id" name="row_id">
                <textarea style="width:400px;" rows="3" id="new_note" name="new_note"></textarea>
            </form>
        </div>
    </div>
	
	<div title="Attachment Upload" id="file_upload_dialog" name="file_upload_dialog" style="display:hidden;" >
		<!-- AJAX GOES HERE !-->
	</div>
	
	<div title="Load Plan Email" id="load_plan_email_dialog" name="load_plan_email_dialog" style="display:hidden;" >
		<!-- AJAX GOES HERE !-->
	</div>
	
</html>