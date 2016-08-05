<script>
	$("#link_row").show();
	$("#to_business_cash_account_row").show();
</script>
<div style="padding:10px; background:#DDD; margin-top:10px; margin-bottom:10px;">
	<span style="font-weight:bold; color:black;">Invoices</span>
	<table style="">	
		<?php if(empty($funded_invoices)): ?>
			<tr>
				<td style="text-align:center; width:320px; vertical-align: middle;">
					No funded invoices in the system
				</td>
			</tr>
		<?php else:?>
			<tr id="" style="font-weight:bold;">
				<td  style="width:30px; vertical-align:bottom;">
				</td>
				<td  style="width: 120px; vertical-align:bottom;">
					Broker
				</td>
				<td  style="width: 60px; vertical-align:bottom;">
					Load #
				</td>
				<td  style="width:80px; vertical-align:bottom;">
					Invoice #
				</td>
				<td  style="width:60px; vertical-align:bottom; text-align:right;">
					Amount
				</td>
			</tr>
			<?php foreach($funded_invoices as $invoice): ?>
				<tr id="" style="">
					<td  style="vertical-align: middle;">
						<input type="checkbox" onclick="unpaid_invoice_clicked('<?=$invoice["id"]?>')" id="invoice_checkbox_<?=$invoice["id"]?>" name="invoice_checkbox_<?=$invoice["id"]?>" value="<?=$invoice["id"]?>" />
					</td>
					<td  style="vertical-align: middle;">
						<?=$invoice["broker"]["customer_name"]?>
					</td>
					<td  style="vertical-align: middle;">
						<?=$invoice["customer_load_number"]?>
					</td>
					<td  style="vertical-align: middle;">
						<?=$invoice["invoice_number"]?>
					</td>
					<td  style="vertical-align: middle; text-align:right;">
						<input type="text" name="invoice_amount_<?=$invoice["id"]?>" id="invoice_amount_<?=$invoice["id"]?>" onfocus="edit_funded_amount('<?=$invoice["id"]?>')" onblur="edit_funded_amount_done('<?=$invoice["id"]?>')" value="<?=number_format($invoice["amount_funded"],2,'.','') ?>" style="text-align:right; width:60px;">
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</table>
</div>

<table style="margin-left:30px;">
	<tr id="amount_funded_row" name="amount_funded_row">
		<td  style="width:185px; vertical-align: middle;">
			Gross Pay
		</td>
		<td  style="vertical-align: middle;">
			<input type="text" readonly id="funded_amount" name="funded_amount" style="text-align:right;" class="left_bar_input" value="0">
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
</table>

<div style="padding:10px; background:#DDD; margin-top:10px; margin-bottom:10px;">
	<span style="font-weight:bold; color:black;">Deductions</span>
	<span style="float:right;"><a href="#" onclick="add_deduction()">+Add</a></span>
	<table style="">	
		<tr id="" style="font-weight:bold;">
			<td  style="width: 100px; vertical-align:bottom;">
				FM Account
			</td>
			<td  style="width: 190px; vertical-align:bottom;">
				Notes
			</td>
			<td  style="width:60px; vertical-align:bottom; text-align:right;">
				Amount
			</td>
		</tr>
		<tr id="deduction_row_1" style="">
			<td  style="vertical-align: middle;">
				<?php echo form_dropdown('fm_account_1',$fm_account_options,"Select",'id="fm_account_1" onblur="calc_total_deductions()" style="width:90px"');?>
			</td>
			<td  style="vertical-align: middle;">
				<input id="d_notes_1" name="d_notes_1" type="text" style="width:180px"></input>
			</td>
			<td  style="vertical-align: middle;">
				<input id="d1" name="d1" type="text" style="text-align:right; width:60px" onblur="calc_total_deductions()"></input>
			</td>
		</tr>
		<tr id="deduction_row_2" style="display:none;">
			<td  style="vertical-align: middle;">
				<?php echo form_dropdown('fm_account_2',$fm_account_options,"Select",'id="fm_account_2"  style="width:90px"');?>
			</td>
			<td  style="vertical-align: middle;">
				<input id="d_notes_2" name="d_notes_2" type="text" style="width:180px"></input>
			</td>
			<td  style="vertical-align: middle;">
				<input id="d2" name="d2" type="text" style="text-align:right; width:60px" onblur="calc_total_deductions()"></input>
			</td>
		</tr>
		<tr id="deduction_row_3" style="display:none;">
			<td  style="vertical-align: middle;">
				<?php echo form_dropdown('fm_account_3',$fm_account_options,"Select",'id="fm_account_3"  onblur="calc_total_deductions()" style="width:90px"');?>
			</td>
			<td  style="vertical-align: middle;">
				<input id="d_notes_3" name="d_notes_3" type="text" style="width:180px"></input>
			</td>
			<td  style="vertical-align: middle;">
				<input id="d3" name="d3" type="text" style="text-align:right; width:60px" onblur="calc_total_deductions()"></input>
			</td>
		</tr>
		<tr id="deduction_row_4" style="display:none;">
			<td  style="vertical-align: middle;">
				<?php echo form_dropdown('fm_account_4',$fm_account_options,"Select",'id="fm_account_4" onblur="calc_total_deductions()" style="width:90px"');?>
			</td>
			<td  style="vertical-align: middle;">
				<input id="d_notes_4" name="d_notes_4" type="text" style="width:180px"></input>
			</td>
			<td  style="vertical-align: middle;">
				<input id="d4" name="d4" type="text" style="text-align:right; width:60px" onblur="calc_total_deductions()"></input>
			</td>
		</tr>
		<tr id="deduction_row_5" style="display:none;">
			<td  style="vertical-align: middle;">
				<?php echo form_dropdown('fm_account_5',$fm_account_options,"Select",'id="fm_account_5" onblur="calc_total_deductions()" style="width:90px"');?>
			</td>
			<td  style="vertical-align: middle;">
				<input id="d_notes_5" name="d_notes_5" type="text" style="width:180px"></input>
			</td>
			<td  style="vertical-align: middle;">
				<input id="d5" name="d5" type="text" style="text-align:right; width:60px" onblur="calc_total_deductions()"></input>
			</td>
		</tr>
	</table>
</div>

<table style="margin-left:30px;">
	<tr id="total_deductions_row" name="total_deductions_row">
		<td  style="width:185px; vertical-align: middle;">
			Total Deductions
		</td>
		<td  style="vertical-align: middle;">
			<input type="text" readonly id="total_deductions" name="total_deductions" style="text-align:right;" class="left_bar_input" value="0">
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
	<tr id="net_pay_row" name="net_pay_row">
		<td  style="font-weight:bold; width:185px; vertical-align: middle;">
			Net Pay
		</td>
		<td  style="vertical-align: middle;">
			<input type="text" readonly id="net_pay" name="net_pay" style="text-align:right;" class="left_bar_input" value="0">
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
	<tr id="load_datetime_row" name="load_datetime_row">
		<td  style="width:185px; vertical-align: middle;">
			Load Date-time
		</td>
		<td  style="vertical-align: middle;">
			<input type="text" id="load_datetime" name="load_datetime" style="text-align:right;" class="left_bar_input">
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
</table>