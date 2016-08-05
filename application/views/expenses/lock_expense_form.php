<?php
	$issuer_text = "";
	if(!empty($expense["issuer_id"]))
	{
		//GET COMPANY
		$where = null;
		$where["id"] = $expense["issuer_id"];
		$company = db_select_company($where);
		
		$issuer_text = $company["company_side_bar_name"];
	}
	
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

<?php $attributes = array('id' => 'lock_expense_form'); ?>
<?=form_open('expenses/lock_expense',$attributes)?>
	<input type="hidden" id="lock_expense_id" name="lock_expense_id" value="<?=$expense['id']?>"/>
	<input type="hidden" id="po_action" name="po_action" value="<?=$po_action?>"/>
	<input type="hidden" id="po_id" name="po_id" value="<?=$po['id']?>"/>
	<input type="hidden" id="expense_owner" name="expense_owner" value="<?=$owner_text?>"/>
	<input type="hidden" id="expense_category" name="expense_category" value="<?=$category_text?>"/>
	<table style="margin-left:30px;">
		<tr id="lock_load_row">
			<td style="width:185px;">
				Issuer
			</td>
			<td>
				<?=$issuer_text?>
			</td>
		</tr>
		<tr id="lock_equipment_row">
			<td style="width:185px;">
				Owner
			</td>
			<td>
				<?=$owner_text?>
			</td>
		</tr>
		<tr id="lock_description_row">
			<td style="width:185px;">
				Category
			</td>
			<td>
				<?=$category_text?>
			</td>
		</tr>
	</table>
</form>