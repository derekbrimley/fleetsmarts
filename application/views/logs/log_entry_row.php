			<?php
				$log_entry_id = $log_entry["id"];
				$main_driver = $log_entry["main_driver"]["client_nickname"];
				$codriver = $log_entry["codriver"]["client_nickname"];
				$load_number = $log_entry["load"]["customer_load_number"];
				$entry_datetime = date("h:i m/d/y",strtotime($log_entry["entry_datetime"]));
				$date = date("m/d/y",strtotime($log_entry["entry_datetime"]));
				$time = date("H:i",strtotime($log_entry["entry_datetime"]));
				$city_state = $log_entry["city"].", ".$log_entry["state"];
				$city = $log_entry["city"];
				$state = $log_entry["state"];
				$route_url = $log_entry["route"];
				
				$entry_datetime_text = "<span class='city_state' oncontextmenu=\"edit_cell('$log_entry_id','date'); return false;\">$date</span> <span class='city_state' oncontextmenu=\"edit_cell('$log_entry_id','time'); return false;\">$time</span>";
				
				if(empty($city))
				{
					$city = "-----";
				}
				if(empty($state))
				{
					$state = "--";
				}
				$city_state_text = "<span class='city_state city ellipsis' oncontextmenu=\"edit_cell('$log_entry_id','city'); return false;\">$city,</span> <div class='city_state state' oncontextmenu=\"edit_cell('$log_entry_id','state'); return false;\">$state</div>";
				
				$route_link = "";
				if(!empty($route_url))
				{
					$route_link = "<a href='$route_url' target='_blank'>Route</a>";
				}
				
				$event_img = null;
				$entry_type = $log_entry["entry_type"];
				if($entry_type == 'Pick')
				{
					$img = '/images/log_pick.png';
					$event_img = '<img style="height:13px; position:relative; top:1px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Drop')
				{
					$img = '/images/log_drop.png';
					$event_img = '<img style="height:13px; position:relative; top:1px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Fuel Fill')
				{
					$img = '/images/log_fuel_fill.png';
				
					//GET FUEL STOP
					$where = null;
					$where["log_entry_id"] = $log_entry["id"];
					$fuel_stop = db_select_fuel_stop($where);
					
					if($fuel_stop["source"] == "Estimate")
					{
						$img = '/images/log_fuel_fill_estimate.png';
					}
				
					$event_img = '<img style="cursor:pointer; height:16px;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Fuel Partial')
				{
					$img = '/images/log_fuel_partial.png';
					$event_img = '<img style="cursor:pointer; height:16px;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Fuel Reefer')
				{
					$img = '/images/log_fuel_reefer.png';
					$event_img = '<img style="cursor:pointer; height:16px;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Checkpoint')
				{
					$img = '/images/log_checkpoint.png';
					$event_img = '<img style="height:16px; position:relative; left:2px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Checkpoint OOR')
				{
					$img = '/images/log_checkpoint_oor.png';
					$event_img = '<img style="height:16px; position:relative; left:2px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Driver In')
				{
					$img = '/images/driver_in.png';
					$event_img = '<img style="height:18px; position:relative; left:2px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Driver In OOR')
				{
					$img = '/images/driver_in_oor.png';
					$event_img = '<img style="height:18px; position:relative; left:2px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Driver Out')
				{
					$img = '/images/driver_out.png';
					$event_img = '<img style="height:18px; position:relative; left:2px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Driver Out OOR')
				{
					$img = '/images/driver_out_oor.png';
					$event_img = '<img style="height:18px; position:relative; left:2px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Pick Trailer')
				{
					$img = '/images/pick_trailer.png';
					$event_img = '<img style="height:15px; position:relative; left:2px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Pick Trailer OOR')
				{
					$img = '/images/pick_trailer_oor.png';
					$event_img = '<img style="height:15px; position:relative; left:2px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Drop Trailer')
				{
					$img = '/images/drop_trailer.png';
					$event_img = '<img style="height:13px; position:relative; left:0px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Drop Trailer OOR')
				{
					$img = '/images/drop_trailer_oor.png';
					$event_img = '<img style="height:13px; position:relative; left:0px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Check Call')
				{
					$img = '/images/log_check_call.png';
					$event_img = '<img style="height:18px; position:relative; left:2px; cursor:pointer;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Wet Service')
				{
					$img = '/images/log_service.png';
					$event_img = '<img style="cursor:pointer; height:20px; position:relative; bottom:1px;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Dry Service')
				{
					$img = '/images/log_service.png';
					$event_img = '<img style="cursor:pointer; height:18px; position:relative; bottom:1px;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Shift Report')
				{
					$img = '/images/log_shift_report.png';
					$event_img = '<img style="cursor:pointer; height:14px; left:1px; position:relative; top:2px;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'End Leg')
				{
					$img = '/images/log_end_leg.png';
					$event_img = '<img style="cursor:pointer; height:15px; position:relative; left:1px;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'End Week')
				{
					$img = '/images/end_week.png';
					$event_img = '<img style="cursor:pointer; height:14px; position:relative; left:1px;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Geopoint')
				{
					$img = '/images/geopoint_icon.png';
					$event_img = '<img style="cursor:pointer; height:14px; position:relative; left:1px;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				else if($entry_type == 'Geopoint Stop')
				{
					$img = '/images/geopoint_stop_icon.png';
					$event_img = '<img style="cursor:pointer; height:14px; position:relative; left:1px;" src="'.$img.'" title="'.$entry_type.'" onclick="event_icon_clicked(\''.$log_entry_id.'\')"/>';
				}
				
				if(empty($log_entry["locked_datetime"]))
				{
					$locked_img = "";
				}
				else
				{
					if(user_has_permission("unlock log event"))
					{
						$locked_img = '<img style="height:12px; cursor:pointer;" onclick="unlock_event(\''.$log_entry_id.'\')" src="/images/locked.png" title="Unlock" />';
					}
					else
					{
						$locked_img = "<img style='height:12px;' src='/images/locked.png' title='Locked' />";
					}
				}
				
			?>
			<input type="hidden" id="hidden_load_number_<?=$log_entry["id"]?>" value="<?=htmlentities($load_number)?>"/>
			<input type="hidden" id="hidden_main_driver_id_<?=$log_entry["id"]?>" value="<?=$log_entry["main_driver_id"]?>"/>
			<input type="hidden" id="hidden_codriver_id_<?=$log_entry["id"]?>" value="<?=$log_entry["codriver_id"]?>"/>
			<input type="hidden" id="hidden_truck_id_<?=$log_entry["id"]?>" value="<?=$log_entry["truck_id"]?>"/>
			<input type="hidden" id="hidden_trailer_id_<?=$log_entry["id"]?>" value="<?=$log_entry["trailer_id"]?>"/>
			<input type="hidden" id="hidden_date_<?=$log_entry["id"]?>" value="<?=$date?>"/>
			<input type="hidden" id="hidden_time_<?=$log_entry["id"]?>" value="<?=$time?>"/>
			<input type="hidden" id="hidden_address_<?=$log_entry["id"]?>" value="<?=htmlentities($log_entry["address"])?>"/>
			<input type="hidden" id="hidden_city_<?=$log_entry["id"]?>" value="<?=htmlentities($city)?>"/>
			<input type="hidden" id="hidden_state_<?=$log_entry["id"]?>" value="<?=htmlentities($state)?>"/>
			<input type="hidden" id="hidden_odometer_<?=$log_entry["id"]?>" value="<?=$log_entry["odometer"]?>"/>
			<input type="hidden" id="hidden_entry_notes_<?=$log_entry["id"]?>" value="<?=htmlentities($log_entry["entry_notes"])?>"/>
			<table id="log_table" style="margin-left:3px; font-size:10px;">
				<tr style="height:15px;">
					<td style="overflow:hidden; min-width:30px;  max-width:30px;"><?=$event_img?></td>
					<td id="load_number_<?=$log_entry["id"]?>" class="editable_cell ellipsis" style="overflow:hidden; min-width:65px;  max-width:65px;" VALIGN="middle" title="<?=htmlentities($load_number)?>" oncontextmenu="edit_cell('<?=$log_entry["id"]?>','load_number'); return false;"><?=htmlentities($load_number)?></td>
					<td id="main_driver_id_<?=$log_entry["id"]?>" class="editable_cell" style="overflow:hidden; min-width:60px;  max-width:60px;" VALIGN="top" title="<?=$main_driver?>" oncontextmenu="edit_cell('<?=$log_entry["id"]?>','main_driver_id'); return false;"><?=substr($main_driver,0,strpos($main_driver," ")+2)?></td>
					<td id="codriver_id_<?=$log_entry["id"]?>" class="editable_cell" style="overflow:hidden; min-width:60px;  max-width:60px;" VALIGN="top" title="<?=$codriver?>" oncontextmenu="edit_cell('<?=$log_entry["id"]?>','codriver_id'); return false;"><?=substr($codriver,0,strpos($codriver," ")+2)?></td>
					<td id="truck_id_<?=$log_entry["id"]?>" class="editable_cell" style="overflow:hidden; min-width:70px;  max-width:70px;" VALIGN="top" title="<?=$log_entry["truck"]["truck_number"]?>" oncontextmenu="edit_cell('<?=$log_entry["id"]?>','truck_id'); return false;"><?=$log_entry["truck"]["truck_number"]?></td>
					<td id="trailer_id_<?=$log_entry["id"]?>" class="editable_cell" style="overflow:hidden; min-width:60px;  max-width:60px;" VALIGN="top" title="<?=$log_entry["trailer"]["trailer_number"]?>" oncontextmenu="edit_cell('<?=$log_entry["id"]?>','trailer_id'); return false;"><?=$log_entry["trailer"]["trailer_number"]?></td>
					<td id="entry_datetime_<?=$log_entry["id"]?>" class="editable_cell" style="overflow:hidden; min-width:85px;  max-width:85px;" VALIGN="top" title=""><?=$entry_datetime_text?></td>
					<td id="address_<?=$log_entry["id"]?>" class="editable_cell ellipsis" style="overflow:hidden; min-width:85px;  max-width:85px;" VALIGN="top"title="<?=htmlentities($log_entry["address"])?>" oncontextmenu="edit_cell('<?=$log_entry["id"]?>','address'); return false;"><?=htmlentities($log_entry["address"])?></td>
					<td id="city_state_<?=$log_entry["id"]?>" class="editable_cell ellipsis" style="overflow:hidden; min-width:75px;  max-width:75px;" VALIGN="top" title="<?=$city_state?>"  ><?=$city_state_text?></td>
					<td id="odometer_<?=$log_entry["id"]?>" class="editable_cell" style="overflow:hidden; min-width:60px;  max-width:60px; text-align:right;" VALIGN="top" title="<?=number_format($log_entry["odometer"])?>" oncontextmenu="edit_cell('<?=$log_entry["id"]?>','odometer'); return false;"><?=number_format($log_entry["odometer"])?></td>
					<td id="miles_<?=$log_entry["id"]?>" class="" style="overflow:hidden; min-width:45px;  max-width:45px; text-align:right;" VALIGN="top" title="<?=$log_entry["miles"]?>" oncontextmenu=""><?=$log_entry["miles"]?></td>
					<td id="out_of_route_<?=$log_entry["id"]?>" style="overflow:hidden; min-width:45px;  max-width:45px; text-align:right;" VALIGN="top" title="<?=$log_entry["out_of_route"]?>%"  ><?=$log_entry["out_of_route"]?></td>
					<td id="mpg_<?=$log_entry["id"]?>" style="overflow:hidden; min-width:40px;  max-width:40px; text-align:right;" VALIGN="top" title="<?=$log_entry["mpg"]?>"  ><?=$log_entry["mpg"]?></td>
					<td id="entry_notes_<?=$log_entry["id"]?>" class="editable_cell ellipsis" style="overflow:hidden; min-width:120px;  max-width:120px; padding-left:15px;" VALIGN="top" title="<?=htmlentities($log_entry["entry_notes"])?>" oncontextmenu="edit_cell('<?=$log_entry["id"]?>','entry_notes'); return false;"><?=htmlentities($log_entry["entry_notes"])?></td>
					<td id="route_<?=$log_entry["id"]?>" class="" style="overflow:hidden; min-width:45px;  max-width:45px; padding-left:15px;" VALIGN="top" title="" oncontextmenu=""><?=$route_link?></td>
					<td><?=$locked_img?><td>
				</tr>
			</table>