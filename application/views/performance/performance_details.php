<style>	
</style>

<?php
	$pr_id = $pr["id"];

	$solo_or_team =$pr["solo_or_team"];
?>

<script>
</script>

<div style="min-height:95px;">
	<div style="width:20px; height:45px; float:right;">
		<img id="refresh_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:13px; position:relative; left:0px;" src="/images/refresh.png" title="Refresh" onclick="this.src='/images/loading.gif'; open_row_details('<?=$pr["id"]?>')"/>
		<img id="edit_icon" class="details_<?=$pr_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:0px;" src="/images/edit.png" title="Edit" onclick="edit_performance_review('<?=$pr["id"]?>')"/>
		<img id="save_icon_<?=$pr_id?>" class="edit_<?=$pr_id?>" style="display:none; margin-bottom:13px; margin-right:15px; cursor:pointer; height:14px; position:relative; left:1px;" src="/images/save.png" title="Save" onclick="save_performance_details('<?=$pr["id"]?>');"/>
	</div>
	<div class="heading">
		Performance Review Details
	</div>
	<hr style="width:910;">
	<div style="font-size:12px;">
		<form id="pr_details_form_<?=$pr_id?>">
			<input type="hidden" id="" name="pr_id" value="<?=$pr_id?>"/>
			<table style="margin-top:5px;">
				<tr>
					<td style="width:100px; font-weight:bold;">
						Solo or Team
					</td>
					<td style="width:150px;">
						<span class="details_<?=$pr_id?>"><?=$solo_or_team?></span>
						<?php
							$options = array(
								"Select" => "Select",
								"Solo" => "Solo",
								"Team" => "Team",
							);
						?>
						<?php echo form_dropdown("solo_or_team",$options,"$solo_or_team","id='solo_or_team_$pr_id' class='edit_$pr_id' style='width:70px; height:24px; font-size:12px; position:relative; bottom:5px; display:none;'");?>
					</td>
					<td style="width:100px; font-weight:bold;">
						Fleet Manager
					</td>
					<td style="width:150px;">
						<span class="details_<?=$pr["id"]?>"><?=$pr["fleet_manager"]["f_name"]?></span>
						<?php echo form_dropdown("pr_fm",$fleet_manager_dropdown_options,$pr["fm_id"],"id='pr_fm_$pr_id' class='edit_$pr_id' style='width:125px; height:24px; font-size:12px; position:relative; bottom:5px; display:none;'");?>
					</td>
					<td style="width:100px; font-weight:bold;">
						Driver Manager
					</td>
					<td style="width:150px;">
						<span class="details_<?=$pr["id"]?>"><?=$pr["driver_manager"]["f_name"]?></span>
						<?php echo form_dropdown("pr_dm",$driver_manager_dropdown_options,$pr["dm_id"],"id='pr_dm_$pr_id' class='edit_$pr_id' style='width:125px; height:24px; font-size:12px; position:relative; bottom:5px; display:none;'");?>
					</td>
					<td style="width:160px; text-align:right;">
						<a target="_blank" href="<?=base_url("index.php/performance/load_truck_performance_report/$log_entry_id")?>">Truck Statement</a>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div class="heading">
		Loads Review
	</div>
	<hr style="width:910;">
	<div style="font-size:12px;">
		<table>
			<tr style="font-weight:bold;">
				<td style="width:80px;">
					Load
				</td>
				<td style="width:80px; padding-left:5px;">
					Broker
				</td>
				<td style="width:165px; padding-left:5px;">
					Pick
				</td>
				<td style="width:165px; padding-left:5px;">
					Drop
				</td>
				<td style="width:65px; text-align:right;">
					Total Miles
				</td>
				<td style="width:55px; text-align:right;">
					Rate
				</td>
				<td style="width:70px; text-align:right;">
					Rate/Mile
				</td>
				<td style="width:80px; text-align:right;">
					% on Week
				</td>
				<td style="width:60px; text-align:right;">
					Miles
				</td>
				<td style="width:73px; text-align:right;">
					Revenue
				</td>
			</tr>
			<?php if(!empty($pr_stats["loads_for_week"])):?>
				<?php foreach($pr_stats["loads_for_week"] as $load_for_week): ?>
					<?php
						$rate_style = "";
						$rate_title = "Funded";
						if($load_for_week["rate_source"] == "expected_revenue")
						{
							$rate_style = "color:orange;";
							$rate_title = "Expected";
						}
						
						$miles_style = "";
						$miles_title = "";
						if($load_for_week["miles_source"] == "expected_miles")
						{
							$miles_style = "color:orange;";
							$miles_title = "Expected";
						}
					?>
					<tr>
						<td class="ellipsis" style="min-width:80px;  max-width:80px;" title="<?=$load_for_week["load_number"]?>">
							<?=$load_for_week["load_number"]?>
						</td>
						<td class="ellipsis" style="min-width:80px;  max-width:80px; padding-left:5px;" title="<?=$load_for_week["broker_name"]?>">
							<?=$load_for_week["broker_name"]?>
						</td>
						<td class="ellipsis" style="min-width:165px;  max-width:165px; padding-left:5px;" title="<?=$load_for_week["pick"]?>">
							<?=$load_for_week["pick"]?>
						</td>
						<td class="ellipsis" style="min-width:165px;  max-width:165px; padding-left:5px;" title="<?=$load_for_week["drop"]?>">
							<?=$load_for_week["drop"]?>
						</td>
						<td style="text-align:right; <?=$miles_style?>" title="<?=$miles_title?>">
							<?=number_format($load_for_week["map_miles"])?>
						</td>
						<td style="text-align:right; <?=$rate_style?>" title="<?=$rate_title?>">
							<?=number_format($load_for_week["rate"])?>
						</td>
						<td style="text-align:right;">
							<?=number_format($load_for_week["rate_per_mile"],2)?>
						</td>
						<td style="text-align:right;" title="">
							<?=number_format($load_for_week["percentage_on_week"]*100,2)?>%
						</td>
						<td style="text-align:right;">
							<?=number_format($load_for_week["percentage_on_week"]*$load_for_week["map_miles"])?>
						</td>
						<td style="text-align:right;">
							<?=number_format($load_for_week["revenue"],2)?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else:?>
				<tr><td colspan="4">There are no loads for this truck</td></tr>
			<?php endif;?>
		</table>
	</div>
	<div class="heading">
		Commission Checklist
	</div>
	<hr style="width:910;">
	<table style="float:left;">
		<tr style="font-weight:bold;">
			<td style="width:200px;">
				Logs
			</td>
			<td style="width:200px;">
				BoLs
			</td>
		</tr>
		<?php if(!empty($pr_stats["loads_for_week"])):?>
			<?php foreach($pr_stats["loads_for_week"] as $load_for_week): ?>
				<tr>
					<td>
						<?php if($load_for_week["miles_source"] == "expected_miles"):?>
							<img id="" class="" style="height:14px; position:relative; left:1px; top:2px;" src="/images/empty_red_box.png" title="Incomplete" onclick=""/>
						<?php else:?>
							<img id="" class="" style="height:14px; position:relative; left:1px; top:2px;" src="/images/green_box_w_checkmark.png" title="Complete!" onclick=""/>
						<?php endif;?>
						<?=$load_for_week["load_number"]?>
					</td>
					<td>
						<?php if($load_for_week["rate_source"] == "expected_revenue"):?>
							<img id="" class="" style="height:14px; position:relative; left:1px; top:2px;" src="/images/empty_red_box.png" title="Incomplete" onclick=""/>
						<?php else:?>
							<img id="" class="" style="height:14px; position:relative; left:1px; top:2px;" src="/images/green_box_w_checkmark.png" title="Complete!" onclick=""/>
						<?php endif;?>
						<?=$load_for_week["load_number"]?>
					</td>
				</tr>
			<?php endforeach;?>
		<?php endif;?>
	</table>
	<table style="float:left;">
		<tr style="font-weight:bold;">
			<td style="">
				Shift Reports
			</td>
		</tr>
		<?php if(empty($shift_report_log_entries)):?>
		<tr>
			<td>
				<img id="" class="" style="height:14px; position:relative; left:1px; top:2px;" src="/images/empty_red_box.png" title="Incomplete" onclick=""/>
				All shift reports complete
			</td>
		</tr>
		<?php else:?>
			<?php foreach($shift_report_log_entries as $sr_log_entry): ?>
				<?php
					//GET SHIFT REPORT
					$where = null;
					$where["log_entry_id"] = $sr_log_entry["id"];
					$shift_report = db_select_shift_report($where);
					
					if(!empty($shift_report["client_id"]))
					{
						//GET DRIVER
						$where = null;
						$where["id"] = $shift_report["client_id"];
						$client = db_select_client($where);
						
						$driver_text = $client["client_nickname"];
					}
					else
					{
						$driver_text = "Driver?";
					}
					
					$shift_report_is_complete = shift_report_is_complete($shift_report);
				?>
				<tr>
					<td>
						<?php if($shift_report_is_complete["is_complete"]):?>
							<img id="" class="" style="height:14px; position:relative; left:1px; top:2px;" src="/images/green_box_w_checkmark.png" title="Complete!" onclick=""/>
						<?php else:?>
							<img id="" class="" style="height:14px; position:relative; left:1px; top:2px;" src="/images/empty_red_box.png" title="<?=$shift_report_is_complete["message"]?>" onclick=""/>
						<?php endif;?>
						<?=$driver_text?>
					</td>
					<td style="text-align:right; padding-top:4px; padding-left:5px;">
						<?=date("m/d/y",strtotime($log_entry["entry_datetime"]))?>
					</td>
				</tr>
			<?php endforeach;?>
		<?php endif;?>
	</table>
	<div style="clear:both;"></div>
</div>