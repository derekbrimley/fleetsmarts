<style>
	.category
	{
		font-size:12px;
		float:right;
	}
	
	.stat
	{
		font-size:14px;
	}
	
	.summary_stat
	{
		width:50px;
		text-align:right;
		padding-right:90px;
	}
</style>
<table  style="table-layout:fixed; font-size:12px; float:left;">
	<tr>
		<td style="">
			Total Miles
		</td>
		<td style="width:45px; text-align:right;">
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["map_miles"])?>
		</td>
	</tr>
	<tr>
		<td style="width:110px;">
			Bob Tail Miles
		</td>
		<td style="width:45px; text-align:right;">
			<?php if(!empty($summary_stats["map_miles"])):?>
				<?=number_format($summary_stats["total_bobtail_miles"]/$summary_stats["map_miles"]*100,2)?>%
			<?php endif;?>
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_bobtail_miles"])?>
		</td>
	</tr>
	<tr>
		<td style="">
			Dead Head Miles
		</td>
		<td style="width:45px; text-align:right;">
			<?php if(!empty($summary_stats["map_miles"])):?>
				<?=number_format($summary_stats["total_deadhead_miles"]/$summary_stats["map_miles"]*100,2)?>%
			<?php endif;?>
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_deadhead_miles"])?>
		</td>
	</tr>
	<tr>
		<td style="">
			Light Freight Miles
		</td>
		<td style="width:45px; text-align:right;">
			<?php if(!empty($summary_stats["map_miles"])):?>
				<?=number_format($summary_stats["total_light_miles"]/$summary_stats["map_miles"]*100,2)?>%
			<?php endif;?>
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_light_miles"])?>
		</td>
	</tr>
	<tr>
		<td style="">
			Loaded Miles
		</td>
		<td style="width:45px; text-align:right;">
			<?php if(!empty($summary_stats["map_miles"])):?>
				<?=number_format($summary_stats["total_loaded_miles"]/$summary_stats["map_miles"]*100,2)?>%
			<?php endif;?>
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_loaded_miles"])?>
		</td>
	</tr>
	<tr>
		<td style="">
			Reefer Miles
		</td>
		<td style="width:45px; text-align:right;">
			<?php if(!empty($summary_stats["map_miles"])):?>
				<?=number_format($summary_stats["total_reefer_miles"]/$summary_stats["map_miles"]*100,2)?>%
			<?php endif;?>
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_reefer_miles"])?>
		</td>
	</tr>
</table>
<table  style="table-layout:fixed; font-size:12px; float:left;">
	<tr>
		<td style="width:100px;">
			Total Trucks
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_trucks"])?>
		</td>
	</tr>
	<tr>
		<td style="">
			Carrier Rate
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["carrier_rate"],2)?>
		</td>
	</tr>
	<tr>
		<td style="">
			Booking Rate
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["book_rate"],2)?>
		</td>
	</tr>
	<tr>
		<td style="">
			Booking Margin
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["book_rate"] - $summary_stats["carrier_rate"],2)?>
		</td>
	</tr>
	<tr>
		<td style="">
			Total Teams
		</td>
		<td class="summary_stat">
			<?=$summary_stats["total_teams"]?>
		</td>
	</tr>
	<tr>
		<td style="">
			Total Solos
		</td>
		<td class="summary_stat">
			<?=$summary_stats["total_solos"]?>
		</td>
	</tr>
</table>
<table  style="table-layout:fixed; font-size:12px; float:left;">
	<tr>
		<td style="width:110px;">
			Miles per Truck
		</td>
		<td class="summary_stat">
			<?php if($summary_stats["total_trucks"] != 0):?>
				<?=number_format($summary_stats["map_miles"]/$summary_stats["total_trucks"])?>
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td style="width:100px;">
			Total Revenue
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_revenue"],2)?>
		</td>
	</tr>
	<tr>
		<td style="">
			Total Raw Profit
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_raw_profit"],2)?>
		</td>
	</tr>
	<tr>
		<td style="">
			Total Truck Profit
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_truck_profit"],2)?>
		</td>
	</tr>
	<tr>
		<td style="">
			Truck Profit/Team
		</td>
		<td class="summary_stat">
			<?php if($summary_stats["total_teams"] != 0):?>
				<?=number_format($summary_stats["total_team_profit"]/$summary_stats["total_teams"],2)?>
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td style="">
			Truck Profit/Solo
		</td>
		<td class="summary_stat">
			<?php if($summary_stats["total_solos"] != 0):?>
				<?=number_format($summary_stats["total_solo_profit"]/$summary_stats["total_solos"],2)?>
			<?php endif;?>
		</td>
	</tr>
</table>
<table  style="table-layout:fixed; font-size:12px; float:left;">
	<tr>
		<td style="width:90px;">
			MPG
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			<?=number_format($summary_stats["mpg"],2)?>
		</td>
	</tr>
	<tr>
		<td style="">
			OOR
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			<?=number_format($summary_stats["oor"],2)?>
		</td>
	</tr>
	<tr>
		<td style="">
			Nat'l Fuel Avg
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			<?=number_format($summary_stats["avg_fuel_price"],2)?>
		</td>
	</tr>
	<tr>
		<td style="">
			Fuel Savings
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			<?=number_format($summary_stats["average_fuel_expense"] - $summary_stats["avg_fuel_price"],2)?>
		</td>
	</tr>
	<tr>
		<td style="">
			Miles per Team
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			<?php if($summary_stats["total_teams"] != 0):?>
				<?=number_format($summary_stats["total_team_miles"]/$summary_stats["total_teams"])?>
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td style="">
			Miles per Solo
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			<?php if($summary_stats["total_solos"] != 0):?>
				<?=number_format($summary_stats["total_solo_miles"]/$summary_stats["total_solos"])?>
			<?php endif;?>
		</td>
	</tr>
</table>
