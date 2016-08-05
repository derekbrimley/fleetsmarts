<?php $attributes = array('id' => 'upload_contract_form', 'name'=>'upload_contract_form', 'target'=>'_blank'); ?>
<?=form_open_multipart('people/upload_contract',$attributes)?>
	<input type="hidden" id="client_id" name="client_id" value="<?=$client["id"]?>"/>
	<div style="font-weight:bold; width: 320px; margin:auto; margin-top:15px; padding-left:5px;">
		Contract for <?=$client["client_nickname"]?>
	</div>
	<table style="width: 320px; margin:auto; margin-top:15px;">
		<tr>
			<td style="vertical-align:middle; color:red; width:5px;">
				*
			</td>
			<td style="vertical-align:middle; width:100px;">
				Contract
			</td>
			<td style="width:200px;">
				<input type="file" id="contract" name="contract" class="" />
			</td>
		</tr>
	</table>
</form>