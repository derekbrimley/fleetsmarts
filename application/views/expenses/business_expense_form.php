<?php
	$category_text = "";
	if(!empty($expense["category"]))
	{
		$category_text = $expense["category"];
	}
	
	//GET COMPANY
	$where = null;
	$where["id"] = $expense["company_id"];
	$company = db_select_company($where);
	
	$owner_text = $company["company_side_bar_name"];
	
?>
<?php $attributes = array('name'=>'business_expense_form','id'=>'business_expense_form', )?>
<?=form_open('expenses/record_business_expense',$attributes);?>
	<input type="hidden" id="allocated_expense_id" name="allocated_expense_id" value="<?=$expense["id"]?>" >
	<table style="margin-left:30px;">
		<tr>
			<td style="width:185px;">Owner</td>
			<td>
				<?=$owner_text?>
			</td>
		</tr>
		<tr>
			<td style="width:185px;">Category</td>
			<td>
				<?=$category_text?>
			</td>
		</tr>
		<tr>
			<td style="width:185px;">Expense Account</td>
			<td>
				<?php echo form_dropdown('expense_account_id',$expense_options,'Select','id="expense_account_id" onChange="" style="" class="left_bar_input"');?>
			</td>
		</tr>
	</table>
</form>	