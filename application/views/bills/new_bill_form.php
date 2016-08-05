<script>
	$('#new_invoice_date').datepicker({ showAnim: 'blind' });
</script>

<?php $attributes = array('id' => 'final_new_invoice_form', 'name'=>'final_new_invoice_form', 'target'=>'_blank'); ?>
<?=form_open_multipart('bills/insert_new_invoice',$attributes)?>
	<input type="hidden" id="new_invoice_business_user_id" name="new_invoice_business_user_id"  value="<?=$business_user_id?>"/>
	<input type="hidden" id="new_invoice_member_or_business" name="new_invoice_member_or_business" value="<?=$member_or_business?>"/>
	<input type="hidden" id="new_invoice_bill_type" name="new_invoice_bill_type" value="<?=$bill_type?>"/>
	<input type="hidden" id="new_invoice_new_bill_ticket" name="new_invoice_new_bill_ticket" value="<?=$new_bill_ticket?>"/>
	<input type="hidden" id="new_invoice_payment_method" name="new_invoice_payment_method" value="<?=$payment_method?>"/>
	<input type="hidden" id="new_invoice_relationship_id" name="new_invoice_relationship_id" value="<?=$relationship_id?>"/>
	<input type="hidden" id="new_invoice_bill_holder_id" name="new_invoice_bill_holder_id" value="<?=$bill_holder["id"]?>"/>
	<table style="font-size:14px; width:400px; margin:auto;">
		<tr>
			<td style="width:180px;"><?=$balance_sheet_account_label?></td>
			<td>
				<?php echo form_dropdown('balance_sheet_id',$balance_sheet_account_options,'Select','id="balance_sheet_id" onChange="balance_sheet_account_selected()" class="left_bar_input"');?>
			</td>
		</tr>
		<tr id="income_statement_account_id_row" style="display:none;">
			<td style="width:180px;"><?=$income_statement_account_label?></td>
			<td>
				<?php echo form_dropdown('income_statement_id',$income_statement_account_options,'Select','id="income_statement_id" onChange="show_more_fields()" class="left_bar_input"');?>
			</td>
		</tr>
		<tr id="deposit_account_id_row" style="display:none;">
			<td style="width:180px;"><?=$deposit_account_label?></td>
			<td>
				<?php echo form_dropdown('deposit_account_id',$deposit_account_options,'Select','id="deposit_account_id" onChange="show_more_fields()" class="left_bar_input"');?>
			</td>
		</tr>
		<?php if(!empty($bill_holder)):?>
			<?php
				//GET INVOICE
				$where = null;
				$where["id"] = $bill_holder["invoice_id"];
				$invoice = db_select_invoice($where);
			?>
			<tr id="new_invoice_date_row" style="display:none;">
				<td style="width:180px;">Invoice Date</td>
				<td>
					<input type="hidden" id="new_invoice_date" name="new_invoice_date"  value="<?=$bill_holder["bill_datetime"]?>"/>
					<?=date("m/d/y",strtotime($bill_holder["bill_datetime"]))?>
				</td>
			</tr>
			<tr id="new_invoice_amount_row" style="display:none;">
				<td style="width:180px;">Invoice Amount</td>
				<td>
					<input type="hidden" id="new_invoice_amount" name="new_invoice_amount"  value="<?=$bill_holder["amount"]?>"/>
					<?=number_format($bill_holder["amount"],2)?>
				</td>
			</tr>
			<tr id="new_invoice_number_row" style="display:none;">
				<td style="width:180px;">Invoice Number</td>
				<td>
					<input type="hidden" id="new_invoice_number" name="new_invoice_number"  value="<?=$invoice["invoice_number"]?>"/>
					<?=$invoice["invoice_number"]?>
				</td>
			</tr>
			<tr id="new_invoice_desc_row" style="display:none;">
				<td style="width:180px;">Description</td>
				<td>
					<textarea text" id="new_invoice_desc" name="new_invoice_desc"  class="left_bar_input"><?=$bill_holder["description"]?></textarea>
				</td>
			</tr>
			<?php if(!empty($bill_holder["file_guid"])):?>
				<tr id="file_row" style="display:none;">
					<td style="width:180px; vertical-align:bottom;">Document Upload</td>
					<td style="vertical-align:bottom;">
						<input type="hidden" id="bill_holder_file_guid" name="bill_holder_file_guid" value="<?=$bill_holder["file_guid"]?>"/>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$bill_holder["file_guid"]?>" onclick="event.stopImmediatePropagation();"><?=$invoice["invoice_number"]?></a>
					</td>
				</tr>
			<?php else:?>
				<tr id="file_row" style="display:none;">
					<td style="width:180px; vertical-align:bottom;">Document</td>
					<td style="vertical-align:bottom;">
						<input type="hidden" id="bill_holder_file_guid" name="bill_holder_file_guid" value="" />
						<input type="file" id="invoice_file" name="invoice_file" style="width:190px;" />
					</td>
				</tr>
			<?php endif;?>
		<?php else:?>
			<tr id="new_invoice_date_row" style="display:none;">
				<td style="width:180px;">Invoice Date</td>
				<td>
					<input type="text" id="new_invoice_date" name="new_invoice_date"  class="left_bar_input"/>
				</td>
			</tr>
			<tr id="new_invoice_amount_row" style="display:none;">
				<td style="width:180px;">Invoice Amount</td>
				<td>
					<input type="text" id="new_invoice_amount" name="new_invoice_amount"  class="left_bar_input"/>
				</td>
			</tr>
			<tr id="new_invoice_number_row" style="display:none;">
				<td style="width:180px;">Invoice Number</td>
				<td>
					<input type="text" id="new_invoice_number" name="new_invoice_number"  class="left_bar_input"/>
				</td>
			</tr>
			<tr id="new_invoice_desc_row" style="display:none;">
				<td style="width:180px;">Description</td>
				<td>
					<textarea text" id="new_invoice_desc" name="new_invoice_desc"  class="left_bar_input"/>
				</td>
			</tr>
			<tr id="generate_invoice_row" style="display:none;">
				<td style="width:180px; vertical-align:bottom;"></td>
				<td style="vertical-align:middle;">
					<span class="link" onclick="generate_invoice()">Generate Invoice</a>
				</td>
			</tr>
			<tr id="file_row" style="display:none;">
				<td style="width:180px; vertical-align:bottom;">Document Upload</td>
				<td style="vertical-align:bottom;">
					<input type="hidden" id="bill_holder_file_guid" name="bill_holder_file_guid" value="" />
					<input type="file" id="invoice_file" name="invoice_file" style="width:190px;" />
				</td>
			</tr>
		<?php endif;?>
	</table>
</form>
<?php $attributes = array('id' => 'generate_invoice_form', 'name'=>'generate_invoice_form', 'target'=>'_blank'); ?>
<?=form_open('bills/generate_invoice',$attributes)?>
	<input type="hidden" id="gi_invoice_relationship_id" name="gi_invoice_relationship_id"/>
	<input type="hidden" id="gi_invoice_date" name="gi_invoice_date"/>
	<input type="hidden" id="gi_invoice_amount" name="gi_invoice_amount"/>
	<input type="hidden" id="gi_invoice_number" name="gi_invoice_number"/>
	<input type="hidden" id="gi_invoice_desc" name="gi_invoice_desc"/>
</form>