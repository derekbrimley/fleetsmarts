<div>
	<div id="notes_ajax_div" style="height:245px; overflow:auto; font-size:12px;">
		<table>
			<?php if(!empty($notes)):?>
				<?php foreach($notes as $note):?>
					<?php
						//GET NOTE USER
						$where = null;
						$where["id"] = $note["user_id"];
						$note_user = db_select_user($where);
					
						$initials = substr($note_user["person"]["f_name"],0,1).substr($note_user["person"]["l_name"],0,1);
					?>
					<tr>
						<td style="padding-top:10px;">
							<?=$initials?>
						</td>
						<td style="padding-top:10px; padding-left:10px; width:85px;">
							<?=date("m/d/y H:i",strtotime($note["note_datetime"]))?>
						<td>
						<td style="padding-top:10px; padding-left:10px;">
							<?=$note["note_text"]?>
						</td>
					</tr>
				<?php endforeach;?>
			<?php endif;?>
		</table>
		<?=str_replace("\n","<br>",$load["billing_notes"]);?>
	</div>
</div>
<div style="position:absolute; bottom:0; width:95%;">
	<form id="add_note_form">
		<div style="font-size:14px;">Add Note:</div>
		<input type="hidden" id="row_id" name="row_id" value="<?=$load["id"]?>">
		<textarea style="width:100%;" rows="3" id="new_note" name="new_note" ></textarea>
	</form>
</div>
