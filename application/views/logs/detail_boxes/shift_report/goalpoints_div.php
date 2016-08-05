<table style="margin-left:90px; margin-top:5px; margin-bottom:10px; line-height:10px; font-size:10px;">
	<?php
		$i = 0;
	?>
	<?php if(!empty($goalpoints)):?>
		<?php
			$previous_gp_expected_time = null;
		?>
		<?php foreach($goalpoints as $goalpoint):?>
			<?php
				$i++;
				$row_style = "";
				if($i%2 == 1)
				{
					$row_style = "background:#E0E0E0;";
				}
				
				$goalpoint_id = $goalpoint["id"];
				$show_error = false;
				if($i > 1)
				{
					if($goalpoint["expected_time"] < $previous_gp_expected_time)
					{
						$show_error = true;
						$error_title = "Non-chronological events";
					}
				}
				$previous_gp_expected_time = $goalpoint["expected_time"];
				
				if($goalpoint["duration"] < 0)
				{
					$show_error = true;
					$error_title = "Negative duration";
				}
				
				
				$deadline_text = "";
				if(!empty($goalpoint["deadline"]))
				{
					$deadline_text =date("m/d/y H:i",strtotime($goalpoint["deadline"]));
				}
				
				
				$replace_these = array("Arrival","Departure");
				$replace_with = array("<br>Arrival","<br>Departure");
				$goalpoint_type_text = str_replace($replace_these,$replace_with,$goalpoint["gp_type"]);
			?>
			<style>
				.gp_row_edit_<?=$goalpoint["id"]?>
				{
					display:none;
				}
			</style>
			<tr style="<?=$row_style?> height:40px;" onmouseover="gp_row_mouseover('<?=$goalpoint_id?>')" onmouseout="gp_row_mouseout('<?=$goalpoint_id?>')">
				<td style="width:30px; padding-top:5px; background:rgb(239, 239, 239);">
					<?php if(empty($goalpoint["completion_time"])):?>
						<div class="gp_row_details_<?=$goalpoint["id"]?>">
							<img id="order_up_arrow_<?=$goalpoint["id"]?>" style="display:block; cursor:pointer; margin-bottom:5px; height:10px; position:relative; display:none;" src="/images/grey_flat_up_arrow.png" title="Move Up" onclick="order_gp('<?=$log_entry_id?>','<?=$goalpoint_id?>','up')"/>
							<img id="order_down_arrow_<?=$goalpoint["id"]?>" style="display:block; cursor:pointer; height:10px; position:relative; display:none;" src="/images/grey_flat_down_arrow.png" title="Move Up" onclick="order_gp('<?=$log_entry_id?>','<?=$goalpoint_id?>','down')"/>
						</div>
					<?php else:?>
						<?php if($show_error):?>
							<img id="" style="height:15px; position:relative; top:5px; left:10px;" src="/images/red_exclamation_mark.png" title="<?=$error_title?>" onclick=""/>
						<?php else:?>
							<img id="" style="height:15px; position:relative; top:5px; left:5px;" src="/images/green_checkmark.png" title="Complete" onclick=""/>
						<?php endif;?>
				<?php endif;?>
				</td>
				<td style="width:70px; padding-top:5px;">
					<?php if(empty($goalpoint["completion_time"])):?>
						<a class="gp_exp_details_<?=$log_entry_id?>" target="_blank" style="text-decoration:none;" href="<?=$goalpoint["dispatch_notes"]?>"><?=str_replace(' ','<br>',date("m/d/y H:i",strtotime($goalpoint["expected_time"])))?></a>
						<img class="gp_exp_loading_<?=$log_entry_id?>" id="" class="" style="height:15px; position:relative; left:4px; display:none;" src="/images/loading.gif" title="" onclick=""/>
					<?php else:?>
						<a class="" target="_blank" style="text-decoration:none;" href="<?=$goalpoint["dispatch_notes"]?>"><?=str_replace(' ','<br>',date("m/d/y H:i",strtotime($goalpoint["expected_time"])))?></a>
					<?php endif;?>
				</td>
				<td style="width:70px; padding-top:5px;">
					<?php if(strtotime($goalpoint["deadline"]) < strtotime($goalpoint["expected_time"])):?>
						<span style="color:red; font-weight:bold;"><?=$deadline_text?></span>
					<?php else:?>
						<span><?=$deadline_text?></span>
					<?php endif;?>
				</td>
				<td style="width:80px; padding-top:5px;">
					<span class=""><?=$goalpoint_type_text?> <?php if(!empty($goalpoint["duration"])):?>(<?=hours_to_text($goalpoint["duration"]/60)?>)<?php endif;?><br><?=$goalpoint["arrival_departure"]?></span>
				</td>
				<td style="width:70px; padding-top:5px;">
					<span class="gp_row_details_<?=$goalpoint["id"]?>"><a target="_blank" href="http://maps.google.com/maps?q=<?=$goalpoint["gps"]?>">GPS</a></span>
					<input placeholder="Lat, Long" type="text" id="edit_gp_gps_<?=$goalpoint_id?>" name="edit_gp_gps_<?=$goalpoint_id?>" class="gp_row_edit_<?=$goalpoint["id"]?>" style="font-family:arial; font-size:12px; width:60px; height:24px; font-size:10px;" value="<?=$goalpoint["gps"]?>" onblur="auto_fill_goalpoint_edit_location('<?=$goalpoint["id"]?>')"/>
				</td>
				<td style="width:85px; padding-right:10px; padding-top:5px;">
					<span id="gp_location_<?=$goalpoint["id"]?>" class=""><?=$goalpoint["location"]?></span>
				</td>
				<td style="width:175; padding-top:5px; padding-right:15px;">
					<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$goalpoint["dm_notes"]?></span>
					<input placeholder="Notes" type="text" id="edit_gp_notes_<?=$goalpoint_id?>" name="edit_gp_notes_<?=$goalpoint_id?>" class="gp_row_edit_<?=$goalpoint["id"]?>" style="font-family:arial; font-size:12px; width:175px; height:24px; font-size:10px;" value="<?=$goalpoint["dm_notes"]?>"/>
				</td>
				<td style="width:70px; padding-top:5px; text-align:right; padding-right:10px;" title="<?=convert_hours_to_duration_text($goalpoint["leeway"])?>">
					<?php if(empty($goalpoint["completion_time"])):?>
						<?php if(!empty($goalpoint["leeway"])):?>
							<?php if($goalpoint["leeway"] <= 0):?>
								<span style="color:red; font-weight:bold;"><?=convert_hours_to_duration_text($goalpoint["leeway"])?><br>LATE</span>
							<?php elseif($goalpoint["leeway"] < 1):?>
								<span style="color:rgb(255, 94, 0); font-weight:bold;"><?=convert_hours_to_duration_text($goalpoint["leeway"])?><br>EARLY</span>
							<?php else:?>
								<span style="color:green; font-weight:bold;"><?=convert_hours_to_duration_text($goalpoint["leeway"])?><br>EARLY</span>
							<?php endif;?>
						<?php endif;?>
					<?php endif;?>
				</td>
				<?php if(empty($goalpoint["completion_time"])):?>
					<td style="width:60; padding-top:5px;">
						<img id="complete_gp_icon_<?=$goalpoint_id?>" class="gp_row_details_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; position:relative; left:2px; margin-right:20px;" src="/images/approve_commission.png" title="Complete" onclick="open_complete_goalpoint_dialog('<?=$goalpoint["id"]?>','<?=$log_entry_id?>','<?=date("m/d/y H:i",strtotime($goalpoint["expected_time"]))?>')"/>
						<img id="edit_gp_icon_<?=$goalpoint_id?>" class="gp_row_details_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; position:relative; left:1px;" src="/images/edit.png" title="Edit" onclick="edit_goalpoint('<?=$goalpoint["id"]?>')"/>
						<img id="delete_gp_icon_<?=$goalpoint_id?>" class="gp_row_edit_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; position:relative; left:4px; margin-right:20px; display:none;" src="/images/trash.png" title="Delete" onclick="delete_goalpoint('<?=$goalpoint["id"]?>','<?=$log_entry_id?>')"/>
						<img id="save_gp_icon_<?=$goalpoint_id?>" class="gp_row_edit_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; position:relative; left:4px; display:none;" src="/images/save.png" title="Save" onclick="save_goalpoint('<?=$goalpoint["id"]?>','<?=$log_entry_id?>')"/>
						<img id="loading_icon_save_gp_<?=$goalpoint_id?>" class="" style="height:15px; position:relative; left:4px; display:none;" src="/images/loading.gif" title="" onclick=""/>
					<td>
				<?php else:?>
					<td style="width:60; padding-top:5px; text-align:right;">
						<span class=""><?=str_replace(' ','<br>',date("m/d/y H:i",strtotime($goalpoint["completion_time"])))?></span>
					</td>
				<?php endif;?>
			</tr>
		<?php endforeach;?>
	<?php endif;?>
</table>
<script>
	$("#next_gp_time_<?=$log_entry_id?>").val('<?=$next_gp["expected_time"]?>');
</script>
