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
</style>
<tr style="<?=$row_style?> height:35px;">
	<td style="min-width:110px; max-width:110px; padding-top:5px;" class="ellipsis">
		<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$goalpoint_type_text?> <?php if(!empty($goalpoint["duration"])):?>(<?=hours_to_text($goalpoint["duration"]/60)?>)<?php endif;?><br><?=$goalpoint["arrival_departure"]?></span>
	</td>
	<td style="width:80; padding-top:5px;">
			<span class=""><?=str_replace(' ','<br>',date("m/d/y H:i",strtotime($goalpoint["completion_time"])))?></span>
	</td>
	<td style="min-width:70px; max-width:70px; padding-top:5px;">
		<?php if(strtotime($goalpoint["deadline"]) < strtotime($goalpoint["expected_time"])):?>
			<span class="gp_row_details_<?=$goalpoint["id"]?>" style="color:red; font-weight:bold;"><?=$deadline_text?></span>
		<?php else:?>
			<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$deadline_text?></span>
		<?php endif;?>
	</td>
	<td style="min-width:80px; max-width:80px; padding-right:10px; padding-top:5px;"  class="ellipsis" title="<?=$client["client_nickname"]?>">
		<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$client["client_nickname"]?></span>
	</td>
	<td style="min-width:80px; max-width:80px; padding-top:5px;" class="ellipsis">
		<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$truck["truck_number"]?></span>
	</td>
	<td style="min-width:80px; max-width:80px; padding-top:5px;" class="ellipsis">
		<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$trailer["trailer_number"]?></span>
	</td>
	<td style="width:70px; padding-top:5px;">
		<?php if(!empty($goalpoint["gps"])):?>
			<span class="gp_row_details_<?=$goalpoint["id"]?>"><a target="_blank" href="http://maps.google.com/maps?q=<?=$goalpoint["gps"]?>" title="<?=$goalpoint["gps"]?>">GPS</a></span>
		<?php endif;?>
	</td>
	<td style="min-width:105px; max-width:105px; padding-right:5px; padding-top:5px;" class="ellipsis" title="<?=$goalpoint["location_name"]?> <?=$goalpoint["location"]?>">
		<div id="gp_location_name_<?=$goalpoint["id"]?>" class="gp_row_details_<?=$goalpoint_id?> ellipsis" style="padding-right;5px;"><?=$goalpoint["location_name"]?></div>
		<div id="gp_location_<?=$goalpoint["id"]?>" class="gp_row_details_<?=$goalpoint_id?> ellipsis"><?=$goalpoint["location"]?></div>
	</td>
	<td style="min-width:135px; max-width:135px; padding-top:5px; padding-right:5px;" class="" title="<?=$goalpoint["dm_notes"]?>">
		<span class="gp_row_details_<?=$goalpoint["id"]?>"><?=$goalpoint["dm_notes"]?></span>
	</td>
</tr>
