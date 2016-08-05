<table style="margin-left:120px; margin-top:5px; margin-bottom:10px; line-height:10px; font-size:10px;">
	<?php
		$i = 0;
		$success_count = 0;
	?>
	<?php if(!empty($contact_attempts)):?>
		<?php
			$total_expected_miles = 0;
			$total_actual_miles = 0;
		?>
		<?php foreach($contact_attempts as $ca):?>
			<?php
				$i++;
				$row_style = "";
				if($i%2 == 1)
				{
					$row_style = "background:#E0E0E0;";
				}
				
				$ca_id = $ca["id"];
				
				//GET PERSON
				$where = null;
				$where["id"] = $ca["dispatcher_person_id"];
				$dispatcher_person = db_select_person($where);
				
				if($ca["contact_result"] == "Success")
				{
					$success_count++;
				}
				
				$total_expected_miles = $total_expected_miles + $ca["expected_miles"];
				$total_actual_miles = $total_actual_miles + $ca["actual_miles"];
			?>
			<tr style="<?=$row_style?> height:40px;"  onmouseover="ca_row_mouseover('<?=$ca["id"]?>')" onmouseout="ca_row_mouseout('<?=$ca["id"]?>')">
				<td style="width:55px; padding-top:5px;">
					<?=str_replace(' ','<br>',date("m/d/y H:i",strtotime($ca["ca_time"])))?>
				</td>
				<td style="width:55px; padding-top:7px;">
					<a target="_blank" href="http://maps.google.com/maps?q=<?=$ca["ca_gps"]?>">GPS</a>
				</td>
				<td style="width:60px; padding-top:7px;">
					<?=$ca["contact_method"]?>
				</td>
				<td style="width:60px; padding-top:7px;">
					<?=$ca["contact_result"]?>
				</td>
				<td style="width:165; padding-top:7px; padding-right:5px;">
					<?=$ca["notes"]?> - <?=$dispatcher_person["f_name"]?>
				</td>
				<td style="width:165; padding-top:7px; padding-right:5px;">
					<?=$ca["computer_notes"]?>
				</td>
				<td style="width:35px; padding-top:7px; padding-right:20px; text-align:right;">
					<?=$ca["expected_miles"]?>
				</td>
				<td style="width:25px; padding-top:7px; padding-right:15px; text-align:right;">
					<a target="_blank" href="<?=$ca["expected_map_url"]?>"><?=$ca["actual_miles"]?></a>
				</td>
				<td style="width:30px; padding-top:7px; padding-right:20px; text-align:right;">
					<?=$ca["efficiency_rating"]?>%
				</td>
				<td style="width:30px; padding-top:5px; text-align:center; background:rgb(239, 239, 239);">
					<img id="ca_trash_<?=$ca["id"]?>" class="" style="cursor:pointer; height:15px; display:none;" src="/images/trash.png" title="Delete" onclick="delete_contact_attempt('<?=$ca["id"]?>','<?=$log_entry_id?>')"/>
				</td>
			</tr>
		<?php endforeach;?>
	<?php endif;?>
	<tr style="height:10px;">
	</tr>
	<tr style="">
		<td style="width:55px;">
			<input placeholder="Date Time" type="text" id="temp_ca_time_<?=$log_entry_id?>" name="temp_ca_time_<?=$log_entry_id?>" class="" style="font-family:arial; font-size:10px; width:50px; height:24px;" value=""/>
		</td>
		<td style="width:55px;">
			<input placeholder="Lat, Long" type="text" id="temp_ca_gps_<?=$log_entry_id?>" name="temp_ca_gps_<?=$log_entry_id?>" class="" style="font-family:arial; font-size:10px; width:50px; height:24px;" onblur="auto_fill_goalpoint_location('<?=$log_entry_id?>')" value=""/>
		</td>
		<td style="width:60px;">
			<?php
				$options = array(
					"Select" => "Select Form",
					"Call" => "Call",
					"Text" => "Text",
					"Both" => "Both",
				);
			?>
			<?php echo form_dropdown("temp_ca_method_$log_entry_id",$options,"Select","id='temp_ca_method_$log_entry_id' class='' style='width:50px; height:24px; font-size:10px;'");?>
		</td>
		<td style="width:60px;">
			<?php
				$options = array(
					"Select" => "Select Result",
					"Success" => "Success",
					"Fail" => "Fail",
				);
			?>
			<?php echo form_dropdown("temp_ca_result_$log_entry_id",$options,"Select","id='temp_ca_result_$log_entry_id' class='' style='width:50px; height:24px; font-size:10px;'");?>
		</td>
		<td style="width:170;">
			<input placeholder="Notes" type="text" id="temp_ca_notes_<?=$log_entry_id?>" name="temp_ca_notes_<?=$log_entry_id?>" class="" style="font-family:arial; font-size:10px; width:160px; height:24px;" value=""/>
		</td>
		<td style="width:170;">
		</td>
		<td style="width:50px;">
		</td>
		<td style="width:40px;">
		</td>
		<td style="width:50px; text-align:right;" >
			<img title="Add Contact Attempt" src="/images/add_circle.png" style="position:relative; top:2px; height:20px; cursor:pointer;" onclick="add_contact_attempt('<?=$log_entry_id?>')"/>
			<input type="hidden" id="next_gp_time_<?=$log_entry_id?>" value="<?=$next_gp["expected_time"]?>"/>
		</td>
	</tr>
</table>
<?php
	$contact_percentage = 0;
	$efficiency_rating = 0;
	if($i != 0)
	{
		
		
		
		$contact_percentage = round($success_count/$i*100,2);
		
		//GET SHIFT REPORT
		$where = null;
		$where["log_entry_id"] = $log_entry_id;
		$shift_report = db_select_shift_report($where);

		$oor = round((($shift_report["odometer_miles"] - $total_actual_miles)/$total_actual_miles)*100,2);
		
		if($total_expected_miles != 0)
		{
			$efficiency_rating = round($total_actual_miles/$total_expected_miles*100,2);
		}
		
		$update = null;
		$update["contact_percentage"] = $contact_percentage;
		$update["efficiency_rating"] = $efficiency_rating;
		$update["map_miles"] = $total_actual_miles;
		$update["oor"] = $oor;
		
		//UPDATE SHIFT REPORT
		$where = null;
		$where["id"] = $shift_report["id"];
		db_update_shift_report($update,$where);

	
	}
	
?>
<script>
	$("#contact_percentage_<?=$log_entry_id?>").html("<?=number_format($contact_percentage,2)?>%");
	$("#efficiency_rating_<?=$log_entry_id?>").html("<?=number_format($efficiency_rating,2)?>%");
	$("#map_miles_<?=$log_entry_id?>").html("<?=number_format($total_actual_miles)?>");
	$("#shift_report_oor_<?=$log_entry_id?>").html("<?=number_format($oor,2)?>%");
	//alert('contact percentage');
</script>

