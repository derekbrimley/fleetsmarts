<script>
	function account_class_selected()
	{
		//HIDE ALL PARENT ACCOUNT DROPDOWNS
		$("#parent_asset_account").hide();
		$("#parent_liability_account").hide();
		$("#parent_revenue_account").hide();
		$("#parent_expense_account").hide();
		
		$("#asset_category").hide();
		$("#liability_category").hide();
		$("#revenue_category").hide();
		$("#expense_category").hide();
		
		$("#account_category_row").hide();
		$("#account_name_row").hide();
	
	
		if($("#account_class").val() == "Asset")
		{
			$("#parent_asset_account").show();
			$("#asset_category").show();
		}
		else if($("#account_class").val() == "Liability")
		{
			$("#parent_liability_account").show();
			$("#liability_category").show();
		}
		else if($("#account_class").val() == "Revenue")
		{
			$("#parent_revenue_account").show();
			$("#revenue_category").show();
		}
		else if($("#account_class").val() == "Expense")
		{
			$("#parent_expense_account").show();
			$("#expense_category").show();
		}
		
		$("#parent_account_row").show();
		$("#account_category_row").show();
	}
	
	function category_account_selected(category)
	{
		$("#new_category_row").hide();
		
		if(category == "New Category")
		{
			$("#new_category_row").show();
		}
		
		$("#account_name_row").show();
	}
</script>
<?php $attributes = array('id' => 'new_account_form'); ?>
<?=form_open('accounts/load_new_account_form',$attributes)?>
	<input type="hidden" id="business_user_company_id" name="business_user_company_id" value="<?=$business_user_id?>"/>
	<input type="hidden" id="account_type" name="account_type" value="<?=$account_with?>"/>
	<table style="font-size:14px; width:360px; margin:auto; margin-top:0px;">
		<?php if($account_with == "Customer" || $account_with == "Vendor" || $account_with == "Member"):?>
			<tr id="relationship_row" name="relationship_row"style="">
				<td><?=$account_with?></td>
				<td>
					<?php echo form_dropdown('relationship_id',$relationship_options,'Select','id="relationship_id" onChange="" style="" class="left_bar_input"');?>
				</td>
			</tr>
		<?php else:?>
			<input type="hidden" id="relationship_id" name="relationship_id" value="none"/>
		<?php endif;?>
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
				<?php echo form_dropdown('parent_asset_account',$asset_account_options,'Select','id="parent_asset_account" onChange="" style="display:none;" class="left_bar_input"');?>
				<?php echo form_dropdown('parent_liability_account',$liability_account_options,'Select','id="parent_liability_account" onChange="" style="display:none;" class="left_bar_input"');?>
				<?php echo form_dropdown('parent_revenue_account',$revenue_account_options,'Select','id="parent_revenue_account" onChange="" style="display:none;" class="left_bar_input"');?>
				<?php echo form_dropdown('parent_expense_account',$expense_account_options,'Select','id="parent_expense_account" onChange="" style="display:none;" class="left_bar_input"');?>
			</td>
		</tr>
		<tr id="account_category_row" name="account_category_row"style="display:none;">
			<td>Account Category</td>
			<td>
				<?php echo form_dropdown('asset_category',$asset_category_options,'Select','id="asset_category" onChange="category_account_selected(this.value)" style="display:none;" class="left_bar_input"');?>
				<?php echo form_dropdown('liability_category',$liability_category_options,'Select','id="liability_category" onChange="category_account_selected(this.value)" style="display:none;" class="left_bar_input"');?>
				<?php echo form_dropdown('revenue_category',$revenue_category_options,'Select','id="revenue_category" onChange="category_account_selected(this.value)" style="display:none;" class="left_bar_input"');?>
				<?php echo form_dropdown('expense_category',$expense_category_options,'Select','id="expense_category" onChange="category_account_selected(this.value)" style="display:none;" class="left_bar_input"');?>
			</td>
		</tr>
		<tr id="new_category_row" name="new_category_row"style="display:none;">
			<td>New Category</td>
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