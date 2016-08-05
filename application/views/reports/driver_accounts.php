<script>
	$("#scrollable_content").height($("#body").height() - 195);
	
	$("#edit_cell_form").submit(function (e) {
        e.preventDefault(); //prevent default form submit
		save_edit_cell();
    });
</script>

<div id="main_content_header">
	<div id="plain_header">
		<div style="text-align:right; width: 130px; float:right; margin-right:0px; font-size:16px;"><?=number_format($total_total,2)?> </div>
		<div style="text-align:right; width: 130px; float:right; font-size:16px;"><?=number_format($total_reserve,2)?></div>
		<div style="text-align:right; width: 130px; float:right; font-size:16px;"><?=number_format($total_damage,2)?></div>
		<div style="text-align:right; width: 160px; float:right; font-size:16px;"><?=number_format($total_de,2)?></div>
		<div style="text-align:right; width: 130px; float:right; font-size:16px;"><?=number_format($total_baha,2)?></div>
		<div style="text-align:right; width: 130px; float:right; font-size:16px;"><?=number_format($total_pay,2)?></div>
		<div style="float:left; font-weight:bold;">Driver Accounts</div>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px; margin-left:10px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:200px;" VALIGN="top">Driver</td>
		<td style="width:100px; text-align:right;" VALIGN="top">Pay</td>
		<td style="width:130px; text-align:right;" VALIGN="top">BAHA</td>
		<td style="width:160px; text-align:right;" VALIGN="top">Driver Equipment</td>
		<td style="width:130px; text-align:right;" VALIGN="top">Damage</td>
		<td style="width:130px; text-align:right;" VALIGN="top">Reserve</td>
		<td style="width:130px; text-align:right;" VALIGN="top">Total</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<table  style="table-layout:fixed; margin:5px; margin-left:10px; font-size:12px;">
		<?php foreach($driver_account_balances as $dbalances):?>
			<tr class="" style="line-height:30px;">
				<td style="width:200px;" VALIGN="top"><?=$dbalances["client_nickname"]?></td>
				<td style="width:100px; text-align:right;" VALIGN="top" title="<?=$dbalances["pay_account_id"]?>"><?=number_format($dbalances["pay_balance"],2)?></td>
				<td style="width:130px; text-align:right;" VALIGN="top" title="<?=$dbalances["baha_account_id"]?>"><?=number_format($dbalances["baha_balance"],2)?></td>
				<td style="width:160px; text-align:right;" VALIGN="top" title="<?=$dbalances["de_account_id"]?>"><?=number_format($dbalances["de_balance"],2)?></td>
				<td style="width:130px; text-align:right;" VALIGN="top" title="<?=$dbalances["damage_account_id"]?>"><?=number_format($dbalances["damage_balance"],2)?></td>
				<td style="width:130px; text-align:right;" VALIGN="top" title="<?=$dbalances["reserve_account_id"]?>"><?=number_format($dbalances["reserve_balance"],2)?></td>
				<td style="width:130px; text-align:right;" VALIGN="top"><?=number_format($dbalances["total_balance"],2)?></td>
			</tr>
		<?php endforeach;?>
	</table>
</div>