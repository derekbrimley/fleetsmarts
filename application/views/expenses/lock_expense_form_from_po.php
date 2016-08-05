<?php
	$issuer_text = "";
	if(!empty($expense["issuer_id"]))
	{
		//GET COMPANY
		$where = null;
		$where["id"] = $po["issuer_id"];
		$person = db_select_person($where);
		
		$issuer_text = $person["full_name"];
	}
	
	$category_text = "";
	if(!empty($po["category"]))
	{
		$category_text = $po["category"];
	}
	
	$owner_text = "";
	if(!empty($po["category"]))
	{
		//GET COMPANY
		$where = null;
		$where["id"] = $po["owner_id"];
		$company = db_select_company($where);
		
		$owner_text = $company["company_side_bar_name"];
	}
	
	$status_text = "<span style='color:red; font-weight:bold;'>Unapproved</span>";
	if(!empty($po["approved_datetime"]))
	{
		$status_text = "<span style='color:green; font-weight:bold;'>Approved</span>";
	}
	
?>

<?php $attributes = array('id' => 'lock_expense_form'); ?>
<?=form_open('expenses/lock_expense',$attributes)?>
	<input type="hidden" id="lock_expense_id" name="lock_expense_id" value="<?=$expense['id']?>"/>
	<input type="hidden" id="po_action" name="po_action" value="<?=$po_action?>"/>
	<input type="hidden" id="po_id" name="po_id" value="<?=$po['id']?>"/>
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
		<tr id="lock_description_row">
			<td style="width:185px;">
				PO Status
			</td>
			<td>
				<?=$status_text?>
			</td>
		</tr>
	</table>
</form>