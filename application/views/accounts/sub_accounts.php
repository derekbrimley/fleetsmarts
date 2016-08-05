<table style="margin-top:15px; margin-left:20px; font-size:12px;">
	<tr>
		<td onclick="" style="width:285px;" VALIGN="top"><span class="link" onclick="load_account_details(<?=$parent_account["id"]?>);"><?=$parent_account["account_name"]?></span></td>
		<td onclick="" style="width:300px;" VALIGN="top"><?=$parent_account["category"]?></td>
		<td onclick="" style="width:120px;" VALIGN="top"><?=$parent_account["account_type"]?></td>
		<td onclick="" style="width:220px; text-align:right;" title="<?=$parent_account["id"]?>" VALIGN="top"><?=number_format(get_account_balance($parent_account["id"]),2); ?></td>
	</tr>
	<?php if(!empty($sub_accounts)):?>
		<?php foreach($sub_accounts as $account):?>
			<tr>
				<td onclick="" style="width:285px;" VALIGN="top"><span class="link" onclick="load_account_details(<?=$account["id"]?>);"><?=$account["account_name"]?></span></td>
				<td onclick="" style="width:300px;" VALIGN="top"><?=$account["category"]?></td>
				<td onclick="" style="width:120px;" VALIGN="top"><?=$account["account_type"]?></td>
				<td onclick="" style="width:220px; text-align:right;" title="<?=$account["id"]?>" VALIGN="top"><?=number_format(get_account_balance($account["id"]),2); ?></td>
			</tr>
		<?php endforeach;?>
	<?php endif;?>
</table>
