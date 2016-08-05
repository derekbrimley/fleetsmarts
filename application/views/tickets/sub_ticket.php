<?php
	if($ticket['truck_or_trailer']=="Truck")
	{
		$where = null;
		$where['id'] = $ticket['truck_id'];
		$truck = db_select_truck($where);
		$unit_number = $truck['truck_number'];
	}
	else 
	{
		$where = null;
		$where['id'] = $ticket['trailer_id'];
		$trailer = db_select_trailer($where);
		$unit_number = $trailer['trailer_number'];
	}
	
	$where = null;
	$where["object_type"] = "Ticket";
	$where['object_id'] = $ticket['id'];
	$action_items = db_select_action_items($where, 'due_date ASC');
	
	//GET PAYMENT HISTORY
	$where = null;
	$where["ticket_id"] = $ticket["id"];
	$ticket_payments = db_select_ticket_payments($where);
	
	//GET INSURANCE_CLAIM
	$claim_ticket_id = $ticket['claim_ticket_id'];
	$where = null;
	$where["ticket_id"] = $claim_ticket_id;
	$insurance_claim = db_select_insurance_claim($where);
	
	$true_false_options = array(
		"" => "Select",
		"True" => "Yes",
		"False" => "No"
	);
	$inspection_type_options = array(
		"" => "Select",
		"Quick" => "Quick",
		"Full" => "Full"
	);
	$ticket_id = $ticket['id'];
	$onclick = "onClick='truck_inspection_type_selected(<?=$ticket_id?>)'"
?>
<style>
	
	.sub_menu_tab_selected
	{
		background-color: rgb(239, 239, 239);
		line-height: 29px;
		text-align: center;
		text-decoration: none;
		color: black;
		font-family: arial;
		font-size: 12px;
		font-weight: bold;
		padding-top: 5px;
		padding-bottom: 4px;
		padding-right: 10px;
		padding-left: 10px;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
		cursor:pointer;
	}
	.add_btn
	{
		cursor:pointer;
		background-color:#6295FC;
		color:white;
		border:none;
		border-radius:3px;
		border: none;
	}
	.add_btn:hover
	{
		background-color:#4e77c9;
	}
	.add_btn:active
	{
		background-color:#3a5997;
	}
	.sub_menu_tab
	{
		background-color: #6295FC;
		line-height: 29px;
		text-align: center;
		text-decoration: none;
		color: white;
		font-family: arial;
		font-size: 12px;
		font-weight: bold;
		padding-top: 5px;
		padding-bottom: 4px;
		padding-right: 10px;
		padding-left: 10px;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
		cursor:pointer;
	}

	.sub_menu_tab:hover
	{
		background-color: rgb(239, 239, 239);
		color: #6295FC;
	}
	

	.ticket_form_input
	{
		width:111px;
		margin-bottom:3px;
	}
	
	.inspection_file_chooser
	{
		width:135px;
	}
	
	::-webkit-scrollbar-track
	{
		background:transparent;
	}
</style>
<script>
	$(".dp").datepicker();	
	$("#estimated_completion_date").datepicker();
	$("#completion_date").datepicker();
</script>
<div id="sub_div_<?=$ticket["id"]; ?>" style="width:982px; background-color:#F2F2F2;padding-bottom:3px;padding-top:3px;">
	<div id="sub_details_<?=$ticket["id"]; ?>" style="margin-right:3px;border:1px solid #CFCFCF;float:left;font-size:12px;height:228px;margin-left:5px;width:300px;background-color:rgb(239, 239, 239);">
		
		<!-- TITLE BAR OF MINI BOX 1 !-->
		<div style="background-color:#CFCFCF;height:25px">
			<span id="details_tab_<?=$ticket["id"]; ?>" style="" onclick="details_tab_change(<?=$ticket["id"]; ?>)" class="sub_menu_tab_selected">Details</span>
			<?php if(!is_null($ticket['claim_ticket_id'])): ?>
				<span id="insurance_tab_<?=$ticket["id"]; ?>" style="" onclick="insurance_sub_tab_clicked(<?=$ticket["id"]; ?>)" class="sub_menu_tab">Insurance</span>
			<?php endif ?>
			<?php if(!is_null($ticket['inspection_id'])): ?>
				<span id="inspections_tab_<?=$ticket["id"]; ?>" style="" onclick="inspections_sub_tab_clicked(<?=$ticket["id"]; ?>)" class="sub_menu_tab">Inspection</span>
			<?php endif ?>
			<span id="save_btn_<?=$ticket["id"]; ?>" style="cursor:pointer;display:none;margin-right:5px;float:right;">
				<img style="height:16px;position:relative;top:5px;" src="<?=base_url("images/save.png")?>" onclick="save_ticket(<?=$ticket["id"]; ?>)"/>
			</span>
			<span id="edit_inspection_btn_<?=$ticket["id"]; ?>" title="Edit Inspection Details" style="display:none;cursor:pointer; float:right; margin-right:5px;">
				<img style="height:16px;position:relative;top:5px;" src="<?=base_url("images/edit.png")?>" onclick="edit_inspection(<?=$ticket["id"]; ?>)"/>
			</span>
			<span id="inspection_save_btn_<?=$ticket["id"]; ?>" style="cursor:pointer;display:none;margin-right:5px;float:right;">
				<img style="height:16px;position:relative;top:5px;" src="<?=base_url("images/save.png")?>" onclick="save_inspection_ticket(<?=$ticket["id"]; ?>)"/>
			</span>
			<span id="edit_detail_btn_<?=$ticket["id"]; ?>" title="Edit Details" style="cursor:pointer; float:right; margin-right:5px;">
				<img style="height:16px;position:relative;top:5px;" src="<?=base_url("images/edit.png")?>" onclick="edit_ticket(<?=$ticket["id"]; ?>)"/>
			</span>
			<span id="loading_detail_gif_<?=$ticket["id"]; ?>" style="display:none; float:right; margin-right:5px;">
				<img style="height:16px;position:relative;top:5px;" src="<?=base_url("images/loading.gif")?>" />
			</span>
			
			<span id="insurance_save_btn_<?=$ticket["id"]; ?>" style="cursor:pointer;display:none;margin-right:5px;float:right;">
				<img style="height:16px;position:relative;top:5px;" src="<?=base_url("images/save.png")?>" onclick="save_insurance_ticket(<?=$ticket["id"]; ?>)"/>
			</span>
			<span id="insurance_edit_detail_btn_<?=$ticket["id"]; ?>" title="Edit Insurance Details" style="cursor:pointer; float:right; margin-right:5px;display:none;">
				<img style="height:16px;position:relative;top:5px;" src="<?=base_url("images/edit.png")?>" onclick="edit_insurance_ticket(<?=$ticket["id"]; ?>)"/>
			</span>
			
			<span id="refresh_btn" style="cursor:pointer;float:right;display:none; float:right; margin-right:5px;">
				<img style="height:16px;position:relative;top:5px;" src="<?=base_url("images/refresh.png")?>"/>
			</span>
			<span id="attach_btn_<?=$ticket["id"]; ?>" style="cursor:pointer;margin-right:5px;float:right;">
				<img style="height:16px;position:relative;top:5px;margin-left:2px; margin-right:2px;" src="<?=base_url("images/paper_clip2.png")?>" onclick="open_file_upload(<?=$ticket["id"]; ?>)"/>
			</span>
			<?php if(empty($ticket["claim_ticket_id"]) &&  $ticket["category"] != "Claim" ): ?>
				<span id="generate_insurance_claim_icon_<?=$ticket["id"]; ?>" style="cursor:pointer; float:right; margin-right:5px;">
					<img title="Generate Insurance Claim" style="height:16px;position:relative;top:5px;" src="<?=base_url("images/insurance_claim_icon.png")?>" onclick="generate_insurance_claim_clicked(<?=$ticket["id"]; ?>)"/>
				</span>
			<?php endif ?>
			<?php if(is_null($ticket['completion_date'])): ?>
				<span id="close_ticket_icon_<?=$ticket["id"]; ?>" style="cursor:pointer; float:right; margin-right:5px;">
					<img title="Close Ticket" style="height:16px;position:relative;top:5px;" src="<?=base_url("images/gray_box_w_check.png")?>" onclick="close_ticket_clicked(<?=$ticket["id"]; ?>)"/>
				</span>
			<?php endif ?>
		</div>
		
		<!-- DETAILS TAB !-->
		<div id="minibox_1_details_content_<?=$ticket["id"]; ?>" name="details_minibox_<?=$ticket["id"]; ?>" style="float:left;width:100%;height:201px;overflow:auto;overflow-x:hidden;">
			<form id="edit_ticket_form_<?=$ticket["id"]; ?>">
				<input id="ticket_id" name="ticket_id" type="hidden" value="<?=$ticket["id"]; ?>" />
				<table style="width:288px;margin-left:10px;margin-top:10px;table-layout:fixed;">
					<tr style="height:25px;">
						<td style="width:167px">Ticket Number</td>
						<td style="width:121px;"><?=$ticket['id']?></td>
					</tr>
					<tr style="height:25px;">
						<td style="">Description</td>
						<td style="padding-bottom:5px;padding-right:5px;" class="detail_<?=$ticket['id']?>" title="<?=$ticket['description']?>"><?=$ticket['description']?></td>
						<td id="edit_description_<?=$ticket['id']?>" class="edit_<?=$ticket['id']?>" style="display:none;">
							<input id="description" name="description" title="<?=$ticket['description']?>" value="<?=$ticket['description']?>" class="ticket_form_input" />
						</td>
					</tr>
					<tr>
						<td style="">Incident Date</td>
						<td class="detail_<?=$ticket['id']?>">
							<?php if(!is_null($ticket['incident_date'])): ?>
								<?=date('m/d/y',strtotime($ticket['incident_date']))?>
							<?php endif ?>
						</td>
						<td id="edit_incident_date_<?=$ticket['id']?>" class="edit_<?=$ticket['id']?>" style="display:none;">
							<input id="incident_date" name="incident_date" class="ticket_form_input dp"
							<?php if(!is_null($ticket['incident_date'])): ?>
								value="<?=date('m/d/y',strtotime($ticket['incident_date']))?>"/>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<td style="width:100px;">Next Action Date</td>
						<td class="detail_<?=$ticket['id']?>">
							<?php if(!is_null($ticket['action_item_due_date'])): ?>
								<?=date('m/d/y',strtotime($ticket['action_item_due_date']))?>
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<td style="">Responsible Party</td>
						<td class="detail_<?=$ticket['id']?>"><?=$ticket['responsible_party']?></td>
						<td id="edit_responsible_party_<?=$ticket['id']?>" class="edit_<?=$ticket['id']?>" style="display:none;">
							<input value="<?= $ticket['responsible_party'] ?>" id="responsible_party" class="ticket_form_input"/>
						</td>
					</tr>
					<tr>
						<td style="">Estimated Repair Date</td>
						<td class="detail_<?=$ticket['id']?>">
							<?php if(!is_null($ticket['estimated_completion_date'])): ?>
								<?=date('m/d/y',strtotime($ticket['estimated_completion_date']))?>
							<?php else: ?>
								TBD
							<?php endif ?>
						</td>
						<td id="edit_estimated_completion_date_<?=$ticket['id']?>" class="edit_<?=$ticket['id']?>" style="display:none;">
							<input id="estimated_completion_date" name="estimated_completion_date" class="ticket_form_input dp" 
							<?php if(!is_null($ticket['estimated_completion_date'])): ?>
								value="<?=date('m/d/y',strtotime($ticket['estimated_completion_date']))?>"
							<?php else: ?>
								value="TBD"
							<?php endif ?>
							class="ticket_form_input dp"/>
						</td>
					</tr>
					<tr style="height:25px;">
						<td style="">Unit Type</td>
						<td class="detail_<?=$ticket['id']?>"><?=$ticket['truck_or_trailer']?></td>
						<td id="edit_unit_type_<?=$ticket['id']?>" class="edit_<?=$ticket['id']?>" style="display:none;">
							<?php
								$unit_type_options = array(
									"Truck" => "Truck",
									"Trailer" => "Trailer",
									"Other" => "Other"
								)
							?>
							<?php echo form_dropdown("unit_type",$unit_type_options,$ticket['truck_or_trailer'],'id="unit_type" class="ticket_form_input"');?>
						</td>
					</tr>
					<tr>
						<td style="">Unit Number</td>
						<td class="detail_<?=$ticket['id']?>"><?=$unit_number?></td>
						<td id="edit_unit_number<?=$ticket['id']?>" class="edit_<?=$ticket['id']?>" style="display:none;">
							<?php if($ticket['truck_or_trailer']=="Truck"): ?>
								<?php echo form_dropdown('unit_number',$truck_options,$ticket['truck_id'],'id="unit_number" class="ticket_form_input"');?>
							<?php elseif($ticket['truck_or_trailer']=="Trailer"): ?>
								<?php echo form_dropdown('unit_number',$trailer_options,$ticket['trailer_id'],'id="unit_number" class="ticket_form_input"');?>
							<?php else: ?>
								
							<?php endif ?>
						</td>
					</tr>
					<tr>
						<td style="">Category</td>
						<td class="detail_<?=$ticket['id']?>"><?=$ticket['category']?></td>
						<td id="edit_category_<?=$ticket['id']?>" class="edit_<?=$ticket['id']?>" style="display:none;">
							<?php
								$category_options = array(
									"Damage" => "Damage",
									"Claim" => "Claim"
								)
							?>
							<?php echo form_dropdown('category',$category_options,$ticket['category'],'id="category" class="ticket_form_input"');?>
						</td>
					</tr>
					<tr>
						<td style="">Amount</td>
						<td class="detail_<?=$ticket['id']?>"><?=number_format($ticket['amount'],2)?></td>
						<td id="edit_amount_<?=$ticket['id']?>" class="edit_<?=$ticket['id']?>" style="display:none;">
							<input id="amount" name="amount" value="<?=$ticket['amount']?>" class="ticket_form_input" />
						</td>
					</tr>
					<tr style="height:25px;">
						<td style="">Date Closed</td>
						<td class="detail_<?=$ticket['id']?>">
							<?php if(!is_null($ticket['completion_date'])): ?>
								<?=date('m/d/y',strtotime($ticket['completion_date']))?></td>
							<?php endif ?>
						</td>
						<td id="edit_completion_date_<?=$ticket['id']?>" class="edit_<?=$ticket['id']?>" style="display:none;">
							<input id="completion_date" name="completion_date" class="ticket_form_input dp"
							<?php if(!is_null($ticket['completion_date'])): ?>
								value="<?=date('m/d/y',strtotime($ticket['completion_date']))?>" />
							<?php endif ?>
						</td>
					</tr>
				</table>
			</form>
		</div>
		
		<!-- INSURANCE TAB !-->
		<div id="minibox_1_insurance_content_<?=$ticket["id"]?>" name="minibox_1_insurance_content_<?=$ticket["id"]?>" style="float:left;width:100%;height:201px;overflow:auto;overflow-x:hidden;display:none;">
			<?php if(!is_null($ticket['claim_ticket_id'])): ?>
				<form id="edit_insurance_ticket_form_<?=$ticket["id"]?>">
					<input id="ticket_id" name="ticket_id" type="hidden" value="<?=$ticket["id"]; ?>" />
					<input id="claim_ticket_id" name="claim_ticket_id" type="hidden" value="<?=$ticket["claim_ticket_id"]; ?>" />
					<table style="width:288px;margin-left:10px;margin-top:10px;table-layout:fixed;">
						<tr style="height:25px;">
							<td style="font-weight:bold;width:167px">Claim Number</td>
							<td class="insurance_<?=$ticket['id']?>"><?=$ticket['claim_ticket_id']?></td>
							<td id="edit_insurance_claim_number_<?=$ticket['id']?>" style="display:none;" name="edit_insurance_claim_number_<?=$ticket['id']?>" class="edit_insurance_<?=$ticket['id']?>">
								<input id="claim_number" name="claim_number" value="<?=$ticket['claim_ticket_id']?>" class="ticket_form_input"/>
							</td>
						</tr>
					</table>
				</form>
			<?php endif ?>
		</div>
		
		<!-- INSPECTION TAB !-->
		<div id="minibox_1_inspections_content_<?=$ticket["id"]?>" name="minibox_1_inspections_content_<?=$ticket["id"]?>" style="float:left;width:100%;height:201px;overflow:auto;overflow-x:hidden;display:none;">
			<?php if(!empty($ticket['inspection_id'])): ?>
				
				<?php $attributes = array('id' => 'edit_inspection_form_'.$ticket['id']); ?>
				<?=form_open('tickets/upload_inspection_pics',$attributes)?>
					<input id="ticket_id" name="ticket_id" type="hidden" value="<?=$ticket['id']?>"/>
					<?php if(!is_null($ticket['truck_id'])): ?>
						
						<!-- INSPECTION TYPE TABLE !-->
						<table style="width:288px;margin-left:10px;margin-top:10px;table-layout:fixed;">
							<tr class="inspection_edit_<?=$ticket['id']?>" style="">
								<td style="width:167px;">Inspection Type</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $ticket['inspection_type'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<?php echo form_dropdown('truck_inspection_type',$inspection_type_options,$ticket['inspection_type'],'onClick="truck_inspection_type_selected(8)" id="truck_inspection_type" class="ticket_form_input"');?>
								</td>
							</tr>
							<tr class="inspection_edit_<?=$ticket['id']?>" style="">
								<td style="width:167px;">Odometer</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['odometer'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<input type="text" id="odometer" name="odometer"  class="ticket_form_input" value="<?=$inspection['odometer']?>"/>
								</td>
							</tr>
						</table>
						
						<div style="margin-left:10px;margin-top:10px;">
							<span class="heading">Quick Inspection</span>
							<br>
							<hr style="width:100%;">
						</div>
						
						<!-- TRUCK QUICK INSPECTION TABLE !-->
						<table style="width:288px;margin-left:10px;margin-top:10px;table-layout:fixed;">
							<tr>
								<td style="width:167px;">Vibration In Steering</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['is_steering_vibrating'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<?php echo form_dropdown('is_steering_vibrating',$true_false_options,$inspection['is_steering_vibrating'],'id="is_steering_vibrating" class="ticket_form_input"');?>
								</td>
							</tr>
							<tr>
								<td>Vibration Description</td>
								<td class="inspection_detail_<?=$ticket['id']?>" title="<?= $inspection['is_steering_vibrating_desc'] ?>"><?= $inspection['is_steering_vibrating_desc'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<textarea id="is_steering_vibrating_desc" name="is_steering_vibrating_desc" class="ticket_form_input"><?= $inspection['is_steering_vibrating_desc'] ?></textarea>
								</td>
							</tr>
							<tr>
								<td>Tires Wearing Uniformly</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['are_truck_tires_wearing_uniformly'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<?php echo form_dropdown('are_truck_tires_wearing_uniformly',$true_false_options,$inspection['are_truck_tires_wearing_uniformly'],'id="are_truck_tires_wearing_uniformly" class="ticket_form_input"');?>
								</td>
							</tr>
							<tr>
								<td>Tire Wear Description</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['are_truck_tires_wearing_uniformly_desc'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<textarea id="are_truck_tires_wearing_uniformly_desc" name="are_truck_tires_wearing_uniformly_desc" class="ticket_form_input"><?= $inspection['are_truck_tires_wearing_uniformly_desc'] ?></textarea>
								</td>
							</tr>
							<tr>
								<td>Truck Pulling Left</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['is_pulling_left'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<?php echo form_dropdown('is_pulling_left',$true_false_options,$inspection['is_pulling_left'],'id="is_pulling_left" class="ticket_form_input"');?>
								</td>
							</tr>
							<tr>
								<td>Truck Pulling Left Description</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['is_pulling_left_desc'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<textarea id="is_pulling_left_desc" name="is_pulling_left_desc" class="ticket_form_input"><?= $inspection['is_pulling_left_desc'] ?></textarea>
								</td>
							</tr>
							<tr>
								<td>Truck Pulling Right</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['is_pulling_right'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<?php echo form_dropdown('is_pulling_right',$true_false_options,$inspection['is_pulling_right'],'id="is_pulling_right" class="ticket_form_input"');?>
								</td>
							</tr>
							<tr>
								<td>Truck Pulling Right Description</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['is_pulling_right_desc'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<textarea id="is_pulling_right_desc" name="is_pulling_right_desc" class="ticket_form_input"><?= $inspection['is_pulling_right_desc'] ?></textarea>
								</td>
							</tr>
							<tr>
								<td>Tire Incident</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['is_new_truck_tire_incident'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<?php echo form_dropdown('is_new_truck_tire_incident',$true_false_options,$inspection['is_new_truck_tire_incident'],'id="is_new_truck_tire_incident" class="ticket_form_input"');?>
								</td>
							</tr>
							<tr>
								<td>Tire Incident Description</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['is_new_truck_tire_incident_desc'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<textarea id="is_new_truck_tire_incident_desc" name="is_new_truck_tire_incident_desc" class="ticket_form_input"><?= $inspection['is_new_truck_tire_incident_desc'] ?></textarea>
								</td>
							</tr>
							<tr>
								<td>Additional Truck Notes</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['additional_truck_notes'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<textarea id="additional_truck_notes" name="additional_truck_notes" class="ticket_form_input"><?= $inspection['additional_truck_notes'] ?></textarea>
								</td>
							</tr>
							<tr>
								<td>New Truck Damage</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['is_new_truck_damage'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<?php echo form_dropdown('is_new_truck_damage',$true_false_options,$inspection['is_new_truck_damage'],'id="is_new_truck_damage" class="ticket_form_input"');?>
								</td>
							</tr>
							<tr>
								<td>New Truck Damage Description</td>
								<td class="inspection_detail_<?=$ticket['id']?>"><?= $inspection['is_new_truck_damage_desc'] ?></td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="display:none">
									<textarea id="is_new_truck_damage_desc" name="is_new_truck_damage_desc" class="ticket_form_input"><?= $inspection['is_new_truck_damage_desc'] ?></textarea>
								</td>
							</tr>
						</table>
						
						<div style="margin-left:10px;margin-top:10px;">
							<span class="heading">Inspection Pictures</span>
							<br>
							<hr style="width:100%;">
						</div>
						
						<!-- TRUCK INSPECTION PICS TABLE !-->
						<table style="width:288px;margin-left:10px;margin-top:10px;table-layout:fixed;">
							<tr>
								<td style="width:167px;">Right Side Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_right_side_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_right_side_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_right_side_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_right_side_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
							<tr>
								<td>Left Side Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_left_side_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_left_side_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_left_side_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_left_side_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
							<tr>
								<td>Front Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_front_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_front_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_front_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_front_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
							<tr>
								<td>Back Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_back_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_back_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_back_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_back_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
							<tr>
								<td>Transponder Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_transponder_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_transponder_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_transponder_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_transponder_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
							<tr>
								<td>Driver Seat Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_driver_seat_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_driver_seat_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_driver_seat_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_driver_seat_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
							<tr>
								<td>Passenger Seat Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_passenger_seat_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_passenger_seat_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_passenger_seat_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_passenger_seat_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
							<tr>
								<td>Dash Board Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_dash_board_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_dash_board_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_dash_board_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_dash_board_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
							<tr>
								<td>Odometer Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_odometer_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_odometer_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_odometer_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_odometer_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
							<tr>
								<td>Front Right Axle Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_front_right_axle_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_front_right_axle_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_front_right_axle_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_front_right_axle_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
							<tr>
								<td>Front Left Axle Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_front_left_axle_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_front_left_axle_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_front_left_axle_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_front_left_axle_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
							<tr>
								<td>Back Right Axle Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_back_right_axle_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_back_right_axle_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_back_right_axle_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_back_right_axle_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
							<tr>
								<td>Back Left Axle Picture</td>
								<td>
									<?php if(!empty($inspection["truck_pic_back_left_axle_guid"])):?>
										<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$inspection["truck_pic_back_left_axle_guid"] ?>">View Pic</a>
									<?php endif;?>
								</td>
								<td class="inspection_edit_<?=$ticket['id']?>" style="text-align:center; display:none">
									<img id="truck_pic_back_left_axle_guid"  src="<?=base_url("images/camera.png")?>" onclick="open_inspection_picture_dialog('<?=$inspection["id"]?>','<?=$ticket["id"]?>','truck_pic_back_left_axle_guid')" style="position:relative; bottom:6px; height:20px; cursor:pointer;"/>
								</td>
							</tr>
						</table>
					<?php endif;?>
				</form>
			<?php endif ?>
		</div>
	</div>
	
	<div id="sub_tasks_<?=$ticket["id"]; ?>" style="margin-right:3px;border:1px solid #CFCFCF;float:left;font-size:12px;height:228px;width:360px;background-color:rgb(239, 239, 239);">
		<div style="background-color:#CFCFCF;height:25px;">
			<span id="tasks_tab_<?=$ticket["id"]; ?>" style="" onclick="tasks_tab_change(<?=$ticket["id"]; ?>)" class="sub_menu_tab_selected">Tasks</span>
			<span id="payments_tab_<?=$ticket["id"]; ?>" style="" onclick="payments_sub_tab_cicked(<?=$ticket["id"]; ?>)" class="sub_menu_tab">Payments</span>
			<span id="loading_tasks_gif_<?=$ticket["id"]; ?>" style="display:none;margin-right:2px;">
				<img style="height:16px;position:relative;bottom:7px;left:343px;" src="<?=base_url("images/loading.gif")?>" />
			</span>
		</div>
		<div id="minibox_2_tasks_content_<?=$ticket["id"]?>" name="minibox_2_tasks_content_<?=$ticket["id"]?>" style="float:left;width:100%;height:201px;">
			<div style="float:left;">
				<table style="margin-left:10px;font-weight:bold;">
					<tr>
						<td style="padding-top:10px; min-width:190px; max-width:190px;">Action Item</td>
						<td style="padding-top:10px; min-width:60px; max-width:60px;">Due Date</td>
						<td style="padding-top:10px; min-width:100px; max-width:100px;">Completion Date</td>
					</tr>
				</table>
				<div style="width:366px;height:139px;overflow:auto;">
					<table style="margin-left:10px;">
						<?php if(!empty($action_items)): ?>
							<?php foreach($action_items as $action_item): ?>
								<tr>
									<td style=" min-width:190px; max-width:190px;"title="<?= $action_item['description'] ?>"><?= $action_item['description'] ?></td>
									<td style=" min-width:60px; max-width:60px;">
										<?php if(!is_null($action_item['due_date'])): ?>
											<?= date('m/d/y',strtotime($action_item['due_date'])) ?>
										<?php endif ?>
									</td>
									<td style=" min-width:100px; max-width:100px; text-align:center;" id="completion_date_<?=$action_item['id']?>">
										
										<?php if(!is_null($action_item['completion_date'])): ?>
											<?= date('m/d/y',strtotime($action_item['completion_date'])) ?>
										<?php else: ?>
											<img style="margin:auto; width:15px;cursor:pointer" onclick="complete_action(<?=$action_item['id']?>)" src="<?=base_url("images/nextgen_action_item_button_icon.png")?>" />
										<?php endif ?>
									</td>
								</tr>
							<?php endforeach ?>
						<?php endif ?>
					</table>
				</div>
			</div>
			<div style="width:365px;height:34px;margin-right:1px;float:right;">
				<form id="action_item_form_<?= $ticket['id'] ?>" style="float:right;margin-top:5px;margin-right:9px;">
					<input name="ticket_id" id="t	icket_id" type="hidden" value="<?= $ticket['id'] ?>"/>
					<input name="note" id="note_<?= $ticket['id'] ?>" style="width:215;height:25px;margin-right:5px;" placeholder="Action Item"/>
					<input name="due_date" id="due_date_<?= $ticket['id'] ?>" class="dp" style="height:25px;width:67px;" placeholder="Due Date"/>
					<button onclick="add_action_item(<?= $ticket['id'] ?>)" class="jq_button" style="width:50px; height:25px; display:inline;" type="button">Add</button>
				</form>
			</div>
		</div>
		<div id="minibox_2_payment_history_content_<?=$ticket["id"]; ?>" name="minibox_1_payment_history_content_<?=$ticket["id"]; ?>" style="float:left;width:100%;height:201px;overflow:auto;overflow-x:hidden; display:none;">
			<table style="margin-top:10px; margin-left:10px;">
				<?php if(!empty($ticket_payments)):?>
					<?php foreach($ticket_payments as $ticket_payment):?>
						<tr>
							<td style="width:50px;">
								<?=date("m/d/y",strtotime($ticket_payment["account_entry"]["entry_datetime"]));?>
							</td>
							<td style="width:172px; padding-left:10px; padding-bottom:10px;">
								<?=$ticket_payment["account_entry"]["entry_description"]?>
							</td>
							<td style="width:50px; text-align:right;">
								<?=number_format($ticket_payment["account_entry"]["entry_amount"],2)?>
							</td>
						</tr>
					<?php endforeach;?>
				<?php else:?>
						<tr>
							<td>
								There are no payments in the system
							</td>
						</tr>
				<?php endif;?>
			</table>
		</div>
	</div>
	
	<div id="sub_notes_<?=$ticket["id"]; ?>" style="border:1px solid #CFCFCF;float:left;font-size:12px;height:228px;width:292px;background-color:rgb(239, 239, 239);">
		<div style="background-color:#CFCFCF;height:25px;">
			<span id="notes_tab_<?=$ticket["id"]; ?>" style="top:5px;left:4px;" onclick="notes_tab_change(<?=$ticket["id"]; ?>)" class="sub_menu_tab_selected">Notes</span>
			<span id="loading_notes_gif_<?=$ticket["id"]; ?>" style="display:none;margin-right:2px;">
				<img style="height:16px;position:relative;top:5px;left:205px;" src="<?=base_url("images/loading.gif")?>" />
			</span>
		</div>
		<div style="padding-left:10px;padding-top:10px;width:283px;height:159px;overflow:auto;overflow-x:hidden;">
			<?=nl2br($ticket['notes'])?>
		</div>
		<div style="width:300px;margin-right:1px;margin-top:5px;">
			<form id="note_form_<?= $ticket['id'] ?>" style="margin-left:5px;margin-top:1px;">
				<input name="ticket_id" id="ticket_id" type="hidden" value="<?= $ticket['id'] ?>"/>
				<input name="note" id="note_text_<?= $ticket['id'] ?>" type="hidden" value=""/>
			</form>
			<input name="temp_note" id="temp_note_text_<?= $ticket['id'] ?>" style="width:220;height:25px; margin-left:5px; margin-right:5px;margin-bottom:10px;" placeholder="Note"/>
			<button onclick="add_note(<?= $ticket['id'] ?>)" class="jq_button" style="width:50px; height:25px; display:inline;" type="button">Add</button>
		</div>
	</div>
	<div id="sub_attachments_<?=$ticket["id"]; ?>" style="width:975px;float:left;">
		<?php if(!empty($attachments)):?>
			<?php foreach($attachments as $attachment): ?>
				<div id="attachment_<?=$attachment['id']?>" class="attachment_box" style="float:left;margin:5px;">
					<a title="Download <?=$attachment['attachment_name']?>" style="text-decoration:none;color:#4e77c9;" target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>"><?=$attachment['attachment_name']?></a>
				</div>
			<?php endforeach ?>
		<?php endif;?>
	</div>
	<div style="clear: both"></div> 
</div>