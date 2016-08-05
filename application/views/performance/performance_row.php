<?php
				//$pr_stats = get_performance_stats($pr["end_week_id"]);
				
			?>
				<table id="log_table" style="margin-left:3px; font-size:10px;">
						<tr style="height:15px;">
							<td style="overflow:hidden; min-width:30px;  max-width:30px;">
								<?php if($pr_is_complete):?>
									<img style="height:16px; position:relative; bottom:0px; margin-left:5px; margin-right:5px; cursor:pointer;" src="/images/blue_box_with_upward_trend_line.png" onclick="status_icon_clicked('<?=$pr["id"]?>')">
								<?php else:?>
									<img style="height:16px; position:relative; bottom:0px; margin-left:5px; margin-right:5px; cursor:pointer;" src="/images/red_box_with_downward_trend_line.png" onclick="status_icon_clicked('<?=$pr["id"]?>')">
								<?php endif;?>
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:53px;  max-width:53px;" VALIGN="middle" title="">
								<?=$pr["truck"]["truck_number"]?>
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:53px;  max-width:53px;" VALIGN="middle" title="">
								<?=$pr["solo_or_team"]?>
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:43px;  max-width:43px;" VALIGN="middle" title="">
								<?=$pr["fleet_manager"]["f_name"]?>
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:43px;  max-width:43px;" VALIGN="middle" title="">
								<?=$pr["driver_manager"]["f_name"]?>
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:90px;  max-width:90px;" VALIGN="middle" title="<?=$pr_stats["start"]?>">
								<?=$pr_stats["start"]?>
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:90px;  max-width:90px; padding-left:5px;" VALIGN="middle" title="<?=$pr_stats["end"]?>">
								<?=$pr_stats["end"]?>
							</td>
							<?php if($pr_stats["hours"] < 167.9 || $pr_stats["hours"] > 168.1):?>
								<td class="ellipsis" style="overflow:hidden; min-width:40px;  max-width:40px; text-align:right; font-weight:bold; color:red;" VALIGN="middle" title="<?=$pr_stats["hours"]?>">
									<?=round($pr_stats["hours"],1)?>
								</td>
							<?php else:?>
								<td class="ellipsis" style="overflow:hidden; min-width:40px;  max-width:40px; text-align:right;" VALIGN="middle" title="<?=$pr_stats["hours"]?>">
									<?=round($pr_stats["hours"],1)?>
								</td>
							<?php endif;?>
							<?php if($shift_report_hours != $pr_stats["hours"]):?>
								<td class="ellipsis" style="overflow:hidden; min-width:40px;  max-width:40px; text-align:right; font-weight:bold; color:red;" VALIGN="middle" title="<?=$shift_report_hours?>">
									<?=round($shift_report_hours,1)?>
								</td>
							<?php else:?>
								<td class="ellipsis" style="overflow:hidden; min-width:40px;  max-width:40px; text-align:right;" VALIGN="middle" title="<?=$shift_report_hours?>">
									<?=round($shift_report_hours,1)?>
								</td>
							<?php endif;?>
							<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" VALIGN="middle" title="">
								<?=number_format($pr_stats["oor"],2)?>%
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:50px;  max-width:50px; text-align:right;" VALIGN="middle" title="">
								<?=number_format($pr_stats["mpg"],2)?>
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" VALIGN="middle" title="">
								<?=number_format($pr_stats["map_miles"])?>
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:50px;  max-width:50px; text-align:right;" VALIGN="middle" title="">
								<?=number_format($pr_stats["rate_per_mile"],2)?>
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:50px;  max-width:50px; text-align:right;" VALIGN="middle" title="<?=$pr_stats["carrier_rate"]?>">
								<?=number_format($pr_stats["carrier_rate"],2)?>
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:65px;  max-width:65px; text-align:right;" VALIGN="middle" title="">
								<?=number_format($pr_stats["total_revenue"],2)?>
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:75px;  max-width:75px; text-align:right;" VALIGN="middle" title="">
								<a target="_blank" href="<?=base_url("index.php/performance/load_truck_performance_report/".$pr["end_week_id"])?>"><?=number_format($pr_stats["carrier_profit"],2)?></a>
							</td>
							<td class="ellipsis" style="overflow:hidden; min-width:75px;  max-width:75px; text-align:right;" VALIGN="middle" title="">
								<?=number_format($pr_stats["raw_profit"],2)?>
							</td>
						</tr>
					</table>