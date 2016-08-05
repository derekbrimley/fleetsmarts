<?php $attributes = array('id' => 'upload_file_form', 'name'=>'upload_file_form', 'target'=>'_blank'); ?>
<?=form_open_multipart('logs/upload_attachment',$attributes)?>
	<input type="hidden" id="attachment_log_entry_id" name="attachment_log_entry_id" value="<?=$log_entry_id?>"/>
	<div style="font-weight:bold; width: 320px; margin-top:15px; padding-left:5px;">
		<?=$log_entry["entry_type"]?> - Log Entry <?=$log_entry_id?>
	</div>
	<table style="width: 370px; margin:auto; margin-top:15px;">
		<tr>
			<td style="vertical-align:middle; color:red; width:5px;">
				*
			</td>
			<td style="vertical-align:middle; min-width:150px;">
				Upload Type
			</td>
			<td style="vertical-align:middle; width:200px;">
			<?php echo form_dropdown("upload_type",$upload_options,"Select","id='upload_type' class='left_bar_input'");?>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:middle; color:red;">
				*
			</td>
			<td style="vertical-align:middle;">
				Attachment Name
			</td>
			<td style="vertical-align:middle;">
				<input type="text" id="attachment_name" name="attachment_name" class="left_bar_input" />
			</td>
		</tr>
		<tr>
			<td style="vertical-align:middle; color:red;">
				*
			</td>
			<td style="vertical-align:middle;">
				File
			</td>
			<td style="vertical-align:middle;">
				<input type="file" id="attachment_file" name="attachment_file" style="width:170px;" />
			</td>
		</tr>
	</table>
</form>