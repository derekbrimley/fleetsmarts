<?php
	$row_id = $trippak["id"];

	//GET TRUCK
	$where = null;
	$where["id"] = $trippak["truck_id"];
	$truck = db_select_truck($where);

	if(empty($truck))
	{
		$where = null;
		$where["truck_number"] = $trippak["truck_number"];
		$truck = db_select_truck($where);
	}

	//GET TRAILER
	$where = null;
	$where["id"] = $trippak["trailer_id"];
	$trailer = db_select_trailer($where);
	
	//GET DRIVER
	$where = null;
	$where["id"] = $trippak["driver_1_id"];
	$driver_1 = db_select_client($where);

	//GET CODRIVER
	$where = null;
	$where["id"] = $trippak["driver_2_id"];
	$driver_2 = db_select_client($where);

	//GET CARRIER
	$where = null;
	$where['id'] = $trippak['carrier_id'];
	$carrier = db_select_company($where);

	//GET USER
	$where = null;
	$where['id'] = $trippak['completed_by_id'];
	$user = db_select_user($where);

	//GET PERSON
	$where = null;
	$where['id'] = $user['person_id'];
	$person = db_select_person($where);
	$full_name = $person["f_name"] . " " . $person['l_name']
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
	
	.trippak_details_table tr
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
		<img id="refresh_trippak_details_icon_<?=$row_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:13px; position:relative; left:0px;" src="/images/refresh.png" title="Refresh" onclick="open_row_details('<?=$trippak["id"]?>')"/>
		<img id="edit_icon" class="details_<?=$row_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; right:1px;" src="/images/edit.png" title="Edit" onclick="edit_row_details('<?=$trippak["id"]?>')"/>
		<img id="save_icon_<?=$row_id?>" class="edit_<?=$row_id?>" style="display:none; margin-bottom:13px; margin-right:15px; cursor:pointer; height:14px; position:relative; left:0px;" src="/images/save.png" title="Save" onclick="save_trippak_edit('<?=$trippak["id"]?>');"/>
	</div>
	<div class="heading">
		Trippak Details
	</div>
	<hr style="">
	<form id="trippak_details_form_<?=$row_id?>">
		<div style="font-size:12px;">
			<input type="hidden" id="" id="trippak_id" name="trippak_id" value="<?=$row_id?>"/>
			<table class="trippak_details_table" style="float:left; width:330px;">
				<tr>
					<td style="width:110px;" class="field_name">	
						Load Number
					</td>
					<td style="max-width:150px;padding-right: 10px;" class="ellipsis" title="<?=$trippak["load_number"]?>">
						<span class="details_<?=$row_id?>"><?=$trippak["load_number"]?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_load_number_<?=$row_id?>" name="edit_load_number" onChange="validate_load(<?=$row_id?>);" value="<?=$trippak["load_number"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:110px;" class="field_name">	
						Carrier
					</td>
					<td style="max-width:150px;padding-right: 10px;" class="ellipsis" title="<?=$carrier['company_side_bar_name']?>">
						<span class=""><?=$carrier['company_side_bar_name']?></span>
					</td>
				</tr>
				<tr>
					<td style="width:110px;" class="field_name">	
						Drop City
					</td>
					<td style="max-width:150px;padding-right: 10px;" class="ellipsis" title="<?=$trippak["final_drop_city"]?>">
						<span class="details_<?=$row_id?>"><?=$trippak["final_drop_city"]?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_final_drop_city_<?=$row_id?>" name="edit_final_drop_city" value="<?=$trippak["final_drop_city"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:110px;" class="field_name">	
						Truck Number
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$truck["truck_number"]?></span>
						<?php echo form_dropdown('edit_truck_number',$truck_dropdown_options,$truck['id'],"id='edit_truck_number_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:110px;" class="field_name">	
						Trailer Number
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$trailer["trailer_number"]?></span>
						<?php echo form_dropdown('edit_trailer_number',$trailer_dropdown_options,$trailer['id'],"id='edit_trailer_number_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
			</table>
			<table class="trippak_details_table" style="float:left; width:275px;">
				<tr>
					<td style="width:100px;" class="field_name">	
						Odometer
					</td>
					<td>
						<span class="details_<?=$row_id?>">
							<?php if($trippak["odometer"]): ?>
								<?=number_format($trippak["odometer"])?>
							<?php endif ?>
						</span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_odometer_<?=$row_id?>" name="edit_odometer" value="<?=$trippak["odometer"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Driver 1
					</td>
					<td style="max-width:150px;padding-right: 10px;" class="ellipsis" title="<?=$driver_1["client_nickname"]?>">
						<span class="details_<?=$row_id?>"><?=$driver_1["client_nickname"]?></span>
						<?php echo form_dropdown('edit_driver_1',$clients_dropdown_options,$driver_1['id'],"id='edit_driver_1_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;padding-right: 10px;" class="field_name">	
						Driver 2
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$driver_2["client_nickname"]?>">
						<span class="details_<?=$row_id?>"><?=$driver_2["client_nickname"]?></span>
						<?php echo form_dropdown('edit_driver_2',$secondary_clients_dropdown_options,$driver_2['id'],"id='edit_driver_2_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						In Time
					</td>
					<td>
						<div class="">
							<?php if($trippak['in_time']): ?>
								<?=date('m/d/y g:i a',strtotime($trippak["in_time"]))?>
							<?php endif ?>
						</div>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Out Time
					</td>
					<td>
						<div class="">
							<?php if($trippak['out_time']): ?>
								<?=date('m/d/y g:i a',strtotime($trippak["out_time"]))?>
							<?php endif ?>
						</div>
					</td>
				</tr>
			</table>
			<table>
				
				<tr>
					<td style="width:110px;" class="field_name">	
						Lumper Amount
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$trippak["lumper_amount"]?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_lumper_amount_<?=$row_id?>" name="edit_lumper_amount" value="<?=$trippak["lumper_amount"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:110px;" class="field_name">	
						Scan Time
					</td>
					<td>
						<div class="">
							<?= date('m/d/y H:i',strtotime($trippak["scan_datetime"]))?>
						</div>
					</td>
				</tr>
				<tr>
					<td style="width:110px;padding-right: 10px;" class="field_name ellipsis">	
						Completed By
					</td>
					<td>
						<div class="">
							<?= $full_name?>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div style="clear:both;"></div>
	</form>
	<div style="clear:both;"></div>
	<div id="truck_attachments" style="margin-top:20px;">
		<span class="heading">Attachments</span>
		<hr>
		<?php if(!empty($attachments)): ?>
			<?php foreach($attachments as $attachment): ?>
				<div class="attachment_box" style="float:left;margin:5px;margin-bottom:20px;">
					<a target="_blank" title="Download <?=$attachment['attachment_name']?>" style="text-decoration:none;color:#4e77c9;" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>">
						<?php if($attachment['attachment_name']): ?>
							<?=$attachment['attachment_name']?>
						<?php else: ?>
							<?=$attachment['file_guid']?>
						<?php endif ?>
					</a>
				</div>
			<?php endforeach ?>
		<?php endif ?>
	</div>
	<div style="clear:both;"></div>
</div>
<div id="ajax_script_div">
	<!-- THIS IS FOR AJAX SCRIPT RESPONSES !-->
</div>
<script>
	$('#expected_pay_date_<?=$trippak["id"]?>').datepicker({ showAnim: 'blind' });
</script>