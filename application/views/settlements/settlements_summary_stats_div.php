<span style="font-weight:bold;">Statements</span>
<span style="width:22px; display:inline-block; float:right;">
	<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px;" />
	<img id="refresh_list" name="refresh_list" src="/images/refresh.png" title="Refresh Log" style="display:none; cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_list()" />
</span>
<span id="booking_stats_box_4" style="font-size:13px; float:right; margin-left:15px; margin-right:15px;">
	Kick In: <span style="font-size:16">$<?=number_format($summary_stats["total_kick_in"],2)?></span>
</span>
<span id="booking_stats_box_4" style="font-size:13px; float:right; margin-left:15px;">
	Earned: <span style="font-size:16">$<?=number_format($summary_stats["total_earned"],2)?></span>
</span>
<span id="booking_stats_box_4" style="font-size:13px; float:right; margin-left:15px;">
	Reserve: <span style="font-size:16">$<?=number_format($summary_stats["total_reserve"],2)?></span>
</span>
<span id="booking_stats_box_4" style="font-size:13px; float:right; margin-left:15px;">
	$Fuel: <span style="font-size:16">$<?=number_format($summary_stats["avg_fuel_price"],2)?></span>
</span>
<span id="booking_stats_box_1" style="font-size:13px; float:right; margin-left:15px;">
	OOR: <span style="font-size:16"><?=number_format($summary_stats["avg_oor"],2)?>%</span>
</span>
<span id="booking_stats_box_2" style="font-size:13px; float:right; margin-left:15px;">
	MPG: <span style="font-size:16"><?=number_format($summary_stats["avg_mpg"],2)?></span>
</span>
<span id="booking_stats_box_3" style="font-size:13px; float:right; margin-left:15px;">
	Miles/Day: <span style="font-size:16"><?=number_format($summary_stats["avg_miles"])?></span>
</span>