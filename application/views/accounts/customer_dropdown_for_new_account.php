<?php $attributes = array('id' => 'customer_for_new_account_form'); ?>
<?=form_open('accounts/load_new_customer_account_form',$attributes)?>
	<table style="font-size:14px; width:360px; margin:auto; margin-top:0px;">
		<tr>
			<td style="width:180px;">Customer</td>
			<td>
				<?php echo form_dropdown('customer_for_new_account',$customer_options,'Select','id="customer_for_new_account" onChange="customer_for_new_account_selected(this.value)" style="" class="left_bar_input"');?>
			</td>
		</tr>
	</table>
</form>