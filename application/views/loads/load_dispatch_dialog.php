<?php
	date_default_timezone_set('US/Mountain');
	
	$row_id = $load["id"];
	
	$load_id = $load["id"];
	
	//GET TRUCK
	$where = null;
	$where["id"] = $load["load_truck_id"];
	$truck = db_select_truck($where);
	
	$truck_id = $truck["id"];
	
	//GET TRAILER
	$where = null;
	$where["id"] = $load["load_trailer_id"];
	$trailer = db_select_trailer($where);
	
	$geocode = null;
	$map_info = null;
	$drive_hrs_to_dest = null;
	$hours_to_spare = null;
	$next_dest_gp = null;
	$map_till_fuel_info = null;
	if(!empty($current_geopoint_goalpoint["gps"]))
	{
		$geocode = reverse_geocode($current_geopoint_goalpoint["gps"]);
		
		//GET NEXT GOALPOINT WITH OR WITH OUT DEADLINE
		$where = null;
		$where = " load_id = $load_id AND truck_id = $truck_id AND completion_time IS NULL AND gp_order = (SELECT MIN(gp_order) FROM `goalpoint` WHERE load_id = $load_id AND truck_id = $truck_id AND completion_time IS NULL AND gp_type <> 'Current Geopoint')";
		$next_gp = db_select_goalpoint($where);
		
		//GET NEXT GOALPOINT W DEADLINE
		$where = null;
		$where = " load_id = $load_id AND truck_id = $truck_id AND completion_time IS NULL AND gp_order = (SELECT MIN(gp_order) FROM `goalpoint` WHERE load_id = $load_id AND truck_id = $truck_id AND completion_time IS NULL AND deadline IS NOT NULL AND gp_type <> 'Current Geopoint')";
		$next_dest_gp = db_select_goalpoint($where);
		
		//GET ALL GOALPOINTS BETWEEN CURRENT GOALPOINT AND NEXT DEST
		$where = null;
		$where = " load_id = $load_id AND truck_id = $truck_id AND arrival_departure = 'Arrival' AND gp_order >  ".$current_geopoint_goalpoint["gp_order"];
		$goalpoints_to_go_till_end = db_select_goalpoints($where,"gp_order");
		
		if(!empty($next_dest_gp))
		{
			//GET ALL GOALPOINTS BETWEEN CURRENT GOALPOINT AND NEXT DEST
			$where = null;
			$where = " load_id = $load_id AND truck_id = $truck_id AND gp_order > ".$current_geopoint_goalpoint["gp_order"]." AND gp_order <= ".$next_dest_gp["gp_order"];
			$goalpoints_to_go = db_select_goalpoints($where,"gp_order");
		
			//GET MAP INFO FOR MAP REQUEST
			$map_events = null;
			
			$starting_event["gps_coordinates"] = $current_geopoint_goalpoint["gps"];
			$map_events[] = $starting_event;
			
			foreach($goalpoints_to_go as $gp)
			{
				$event = null;
				$event["gps_coordinates"] = $gp["gps"];
				$map_events[] = $event;
			}
			
			$map_info = get_map_info($map_events);
			
			$drive_hrs_to_dest = $map_info["map_miles"]/50;
			$hours_till_deadline = 	(strtotime($next_dest_gp["deadline"]) - time())/60/60;
			$hours_to_spare = $hours_till_deadline - $drive_hrs_to_dest;
		}
		
		
		//FIGURE OUT HOW FAR TO NEXT FUEL STOP
		//GET NEXT FUEL STOP
		$where = null;
		$where = " load_id = $load_id AND truck_id = $truck_id AND completion_time IS NULL AND gp_order = (SELECT MIN(gp_order) FROM `goalpoint` WHERE load_id = $load_id AND truck_id = $truck_id AND completion_time IS NULL AND gp_type = 'Fuel')";
		$next_fuel_gp = db_select_goalpoint($where);
		
		if(!empty($next_fuel_gp))
		{
			//GET ALL GOALPOINTS BETWEEN CURRENT GOALPOINT AND NEXT FUEL
			$where = null;
			$where = " load_id = $load_id AND truck_id = $truck_id AND gp_order > ".$current_geopoint_goalpoint["gp_order"]." AND gp_order <= ".$next_fuel_gp["gp_order"];
			$goalpoints_till_fuel = db_select_goalpoints($where,"gp_order");
		}
		else
		{
			//GET ALL GOALPOINTS BETWEEN CURRENT GOALPOINT AND NEXT FUEL
			$where = null;
			$where = " load_id = $load_id AND truck_id = $truck_id AND gp_order > ".$current_geopoint_goalpoint["gp_order"];
			$goalpoints_till_fuel = db_select_goalpoints($where,"gp_order");
		}
		
		if(!empty($goalpoints_till_fuel))
		{
			//GET MAP INFO FOR MAP REQUEST
			$map_events = null;
			
			$starting_event["gps_coordinates"] = $current_geopoint_goalpoint["gps"];
			$map_events[] = $starting_event;
			
			foreach($goalpoints_till_fuel as $gp)
			{
				$event = null;
				$event["gps_coordinates"] = $gp["gps"];
				$map_events[] = $event;
			}
			
			$map_till_fuel_info = get_map_info($map_events);
		}
		
		
	}
	
	
?>
<script>
	$('#dispatch_current_date').datepicker({ showAnim: 'blind' });
	
	check_fuel_capacity();
	
	var needs_load_plan = false;
	
	var missing_deadline = false;
	<?php if(empty($next_dest_gp)):?>
		alert("This load plan doesn't have any deadlines! Update the load plan with some deadlines and goals for the driver before proceeding.")
		missing_deadline = true;
	<?php endif;?>
	
	function check_fuel_capacity()
	{
		if(!$("#fuel_capacity").val())
		{
			alert("This truck is missing a Fuel Tank Capacity attribute! This needs to be updated in the Equipment tab.");
		}
	}
	
	function calc_fuel_range()
	{
		var fuel_capacity = $("#fuel_capacity").val();
		var fuel_level = $("#truck_fuel").val();
		var fuel_range = Math.round(fuel_capacity*fuel_level*6*100)/100;//6 mpg
		
		if(fuel_capacity && fuel_level)
		{
		}
		else
		{
			fuel_range = 0;
		}
		$("#fuel_range_span").html(fuel_range);
		
		if(!$("#miles_till_fuel").val() || fuel_range < $("#miles_till_fuel").val())
		{
			$("#miles_till_fuel_link").css('color','red');
			alert('This truck needs a Fuel Plan! Tell the driver you will need to make a fuel plan and call him back.');
			needs_load_plan = true;
		}
		else
		{
			$("#miles_till_fuel_link").css('color','green');
			needs_load_plan = false;
		}
	}
	
	function driver_answer_changed()
	{
		if($("#driver_answer").val() == "No Answer")
		{
			alert('For the Audio Upload, upload the audio of you leaving the driver a message. Send the driver a text telling him that you tried calling and that you will try again in 15 minutes. Call the driver again in 15 minutes.');
		}
		
		if($("#driver_answer").val() == "Answered" || $("#driver_answer").val() == "No Answer")
		{
			$("#audio_upload_row").show();
		}
		else
		{
			$("#audio_upload_row").hide();
		}
	}
</script>
<style>
	.edit_input
	{
		width:80px;
		height:20px;
	}
	
	.file_input
	{
		width:120px;
	}
	
	.field_name
	{
		font-weight:bold;
	}
	
	.load_dispatch_current_profile_table tr
	{
		height:30px;
	}
	
	#script_table tr
	{
		height:inherit;
	}
	
	#script_table td
	{
		padding-bottom:12px;
	}
	
	.load_dispatch_update_table tr
	{
		height:30px;
	}
	
	.details_box hr
	{
		width:910px;
	}
	
	.script
	{
		color:grey;
		font-size:14px;
		line-height:16px;
	}
	
	.variable
	{
		font-size:14px;
		font-weight:bold;
		color:#f97606;
	}
	
</style>
<div style="min-height:95px;" class="details_box">
	<form id="load_dispatch_update_form"  enctype="multipart/form-data">
		<input type="hidden" id="dispatch_update_load_id" name="dispatch_update_load_id" value="<?=$load["id"]?>"/>
		<input type="hidden" id="current_geopoint_goalpoint_id" name="current_geopoint_goalpoint_id" value="<?=$current_geopoint_goalpoint["id"]?>"/>
		<div class="heading">
			Current Status
		</div>
		<hr style="">
		<div style="font-size:12px;">
			<table class="load_dispatch_current_profile_table" style="float:left; width:250px;">
				<tr>
					<td style="width:70px;" class="field_name">	
						Driver
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<span class=""><?=$load["client"]["client_nickname"]?></span>
					</td>
				</tr>
				<tr>
					<td style="" class="field_name">	
						Truck
					</td>
					<td>
						<span class=""><?=$truck["truck_number"]?></span>
					</td>
				</tr>
				<tr>
					<td style="" class="field_name">	
						Trailer
					</td>
					<td>
						<span class=""><?=$trailer["trailer_number"]?></span>
					</td>
				</tr>
			</table>
			<table class="load_dispatch_current_profile_table" style="float:left; margin-left:40px; width:220px;">
				<tr>
					<td style="width:130px;" class="field_name">	
						Current Location
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<span id="dispatch_current_location" class=""><?=$geocode["city"]?>, <?=$geocode["state"]?></span>
					</td>
				</tr>
				<tr>
					<td style="width:130px;" class="field_name">	
						Next Destination
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<?=$next_gp["location"]?>
					</td>
				</tr>
				<tr>
					<td style="width:130px;" class="field_name">	
						Next Deadline
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<?php if(empty($next_dest_gp)):?>
							<span style="color:red; font-weight:red">Missing</span>
						<?php else:?>
							<?=$next_dest_gp["location"]?>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:130px;" class="field_name">	
						Deadline
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<?php if(empty($next_dest_gp)):?>
							<span style="color:red; font-weight:red">Missing</span>
						<?php else:?>
							<?=date("m/d/y H:i",strtotime($next_dest_gp["deadline"]))?>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:130px;" class="field_name">	
						Miles to Deadline.
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<?php if(empty($next_dest_gp)):?>
							<span style="color:red; font-weight:red">Missing</span>
						<?php else:?>
							<a href="<?=$map_info["route_url"]?>" target="_blank" id="" class=""><?=$map_info["map_miles"]?></a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:130px;" class="field_name">	
						Drive Hrs to Deadline.
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<?php if(empty($next_dest_gp)):?>
							<span style="color:red; font-weight:red">Missing</span>
						<?php else:?>
							<?=convert_hours_to_duration_text($drive_hrs_to_dest)?>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:130px;" class="field_name">	
						Hours to Spare
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<?php if(empty($next_dest_gp)):?>
							<span style="color:red; font-weight:red">Missing</span>
						<?php else:?>
							<?=convert_hours_to_duration_text($hours_to_spare)?>
						<?php endif;?>
					</td>
				</tr>
			</table>
			<table class="load_dispatch_current_profile_table" style="float:left; margin-left:80px; width:330px;">
				<tr>
					<td style="" class="field_name">	
						Truck Codes
					</td>
					<td style="width:90px;" class="ellipsis" title="">
						<?php
							$options = array(
								"Select" => "Select",
								"Good" => "Good",
								"Bad" => "Bad",
							);
						?>
						<?php echo form_dropdown("truck_codes_status",$options,"Select","id='truck_codes_status' class='edit_input' style='font-size:12px;'");?>
					</td>
					<td style="max-width:120px;">
						<input type="file" id="truck_code_guid" name="truck_code_guid" class="file_input" />
					</td>
				</tr>
				<tr>
					<td style="width:120px;" class="field_name">	
						Truck Fuel Level
					</td>
					<td style="" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<?php
							$options = array(
								"Select" => "Select",
								".125" => "1/8",
								".25" => "1/4",
								".375" => "3/8",
								".50" => "1/2",
								".625" => "5/8",
								".75" => "3/4",
								".875" => "7/8",
								"1" => "Full",
							);
						?>
						<?php echo form_dropdown("truck_fuel",$options,"Select","id='truck_fuel' class='edit_input' style='font-size:12px;' onchange='calc_fuel_range()'");?>
					</td>
				</tr>
				<tr>
					<td style="" class="field_name">	
						Fuel Range
					</td>
					<td style="" class="" title="">
						<span id="fuel_range_span">0</span> miles
						<input type="hidden" id="fuel_capacity" value="<?=$truck["fuel_tank_capacity"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="" class="field_name">	
						Miles till Fuel
					</td>
					<td style="" class="" title="">
						<a id="miles_till_fuel_link" style="font-size:16px; font-weight:bold;" href="<?=$map_till_fuel_info["route_url"]?>" target="_blank" id="" class=""><?=$map_till_fuel_info["map_miles"]?></a>
						<input type="hidden" id="miles_till_fuel" value="<?=$map_till_fuel_info["map_miles"]?>"/>
					</td>
				</tr>
			</table>
		</div>
		<div style="clear:both;"></div>
		<div class="heading" style="margin-top:30px;">
			Check Call Script
		</div>
		<hr style="">
		<div style="font-size:12px;">
			<table id="script_table">
				<tr>
					<td>
						<div style="font-size:16px;">
							You are <span style="font-size:16px; font-weight:bold;">SMARTER</span> than the computer.<br> 
							<span style="font-size:16px; font-weight:bold;">READ THE SCRIPT BEFORE YOU CALL</span>.<br>
							If it does <span style="font-size:16px; font-weight:bold;">NOT</span> make sence <span style="font-size:16px; font-weight:bold;">SAY SOMETHING THAT MAKES SENSE</span>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<span class="script">Hey is this <span class="variable"><?=substr($load["client"]["client_nickname"],0,strpos($load["client"]["client_nickname"]," "))?></span>?</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="script">Hey <span class="variable"><?=substr($load["client"]["client_nickname"],0,strpos($load["client"]["client_nickname"]," "))?></span> this is <span class="variable"><?=$this->session->userdata('f_name') ?>.</span> I am doing a check call. How's it going?</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="script">Alright, let me confirm a few things. You're still driving truck <span class="variable"><?=$truck["truck_number"]?></span> and pulling trailer <span class="variable"><?=$trailer["trailer_number"]?></span>. Is that correct?</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="script">Can I get your current truck fuel level?</span>
						<span style="font-size:16px;">(Select the Truck Fuel Level to calculate Fuel Range)</span>
					</td>
				</tr>
				<tr>
					<td>
						<span class="script">I've got here that you are currently in <span class="variable"><?=$geocode["city"]?>, <?=$geocode["state"]?></span> and you are heading to <span class="variable"><?=$next_gp["location"]?></span> for a <span class="variable"><?=$next_gp["gp_type"]?></span>. Is that right?</span>
					</td>
				</tr>
				<?php if(!empty($goalpoints_to_go_till_end)):?>
					<?php if(count($goalpoints_to_go_till_end) > 1):?>
						<tr>
							<td>
								<div class="script">
									OK, it looks like the whole plan is that you've got
									<?php $i = 0;?>
									<?php foreach($goalpoints_to_go_till_end as $gp):?>
										<?php $i++; ?>
										<?php if($i<count($goalpoints_to_go_till_end)):?>
											a 
										<?php else:?>
											and a 
										<?php endif;?>
										<span class="variable"><?=$gp["gp_type"]?></span> in <span class="variable"><?=$gp["location"]?></span>
									<?php endforeach;?>
									. Does that sound right to you?
								</div>
							</td>
						</tr>
					<?php endif;?>
				<?php endif;?>
				<?php if(!empty($next_dest_gp)):?>
					<tr>
						<td>
							<div class="script">
								<?php if(!empty($next_gp["deadline"]) && $hours_till_deadline > 0):?>
									<?php if(empty($next_gp["deadline"])):?>
										You're next deadline is your <span class="variable"><?=$next_dest_gp["gp_type"]?></span> in <span class="variable"><?=$next_dest_gp["location"]?></span> at <span class="variable"><?=date("l H:i",strtotime($next_dest_gp["deadline"]))?></span> which is <span class="variable"><?=hours_to_text_mixed($hours_till_deadline)?></span> from now.</span>. 
									<?php else:?>
										The deadline for that <span class="variable"><?=$next_dest_gp["gp_type"]?></span> is <span class="variable"><?=date("l H:i",strtotime($next_dest_gp["deadline"]))?></span>, which is <span class="variable"><?=hours_to_text_mixed($hours_till_deadline)?></span> from now.</span>
									<?php endif;?>
									You are currently <span class="variable"><?=$map_info["map_miles"]?></span> miles out 
									which means you have about <span class="variable"><?=hours_to_text_mixed($drive_hrs_to_dest)?></span> of driving to get there.
									That means you've got about <span class="variable"><?=hours_to_text_mixed($hours_to_spare)?></span> to spare. 
								<?php else:?>
									The deadline for that <span class="variable"><?=$next_dest_gp["gp_type"]?></span> was <span class="variable"><?=date("l H:i",strtotime($next_dest_gp["deadline"]))?></span>, which was <span class="variable"><?=hours_to_text_mixed($hours_till_deadline)?></span> ago.</span>
									You are currently <span class="variable"><?=$map_info["map_miles"]?></span> miles out 
									which means you have about <span class="variable"><?=hours_to_text_mixed($drive_hrs_to_dest)?></span> of driving to get there.
									That means you're on schedule to be <span class="variable"><?=hours_to_text_mixed($hours_to_spare)?></span> late if you don't stop. 
								<?php endif;?>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<?php if(!empty($next_gp["deadline"]) && $hours_till_deadline > 0):?>
								<span class="script">Are you going to be able to make it on time?</span>
								<span style="font-size:20px;">(If NO, the driver manager needs to be notified immediately)</span>
							<?php else:?>
								<span class="script">I'll make sure your fleet manager is updated.</span>
							<?php endif;?>
						</td>
					</tr>
				<?php endif;?>
				<?php if($hold_report["hold_status"] != "No Hold"):?>
					<tr>
						<td>
							<span class="script">Other than that, I see here that you have some missing paperwork that is putting your account on hold. Let me send you an email <img title="Send Hold Report Email" src="/images/email.png" style="cursor:pointer; position:relative; top:3px; width:20px;" onclick="send_driver_hold_report_email('<?=$load["client_id"]?>')"/> so you can look it over.</span> 
							<span style="font-size:16px;">(Click the email icon to send the driver the hold report)</span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="script">In the mean time, let’s just go over it real quick together.</span>
							<span style="font-size:16px;">(Review the hold report with the driver)</span>
						</td>
					</tr>
				<?php endif;?>
			</table>
			<table style="height:60px;">
				<tr>
					<td  class="field_name" style="width:150px;">
						Driver Status
					</td>
					<td>
						<?php
							$options = array(
								"Select" => "Select",
								"Answered" => "Answered",
								"No Answer" => "No Answer",
								"Sleeping" => "Sleeping - No Call",
								"Waiting" => "Waiting - No Call",
							);
						?>
						<?php echo form_dropdown("driver_answer",$options,"Select","id='driver_answer' class='edit_input' style='font-size:12px; width:150px;' onchange='driver_answer_changed()'");?>
					</td>
				</tr>
				<tr id="audio_upload_row">
					<td  class="field_name">
						Audio Upload
					</td>
					<td>
						<input type="file" id="audio_guid" name="audio_guid" class="file_input" style="width:150px;" />
					</td>
				</tr>
			</table>
		</div>
		<div style="clear:both;"></div>
		<div class="heading" style="margin-top:30px;">
			Hold Report 
		</div>
		<hr style="">
		<div id="" class="">
			<?php if($hold_report["hold_status"] == "No Hold"):?>
				<div style="background-color:green; color:white; margin-top:10px; width:100%; text-align:center; font-size:20px; font-weight:bold;">
					NO HOLD
					<input type="hidden" id="on_hold" name="on_hold" value="No"/>
				</div>
			<?php else:?>
				<div style="background-color:red; color:white; margin-top:10px; width:100%; text-align:center; font-size:20px; font-weight:bold;">
					HOLD
					<input type="hidden" id="on_hold" name="on_hold" value="Yes"/>
				</div>
			<?php endif;?>
			<div style="margin:20px;">
				<table style="float:left;">
					<tr class="heading">
						<td style="width:200px;">
							<a href="https://docs.google.com/document/d/1qWatW0PRT3_UYR3ZWZt5qOFeZLh9D0-Q1o_2c28nN30/edit" target="_blank">How do I fix Missing BOL Pics?</a><br><br>
							Missing BOL Pics (<?=count($hold_report["loads_missing_dc"])?>)
						</td>
					</tr>
					<?php if(!empty($hold_report["loads_missing_dc"])):?>
						<?php foreach($hold_report["loads_missing_dc"] as $load):?>
							<tr>
								<td style="padding-top:5px;">
									<span title=""><?=$load["customer_load_number"]?> (<?=get_final_drop_goalpoint($load["id"])["location"]?>)</span>
								<td>
							</tr>
						<?php endforeach;?>
					<?php endif;?>
				</table>
				<table style="float:left;">
					<tr class="heading">
						<td style="width:200px;">
							<a href="https://docs.google.com/document/d/10K7iJhmOPbK8huZJtCK-PV9vKqcAO9te6biwUJaaQZQ/edit" target="_blank">How do I fix Missing BOL Scans?</a><br><br>
							Missing BOL Scan (<?=count($hold_report["loads_missing_hc"])?>)
						</td>
					</tr>
					<?php if(!empty($hold_report["loads_missing_hc"])):?>
						<?php foreach($hold_report["loads_missing_hc"] as $load):?>
							<tr>
								<td style="padding-top:5px;">
									<span title=""><?=$load["customer_load_number"]?> (<?=get_final_drop_goalpoint($load["id"])["location"]?>)</span>
								<td>
							</tr>
						<?php endforeach;?>
					<?php endif;?>
				</table>
				<table style="float:left;">
					<tr class="heading">
						<td style="width:200px;">
							<a href="https://docs.google.com/document/d/1w-nXJwIAN5AVpFlF_aLTJrt-A31aUYBdLSGjFklyQL0/edit" target="_blank">How do I fix Missing Receipts?</a><br><br>
							Missing Receipts (<?=count($hold_report["client_expenses"])?>)
						</td>
					</tr>
					<?php if(!empty($hold_report["client_expenses"])):?>
						<?php foreach($hold_report["client_expenses"] as $ce):?>
							<tr>
								<td style="padding-top:5px;">
									<span title="<?=date("m/d/y",strtotime($ce["expense_datetime"]))?> <?=$ce["description"]?>">$<?=number_format($ce["expense_amount"],2)?></span>
								<td>
							</tr>
						<?php endforeach;?>
					<?php endif;?>
				</table>
				<div style="clear:both;"></div>
			</div>
		</div>
		<div style="clear:both;"></div>
		<div class="heading" style="margin-top:30px;">
			Load Plan
		</div>
		<hr style="">
		<div>
			<table>
				<tr style="font-weight:bold; height:35px;">
					<td style="min-width:110px; max-width:110px; padding-top:5px;" class="">
						<span class="">Event</span>
					</td>
					<td style="min-width:150px; max-width:150px; padding-right:5px; padding-top:5px;" class="" title="">
						<span class="">Location</span>
					</td>
					<td style="min-width:100px; max-width:100px; padding-top:5px;">
						<span>Expected Time</span>
					</td>
					<td style="min-width:100px; max-width:100px; padding-top:5px;"  class="" title="">
						<span class="">Driver</span>
					</td>
					<td style="min-width:50px; max-width:50px; padding-top:5px;" class="">
						<span class="">Truck</span>
					</td>
					<td style="min-width:50px; max-width:50px; padding-top:5px;" class="">
						<span class="">Trailer</span>
					</td>
					<td style="min-width:340px; max-width:340px; padding-top:5px; padding-right:5px;" class="" title="">
						<span class="">Notes</span>
					</td>
				</tr>
				<?php
					$i = 0;
				?>
				<?php foreach($goalpoints as $goalpoint):?>
					<?php
						$i++;
						$row_style = "";
						if($i%2 == 1)
						{
							$row_style = "background:#E0E0E0;";
						}
						
						//GET DRIVER
						$where = null;
						$where["id"] = $goalpoint["client_id"];
						$client = db_select_client($where);
						
						//GET TRUCK NUMBER
						$where = null;
						$where["id"] = $goalpoint["truck_id"];
						$truck = db_select_truck($where);
						
						//GET TRUCK NUMBER
						$where = null;
						$where["id"] = $goalpoint["trailer_id"];
						$trailer = db_select_trailer($where);
						
						$replace_these = array("Arrival","Departure");
						$replace_with = array("<br>Arrival","<br>Departure");
						$goalpoint_type_text = str_replace($replace_these,$replace_with,$goalpoint["gp_type"]);
						
						$map_link = $goalpoint["dispatch_notes"];
					?>
					<tr style="<?=$row_style?> height:40px;">
						<td style="min-width:110px; max-width:110px; padding-top:5px;" class="">
							<div class="" style="margin-bottom:5px;"><?=$goalpoint_type_text?> <?php if(!empty($goalpoint["duration"])):?>(<?=hours_to_text($goalpoint["duration"]/60)?>)<?php endif;?></div>
							<div><?=$goalpoint["arrival_departure"]?></div>
						</td>
						<td style="min-width:150px; max-width:150px; padding-right:5px; padding-top:5px;" class="" title="<?=$goalpoint["location_name"]?> <?=$goalpoint["location"]?>">
							<div id="gp_location_<?=$goalpoint["id"]?>" class="link" style="padding-right;5px; margin-bottom:5px;"><a target="_blank" style="color:blue;" href="http://maps.google.com/maps?q=<?=$goalpoint["gps"]?>" title="<?=$goalpoint["gps"]?>"><?=$goalpoint["location_name"]?></a></div>
							<div id="gp_location_<?=$goalpoint["id"]?>" class=""><?=$goalpoint["location"]?></div>
						</td>
						<td style="padding-top:5px;">
							<a class="link" target="_blank" style=" color:blue;" href="<?=$goalpoint["dispatch_notes"]?>"><div style="margin-bottom:5px;"><?=date("m/d/y",strtotime($goalpoint["expected_time"]))?></div><div style=""><?=date("H:i",strtotime($goalpoint["expected_time"]))?></div></a>
						</td>
						<td style="min-width:100px; max-width:100px; padding-top:5px;"  class="" title="<?=$client["client_nickname"]?>">
							<span class=""><?=$client["client_nickname"]?></span>
						</td>
						<td style="min-width:50px; max-width:50px; padding-top:5px;" class="">
							<span class=""><?=$truck["truck_number"]?></span>
						</td>
						<td style="min-width:50px; max-width:50px; padding-top:5px;" class="">
							<span class=""><?=$trailer["trailer_number"]?></span>
						</td>
						<td style="padding-top:5px; padding-right:5px;" class="" title="<?=$goalpoint["dm_notes"]?>">
							<span class=""><?=$goalpoint["dm_notes"]?></span>
						</td>
					</tr>
				<?php endforeach;?>
			</table>
			<div style="width:200px; margin:auto; text-align:center; margin-top:30px;">
				<a class="link" target="_blank" style="margin-top:30px;font-size:16px; color:blue;" href="<?=$map_link?>">View Map</a>
			</div>
		</div>
		<div style="clear:both;"></div>
	</form>
</div>