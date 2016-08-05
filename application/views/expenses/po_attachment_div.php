<?php $attributes = array('id' => 'upload_file_form', 'name'=>'upload_file_form', 'target'=>'_blank'); ?>
<?=form_open_multipart('expenses/upload_po_attachment',$attributes)?>
	<input type="hidden" id="po_id" name="po_id" value="<?=$po_id?>"/>
	<div style="font-weight:bold; width: 320px; margin-top:15px; padding-left:5px;">
		Purchase Order <?=$po_id?>
	</div>
	<table style="width: 370px; margin:auto; margin-top:15px;">
		<tr>
			<td style="vertical-align:middle; color:red; width:5px;">
				*
			</td>
			<td style="vertical-align:middle; width:100px;">
				Attachment Name
			</td>
			<td style="width:200px;">
				<input type="text" id="attachment_name" name="attachment_name" class="" />
			</td>
		</tr>
		<tr>
			<td style="vertical-align:middle; color:red;">
				*
			</td>
			<td style="vertical-align:middle;">
				File
			</td>
			<td>
				<input type="file" id="attachment_file" name="attachment_file" class="" />
			</td>
		</tr>
	</table>
</form>