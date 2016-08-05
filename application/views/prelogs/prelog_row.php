<?php
	$i++;
	$row_style = "";
	if($i%2 == 1)
	{
		$row_style = "background:#E0E0E0;";
	}
	
	$goalpoint_id = $goalpoint["id"];
	$show_error = false;
	if(!empty($filter_truck_id))
	{
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
	}
	
	//GET TRUCK NUMBER
	$where = null;
	$where["id"] = $goalpoint["truck_id"];
	$truck = db_select_truck($where);
	
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
<form id="edit_gp_form_<?=$goalpoint_id?>">
	<input type="hidden" name="goalpoint_id" value="<?=$goalpoint_id?>"/>
	<table style="margin-left:10px; line-height:12px; font-size:12px;">
		<?php if(!empty($filter_truck_id)):?>
			<tr style="<?=$row_style?> height:40px;" onmouseover="gp_row_mouseover('<?=$goalpoint_id?>')" onmouseout="gp_row_mouseout('<?=$goalpoint_id?>')">
		<?php else:?>
			<tr style="<?=$row_style?> height:40px;">
		<?php endif;?>
			<td style="width:30px; padding-top:5px; background:white;">
				<?php if(empty($goalpoint["completion_time"])):?>
					<div class="gp_row_details_<?=$goalpoint["id"]?>">
						<img id="order_up_arrow_<?=$goalpoint["id"]?>" style="display:block; cursor:pointer; margin-bottom:5px; height:10px; position:relative; display:none;" src="/images/grey_flat_up_arrow.png" title="Move Up" onclick="order_gp('<?=$goalpoint_id?>','up')"/>
						<img id="order_down_arrow_<?=$goalpoint["id"]?>" style="display:block; cursor:pointer; height:10px; position:relative; display:none;" src="/images/grey_flat_down_arrow.png" title="Move Up" onclick="order_gp('<?=$goalpoint_id?>','down')"/>
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
				<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$truck["truck_number"]?></span>
				<?php echo form_dropdown("gp_truck_id",$gp_edit_truck_options,$goalpoint["truck_id"],"id='gp_truck_id_$goalpoint_id' style='font-size:12px; height:24px;' class='gp_row_edit_$goalpoint_id' onchange=''");?>
			</td>
			<td style="width:70px; padding-top:5px;">
				<?php if(empty($goalpoint["completion_time"])):?>
					<a class="gp_exp_details_<?=$goalpoint_id?>" target="_blank" style="text-decoration:none;" href="<?=$goalpoint["dispatch_notes"]?>"><?=str_replace(' ','<br>',date("m/d/y H:i",strtotime($goalpoint["expected_time"])))?></a>
					<img class="gp_exp_loading_<?=$goalpoint_id?>" id="" class="" style="height:15px; position:relative; left:4px; display:none;" src="/images/loading.gif" title="" onclick=""/>
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
			<td style="width:110px; padding-top:5px;">
				<span class=""><?=$goalpoint_type_text?> <?php if(!empty($goalpoint["duration"])):?>(<?=hours_to_text($goalpoint["duration"]/60)?>)<?php endif;?><br><?=$goalpoint["arrival_departure"]?></span>
			</td>
			<td style="width:70px; padding-top:5px;">
				<span class="gp_row_details_<?=$goalpoint["id"]?>"><a target="_blank" href="http://maps.google.com/maps?q=<?=$goalpoint["gps"]?>">GPS</a></span>
				<input placeholder="Lat, Long" type="text" id="edit_gp_gps_<?=$goalpoint_id?>" name="edit_gp_gps" class="gp_row_edit_<?=$goalpoint["id"]?>" style="font-family:arial; font-size:12px; width:60px; height:24px; font-size:10px;" value="<?=$goalpoint["gps"]?>" onblur="auto_fill_goalpoint_edit_location('<?=$goalpoint["id"]?>')"/>
			</td>
			<td style="width:105px; padding-right:10px; padding-top:5px;">
				<span id="gp_location_<?=$goalpoint["id"]?>" class=""><?=$goalpoint["location"]?></span>
				<input type="hidden" id="gp_location_hidden_<?=$goalpoint["id"]?>" name="edit_gp_location" value=""/>
			</td>
			<td style="width:200; padding-top:5px; padding-right:15px;">
				<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$goalpoint["dm_notes"]?></span>
				<input placeholder="Notes" type="text" id="edit_gp_notes_<?=$goalpoint_id?>" name="edit_gp_notes" class="gp_row_edit_<?=$goalpoint["id"]?>" style="font-family:arial; font-size:12px; width:175px; height:24px; font-size:10px;" value="<?=$goalpoint["dm_notes"]?>"/>
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
			<td style="width:60; padding-top:5px; text-align:right;">
				<?php if(!empty($goalpoint["completion_time"])):?>
					<span class=""><?=str_replace(' ','<br>',date("m/d/y H:i",strtotime($goalpoint["completion_time"])))?></span>
				<?php else:?>
					<span id="edit_gp_icon_span_<?=$goalpoint_id?>" style="display:none;"><img id="edit_gp_icon_<?=$goalpoint_id?>" class="gp_row_details_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; margin-right:10px; position:relative; left:4px;" src="/images/edit.png" title="Edit" onclick="edit_goalpoint('<?=$goalpoint["id"]?>')"/></span>
					<img id="delete_gp_icon_<?=$goalpoint_id?>" class="gp_row_edit_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; position:relative; left:4px; margin-right:10px; display:none;" src="/images/trash.png" title="Delete" onclick="delete_goalpoint('<?=$goalpoint["id"]?>')"/>
					<img id="save_gp_icon_<?=$goalpoint_id?>" class="gp_row_edit_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; margin-right:10px; position:relative; left:4px; display:none;" src="/images/save.png" title="Save" onclick="save_goalpoint('<?=$goalpoint["id"]?>')"/>
					<img id="loading_icon_save_gp_<?=$goalpoint_id?>" class="" style="height:15px; position:relative; left:4px; display:none; margin-right:10px;" src="/images/loading.gif" title="" onclick=""/>
				<?php endif;?>
			</td>
		</tr>
	</table>
</form>