<?php
	$row_id = $load["id"];
	
	//GET TRUCK
	$where = null;
	$where["id"] = $load["load_truck_id"];
	$truck = db_select_truck($where);
	
	//GET TRAILER
	$where = null;
	$where["id"] = $load["load_trailer_id"];
	$trailer = db_select_trailer($where);
	
	$geocode = null;
	if(!empty($current_geopoint_goalpoint["gps"]))
	{
		$geocode = reverse_geocode($current_geopoint_goalpoint["gps"]);
	}
	
	
?>
<script>
	$('#dispatch_current_date').datepicker({ showAnim: 'blind' });
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
		height:20px;
	}
	
	.load_dispatch_update_table tr
	{
		height:30px;
	}
	
	.details_box hr
	{
		width:910px;
	}
	
</style>
<div style="min-height:95px;" class="details_box">
	<form id="load_dispatch_update_form"  enctype="multipart/form-data">
		<input type="hidden" id="dispatch_update_load_id" name="dispatch_update_load_id" value="<?=$load["id"]?>"/>
		<input type="hidden" id="current_geopoint_goalpoint_id" name="current_geopoint_goalpoint_id" value="<?=$current_geopoint_goalpoint["id"]?>"/>
		<div class="heading">
			Current Profile
		</div>
		<hr style="">
		<div style="font-size:12px;">
			<table class="load_dispatch_current_profile_table" style="float:left; width:250px;">
				<tr>
					<td style="width:100px;" class="field_name">	
						Truck
					</td>
					<td>
						<span class=""><?=$truck["truck_number"]?></span>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Trailer
					</td>
					<td>
						<span class=""><?=$trailer["trailer_number"]?></span>
					</td>
				</tr>
			</table>
			<table class="load_dispatch_current_profile_table" style="float:left; margin-left:100px; width:250px;">
				<tr>
					<td style="width:100px;" class="field_name">	
						Driver
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<span class=""><?=$load["client"]["client_nickname"]?></span>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Carrier
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<span class=""><?=$load["billed_under_carrier"]["company_name"]?></span>
					</td>
				</tr>
			</table>
			<table class="load_dispatch_current_profile_table" style="float:left; margin-left:80px; width:200px;">
				<tr>
					<td style="width:100px;" class="field_name">	
						Fleet Manager
					</td>
					<td style="max-width:100px;" class="ellipsis" title="<?=$load["broker"]["customer_name"]?>">
						<?=$load["fleet_manager"]["f_name"]?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Driver Manager
					</td>
					<td style="max-width:100px;" class="ellipsis" title="<?=$load["contact_info"]?>">
						<?=$load["driver_manager"]["f_name"]?>
					</td>
				</tr>
			</table>
		</div>
		<div style="clear:both;"></div>
		<div class="heading" style="margin-top:30px;">
			Current Location
		</div>
		<hr style="">
		<div style="font-size:12px;">
			<input type="hidden" id="" id="load_id" name="load_id" value="<?=$row_id?>"/>
			<table class="" style="">
				<tr>
					<td style="width:100px" class="field_name">	
						GPS
					</td>
					<td style="width:145px">
						<input type="text" id="dispatch_gps" name="dispatch_gps" style="width:100px;" value="<?=$current_geopoint_goalpoint["gps"]?>" onchange="auto_fill_dispatch_update_location()"/>
					</td>
					<td style="width:130px;" class="field_name">	
						Current Location
					</td>
					<td style="width:225px;">
						<span id="dispatch_current_location" class=""><?=$geocode["city"]?>, <?=$geocode["state"]?></span>
					</td>
					<td style="width:110px"class="field_name">	
						Current Time
					</td>
					<td style="">
						<input type="text" id="dispatch_current_date" name="dispatch_current_datetime" style="width:80px;" value="<?=date("m/d/y",strtotime($current_geopoint_goalpoint["expected_time"]))?>" placeholder="Date" />
						<input type="text" id="dispatch_current_time" name="dispatch_current_datetime" style="width:80px; margin-left:5px;" value="<?=date("H:i",strtotime($current_geopoint_goalpoint["expected_time"]))?>" placeholder="Time" />
					</td>
				</tr>
			</table>
		</div>
		<div style="clear:both;"></div>
		<div class="heading" style="margin-top:30px;">
			Dispatch Update 
			<a target="_blank" style="float:right; color:blue; margin-right:30px" href="https://docs.google.com/document/d/1EZD2pEvTkVwsMgWtp4Xt-Pwc-QICmFm6-8PTaJ6JyzY/edit?usp=sharing" class="link">Trailer Help?</a>
			<a target="_blank" style="float:right; color:blue; margin-right:30px" href="https://docs.google.com/document/d/1mCB3ZY8h4if1nSh3VT30kOOrjfU4Hw6H4_XMIQ8wbes/edit?usp=sharing" class="link">Truck Help?</a>
			<a target="_blank" style="float:right; color:blue; margin-right:30px" href="https://docs.google.com/document/d/1fc7PDWuz3hDjzErQbJKhrkVekJiHHyvX3IPNwVMTvV0/edit?usp=sharing" class="link">HOS Help?</a>
			<span class="" style="color:black; text-decoration:none; float:right; margin-right:30px;">BigRoad Username: <?=$load["client"]["bigroad_username"]?> Password: <?=$load["client"]["bigroad_password"]?></span>
		</div>
		<hr style="">
		<div style="font-size:12px;">
			<input type="hidden" id="" id="load_id" name="load_id" value="<?=$row_id?>"/>
			<table class="load_dispatch_update_table" style="float:left;">
				<tr>
					<td style="width:100px;" class="field_name">	
						HOS:Break
					</td>
					<td>
						<input type="text" id="hos_break_hour" name="hos_break_hour" style="width:45px;" value="" placeholder="Hour"/> : 
						<input type="text" id="hos_break_minute" name="hos_break_minute" style="width:45px;" value="" placeholder="Minute"/>
					</td>
				</tr>
				<tr>
					<td style="" class="field_name">	
						HOS:Drive
					</td>
					<td>
						<input type="text" id="hos_drive_hour" name="hos_drive_hour" style="width:45px;" value="" placeholder="Hour"/> : 
						<input type="text" id="hos_drive_minute" name="hos_drive_minute" style="width:45px;" value="" placeholder="Minute"/>
					</td>
				</tr>
				<tr>
					<td style="" class="field_name">	
						HOS:Shift
					</td>
					<td style="width:100px;">
						<input type="text" id="hos_shift_hour" name="hos_shift_hour" style="width:45px;" value="" placeholder="Hour"/> : 
						<input type="text" id="hos_shift_minute" name="hos_shift_minute" style="width:45px;" value="" placeholder="Minute"/>
					</td>
				</tr>
				<tr>
					<td style="" class="field_name">	
						HOS:Cycle
					</td>
					<td>
						<input type="text" id="hos_cycle_hour" name="hos_cycle_hour" style="width:45px;" value="" placeholder="Hour"/> : 
						<input type="text" id="hos_cycle_minute" name="hos_cycle_minute" style="width:45px;" value="" placeholder="Minute"/>
					</td>
				</tr>
				<tr>
					<td style="" class="field_name">	
						HOS:Screenshot
					</td>
					<td style="">
						<input type="file" id="hos_remaining_guid" name="hos_remaining_guid" class="file_input" />
					</td>
				</tr>
			</table>
			<table class="load_dispatch_update_table" style="float:left; margin-left:25px; width:330px;">
				<tr>
					<td style="width:100px;" class="field_name">	
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
						<?php echo form_dropdown("truck_fuel",$options,"Select","id='truck_fuel' class='edit_input' style='font-size:12px;'");?>
					</td>
				</tr>
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
						<input type="file" id="truck_codes_guid" name="truck_codes_guid" class="file_input" />
					</td>
				</tr>
			</table>
			<table class="load_dispatch_update_table" style="float:left; margin-left:25px; width:330px;">
				<tr>
					<td style="width:110px;" class="field_name">	
						Trailer Fuel Level
					</td>
					<td style="width:90px;" class="ellipsis" title="<?=$load["contact_info"]?>">
						<input type="text" class="edit_input" id="trailer_fuel" name="trailer_fuel" placeholder="Percent 0-100"/>
					</td>
					<td style="max-width:120px;">
						<input type="file" id="trailer_fuel_guid" name="trailer_fuel_guid" class="file_input" />
					</td>
				</tr>
				<tr>
					<td style="" class="field_name">	
						Trailer Codes
					</td>
					<td style="" class="ellipsis" title="<?=$load["contact_info"]?>">
						<?php
							$options = array(
								"Select" => "Select",
								"Good" => "Good",
								"Bad" => "Bad",
							);
						?>
						<?php echo form_dropdown("trailer_codes_status",$options,"Select","id='trailer_codes_status' class='edit_input' style='font-size:12px;'");?>
					</td>
					<td style="">
						<input type="file" id="trailer_codes_guid" name="trailer_codes_guid" class="file_input" />
					</td>
				</tr>
				<tr>
					<td style="" class="field_name">	
						Reefer Temp
					</td>
					<td style="" class="ellipsis" title="<?=$load["contact_info"]?>">
						<input type="text" class="edit_input" id="reefer_temp" name="reefer_temp" />
					</td>
					<td style="">
						<input type="file" id="reefer_temp_guid" name="reefer_temp_guid" class="file_input" />
					</td>
				</tr>
			</table>
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
					<tr style="<?=$row_style?> height:35px;">
						<td style="min-width:110px; max-width:110px; padding-top:5px;" class="">
							<span class=""><?=$goalpoint_type_text?> <?php if(!empty($goalpoint["duration"])):?>(<?=hours_to_text($goalpoint["duration"]/60)?>)<?php endif;?><br><?=$goalpoint["arrival_departure"]?></span>
						</td>
						<td style="min-width:150px; max-width:150px; padding-right:5px; padding-top:5px;" class="" title="<?=$goalpoint["location_name"]?> <?=$goalpoint["location"]?>">
							<div id="gp_location_<?=$goalpoint["id"]?>" class="link" style="padding-right;5px;"><a target="_blank" style="color:blue;" href="http://maps.google.com/maps?q=<?=$goalpoint["gps"]?>" title="<?=$goalpoint["gps"]?>"><?=$goalpoint["location_name"]?></a></div>
							<div id="gp_location_<?=$goalpoint["id"]?>" class=""><?=$goalpoint["location"]?></div>
						</td>
						<td style="padding-top:5px;">
							<a class="link" target="_blank" style=" color:blue;" href="<?=$goalpoint["dispatch_notes"]?>"><?=str_replace(' ','<br>',date("m/d/y H:i",strtotime($goalpoint["expected_time"])))?></a>
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