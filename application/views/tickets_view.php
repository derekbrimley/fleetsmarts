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
		
		<?php include("tickets/tickets_script.php"); ?>

	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" style="width:175px">
				<button class='left_bar_button jq_button' id="log_entry" onclick="open_new_ticket_dialog()">New Ticket</button>
				<br>
				<?php $attributes = array('name'=>'filter_form','id'=>'filter_form','onkeypress'=>'return event.keyCode != 13;')?>
				<?=form_open('tickets/load_report',$attributes);?>
					<div style="margin-top:15px;" id="scrollable_filter_div"  class="scrollable_div">
						<span style="font-weight:bold;">Search</span>
						<hr/>
						<input placeholder="Ticket or Claim #"type="text" id="ticket_search_input" name="ticket_search_input" class="left_bar_input" onkeypress="return load_tickets_enter(event)" onChange="load_tickets()"/>
						<br>
						<br>
						<span style="font-weight:bold;">Truck #</span>
						<hr/>
						<?php echo form_dropdown('truck_number_input',$truck_number_options,'All','onChange="load_tickets()" id="truck_number_input" class="left_bar_input"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Trailer #</span>
						<hr/>
						<?php echo form_dropdown('trailer_number_input',$trailer_number_options,'All','onChange="load_tickets()" id="trailer_number_input" class="left_bar_input"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Category</span>
						<hr/>
						<?php echo form_dropdown('ticket_category_input',$ticket_category_options,'All','onChange="load_tickets()" id="ticket_category_input" class="left_bar_input"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Status</span>
						<hr/>
							<select id="status_filter" name="status_filter" onChange="load_tickets()"class="left_bar_input">
								<option>All</option>
								<option>Open</option>
								<option>Closed</option>
							</select>
						<br>
						<br>
						<span style="font-weight:bold;">Incident Date</span>
						<hr/>
							<input onChange="load_tickets()" placeholder="After" id="after_incident_date_filter" name="after_incident_date_filter" class="left_bar_input dp"/>
							<input onChange="load_tickets()" placeholder="Before" id="before_incident_date_filter" name="before_incident_date_filter" class="left_bar_input dp"/>
						<br>
						<br>
						<span style="font-weight:bold;">Action Date</span>
						<hr/>
							<input onChange="load_tickets()" placeholder="After" id="after_action_date_filter" name="after_action_date_filter" class="left_bar_input dp"/>
							<input onChange="load_tickets()" placeholder="Before" id="before_action_date_filter" name="before_action_date_filter" class="left_bar_input dp"/>
						<br>
						<br>
						<span style="font-weight:bold;">Estimated Repair Date</span>
						<hr/>
							<input onChange="load_tickets()" placeholder="After" id="after_estimated_date_filter" name="after_estimated_date_filter" class="left_bar_input dp"/>
							<input onChange="load_tickets()" placeholder="Before" id="before_estimated_date_filter" name="before_estimated_date_filter" class="left_bar_input dp"/>
						<br>
						<br>
						<span style="font-weight:bold;">Date Closed</span>
						<hr/>
							<input onChange="load_tickets()" placeholder="After" id="after_completion_date_filter" name="after_completion_date_filter" class="left_bar_input dp"/>
							<input onChange="load_tickets()" placeholder="Before" id="before_completion_date_filter" name="before_completion_date_filter" class="left_bar_input dp"/>
						<br>
						<br>
					</div>
				</form>
			</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header">
					<span style="font-weight:bold;">Tickets</span>
					<div style="float:right; width:25px;">
						<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
						<img id="refresh_tickets" name="refresh_tickets" src="/images/refresh.png" title="Refresh Tickets" style="cursor:pointer; float:right; height:20px; padding-top:5px;"  />
					</div>
				</div>
				<!-- REPORT GOES HERE !-->
			</div>
			
		</div>
	</body>
	<div id="add_notes" title="Add Note" style="padding:10px; display:none;">
        <div>
            <span id="notes_header" style="font-weight:bold;font-size:14px;">Ticket Notes</span>
            <br>
            <br>
            <div id="notes_ajax_div" style="height:215px; overflow:auto;font-size:12px;">
                <!-- AJAX WILL POPULATE THIS !-->
            </div>
        </div>
        <div style="position:absolute; bottom:0">
            <?php $attributes = array('name'=>'add_note_form','id'=>'add_note_form', )?>
            <?=form_open('leads/save_note/',$attributes);?>
                <div style="font-size:14px;">Add Note:</div>
                <input type="hidden" id="ticket_id" name="ticket_id">
                <textarea style="width:400px;" rows="3" id="new_note" name="new_note"></textarea>
            </form>
        </div>
    </div>
	<div id="create_new_ticket" title="Create New Ticket" style="display:none;">
		<div id="new_ticket_form_div">
			<!-- AJAX GOES HERE!-->
		</div>
		<div id="success_div" style="font-size:14px; text-align:center; margin-top:25px; display:none;">
			Creating New Ticket!
		</div>
	</div>
	
</html>

<div title="Ticket Attachment Upload" id="file_upload_dialog" name="file_upload_dialog" style="display:hidden;" >
	<!-- AJAX GOES HERE !-->
</div>

<div title="Inspection Picture Upload" id="inspection_picture_dialog" name="inspection_picture_dialog" style="display:hidden;" >
	<!-- AJAX GOES HERE !-->
</div>