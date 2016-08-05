<?php $attributes = array('name'=>'expense_type_form','id'=>'expense_type_form', )?>
<?=form_open('expenses/expense_type_form_selected',$attributes);?>
	<input type="hidden" id="allocated_expense_id" name="allocated_expense_id" value="<?=$expense["id"]?>" >
	<div style="text-align:center; background-color:#dddddd; padding:5px;">
		<?=$expense["description"]?>
	</div>
	<?php if(!empty($pos)): ?>
		<?php foreach($pos as $po): ?>
			<div style="text-align:center; background-color:#dddddd; padding:5px;">
				<?=$po["po_log"]?>
			</div>
		<?php endforeach ?>
	<?php endif ?>
	<table style="margin-left:30px; margin-top:20px;">
		<tr>
			<td style="width:185px;">Transaction Type</td>
			<td>
				<?php 
					//GET EXPENSE OWNER
					$where = null;
					$where["id"] = $expense["company_id"];
					$exp_owner_company = db_select_company($where);
					
					if($exp_owner_company["category"] == "Leasing")
					{
						$options = array(
								'Select' => 'Select',
								'Invoice Paid'    => 'Bill Paid',
								'Business Expense' => 'Business Expense',
								'Cash to Cash Transfer'    => 'Cash to Cash Transfer',
								'Invoice Payment Received'    => 'Invoice Payment Received',
								'Ticket Expense' => 'Ticket Expense',
						); 
					}
					else if($exp_owner_company["category"] == "Coop")
					{
						$options = array(
								'Select' => 'Select',
								'Invoice Paid'    => 'Bill Paid',
								'Business Expense' => 'Business Expense',
								'Cash to Cash Transfer'    => 'Cash to Cash Transfer',
								'Fuel Purchase' => 'Fuel Purchase',
								'Invoice Payment Received'    => 'Invoice Payment Received',
								'Load Payment Received'    => 'Freight Payment Received',
								'Member Expense' => 'Member Expense',
						); 
					}
					else
					{
						$options = array(
								'Select' => 'Select',
								'Invoice Paid'    => 'Bill Paid',
								'Business Expense' => 'Business Expense',
								'Cash to Cash Transfer'    => 'Cash to Cash Transfer',
								'Invoice Payment Received'    => 'Invoice Payment Received',
						); 
					}
				?>
				<?php echo form_dropdown('transaction_type_dropdown',$options,"Select",'id="transaction_type_dropdown" onChange="transaction_type_selected()" class="left_bar_input"');?>
			</td>
			<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
				*
			</td>
		</tr>
	</table>
</form>
<div id="transaction_form_div" name="transaction_form_div">
	<!-- AJAX GOES HERE!-->
</div>
