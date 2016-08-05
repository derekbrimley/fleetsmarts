<style>	
	.edit_<?=$log_entry_id?>
	{
		display:none;
	}
	
	.shift_report_table tr
	{
		line-height:15px;
	}
</style>

<script>
	fill_in_locations("s_location_<?=$log_entry_id?>",$("#shift_s_gps_<?=$log_entry_id?>").val());
	fill_in_locations("e_location_<?=$log_entry_id?>",$("#shift_e_gps_<?=$log_entry_id?>").val());
	load_goalpoints_div('<?=$log_entry_id?>');
	load_contact_attempts_div('<?=$log_entry_id?>');
	
	//DIALOG: MARK GOALPOINT COMPLETE DIALOG
	$( "#gp_complete_dialog_<?=$log_entry_id?>" ).dialog(
	{
		autoOpen: false,
		height: 250,
		width: 450,
		modal: true,
		buttons: 
		[
			{
				text: "Save",
				click: function() 
				{
					var attach_le_id = $("#attachment_log_entry_id").val();
					//alert(attach_le_id);
				
					//SUBMIT FORM
					mark_gp_complete('<?=$log_entry_id?>');
					//$( this ).dialog( "close" );
				},//end add load
			},
			{
				text: "Cancel",
				click: function() 
				{
					//RESIZE DIALOG BOX
					$( this ).dialog( "close" );
				}
			}
		],//end of buttons
		open: function()
		{
		},//end open function
		close: function() 
		{
			$("#complete_gp_expected_time_text").html("");
		}
	});//end dialog form
	
	//DIALOG: MARK GOALPOINT COMPLETE DIALOG
	$("#missed_goalpoints_dialog_<?=$log_entry_id?>" ).dialog(
	{
		autoOpen: false,
		height: 450,
		width: 500,
		modal: true,
		buttons: 
		[
			{
				id: "missed_goalpoints_save_button_<?=$log_entry_id?>",
				text: "Save",
				click: function() 
				{
					validate_incomplete_goalpoints_dialog('<?=$log_entry_id?>');
					//$( this ).dialog( "close" );
				},//end add load
			},
			{
				id: "missed_goalpoints_cancel_button_<?=$log_entry_id?>",
				text: "Cancel",
				click: function() 
				{
					//RESIZE DIALOG BOX
					$( this ).dialog( "close" );
				}
			}
		],//end of buttons
		open: function()
		{
		},//end open function
		close: function() 
		{
			$("#complete_gp_expected_time_text").html("");
		}
	});//end dialog form

</script>
<?php
	$shift_report_is_complete = shift_report_is_complete($shift_report);
	$shift_report_details_is_complete = shift_report_details_is_complete($shift_report);
	$shift_report_plans_is_complete = shift_report_plans_is_complete($shift_report);
	$shift_report_goalpoints_is_complete = shift_report_goalpoints_is_complete($shift_report);
	$shift_report_recap_is_complete = shift_report_recap_is_complete($shift_report);

	$start_shift_time_text = "";
	if(!empty($shift_report["shift_s_time"]))
	{
		$start_shift_time_text = date('m/d/y H:i',strtotime($shift_report["shift_s_time"]));
	}
	
	$end_shift_time_text = "";
	if(!empty($shift_report["shift_e_time"]))
	{
		$end_shift_time_text = date('m/d/y H:i',strtotime($shift_report["shift_e_time"]));
	}

	//GET PERSON
	$where = null;
	$where["id"] = $this->session->userdata('person_id');
	$dispatcher_person = db_select_person($where);
?>
<div style="font-size:12px;">
	<?php if(empty($log_entry["locked_datetime"])): ?>
		<div style="width:20px; height:45px; float:right;">
			<img id="refresh_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:13px; position:relative; left:0px;" src="/images/refresh.png" title="Refresh" onclick="this.src='/images/loading.gif'; open_event_details('<?=$log_entry_id?>')"/>
			<img id="edit_icon" class="details_<?=$log_entry_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:0px;" src="/images/edit.png" title="Edit" onclick="edit_event('<?=$log_entry_id?>')"/>
			<img id="save_icon_<?=$log_entry_id?>" class="edit_<?=$log_entry_id?>" style="display:none; margin-bottom:13px; margin-right:15px; cursor:pointer; height:14px; position:relative; left:1px;" src="/images/save.png" title="Save" onclick="this.src='/images/loading.gif';save_shift_report('<?=$log_entry_id?>');"/>
			<img id="attachment_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:18px; position:relative; left:3px;" src="/images/paper_clip2.png" title="Attach Document" onclick="open_file_upload('<?=$log_entry_id?>')"/>
			<img id="delete_icon" style="display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/trash.png" title="Delete" onclick="delete_event('<?=$log_entry_id?>')"/>
		</div>
	<?php else: ?>
		<div style="width:20px; float:right;">
			<img id="locked_icon" style="display:block; margin-bottom:12px; margin-right:15px; height:15px; position:relative; left:2px;" src="/images/locked.png" title="Locked <?=date("n/j H:i",strtotime($log_entry["locked_datetime"]))?>"/>
		</div>
	<?php endif; ?>
	<div style="width:60px; float:left;">
		<div style="font-size:12px; font-weight:bold; width:90px;">
			<?php if($shift_report_is_complete["is_complete"]):?>
				<img src="/images/load_status_8_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('Complete!');" title="Complete!">
			<?php else:?>
				<img src="/images/load_status_1_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$shift_report_is_complete["message"]?>')"  title="<?=$shift_report_is_complete["message"]?>">
			<?php endif;?>
			Shift Report
		</div>
	</div>
	<form id="shift_report_form_<?=$log_entry_id?>" name="shift_report_form_">
		<input type="hidden" id="log_entry_id" name="log_entry_id" value="<?=$log_entry_id?>">
		<div style="margin-left:120px;">
			<div class="heading">
				<?php if($shift_report_details_is_complete["is_complete"]):?>
					<img src="/images/load_status_8_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$shift_report_details_is_complete["message"]?>');" title="<?=$shift_report_details_is_complete["message"]?>">
				<?php else:?>
					<img src="/images/load_status_1_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$shift_report_details_is_complete["message"]?>');"  title="<?=$shift_report_details_is_complete["message"]?>">
				<?php endif;?>
				Shift Report Details
			</div>
			<div style="color:grey; font-style:italic; margin-top:5px;">
				To be filled out by the driver manager before the shift begins. Start and End shift info will be approximations.<br>
				To be updated by the dispatcher once the information is obtained from the driver. Start and End shift info will be exact.
			</div>
			<hr style="width:715px;"><br>
		</div>
		<div>
			<table class="shift_report_table" style="margin-left:120px; margin-top:5px; margin-bottom:10px; ">
				<tr>
					<td style="width:100px; font-weight:bold;">
					</td>
					<td style="width:100px;">
					</td>
					<td style="width:100px; font-weight:bold;">
					</td>
					<td style="width:150px; text-align:center;" class="heading">
						Start Shift
					</td>
					<td style="width:100px; font-weight:bold;">
					</td>
					<td style="width:150px; text-align:center;" class="heading">
						End Shift
					</td>
				</tr>
				<tr>
					<td style="font-weight:bold;">
						Driver
					</td>
					<td style="text-align:left;">
						<span class="details_<?=$log_entry_id?>"><?=$client["client_nickname"]?></span>
						<?php echo form_dropdown("client_id",$driver_dropdown_options,$shift_report["client_id"],"id='client_id_$log_entry_id' class='edit_".$log_entry_id."'style='position:relative; left:0px; bottom:3px; width:100px; height:24px;'");?>
					</td>
					<td style="text-align:right; font-weight:bold;">
						Time
					</td>
					<td style="text-align:center;">
						<span class="details_<?=$log_entry_id?>"><?=$start_shift_time_text?></span>
						<input type="text" placeholder="Date Time" id="shift_s_time_<?=$log_entry_id?>" name="shift_s_time" class="edit_<?=$log_entry_id?>" style="position:relative; left:24px; bottom:5px; font-family:arial; font-size:12px; width:100px; height:24px; text-align:center;" value="<?=$start_shift_time_text?>"/>
					</td>
					<td style="text-align:right; font-weight:bold;">
						Time
					</td>
					<td style="text-align:center;">
						<span class="details_<?=$log_entry_id?>"><?=$end_shift_time_text?></span>
						<input type="text" placeholder="Date Time" id="shift_e_time_<?=$log_entry_id?>" name="shift_e_time" class="edit_<?=$log_entry_id?>" style="position:relative; left:24px; bottom:5px; font-family:arial; font-size:12px; width:100px; height:24px; text-align:center;" value="<?=$end_shift_time_text?>"/>
					</td>
				</tr>
				<tr>
					<td style="font-weight:bold;">
						
					</td>
					<td>
						
					</td>
					<td style="text-align:right; font-weight:bold;">
						Location
					</td>
					<td style="text-align:center;">
						<span id="s_location_<?=$log_entry_id?>" class="details_<?=$log_entry_id?>"><img style="height:12px;" src="/images/loading.gif"/></span>
						<input type="text" placeholder="Lat, Long" id="shift_s_gps_<?=$log_entry_id?>" name="shift_s_gps" class="edit_<?=$log_entry_id?>" style="position:relative; left:24px; bottom:5px; font-family:arial; font-size:12px; width:100px; height:24px; text-align:center;" value="<?=$shift_report["shift_s_gps"]?>"/>
					</td>
					<td style="text-align:right; font-weight:bold;">
						Location
					</td>
					<td style="text-align:center;">
						<span id="e_location_<?=$log_entry_id?>"  class="details_<?=$log_entry_id?>"><img style="height:12px;" src="/images/loading.gif"/></span>
						<input type="text" placeholder="Lat, Long" id="shift_e_gps_<?=$log_entry_id?>" name="shift_e_gps" class="edit_<?=$log_entry_id?>" style="position:relative; left:24px; bottom:5px; font-family:arial; font-size:12px; width:100px; height:24px; text-align:center;" value="<?=$shift_report["shift_e_gps"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="font-weight:bold;">
					</td>
					<td>
					</td>
					<td style="text-align:right; font-weight:bold;">
						Odometer
					</td>
					<td style="text-align:center;">
						<span id="s_odometer_<?=$log_entry_id?>" class="details_<?=$log_entry_id?>"><?php if(isset($shift_report["shift_s_odometer"])){echo number_format($shift_report["shift_s_odometer"]);}?></span>
						<input type="text" placeholder="Start" id="shift_s_odometer_<?=$log_entry_id?>" name="shift_s_odometer" class="edit_<?=$log_entry_id?>" style="position:relative; left:24px; bottom:5px; font-family:arial; font-size:12px; width:100px; height:24px; text-align:center;" value="<?=$shift_report["shift_s_odometer"]?>"/>
					</td>
					<td style="text-align:right; font-weight:bold;">
						Odometer
					</td>
					<td style="text-align:center;">
						<span id="e_odometer_<?=$log_entry_id?>"  class="details_<?=$log_entry_id?>"><?php if(isset($shift_report["shift_e_odometer"])){echo number_format($shift_report["shift_e_odometer"]);}?></span>
						<input type="text" placeholder="End" id="shift_e_odometer_<?=$log_entry_id?>" name="shift_e_odometer" class="edit_<?=$log_entry_id?>" style="position:relative; left:24px; bottom:5px; font-family:arial; font-size:12px; width:100px; height:24px; text-align:center;" value="<?=$shift_report["shift_e_odometer"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="font-weight:bold;">
					</td>
					<td>
					</td>
					<td style="text-align:right; font-weight:bold;">
						Fuel Level
					</td>
					<td style="text-align:center;">
						<span class="details_<?=$log_entry_id?>"><?=$shift_report["shift_s_fuel_level"]?></span>
						<input type="text" id="shift_s_fuel_level_<?=$log_entry_id?>" name="shift_s_fuel_level" class="edit_<?=$log_entry_id?>" style="position:relative; left:24px; bottom:5px; font-family:arial; font-size:12px; width:100px; height:24px; text-align:center;" value="<?=$shift_report["shift_s_fuel_level"]?>"/>
					</td>
					<td style="text-align:right; font-weight:bold;">
						Fuel Level
					</td>
					<td style="text-align:center;">
						<span class="details_<?=$log_entry_id?>"><?=$shift_report["shift_e_fuel_level"]?></span>
						<input type="text" id="shift_e_fuel_level_<?=$log_entry_id?>" name="shift_e_fuel_level" class="edit_<?=$log_entry_id?>" style="position:relative; left:24px; bottom:5px; font-family:arial; font-size:12px; width:100px; height:24px; text-align:center;" value="<?=$shift_report["shift_e_fuel_level"]?>"/>
					</td>
				</tr>
			</table>
		</div>
		<div style="margin-left:120px;">
			<div class="heading">
				<?php if($shift_report_plans_is_complete["is_complete"]):?>
					<img src="/images/load_status_8_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$shift_report_plans_is_complete["message"]?>');" title="<?=$shift_report_plans_is_complete["message"]?>">
				<?php else:?>
					<img src="/images/load_status_1_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$shift_report_plans_is_complete["message"]?>');"  title="<?=$shift_report_plans_is_complete["message"]?>">
				<?php endif;?>
				Plans
			</div>
			<div style="color:grey; font-style:italic; margin-top:5px;"></div>
			<hr style="width:715px;"><br>
		</div>
		<table style="margin-left:120px; margin-top:5px; margin-bottom:10px; line-height:16px; width:725px;">
			<tr style="height:40px;">
				<td style="font-weight:bold; width:150px; height:40px;">
					Plan Summary
				</td>
				<td style="width:550px;">
					<span class="details_<?=$log_entry_id?>"><?=$shift_report["plan_summary"]?></span>
					<textarea id="plan_summary" name="plan_summary" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$shift_report["plan_summary"]?></textarea>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold; width:150px; height:40px; padding-top:10px;">
					Fuel Plan
				</td>
				<td style="width:550px; padding-top:10px;">
					<span class="details_<?=$log_entry_id?>"><?=$shift_report["fuel_plan"]?></span>
					<textarea id="fuel_plan" name="fuel_plan" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$shift_report["fuel_plan"]?></textarea>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold; width:150px; height:40px; padding-top:10px;">
					Toll Plan
				</td>
				<td style="width:550px; padding-top:10px;">
					<span class="details_<?=$log_entry_id?>"><?=$shift_report["toll_plan"]?></span>
					<textarea id="toll_plan" name="toll_plan" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$shift_report["toll_plan"]?></textarea>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold; width:150px; height:40px; padding-top:10px;">
					Route Plan
				</td>
				<td style="width:550px; padding-top:10px;">
					<span class="details_<?=$log_entry_id?>"><?=$shift_report["route_plan"]?></span>
					<textarea id="route_plan" name="route_plan" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$shift_report["route_plan"]?></textarea>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold; width:150px; height:40px; padding-top:10px;">
					Call with Driver
				</td>
				<td style="width:550px; padding-top:10px;">
					<?php if(!empty($shift_report["audio_w_driver_file_guid"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$shift_report["audio_w_driver_file_guid"]?>" onclick="">Listen</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold; width:150px; height:40px; padding-top:10px;">
					Call with Dispatcher
				</td>
				<td style="width:550px; padding-top:10px;">
					<?php if(!empty($shift_report["audio_w_dispatch_file_guid"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$shift_report["audio_w_dispatch_file_guid"]?>" onclick="">Listen</a>
					<?php endif; ?>
				</td>
			</tr>
		</table>
		<?php
			/**
			<div style="margin-left:120px;">
				<div class="heading">
					<?php if($shift_report_goalpoints_is_complete["is_complete"]):?>
						<img src="/images/load_status_8_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$shift_report_goalpoints_is_complete["message"]?>');" title="<?=$shift_report_goalpoints_is_complete["message"]?>">
					<?php else:?>
						<img src="/images/load_status_1_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$shift_report_goalpoints_is_complete["message"]?>');"  title="<?=$shift_report_goalpoints_is_complete["message"]?>">
					<?php endif;?>
					Expected Goalpoints
				</div>
				<div style="color:grey; font-style:italic; margin-top:5px;"></div>
				<hr style="width:715px;"><br>
			</div>
			<table style="margin-left:120px; margin-top:5px; margin-bottom:10px; line-height:10px; font-size:10px;">
				<tr style="font-weight:bold;">
					<td style="width:70px;">
						Expected<br>Time
					</td>
					<td style="width:70px;">
						Deadline
					</td>
					<td style="width:80px;">
						Goalpoint<br>Type
					</td>
					<td style="width:70px;">
						GPS
					</td>
					<td style="width:95px;">
						Location
					</td>
					<td style="width:175px; padding-right:15px;">
						Notes from DM
					</td>
					<td style="width:70px; text-align:right; padding-right:10px;">
						Leeway
					</td>
					<td style="width:60px; text-align:right;">
						Complete
					</td>
				</tr>
			</table>
			<div id="goalpoints_div_<?=$log_entry_id?>">
				<!--AJAX GOES HERE!-->
				<span style="margin-left:450px;"><img style="height:20px;" src="/images/loading.gif"/></span>
			</div>
			<table style="margin-left:120px; margin-top:15px; margin-bottom:10px; line-height:10px; font-size:10px;">
				<tr style="">
					<td style="width:70px;">
						<span>Has deadline?</span>
						<input id="has_deadline_cb_<?=$log_entry_id?>" type="checkbox" onchange="has_deadline_changed('<?=$log_entry_id?>')"/>
					</td>
					<td style="width:70px;">
						<input placeholder="Date Time" type="text" id="temp_deadline_<?=$log_entry_id?>" name="temp_deadline_<?=$log_entry_id?>" class="" style="font-family:arial; font-size:10px; width:60px; height:24px; display:none;" value=""/>
					</td>
					<td style="width:80px;">
						<?php
							$options = array(
								"Select" => "Select Type",
								"Start" => "Start",
								"Pick" => "Pick (2 hr)",
								"Drop" => "Drop (2 hr)",
								"Driver Change" => "Driver Change (15 min)",
								"Trailer Change" => "Trailer Change (15 min)",
								"Goal" => "Goal (0 min)",
								"Fuel" => "Fuel (30 min)",
								"Break" => "Break (15 min)",
								"End" => "End",
							);
						?>
						<?php echo form_dropdown("temp_gp_type_$log_entry_id",$options,"Select","id='temp_gp_type_$log_entry_id' class='' style='width:70px; height:24px; font-size:10px;'");?>
					</td>
					<td style="width:70px;">
						<input placeholder="Lat, Long" type="text" id="temp_gp_gps_<?=$log_entry_id?>" name="temp_gp_gps_<?=$log_entry_id?>" class="" style="font-family:arial; font-size:10px; width:60px; height:24px;" onblur="auto_fill_goalpoint_location('<?=$log_entry_id?>')"/>
					</td>
					<td style="width:95px;">
						<span id="gp_location_text_<?=$log_entry_id?>" name="gp_location_text_<?=$log_entry_id?>" style="position:relative; top:4px;"></span>
					</td>
					<td style="width:175; padding-right:15px;">
						<input placeholder="Notes" type="text" id="temp_gp_notes_<?=$log_entry_id?>" name="temp_gp_notes_<?=$log_entry_id?>" class="" style="font-family:arial; font-size:10px; width:190px; height:24px;" value=""/>
					</td>
					<td style="width:70px;">
					</td>
					<td style="width:60px; text-align:center;">
						<img title="Add Goalpoint" src="/images/add_circle.png" style="position:relative; top:2px; left:15px; height:20px; cursor:pointer;" onclick="add_goalpoint('<?=$log_entry_id?>')"/>
					</td>
				</tr>
			</table>
			
			<div style="margin-left:120px; margin-top:30px;">
				<div class="heading">
					Contact Log
				</div>
				<div style="color:grey; font-style:italic; margin-top:5px;"></div>
				<hr style="width:715px;"><br>
			</div>
			<table style="margin-left:120px; margin-top:5px; margin-bottom:10px; line-height:10px; font-size:10px;">
				<tr style="font-weight:bold;">
					<td style="width:55px;">
						Time
					</td>
					<td style="width:55px;">
						GPS
					</td>
					<td style="width:60px;">
						Form of Contact
					</td>
					<td style="width:60px;">
						Result
					</td>
					<td style="width:170px;">
						Dispatcher Notes
					</td>
					<td style="width:170px;">
						Computer Notes
					</td>
					<td style="width:55px;" title="Expected Miles">
						Expected<br>Miles
					</td>
					<td style="width:40px;">
						Actual<br>Miles
					</td>
					<td style="width:50px;" title="Efficiency Rating">
						Efficiency Rating
					</td>
				</tr>
			</table>
			
			<div id="contact_attempts_div_<?=$log_entry_id?>">
				<!--AJAX GOES HERE!-->
				<span style="margin-left:450px;"><img style="height:20px;" src="/images/loading.gif"/></span>
			</div>
			**/
		?>
		<div style="margin-left:120px;">
			<div class="heading">
				<?php if($shift_report_recap_is_complete["is_complete"]):?>
					<img src="/images/load_status_8_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$shift_report_recap_is_complete["message"]?>');" title="<?=$shift_report_recap_is_complete["message"]?>">
				<?php else:?>
					<img src="/images/load_status_1_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$shift_report_recap_is_complete["message"]?>');"  title="<?=$shift_report_recap_is_complete["message"]?>">
				<?php endif;?>
				Shift Recap
			</div>
			<div style="color:grey; font-style:italic; margin-top:5px;"></div>
			<hr style="width:715px;"><br>
		</div>
		<table style="margin-left:120px; margin-top:5px; margin-bottom:0px; line-height:16px; width:725px;">
			<tr style="height:40px;">
				<td style="font-weight:bold; width:150px; height:40px;">
					Dispatcher Notes
				</td>
				<td style="width:550px;">
					<span class="details_<?=$log_entry_id?>"><?=$shift_report["dispatch_notes"]?></span>
					<textarea id="dispatch_notes" name="dispatch_notes" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$shift_report["dispatch_notes"]?></textarea>
				</td>
			</tr>
		</table>
		<?php
			/**
			<table style="margin-left:120px; margin-top:5px; margin-bottom:10px; line-height:16px;">
				<tr style="height:30px;">
					<td style="font-weight:bold; width:155px; height:40px;">
						Engine Idle Time
					</td>
					<td style="width:250px;">
						<span class="details_<?=$log_entry_id?>"><?=$shift_report["idle_time"]?></span>
						<input type="text" id="idle_time_<?=$log_entry_id?>" name="idle_time" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:100px; height:24px;" value="<?=$shift_report["idle_time"]?>"/>
					</td>
					<td style="font-weight:bold; width:150px; height:40px; text-align:right;">
						Total Map Miles
					</td>
					<td style="width:150px; text-align:right;">
						<span id="map_miles_<?=$log_entry_id?>" class=""><?=$shift_report["map_miles"]?></span>
					</td>
				</tr>
				<tr style="height:30px;">
					<td style="font-weight:bold; width:155px; height:40px;">
						Efficiency Rating
					</td>
					<td style="width:250px;">
						<span id="efficiency_rating_<?=$log_entry_id?>" class=""><?=$shift_report["efficiency_rating"]?></span>
					</td>
					<td style="font-weight:bold; width:150px; height:40px; text-align:right;">
						Total Odometer Miles
					</td>
					<td style="width:150px; text-align:right;">
						<span id="odometer_miles_<?=$log_entry_id?>" class=""><?=$shift_report["odometer_miles"]?></span>
					</td>
				</tr>
				<tr style="height:30px;">
					<td style="font-weight:bold; width:155px; height:40px;">
						Contact %
					</td>
					<td style="width:250px;">
						<span id="contact_percentage_<?=$log_entry_id?>" class=""><?=$shift_report["contact_percentage"]?>%</span>
					</td>
					<td style="font-weight:bold; width:150px; height:40px; text-align:right;">
						OOR %
					</td>
					<td style="width:150px; text-align:right;">
						<span id="shift_report_oor_<?=$log_entry_id?>" class=""><?=number_format($shift_report["oor"],2)?>%</span>
					</td>
				</tr>
			</table>
			**/
		?>
		<div id="attachment_div" style="margin-left:120px;">
			<div class="heading" style="">Attachments</div>
			<hr style="width:715px;">
			<?php if(!empty($attachments)):?>
					<?php foreach($attachments as $attachment):?>
						<div class="attachment_box" style="float:left;margin:5px;">
							<a target="_blank" style="text-decoration:none;color:#4e77c9;" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>" onclick=""><?=$attachment["attachment_name"]?></a>&nbsp;
						</div>
					<?php endforeach;?>
			<?php endif;?>
		</div>
		<div style="clear:both;"></div>
	</form>
	<form id="new_gp_form_<?=$log_entry_id?>" name="new_gp_form_<?=$log_entry_id?>">
		<input type="hidden" id="log_entry_id_<?=$log_entry_id?>" name="log_entry_id" value="<?=$log_entry_id?>">
		<input type="hidden" id="deadline_<?=$log_entry_id?>" name="deadline" value="">
		<input type="hidden" id="gp_type_<?=$log_entry_id?>" name="gp_type" value="">
		<input type="hidden" id="gp_gps_<?=$log_entry_id?>" name="gp_gps" value="">
		<input type="hidden" id="gp_location_<?=$log_entry_id?>" name="gp_location" value="">
		<input type="hidden" id="gp_notes_<?=$log_entry_id?>" name="gp_notes" value="">
	</form>
	<form id="edit_gp_form_<?=$log_entry_id?>" name="edit_gp_form_<?=$log_entry_id?>">
		<input type="hidden" id="edit_gp_log_entry_id_<?=$log_entry_id?>" name="log_entry_id" value="<?=$log_entry_id?>">
		<input type="hidden" id="edit_gp_id_<?=$log_entry_id?>" name="goalpoint_id" value="">
		<input type="hidden" id="edit_deadline_<?=$log_entry_id?>" name="deadline" value="">
		<input type="hidden" id="edit_gp_type_<?=$log_entry_id?>" name="gp_type" value="">
		<input type="hidden" id="edit_gp_gps_<?=$log_entry_id?>" name="gp_gps" value="">
		<input type="hidden" id="edit_gp_location_<?=$log_entry_id?>" name="gp_location" value="">
		<input type="hidden" id="edit_gp_notes_<?=$log_entry_id?>" name="gp_notes" value="">
	</form>
	<form id="new_ca_form_<?=$log_entry_id?>" name="new_ca_form_<?=$log_entry_id?>">
		<input type="hidden" id="new_ca_log_entry_id_<?=$log_entry_id?>" name="log_entry_id" value="<?=$log_entry_id?>">
		<input type="hidden" id="new_ca_time_<?=$log_entry_id?>" name="new_ca_time" value="">
		<input type="hidden" id="new_ca_gps_<?=$log_entry_id?>" name="new_ca_gps" value="">
		<input type="hidden" id="new_ca_method_<?=$log_entry_id?>" name="new_ca_method" value="">
		<input type="hidden" id="new_ca_result_<?=$log_entry_id?>" name="new_ca_result" value="">
		<input type="hidden" id="new_ca_notes_<?=$log_entry_id?>" name="new_ca_notes" value="">
		<input type="hidden" id="new_ca_exp_miles_<?=$log_entry_id?>" name="new_ca_exp_miles" value="">
		<input type="hidden" id="new_ca_actual_miles_<?=$log_entry_id?>" name="new_ca_actual_miles" value="">
	</form>
</div>

<div id="gp_complete_dialog_<?=$log_entry_id?>" title="Goalpoint Complete" style="display:none;">
	<form id="complete_gp_form_<?=$log_entry_id?>" name="complete_gp_form_<?=$log_entry_id?>">
		<input type="hidden" id="complete_gp_id_<?=$log_entry_id?>" name="complete_gp_id" value="">
		<table style="margin:40;">
			<tr>
				<td style="width:200px;">
					Expected Datetime
				</td>
				<td style="width:200px;">
					<span id="complete_gp_expected_time_text_<?=$log_entry_id?>"></span>
				</td>
			</tr>
			<tr>
				<td style="padding-top:5px;">
					Completion Datetime
				</td>
				<td>
					<input placeholder="Date Time" type="text" id="complete_gp_time_<?=$log_entry_id?>" name="complete_gp_time" class="" style="font-family:arial; font-size:12px; width:150px; height:24px;" value=""/>
				</td>
			</tr>
		</table>
	</form>
</div>

<div id="missed_goalpoints_dialog_<?=$log_entry_id?>" title="Overdue Goalpoints" style="display:none;">
	<!--AJAX HERE!-->
</div>