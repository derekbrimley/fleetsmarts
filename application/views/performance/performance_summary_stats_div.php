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
		padding-right:85px;
	}
	
	.stat_title
	{
		font-weight:bold;
	}
</style>
<table  style="table-layout:fixed; font-size:12px; float:left;">
	<tr>
		<td class="stat_title" style="">
			Total Miles
		</td>
		<td style="width:45px; text-align:right;">
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["map_miles"])?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="width:110px;">
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
		<td class="stat_title" style="">
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
		<td class="stat_title" style="">
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
		<td class="stat_title" style="">
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
		<td class="stat_title" style="">
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
		<td class="stat_title" style="width:100px;">
			Total Trucks
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_trucks"])?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Carrier Rate
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["carrier_rate"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Booking Rate
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["book_rate"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Booking Margin
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["book_rate"] - $summary_stats["carrier_rate"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="width:100px;">
			Total Revenue
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_revenue"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Total Raw Profit
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_raw_profit"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Total Dropped
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["expected"],2)?>
		</td>
	</tr>
</table>
<table  style="table-layout:fixed; font-size:12px; float:left;">
	<tr>
		<td class="stat_title" style="width:110px;">
			Miles per Truck
		</td>
		<td class="summary_stat">
			<?php if($summary_stats["total_trucks"] != 0):?>
				<?=number_format($summary_stats["map_miles"]/$summary_stats["total_trucks"])?>
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Total Truck Profit
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["total_truck_profit"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="width:100px;">
			Real Booking Rate
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["real_booking_rate"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Real Margin
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["real_margin"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Real Revenue
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["real_revenue"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Real Raw Profit
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["real_raw_profit"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Funded
		</td>
		<td class="summary_stat">
			<?=number_format($summary_stats["funded_total"],2)?>
		</td>
	</tr>
</table>
<table  style="table-layout:fixed; font-size:12px; float:left;">
	<tr>
		<td class="stat_title" style="width:90px;">
			MPG
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			<?=number_format($summary_stats["mpg"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			OOR
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			<?=number_format($summary_stats["oor"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Nat'l Fuel Avg
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			<?=number_format($summary_stats["avg_fuel_price"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Fuel Savings
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			<?=number_format($summary_stats["average_fuel_expense"] - $summary_stats["avg_fuel_price"],2)?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Driver Quits
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			?
		</td>
	</tr>
	<tr title="Loads:<?=$summary_stats["load_count"]?> Audits:<?=$summary_stats["audit_passes"]+$summary_stats["audit_fails"]?> Pass:<?=$summary_stats["audit_passes"]?> Fail:<?=$summary_stats["audit_fails"]?>">
		<td class="stat_title" style="">
			Audit Pass%
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			<?php if($summary_stats["load_count"] != 0):?>
				<?=number_format($summary_stats["audit_passes"]/$summary_stats["load_count"])?>%
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td class="stat_title" style="">
			Collection %
		</td>
		<td class="summary_stat" style="padding-right:0px;">
			<?=number_format($summary_stats["collection_rate"],2)?>%
		</td>
	</tr>
</table>
