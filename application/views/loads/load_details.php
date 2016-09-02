<?php
	date_default_timezone_set('America/Denver');
	
	$row_id = $load["id"];
	
	//GET TRUCK
	$where = null;
	$where["id"] = $load["load_truck_id"];
	$truck = db_select_truck($where);
	
	//GET TRAILER
	$where = null;
	$where["id"] = $load["load_trailer_id"];
	$trailer = db_select_trailer($where);
	
	if($load["expected_miles"] != 0)
	{
		$rate_per_mile = $load["expected_revenue"]/$load["expected_miles"];
	}
	else
	{
		$rate_per_mile = 0;
	}
	
	if(empty($load["ready_for_dispatch_datetime"]))
	{
		$ready_for_dispatch_text = "Not Ready";
	}
	else
	{
		$ready_for_dispatch_text = date("m/d/y H:i",strtotime($load["ready_for_dispatch_datetime"]));
	}
	
	if(empty($load["initial_dispatch_datetime"]))
	{
		$initial_dispatch_text = "";
	}
	else
	{
		$initial_dispatch_text = date("m/d/y H:i",strtotime($load["initial_dispatch_datetime"]));
	}
	
	//GET MOST RECENT ZONAR GEOPOINT
	$current_location = null;
	$current_geopoint = null;
	if(!empty($load["load_truck_id"]))
	{
		$current_geopoint = get_most_recent_geopoint($load["load_truck_id"]);
	}
	
	if(!empty($current_geopoint))
	{
		$current_location_geocode = reverse_geocode($current_geopoint["latitude"].",".$current_geopoint["longitude"]);
		$current_location = $current_location_geocode["city"].", ".$current_location_geocode["state"];
	}
	
	
	//GET MOST RECENT TRAILER GEOPOINT
	$current_trailer_geopoint = null;
	if(!empty($load["load_trailer_id"]))
	{
		$current_trailer_geopoint = get_most_recent_trailer_geopoint($load["load_trailer_id"]);
		if(strtotime($current_trailer_geopoint["datetime_occurred"]) < (time() - 60*60))
		{
			$current_trailer_geopoint = null;
		}
	}
	
?>
<script>
</script>
<style>
	.edit_<?=$row_id?>
	{
		display:none;
	}
	
	.edit_input
	{
		width:150px;
		position:relative;
		
	}
	
	.edit_input[type="text"]
	{
		padding-left:5px;
	}
	
	.field_name
	{
		font-weight:bold;
	}
	
	.load_details_table tr
	{
		height:25px;
	}
	
	.details_box hr
	{
		width:910px;
	}
	
</style>
<div style="min-height:95px;" class="details_box">
	<div style="width:20px; height:45px; float:right;">
		<img id="refresh_load_details_icon_<?=$row_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:13px; position:relative; left:0px;" src="/images/refresh.png" title="Refresh" onclick="open_row_details('<?=$load["id"]?>')"/>
		<img id="edit_icon" class="details_<?=$row_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; right:1px;" src="/images/edit.png" title="Edit" onclick="edit_row_details('<?=$load["id"]?>')"/>
		<img id="save_icon_<?=$row_id?>" class="edit_<?=$row_id?>" style="display:none; margin-bottom:13px; margin-right:15px; cursor:pointer; height:14px; position:relative; left:0px;" src="/images/save.png" title="Save" onclick="save_load_edit('<?=$load["id"]?>');"/>
		<?php if(empty($load["load_truck_id"]) || empty($load["client_id"])):?>
			<img id="dispatch_icon" class="" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; right:2px;" src="/images/grey_headset_icon.png" title="Dispatch" onclick="alert('The load must have a Truck and Driver before a check call can be performed!')"/>
		<?php else:?>
			<img id="dispatch_icon" class="" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; right:2px;" src="/images/grey_headset_icon.png" title="Dispatch" onclick="load_status_changed('<?=$load["id"]?>','open_dispatch_dialog')"/>
		<?php endif;?>
		<img id="attachment_icon" class="" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:20px; position:relative; left:1px;" src="/images/paper_clip2.png" title="Attachment" onclick="open_file_upload('<?=$load["id"]?>')"/>
		<img id="cancel_icon_<?=$row_id?>" class="" style="margin-bottom:13px; margin-right:15px; cursor:pointer; height:14px; position:relative; right:1px;" src="/images/grey_cancel_icon.png" title="Cancel" onclick="open_cancel_load_dialog('<?=$load["id"]?>');"/>
	</div>
	<div class="heading">
		Load Details
	</div>
	<hr style="">
	<div style="font-size:12px;">
		<form id="load_details_form_<?=$row_id?>">
			<input type="hidden" id="" id="load_id" name="load_id" value="<?=$row_id?>"/>
			<table class="load_details_table" style="float:left; width:250px;">
				<tr>
					<td style="width:100px;" class="field_name">	
						Load Number
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["customer_load_number"]?>">
						<span class="details_<?=$row_id?>"><?=$load["customer_load_number"]?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_load_number_<?=$row_id?>" name="edit_load_number" value="<?=$load["customer_load_number"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Carrier
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["billed_under_carrier"]["company_name"]?>">
						<span class="details_<?=$row_id?>"><?=$load["billed_under_carrier"]["company_name"]?></span>
						<?php echo form_dropdown('edit_billed_under',$billed_under_options,$load['billed_under'],"id='edit_billed_under_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Fleet Manager
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["fleet_manager"]["f_name"]." ".$load["fleet_manager"]["l_name"]?>">
						<span class="details_<?=$row_id?>"><?=$load["fleet_manager"]["f_name"]." ".$load["fleet_manager"]["l_name"]?></span>
						<?php echo form_dropdown('edit_fleet_manager',$fleet_managers_dropdown_options,$load['fleet_manager_id'],"id='edit_fleet_manager_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Driver Manager
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$load["driver_manager"]["f_name"]." ".$load["driver_manager"]["l_name"]?></span>
						<?php echo form_dropdown('edit_driver_manager',$dm_filter_dropdown_options,$load['dm_id'],"id='edit_driver_manager_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Nat'l Fuel Avg
					</td>
					<td>
						<span class="details_<?=$row_id?>">$<?=number_format($load["natl_fuel_avg"],2)?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_natl_avg_<?=$row_id?>" name="edit_natl_avg" value="<?=$load["natl_fuel_avg"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Expected Rate
					</td>
					<td>
						<span class="details_<?=$row_id?>">$<?=number_format($load["expected_revenue"])?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_expected_rate_<?=$row_id?>" name="edit_expected_rate" value="<?=$load["expected_revenue"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Ready for Dispatch
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$ready_for_dispatch_text?></span>
						<?php if(empty($load["ready_for_dispatch_datetime"])):?>
							<?php
								$options = array(
									"Not Ready"	=>	"Not Ready",
									"Ready"	=>	"Ready",
								);
							?>
							<?php echo form_dropdown('edit_ready_for_dispatch',$options,$ready_for_dispatch_text,"id='edit_ready_for_dispatch_$row_id' class='edit_$row_id edit_input' style=''");?>
						<?php else:?>
							<span class="edit_<?=$row_id?>"><?=$ready_for_dispatch_text?></span>
							<input type="hidden" id="edit_ready_for_dispatch_<?=$row_id?>" name="edit_ready_for_dispatch" />
						<?php endif;?>
					
					</td>
				</tr>
			</table>
			<table class="load_details_table" style="float:left; margin-left:80px; width:250px;">
				<tr>
					<td style="width:100px;" class="field_name">	
						Driver
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<span class="details_<?=$row_id?>"><?=$load["client"]["client_nickname"]?></span>
						<?php echo form_dropdown('edit_client',$clients_dropdown_options,$load['client_id'],"id='edit_client_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Truck
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$truck["truck_number"]?></span>
						<?php echo form_dropdown('edit_truck',$truck_dropdown_options,$load['load_truck_id'],"id='edit_truck_$row_id' class='edit_$row_id edit_input' onchange='check_if_truck_is_assigned($row_id)' style=''");?>
						<input type="hidden" id="truck_is_already_assigned" name="truck_is_already_assigned" value="no" />
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Trailer
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$trailer["trailer_number"]?></span>
						<?php echo form_dropdown('edit_trailer',$trailer_dropdown_options,$load['load_trailer_id'],"id='edit_trailer_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Freight Type
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$load["is_reefer"]?></span>
						<?php $options = array(
							'Reefer' => 'Reefer',
							'Dry' => 'Dry',
							); 
						?>
						<?php echo form_dropdown('edit_is_reefer',$options,$load['is_reefer'],"id='edit_is_reefer_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Reefer Temp
					</td>
					<td>
						<div class="details_<?=$row_id?>">
							<div style="width:60px; text-align:center; float:left;">
								<?=$load["reefer_low_set"]?>&nbsp;
							</div>
							<div style="margin-left:7px; margin-right:7px; float:left;">
								to
							</div>
							<div style="width:60px; text-align:center; float:left;">
								<?=$load["reefer_high_set"]?>&nbsp;
							</div>
						</div>
						<div class="edit_<?=$row_id?>">
							<div style="width:60px; text-align:center; float:left;">
								<input type="text" class="edit_input" style="width:60px; text-align:center; " id="edit_reefer_low_set_<?=$row_id?>" name="edit_reefer_low_set" value="<?=$load["reefer_low_set"]?>"/>
							</div>
							<div style="margin-left:10px; margin-right:10px; float:left;">
								to
							</div>
							<div style="width:60px; text-align:center; float:left;">
								<input type="text" class="edit_input" style="width:60px; text-align:center;" id="edit_reefer_high_set_<?=$row_id?>" name="edit_reefer_high_set" value="<?=$load["reefer_high_set"]?>"/>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Expected Miles
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=number_format($load["expected_miles"])?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_expected_miles_<?=$row_id?>" name="edit_expected_miles" value="<?=$load["expected_miles"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Initial Dispatch
					</td>
					<td>
						<?php if(empty($load["signed_load_plan_guid"])):?>
							<span class=""><?=$initial_dispatch_text?></span>
						<?php else:?>
							<a target="_blank" href='<?=base_url("/index.php/documents/download_file")."/".$load["signed_load_plan_guid"]?>'>Accepted Load Plan</a>
						<?php endif;?>
					</td>
				</tr>
			</table>
			<table class="load_details_table" style="float:left; margin-left:80px; width:250px;">
				<tr>
					<td style="width:100px;" class="field_name">	
						Broker
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["broker"]["customer_name"]?>">
						<span class="details_<?=$row_id?>"><?=$load["broker"]["customer_name"]?></span>
						<?php echo form_dropdown('edit_broker',$broker_dropdown_options,$load['broker_id'],"id='edit_broker_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Contact Info
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["contact_info"]?>">
						<span class="details_<?=$row_id?>"><?=$load["contact_info"]?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_contact_info_<?=$row_id?>" name="edit_contact_info" value="<?=$load["contact_info"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Billing Method
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$load["billing_method"]?></span>
						<?php $options = array(
							'Factor' => 'Factor',
							'Direct Bill' => 'Direct Bill',
							); 
						?>
						<?php echo form_dropdown('edit_billing_method',$options,$load['billing_method'],"id='edit_billing_method_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Time of Booking
					</td>
					<td>
						<span class=""><?=date("n\/d\/y H:i",strtotime($load['booking_datetime']))?></span>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Load Type
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$load["billing_method"]?></span>
						<?php $options = array(
							'Full Load' => 'Full Load',
							'Partial' => 'Partial',
							'Power Only' => 'Power Only',
							); 
						?>
						<?php echo form_dropdown('edit_load_type',$options,$load['billing_method'],"id='edit_load_type_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Rate/Mile
					</td>
					<td>
						<span class="" >$<?=number_format($rate_per_mile,2)?></span>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Pre-drop BOL 
					</td>
					<td>
						<?php if(!empty($load["unsigned_bol_guid"])):?>
							<a target="_blank" href='<?=base_url("/index.php/documents/download_file")."/".$load["unsigned_bol_guid"]?>'>Unsigned BOL</a>
						<?php else:?>
							<a target="_blank" style="color:blue;" href="https://docs.google.com/document/d/18QEu4fJnsTjB7aKhqOdvVYkXYaNSoS7u4H72WvzWmDY/edit?usp=sharing" class="link">?</a>
						<?php endif;?>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div style="clear:both;"></div>
	<div class="heading" style="margin-top:20px;">
		Check Calls
	</div>
	<hr style="">
	<div id="load_plan_div_<?=$row_id?>" style="" class="">
		<table style="margin-left:30px; margin-top:5px; line-height:30px; font-size:10px;">
			<tr style="font-weight:bold; line-height:10px;">
				<td style="width:90px;">
					Time
				</td>
				<td style="width:50px; padding-right:5px;">
					Truck
				</td>
				<td style="width:60px; padding-right:5px;">
					Trailer
				</td>
				<td style="width:40px;">
					Truck Fuel
				</td>
				<td style="width:90px;">
					Truck Codes
				</td>
				<td style="width:195px; padding-right:5px;">
					Location
				</td>
				<td style="width:95px;">
					Driver
				</td>
				<td style="width:60px;">
					Answered
				</td>
				<td style="width:40px;">
					Hold?
				</td>
				<td style="width:70px;">
					Dispatcher
				</td>
				<td style="width:60px; text-align:right; padding-right:10px;">
					Audio
				</td>
			</tr>
		</table>
	</div>
	<div id="load_updates_div_<?=$row_id?>" style="max-height:200px;" class="scrollable_div">
		<table style="margin-left:30px; margin-bottom:10px;">
			<?php if(!empty($load_check_calls)):?>
				<?php
				 $i = 0;
				?>
				<?php foreach($load_check_calls as $cc):?>
					<?php
						$i++;
						$row_style = "";
						if($i%2 == 1)
						{
							$row_style = "background:#E0E0E0;";
						}
						
						//GET TRUCK
						$where = null;
						$where["id"] = $cc["truck_id"];
						$cc_truck = db_select_truck($where);
						
						//GET TRAILER
						$where = null;
						$where["id"] = $cc["trailer_id"];
						$cc_trailer = db_select_trailer($where);
						
						//GET DRIVER
						$where = null;
						$where["id"] = $cc["driver_id"];
						$cc_client = db_select_client($where);
						
						//GET USER RECORDER
						$where = null;
						$where["id"] = $cc["user_id"];
						$recorder_user = db_select_user($where);
						
						//GET INITIALS
						$initials = substr($recorder_user["person"]["f_name"],0,1).substr($recorder_user["person"]["l_name"],0,1);
					?>
					<tr style="font-size:11px; height:30px; line-height:30px; <?=$row_style?>">
						<td style="width:90px; " title="Recorded <?=date("m/d/y H:i",strtotime($cc["recorded_datetime"]))?>">
							<?=date("m/d/y H:i",strtotime($cc["recorded_datetime"]))?>
						</td>
						<td style="width:50px; padding-right:5px;">
							<?=$cc_truck["truck_number"]?>
						</td>
						<td style="width:60px;  padding-right:5px;">
							<?=$cc_trailer["trailer_number"]?>
						</td>
						<td style="width:30px;text-align:right; padding-right:10px;">
							<?php if(!empty($cc["truck_fuel_level"])):?>
								<?=number_format($cc["truck_fuel_level"]*100)?>%
							<?php endif;?>
						</td>
						<td style="width:90px;">
							<a href="<?=base_url("/index.php/documents/download_file")."/".$cc["truck_code_guid"]?>" target="_blank"><?=$cc["truck_code_status"]?></a>
						</td>
						<td style="width:195px; padding-right:5px;">
							<a target="_blank" href='http://maps.google.com/maps?q=<?=$cc["gps"]?>'><?=$cc["location"]?></a>
						</td>
						<td class="ellipsis" style="max-width:95px; min-width:95px;">
							<?=$cc_client["client_nickname"]?>
						</td>
						<td style="width:60px;">
							<?php if($cc["driver_answered"] == 'No Answer'):?>
								<a style="color:red" href="<?=base_url("/index.php/documents/download_file")."/".$cc["audio_guid"]?>" target="_blank"><span style="font-weight:bold;">NO</span></a>
							<?php else:?>
								<?php if(!empty($cc["audio_guid"])):?>
									<a style="color:green" href="<?=base_url("/index.php/documents/download_file")."/".$cc["audio_guid"]?>" target="_blank"><span style="font-weight:bold;"><?=$cc["driver_answered"]?></span></a>
								<?php else:?>
									<span style="font-weight:bold;"><?=$cc["driver_answered"]?></span>
								<?php endif;?>
							<?php endif;?>
						</td>
						<td style="width:40px;">
							<?php if($cc["on_hold"] == 'Yes'):?>
								<span style="color:red; font-weight:bold;">YES</span>
							<?php elseif($cc["on_hold"] == 'No'):?>
								<span style="color:green; font-weight:bold;">No</span>
							<?php endif;?>
						</td>
						<td style="width:70px;">
							<span title="<?=$recorder_user["person"]["full_name"]?>"><?=$recorder_user["person"]["f_name"]?></span>
						</td>
						<td style="width:60px;text-align:right; padding-right:10px;">
							<a href="<?=base_url("/index.php/documents/download_file")."/".$cc["audio_guid"]?>" target="_blank">Listen</a>
						</td>
					</tr>
				<?php endforeach;?>
			<?php endif;?>
		</table>
	</div>
	<div style="clear:both;"></div>
	<div class="heading" style="margin-top:20px;">
		Load Updates
	</div>
	<hr style="">
	<div id="load_plan_div_<?=$row_id?>" style="" class="">
		<table style="margin-left:30px; margin-top:5px; line-height:30px; font-size:10px;">
			<tr style="font-weight:bold; line-height:10px;">
				<td style="width:90px;">
					Time
				</td>
				<td style="width:50px; padding-right:5px;">
					Truck
				</td>
				<td style="width:60px; padding-right:5px;">
					Trailer
				</td>
				<td style="width:40px;">
					Trailer<br>Fuel
				</td>
				<td style="width:40px;">
					Reefer<br>Set
				</td>
				<td style="width:40px;">
					Reefer<br>Temp
				</td>
				<td style="width:120px; padding-right:5px;">
					Location
				</td>
				<td style="width:40px;">
					OOR?
				</td>
				<td style="width:40px;">
					<!-- Miles Driven !-->
				</td>
				<td style="width:90px;">
					Driver
				</td>
				<td style="width:40px; text-align:right;">
					Break
				</td>
				<td style="width:40px; text-align:right;">
					Drive
				</td>
				<td style="width:40px; text-align:right;">
					Shift
				</td>
				<td style="width:40px; text-align:right; padding-right:10px;">
					Cycle
				</td>
			</tr>
		</table>
	</div>
	<div id="load_updates_div_<?=$row_id?>" style="" class="">
		<table style="margin-left:30px; margin-bottom:10px;">
			<?php if(!empty($dispatch_updates)):?>
				<?php
				 $i = 0;
				?>
				<?php foreach($dispatch_updates as $du):?>
					<?php
						$i++;
						$row_style = "";
						if($i%2 == 1)
						{
							$row_style = "background:#E0E0E0;";
						}
						
						$du_id = $du["id"];
						
						//GET TRUCK
						$where = null;
						$where["id"] = $du["truck_id"];
						$du_truck = db_select_truck($where);
						
						//GET TRAILER
						$where = null;
						$where["id"] = $du["trailer_id"];
						$du_trailer = db_select_trailer($where);
						
						//GET DRIVER
						$where = null;
						$where["id"] = $du["client_id"];
						$du_client = db_select_client($where);
						
						//GET USER RECORDER
						$where = null;
						$where["id"] = $du["recorder_id"];
						$recorder_user = db_select_user($where);
						
						//GET INITIALS
						$initials = substr($recorder_user["person"]["f_name"],0,1).substr($recorder_user["person"]["l_name"],0,1);
					?>
					<tr style="font-size:11px; height:30px; line-height:30px; <?=$row_style?>">
						<td style="width:90px; " title="Recorded <?=date("m/d/y H:i",strtotime($du["recorded_time"]))?>">
							<?=date("m/d/y H:i",strtotime($du["update_datetime"]))?>
						</td>
						<td style="width:50px; padding-right:5px;">
							<?=$du_truck["truck_number"]?>
						</td>
						<td style="width:60px;  padding-right:5px;">
							<?=$du_trailer["trailer_number"]?>
						</td>
						<td style="width:20px;text-align:right; padding-right:20px;">
							<?=number_format($du["trailer_fuel"])?>%
						</td>
						<td style="width:20px;text-align:right; padding-right:20px;">
							<?=number_format($du["reefer_set"])?>
						</td>
						<td style="width:20px;text-align:right; padding-right:20px;">
							<?=number_format($du["reefer_temp"])?>
						</td>
						<td style="width:120px; padding-right:5px;">
							<a target="_blank" href='http://maps.google.com/maps?q=<?=$du["gps"]?>'><?=$du["location"]?></a>
						</td>
						<td style="width:40px;">
							<?php if($du["is_oor"] == 'Yes'):?>
								<span style="color:red; font-weight:bold;">YES</span>
							<?php elseif($du["is_oor"] == 'No'):?>
								<span style="">No</span>
							<?php endif;?>
						</td>
						<td style="width:40px;">
							<!-- Miles Driven !-->
						</td>
						<td class="ellipsis" style="max-width:90px; min-width:90px;">
							<?=$du_client["client_nickname"]?>
						</td>
						<td style="width:40px;text-align:right;">
							<?php if(!empty($du["hos_break"])):?>
								<?=convert_hours_to_duration_text($du["hos_break"])?>
							<?php endif;?>
						</td>
						<td style="width:40px;text-align:right;">
							<?php if(!empty($du["hos_drive"])):?>
								<?=convert_hours_to_duration_text($du["hos_drive"])?>
							<?php endif;?>
						</td>
						<td style="width:40px;text-align:right;">
							<?php if(!empty($du["hos_shift"])):?>
								<?=convert_hours_to_duration_text($du["hos_shift"])?>
							<?php endif;?>
						</td>
						<td style="width:40px;text-align:right; padding-right:10px;">
							<?php if(!empty($du["hos_cycle"])):?>
								<?=convert_hours_to_duration_text($du["hos_cycle"])?>
							<?php endif;?>
						</td>
						<td style="width:30px; text-align:right;">
							<span title="<?=$recorder_user["person"]["full_name"]?>"><?=$initials?></span>
						</td>
						<td style="width:45px;">
							<img title="Email Load Plan" src="/images/email.png" style="cursor:pointer; position:relative; top:8px; left:20px; width:20px;" onclick="open_load_plan_email_dialog('<?=$du["id"]?>')"/>
						</td>
					</tr>
				<?php endforeach;?>
			<?php endif;?>
			<?php if(!empty($current_geopoint)):?>
				<tr style="font-size:11px; height:30px; line-height:30px; border-top:1px solid grey;">
					<td style="width:90px; " >
						<?=date("m/d/y H:i",strtotime($current_geopoint["datetime"]))?>
					</td>
					<td style="width:50px; padding-right:5px;">
						<?=$truck["truck_number"]?>
					</td>
					<td style="width:60px; padding-right:5px;">
						<?=$trailer["trailer_number"]?>
					</td>
					<td style="width:20px;text-align:right; padding-right:20px;">
						<?php if(!empty($current_trailer_geopoint["fuel_level"])):?>
							<?=number_format($current_trailer_geopoint["fuel_level"])?>%
						<?php endif;?>
					</td>
					<td style="width:20px;text-align:right; padding-right:20px;">
						<?php if(!empty($current_trailer_geopoint["set_temperature"])):?>
							<?=number_format($current_trailer_geopoint["set_temperature"])?>
						<?php endif;?>
					</td>
					<td style="width:20px;text-align:right; padding-right:20px;">
						<?php if(!empty($current_trailer_geopoint["return_temperature"])):?>
							<?=number_format($current_trailer_geopoint["return_temperature"])?>
						<?php endif;?>
					</td>
					<td style="width:120px;  padding-right:5px;">
						<a target="_blank" href='http://maps.google.com/maps?q=<?=$current_geopoint["latitude"].",".$current_geopoint["longitude"]?>'><?=$current_location?></a>
					</td>
					<td style="width:40px;">
						<?php if($current_geopoint["is_oor"] == 'Yes'):?>
							<span style="color:red; font-weight:bold;">YES</span>
						<?php elseif($current_geopoint["is_oor"] == 'No'):?>
							<span style="">No</span>
						<?php endif;?>
					</td>
					<td style="width:40px;">
						<!-- Miles Driven !-->
					</td>
					<td class="ellipsis" style="max-width:90px; min-width:90px;">
						<?=$load["client"]["client_nickname"]?>
					</td>
					<td style="width:40px;text-align:right;">
						
					</td>
					<td style="width:40px;text-align:right;">
						
					</td>
					<td style="width:40px;text-align:right;">
						
					</td>
					<td style="width:40px;text-align:right;">
						
					</td>
					<td style="width:30px; text-align:right;">
						<!-- initials !-->
					</td>
					<td style="width:45px;">
						<img title="Add to Log" id="save_load_update_<?=$row_id?>" src="/images/add_circle.png" style="height:20px; cursor:pointer; position:relative; top:8px; left:20px;" onclick="save_dispatch_update('<?=$row_id?>')"/>
					</td>
				</tr>
			<?php endif;?>
		</table>
		<form id="load_update_form_<?=$row_id?>">
			<input type="hidden" id="load_id" name="load_id" value="<?=$load["id"]?>">
			<input type="hidden" id="current_geopoint_id" name="current_geopoint_id" value="<?=$current_geopoint["id"]?>">
			<input type="hidden" id="current_trailer_geopoint_id" name="current_trailer_geopoint_id" value="<?=$current_trailer_geopoint["id"]?>">
		</form>
	</div>
	<div style="clear:both;"></div>
	<div class="heading" style="margin-top:20px;">
		Load Plan
	</div>
	<hr style="">
	<div id="load_plan_div">
		<table style="margin-left:0px; margin-top:5px; margin-bottom:10px; line-height:10px; font-size:10px;">
			<tr style="font-weight:bold;">
				<td style="width:30px;">
				</td>
				<td style="width:70px;">
					Driver
				</td>
				<td style="width:50px;">
					Truck
				</td>
				<td style="width:50px;">
					Trailer
				</td>
				<td style="width:60px;">
					Expected<br>Time
				</td>
				<td style="width:70px;">
					Deadline
				</td>
				<td style="width:110px;">
					Goalpoint<br>Type
				</td>
				<td style="width:70px;">
					GPS
				</td>
				<td style="width:105px; padding-right:5px;">
					Location
				</td>
				<td style="width:135px; padding-right:5px;">
					Notes
				</td>
				<td style="width:60px; text-align:right; padding-right:5px;">
					Leeway
				</td>
				<td style="width:80px; text-align:right;">
					Complete
				</td>
			</tr>
		</table>
		<div id="goalpoints_div_<?=$row_id?>" style="min-height:20px;">
			<!--AJAX GOES HERE!-->
			<script>load_goalpoints_div('<?=$row_id?>')</script>
			<span style="margin-left:450px;"><img style="height:20px;" src="/images/loading.gif"/></span>
		</div>
		<form id="new_goalpoint_form_<?=$row_id?>">
			<input type="hidden" name="add_new_gp_load_id" value="<?=$row_id?>" />
			<table style="margin-left:0px; margin-top:15px; margin-bottom:10px; line-height:10px; font-size:10px;">
				<tr style="">
					<td style="width:30px;">
					</td>
					<td style="width:70px;">
						<?php echo form_dropdown("temp_gp_client",$clients_dropdown_options,$load["client_id"],"id='temp_gp_client_$row_id' class='' style='width:60px; height:30px; font-size:10px;'");?>
					</td>
					<td style="width:50px;">
						<?php echo form_dropdown("temp_gp_truck",$truck_dropdown_options,$load["load_truck_id"],"id='temp_gp_truck_$row_id' class='' style='width:40px; height:30px; font-size:10px;'");?>
					</td>
					<td style="width:50px;">
						<?php echo form_dropdown("temp_gp_trailer",$trailer_dropdown_options,$load["load_trailer_id"],"id='temp_gp_trailer_$row_id' class='' style='width:40px; height:30px; font-size:10px;'");?>
					</td>
					<td style="width:60px;">
						<span>deadline?</span>
						<input id="has_deadline_cb_<?=$row_id?>" type="checkbox" onchange="has_deadline_changed('<?=$row_id?>')"/>
					</td>
					<td style="width:70px;">
						<input placeholder="Date Time" type="text" id="temp_deadline_<?=$row_id?>" name="temp_deadline" class="" style="font-family:arial; font-size:10px; width:60px; height:30px; display:none;" value=""/>
					</td>
					<td style="width:110px;">
						<?php
							$options = array(
								"Select" => "Select Type",
								"Pick" => "Pick (2 hr)",
								"Drop" => "Drop (2 hr)",
								"Driver Change" => "Driver Change (30 min)",
								"Truck Change" => "Truck Change (30 min)",
								"Trailer Change" => "Trailer Change (15 min)",
								"Fuel" => "Fuel (30 min)",
								"Break" => "Break (15 min)",
								"Waypoint" => "Waypoint (0 min)",
							);
						?>
						<?php echo form_dropdown("temp_gp_type",$options,"Select","id='temp_gp_type_$row_id' class='' style='width:100px; height:30px; font-size:10px;'");?>
					</td>
					<td style="width:70px;">
						<input placeholder="Lat, Long" type="text" id="temp_gp_gps_<?=$row_id?>" name="temp_gp_gps" class="" style="font-family:arial; font-size:10px; width:60px; height:30px;" onblur="auto_fill_goalpoint_location('<?=$row_id?>')"/>
					</td>
					<td style="width:105px; padding-right:5px;">
						<input placeholder="Location Name" type="text" id="temp_gp_location_name_<?=$row_id?>" name="temp_gp_location_name" class="gp_row_edit_<?=$row_id?>" style="font-family:arial; font-size:10px; width:95px; height:15px;" value=""/><br>
						<div id="gp_location_text_<?=$row_id?>" name="gp_location_text" style="color:#808080d6; border:solid #ADADAD 1px; border-radius:2px; background:white; padding-top:3px; height:10px; width:93px; position:relative; top:0px;">City, State</div>
						<input type="hidden" id="new_gp_location_<?=$row_id?>" name="new_gp_location" style="width:95px;"/>
					</td>
					<td style="width:135px; padding-right:5px;">
						<input placeholder="Notes" type="text" id="temp_gp_notes_<?=$row_id?>" name="temp_gp_notes" class="" style="font-family:arial; font-size:10px; width:125px; height:30px;" value=""/>
					</td>
					<td style="min-width:60px; padding-right:5px;">
					</td>
					<td style="width:80px; text-align:center;">
						<img title="Add Goalpoint" id="add_goalpoint_circle_<?=$row_id?>" src="/images/add_circle.png" style="position:relative; top:2px; left:15px; height:20px; cursor:pointer;" onclick="add_new_goalpoint('<?=$row_id?>')"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div style="clear:both;"></div>
	<div id="truck_attachments" style="margin-top:20px;">
		<span class="heading">Attachments</span>
		<hr>
		<?php if(!empty($attachments)): ?>
			<?php foreach($attachments as $attachment): ?>
				<div class="attachment_box" style="float:left;margin:5px;margin-bottom:20px;">
					<a target="_blank" title="Download <?=$attachment['attachment_name']?>" style="text-decoration:none;color:#4e77c9;" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>"><?=$attachment['attachment_name']?></a>
				</div>
			<?php endforeach ?>
		<?php endif ?>
	</div>
	<div style="clear:both;"></div>
</div>
<div id="ajax_script_div">
	<!-- THIS IS FOR AJAX SCRIPT RESPONSES !-->
</div>