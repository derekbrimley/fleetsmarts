<?php
				$settlement_id = $settlement["id"];
				$img_style = " height:16px; position:relative; bottom:0px; margin-left:5px; margin-right:5px; ";
				
				//DETERMINE STATUS IMAGE
				if(!isset($settlement["kick_in"]))
				{
					$title = "Pending Kick In";
					$img = '/images/kick_in.png'; 
				}
				else if(empty($settlement["approved_datetime"]))
				{
					$title = "Pending Approval";
					$img = '/images/pending_settlement.png'; 
				}
				else if(empty($settlement["settled_datetime"]))
				{
					$title = "Pending Settlement";
					$img = '/images/pending_settlement_square.png'; 
					$img_style = " position:relative; height:19px; bottom:3px; left:1px; ";
				}
				else
				{
					$title = "Closed";
					$img = '/images/closed.png'; 
				}
				
				$status_img = '<img style="'.$img_style.' cursor:pointer;" title="'.$title.'" src="'.$img.'" onclick="status_icon_clicked(\''.$settlement_id.'\')"/>';
			
				//GET LOG ENTRY FOR END WEEK
				$where = null;
				$where["id"] = $settlement["end_week_id"];
				$log_entry = db_select_log_entry($where);
				
				$stats = get_driver_end_week_stats($log_entry,$settlement["client_id"]);
				
				$log_entry_id = $log_entry["id"];
				$driver_id = $settlement["client_id"];
				
				@$average_miles_per_day = number_format($stats["total_map_miles"]/$stats["total_in_truck_hours"]*24);
				@$average_miles_per_gallon = number_format($stats["total_odometer_miles"]/$stats["total_gallons"],2);
				@$oor_percentage = number_format(($stats["total_odometer_miles"]-$stats["total_map_miles"])/$stats["total_map_miles"]*100,2);
			
				$settled_date = "";
				if(!empty($settlement["settled_datetime"]))
				{
					$settled_date = date("m/d/y",strtotime($settlement["settled_datetime"]));
				}
				
				$driver_id = $settlement["client_id"];
				$log_entry_id = $settlement["end_week_id"];
				
			?>
				<table id="log_table" style="margin-left:3px; font-size:10px;">
					<tr style="height:15px;">
						<td style="overflow:hidden; min-width:30px;  max-width:30px;"><?=$status_img?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:45px;  max-width:45px;" VALIGN="middle" title="">
							<?=$settlement["fleet_manager"]["f_name"]?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:80px;  max-width:80px;" VALIGN="middle" title="">
							<?=$settlement["client"]["client_nickname"]?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:85px;  max-width:85px;" VALIGN="middle" title="">
							<?=date("n/j/y H:i",strtotime($stats["previous_end_week_end_leg"]["entry_datetime"])) ?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:85px;  max-width:85px;" VALIGN="middle" title="">
							<?=date("n/j/y H:i",strtotime($stats["this_end_week_end_leg"]["entry_datetime"])) ?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:40px;  max-width:40px; text-align:right;" VALIGN="middle" title="">
							<?=round($stats["total_truck_hours"])?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" VALIGN="middle" title="">
							<?=$stats["total_map_miles"]?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:75px;  max-width:75px; text-align:right;" VALIGN="middle" title="">
							<?=$average_miles_per_day ?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:50px;  max-width:50px; text-align:right;" VALIGN="middle" title="">
							<?=$average_miles_per_gallon ?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" VALIGN="middle" title="">
							<?=$oor_percentage ?>%
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" VALIGN="middle" title="">
							$<?=@number_format($stats["total_fuel_expense"]/$stats["total_gallons"],2) ?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:65px;  max-width:65px; text-align:right;" VALIGN="middle" title="">
							$<?=number_format($stats["total_damage_share"] + $stats["damage_adjustment_expense"]["expense_amount"],2)?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" VALIGN="middle" title="">
							$<?=number_format($stats["statement_amount"],2)?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" VALIGN="middle" title="">
							$<?=number_format($settlement["kick_in"],2)?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:75px;  max-width:75px; text-align:right;" VALIGN="middle" title="">
							<?php if(empty($settlement["approved_datetime"])):?>
								<a target="_blank" href="<?=base_url("index.php/settlements/load_driver_settlement_view/$log_entry_id/$driver_id")?>">Statement</a>
							<?php else:?>
								<a target="_blank" href="<?=base_url("index.php/settlements/display_db_settlement/$settlement_id")?>">Statement</a>
							<?php endif;?>
						</td>
						<td class="ellipsis" style="overflow:hidden; min-width:60px;  max-width:60px; text-align:right;" VALIGN="middle" title="">
							<?=$settled_date?>
						</td>
					</tr>
				</table>
