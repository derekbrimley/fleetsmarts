<?php
	
	
?>
<?php $attributes = array('name'=>'record_funding_filter_form','id'=>'record_funding_filter_form', )?>
<?=form_open('expenses/load_record_funding_form',$attributes);?>
	<input type="hidden" id="allocated_expense_id" name="allocated_expense_id" value="<?=$expense["id"]?>" >
	<table style="margin-left:30px;">
		<tr id="billing_method_row" style="display:none;">
			<td style="vertical-align: middle;">Billing Method</td>
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
			<td style="vertical-align: middle;">Billed Under</td>
			<td>
				<?php echo form_dropdown('billed_under_dropdown',$billed_under_dropdown_options,"Select",'id="billed_under_dropdown" onChange="billed_under_selected()" class="left_bar_input"');?>
			</td>
			<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
				*
			</td>
		</tr>
	</table>
</form>	