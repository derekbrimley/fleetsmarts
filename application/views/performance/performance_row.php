<?php
	//$pr = get_performance_stats($pr["end_week_id"]);
	$pr_is_complete = false;
?>
<table id="log_table" style="margin-left:3px; font-size:10px;">
	<tr style="height:15px;">
		<td style="overflow:hidden; min-width:30px;  max-width:30px;" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?php if($pr_is_complete):?>
				<img style="height:16px; position:relative; bottom:0px; margin-left:5px; margin-right:5px; cursor:pointer;" src="/images/blue_box_with_upward_trend_line.png" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?php else:?>
				<img style="height:16px; position:relative; bottom:0px; margin-left:5px; margin-right:5px; cursor:pointer;" src="/images/red_box_with_downward_trend_line.png" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?php endif;?>
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:53px;  max-width:53px;" VALIGN="middle" title="" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=$pr["truck"]["truck_number"]?>
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:53px;  max-width:53px;" VALIGN="middle" title="" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=$pr["solo_or_team"]?>
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:43px;  max-width:43px;" VALIGN="middle" title="" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=$pr["fleet_manager"]["f_name"]?>
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:43px;  max-width:43px;" VALIGN="middle" title="" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=$pr["driver_manager"]["f_name"]?>
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:90px;  max-width:90px;" VALIGN="middle" title="<?=date('m/d/y H:i',strtotime($pr["start_datetime"]))?>" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=date('m/d/y H:i',strtotime($pr["start_datetime"]))?>
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:90px;  max-width:90px; padding-left:5px;" VALIGN="middle" title="<?=date('m/d/y H:i',strtotime($pr["end_datetime"]))?>" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=date('m/d/y H:i',strtotime($pr["end_datetime"]))?>
		</td>
		<?php if($pr["hours"] < 167.9 || $pr["hours"] > 168.1):?>
			<td class="ellipsis" style="overflow:hidden; min-width:40px;  max-width:40px; text-align:right; font-weight:bold; color:red;" VALIGN="middle" title="<?=$pr["hours"]?>" onclick="status_icon_clicked('<?=$pr["id"]?>')">
				<?=round($pr["hours"],1)?>
			</td>
		<?php else:?>
			<td class="ellipsis" style="overflow:hidden; min-width:40px;  max-width:40px; text-align:right;" VALIGN="middle" title="<?=$pr["hours"]?>" onclick="status_icon_clicked('<?=$pr["id"]?>')">
				<?=round($pr["hours"],1)?>
			</td>
		<?php endif;?>
		<td class="ellipsis" style="overflow:hidden; min-width:40px;  max-width:40px; text-align:right; font-weight:bold; color:red;" VALIGN="middle" title="" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" VALIGN="middle" title="" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=number_format($pr["oor_percentage"],2)?>%
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:50px;  max-width:50px; text-align:right;" VALIGN="middle" title="" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=number_format($pr["mpg"],2)?>
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" VALIGN="middle" title="" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=number_format($pr["map_miles"])?>
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:50px;  max-width:50px; text-align:right;" VALIGN="middle" title="" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=number_format($pr["booking_rate"],2)?>
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:50px;  max-width:50px; text-align:right;" VALIGN="middle" title="<?=$pr["driver_rate"]?>" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=number_format($pr["driver_rate"],2)?>
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:65px;  max-width:65px; text-align:right;" VALIGN="middle" title="" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=number_format($pr["total_revenue"],2)?>
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:75px;  max-width:75px; text-align:right;" VALIGN="middle" title="">
			<a target="_blank" href="<?=base_url("index.php/performance/load_truck_performance_report/".$pr["end_week_id"])?>"><?=number_format($pr["driver_profit"],2)?></a>
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:75px;  max-width:75px; text-align:right;" VALIGN="middle" title="" onclick="status_icon_clicked('<?=$pr["id"]?>')">
			<?=number_format($pr["raw_profit"],2)?>
		</td>
	</tr>
</table>