<?php $attributes = array('id' => 'upload_signature_form', 'name'=>'upload_signature_form', 'target'=>'_blank'); ?>
<?=form_open_multipart('people/upload_signatures',$attributes)?>
	<input type="hidden" id="person_id" name="person_id" value="<?=$person["id"]?>"/>
	<div style="font-weight:bold; width: 320px; margin:auto; margin-top:15px; padding-left:5px;">
		<?=$person["full_name"]?>
	</div>
	<table style="width: 320px; margin:auto; margin-top:15px;">
		<tr>
			<td style="vertical-align:middle; color:red; width:5px;">
				*
			</td>
			<td style="vertical-align:middle; width:100px;">
				E Signature
			</td>
			<td style="width:200px;">
				<input type="file" id="e_signature" name="e_signature" class="" />
			</td>
		</tr>
		<tr>
			<td style="vertical-align:middle; color:red;">
				*
			</td>
			<td style="vertical-align:middle;">
				E Initials
			</td>
			<td>
				<input type="file" id="e_initials" name="e_initials" class="" />
			</td>
		</tr>
	</table>
</form>