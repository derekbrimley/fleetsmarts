<?php $attributes = array('name'=>'invoice_payment_received_form','id'=>'invoice_payment_received_form', )?>
<?=form_open('expenses/record_invoice_payment_received',$attributes);?>
	<input type="hidden" id="allocated_expense_id" name="allocated_expense_id" value="<?=$expense["id"]?>" >
	<input type="hidden" id="hidden_cash_load_amount" name="hidden_cash_load_amount" value="<?=$expense["expense_amount"]?>">
	<input type="hidden" id="receivable_account_id" name="receivable_account_id" value="<?=$receivable_account_id?>" >
	<table style="">	
		<?php if(empty($invoices)): ?>
			<tr>
				<td style="text-align:center; width:320px; vertical-align: middle;">
					No invoices in the system for this account
				</td>
			</tr>
		<?php else:?>
			<tr id="" style="font-weight:bold;">
				<td  style="width:30px; vertical-align:top;">
				</td>
				<td  style="width: 60px; vertical-align:top;">
					Date
				</td>
				<td  style="width: 60px; vertical-align:top;">
					Invoice #
				</td>
				<td  style="width:135px; vertical-align:top; padding-left:5px;">
					Description
				</td>
				<td  style="width:60px; vertical-align:top; text-align:right;">
					Amount
				</td>
			</tr>
			<?php foreach($invoices as $invoice): ?>
				<tr id="" style="">
					<td  style="vertical-align: top;">
						<input type="checkbox" onclick="paid_invoice_clicked('<?=$invoice["id"]?>')" id="paid_bill_checkbox_<?=$invoice["id"]?>" name="paid_bill_checkbox_<?=$invoice["id"]?>" value="<?=$invoice["id"]?>" />
					</td>
					<td  style="vertical-align: top;">
						<?=date("m/d/y",strtotime($invoice["invoice_datetime"]))?>
					</td>
					<td  style="vertical-align: top;">
						<?=$invoice["invoice_number"]?>
					</td>
					<td  style="vertical-align: top; padding-left:5px; padding-bottom:10px;">
						<?=$invoice["invoice_description"]?>
					</td>
					<td  style="vertical-align: top; text-align:right; padding-left:5px;">
						<input type="text" name="paid_bill_amount_<?=$invoice["id"]?>" id="paid_bill_amount_<?=$invoice["id"]?>" onfocus="onfocus_paid_invoice_amount('<?=$invoice["id"]?>')" onblur="onblur_paid_invoice_amount('<?=$invoice["id"]?>')" value="<?=number_format(get_invoice_balance($invoice),2,'.','') ?>" style="text-align:right; width:55px;">
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</table>
</form>	