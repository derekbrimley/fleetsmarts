<?php
	$row = $account["id"];
?>
<table  style="table-layout:fixed; font-size:12px;">
	<tr style="line-height:30px; font-weight:bold;">
		<td onclick="open_sub_accounts('<?=$row?>')" style="width:300px; padding-left:5px;" VALIGN="top"><?=$account["account_name"]?></td>
		<td onclick="open_sub_accounts('<?=$row?>')" style="width:300px;" VALIGN="top"><?=$account["category"]?></td>
		<td onclick="open_sub_accounts('<?=$row?>')" style="width:120px;" VALIGN="top"><?=$account["account_type"]?></td>
		<td onclick="open_sub_accounts('<?=$row?>')" style="width:120px;" VALIGN="top"><?=$account["account_class"]?></td>
		<td onclick="open_sub_accounts('<?=$row?>')" style="width:100px; font-weight:bold; text-align:right;" title="<?=$row?>" VALIGN="top"><?=number_format(get_account_balance($account["id"],true),2); ?></td>
	</tr>
</table>

