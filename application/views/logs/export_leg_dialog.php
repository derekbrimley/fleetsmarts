<style>
	#leg_calc_table tr
	{
		height:15px;
	}
	
	#leg_calc_table td
	{
		border: solid 1px;
		border-color:grey;
	}
</style>

<span style="font-weight:bold;"> Leg <?=$leg_id?> </span>
<br>
<form id="leg_calc_form_<?=$leg_id?>" name="leg_calc_form_<?=$leg_id?>">
	<input type="hidden" id="leg_id" name="leg_id" value="<?=$leg_id?>"/>
</form>
<table id="leg_calc_table" style="margin:0 auto; margin-top:20px;">
	<tr>
		<td style="width:75px;">
			<?=$leg_calc["date_range"]?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["truck_rent"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["trailer_rent"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["insurance_expense"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["damage_expense"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["truck_mileage"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["trailer_mileage"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["factoring"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["bad_debt"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["map_miles"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?php 
				if($leg_calc["rate_type"] == "Bobtail")
				{
					echo round($leg_calc["map_miles"]);
				}
				else 
				{
					echo 0;
				}
			?>
		</td>
	</tr>
	<tr>
		<td>
			<?php 
				if($leg_calc["rate_type"] == "Dead Head")
				{
					echo round($leg_calc["map_miles"]);
				}
				else 
				{
					echo 0;
				}
			?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["odometer_miles"])?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["hours"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["gallons_used"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["fuel_expense"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["carrier_revenue"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["carrier_expense"],2)?>
		</td>
	</tr>
	<tr>
		<td>
			<?=round($leg_calc["carrier_profit"],2)?>
		</td>
	</tr>
</table>