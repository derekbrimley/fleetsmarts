<script>
	$('#new_invoice_date').datepicker({ showAnim: 'blind' });
</script>

<?php $attributes = array('id' => 'final_new_invoice_form', 'name'=>'final_new_invoice_form', 'target'=>'_blank'); ?>
<?=form_open_multipart('invoices/insert_new_invoice',$attributes)?>
	<input type="hidden" id="new_invoice_business_user_id" name="new_invoice_business_user_id"  value="<?=$business_user_id?>"/>
	<input type="hidden" id="new_invoice_invoice_type" name="new_invoice_invoice_type" value="<?=$new_invoice_type?>"/>
	<input type="hidden" id="new_invoice_relationship_id" name="new_invoice_relationship_id" value="<?=$relationship_id?>"/>
	<input type="hidden" id="new_invoice_payment_method" name="new_invoice_payment_method"  value="<?=$payment_method?>"/>
	<table style="font-size:14px; width:400px; margin:auto;">
		<tr id="income_statement_account_id_row" style="">
			<td style="width:180px;"><?=$income_statement_account_label?></td>
			<td>
				<?php echo form_dropdown('income_statement_id',$income_statement_account_options,'Select','id="income_statement_id" onChange="show_more_fields()" class="left_bar_input"');?>
			</td>
		</tr>
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
		<tr id="file_row" style="display:none;">
			<td style="width:180px; vertical-align:bottom;">Document Upload</td>
			<td style="vertical-align:bottom;">
				<input type="file" id="invoice_file" name="invoice_file" style="width:190px;" />
			</td>
		</tr>
	</table>
</form>