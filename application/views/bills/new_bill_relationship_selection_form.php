<?php $attributes = array('id' => 'load_new_invoice_form'); ?>
<?=form_open('invoice/customer_vendor_selected',$attributes)?>
	<input type="hidden" id="relationship_selected_bill_holder_id" name="relationship_selected_bill_holder_id" value="<?=$bill_holder_id?>"/>
	<input type="hidden" id="relationship_selected_business_or_member" name="relationship_selected_business_or_member" value="<?=$member_or_business?>"/>
	<input type="hidden" id="relationship_selected_member_id" name="relationship_selected_member_id" value="<?=$member_id?>"/>
	<input type="hidden" id="relationship_selected_business_user_id" name="relationship_selected_business_user_id" value="<?=$business_user_id?>"/>
	<input type="hidden" id="relationship_selected_bill_type" name="relationship_selected_bill_type" value="<?=$bill_type?>"/>
	<input type="hidden" id="relationship_selected_new_bill_ticket" name="relationship_selected_new_bill_ticket" value="<?=$new_bill_ticket?>"/>
	<input type="hidden" id="relationship_selected_payment_method" name="relationship_selected_payment_method" value="<?=$payment_method?>"/>
	<table style="font-size:14px; width:400px; margin:auto;">
		<tr id="vendor_relationship_row" style="display:none;">
			<td style="width:180px;"><?=$customer_vendor?></td>
			<td>
				<?php echo form_dropdown('relationship_selected_relationship_id',$relationship_options,$pre_selected_vendor,'id="relationship_selected_relationship_id" onChange="relationship_selected()" class="left_bar_input"');?>
			</td>
		</tr>
	</table>
</form>
<div id="new_invoice_form_div">
	<!-- AJAX GOES HERE!-->
</div>