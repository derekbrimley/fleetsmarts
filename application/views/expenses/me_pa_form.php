<?php
?>
<?php $attributes = array('name'=>'me_pa_form','id'=>'me_pa_form', )?>
<?=form_open('expenses/me_form',$attributes);?>
	<input type="hidden" id="allocated_expense_id" name="allocated_expense_id" value="<?=$expense["id"]?>" >
	<table style="margin-left:30px;">
		<tr>
			<td style="width:185px;">Member Payable Account</td>
			<td>
				<?php echo form_dropdown('member_payable_account_id',$payable_options,'Select','id="member_payable_account_id" onChange="" style="" class="left_bar_input"');?>
			</td>
		</tr>
		<tr>
			<td style="width:185px;">Revenue Account for PA Fee</td>
			<td>
				<?php echo form_dropdown('rev_account_id',$rev_options,'Select','id="rev_account_id" onChange="" style="" class="left_bar_input"');?>
			</td>
		</tr>
	</table>
</form>