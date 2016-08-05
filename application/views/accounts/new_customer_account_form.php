
<?php $attributes = array('id' => 'pre_new_account_form'); ?>
<?=form_open('accounts/load_new_account_form',$attributes)?>
	<table style="font-size:14px; width:360px; margin:auto; margin-top:0px;">
		<tr>
			<td style="width:180px;">Account Class</td>
			<td>
				<?php $options = array(
					'Select'  	=> 'Select' ,
					'Asset'  => 'Asset',
					'Liability'  => 'Liability',
					'Revenue'  	=> 'Revenue',
					'Expense' 	=> 'Expense',
					); ?>
				<?php echo form_dropdown('account_class',$options,"Select",'id="account_class" onChange="account_class_selected()" class="left_bar_input"');?>
			</td>
		</tr>
		<tr id="parent_account_row" name="parent_account_row"style="display:none;">
			<td>Parent Account</td>
			<td>
				<?php echo form_dropdown('parent_asset_account',$asset_account_options,'Select','id="parent_asset_account" onChange="parent_account_selected(this.value)" style="display:none;" class="left_bar_input"');?>
				<?php echo form_dropdown('parent_liability_account',$liability_account_options,'Select','id="parent_liability_account" onChange="parent_account_selected(this.value)" style="display:none;" class="left_bar_input"');?>
				<?php echo form_dropdown('parent_revenue_account',$revenue_account_options,'Select','id="parent_revenue_account" onChange="parent_account_selected(this.value)" style="display:none;" class="left_bar_input"');?>
				<?php echo form_dropdown('parent_expense_account',$expense_account_options,'Select','id="parent_expense_account" onChange="parent_account_selected(this.value)" style="display:none;" class="left_bar_input"');?>
			</td>
		</tr>
		<tr id="account_category_row" name="account_category_row"style="display:none;">
			<td>Account Category</td>
			<td>
				<input id="account_category" name="account_category" type="text" class="left_bar_input"/>
			</td>
		</tr>
		<tr id="account_name_row" name="account_name_row"style="display:none;">
			<td>Account Name</td>
			<td>
				<input id="account_name" name="account_name" type="text" class="left_bar_input"/>
			</td>
		</tr>
	</table>
</form>