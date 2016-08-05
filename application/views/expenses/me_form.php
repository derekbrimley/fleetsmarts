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
			<td style="width:185px;">Member</td>
			<td>
				<?php echo form_dropdown('member_relationship_id',$member_options,'Select','id="member_relationship_id" onChange="member_selected()" style="" class="left_bar_input"');?>
			</td>
		</tr>
		<tr id="payment_method_row" style="display:none;">
			<td style="">Payment Method</td>
			<td>
				<?php
					$options = array(
						"Select" => "Select",
						"Personal Advance" => "Personal Advance",
						"FleetProtect" => "FleetProtect",
						"Next Settlement" => "Next Settlement",
						"Receipt Required" => "Receipt Required",
					);
				?>
				<?php echo form_dropdown('payment_method',$options,'Select','id="payment_method" onChange="payment_method_selected()" style="" class="left_bar_input"');?>
			</td>
		</tr>
		<tr id="owner_row" style="display:none;">
			<td style="">Owner</td>
			<td>
				<?=$owner_text?>
			</td>
		</tr>
		<tr id="category_row" style="display:none;">
			<td style="">Category</td>
			<td>
				<?=$category_text?>
			</td>
		</tr>
	</table>
	<div id="save_to_continue_div" style="display:none; margin-left:30px; margin-top:100px; font-style:italic;">
		Press Save to continue
	</div>
</form>	