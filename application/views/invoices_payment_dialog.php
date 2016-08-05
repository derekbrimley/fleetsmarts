<style type="text/css">
	tr
	{
		height:25px;
	}
</style>
<?php $attributes = array('name'=>'submit_payment_form','id'=>'submit_payment_form', )?>
<?=form_open('invoices/submit_payment',$attributes);?>
	<?php echo form_hidden('vendor_id',$vendor_id); ?>
	<?php echo form_hidden('guid',$guid); ?>
	<?php echo form_hidden('total_of_invoices',$total_of_invoices); ?>
	<div style="margin:5px; font-size:14px;">
		Email Subject Line: <?=$vendor_name?> Payment <?=date("m-d-y")?> | 
	</div>
	<table  style="table-layout:fixed; margin:5px; font-size:12px;">
		<tr class="" style="line-height:25px;">
			<td style="width:180px; font-weight:bold;" VALIGN="top">
				Via Cash Account
			</td>
			<td id="cash_account_td" style="width:435px; text-align:right;" VALIGN="top">
				<?php echo form_dropdown('via_cash_account',$allowed_cash_or_expense_accounts_options,"Select",'id="via_cash_account" onChange="" class="left_bar_input"');?>
			</td>
		</tr>
		<tr class="" style="line-height:25px;">
			<td style="width:180px; font-weight:bold;" VALIGN="top">
				Link
			</td>
			<td id="cash_account_td" style="text-align:right;" VALIGN="top">
				<input type="text" id="entry_link" name="entry_link" style="width:156px;">
			</td>
		</tr>
		<tr class="" style="line-height:25px;">
			<td style="width:180px; font-weight:bold;" VALIGN="top">
				Vendor
			</td>
			<td style="text-align:right;" VALIGN="top">
				<?=$vendor_name?>
			</td>
		</tr>
	</table>
	<br>
	<table  style="table-layout:fixed; margin:5px; font-size:12px;">
		<tr class="heading" style="line-height:25px;">
			<td style="width:110px;" VALIGN="top">Invoice</td>
			<td style="width:90px;" VALIGN="top">Invoice Type</td>
			<td style="width:70px;" VALIGN="top">Unit</td>
			<td style="width:180px;" VALIGN="top">Description</td>
			<td style="width:80px;" VALIGN="top">Date</td>
			<td style="width:80px; text-align:right;" VALIGN="top">Amount</td>
		</tr>
	</table>
	<?php foreach ($invoices as $invoice): ?>
		<?php
				$invoice_id = $invoice["id"];
				$invoice_amount = round($invoice["invoice_amount"],2);
				
				//CREATE DATE RANGE TEXT
				$date_range = date("m/d",strtotime($invoice["start_datetime"]))." - ".date("m/d",strtotime($invoice["end_datetime"]));
				if($invoice["bill_type"] == "Service")
				{
					$date_range = date("m/d/y",strtotime($invoice["end_datetime"]));
				}
				
				//GET PAYMENT DATE
				$payment_date = "";
				if(!empty($invoice["payment_datetime"]))
				{
					$payment_date = date("m/d/y",strtotime($invoice["payment_datetime"]));
				}
				
		?>
		<div style="padding-top:5px;padding-bottom:3px;">
			<table style="margin-left:3px; font-size:12px;">
				<tr style="">
					<td style="overflow:hidden; min-width:110px;  max-width:110px; line-height:18px;" VALIGN="top" title="<?=$invoice["invoice_number"]?>" onclick=""><a target="_blank" href="<?=$invoice["invoice_link"] ?>"><?=$invoice["invoice_number"]?></a></td>
					<td style="overflow:hidden; min-width:90px;  max-width:90px; line-height:18px;" VALIGN="top" title=""><?=$invoice["bill_type"]?></td>
					<td style="overflow:hidden; min-width:70px;  max-width:70px; line-height:18px;" VALIGN="top" title=""><?=$invoice["unit_number"]?></td>
					<td style="overflow:hidden; min-width:180px;  max-width:180px; line-height:18px;" VALIGN="top" title=""><?=$invoice["invoice_desc"]?></td>
					<td style="overflow:hidden; min-width:80px;  max-width:80px;  line-height:18px;" VALIGN="top" title="<?=$date_range?>"><?=$date_range?></td>
					<td style="overflow:hidden; min-width:80px;  max-width:80px;  line-height:18px; text-align:right;" VALIGN="top"><?=number_format($invoice['invoice_amount'], 2,'.','');?></td>
				</tr>
			</table>
		</div>        
	<?php endforeach; ?>
	<table  style="table-layout:fixed; margin:5px; font-size:12px;">
		<tr class="" style="line-height:25px; font-size:16px; font-weight:bold;">
			<td style="width:110px;" VALIGN="top"></td>
			<td style="width:90px;" VALIGN="top"></td>
			<td style="width:70px;" VALIGN="top"></td>
			<td style="width:180px;" VALIGN="top"></td>
			<td style="width:80px;" VALIGN="top"></td>
			<td style="width:80px; text-align:right;" VALIGN="top"><?=number_format($total_of_invoices,2)?></td>
		</tr>
	</table>
	<span style="font-weight:bold;">
		Payment Notes
	</span>
	<br>
	<textarea id="payment_notes" name="payment_notes" rows="3" style="width:615px;"></textarea>
	
	<?php if(!$all_invoices_are_allocated): ?>
		<div style="margin-top:25px; color:red; font-weight:bold;">
			There are unallocated invoices! You must allocated all the invoices before you can make the payment.
		</div>
	<?php else:?>
		<script>$("#submit_payment").prop("disabled",false);</script>
	<?php endif;?>
	
</form>