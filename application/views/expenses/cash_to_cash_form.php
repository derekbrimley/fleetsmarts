<?php $attributes = array('name'=>'cash_to_cash_form','id'=>'cash_to_cash_form', )?>
<?=form_open('expenses/record_cash_to_cash',$attributes);?>
	<input type="hidden" id="allocated_expense_id" name="allocated_expense_id" value="<?=$expense["id"]?>" >
	<table style="margin-left:30px;">
		<tr>
			<td style="width:185px;">Matching Transfer</td>
			<td>
				<?php echo form_dropdown('matching_expense',$c2c_options,'Select','id="matching_expense" onChange="matching_transfer_selected()" style="" class="left_bar_input"');?>
			</td>
		</tr>
		<tr id="corresponding_account_row" name="corresponding_account_row" style="display:none;" >
			<td style="width:185px;">Corresponding Account</td>
			<td>
				<?php echo form_dropdown('corresponding_account',$cash_accounts_options,'Select','id="corresponding_account" onChange="matching_transfer_selected()" style="" class="left_bar_input"');?>
			</td>
		</tr>
	</table>
</form>	