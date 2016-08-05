<div style="margin-top:20px;">
	<?php if(!empty($invoices)):?>
		<?php echo $vendor['company_name'] ." Payment ".date("m/d/Y",strtotime($payment_date))." | " ?>
		<table style="margin-top:60px;">
			<tr>
				<td colspan="2">
					<span class="heading" style="font-size:16px; color:black;">Payment Approval</span>
				</td>
				<td colspan="2">
					<span class="heading" style="float:right; width:250px; text-align:right; font-size:16px; color:black;"><?=$vendor["company_name"]?></span>
				</td>
			</tr>
			<tr class="heading">
				<td style="width:100px">
					Invoice
				</td>
				<td style="width:380px">
					Description
				</td>
				<td style="width:50px; padding-left:20px;">
					Date
				</td>
				<td style="width:100px; text-align:right;">
					Amount
				</td>
			</tr>
			<?php
				$total = 0;
			?>
			<?php foreach($invoices as $invoice):?>
				<?php
					$date_text = date("m/d/y", strtotime($invoice["invoice_datetime"]));
					$total += $invoice["invoice_amount"];
				?>
				<tr id="payment_view_row_<?=$invoice["id"]?>" style="display:none;">
					<td style="">
						<?=$invoice["invoice_number"]?>
					</td>
					<td style="">
						<?=$invoice["invoice_description"]?>
					</td>
					<td style="padding-left:20px;">
						<?=$date_text?>
					</td>
					<td style="text-align:right;">
						<?=number_format($invoice["invoice_amount"],2)?>
						<input type="hidden" id="bill_amount_<?=$invoice["id"]?>" value="<?=$invoice["invoice_amount"]?>"/>
					</td>
				</tr>
			<?php endforeach;?>
			<tr style="font-size:20px; font-weight:bold; color:black;">
				<td colspan="4" style="text-align:right;">
					$<span id="payment_total_span">0.00</span>
				</td>
			</tr>
		</table>
	<?php else: ?>
		<table>
			<tr>
				<td style="font-weight:bold; padding-left:40px;">
					There are no results for this filter set
				</td>
			</tr>
		<table>
	<?php endif; ?>
</div>