<form id="upload_file_form"  enctype="multipart/form-data">
	<input type="hidden" id="trippak_id" name="trippak_id" value="<?=$trippak["id"]?>"/>
	<div style="font-weight:bold; width: 320px; margin-top:15px; padding-left:5px;">
		Attachment for Trippak: <?=$trippak["id"]?>
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
				<input type="file" id="attachment_file" name="attachment_file" class="left_bar_input" />
			</td>
		</tr>
	</table>
</form>