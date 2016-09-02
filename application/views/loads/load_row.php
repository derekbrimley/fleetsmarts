<?php
	date_default_timezone_set('America/Denver');
						
	//$img = "/images/load_action_".$load['status_number']."_icon.png";
	
	
	//DETERMINE NOTES IMAGE
	if(empty($load["load_notes"]))
	{
		$notes_img = "/images/add_notes_empty.png";
	}
	else
	{
		$notes_img = "/images/add_notes.png";
	}
	
	// $load_notes_short = $load['load_notes'];
	// if (strlen($load_notes_short)>15)
	// {
		// //find last space within length
		// $last_space = strrpos(substr($load_notes_short, 0, 19), ' ');
		// $trimmed_text = substr($load_notes_short, 0, $last_space);
	  
		// //add ellipses (...)
		// $load_notes_short = $trimmed_text.' ...';
	// }
	
	//MAKE EACH PICK OR DROP A LINK AND ADD A TITLE THAT SHOWS CITY,STATE
	$i = 1;
	$pick_text = "";
	$pick_title = "";
	$these_picks = $load['load_picks'];
	sort($these_picks);
	foreach($these_picks as $pick)
	{
		if($i == 1)
		{
			$pick_text = $pick['stop']["city"].", ".$pick['stop']["state"];
			$pick_title = $pick_title."\n".$pick['stop']["city"].", ".$pick['stop']["state"]."  ".date("n/j h:i",strtotime($pick["appointment_time"]));
		}
	}
	$pick_title = substr($pick_title,1);
	
	$drop_text = "";
	$drop_title = "";
	$these_drops = $load['load_drops'];
	sort($these_drops);
	foreach($these_drops as $drop)
	{
		$drop_text = $drop['stop']["city"].", ".$drop['stop']["state"];
		$drop_title = $drop_title."\n".$drop['stop']["city"].", ".$drop['stop']["state"]."  ".date("n/j h:i",strtotime($drop["appointment_time"]));
	}
	$drop_title = substr($drop_title,1);
	
	$load_url = $load['id'];
	
	//GET NEXT GOALPOINT
	$where = null;
	$where = "load_id = $load_url AND completion_time IS NULL and gp_type <> 'Current Geopoint'";
	$next_gp = db_select_goalpoint($where,"gp_order DESC");
	
	$action_style = "cursor:pointer; position:relative; left:2px; height:16px; width:16px";
	$action = "";
	//DETERMINE TITLE AND ONCLICK FOR EACH ACTION ICON
	if ($load["status_number"] == 1) //RATE CON PENDING
	{
		$action_title = "Mark RC Received";
		$action = "open_rc_dialog";
		$img = "/images/load_action_1_icon.png";
	}
	else if ($load["status_number"] == 2) //DISPATCH PENDING
	{
		$action_title = "Dispatch Load";
		$action = "open_dispatch_dialog";
		$img = "/images/green_envelope_box.png";
		
	}
	else if ($load["status_number"] == 3) //PICK PENDING
	{
		if(empty($next_gp["gps"]))
		{
			$action = "alert_missing_gp_gps";
		}
		elseif(empty($next_gp["truck_id"]))
		{
			$action = "alert_missing_gp_truck";
		}
		else
		{
			$action = "open_complete_goalpoint_dialog";
		}
		$action_title = "Mark Pick";
		$img = "/images/blue_box_w_white_up_arrow.png";
	}
	else if ($load["status_number"] == 4) //DROP PENDING
	{
		if(empty($next_gp["gps"]))
		{
			$action = "alert_missing_gp_gps";
		}
		elseif(empty($next_gp["truck_id"]))
		{
			$action = "alert_missing_gp_truck";
		}
		else
		{
			$action = "open_complete_goalpoint_dialog";
		}
		$action_title = "Mark Drop";
		$img = "/images/purple_box_w_white_down_arrow.png";
	}
	else if ($load["status_number"] == 5) //Dropped
	{
		$action_style = " position:relative; left:2px; height:16px; width:16px";
		$action_title = "Dropped";
		$action = "open_load_details";
		$img = "/images/solid_darkgreen_box_w_white_check.png";
	}
	else if ($load["status_number"] == 100) //Cancelled
	{
		$action_style = " position:relative; left:2px; height:16px; width:16px";
		$action_title = "Cancelled";
		$action = "open_load_details";
		$img = "/images/load_action_100_icon.png";
	}
	
	//IF RC LINK IS AVAILABLE, MAKE LINK TEXT
	$rc_link_text = '<a onClick="open_file_upload(\''.$load["id"].'\');return false;" style="color:black;" href="#">Add</a>';
	if(!empty($load["rc_link"]))
	{
		//$rc_link_text = "<a href='".$load['rc_link']."' target='_blank'>RC</a> ";
		$rc_link_text = '<a target="_blank" href="'.base_url("/index.php/documents/download_file")."/".$load["rc_link"].'">RC</a>';
	}
	
	
	$pick_date_style = "";
	$pick_date_text = "";
	$pick_date_title = "";
	if(!empty($load["first_pick_datetime"]))
	{
		$pick_date_text = date("m/d/y", strtotime($load['first_pick_datetime']));
		$pick_date_title = date("m/d/y h:i", strtotime($load['first_pick_datetime']));
		
		//MAKE PICK DATE RED IF LATE
		
		if($load["status_number"] < 3)
		{
			if (strtotime($load["first_pick_datetime"]) < (time()-24*60*60))//NEXT DAY
			{
				$pick_date_style = "color:red; font-weight:bold;";
			}
		}
	}
	
	$drop_date_style = "";
	$drop_date_text = "";
	$drop_date_title = "";
	if(!empty($load["final_drop_datetime"]))
	{
		$drop_date_text = date("m/d/y", strtotime($load['final_drop_datetime']));
		$drop_date_title = date("m/d/y H:i", strtotime($load['final_drop_datetime']));
		//$drop_date_title = strtotime($load['final_drop_datetime']);
		
		//MAKE DROP DATE RED IF LATE
		
		if($load["status_number"] < 5)
		{
			if (strtotime($load["final_drop_datetime"]) < (time()-24*60*60))//NEXT DAY
			{
				$drop_date_style = "color:red; font-weight:bold;";
			}
		}
	}
	
	//GET TRUCK
	$where = null;
	$where["id"] = $load["load_truck_id"];
	$truck = db_select_truck($where);
	
	//GET TRAILER
	$where = null;
	$where["id"] = $load["load_trailer_id"];
	$trailer = db_select_trailer($where);
	
	if(!empty($load["load_truck_id"]))
	{
		$geopoint = get_most_recent_geopoint($load["load_truck_id"]);
	}
	else
	{
		$geopoint = null;
	}
	
	$row_color_style = "";
	//DETERMINE IF TRUCK AS ENTERED OR EXITED GEOFENCE OF NEXT GOALPOINT
	if(!empty($geopoint) && !empty($next_gp))
	{
		$next_gp_latlng = explode(",",$next_gp["gps"]);
		if(count($next_gp_latlng) == 2)
		{
			$next_gp_lat = trim($next_gp_latlng[0]);
			$next_gp_lng = trim($next_gp_latlng[1]);
			
			$radius = .0075;
			//IF GEOPOINT IS IN GEOFENCE OF NEXT GOALPOINT
			if(abs($next_gp_lat - $geopoint["latitude"]) < $radius && abs($next_gp_lng - $geopoint["longitude"]) < $radius)
			{
				if($next_gp["arrival_departure"] == 'Arrival')
				{
					$row_color_style = "background:rgba(0,255,0,0.3);";//COLOR ROW GREEN
				}
				elseif($next_gp["arrival_departure"] == 'Departure')
				{
					$row_color_style = "background:rgba(255, 165, 0, 0.77);";//COLOR ROW RED
				}
			}
			else
			{
				if($next_gp["arrival_departure"] == 'Departure')
				{
					$row_color_style = "background:rgba(255, 0, 0, 0.3);";//COLOR ROW RED
				}
			}
		}
	}
	
	//GET NEXT PICK OR DROP GOALPOINT
	$where = null;
	$where = "load_id = $load_url AND completion_time IS NULL AND (gp_type = 'Pick' OR gp_type = 'Drop')";
	$next_pick_drop_gp = db_select_goalpoint($where,"gp_order DESC");
	
	//GET MOST RECENT CHECK CALL
	$where = null;
	$where["load_id"] = $load["id"];
	$most_recent_check_call = db_select_load_check_call($where,"recorded_datetime");
	
	//echo $most_recent_dispatch_update["id"];
	
	$update_text = "00:00";
	$update_style = "";
	if(!empty($most_recent_check_call))
	{
		//GET TIME DIFFERENCE BETWEEN NOW AND MOST RECENT DISPATCH UPDATE
		$seconds_diff = time() - strtotime($most_recent_check_call["recorded_datetime"]);
		
		if($seconds_diff < (2*60*60))//2 hours
		{
			$update_style = "color:green; font-weight:bold;";
		}
		elseif($seconds_diff > (3*60*60))//2 hours
		{
			$update_style = "color:red; font-weight:bold;";
		}
		else
		{
			$update_style = "color:orange; font-weight:bold;";
		}
		
		//$update_text = hours_to_text_mixed($seconds_diff/60/60,"hr","min");
		$update_text = convert_hours_to_duration_text($seconds_diff/60/60);
		
	}

	$driver_style = "";
	if(!empty($load["client"]))
	{
		$hold_report = get_hold_report($load["client"]["id"]);
		if($hold_report["hold_status"] == "Hold")
		{
			$driver_style = "color:red;";
		}
	}
	
	//GET MOST RECENT TRAILER GEOPOINT
	$current_trailer_geopoint = get_most_recent_trailer_geopoint($load["load_trailer_id"]);
	if(strtotime($current_trailer_geopoint["datetime_occurred"]) < (time() - 60*60))
	{
		$current_trailer_geopoint = null;
	}
	
?>
<div style="height:20px; padding-top:5px; padding-bottom:3px; <?=$row_color_style?>" class="clickable_row" onclick="">
	<input type="hidden" id="next_pick_drop_goalpoint_id_<?=$load_url?>" value="<?=$next_pick_drop_gp["id"]?>"/>
	<table style="font-size:10px; margin-left:5px;">
		<tr style="line-height:18px;">
			<td style="overflow:hidden; min-width:30px;  max-width:30px;  cursor:default;"   VALIGN="top" ><img id="status_icon_<?=$load_url?>" title="<?=$action_title?>" onclick="load_status_changed('<?=$load_url?>','<?=$action?>')" style="<?=$action_style?>" src="<?=$img?>" /></td>
			<td style="overflow:hidden; min-width:40px;  max-width:40px;  line-height:18px;" VALIGN="top" onclick="row_clicked('<?=$load_url?>')"><?=$load['fleet_manager']["f_name"]?></td>
			<td style="overflow:hidden; min-width:40px;  max-width:40px;  line-height:18px;" VALIGN="top" onclick="row_clicked('<?=$load_url?>')"><?=$load['driver_manager']["f_name"]?></td>
			<td style="overflow:hidden; min-width:70px; max-width:70px; line-height:18px;" VALIGN="top" class="ellipsis" title="<?=$load['billed_under_carrier']["company_side_bar_name"]?>" onclick="row_clicked('<?=$load_url?>')"><?=$load['billed_under_carrier']["company_side_bar_name"]?></td>
			<td style="overflow:hidden; min-width:55px; max-width:55px; line-height:18px; padding-left:5px; <?=$driver_style?>" VALIGN="top" class="ellipsis" title="<?=$load['client']["client_nickname"]?>" onclick="row_clicked('<?=$load_url?>')"><?=$load['client']["client_nickname"]?></td>
			<td style="overflow:hidden; min-width:45px; max-width:45px; line-height:18px;" VALIGN="top" class="ellipsis" title="<?=$truck['truck_number']?>" onclick="row_clicked('<?=$load_url?>')"><?=$truck['truck_number']?></td>
			<td style="overflow:hidden; min-width:45px; max-width:45px; line-height:18px;" VALIGN="top" class="ellipsis" title="<?=$trailer['trailer_number']?>" onclick="row_clicked('<?=$load_url?>')"><?=$trailer['trailer_number']?></td>
			<td style="overflow:hidden; min-width:60px; max-width:60px; line-height:18px;" VALIGN="top" title="<?=$load['customer_load_number']?>" onclick="row_clicked('<?=$load_url?>')"><?=$load['customer_load_number']?></td>
			<td style="overflow:hidden; min-width:60px;  max-width:60px;  line-height:18px;" VALIGN="top" class="ellipsis" onclick="row_clicked('<?=$load_url?>')" title="<?=$load['broker']["customer_name"]?>"><?=$load['broker']["customer_name"]?></td>
			<td style="overflow:hidden; min-width:60px;  max-width:60px;  line-height:18px; padding-right:10px; text-align:right;" VALIGN="top" onclick="row_clicked('<?=$load_url?>')">$<?=$load['expected_revenue']?></td>
			<td style="overflow:hidden; min-width:30px; max-width:30px; line-height:18px;" VALIGN="top" title="Rate Con"><?=$rc_link_text?></td>
			<?php if(empty($load["first_pick_datetime"])):?>
				<td style="overflow:hidden; min-width:290px;  max-width:290px;  line-height:18px; padding-right:5px;" class="ellipsis" VALIGN="top" onclick="row_clicked('<?=$load_url?>')" title="<?=$load["load_desc"]?>"><?=$load["load_desc"]?></td>
			<?php else:?>
				<td style="overflow:hidden; min-width:60px;  max-width:60px;  line-height:18px; <?=$pick_date_style?>" VALIGN="top" onclick="row_clicked('<?=$load_url?>')" title="<?=$pick_date_title?>"><?=$pick_date_text?></td>
				<td style="overflow:hidden; min-width:60px;  max-width:60px;  line-height:18px; <?=$drop_date_style?>" VALIGN="top" onclick="row_clicked('<?=$load_url?>')" title="<?=$drop_date_title?>"><?=$drop_date_text?></td>
				<td style="overflow:hidden; min-width:85px;  max-width:85px;  line-height:18px;" class="ellipsis" VALIGN="top" title="<?=$pick_title?>" onclick="row_clicked('<?=$load_url?>')"><?=$pick_text?></td>
				<td style="overflow:hidden; min-width:85px;  max-width:85px;  line-height:18px; padding-left:5px;" class="ellipsis" VALIGN="top" title="<?=$drop_title?>" onclick="row_clicked('<?=$load_url?>')"><?=$drop_text?></td>
			<?php endif;?>
			<td style="overflow:hidden; min-width:50px;  max-width:50px;  line-height:18px; padding-left:5px; <?=$update_style?>" class="ellipsis" VALIGN="top" title="<?=$update_text?>" onclick="row_clicked('<?=$load_url?>')"><?=$update_text?></td>
			<td style="overflow:hidden; min-width:30px; max-width:30px; line-height:18px;" VALIGN="top" onclick="" title=""><img id="notes_icon_<?=$load["id"]?>" name="notes_icon_<?=$load["id"]?>" title="<?=$load["load_desc"].$load['load_notes']?>" onclick="open_notes('<?=$load["id"]?>')" style="cursor:pointer; position:relative; left:2px; height:16px; width:16px" src="<?=$notes_img?>" /></td>
			<td style="overflow:hidden; min-width:30px; max-width:30px; line-height:18px;" VALIGN="top" onclick="" title="">
				<?php if(!empty($geopoint)):?>
					<?php if($geopoint["is_oor"] == "Yes"):?>
						<a href="<?=$geopoint["oor_url"]?>" target="_blank">
							<?php if($geopoint["speed"] == 0):?>
								<img id="geopoint_<?=$load["id"]?>" name="geopoint_<?=$load["id"]?>" title="OOR <?=date('m/d H:i',strtotime($geopoint["datetime"]))." ".round($geopoint["speed"])."MPH"?>" onclick="" style="cursor:pointer; position:relative; left:2px; height:16px; width:16px" src="/images/blinking_red_triangle.gif" />
							<?php else:?>
								<img id="geopoint_<?=$load["id"]?>" name="geopoint_<?=$load["id"]?>" title="OOR <?=date('m/d H:i',strtotime($geopoint["datetime"]))." ".round($geopoint["speed"])."MPH"?>" onclick="" style="cursor:pointer; position:relative; left:2px; height:16px; width:16px" src="/images/blinking_green_triangle.gif" />
							<?php endif;?>
						</a>
					<?php else:?>
						<a href="http://maps.google.com/maps?q=<?=$geopoint["latitude"].",".$geopoint["longitude"]?>" target="_blank">
							<?php if($geopoint["speed"] == 0):?>
								<img id="geopoint_<?=$load["id"]?>" name="geopoint_<?=$load["id"]?>" title="<?=date('m/d H:i',strtotime($geopoint["datetime"]))." ".round($geopoint["speed"])."MPH"?>" onclick="" style="cursor:pointer; position:relative; left:2px; height:16px; width:16px" src="/images/geopoint_stop_icon.png" />
							<?php else:?>
								<img id="geopoint_<?=$load["id"]?>" name="geopoint_<?=$load["id"]?>" title="<?=date('m/d H:i',strtotime($geopoint["datetime"]))." ".round($geopoint["speed"])."MPH"?>" onclick="" style="cursor:pointer; position:relative; left:2px; height:16px; width:16px" src="/images/geopoint_icon.png" />
							<?php endif;?>
						</a>
					<?php endif;?>
				<?php endif;?>
			</td>
			<td style="overflow:hidden; min-width:30px; max-width:30px; line-height:18px;" VALIGN="top" onclick="row_clicked('<?=$load_url?>')" title="">
				<?php
					
					$load_id = $load["id"];
					
					//HAS A PICKED BEEN MARKED?
					$where = null;
					$where = " load_id = $load_id AND completion_time IS NOT NULL AND gp_type = 'Pick' ";
					$completed_picks = db_select_goalpoints($where);
					
				?>
				<!-- SET ALERT INDICATOR -- THIS WILL OVERRIDE ANY NON-ALERT INDICATORS !-->
				<?php if((empty($load["rcr_datetime"]) && !empty($load["rc_link"])) || (empty($load["rc_link"]) && time() > (strtotime($load["booking_datetime"]) + 15*60))):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Rate Con is overdue" onclick="" style="position:relative; left:2px; height:16px;" src="/images/blinking_rate_con_icon.gif" />
				<?php elseif(!empty($current_trailer_geopoint) && !empty($completed_picks) && $load["is_reefer"] == "Reefer" && ((abs($current_trailer_geopoint["set_temperature"] - $current_trailer_geopoint["return_temperature"]) > 8) || ($current_trailer_geopoint["set_temperature"] < ($load["reefer_low_set"] - .5) || $current_trailer_geopoint["set_temperature"] > ($load["reefer_high_set"] + .5))) && !($next_pick_drop_gp["arrival_departure"] == 'Departure' && $geopoint["speed"] == 0) ):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Reefer Temp is out of spec" onclick="" style="position:relative; left:2px; height:16px;" src="/images/blinking_blue_alert.gif" />
				<?php elseif(!empty($current_trailer_geopoint) && $load["is_reefer"] == "Reefer" && $current_trailer_geopoint["fuel_level"] < 20 ):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Reefer is low on fuel" onclick="" style="position:relative; left:2px; height:16px;" src="/images/blinking_blue_fuel.gif" />
				<?php elseif(!empty($most_recent_check_call) && $most_recent_check_call["truck_fuel_level"] < .25 ):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Truck is low on fuel" onclick="" style="position:relative; left:2px; height:16px;" src="/images/blinking_red_fuel.gif" />
				<?php elseif((!empty($load["ready_for_dispatch_datetime"]) || !empty($completed_picks)) && empty($load["initial_dispatch_datetime"])):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Initial Dispatch is Overdue" onclick="" style="position:relative; left:2px; height:16px;" src="/images/blinking_green_envelope.gif" />
					<?php /**
					<?php elseif(!empty($most_recent_check_call) && $most_recent_check_call["truck_code_status"] == "Bad" ):?>
						<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Truck has bad codes" onclick="" style="position:relative; left:2px; height:16px;" src="/images/blinking_red_exclamation.gif" />		
					**/?>
				<?php elseif((!empty($load["initial_dispatch_datetime"]) && empty($most_recent_check_call)) || (!empty($most_recent_check_call) &&  time() > (strtotime($most_recent_check_call["recorded_datetime"]) + 3*60*60)) || (!empty($most_recent_check_call) && $most_recent_check_call["driver_answered"] == 'No Answer' &&  time() > (strtotime($most_recent_check_call["recorded_datetime"]) + 15*60))):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Check Call is Overdue" onclick="" style="position:relative; left:2px; height:16px;" src="/images/blinking_dispatch_icon.gif" />
				<?php /**
				<?php elseif($load["status"] == "Drop Pending" && empty($load["unsigned_bol_guid"])):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Pre-drop BoL is Overdue" onclick="" style="position:relative; left:2px; height:16px;" src="/images/blinking_blue_doc.gif" />
				**/?>
				<?php /**
				<?php elseif(!empty($load["initial_dispatch_datetime"]) && empty($load["signed_load_plan_guid"])):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Missing Accepted Load Plan" onclick="" style="position:relative; left:2px; height:16px;" src="/images/blinking_orange_doc.gif" />
				**/?>
				<!-- SET NON-ALERT INDICATOR -- THIS WILL BE OVERRIDDEN BY AN ALERT INDICATORS !-->
				<?php elseif(empty($load["rcr_datetime"])):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Waiting for Rate Con" onclick="" style="position:relative; left:4px; height:14px;" src="/images/red_document_shape.png" />
				<?php elseif(empty($load["load_truck_id"])):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Waiting on Truck to be assigned" onclick="" style="position:relative; right:2px; height:15px;" src="/images/orange_truck.png" />
				<?php elseif(empty($load["initial_dispatch_datetime"])):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Waiting on Initial Dispatch" onclick="" style="position:relative; left:2px; height:14px;" src="/images/orange_headset.png" />
				<?php elseif($next_gp["gp_type"] == "Pick"):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Next event is a Pick" onclick="" style="position:relative; left:2px; height:14px;" src="/images/log_pick.png" />
				<?php elseif($next_gp["gp_type"] == "Drop"):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Next event is a Drop" onclick="" style="position:relative; left:2px; height:14px;" src="/images/log_drop.png" />
				<?php elseif($next_gp["gp_type"] == "Driver Change"):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Next event is a Driver Change" onclick="" style="position:relative; left:2px; height:14px;" src="/images/driver_change.png" />
				<?php elseif($next_gp["gp_type"] == "Truck Change"):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Next event is a Truck Change" onclick="" style="position:relative; right:2px; height:16px;" src="/images/blue_truck.png" />
				<?php elseif($next_gp["gp_type"] == "Trailer Change"):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Next event is a Trailer Change" onclick="" style="position:relative; left:2px; height:10px;" src="/images/drop_trailer.png" />
				<?php elseif($next_gp["gp_type"] == "Fuel"):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Next event is a Fuel stop" onclick="" style="position:relative; left:2px; height:16px;" src="/images/log_fuel_fill.png" />
				<?php elseif($next_gp["gp_type"] == "Break"):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Next event is a Break" onclick="" style="position:relative; left:3px; height:14px;" src="/images/pink_hour_glass.png" />
				<?php elseif($next_gp["gp_type"] == "Waypoint"):?>
					<img id="indicator_icon_<?=$load["id"]?>" name="indicator_icon_<?=$load["id"]?>" title="Next event is a waypoint" onclick="" style="position:relative; left:5px; height:15px;" src="/images/log_checkpoint.png" />
				<?php endif;?>
			</td>
		</tr>
	</table>
</div>