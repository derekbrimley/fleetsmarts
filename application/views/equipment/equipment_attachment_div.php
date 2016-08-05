<?php $attributes = array('id' => 'upload_file_form', 'name'=>'upload_file_form', 'target'=>'_blank'); ?>
<?=form_open_multipart('equipment/upload_equipment_attachment',$attributes)?>
	<input type="hidden" id="equipment_id" name="equipment_id" value="<?=$equipment['id']?>"/>
	<input type="hidden" id="attachment_equipment_type" name="attachment_equipment_type" value="<?=$equipment_type?>"/>
	<div style="font-weight:bold; width: 320px; margin-top:15px; padding-left:5px;">
		<?=ucfirst($equipment_type)?> # <?=$equipment_number?>
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
			<td style="vertical-align:middle; color:red; width:5px;">
				*
			</td>
			<td style="vertical-align:middle; width:120px;">
				Attachment Name
			</td>
			<td style="width:200px; padding-top:5px;">
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
			<td style=" padding-top:5px;">
				<input type="file" id="equipment_attachment_file" name="equipment_attachment_file" class="left_bar_input" />
			</td>
		</tr>
	</table>
</form>