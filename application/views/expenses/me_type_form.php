<?php
?>
<?php $attributes = array('name'=>'me_type_form','id'=>'me_type_form', )?>
<?=form_open('expenses/me_type_selected',$attributes);?>
	<input type="hidden" id="allocated_expense_id" name="allocated_expense_id" value="<?=$expense["id"]?>" >
	<table style="margin-left:30px;">
		<tr>
			<td style="width:185px;">Member Expense Type</td>
			<td>
				<?php
					$options = array(
						"Select" => "Select",
						"BA - Non-Standard" => "BA - Non-Standard",
						"BA - Standard" => "BA - Standard",
						"Personal Advance" => "Personal Advance"
					);
				?>
				<?php echo form_dropdown('me_type',$options,'Select','id="me_type" onChange="me_type_selected()" style="" class="left_bar_input"');?>
			</td>
		</tr>
	</table>
</form>
<div id="member_expense_form_div">
	<!-- AJAX GOES HERE !-->
</div>	