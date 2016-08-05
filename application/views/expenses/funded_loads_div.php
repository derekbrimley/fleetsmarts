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
					<input type="text" name="invoice_amount_<?=$invoice["id"]?>" id="invoice_amount_<?=$invoice["id"]?>" onfocus="onfocus_load_amount('<?=$invoice["id"]?>')" onblur="onblur_load_amount('<?=$invoice["id"]?>')" value="<?=number_format($invoice["amount_funded"],2,'.','') ?>" style="text-align:right; width:60px;">
				</td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>
</table>
