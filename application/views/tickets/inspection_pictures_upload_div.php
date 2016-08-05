<?php $attributes = array('id' => 'upload_inspection_pic_form', 'name'=>'upload_inspection_pic_form', 'target'=>'_blank'); ?>
<?=form_open_multipart('tickets/upload_inspection_pic',$attributes)?>
	<input type="hidden" id="inspection_id" name="inspection_id" value="<?=$inspection_id?>"/>
	<input type="hidden" id="ticket_id" name="ticket_id" value="<?=$ticket_id?>"/>
	<input type="hidden" id="pic_title" name="pic_title" value="<?=$pic_title?>"/>
	<div style="font-weight:bold; width: 320px; margin-top:15px; padding-left:5px;">
		Ticket <?=$ticket_id?>
	</div>
	<table style="width: 370px; margin:auto; margin-top:15px;">
		<tr>
			<td style="vertical-align:middle; color:red; width:5px;">
				
			</td>
			<td style="vertical-align:middle; width:120px;">
				Picture
			</td>
			<td style="vertical-align:middle; width:200px; padding-top:5px;">
				<?=$pic_title_text?>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:middle; color:red;">
				*
			</td>
			<td style="vertical-align:middle;">
				File
			</td>
			<td style=" padding-top:5px;">
				<input type="file" id="attachment_file" name="attachment_file" class="" />
			</td>
		</tr>
	</table>
</form>

<!--
	CHECK OUT HOW THE UPLOAD WORKS IN expenses_view.php FOR THE FILE UPLOADS TO A PUBLIC FOLDER
	
	ONCE IN A PUBLIC FOLDER, THE FILE CAN BE EASILY CALLED AND DISPLAYED IN A THUMBNAIL TYPE REPORT
!-->