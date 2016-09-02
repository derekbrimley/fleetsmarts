<?php
	date_default_timezone_set('America/Denver');

	$trippak_id = $trippak['id'];

	$where = null;
	$where['id'] = $trippak['carrier_id'];
	$carrier = db_select_company($where);

	$where = null;
	$where['id'] = $trippak['trailer_id'];
	$trailer = db_select_trailer($where);

	$where = null;
	$where['id'] = $trippak['driver_1_id'];
	$driver_1 = db_select_client($where);

	$where = null;
	$where['id'] = $trippak['driver_2_id'];
	$driver_2 = db_select_client($where);
?>

<table  style="table-layout:fixed; font-size:10px;">
	<tr class="" style="line-height:30px;" title="<?=$trippak["id"]?>">
		<?php if(empty($trippak['completion_datetime'])): ?>
			<td onclick="row_clicked('<?=$trippak_id?>')" style="min-width:25px; max-width:25px;padding-left:5px;" VALIGN="top">
				<img id="action_icon_<?=$trippak_id?>" style="height:16px; position:relative; top:7px; left:5px;" src="/images/load_status_1_icon.png" title=""/>
			</td>
		<?php elseif(!empty($trippak['completion_datetime'])): ?>
			<td onclick="row_clicked('<?=$trippak_id?>')" style="min-width:25px; max-width:25px;padding-left:5px;" VALIGN="top">
				<img id="action_icon_<?=$trippak_id?>" style="height:16px; position:relative; top:7px; left:5px;" src="/images/load_status_8_icon.png" title=""/>
			</td>
		<?php endif ?>
		<td class="pending_td" onclick="row_clicked('<?=$trippak_id?>')" class="ellipsis" style="min-width:100px; max-width:100px;padding-right:5px;" title="Scan Time" VALIGN="top"><?=date('m/d/y H:i',strtotime($trippak['scan_datetime']))?></td>
		<td class="fm_td" onclick="row_clicked('<?=$trippak_id?>')" class="ellipsis" style="min-width:60px; max-width:60px;padding-right:5px;" VALIGN="top" title="<?=$trippak["load_number"]?>"><?=$trippak["load_number"]?></td>
		<td class="dm_td" onclick="row_clicked('<?=$trippak_id?>')" class="ellipsis" style="min-width:150px; max-width:150px;padding-right:5px;" VALIGN="top" title="<?=$carrier["company_side_bar_name"]?>"><?=$carrier["company_side_bar_name"]?></td>
		<td onclick="row_clicked('<?=$trippak_id?>')" style="min-width:150px; max-width:150px;padding-right:5px;" class="ellipsis" VALIGN="top" title="<?=$trippak["final_drop_city"]?>"><?=$trippak["final_drop_city"]?></td>
		<td class="driver1_td" onclick="row_clicked('<?=$trippak_id?>')" class="ellipsis" style="min-width:75px; max-width:75px;padding-right:5px;" VALIGN="top" class="ellipsis" title="<?=$trippak["truck_number"]?>"><?=$trippak["truck_number"]?></td>
		<td class="driver2_td" onclick="row_clicked('<?=$trippak_id?>')" class="ellipsis" style="min-width:75px; max-width:75px;padding-right:5px;" VALIGN="top" class="ellipsis" title="<?=$trailer["trailer_number"]?>"><?=$trailer["trailer_number"]?></td>
		<td onclick="row_clicked('<?=$trippak_id?>')" class="ellipsis" style="min-width:175px; max-width:175px;padding-right:5px;" VALIGN="top" class="ellipsis" title="<?=$driver_1["client_nickname"]?>"><?=$driver_1["client_nickname"]?></td>
		<td onclick="row_clicked('<?=$trippak_id?>')" class="ellipsis" style="min-width:175px; max-width:175px;padding-right:5px;" VALIGN="top" title="<?=$driver_2['client_nickname']?>"><?=$driver_2['client_nickname']?></td>
	</tr>
</table>
