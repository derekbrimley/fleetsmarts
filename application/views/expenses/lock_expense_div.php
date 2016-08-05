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
<div style="padding:10px;">
	<?php $attributes = array('id' => 'match_po_form'); ?>
	<?=form_open('expenses/match_po',$attributes)?>
		<input type="hidden" id="expense_id" name="expense_id" value="<?=$expense['id']?>"/>
		<div style="text-align:center; background-color:#dddddd; padding:5px;">
			<?=$expense["description"]?>
		</div>
		<table style="margin-left:30px; margin-top:20px;">
			<tr id="">
				<td style="width:185px;">
					PO Action
				</td>
				<td>
					<?php
						$options = array(
								"Select" => "Select",
								"Match PO" => "Match PO",
								"Create PO" => "Create PO",
								"Skip PO" => "Skip PO",
						);
					?>
					<?php echo form_dropdown('lock_action',$options,'Select','id="lock_action" onChange="po_action_selected()" style="" class="left_bar_input"');?>
				</td>
			</tr>
			<tr id="po_match_row" style="display:none;">
				<td style="width:185px;">
					Purchase Order
				</td>
				<td>
					<?php echo form_dropdown('po_match_id',$po_options,'Select','id="po_match_id" onChange="load_lock_expense_form()" style="" class="left_bar_input"');?>
				</td>
			</tr>
		</table>
	</form>
	<div id="lock_expense_div" style="display:none;">
		<!-- AJAX GOES HERE !-->
	</div>
</div>