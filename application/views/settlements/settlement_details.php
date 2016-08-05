<style>	
	.edit_<?=$settlement["id"]?>
	{
		display:none;
	}
</style>

<?php
	$settled_date = "";
	if(!empty($settlement["approved_datetime"]))
	{
		$settled_date = date("m/d/y",strtotime($settlement["approved_datetime"]));
	}
	
	$kick_in_text = "";
	if(!is_null($settlement["kick_in"]))
	{
		$kick_in_text = number_format($settlement["kick_in"],2);
	}
	
	$driver_id = $settlement["client_id"];
	$log_entry_id = $settlement["end_week_id"];
?>

<script>
	//OPENS EDIT VIEW
	function edit_details(settlement_id)
	{
		$('.edit_'+settlement_id).css({"display":"block"});
		$('.details_'+settlement_id).css({"display":"none"});
	}
	
	//UPDATES KICK IN TO REFLECT NEW AMOUNT
	function target_pay_changed(new_target,settlement_id)
	{
		var money_earned = <?=round($stats["statement_amount"],2)?>;
		var new_kick_in = Math.round((new_target - money_earned)*100)/100;
	
		$("#kick_in_"+settlement_id).val(new_kick_in);
	}
	
	//UPDATES KICK IN TO REFLECT NEW AMOUNT
	function kick_in_changed(new_kick_in,settlement_id)
	{
		
		new_kick_in = parseFloat(new_kick_in);//makes sure that new_kick_in is treated as a number not a string
		
		var money_earned = <?=round($stats["statement_amount"],2)?>;
		if(isNaN(new_kick_in))
		{
			$("#target_pay_"+settlement_id).val(money_earned);
		}
		else
		{
			var new_target = Math.round((new_kick_in + money_earned)*100)/100;
			$("#target_pay_"+settlement_id).val(new_target);
		}
	}
	
	
	//}
</script>

<div style="min-height:95px;">
	<div style="width:20px; float:right;">
		<?php if(empty($settlement["approved_datetime"])): ?>
			<img id="refresh_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:3px;" src="/images/refresh.png" title="Refresh" onclick="this.src='/images/loading.gif'; open_row_details('<?=$settlement["id"]?>')"/>
			<?php if(user_has_permission("approve settlements")):?>
				<?php if($stats["is_ready_to_approve"] == false): ?>
					<img id="approve_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/approve_commission.png" title="Approve" onclick="alert('There are unsettled receipts!')"/>
				<?php elseif($settlement["kick_in"] === null || empty($settlement["html"]) || empty($settlement["fm_id"])): ?>
					<img id="approve_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/approve_commission.png" title="Approve" onclick="alert('Forgetting something? Make sure Kick In, Settlement Link, and Fleet Manager are all there!!')"/>
				<?php else:?>
					<img id="approve_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/approve_commission.png" title="Approve" onclick="approve_settlement('<?=$settlement["id"]?>')"/>
				<?php endif;?>
			<?php endif;?>
			<img id="edit_icon" class="details_<?=$settlement["id"]?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:3px;" src="/images/edit.png" title="Edit" onclick="edit_details('<?=$settlement["id"]?>')"/>
			<img id="save_icon_<?=$settlement["id"]?>" class="edit_<?=$settlement["id"]?>" style="display:none; margin-bottom:13px; margin-right:15px; cursor:pointer; height:14px; position:relative; left:3px;" src="/images/save.png" title="Save" onclick="this.src='/images/loading.gif';save_settlement('<?=$settlement["id"]?>');"/>
		<?php else: ?>
			<img id="unlock_icon_<?=$settlement["id"]?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/unlocked.png" title="Unlock" onclick="unlock_settlement('<?=$settlement["id"]?>')"/>
			<?php if(empty($settlement["settled_datetime"])): ?>
				<img id="settle_icon_<?=$settlement["id"]?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; width:35px; position:relative; right:11px; top:-10px;" src="/images/dollar_sign.png" title="Settle" onclick="settle_settlement('<?=$settlement["id"]?>')"/>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div style="font-size:12px;">
		<div id="two_tables" style="width:900px; overflow:hidden;">
			<div id="settlement_details_box" style="float:left; width:440px;">
				<div style="margin-left:20px; margin-top:15px;" class="heading">
					Statement Details<br>
					<hr style="width:400px;">
				</div>
				<form id="settlement_details_form_<?=$settlement["id"]?>" name="settlement_details_form_<?=$settlement["id"]?>" onsubmit="save_settlement('<?=$settlement["id"]?>'); return false;">
					<input type="hidden" id="settlement_id" name="settlement_id" value="<?=$settlement["id"]?>">
					<table style="margin-left:20px;">
						<tr>
							<td style="width:150px; font-weight:bold;">
								Earned
							</td>
							<td style="width:100px;" title="<?=number_format($stats["total_client_expenses"],2)?>">
								<?=number_format($stats["statement_amount"],2)?> 
								<input type="hidden" id="profit_share" name="profit_share" value="<?=round($stats["statement_amount"],2)?>" />
							</td>
						</tr>
						<tr>
							<td style="font-weight:bold;">
								Kick In
							</td>
							<td style="">
								<span class="details_<?=$settlement["id"]?>" id="kick_in_text_<?=$settlement["id"]?>" name="kick_in_<?=$settlement["id"]?>"><?=$kick_in_text?></span>
								<input type="text" id="kick_in_<?=$settlement["id"]?>" name="kick_in" class="edit_<?=$settlement["id"]?>" style="width:150px; position:relative; left:2px; bottom:7px; font-size:12px; font-family:arial;" value="<?=$kick_in_text?>" onchange="kick_in_changed(this.value,'<?=$settlement["id"]?>')"/>
							</td>
						</tr>
						<tr>
							<td style="font-weight:bold;">
								Target Pay
							</td>
							<td style="">
								<span class="details_<?=$settlement["id"]?>"><?=number_format($settlement["target_pay"],2)?></span>
								<input type="text" id="target_pay_<?=$settlement["id"]?>" name="target_pay" class="edit_<?=$settlement["id"]?>" style="width:150px; position:relative; left:2px; bottom:7px; font-size:12px; font-family:arial;" value="<?=round($settlement["target_pay"],2)?>" onchange="target_pay_changed(this.value,'<?=$settlement["id"]?>')"/>
							</td>
						</tr>
						<tr>
							<td style="font-weight:bold;">
								Settled
							</td>
							<td style="">
								<?=$settled_date?>
							</td>
						</tr>
						<tr>
							<td style="font-weight:bold;">
								Fleet Manager
							</td>
							<td style="">
								<span class="details_<?=$settlement["id"]?>"><?=$settlement["fleet_manager"]["f_name"]." ".$settlement["fleet_manager"]["l_name"]?></span>
								<span class="edit_<?=$settlement["id"]?>"><?php echo form_dropdown('fm_dropdown',$fleet_manager_dropdown_options,$settlement["fm_id"],'id="fm_dropdown" style="width:150px; font-size:12px; position:relative; bottom:5px;"');?></span>
							</td>
						</tr>
						<tr>
							<td style="font-weight:bold;">
								Approved By
							</td>
							<td style="">
								<?=$settlement["approved_by_person"]["f_name"]?>
							</td>
						</tr>
						<tr>
							<td style="font-weight:bold;">
								Statement Link
							</td>
							<td>
								<?php if(empty($settlement["approved_datetime"])):?>
								<a target="_blank" href="<?=base_url("index.php/settlements/load_driver_settlement_view/$log_entry_id/$driver_id")?>">Statement</a>
								<?php else:?>
									<a target="_blank" href="<?=base_url("index.php/settlements/display_db_settlement/$settlement_id")?>">Statement</a>
								<?php endif;?>
							</td>
						</tr>
						<tr>
							<td style="font-weight:bold;">
								Notes to Driver
							</td>
							<td style="height:55px;">
								<span class="details_<?=$settlement["id"]?>" style="width:250px; display:block; line-height:15px;"><?=$settlement["notes_to_driver"]?></span>
								<textarea id="notes_to_driver" name="notes_to_driver" class="edit_<?=$settlement["id"]?>" style="position:relative; right:3px; bottom:5px; font-family:arial; font-size:12px; width:240px;" rows="3"><?=$settlement["notes_to_driver"]?></textarea>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div id="credit_box" style="float:right; width:440px;">
				<div style="margin-left:20px; margin-top:15px;" class="heading">
					<span>Credits</span>
					<br>
					<hr style="width:400px;">
				</div>
				<table style="margin-left:20px;">
					<?php if(!empty($statement_credits)): ?>
						<?php foreach($statement_credits as $statement_credit): ?>
							<?php
								//GET ACCOUNT
								$where = null;
								$where["id"] = $statement_credit["debited_account_id"];
								$vendor_credit_account = db_select_account($where);
								
								if($vendor_credit_account["relationship_id"] == 0)
								{
									//GET COMPANY
									$where = null;
									$where["id"] = $vendor_credit_account["company_id"];
									$vendor_credits_company = db_select_company($where);
								}
								else
								{
									
									//GET RELATIONSHIP
									$where = null;
									$where["id"] = $vendor_credit_account["relationship_id"];
									$vendor_credit_relationship = db_select_business_relationship($where);
									
									//GET COMPANY
									$where = null;
									$where["id"] = $vendor_credit_relationship["related_business_id"];
									$vendor_credits_company = db_select_company($where);
								}
							?>
							<tr>
								<td style="width:100px; padding-bottom:15px;">
									<?= $vendor_credits_company["company_side_bar_name"]?>
								</td>
								<td style="width:200px; padding-bottom:15px;">
									<?= $statement_credit["credit_description"]?>
								</td>
								<td style="width:60px; text-align:right; padding-bottom:15px;">
									<?= number_format($statement_credit["credit_amount"],2)?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
						<tr>
							<td>
								There are no credits for this driver this week
							</td>
						</tr>
					<?php endif; ?>
				</table>
			</div>
			<?php if(empty($settlement["approved_datetime"])):?>
				<div style="float:right; width:440px;">
					<form id="add_credit_form_<?=$settlement["id"]?>" name="add_credit_form">
						<input type="hidden" id="settlement_id" name="settlement_id" value="<?=$settlement["id"]?>"/>
						<table style="margin-left:20px;">
							<tr>
								<td style="width:100px; padding-bottom:15px;">
									<?php echo form_dropdown('invoiced_company_dd',$business_users_options,"Select",'id="invoiced_company_dd_'.$settlement["id"].'" style="width:90px; font-size:12px;"');?>
								</td>
								<td style="width:200px; padding-bottom:15px;">
									<input type="text" id="credit_description_<?=$settlement["id"]?>" name="credit_description" style="width:190px;" />
								</td>
								<td style="width:60px; text-align:right; padding-bottom:15px;">
									<input type="text" id="credit_amount_<?=$settlement["id"]?>" name="credit_amount"  style="width:50px; text-align:right;" />
								</td>
								<td>
									<img id="approve_icon" style="display:block; cursor:pointer; height:25px; position:relative; left:15px; bottom:4px;" src="/images/add_circle.png" title="Add Credit" onclick="add_statement_credit('<?=$settlement["id"]?>')"/>
								</td>
							</tr>
						</table>
					</form>
				</div>
			<?php endif;?>
		</div>
	</div>
</div>
<div title="Attachment Upload" id="file_upload_dialog" name="file_upload_dialog" style="display:hidden;" >
	<!-- AJAX GOES HERE !-->
</div>