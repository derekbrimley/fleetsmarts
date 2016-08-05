<button class="jq_button" style="display:inline; width:80px;" onclick="save_edit_cell()">Save</button>
<?php echo form_dropdown('cell_edit_dropdown',$options,"Select","id='cell_edit_dropdown' style='margin-left:10px;width:855px;' onchange='cell_edit_dropdown_changed()' ");?>
<div style="cursor:pointer; display:inline; color:gray; margin-left:5px;" onclick="cancel_edit_cell()"> X </div>
