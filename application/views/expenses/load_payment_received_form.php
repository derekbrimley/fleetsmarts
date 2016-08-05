<?php $attributes = array('name'=>'load_payment_received_form','id'=>'load_payment_received_form', )?>
<?=form_open('expenses/load_payment_received',$attributes);?>
	<input type="hidden" id="allocated_expense_id" name="allocated_expense_id" value="<?=$expense["id"]?>" >
	
	<table style="margin-left:30px;">
		<tr id="billing_method_row" style="">
			<td style="width:185px;">Billing Method</td>
			<td>
				<?php 
					$options = array(
							'Select' => 'Select',
							'Factor'  => 'Factor Loads',
							'Direct Bill'    => 'Direct Bill',
					); 
				?>
				<?php echo form_dropdown('billing_method_dropdown',$options,"Select",'id="billing_method_dropdown" onChange="billing_method_selected()" class="left_bar_input"');?>
			</td>
			<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
				*
			</td>
		</tr>
		<tr id="billed_under_row" style="display:none;">
			<td style="">Billed Under</td>
			<td>
				<?php echo form_dropdown('billed_under_dropdown',$billed_under_dropdown_options,"Select",'id="billed_under_dropdown" onChange="billed_under_selected()" class="left_bar_input"');?>
			</td>
			<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
				*
			</td>
		</tr>
	</table>
	
	<div id="funded_loads_div" style="padding:10px; min-height:50px; background:#DDD; margin-top:10px; margin-bottom:10px; display:none;">
		<span style="font-weight:bold; color:black;">Invoices</span>
		<!-- AJAX GOES HERE !-->
	</div>
	
	<table style="margin-left:30px;">
		<tr id="gross_pay_row" style="display:none;">
			<td style="width:185px;">Gross Pay</td>
			<td>
				<input type="text" readonly id="funded_amount" name="funded_amount" style="text-align:right; border:none;" class="left_bar_input" value="0">
			</td>
			<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			</td>
		</tr>
	</table>
	
	<div id="deductions_div" style="display:none; padding:10px; background:#DDD; margin-top:10px; margin-bottom:10px;">
		<span style="font-weight:bold; color:black;">Deductions</span>
		<span style="float:right;"><a href="#" onclick="add_deduction()">+Add</a></span>
		<table style="">	
			<tr id="" style="font-weight:bold;">
				<td  style="width: 285px; vertical-align:bottom;">
					Notes
				</td>
				<td  style="width:100px; vertical-align:bottom; text-align:right;">
					Amount
				</td>
			</tr>
			<?php for($i = 1; $i <= 10; $i++):?>
				<?php
					if($i>1)
					{
						$display_style = "display:none;";
					}
					else
					{
						$display_style = "";
					}
				?>
				<tr id="deduction_row_<?=$i?>" style="<?=$display_style?>">
					<td  style="vertical-align: middle;">
						<input id="d_notes_<?=$i?>" name="d_notes_<?=$i?>" type="text" style="width:280px"></input>
					</td>
					<td  style="vertical-align: middle; text-align:right;">
						<input id="d_amount_<?=$i?>" name="d_amount_<?=$i?>" type="text" style="text-align:right; width:90px" onblur="calc_total_deductions()"></input>
					</td>
				</tr>
			<?php endfor;?>
			
		</table>
	</div>
	
	<table style="margin-left:30px;">
		<tr id="total_deductions_row" style="display:none;">
			<td style="width:185px;">Total Deductions</td>
			<td>
				<input type="text" readonly id="total_deduction_amount" name="total_deduction_amount" style="text-align:right; border:none;" class="left_bar_input" value="0">
			</td>
			<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			</td>
		</tr>
	</table>
	
	<div id="reimbursements_div" style="display:none; padding:10px; background:#DDD; margin-top:10px; margin-bottom:10px;">
		<span style="font-weight:bold; color:black;">Reimbursements</span>
		<span style="float:right;"><a href="#" onclick="add_reimbursement()">+Add</a></span>
		<table style="">	
			<tr id="" style="font-weight:bold;">
				<td  style="width: 280px; vertical-align:bottom;">
					Notes
				</td>
				<td  style="width:100px; vertical-align:bottom; text-align:right;">
					Amount
				</td>
			</tr>
			<?php for($i = 1; $i <= 10; $i++):?>
				<?php
					if($i>1)
					{
						$display_style = "display:none;";
					}
					else
					{
						$display_style = "";
					}
				?>
				<tr id="reimbursement_row_<?=$i?>" style="<?=$display_style?>">
					<td  style="vertical-align: middle;">
						<input id="r_notes_<?=$i?>" name="r_notes_<?=$i?>" type="text" style="width:280px"></input>
					</td>
					<td  style="vertical-align: middle; text-align:right;">
						<input id="r_amount_<?=$i?>" name="r_amount_<?=$i?>" type="text" style="text-align:right; width:90px" onblur="calc_total_reimbursements()"></input>
					</td>
				</tr>
			<?php endfor;?>
			
		</table>
	</div>
	
	<table style="margin-left:30px;">
		<tr id="total_reimbursements_row" style="display:none;">
			<td style="width:185px;">Total Reimbursements</td>
			<td>
				<input type="text" readonly id="total_reimbursement_amount" name="total_reimbursement_amount" style="text-align:right; border:none;" class="left_bar_input" value="0">
			</td>
			<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			</td>
		</tr>
	</table>
	
	<table style="margin-left:30px;">
		<tr id="calculated_total_row" style="display:none;">
			<td style="width:185px;">Calculated Total</td>
			<td>
				<input type="text" readonly id="calculated_total" name="calculated_total" style="text-align:right; border:none;" class="left_bar_input" value="0">
			</td>
			<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			</td>
		</tr>
		<tr id="cash_load_amount_row" style="display:none;">
			<td style="width:185px;">Cash Load Amount</td>
			<td>
				<input type="text" readonly id="cash_load_amount" name="cash_load_amount" style="text-align:right; border:none; color:red; font-size:18px; font-weight:bold;" class="left_bar_input" value="<?=number_format($expense["expense_amount"],2)?>">
				<input type="hidden" id="hidden_cash_load_amount" name="hidden_cash_load_amount" value="<?=$expense["expense_amount"]?>">
			</td>
			<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			</td>
		</tr>
	</table>
</form>	