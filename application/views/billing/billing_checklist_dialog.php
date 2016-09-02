<script>
	$('.datepicker').datepicker({ showAnim: 'blind' });
</script>
<style>
	.checklist_input
	{
		width:150px;
	}
</style>
<div id="checklist_div" style="float:left; width:210px; border-right: 1px solid #DDD; padding:10px;">
	<span class="heading">Checklist</span>
	<hr/>
	<br>
	<?php
		//PREPARE CHECKLIST DATES
		$digital_is_checked = "checked disabled";
		$digital_list_style = "color:#DDD;";
		if(empty($load["digital_received_datetime"]))
		{
			$digital_is_checked = "";
			$digital_list_style = "color: black;";
		}
		
		$process_audit_is_checked = "checked disabled";
		$process_audit_list_style = "color:#DDD;";
		if(empty($load["process_audit"]))
		{
			$process_audit_is_checked = "";
			$process_audit_list_style = "color: black;";
		}
		
		$envelope_is_checked = "checked disabled";
		$envelope_list_style = "color:#DDD;";
		if(empty($load["envelope_pic_datetime"]))
		{
			$envelope_is_checked = "";
			$envelope_list_style = "color: black;";
		}
		
		$dropbox_is_checked = "checked disabled";
		$dropbox_list_style = "color:#DDD;";
		if(empty($load["dropbox_pic_datetime"]))
		{
			$dropbox_is_checked = "";
			$dropbox_list_style = "color: black;";
		}
		
		$billing_is_checked = "checked disabled";
		$billing_list_style = "color:#DDD;";
		if(empty($load["billing_datetime"]))
		{
			$billing_is_checked = "";
			$billing_list_style = "color: black;";
		}
		
		$funded_is_checked = "checked disabled";
		$funded_list_style = "color:#DDD;";
		if(empty($load["amount_funded"]))
		{
			$funded_is_checked = "";
			$funded_list_style = "color: black;";
		}
		
		$hc_processed_is_checked = "checked disabled";
		$hc_processed_list_style = "color:#DDD;";
		if(empty($load["hc_processed_datetime"]))
		{
			$hc_processed_is_checked = "";
			$hc_processed_list_style = "color: black;";
		}
		
		//HC SENT
		if($load["originals_required"] == "No")
		{
			$hc_sent_is_checked = "disabled";
			$hc_sent_list_style = "color:#DDD;";
		}
		else
		{
			$hc_sent_is_checked = "checked disabled";
			$hc_sent_list_style = "color:#DDD;";
			if(empty($load["hc_sent_datetime"]))
			{
				$hc_sent_is_checked = "";
				$hc_sent_list_style = "color: black;";
			}
		}
		
		//HC DELIVERED
		if($load["originals_required"] == "No")
		{
			$hc_received_is_checked = "disabled";
			$hc_received_list_style = "color:#DDD;";
		}
		else
		{
			if(empty($load["hc_sent_datetime"]))
			{
				$hc_received_is_checked = "disabled";
				$hc_received_list_style = "color:#DDD;";
			}
			else
			{
				$hc_received_is_checked = "checked disabled";
				$hc_received_list_style = "color:#DDD;";
				if(empty($load["hc_received_datetime"]))
				{
					$hc_received_is_checked = "";
					$hc_received_list_style = "color: black;";
				}
			}
		}
		
		
		//HOLD CHECKBOX IS ONLY GRAYED OUT WHEN LOAD CLOSES
		$hold_is_checked = "checked disabled";
		$hold_list_style = "color:#DDD;";
		if(empty($load["invoice_closed_datetime"]))
		{
			$hold_is_checked = "";
			$hold_list_style = "color: black;";
		}
		
		if(empty($load["amount_funded"]))
		{
			$recoursed_is_checked = "disabled";
			$recoursed_list_style = "color:#DDD;";
		}
		else
		{
			if(empty($load["recoursed_datetime"]))
			{
				$recoursed_is_checked = "";
				$recoursed_list_style = "color: black;";
			}
			else
			{
				$recoursed_list_style = "color:#DDD;";
				$recoursed_is_checked = "checked disabled";
			}
			
		}
		
		//REIMBURSED
		if(empty($load["recoursed_datetime"]))
		{
			$reimbursed_list_style = "color:#DDD;";
			$reimbursed_is_checked = "disabled";
		}
		else
		{
			if(empty($load["reimbursed_datetime"]))
			{
				$reimbursed_is_checked = "";
				$reimbursed_list_style = "color: black;";
			}
			else
			{
				$reimbursed_list_style = "color:#DDD;";
				$reimbursed_is_checked = "checked disabled";
			}
		}
		
		//CLOSED
		if(empty($load["funded_datetime"]) || (!empty($load["recoursed_datetime"]) && empty($load["reimbursed_datetime"])))//DISPABLE NOT COMPLETE
		{
			$invoice_closed_list_style = "color:#DDD;";
			$invoice_closed_is_checked = "disabled";
		}
		else
		{
			if(empty($load["invoice_closed_datetime"]))//ENABLE
			{
				if(user_has_permission('close out load in billing'))
				{
					$invoice_closed_is_checked = "";
					$invoice_closed_list_style = "color: black;";
				}
				else
				{
					$invoice_closed_list_style = "color:#DDD;";
					$invoice_closed_is_checked = "disabled";
				}
			}
			else //MARKED COMPLETE
			{
				$invoice_closed_list_style = "color:#DDD;";
				$invoice_closed_is_checked = "checked disabled";
			}
		}
		
		
		//FORMAT TEXT FOR ORIGIN AND DESTINATION
		$pick_text = "";
		$these_picks = $load['load_picks'];
		sort($these_picks);
		foreach($these_picks as $pick)
		{
			$pick_text = $pick['stop']["city"].", ".$pick['stop']["state"]." ".date("n/j/y",strtotime($pick["in_time"]));
			break;
		}
		
		$drop_text = "";
		$these_drops = $load['load_drops'];
		sort($these_drops);
		foreach($these_drops as $drop)
		{
			$drop_text = $drop['stop']["city"].", ".$drop['stop']["state"]." ".date("n/j/y",strtotime($drop["in_time"]));
			break;
		}
		
		//MAKE RC LINK
		//$rc_link = "RC: <input type='file' id='rc_file' name='rc_file' />";
		$rc_link = '<a target="_blank" href="'.base_url("/index.php/documents/download_file")."/".$load["rc_link"].'">RC</a>';
	
		//MAKE BOL LINK
		$bol_link = "";
		if(!empty($load["bol_link"]))
		{
			//$bol_link = "<a href='".$load['bol_link']."' target='_blank'>BOL</a> ";
			$bol_link = '<a target="_blank" href="'.base_url("/index.php/documents/download_file")."/".$load["bol_link"].'">BOL</a>';
		}
		
		//PREPARE INVOICE NUMBER IF NOT FACTORED BY INSIGHT
		$invoice_number = "";
		if($load["billing_method"] != "Factor")
		{
			$invoice_number = $load["internal_load_number"];
		}
		
		//PREPARE HC_RECEIVED LABEL TEXT
		$hc_received_label = "HC Received by Insight";
		if($load["billing_method"] != "Factor")
		{
			$hc_received_label = "HC Received by Customer";
		}
		
		//CREATE TEXT FOR BILLIABLE EXPENSES ROW
		$where = null;
		$where["load_id"] = $load["id"];
		$where["is_billable"] = "Yes";
		$billable_load_expenses = db_select_load_expenses($where);
		$expenses_text = null;
		foreach($billable_load_expenses as $expense)
		{
			$link_text = "$".$expense["expense_amount"]." ".$expense["explanation"];
			$link = $expense["link"];
			if(!empty($link))
			{
				$text = "<a href=\"$link\" target=\"_blank\">$link_text</a> ";
			}
			else
			{
				$text = $link_text;
			}
			
			$expenses_text = $expenses_text.$text;
		
		}
	?>
	<table>
		<input type="text" id="focus_stealer" style="display:none;"/>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="digital_cb_<?=$load["id"]?>" value="digital_cb_<?=$load["id"]?>" <?=$digital_is_checked?> onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$digital_list_style?>">
				Digital Received
			</td>
		</tr>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="process_audit_cb_<?=$load["id"]?>" value="process_audit_cb_<?=$load["id"]?>" <?=$process_audit_is_checked?> onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$process_audit_list_style?>">
				Process Audit
			</td>
		</tr>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="billed_cb_<?=$load["id"]?>" <?=$billing_is_checked?>  onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$billing_list_style?>">
				Billed
			</td>
		</tr>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="funded_cb_<?=$load["id"]?>" <?=$funded_is_checked?>  onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$funded_list_style?>">
				Funded
			</td>
		</tr>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="envelope_cb_<?=$load["id"]?>" value="envelope_cb_<?=$load["id"]?>" <?=$envelope_is_checked?> onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$envelope_list_style?>">
				Envelope Pic
			</td>
		</tr>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="dropbox_cb_<?=$load["id"]?>" value="dropbox_cb_<?=$load["id"]?>" <?=$dropbox_is_checked?> onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$dropbox_list_style?>">
				Dropbox Pic
			</td>
		</tr>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="hc_processed_cb_<?=$load["id"]?>" <?=$hc_processed_is_checked?>  onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$hc_processed_list_style?>">
				BOL Scanned
			</td>
		</tr>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="hc_sent_cb_<?=$load["id"]?>" <?=$hc_sent_is_checked?>  onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$hc_sent_list_style?>">
				BOL Sent
			</td>
		</tr>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="hc_received_cb_<?=$load["id"]?>" <?=$hc_received_is_checked?>  onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$hc_received_list_style?>">
				BOL Delivered
			</td>
		</tr>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="hold_cb_<?=$load["id"]?>" <?=$hold_is_checked?>  onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$hold_list_style?>">
				Hold
			</td>
		</tr>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="recoursed_cb_<?=$load["id"]?>" <?=$recoursed_is_checked?>  onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$recoursed_list_style?>">
				Recoursed
			</td>
		</tr>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="reimbursed_cb_<?=$load["id"]?>" <?=$reimbursed_is_checked?>  onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$reimbursed_list_style?>">
				Reimbursed
			</td>
		</tr>
		<tr>
			<td style="width:40px;">
				<input type="checkbox" id="invoice_closed_cb_<?=$load["id"]?>" <?=$invoice_closed_is_checked?>  onclick="checklist_clicked(this,'<?=$load["id"]?>')" />
			</td>
			<td style="line-height:20px;<?=$invoice_closed_list_style?>">
				Closed
			</td>
		</tr>
	</table>
</div>
<form id="billing_checklist_form"  enctype="multipart/form-data">
	<input type="hidden" id="action_<?=$load["id"]?>" name="action_<?=$load["id"]?>"/>
	<input type="hidden" name="billing_method" id="billing_method" value="<?=$load["billing_method"]?>" >
	<input type="hidden" name="fleet_manager_id" id="fleet_manager_id" value="<?=$load["fleet_manager_id"]?>" >
	<input type="hidden" name="client_id" id="client_id" value="<?=$load["client_id"]?>" >
	<input type="hidden" name="load_id" id="load_id" value="<?=$load["id"]?>" >
	<div id="input_div" style="float:left; width:400px; height:290px; padding:10px;">
		<div id="digital_input_div" style="margin-left:20px; display:none;">
			<span class="heading">Digital Received</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:150px; line-height:23px;">
						Date of BOL Pic
					</td>
					<td>
						<input class="datepicker checklist_input" type="text" id="digital_received_date_<?=$load["id"]?>" name="digital_received_date_<?=$load["id"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						BOL Pic
					</td>
					<td>
						<input class="checklist_input" type="file" id="dc_file_<?=$load["id"]?>" name="dc_file_<?=$load["id"]?>" class="" />
					</td>
				</tr>
				<tr></tr>
			</table>
			<br>
			<br>
		</div>
		<div id="process_audit_input_div" style="margin-left:20px; display:none;">
			<span class="heading">Process Audit</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:150px; line-height:23px;">
						Defer to Tarriff 
					</td>
					<td>
						<?php $options = array(
							'Select' => 'Select',
							'Pass'  => 'Pass',
							'Fail'    => 'Fail',
							); 
						?>
						<?php echo form_dropdown('defer_to_tarriff',$options,"Select",'id="defer_to_tarriff" style="" class="checklist_input" onchange=""');?><span style="margin-left:10px;" title='Was a clause added to the rate con saying to defer to the carrier tarriff?' class="link" onclick="alert(this.title)">?<span>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Ontime According to RC 
					</td>
					<td>
						<?php $options = array(
							'Select' => 'Select',
							'Pass'  => 'Pass',
							'Fail'    => 'Fail',
							); 
						?>
						<?php echo form_dropdown('ontime_by_rc',$options,"Select",'id="ontime_by_rc" style="" class="checklist_input" onchange=""');?><span style="margin-left:10px;" title='Was the load delivered ontime according the latest rate con?' class="link" onclick="alert(this.title)">?<span>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Shipper Load and Count
					</td>
					<td>
						<?php $options = array(
							'Select' => 'Select',
							'Pass'  => 'Pass',
							'Fail'    => 'Fail',
							); 
						?>
						<?php echo form_dropdown('shipper_load_and_count',$options,"Select",'id="shipper_load_and_count" style="" class="checklist_input" onchange=""');?><span style="margin-left:10px;" title='Did the driver write Shipper Load, Count, and Secured on the bill of lading?' class="link" onclick="alert(this.title)">?<span>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Seal Pic (Departure)
					</td>
					<td>
						<?php $options = array(
							'Select' => 'Select',
							'Pass'  => 'Pass',
							'Fail'    => 'Fail',
							); 
						?>
						<?php echo form_dropdown('seal_pic_depart',$options,"Select",'id="seal_pic_depart" style="" class="checklist_input" onchange=""');?><span style="margin-left:10px;" title='Did driver send in a picture of the seal at the time of departure?' class="link" onclick="alert(this.title)">?<span>
					</td>
					<tr>
					<td style="width:150px; line-height:23px;">
						Load Pic (Departure)
					</td>
					<td>
						<?php $options = array(
							'Select' => 'Select',
							'Pass'  => 'Pass',
							'Fail'    => 'Fail',
							); 
						?>
						<?php echo form_dropdown('load_pic_depart',$options,"Select",'id="load_pic_depart" style="" class="checklist_input" onchange=""');?><span style="margin-left:10px;" title='Did driver send in a picture of the load at the time of departure from pick?' class="link" onclick="alert(this.title)">?<span>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Seal Number on Bills
					</td>
					<td>
						<?php $options = array(
							'Select' => 'Select',
							'Pass'  => 'Pass',
							'Fail'    => 'Fail',
							); 
						?>
						<?php echo form_dropdown('seal_number',$options,"Select",'id="seal_number" style="" class="checklist_input" onchange=""');?><span style="margin-left:10px;" title='Did driver write the seal number on bill of lading?' class="link" onclick="alert(this.title)">?<span>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Seal Pic (Arrival)
					</td>
					<td>
						<?php $options = array(
							'Select' => 'Select',
							'Pass'  => 'Pass',
							'Fail'    => 'Fail',
							); 
						?>
						<?php echo form_dropdown('seal_pic_arrive',$options,"Select",'id="seal_pic_arrive" style="" class="checklist_input" onchange=""');?><span style="margin-left:10px;" title='Did driver send in a picture of the seal at the time of arrival to drop?' class="link" onclick="alert(this.title)">?<span>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Load Pic (Arrival)
					</td>
					<td>
						<?php $options = array(
							'Select' => 'Select',
							'Pass'  => 'Pass',
							'Fail'    => 'Fail',
							); 
						?>
						<?php echo form_dropdown('load_pic_arrive',$options,"Select",'id="load_pic_arrive" style="" class="checklist_input" onchange=""');?><span style="margin-left:10px;" title='Did driver send in a picture of the load at the time of arrival to drop?' class="link" onclick="alert(this.title)">?<span>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Seal Intact on Bills
					</td>
					<td>
						<?php $options = array(
							'Select' => 'Select',
							'Pass'  => 'Pass',
							'Fail'    => 'Fail',
							); 
						?>
						<?php echo form_dropdown('seal_intact',$options,"Select",'id="seal_intact" style="" class="checklist_input" onchange=""');?><span style="margin-left:10px;" title='Did the driver write Seal Intact in the bill of lading?' class="link" onclick="alert(this.title)">?<span>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Clean Bills
					</td>
					<td>
						<?php $options = array(
							'Select' => 'Select',
							'Pass'  => 'Pass',
							'Fail'    => 'Fail',
							); 
						?>
						<?php echo form_dropdown('clean_bills',$options,"Select",'id="clean_bills" style="" class="checklist_input" onchange=""');?><span style="margin-left:10px;" title='Did driver produce clean bills (ie. no damages, shortages, other problems)?' class="link" onclick="alert(this.title)">?<span>
					</td>
				</tr>
				</tr>
			</table>
			<br>
			<br>
		</div>
		<div id="billed_input_div" style="margin-left:20px; display:none;">
			<span class="heading">Invoice Billed</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:130px; line-height:23px;">
						Date Billed
					</td>
					<td>
						<input class="checklist_input datepicker" type="text" id="invoice_billed_date_<?=$load["id"]?>" name="invoice_billed_date_<?=$load["id"]?>"/>
					</td>
					<td style="vertical-align:middle; color:red;">
						*
					</td>
				</tr>
				<tr>
					<td style="line-height:23px;">
						Amount Billed
					</td>
					<td>
						<input class="checklist_input" type="text" id="amount_billed_<?=$load["id"]?>" name="amount_billed_<?=$load["id"]?>"/>
					</td>
					<td style="vertical-align:middle; color:red;">
						*
					</td>
				</tr>
				<tr style="height:20px;"></tr>
				<?php if($load["billing_method"] == "Factor"): ?>
					<tr style="height:20px;">
						<td>
							FL Username:
						</td>
						<td>
							<?=$load["billed_under_carrier"]["fl_username"]?>
						</td>
					</tr>
					<tr style="height:20px;">
						<td>
							FL Password:
						</td>
						<td>
							<?=$load["billed_under_carrier"]["fl_password"]?>
						</td>
					</tr>
				<?php endif; ?>
				
				<tr style="height:20px;">
					<td>
						<?=$load["broker"]["customer_name"]." MC#"?>
					</td>
					<td>
						<?=$load["broker"]["mc_number"]?>
					</td>
				</tr>
				<tr style="height:20px;">
					<td>
						Pick:
					</td>
					<td>
						<?=$pick_text?>
					</td>
					<td>
						<?=$rc_link?>
					</td>
				</tr>
				<tr style="height:20px;">
					<td>
						Drop:
					</td>
					<td>
						<?=$drop_text?>
					</td><td>
						<?=$bol_link?>
					</td>
				</tr>
				<tr style="height:20px;">
					<td>
						Billiable Expenses:
					</td>
					<td>
						<?=$expenses_text?>
					</td>
				</tr>
				<tr style="height:20px;">
					<td>
						Gross Pay:
					</td>
					<td>
						<?="$".number_format($load["amount_to_bill"],2)?>
					</td>
				</tr>
			</table>
			<br>
			<span style="font-weight:bold;">Email Subject Line:</span>
			<br>
			<br>
			<?=$load["billed_under_carrier"]["company_name"]." | ".$load["broker"]["customer_name"]." load# ".$load["customer_load_number"]?>
		</div>
		<div id="funded_input_div" style="margin-left:20px; display:none;">
			<span class="heading">Invoice Funded</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:150px; line-height:23px;">
						Amount Funded
					</td>
					<td>
						<input class="checklist_input" type="text" id="amount_funded_<?=$load["id"]?>" name="amount_funded_<?=$load["id"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Invoice #
					</td>
					<td>
						<input class="checklist_input" type="text" id="invoice_number_<?=$load["id"]?>" name="invoice_number_<?=$load["id"]?>" value="<?=$invoice_number?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Finance Cost
					</td>
					<td>
						<input class="checklist_input" type="text" id="finance_cost_<?=$load["id"]?>" name="finance_cost_<?=$load["id"]?>"/>
					</td>
				</tr>
				<tr></tr>
				<tr style="height:20px;">
					<td>
						Amount Billed:
					</td>
					<td>
						$<?= number_format($load["amount_billed"],2)?>
						<input class="checklist_input" type="hidden" id="amount_billed_hidden_<?=$load["id"]?>" name="amount_billed_hidden_<?=$load["id"]?>" value="<?=round($load["amount_billed"],2)?>"/>
					</td>
				</tr>
				<?php if($load["billing_method"] == "Factor"): ?>
					<tr style="height:20px;">
						<td>
							FL Username:
						</td>
						<td>
							<?=$load["billed_under_carrier"]["fl_username"]." (".$load["billed_under_carrier"]["company_side_bar_name"].")"?>
						</td>
					</tr>
					<tr style="height:20px;">
						<td>
							FL Password:
						</td>
						<td>
							<?=$load["billed_under_carrier"]["fl_password"]?>
						</td>
					</tr>
				<?php endif; ?>
			</table>
		</div>
		<div id="envelope_input_div" style="margin-left:20px; display:none;">
			<span class="heading">Envelope Pic</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:150px; line-height:23px;">
						Date of Envelope Pic
					</td>
					<td>
						<input class="checklist_input datepicker" type="text" id="envelope_pic_date_<?=$load["id"]?>" name="envelope_pic_date_<?=$load["id"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Envelope Pic
					</td>
					<td>
						<input class="checklist_input" type="file" id="envelope_file_<?=$load["id"]?>" name="envelope_file_<?=$load["id"]?>" class="" />
					</td>
				</tr>
				<tr></tr>
			</table>
			<br>
			<br>
		</div>
		<div id="dropbox_input_div" style="margin-left:20px; display:none;">
			<span class="heading">Dropbox Pic</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:150px; line-height:23px;">
						Date of Dropbox Pic
					</td>
					<td>
						<input class="checklist_input datepicker" type="text" id="dropbox_date_<?=$load["id"]?>" name="dropbox_date_<?=$load["id"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Dropbox Pic
					</td>
					<td>
						<input class="checklist_input" type="file" id="dropbox_file_<?=$load["id"]?>" name="dropbox_file_<?=$load["id"]?>" class="" />
					</td>
				</tr>
				<tr></tr>
			</table>
			<br>
			<br>
		</div>
		<div id="hc_processed_input_div" style="margin-left:20px; display:none;">
			<span class="heading">HC Processed</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:150px; line-height:23px;">
						BOL Scan
					</td>
					<td>
						<input class="checklist_input" type="file" id="hc_file_<?=$load["id"]?>" name="hc_file_<?=$load["id"]?>" class="" />
					</td>
				</tr>
				<tr style="height:30px;"></tr>
			</table>
			<br>
			<br>
		</div>
		<div id="hc_sent_input_div" style="margin-left:20px; display:none;">
			<span class="heading">HC Sent</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:150px; line-height:23px;">
						Date BOL Sent
					</td>
					<td>
						<input class="checklist_input datepicker" type="text" id="hc_sent_date_<?=$load["id"]?>" name="hc_sent_date_<?=$load["id"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Proof of Request
					</td>
					<td>
						<input class="checklist_input" type="file" id="hc_sent_proof_<?=$load["id"]?>" name="hc_sent_proof_<?=$load["id"]?>" class="" />
					</td>
				</tr>
				<tr style="height:30px;"></tr>
				<?php if($load["billing_method"] == "Factor"): ?>
					<tr>
						<td style="height:20px; width:150px; line-height:23px;">
							Address
						</td>
						<td style=" line-height:23px;">
							FactorLoads <br>
							820 S. 300 W. <br>
							Heber City, UT 84032 <br>
						</td>
					</tr>
				<?php endif; ?>
			</table>
		</div>
		<div id="hc_received_input_div" style="margin-left:20px; display:none;">
			<span class="heading">BOL Received</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:150px; line-height:23px;">
						Date BOL Delivered
					</td>
					<td>
						<input class="checklist_input datepicker" type="text" id="hc_received_date_<?=$load["id"]?>" name="hc_received_date_<?=$load["id"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Proof of BOL Delivery
					</td>
					<td>
						<input class="checklist_input" type="file" id="hc_received_proof_<?=$load["id"]?>" name="hc_received_proof_<?=$load["id"]?>" class="" />
					</td>
				</tr>
				<tr></tr>
				<?php if($load["billing_method"] == "Factor"): ?>
					<tr style="height:20px;">
						<td>
							FL Username:
						</td>
						<td>
							<?=$load["billed_under_carrier"]["fl_username"]." (".$load["billed_under_carrier"]["company_side_bar_name"].")"?>
						</td>
					</tr>
					<tr style="height:20px;">
						<td>
							FL Password:
						</td>
						<td>
							<?=$load["billed_under_carrier"]["fl_password"]?>
						</td>
					</tr>
				<?php endif; ?>
			</table>
		</div>
		<div id="hold_input_div" style="margin-left:20px; display:none;">
			<span class="heading">Invoice on Hold</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:150px; line-height:23px;">
						Date of Hold
					</td>
					<td>
						<input class="checklist_input datepicker" type="text" id="hold_date_<?=$load["id"]?>" name="hold_date_<?=$load["id"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Hold Reason
					</td>
					<td>
						<?php $options = array(
							'Select' => 'Select',
							'Late Fees'  => 'Late Fees',
							'Missing BOL'    => 'Missing BOL',
							'Missing Receipt'  => 'Missing Receipt',
							'Product Damage'  => 'Product Damage',
							'Product Shortage'  => 'Product Shortage',
							'Wrong Carrier'  => 'Wrong Carrier',
							'No Hold'  => 'No Hold',
							); 
						?>
						<?php echo form_dropdown('hold_reason',$options,"Select",'id="hold_reason" style="" class="checklist_input" onchange=""');?>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Hold Notes
					</td>
					<td>
						<textarea class="checklist_input" id="hold_notes" name="hold_notes"></textarea>
					</td>
				</tr>
			</table>
		</div>
		<div id="recoursed_input_div" style="margin-left:20px; display:none;">
			<span class="heading">Invoice Recoursed</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:150px; line-height:23px;">
						Date Recoursed
					</td>
					<td>
						<input class="checklist_input datepicker" type="text" id="recourse_date_<?=$load["id"]?>" name="recourse_date_<?=$load["id"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Recourse Reason
					</td>
					<td>
						<?php $options = array(
							'Select' => 'Select',
							'Late Fees'  => 'Late Fees',
							'Missing BOL'    => 'Missing BOL',
							'Missing Receipt'  => 'Missing Receipt',
							'Product Damage'  => 'Product Damage',
							'Product Shortage'  => 'Product Shortage',
							'Wrong Carrier'  => 'Wrong Carrier',
							'Customer No Pay'  => 'Customer No Pay',
							); 
						?>
						<?php echo form_dropdown('recourse_reason',$options,"Select",'id="recourse_reason" style="" class="checklist_input" onchange=""');?>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Recourse Notes
					</td>
					<td>
						<textarea class="checklist_input" id="recourse_notes" name="recourse_notes"></textarea>
					</td>
				</tr>
			</table>
		</div>
		<div id="reimbursed_input_div" style="margin-left:20px; display:none;">
			<span class="heading">Invoice Recoursed</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:150px; line-height:23px;">
						Date Reimbursed
					</td>
					<td>
						<input class="checklist_input datepicker" type="text" id="reimbursed_date_<?=$load["id"]?>" name="reimbursed_date_<?=$load["id"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="width:150px; line-height:23px;">
						Amount Reimbursed
					</td>
					<td>
						<input class="checklist_input" type="text" id="amount_reimbursed_<?=$load["id"]?>" name="amount_reimbursed_<?=$load["id"]?>"/>
					</td>
				</tr>
			</table>
		</div>
		<div id="invoice_closed_input_div" style="margin-left:20px; display:none;">
			<span class="heading">Invoice Closed</span>
			<hr style="width:400px;"/>
			<br>
			<table>
				<tr>
					<td style="width:150px; line-height:23px;">
						Date Closed
					</td>
					<td>
						<input class="checklist_input datepicker" type="text" id="invoice_closed_date_<?=$load["id"]?>" name="invoice_closed_date_<?=$load["id"]?>"/>
					</td>
				</tr>
				<tr></tr>
				<?php if($load["billing_method"] == "Factor"): ?>
					<tr style="height:20px;">
						<td>
							FL Username:
						</td>
						<td>
							<?=$load["billed_under_carrier"]["fl_username"]." (".$load["billed_under_carrier"]["company_side_bar_name"].")"?>
						</td>
					</tr>
					<tr style="height:20px;">
						<td>
							FL Password:
						</td>
						<td>
							<?=$load["billed_under_carrier"]["fl_password"]?>
						</td>
					</tr>
				<?php endif; ?>
			</table>
		</div>
		
	</div>
</form>
