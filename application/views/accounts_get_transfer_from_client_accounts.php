<table style="margin-left:30px;">
	<tr id="transfer_from_client_account_row" style="display:none;">
		<td style="width:185px; vertical-align: middle;">
			From Client Account
		</td>
		<td>
			<?php echo form_dropdown('transfer_from_client_account_dropdown',$client_accounts_dropdown_options,"Select",'id="transfer_from_client_account_dropdown" onChange="$(\'#transfer_to_client_row\').show()" class="left_bar_input"');?>
		</td>
		<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
			*
		</td>
	</tr>
</table>