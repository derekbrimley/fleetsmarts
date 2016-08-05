<?php $attributes = array('name'=>'add_new_entry_form','id'=>'add_new_entry_form', )?>
<?=form_open('accounts/add_new_entry',$attributes);?>
	<input type="hidden" id="allocated_expense_id" name="allocated_expense_id" value="<?=$expense["id"]?>" >
	<div style="text-align:center; background-color:#dddddd; padding:5px;">
		<?=$expense["description"]?>
	</div>
	<div style="margin:25px; color:red;">
		Matching to Invoice Payments COMING SOON!
	</div>
	<div style="margin:25px;">
		Just click Save to continue
	</div>
	<table style="margin-left:30px; margin-top:20px;">
		<tr id="description_row" style="">
			<td style="width:185px; vertical-align: middle;">Entry Description</td>
			<td>
				<textarea id="description" name="description" class="left_bar_input"></textarea>
			</td>
			<td style="vertical-align: middle; color:white; padding-left:5px; font-weight:bold;">
				*
			</td>
		</tr>
	</table>
</form>