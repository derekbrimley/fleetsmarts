<?php
	$i++;
	$row_style = "";
	if($i%2 == 1)
	{
		$row_style = "background:#E0E0E0;";
	}
	
	//MARK CURRENT GEPOINT GOALPOINT GREEN OR RED
	if($goalpoint["gp_type"] == "Current Geopoint")
	{
		//$row_style = "background:#c4f1bc;";
		$row_style = $row_style." font-weight:bold; font-size:12px;";
	}
	
	$row_id = $goalpoint["load_id"];
	
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
	<table style="margin-left:0px; line-height:12px; font-size:11px;">
		<tr style="<?=$row_style?> height:35px;" onmouseover="gp_row_mouseover('<?=$goalpoint_id?>')" onmouseout="gp_row_mouseout('<?=$goalpoint_id?>')">
			<td style="width:30px; padding-top:5px; background:rgb(239, 239, 239);">
				<?php if(empty($goalpoint["completion_time"])):?>
					<?php if($goalpoint["gp_type"] != "Current Geopoint"):?>
						<div class="gp_row_details_<?=$goalpoint["id"]?>">
							<img id="order_up_arrow_<?=$goalpoint["id"]?>" style="display:block; cursor:pointer; margin-bottom:5px; height:10px; position:relative; display:none;" src="/images/grey_flat_up_arrow.png" title="Move Up" onclick="order_gp(<?=$row_id?>,'<?=$goalpoint_id?>','up')"/>
							<img id="order_down_arrow_<?=$goalpoint["id"]?>" style="display:block; cursor:pointer; height:10px; position:relative; display:none;" src="/images/grey_flat_down_arrow.png" title="Move Up" onclick="order_gp(<?=$row_id?>,'<?=$goalpoint_id?>','down')"/>
						</div>
					<?php else:?>
						<img id="" style="display:block; height:16px; position:relative; top:5px; left:5px;" src="/images/grey_geopoint_icon.png" title="Current Geopoint" onclick=""/>
					<?php endif;?>
				<?php else:?>
					<?php if($show_error):?>
						<img id="" style="height:15px; position:relative; top:5px; left:10px;" src="/images/red_exclamation_mark.png" title="<?=$error_title?>" onclick=""/>
					<?php else:?>
						<img id="" style="height:15px; position:relative; top:5px; left:5px;" src="/images/green_checkmark.png" title="Complete" onclick=""/>
					<?php endif;?>
				<?php endif;?>
			</td>
			<td style="min-width:70px; max-width:70px; padding-top:5px;"  class="ellipsis" title="<?=$client["client_nickname"]?>">
				<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$client["client_nickname"]?></span>
				<?php echo form_dropdown("gp_client_id",$clients_dropdown_options,$goalpoint["client_id"],"id='gp_client_id_$goalpoint_id' style='font-size:10px; height:24px; width:60px;' class='gp_row_edit_$goalpoint_id' onchange=''");?>
			</td>
			<td style="min-width:50px; max-width:50px; padding-top:5px;" class="ellipsis">
				<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$truck["truck_number"]?></span>
				<?php echo form_dropdown("gp_truck_id",$truck_dropdown_options,$goalpoint["truck_id"],"id='gp_truck_id_$goalpoint_id' style='font-size:10px; height:24px; width:40px;' class='gp_row_edit_$goalpoint_id' onchange=''");?>
			</td>
			<td style="min-width:50px; max-width:50px; padding-top:5px;" class="ellipsis">
				<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$trailer["trailer_number"]?></span>
				<?php echo form_dropdown("gp_trailer_id",$trailer_dropdown_options,$goalpoint["trailer_id"],"id='gp_trailer_id_$goalpoint_id' style='font-size:10px; height:24px; width:40px;' class='gp_row_edit_$goalpoint_id' onchange=''");?>
			</td>
			<td style="min-width:60px; max-width:60px; padding-top:5px;">
				<?php if(empty($goalpoint["completion_time"])):?>
					<?php /**
					<a class="gp_exp_details_<?=$row_id?>" target="_blank" style="text-decoration:none;" href="<?=$goalpoint["dispatch_notes"]?>"><?=str_replace(' ','<br>',date("m/d/y H:i",strtotime($goalpoint["expected_time"])))?></a>
					**/?>
					<?php if($goalpoint["gp_type"] == "Current Geopoint"):?>
						<a class="gp_exp_details_<?=$row_id?>" target="_blank" style="text-decoration:none;" href="<?=$goalpoint["dispatch_notes"]?>"><?=date("m/d/y H:i",strtotime($goalpoint["expected_time"]))?></a>
					<?php else:?>
						<a class="gp_exp_details_<?=$row_id?>" target="_blank" style="" href="<?=$goalpoint["dispatch_notes"]?>">Map</a>
					<?php endif;?>
					<img class="gp_exp_loading_<?=$row_id?>" id="" style="height:15px; position:relative; left:4px; display:none;" src="/images/loading.gif" title="" onclick=""/>
				<?php else:?>
					<a class="" target="_blank" style="text-decoration:none;" href="<?=$goalpoint["dispatch_notes"]?>"><?=str_replace(' ','<br>',date("m/d/y H:i",strtotime($goalpoint["expected_time"])))?></a>
				<?php endif;?>
			</td>
			<td style="min-width:70px; max-width:70px; padding-top:5px;">
				<?php if(strtotime($goalpoint["deadline"]) < strtotime($goalpoint["expected_time"])):?>
					<span class="gp_row_details_<?=$goalpoint["id"]?>" style="color:red; font-weight:bold;"><?=$deadline_text?></span>
				<?php else:?>
					<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$deadline_text?></span>
				<?php endif;?>
				<input placeholder="Date Time" type="text" id="edit_gp_deadline_<?=$row_id?>" name="edit_gp_deadline" class="gp_row_edit_<?=$goalpoint["id"]?>" style="font-family:arial; font-size:10px; width:60px; height:24px;" value="<?=$deadline_text?>"/>
			</td>
			<td style="min-width:110px; max-width:110px; padding-top:5px;" class="ellipsis">
				<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$goalpoint_type_text?> <?php if(!empty($goalpoint["duration"])):?>(<?=hours_to_text($goalpoint["duration"]/60)?>)<?php endif;?><br><?=$goalpoint["arrival_departure"]?></span>
				<?php if($goalpoint["arrival_departure"] == "Arrival"):?>
					<span class="gp_row_edit_<?=$goalpoint["id"]?>"><?=$goalpoint_type_text?><br><input type="text" placeholder="Minutes" id="edit_gp_duration_<?=$goalpoint["id"]?>" name="edit_gp_duration"  style=" font-size:10px; width:30px; height:14px;" value="<?=$goalpoint["duration"]?>" /> minutes</span>
				<?php else:?>
					<span class="gp_row_edit_<?=$goalpoint["id"]?>"><?=$goalpoint_type_text?> <?php if(!empty($goalpoint["duration"])):?>(<?=hours_to_text($goalpoint["duration"]/60)?>)<?php endif;?><br><?=$goalpoint["arrival_departure"]?></span>
					<input type="hidden" placeholder="Minutes" id="edit_gp_duration_<?=$goalpoint["id"]?>" name="edit_gp_duration"  style=" font-size:10px; width:30px; height:14px;" value="<?=$goalpoint["duration"]?>" />
				<?php endif;?>
			</td>
			<td style="width:70px; padding-top:5px;">
				<?php if(!empty($goalpoint["gps"])):?>
					<span class="gp_row_details_<?=$goalpoint["id"]?>"><a target="_blank" href="http://maps.google.com/maps?q=<?=$goalpoint["gps"]?>" title="<?=$goalpoint["gps"]?>">GPS</a></span>
				<?php endif;?>
				<input placeholder="Lat, Long" type="text" id="edit_gp_gps_<?=$goalpoint_id?>" name="edit_gp_gps" class="gp_row_edit_<?=$goalpoint["id"]?>" style="font-family:arial; font-size:12px; width:60px; height:24px; font-size:10px;" value="<?=$goalpoint["gps"]?>" onblur="auto_fill_goalpoint_edit_location('<?=$goalpoint["id"]?>')"/>
			</td>
			<td style="min-width:105px; max-width:105px; padding-right:5px; padding-top:5px;" class="ellipsis" title="<?=$goalpoint["location_name"]?> <?=$goalpoint["location"]?>">
				<div id="gp_location_name_<?=$goalpoint["id"]?>" class="gp_row_details_<?=$goalpoint_id?> ellipsis" style="padding-right;5px;"><?=$goalpoint["location_name"]?></div>
				<input placeholder="Location Name" type="text" id="edit_gp_location_name_<?=$goalpoint_id?>" name="edit_gp_location_name" class="gp_row_edit_<?=$goalpoint["id"]?>" style="font-family:arial; font-size:10px; width:95px; height:14px;" value="<?=$goalpoint["location_name"]?>"/>
				<div id="gp_location_<?=$goalpoint["id"]?>" class="ellipsis"><?=$goalpoint["location"]?></div>
			</td>
			<td style="min-width:135px; max-width:135px; padding-top:5px; padding-right:5px;" class="" title="<?=$goalpoint["dm_notes"]?>">
				<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$goalpoint["dm_notes"]?></span>
				<input placeholder="Notes" type="text" id="edit_gp_notes_<?=$goalpoint_id?>" name="edit_gp_notes" class="gp_row_edit_<?=$goalpoint["id"]?>" style="font-family:arial; font-size:12px; width:125px; height:24px; font-size:10px;" value="<?=$goalpoint["dm_notes"]?>"/>
			</td>
			<td style="min-width:60px; max-width:60px; padding-top:5px; text-align:right; padding-right:5px;" class="ellipsis" title="<?=convert_hours_to_duration_text($goalpoint['leeway'])?>">
				<?php /**
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
				**/?>
			</td>
			<td style="width:80; padding-top:5px; text-align:right;">
				<?php if(!empty($goalpoint["completion_time"])):?>
					<span class=""><?=str_replace(' ','<br>',date("m/d/y H:i",strtotime($goalpoint["completion_time"])))?></span>
				<?php else:?>
					<?php if($goalpoint["gp_type"] != "Current Geopoint"):?>
						<?php if($is_first_incomplete_gp == TRUE):?>
							<?php if(empty($goalpoint["gps"])):?>
								<span id="mark_complete_span_<?=$goalpoint_id?>" style=""><img id="mark_gp_complete_icon_<?=$goalpoint_id?>" class="gp_row_details_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; margin-right:6px; position:relative; left:4px;" src="/images/grey_checkbox.png" title="Mark Complete" onclick="alert('Goalpoint must have GPS before marking complete!')"/></span>
							<?php elseif(empty($goalpoint["truck_id"])):?>
								<span id="mark_complete_span_<?=$goalpoint_id?>" style=""><img id="mark_gp_complete_icon_<?=$goalpoint_id?>" class="gp_row_details_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; margin-right:6px; position:relative; left:4px;" src="/images/grey_checkbox.png" title="Mark Complete" onclick="alert('Goalpoint must have a truck before marking complete!')"/></span>
							<?php else:?>
								<span id="mark_complete_span_<?=$goalpoint_id?>" style=""><img id="mark_gp_complete_icon_<?=$goalpoint_id?>" class="gp_row_details_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; margin-right:6px; position:relative; left:4px;" src="/images/grey_checkbox.png" title="Mark Complete" onclick="open_mark_goalpoint_complete_dialog('<?=$goalpoint["id"]?>','<?=$goalpoint["load_id"]?>')"/></span>
							<?php endif;?>
							<?php $is_first_incomplete_gp = FALSE; ?>
						<?php endif;?>
						<span id="edit_gp_icon_span_<?=$goalpoint_id?>" style=""><img id="edit_gp_icon_<?=$goalpoint_id?>" class="gp_row_details_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; margin-right:10px; position:relative; left:4px;" src="/images/edit.png" title="Edit" onclick="edit_goalpoint('<?=$goalpoint["id"]?>')"/></span>
						<img id="gp_back_<?=$goalpoint_id?>" class="gp_row_edit_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; position:relative; left:4px; margin-right:10px; display:none;" src="/images/back.png" title="Back" onclick="cancel_edit_gp('<?=$goalpoint["id"]?>')"/>
						<img id="delete_gp_icon_<?=$goalpoint_id?>" class="gp_row_edit_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; position:relative; left:4px; margin-right:10px; display:none;" src="/images/trash.png" title="Delete" onclick="delete_goalpoint('<?=$goalpoint["id"]?>','<?=$row_id?>')"/>
						<img id="save_gp_icon_<?=$goalpoint_id?>" class="gp_row_edit_<?=$goalpoint["id"]?>" style="cursor:pointer; height:15px; margin-right:10px; position:relative; left:4px; display:none;" src="/images/save.png" title="Save" onclick="save_goalpoint('<?=$goalpoint["id"]?>','<?=$row_id?>')"/>
						<img id="loading_icon_save_gp_<?=$goalpoint_id?>" class="" style="height:15px; position:relative; left:4px; display:none; margin-right:10px;" src="/images/loading.gif" title="" onclick=""/>
					<?php endif;?>
				<?php endif;?>
			</td>
		</tr>
	</table>
</form>