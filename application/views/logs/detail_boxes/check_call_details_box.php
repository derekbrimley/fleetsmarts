<style>	
	.edit_<?=$log_entry_id?>
	{
		display:none;
	}
</style>

<script>
	//OPENS EDIT VIEW
	function edit_event(log_entry_id)
	{
		$('.edit_'+log_entry_id).css({"display":"block"});
		$('.details_'+log_entry_id).css({"display":"none"});
	}
	
	//CALCULATE OOR
	function calc_oor()
	{
		var map_miles = $("#map_miles").val();
		var odometer_miles = $("#odometer_miles").val();
		
		var isValid = true;
		
		//VALIDATE INPUTS FOR SAVE
		if(isNaN($("#map_miles").val()))
		{
			isValid = false;
			alert("Map Miles must be a number");
			$("#map_miles").val('');
		}
		
		if(isNaN($("#odometer_miles").val()))
		{
			isValid = false;
			alert("Odometer Miles must be a number");
			$("#odometer_miles").val('');
		}
		
		//alert(map_miles);
		//alert(odometer_miles);
		if(isValid)
		{
			var oor = Math.round(((odometer_miles - map_miles)/map_miles)*1000)/10;
			
			$("#oor_text").text(oor+"%");
			$("#oor").val(oor);
		}
	}
	
	//SAVE CHECK CALL EDIT
	function save_check_call(log_entry_id)
	{
		var isValid = true;
	
		//VALIDATE INPUTS FOR SAVE
		if(isNaN($("#map_miles").val()))
		{
			isValid = false;
			alert("Map Miles must be a number!");
		}
		
		if(isNaN($("#odometer_miles").val()))
		{
			isValid = false;
			alert("Odometer Miles must be a number!");
		}
		
		if(isNaN($("#last_mpg").val()))
		{
			isValid = false;
			alert("Reccent MPG must be a number!");
		}
		
		if(isValid)
		{
			open_event_details(log_entry_id);
			$('#export_leg_dialog_'+log_entry_id).dialog("open");
		
			var this_div = "none";
		
			var dataString = $("#check_call_form_"+log_entry_id).serialize();
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/save_check_call")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						open_event_details(log_entry_id);
						
					},
					404: function(){
						// Page not found
						alert('page not found');
						
					},
					500: function(response){
						// Internal server error
						alert("500 error!")
					}
				}
			});//END AJAX
		}
		else
		{
			$("#save_icon_"+log_entry_id).attr("src","/images/save.png");
		}
	}
	
	function upload_d1_logbook(log_entry_id)
	{
		if($("#file_1").val())
		{
			$('#upload_file_form_1').submit();
			setTimeout(function()
			{
				save_check_call(log_entry_id)
			},900);
		}
	}
	
	function upload_logbooks(log_entry_id)
	{
		$('#upload_file_form').submit();
		
		setTimeout(function()
		{
			save_check_call(log_entry_id)
		},900) ;
	}
</script>
<?php
	$is_check_call_complete = check_call_is_complete($check_call);
	$check_call_hos_is_complete = check_call_hos_is_complete($check_call);
	$check_call_performance_is_complete = check_call_performance_is_complete($check_call);
	$check_call_evaluation_is_complete = check_call_evaluation_is_complete($check_call);
	$check_call_plan_is_complete = check_call_plan_is_complete($check_call);
	$check_call_recap_is_complete = check_call_recap_is_complete($check_call);
?>

<div id="script_div_<?=$log_entry_id?>">
	<!-- AJAX SCRIPT GOES HERE !-->
</div>
<div style="font-size:12px;">
	<?php if(empty($log_entry["locked_datetime"])): ?>
		<div style="width:20px; height:45px; float:right;">
			<img id="refresh_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:13px; position:relative; left:0px;" src="/images/refresh.png" title="Refresh" onclick="this.src='/images/loading.gif'; open_event_details('<?=$log_entry_id?>')"/>
			<img id="edit_icon" class="details_<?=$log_entry_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:0px;" src="/images/edit.png" title="Edit" onclick="edit_event('<?=$log_entry_id?>')"/>
			<img id="save_icon_<?=$log_entry_id?>" class="edit_<?=$log_entry_id?>" style="display:none; margin-bottom:13px; margin-right:15px; cursor:pointer; height:14px; position:relative; left:1px;" src="/images/save.png" title="Save" onclick="this.src='/images/loading.gif';save_check_call('<?=$log_entry_id?>');"/>
			<img id="attachment_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:18px; position:relative; left:3px;" src="/images/paper_clip2.png" title="Attach Document" onclick="open_file_upload('<?=$log_entry_id?>')"/>
			<img id="delete_icon" style="display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/trash.png" title="Delete" onclick="delete_event('<?=$log_entry_id?>')"/>
		</div>
	<?php else: ?>
		<div style="width:20px; float:right;">
			<img id="locked_icon" style="display:block; margin-bottom:12px; margin-right:15px; height:15px; position:relative; left:2px;" src="/images/locked.png" title="Locked <?=date("n/j H:i",strtotime($log_entry["locked_datetime"]))?>"/>
		</div>
	<?php endif; ?>
	<div style="width:60px; float:left;">
		<div style="font-size:12px; font-weight:bold; width:80px;">
			<?php if($is_check_call_complete["is_complete"]):?>
				<img src="/images/load_status_8_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$is_check_call_complete["message"]?>');" title="<?=$is_check_call_complete["message"]?>">
			<?php else:?>
				<img src="/images/load_status_1_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$is_check_call_complete["message"]?>');"  title="<?=$is_check_call_complete["message"]?>">
			<?php endif;?>
			Check Call
		</div>
	</div>
	
	<div style="margin-left:120px;">
		<div class="heading">
			<?php if($check_call_hos_is_complete["is_complete"]):?>
				<img src="/images/load_status_8_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$check_call_hos_is_complete["message"]?>');" title="<?=$check_call_hos_is_complete["message"]?>">
			<?php else:?>
				<img src="/images/load_status_1_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$check_call_hos_is_complete["message"]?>');"  title="<?=$check_call_hos_is_complete["message"]?>">
			<?php endif;?>
			Yesterday's HOS Logs
		</div>
		<div style="color:grey; font-style:italic; margin-top:5px;">To be collected and uploaded by the dispatcher admin</div>
		<hr style="width:715px;"><br>
	</div>
	<input type="hidden" id="check_call_id" name="check_call_id" value="<?=$check_call["id"]?>"/>
	<table style="margin-left:120px; margin-top:5px; margin-bottom:10px; line-height:16px;">
		<tr style="height:40px;">
			<td style="font-weight:bold; min-width:175px;;">
				Driver 1 Logbook
			</td>
			<td style="width:150px;">
				<?php if(!empty($check_call["d1_logbook_file_guid"])):?>
					<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$check_call["d1_logbook_file_guid"]?>" onclick="">Driver 1 Logbook</a>
				<?php endif;?>
			</td>
		</tr>
		<tr style="height:40px;">
			<td style="font-weight:bold;">
				Driver 2 Logbook
			</td>
			<td style="width:150px;">
				<?php if(!empty($check_call["d2_logbook_file_guid"])):?>
					<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$check_call["d2_logbook_file_guid"]?>" onclick="">Driver 2 Logbook</a>
				<?php endif;?>
			</td>
		</tr>
	</table>
	<form id="check_call_form_<?=$log_entry_id?>" name="check_call_form">
	
		<div style="margin-left:120px;">
			<div class="heading">
				<?php if($check_call_performance_is_complete["is_complete"]):?>
					<img src="/images/load_status_8_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$check_call_performance_is_complete["message"]?>');" title="<?=$check_call_performance_is_complete["message"]?>">
				<?php else:?>
					<img src="/images/load_status_1_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$check_call_performance_is_complete["message"]?>');"  title="<?=$check_call_performance_is_complete["message"]?>">
				<?php endif;?>
				Yesterday's Performance
			</div>
			<div style="color:grey; font-style:italic; margin-top:5px;">To be completed by the dispatcher admin during the morning check call and read to the driver by night dispatch during the evening check call</div>
			<hr style="width:715px;"><br>
		</div>
		<table style="margin-left:120px; margin-top:5px; margin-bottom:10px; line-height:16px; width:725px;">
			<input type="hidden" id="log_entry_id" name="log_entry_id" value="<?=$log_entry_id?>">
			<tr style="height:40px;">
				<td style="font-weight:bold; width:175px;">
					Previous Night Plan
				</td>
				<td>
					<?=$previous_check_call["night_plan"]?>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold; width:175px;">
					Evening Recap
				</td>
				<td>
					<?=$previous_check_call["night_dispatch_eval"]?>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold; width:150px; height:40px;">
					Night Recap
				</td>
				<td style="width:550px;">
					<span class="details_<?=$log_entry_id?>"><?=$check_call["night_recap"]?></span>
					<textarea id="night_recap" name="night_recap" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$check_call["night_recap"]?></textarea>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Previous Fuel Plan
				</td>
				<td>
					<?=$previous_check_call["fuel_plan"]?>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Fuel Plan Followed?
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["fuel_plan_followed"]?></span>
					<?php
						$options = array(
							"Select" => "Select",
							"Yes" => "Yes",
							"No" => "No",
						);
					?>
					<?php echo form_dropdown("fuel_plan_followed",$options,$check_call["fuel_plan_followed"],"id='goal_met' class='edit_".$log_entry_id."'style='position:relative; right:3px; bottom:3px; width:70px;'");?>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Previous Paperwork Plan
				</td>
				<td>
					<?=$previous_check_call["paperwork_plan"]?>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Paperwork Plan Followed?
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["paperwork_plan_followed"]?></span>
					<?php
						$options = array(
							"Select" => "Select",
							"Yes" => "Yes",
							"No" => "No",
						);
					?>
					<?php echo form_dropdown("paperwork_plan_followed",$options,$check_call["paperwork_plan_followed"],"id='goal_met' class='edit_".$log_entry_id."'style='position:relative; right:3px; bottom:3px; width:70px;'");?>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Previous Reefer Instructions
				</td>
				<td>
					<?=$previous_check_call["reefer_instructions"]?>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Reefer Instructions Followed?
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["reefer_instructions_followed"]?></span>
					<?php
						$options = array(
							"Select" => "Select",
							"Yes" => "Yes",
							"No" => "No",
						);
					?>
					<?php echo form_dropdown("reefer_instructions_followed",$options,$check_call["reefer_instructions_followed"],"id='goal_met' class='edit_".$log_entry_id."'style='position:relative; right:3px; bottom:3px; width:70px;'");?>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Previous Morning Goal
				</td>
				<td>
					<?=$previous_check_call["morning_goal"]?>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Morning Goal Met?
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["goal_met"]?></span>
					<?php
						$options = array(
							"Select" => "Select",
							"Yes" => "Yes",
							"No" => "No",
						);
					?>
					<?php echo form_dropdown("goal_met",$options,$check_call["goal_met"],"id='goal_met' class='edit_".$log_entry_id."'style='position:relative; right:3px; bottom:3px; width:70px;'");?>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Map Miles
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["map_miles"]?></span>
					<div class="edit_<?=$log_entry_id?>">
						<input type="text" id="map_miles" name="map_miles" value="<?=$check_call["map_miles"]?>" style="display:inline; position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:70px; height:20px;" onchange="calc_oor()"/>
						<span style="color:grey; font-style:italic; position:relative; bottom:5px;">From yesterday's morning check call</span>
					</div>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Odometer Miles
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["odometer_miles"]?></span>
					<div class="edit_<?=$log_entry_id?>">
						<input type="text" id="odometer_miles" name="odometer_miles" value="<?=$check_call["odometer_miles"]?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:70px; height:20px;" onchange="calc_oor()"/>
						<span style="color:grey; font-style:italic; position:relative; bottom:5px;">From yesterday's morning check call</span>
					</div>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					OOR
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["oor"]?>%</span>
					<span id="oor_text"class="edit_<?=$log_entry_id?>"><?=$check_call["oor"]?></span>
					<input type="hidden" id="oor" name="oor" value="<?=$check_call["oor"]?>"/>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold; width:125px; height:40px;">
					Recent MPG
				</td>
				<td style="width:550px;">
					<span class="details_<?=$log_entry_id?>"><?=$check_call["last_mpg"]?></span>
					<input type="text" id="last_mpg" name="last_mpg" class="edit_<?=$log_entry_id?>" value="<?=$check_call["last_mpg"]?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:70px; height:20px;"/>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold; width:125px; height:40px;">
					Morning Check Call Audio
				</td>
				<td style="width:550px;">
					<?php if(!empty($check_call["morning_checkcall_guid"])):?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$check_call["morning_checkcall_guid"]?>" onclick="">Morning Check Call</a>
					<?php endif;?>
				</td>
			</tr>
		<table>
		
		<div style="margin-left:120px;">
			<div class="heading">
				<?php if($check_call_evaluation_is_complete["is_complete"]):?>
					<img src="/images/load_status_8_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$check_call_evaluation_is_complete["message"]?>');" title="<?=$check_call_evaluation_is_complete["message"]?>">
				<?php else:?>
					<img src="/images/load_status_1_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$check_call_evaluation_is_complete["message"]?>');"  title="<?=$check_call_evaluation_is_complete["message"]?>">
				<?php endif;?>
				Yestersday's Driver Evaluation
			</div>
			<div style="color:grey; font-style:italic; margin-top:5px;">To be completed by the dispatcher in the following areas: (1) Ability to work with others (2) Attitude towards problem solving (3) Skill Level</div>
			<hr style="width:715px;">
		</div>
		<table style="margin-left:120px; margin-bottom:10px; line-height:16px; width:725px;">
			<tr style="height:40px; display:none;">
				<td style="font-weight:bold; width:175px;">
				</td>
				<td style="padding-bottom:10px;">
					<div class="edit_<?=$log_entry_id?>" style="color:grey; font-style:italic; margin-top:5px;">
						<span style="font-weight:bold;">Ability to work with others</span> - [10] Driver got along with his codriver and was professional and pleasant for dispatch to work with [1] Driver was un-professional and difficult for his codriver and dispatch to work with
					</div>
					<div class="edit_<?=$log_entry_id?>" style="color:grey; font-style:italic; margin-top:5px;">
						<span style="font-weight:bold;">Attitude towards problem solving</span> - [10] The driver tried to overcome problems to keep the truck moving [1] The driver used solvable problems as excuses to stop the truck
					</div>
					<div class="edit_<?=$log_entry_id?>" style="color:grey; font-style:italic; margin-top:5px;">
						<span style="font-weight:bold;">Skill Level</span> - [10] The driver showed good tucking knowledge, routing ability, and driving skills [1] The driver made poor decisions resulting in damage, loss of time, or OOR miles
					</div>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold; min-width:175px;">
					Ability to Work w/ Others
					<div style="color:grey; font-style:italic; font-weight:normal; margin-top:5px;">
						Driver 1
					</div>
				</td>
				<td style="min-width:75px;">
					<span class="details_<?=$log_entry_id?>"><?=$check_call["d1_pleasantness"]?></span>
					<?php
						$options = array(
							"" => "Select",
							"1" => "1",
							"2" => "2",
							"3" => "3",
							"4" => "4",
							"5" => "5 - Average",
							"6" => "6",
							"7" => "7",
							"8" => "8",
							"9" => "9",
							"10" => "10",
						);
					?>
					<?php echo form_dropdown("d1_pleasantness",$options,$check_call["d1_pleasantness"],"id='goal_met' class='edit_".$log_entry_id."'style='position:relative; right:3px; bottom:3px; width:70px;'");?>
				</td>
				<td style="min-width:510px;">
					<div class="edit_<?=$log_entry_id?>" style="color:grey; font-style:italic;">
						[10] Driver got along with his codriver and was professional and pleasant for dispatch to work with [1] Driver was un-professional and difficult for his codriver and dispatch to work with
					</div>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Attitude
					<div style="color:grey; font-style:italic; font-weight:normal; margin-top:5px;">
						Driver 1
					</div>
				</td>
				<td style="">
					<span class="details_<?=$log_entry_id?>"><?=$check_call["d1_attitude"]?></span>
					<?php
						$options = array(
							"" => "Select",
							"1" => "1",
							"2" => "2",
							"3" => "3",
							"4" => "4",
							"5" => "5 - Average",
							"6" => "6",
							"7" => "7",
							"8" => "8",
							"9" => "9",
							"10" => "10",
						);
					?>
					<?php echo form_dropdown("d1_attitude",$options,$check_call["d1_attitude"],"id='goal_met' class='edit_".$log_entry_id."'style='position:relative; right:3px; bottom:3px; width:70px;'");?>
				</td>
				<td style="">
					<div class="edit_<?=$log_entry_id?>" style="color:grey; font-style:italic;">
						[10] The driver tried to overcome problems to keep the truck moving [1] The driver used solvable problems as excuses to stop the truck
					</div>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Skill Level
					<div style="color:grey; font-style:italic; font-weight:normal; margin-top:5px;">
						Driver 1
					</div>
				</td>
				<td style="">
					<span class="details_<?=$log_entry_id?>"><?=$check_call["d1_skill"]?></span>
					<?php
						$options = array(
							"" => "Select",
							"1" => "1",
							"2" => "2",
							"3" => "3",
							"4" => "4",
							"5" => "5 - Average",
							"6" => "6",
							"7" => "7",
							"8" => "8",
							"9" => "9",
							"10" => "10",
						);
					?>
					<?php echo form_dropdown("d1_skill",$options,$check_call["d1_skill"],"id='goal_met' class='edit_".$log_entry_id."'style='position:relative; right:3px; bottom:3px; width:70px;'");?>
				</td>
				<td style="">
					<div class="edit_<?=$log_entry_id?>" style="color:grey; font-style:italic;">
						[10] The driver showed good trucking knowledge, routing ability, and driving skills [1] The driver made poor decisions resulting in damage, loss of time, or OOR miles
					</div>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Driver Evaluation
					<div style="color:grey; font-style:italic; font-weight:normal; margin-top:5px;">
						Driver 1
					</div>
				</td>
				<td colspan="2">
					<span class="details_<?=$log_entry_id?>"><?=$check_call["d1_eval_notes"]?></span>
					<textarea id="d1_eval_notes" name="d1_eval_notes" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$check_call["d1_eval_notes"]?></textarea>
				</td>
			</tr>
			<tr></tr>
			<tr style="height:40px;">
				<td style="font-weight:bold; min-width:175px;">
					Ability to Work w/ Others
					<div style="color:grey; font-style:italic; font-weight:normal; margin-top:5px;">
						Driver 2
					</div>
				</td>
				<td style="min-width:75px;">
					<span class="details_<?=$log_entry_id?>"><?=$check_call["d2_pleasantness"]?></span>
					<?php
						$options = array(
							"" => "Select",
							"1" => "1",
							"2" => "2",
							"3" => "3",
							"4" => "4",
							"5" => "5 - Average",
							"6" => "6",
							"7" => "7",
							"8" => "8",
							"9" => "9",
							"10" => "10",
						);
					?>
					<?php echo form_dropdown("d2_pleasantness",$options,$check_call["d2_pleasantness"],"id='goal_met' class='edit_".$log_entry_id."'style='position:relative; right:3px; bottom:3px; width:70px;'");?>
				</td>
				<td style="min-width:510px;">
					<div class="edit_<?=$log_entry_id?>" style="color:grey; font-style:italic;">
						[10] Driver got along with his codriver and was professional and pleasant for dispatch to work with [1] Driver was un-professional and difficult for his codriver and dispatch to work with
					</div>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Attitude
					<div style="color:grey; font-style:italic; font-weight:normal; margin-top:5px;">
						Driver 2
					</div>
				</td>
				<td style="">
					<span class="details_<?=$log_entry_id?>"><?=$check_call["d2_attitude"]?></span>
					<?php
						$options = array(
							"" => "Select",
							"1" => "1",
							"2" => "2",
							"3" => "3",
							"4" => "4",
							"5" => "5 - Average",
							"6" => "6",
							"7" => "7",
							"8" => "8",
							"9" => "9",
							"10" => "10",
						);
					?>
					<?php echo form_dropdown("d2_attitude",$options,$check_call["d2_attitude"],"id='goal_met' class='edit_".$log_entry_id."'style='position:relative; right:3px; bottom:3px; width:70px;'");?>
				</td>
				<td style="">
					<div class="edit_<?=$log_entry_id?>" style="color:grey; font-style:italic;">
						[10] The driver tried to overcome problems to keep the truck moving [1] The driver used solvable problems as excuses to stop the truck
					</div>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Skill Level
					<div style="color:grey; font-style:italic; font-weight:normal; margin-top:5px;">
						Driver 2
					</div>
				</td>
				<td style="">
					<span class="details_<?=$log_entry_id?>"><?=$check_call["d2_skill"]?></span>
					<?php
						$options = array(
							"" => "Select",
							"1" => "1",
							"2" => "2",
							"3" => "3",
							"4" => "4",
							"5" => "5 - Average",
							"6" => "6",
							"7" => "7",
							"8" => "8",
							"9" => "9",
							"10" => "10",
						);
					?>
					<?php echo form_dropdown("d2_skill",$options,$check_call["d2_skill"],"id='goal_met' class='edit_".$log_entry_id."'style='position:relative; right:3px; bottom:3px; width:70px;'");?>
				</td>
				<td style="">
					<div class="edit_<?=$log_entry_id?>" style="color:grey; font-style:italic;">
						[10] The driver showed good trucking knowledge, routing ability, and driving skills [1] The driver made poor decisions resulting in damage, loss of time, or OOR miles
					</div>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Driver Evaluation
					<div style="color:grey; font-style:italic; font-weight:normal; margin-top:5px;">
						Driver 2
					</div>
				</td>
				<td colspan="2">
					<span class="details_<?=$log_entry_id?>"><?=$check_call["d2_eval_notes"]?></span>
					<textarea id="d2_eval_notes" name="d2_eval_notes" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$check_call["d2_eval_notes"]?></textarea>
				</td>
			</tr>
		</table>
		
		<div style="margin-left:120px;">
			<div class="heading">
				<?php if($check_call_plan_is_complete["is_complete"]):?>
					<img src="/images/load_status_8_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$check_call_plan_is_complete["message"]?>');" title="<?=$check_call_plan_is_complete["message"]?>">
				<?php else:?>
					<img src="/images/load_status_1_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$check_call_plan_is_complete["message"]?>');"  title="<?=$check_call_plan_is_complete["message"]?>">
				<?php endif;?>
				Today's Night Plan
			</div>
			<div style="color:grey; font-style:italic; margin-top:5px;">To be completed by the dispatcher and fleet manager before night shift begins and communicated to the driver by night dispatch</div>
			<hr style="width:715px;"><br>
		</div>
		<table style="margin-left:120px; margin-top:5px; margin-bottom:10px; line-height:16px; width:725px;">
			<tr style="height:40px;">
				<td style="font-weight:bold; width:175px; height:40px;">
					Today's Day Recap
				</td>
				<td style="width:550px;">
					<span class="details_<?=$log_entry_id?>"><?=$check_call["day_recap"]?></span>
					<textarea id="day_recap" name="day_recap" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$check_call["day_recap"]?></textarea>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Plan For Night
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["night_plan"]?></span>
					<textarea id="night_plan" name="night_plan" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$check_call["night_plan"]?></textarea>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Fuel Plan
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["fuel_plan"]?></span>
					<textarea id="fuel_plan" name="fuel_plan" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$check_call["fuel_plan"]?></textarea>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold; width:150px;">
					Paperwork Plan
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["paperwork_plan"]?></span>
					<textarea id="paperwork_plan" name="paperwork_plan" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$check_call["paperwork_plan"]?></textarea>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Morning Goal (7am)
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["morning_goal"]?></span>
					<textarea id="morning_goal" name="morning_goal" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$check_call["morning_goal"]?></textarea>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Reefer Instructions
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["reefer_instructions"]?></span>
					<textarea id="reefer_instructions" name="reefer_instructions" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$check_call["reefer_instructions"]?></textarea>
				</td>
			</tr>
		</table>
		<div style="margin-left:120px;">
			<div class="heading">
				<?php if($check_call_recap_is_complete["is_complete"]):?>
					<img src="/images/load_status_8_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$check_call_recap_is_complete["message"]?>');" title="<?=$check_call_recap_is_complete["message"]?>">
				<?php else:?>
					<img src="/images/load_status_1_icon.png" style="height:15px; position:relative; top:4px; cursor:pointer;" onclick="alert('<?=$check_call_recap_is_complete["message"]?>');"  title="<?=$check_call_recap_is_complete["message"]?>">
				<?php endif;?>
				Evening Recap
				</div>
			<div style="color:grey; font-style:italic; margin-top:5px;">To be completed by night dispatch during the evening shift</div>
			<hr style="width:715px;"><br>
		</div>
		<table style="margin-left:120px; margin-top:5px; margin-bottom:10px; line-height:16px; width:725px;">
			<tr style="height:40px;">
				<td style="font-weight:bold; width:175px; height:40px;">
					Evening Check Call Audio
				</td>
				<td style="width:550px;">
					<?php if(!empty($check_call["evening_checkcall_guid"])):?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$check_call["evening_checkcall_guid"]?>" onclick="">Evening Check Call</a>
					<?php endif;?>
				</td>
			</tr>
			<tr style="height:40px;">
				<td style="font-weight:bold;">
					Evening Recap
				</td>
				<td>
					<span class="details_<?=$log_entry_id?>"><?=$check_call["night_dispatch_eval"]?></span>
					<textarea id="night_dispatch_eval" name="night_dispatch_eval" class="edit_<?=$log_entry_id?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:550px;"><?=$check_call["night_dispatch_eval"]?></textarea>
				</td>
			</tr>
		</table>
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
</div>