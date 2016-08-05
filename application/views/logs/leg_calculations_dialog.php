<style>
	
	#leg_calc_table td
	{
		text-align:left;
	}
	
	#leg_details_table tr
	{
		height:15px;
	}
</style>
<script>
	function open_export_dialog(leg_id)
	{
		$('#export_leg_dialog_'+leg_id).dialog('open');
	}
</script>
<div>
	<span style="font-weight:bold;">Leg <?=$leg_id?></span>
	<?php if(!empty($leg_calc["hours"])): ?>
		<div style="width:820px; margin: 0 auto;">
			<table id="leg_details_table" style="margin-top:25px; margin-bottom:25px;">
				<tr>
					<td style="width:100px;">
						Map Miles
					</td>
					<td style="width:72px; padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["map_miles"])?>
					</td>
					<td style="width:70px;">
					</td>
					<td style="width:120px;">
						Map Miles/Day
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format(($leg_calc["map_miles"])/$leg_calc["hours"]*24)?>
					</td>
					<td style="width:70px">
					</td>
					<td style="width:110px;">
						Truck Expense
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["truck_rent"] + $leg_calc["truck_mileage"],2)?>
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Truck/MM
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=@number_format(round(($leg_calc["truck_rent"] + $leg_calc["truck_mileage"])/$leg_calc["map_miles"],2),2)?>
					</td>
				</tr>
				<tr>
					<td style="">
						Odometer Miles
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["odometer_miles"])?>
					</td>
					<td style="width:70px;">
					</td>
					<td style="">
						Odometer Miles/Day
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format(($leg_calc["odometer_miles"])/$leg_calc["hours"]*24)?>
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Trailer Expense
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format(($leg_calc["trailer_rent"] + $leg_calc["trailer_mileage"]),2)?>
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Trailer/MM
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=@number_format(round(($leg_calc["trailer_rent"] + $leg_calc["trailer_mileage"])/$leg_calc["map_miles"],2),2)?>
					</td>
				</tr>
				<tr>
					<td style="">
						Rate Type
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=$leg_calc["rate_type"]?>
					</td>
					<td style="width:70px;">
					</td>
					<td style="">
						Total OOR
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=$leg_calc["odometer_miles"] - $leg_calc["map_miles"]?>
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Damage
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["damage_expense"],2)?>
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Damage/MM
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=@number_format(round($leg_calc["damage_expense"]/$leg_calc["map_miles"],2),2)?>
					</td>
				</tr>
				<tr>
					<td style="">
						Revenue Rate
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["rate"],2)?>
					</td>
					<td style="width:70px;">
					</td>
					<td style="">
						OOR %
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["oor"],2)?>
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Insurance Expense
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["insurance_expense"],2)?>
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Insurance/MM
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=@number_format(round($leg_calc["insurance_expense"]/$leg_calc["map_miles"],2),2)?>
					</td>
				</tr>
				<tr>
					<td style="">
						Carrier Revenue
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["carrier_revenue"],2)?>
					</td>
					<td style="width:70px;">
					</td>
					<td style="">
						MPG
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["mpg"],2)?>
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Bad Debt
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["bad_debt"],2)?>
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Bad Debt/MM
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=@number_format(round($leg_calc["bad_debt"]/$leg_calc["map_miles"],2),2)?>
					</td>
				</tr>
				<tr>
					<td style="">
						Carrier Expenses
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["carrier_expense"],2)?>
					</td>
					<td style="width:70px;">
					</td>
					<td style="">
						Fuel Savings
					</td>
					<td style="padding-left:10px;text-align:right;">
						
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Factoring
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["factoring"],2)?>
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Factoring/MM
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=@number_format(round($leg_calc["factoring"]/$leg_calc["map_miles"],2),2)?>
					</td>
				</tr>
				<tr>
					<td style="">
						Carrier Profit
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["carrier_profit"],2)?>
					</td>
					<td style="width:70px;">
					</td>
					<td style="">
						Hours
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["hours"],2)?>
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Fuel Expense
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=number_format($leg_calc["fuel_expense"],2)?>
					</td>
					<td style="width:70px">
					</td>
					<td style="">
						Fuel/MM
					</td>
					<td style="padding-left:10px;text-align:right;">
						<?=@number_format(round($leg_calc["fuel_expense"]/$leg_calc["map_miles"],2),2)?>
					</td>
				</tr>
			</table>
		</div>
	<?php else: ?>
		<br>
		There is no previous leg to this leg!
	<?php endif;?>
</div>
<div id="leg_calc_error_div_<?=$log_entry['id']?>">
	<!-- AJAX GOES HERE !-->
</div>
