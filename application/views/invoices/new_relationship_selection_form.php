<?php $attributes = array('id' => 'load_new_invoice_form'); ?>
<?=form_open('invoice/customer_vendor_selected',$attributes)?>
	<input type="hidden" id="relationship_selected_business_or_member" name="relationship_selected_business_or_member" value="<?=$member_or_business?>"/>
	<input type="hidden" id="relationship_selected_member_id" name="relationship_selected_member_id" value="<?=$member_id?>"/>
	<input type="hidden" id="relationship_selected_business_user_id" name="relationship_selected_business_user_id" value="<?=$business_user_id?>"/>
	<input type="hidden" id="relationship_selected_new_invoice_type" name="relationship_selected_new_invoice_type" value="<?=$new_invoice_type?>"/>
	<table style="font-size:14px; width:400px; margin:auto;">
		<tr>
			<td style="width:180px;"><?=$customer_vendor?></td>
			<td>
				<?php echo form_dropdown('relationship_selected_relationship_id',$relationship_options,'Select','id="relationship_selected_relationship_id" onChange="relationship_selected()" class="left_bar_input"');?>
			</td>
		</tr>
	</table>
</form>
<div id="new_invoice_form_div">
	<!-- AJAX GOES HERE!-->
</div>