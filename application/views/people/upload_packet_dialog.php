
<?php $attributes = array('id' => 'upload_packet_form', 'name'=>'upload_packet_form', 'target'=>'_blank'); ?>
<?=form_open_multipart('people/upload_packet',$attributes)?>
	<input type="hidden" id="company_id" name="company_id" value="<?=$company["id"]?>"/>
	<div style="font-weight:bold; width: 320px; margin:auto; margin-top:15px; padding-left:5px;">
		Packet for <?=$company["company_name"]?>
	</div>
	<table style="width: 320px; margin:auto; margin-top:15px;">
		<tr>
			<td style="vertical-align:middle; color:red; width:5px;">
				*
			</td>
			<td style="vertical-align:middle; width:100px;">
				Packet
			</td>
			<td style="width:200px;">
				<input type="file" id="packet" name="packet" class="" />
			</td>
		</tr>
	</table>
</form>


