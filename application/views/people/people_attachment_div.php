<?php $attributes = array('id' => 'upload_file_form', 'name'=>'upload_file_form', 'target'=>'_blank'); ?>
<?=form_open_multipart('people/upload_people_attachment',$attributes)?>
	<input type="hidden" id="entity_id" name="entity_id" value="<?=$entity['id']?>"/>
	<input type="hidden" id="entity_type" name="entity_type" value="<?=$entity_type?>"/>
	<table style="width: 370px; margin:auto; margin-top:15px;">
		<tr>
			<td style="vertical-align:middle; color:red; width:5px;">
			</td>
			<td style="vertical-align:middle; min-width:150px;">
				Contact
			</td>
			<td style="vertical-align:middle; width:200px;">
			<?php if($entity_type == "driver"): ?>
				<?=$entity['client_nickname']?>
			<?php else: ?>
				<?=$entity['company_name']?>
			<?php endif?>
			</td>
		</tr>
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
				<input type="file" id="people_attachment_file" name="attachment_file" class="" />
			</td>
		</tr>
	</table>
</form>