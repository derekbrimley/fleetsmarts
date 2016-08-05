<?php $attributes = array('name'=>'receivable_account_form','id'=>'receivable_account_form', )?>
<?=form_open('expenses/receivable_account_selected',$attributes);?>
	<input type="hidden" id="allocated_expense_id" name="allocated_expense_id" value="<?=$expense["id"]?>" >
	<table style="margin-left:30px;">
		<tr>
			<td style="width:185px;">Invoice Receivable Account</td>
			<td>
				<?php echo form_dropdown('receivable_account_id',$receivable_options,'Select','id="receivable_account_id" onChange="receivable_account_selected()" style="" class="left_bar_input"');?>
			</td>
		</tr>
	</table>
</form>	
<div id="invoice_payment_received_form_div" name="invoice_payment_received_form_div"  style="display:none; padding:10px; min-height:50px; margin-top:10px; margin-bottom:10px; background:rgb(221,221,221);>
	<!-- AJAX GOES HERE!-->
	<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>
</div>
<div id="invoices_paid_total_div" name="invoices_paid_total_div" style="display:none;">
	<table style="margin-left:30px;">
		<tr id="invoices_paid_total_row" style="">
			<td style="width:185px;">Total Payment</td>
			<td>
				<input type="text" readonly id="total_bills_paid_amount" name="total_bills_paid_amount" style="font-weight:bold; font-size:16px; color:red; text-align:right; border:none;" class="left_bar_input" value="0">
			</td>
			<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			</td>
		</tr>
	</table>
</div>