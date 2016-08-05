<div>
	<button class="jq_button" style="display:inline;width:80px;">Save</button>
	<?php $attributes = array('name'=>'edit_cell_form','id'=>'edit_cell_form', )?>
	<?=form_open('loads/edit_cell',$attributes);?>
		<input type="hidden" id="log_entry_id" name="log_entry_id" value="<?=$log_entry_id?>">
		<input type="hidden" id="field_name" name="field_name" value="<?=$field_name?>">
		<input type="text" id="cell_value" name="cell_value" style="margin-left:34px;width:700px" value="<?=$cell_value?>">
		<input type="hidden" id="gps_city" name="gps_city" value="">
		<input type="hidden" id="gps_state" name="gps_state" value="">
	</form>
</div>