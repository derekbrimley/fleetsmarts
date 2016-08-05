<?php
?>
<?php $attributes = array('name'=>'me_ba_ns_form','id'=>'me_ba_ns_form', )?>
<?=form_open('expenses/me_form',$attributes);?>
	<input type="hidden" id="allocated_expense_id" name="allocated_expense_id" value="<?=$expense["id"]?>" >
	<table style="margin-left:30px;">
		<tr>
			<td style="width:185px;">Receipt Required?</td>
			<td>
				<?php
					$options = array(
						"Select" => "Select",
						"Yes" => "Yes",
						"No" => "No",
					);
				?>
				<?php echo form_dropdown('receipt_required',$options,'Select','id="receipt_required" onChange="receipt_required_selected()" style="" class="left_bar_input"');?>
			</td>
		</tr>
		<tr>
			<td style="width:185px;">Member Payable Account</td>
			<td>
				<?php echo form_dropdown('member_payable_account_id',$payable_options,'Select','id="member_payable_account_id" onChange="" style="" class="left_bar_input"');?>
			</td>
		</tr>
	</table>
</form>