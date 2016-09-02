<?php
	$row_id = $load["id"];
	
	//GET TRUCK
	$where = null;
	$where["id"] = $load["load_truck_id"];
	$truck = db_select_truck($where);
	
	//GET TRAILER
	$where = null;
	$where["id"] = $load["load_trailer_id"];
	$trailer = db_select_trailer($where);
	
	if($load["expected_miles"] != 0)
	{
		$rate_per_mile = $load["expected_revenue"]/$load["expected_miles"];
	}
	else
	{
		$rate_per_mile = 0;
	}
	
	//GET CODRIVER
	$where = null;
	$where["id"] = $load["driver2_id"];
	$codriver = db_select_client($where);
	
	
	//GET AR SPECIALIST
	$where = null;
	$where["id"] = $load["ar_specialist_id"];
	$ars_user = db_select_user($where);
	
	
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
	
	.load_details_table tr
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
		<img id="refresh_load_details_icon_<?=$row_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:13px; position:relative; left:0px;" src="/images/refresh.png" title="Refresh" onclick="open_row_details('<?=$load["id"]?>')"/>
		<img id="edit_icon" class="details_<?=$row_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; right:1px;" src="/images/edit.png" title="Edit" onclick="edit_row_details('<?=$load["id"]?>')"/>
		<img id="save_icon_<?=$row_id?>" class="edit_<?=$row_id?>" style="display:none; margin-bottom:13px; margin-right:15px; cursor:pointer; height:14px; position:relative; left:0px;" src="/images/save.png" title="Save" onclick="save_load_edit('<?=$load["id"]?>');"/>
		<img id="attachment_icon" class="" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:20px; position:relative; left:1px;" src="/images/paper_clip2.png" title="Attachment" onclick="open_file_upload('<?=$load["id"]?>')"/>
	</div>
	<div class="heading">
		Load Details
	</div>
	<hr style="">
	<form id="load_details_form_<?=$row_id?>">
		<div style="font-size:12px;">
			<input type="hidden" id="" id="load_id" name="load_id" value="<?=$row_id?>"/>
			<table class="load_details_table" style="float:left; width:250px;">
				<tr>
					<td style="width:100px;" class="field_name">	
						Load Number
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["customer_load_number"]?>">
						<span class="details_<?=$row_id?>"><?=$load["customer_load_number"]?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_load_number_<?=$row_id?>" name="edit_load_number" value="<?=$load["customer_load_number"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Carrier
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["billed_under_carrier"]["company_name"]?>">
						<span class="details_<?=$row_id?>"><?=$load["billed_under_carrier"]["company_name"]?></span>
						<?php echo form_dropdown('edit_billed_under',$billed_under_options,$load['billed_under'],"id='edit_billed_under_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Fleet Manager
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["fleet_manager"]["f_name"]." ".$load["fleet_manager"]["l_name"]?>">
						<span class="details_<?=$row_id?>"><?=$load["fleet_manager"]["f_name"]." ".$load["fleet_manager"]["l_name"]?></span>
						<?php echo form_dropdown('edit_fleet_manager',$fleet_managers_dropdown_options,$load['fleet_manager_id'],"id='edit_fleet_manager_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Driver Manager
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$load["driver_manager"]["f_name"]." ".$load["driver_manager"]["l_name"]?></span>
						<?php echo form_dropdown('edit_driver_manager',$dm_filter_dropdown_options,$load['dm_id'],"id='edit_driver_manager_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Nat'l Fuel Avg
					</td>
					<td>
						<span class="details_<?=$row_id?>">$<?=number_format($load["natl_fuel_avg"],2)?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_natl_avg_<?=$row_id?>" name="edit_natl_avg" value="<?=$load["natl_fuel_avg"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Expected Rate
					</td>
					<td>
						<span class="details_<?=$row_id?>">$<?=number_format($load["expected_revenue"])?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_expected_rate_<?=$row_id?>" name="edit_expected_rate" value="<?=$load["expected_revenue"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Expected Miles
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=number_format($load["expected_miles"])?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_expected_miles_<?=$row_id?>" name="edit_expected_miles" value="<?=$load["expected_miles"]?>"/>
					</td>
				</tr>
			</table>
			<table class="load_details_table" style="float:left; margin-left:80px; width:250px;">
				<tr>
					<td style="width:100px;" class="field_name">	
						Driver 1
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<span class="details_<?=$row_id?>"><?=$load["client"]["client_nickname"]?></span>
						<?php echo form_dropdown('edit_client',$clients_dropdown_options,$load['client_id'],"id='edit_client_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Driver 2
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<span class="details_<?=$row_id?>"><?=$codriver["client_nickname"]?></span>
						<?php echo form_dropdown('edit_driver2',$clients_dropdown_options,$load['driver2_id'],"id='edit_driver2_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Truck
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$truck["truck_number"]?></span>
						<?php echo form_dropdown('edit_truck',$truck_dropdown_options,$load['load_truck_id'],"id='edit_truck_$row_id' class='edit_$row_id edit_input' onchange='' style=''");?>
						<input type="hidden" id="truck_is_already_assigned" name="truck_is_already_assigned" value="no" />
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Trailer
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$trailer["trailer_number"]?></span>
						<?php echo form_dropdown('edit_trailer',$trailer_dropdown_options,$load['load_trailer_id'],"id='edit_trailer_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Freight Type
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$load["is_reefer"]?></span>
						<?php $options = array(
							'Reefer' => 'Reefer',
							'Dry' => 'Dry',
							); 
						?>
						<?php echo form_dropdown('edit_is_reefer',$options,$load['is_reefer'],"id='edit_is_reefer_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Reefer Temp
					</td>
					<td>
						<div class="">
							<?=$load["reefer_low_set"]?> to <?=$load["reefer_high_set"]?>
						</div>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Unsigned BOL
					</td>
					<td>
						<?php if(!empty($load["unsigned_bol_guid"])):?>
							<a target="_blank" href='<?=base_url("/index.php/documents/download_file")."/".$load["unsigned_bol_guid"]?>'>Unsigned BOL</a>
						<?php endif;?>
					</td>
				</tr>
			</table>
			<table class="load_details_table" style="float:left; margin-left:80px; width:250px;">
				<tr>
					<td style="width:100px;" class="field_name">	
						Broker
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["broker"]["customer_name"]?>">
						<span class="details_<?=$row_id?>"><?=$load["broker"]["customer_name"]?></span>
						<?php echo form_dropdown('edit_broker',$broker_dropdown_options,$load['broker_id'],"id='edit_broker_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Broker MC
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["broker"]["customer_name"]?>">
						<span class=""><?=$load["broker"]["mc_number"]?></span>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Contact Info
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["contact_info"]?>">
						<span class="details_<?=$row_id?>"><?=$load["contact_info"]?></span>
						<input type="text" class="edit_<?=$row_id?> edit_input" id="edit_contact_info_<?=$row_id?>" name="edit_contact_info" value="<?=$load["contact_info"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Billing Method
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$load["billing_method"]?></span>
						<?php $options = array(
							'Factor' => 'Factor',
							'Direct Bill' => 'Direct Bill',
							); 
						?>
						<?php echo form_dropdown('edit_billing_method',$options,$load['billing_method'],"id='edit_billing_method_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Time of Booking
					</td>
					<td>
						<span class=""><?=date("n\/d\/y H:i",strtotime($load['booking_datetime']))?></span>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Load Type
					</td>
					<td>
						<span class="details_<?=$row_id?>"><?=$load["billing_method"]?></span>
						<?php $options = array(
							'Full Load' => 'Full Load',
							'Partial' => 'Partial',
							'Power Only' => 'Power Only',
							); 
						?>
						<?php echo form_dropdown('edit_load_type',$options,$load['billing_method'],"id='edit_load_type_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Rate/Mile
					</td>
					<td>
						<span class="" >$<?=number_format($rate_per_mile,2)?></span>
					</td>
				</tr>
			</table>
		</div>
		<div style="clear:both;"></div>
		<div class="heading">
			Billing Details
		</div>
		<hr style="">
		<div style="font-size:12px;">
			<input type="hidden" id="" id="load_id" name="load_id" value="<?=$row_id?>"/>
			<table class="load_details_table" style="float:left; width:250px;">
				<tr>
					<td style="width:100px;" class="field_name">	
						A/R Specialist
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["customer_load_number"]?>">
						<span class="details_<?=$row_id?>"><?=$ars_user["person"]["f_name"]?></span>
						<?php echo form_dropdown('edit_ars',$ars_dropdown_options,$ars_user['id'],"id='edit_ars_$row_id' class='edit_$row_id edit_input' style=''");?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Digital BoL
					</td>
					<td style="max-width:150px;" class="ellipsis" title="">
						<?php if(empty($load["bol_link"])):?>
							<?php if(!empty($load["digital_received_datetime"])):?>
								<?=date("m/d/y",strtotime($load["digital_received_datetime"]))?>
							<?php endif;?>
						<?php else:?>
							<a href="<?=base_url("/index.php/documents/download_file")."/".$load["bol_link"]?>" target="_blank"><?=date("m/d/y",strtotime($load["digital_received_datetime"]))?></a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Envelope Pic
					</td>
					<td style="max-width:150px;" class="ellipsis" title="">
						<?php if(empty($load["envelope_pic_guid"])):?>
							<?php if(!empty($load["envelope_pic_datetime"])):?>
								<?=date("m/d/y",strtotime($load["envelope_pic_datetime"]))?>
							<?php endif;?>
						<?php else:?>
							<a href="<?=base_url("/index.php/documents/download_file")."/".$load["envelope_pic_guid"]?>" target="_blank"><?=date("m/d/y",strtotime($load["envelope_pic_datetime"]))?></a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Dropbox Pic
					</td>
					<td style="max-width:150px;" class="ellipsis" title="">
						<?php if(empty($load["dropbox_pic_guid"])):?>
							<?php if(!empty($load["dropbox_pic_datetime"])):?>
								<?=date("m/d/y",strtotime($load["dropbox_pic_datetime"]))?>
							<?php endif;?>
						<?php else:?>
							<a href="<?=base_url("/index.php/documents/download_file")."/".$load["dropbox_pic_guid"]?>" target="_blank"><?=date("m/d/y",strtotime($load["dropbox_pic_datetime"]))?></a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						BOL Scan
					</td>
					<td style="max-width:150px;" class="ellipsis" title="">
						<?php if(empty($load["hc_guid"])):?>
							<?php if(!empty($load["hc_processed_datetime"])):?>
								<?=date("m/d/y",strtotime($load["hc_processed_datetime"]))?>
							<?php endif;?>
						<?php else:?>
							<a href="<?=base_url("/index.php/documents/download_file")."/".$load["hc_guid"]?>" target="_blank"><?=date("m/d/y",strtotime($load["hc_processed_datetime"]))?></a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						BOL Sent
					</td>
					<td>
						<?php if(empty($load["hc_sent_proof_guid"])):?>
							<?php if(!empty($load["hc_sent_datetime"])):?>
								<?=date("m/d/y",strtotime($load["hc_sent_datetime"]))?>
							<?php endif;?>
						<?php else:?>
							<a href="<?=base_url("/index.php/documents/download_file")."/".$load["hc_sent_proof_guid"]?>" target="_blank"><?=date("m/d/y",strtotime($load["hc_sent_datetime"]))?></a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						BOL Delivered
					</td>
					<td>
						<?php if(empty($load["hc_received_proof_guid"])):?>
							<?php if(!empty($load["hc_received_datetime"])):?>
								<?=date("m/d/y",strtotime($load["hc_received_datetime"]))?>
							<?php endif;?>
						<?php else:?>
							<a href="<?=base_url("/index.php/documents/download_file")."/".$load["hc_received_proof_guid"]?>" target="_blank"><?=date("m/d/y",strtotime($load["hc_received_datetime"]))?></a>
						<?php endif;?>
					</td>
				</tr>
			</table>
			<table class="load_details_table" style="float:left; margin-left:80px; width:250px;">
				<tr>
					<td style="width:100px;" class="field_name" title="Originals required for billing?">	
						Need HC?
					</td>
					<td style="max-width:150px;" class="ellipsis" title="">
						<?php if(empty($load["no_originals_proof_guid"])):?>
							<?=$load["originals_required"]?>
						<?php else:?>
							<a href="<?=base_url("/index.php/documents/download_file")."/".$load["no_originals_proof_guid"]?>" target="_blank"><?=$load["originals_required"]?></a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Lumper
					</td>
					<td style="max-width:150px;" class="ellipsis" title="">
						<?=$load["has_lumper"]?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Date Billed
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>">
						<?php if(!empty($load["billing_datetime"])):?>
							<?=date("m/d/y",strtotime($load["billing_datetime"]))?>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Amount Billed
					</td>
					<td>
						<?php if(!empty($load["amount_billed"])):?>
							<?=number_format($load["amount_billed"],2)?>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Invoice #
					</td>
					<td>
						<?=$load["invoice_number"]?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Date Funded
					</td>
					<td>
						<?php if(!empty($load["funded_datetime"])):?>
							<?=date("m/d/y",strtotime($load["funded_datetime"]))?>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Amount Funded
					</td>
					<td>
						<?php if(!empty($load["amount_funded"])):?>
							<?=number_format($load["amount_funded"],2)?>
						<?php endif;?>
					</td>
				</tr>
			</table>
			<table class="load_details_table" style="float:left; margin-left:80px; width:250px;">
				<tr>
					<td style="width:100px;" class="field_name">	
						Process Audit
					</td>
					<td>
						<span class="link" onclick="open_process_audit(<?=$load["id"]?>)"><?=$load["process_audit"]?></span>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Hold
					</td>
					<td>
						<?php if(!empty($load["denied_datetime"])):?>
							<?=date("m/d/y",strtotime($load["denied_datetime"]))?>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Hold Reason
					</td>
					<td>
						<?=$load["denied_reason"]?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Recoursed
					</td>
					<td style="max-width:150px;" class="ellipsis" title="">
						<?php if(!empty($load["recoursed_datetime"])):?>
							<?=date("m/d/y",strtotime($load["recoursed_datetime"]))?>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Reimbursed
					</td>
					<td style="max-width:150px;" class="ellipsis" title="">
						<?php if(!empty($load["reimbursed_datetime"])):?>
							<?=date("m/d/y",strtotime($load["reimbursed_datetime"]))?>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Exp. Pay
					</td>
					<td>
						<?php if(!empty($load["expected_pay_datetime"])):?>
							<span class="details_<?=$row_id?>"><?=date("m/d/y",strtotime($load["expected_pay_datetime"]))?></span>
							<input type="text" class="edit_<?=$row_id?> edit_input" id="expected_pay_date_<?=$row_id?>" name="expected_pay_date" value="<?=date("m/d/y",strtotime($load["expected_pay_datetime"]))?>"/>
						<?php else:?>
							<input type="text" class="edit_<?=$row_id?> edit_input" id="expected_pay_date_<?=$row_id?>" name="expected_pay_date" value="" placeholder="Date"/>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td style="width:100px;" class="field_name">	
						Closed
					</td>
					<td>
						<?php if(!empty($load["invoice_closed_datetime"])):?>
							<?=date("m/d/y",strtotime($load["invoice_closed_datetime"]))?>
						<?php endif;?>
					</td>
				</tr>
			</table>
		</div>
	</form>
	<div style="clear:both;"></div>
	<div class="heading">
		Load History
	</div>
	<hr style="">
	<div style="font-size:12px;">
		<table style="margin-left:0px; margin-top:5px; line-height:10px; font-size:10px;">
			<tr style="font-weight:bold;">
				<td style="width:110px;">
					Goalpoint<br>Type
				</td>
				<td style="width:80px;">
					Complete
				</td>
				<td style="width:70px;">
					Deadline
				</td>
				<td style="width:80px; padding-right:10px;">
					Driver
				</td>
				<td style="width:80px;">
					Truck
				</td>
				<td style="width:80px;">
					Trailer
				</td>
				<td style="width:70px;">
					GPS
				</td>
				<td style="width:105px; padding-right:5px;">
					Location
				</td>
				<td style="width:200px;; padding-right:5px;">
					Notes
				</td>
				<td style="width:30px;">
				</td>
			</tr>
		</table>
		<?php
			$i = 0;
		?>
		<?php if(!empty($goalpoints)):?>
			<table style="margin-left:0px; margin-top:5px; margin-bottom:10px; line-height:10px; font-size:10px;">
			<?php foreach($goalpoints as $goalpoint):?>
				<?php include("billing_goalpoint_row.php"); ?>
			<?php endforeach;?>
			</table>
		<?php endif;?>
	</div>
	<div style="clear:both;"></div>
	<div id="truck_attachments" style="margin-top:20px;">
		<span class="heading">Attachments</span>
		<hr>
		<?php if(!empty($attachments)): ?>
			<?php foreach($attachments as $attachment): ?>
				<div class="attachment_box" style="float:left;margin:5px;margin-bottom:20px;">
					<a target="_blank" title="Download <?=$attachment['attachment_name']?>" style="text-decoration:none;color:#4e77c9;" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>"><?=$attachment['attachment_name']?></a>
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
	$('#expected_pay_date_<?=$load["id"]?>').datepicker({ showAnim: 'blind' });
</script>