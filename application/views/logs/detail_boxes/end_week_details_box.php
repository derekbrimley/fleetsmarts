<?php
	$row_id = $log_entry["id"];
?>
<script>
	function create_new_leg(id)
	{
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		//var this_div = $('#main_content');
		var this_div = $('#script_div_'+id);
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&event_id="+id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/create_new_leg")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					//load_log_list();
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error!")
					this_div.html(response);
				}
			}
		});//END AJAX
	}
	
	function create_fuel_estimate(id)
	{
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		//var this_div = $('#main_content');
		var this_div = $('#script_div_'+id);
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&event_id="+id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/create_fuel_estimate")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					//load_log_list();
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error!")
					this_div.html(response);
				}
			}
		});//END AJAX
	}
	
	function lock_end_week(log_entry_id)
	{
		if(confirm("Are you sure you want to lock this week?"))
		{
			//alert('hello');
			var this_div = $('#log_entry_row_'+log_entry_id);
			var dataString = "&log_entry_id="+log_entry_id;
			$("#unlocked_icon_"+log_entry_id).attr('src','/images/loading.gif');
			//TODO: REPLACE LOCK ICON WITH LOADING ICON
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/lock_event")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						//load_log_list();
						
						this_div.html(response);
						
					},
					404: function(){
						// Page not found
						alert('page not found');
						
					},
					500: function(response){
						// Internal server error
						alert("500 error!")
					}
				}
			});//END AJAX
		}
	}
	
	function details_arrow_clicked(image_id,details_div)
	{
		//alert(details_div);
		//$('#'+details_div).show();
		//alert($('#'+details_div).is(":visible"));
		if(!($('#'+details_div).is(":visible")))
		{
			
			$('#'+details_div).show();
			$('#'+image_id).attr('src','/images/hide_details.png');
			$('#'+image_id).attr('title','Close Details');
		}
		else
		{
			$('#'+details_div).hide();
			$('#'+image_id).attr('src','/images/open_details.png');
			$('#'+image_id).attr('title','Open Details');
		}
	}
	
</script>

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

<div id="script_div_<?=$log_entry_id?>">
	<!-- AJAX SCRIPT GOES HERE !-->
</div>
<div style="min-height:120px; font-size:12px;">
	<?php if(empty($log_entry["locked_datetime"])): ?>
		<div style="height: 45px; width:20px; float:right;">
			<img id="refresh_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:13px; position:relative; left:0px;" src="/images/refresh.png" title="Refresh" onclick="this.src='/images/loading.gif'; open_event_details('<?=$log_entry_id?>')"/>
			<img title="Estimate Odometer" style='display:block; margin-bottom:12px; cursor:pointer; height:13px; position:relative; bottom:0px; right:1px;' src="/images/odometer.png" onclick="estimate_odometer('<?=$log_entry_id?>','<?=$log_entry["sync_entry_id"]?>')"/>
			<?php if(empty($log_entry["sync_entry_id"])): ?>
				<img title="Attach End Leg" style='display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; bottom:1px; left:1px;' src="/images/make_new_leg.png" onclick="this.src='/images/loading.gif'; create_new_leg('<?=$log_entry_id?>')"/>
			<?php endif; ?>
			<img title="Create Fuel Estimate" style='display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; bottom:1px; left:0px;' src="/images/make_fuel_estimate.png" onclick="this.src='/images/loading.gif'; create_fuel_estimate('<?=$log_entry_id?>')"/>
			<?php if($allow_lock): ?>
				<img id="unlocked_icon_<?=$row_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/unlocked.png" title="Lock" onclick="lock_end_week('<?=$log_entry_id?>')"/>
			<?php endif; ?>
			<?php if($allow_delete): ?>
				<img id="delete_icon" style="display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/trash.png" title="Delete" onclick="delete_event('<?=$log_entry_id?>')"/>
			<?php endif; ?>
		</div>
	<?php else: ?>
		<div style="height: 45px; width:20px; float:right;">
			<img title="End Leg" style='display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; bottom:1px; left:1px;' src="/images/make_new_leg.png" onclick="create_new_leg('<?=$log_entry_id?>')"/>
		</div>
	<?php endif;?>
	<div style="margin-left:72px;">
		Notes:<span style="margin-left:73px;"><?=$log_entry["entry_notes"]?></span>
	</div>
	<br>
	<?php if(!empty($log_entry["sync_entry_id"])): ?>
		<?php if(!empty($log_entry["truck_id"]) && $previous_truck_end_week_exists): ?>
			<table style="margin-left:72px;">
				<tr>
					<td style="width:20px;">
					</td>
					<td style="font-weight:bold; width:110px;" title="Total Expenses $<?=$total_truck_expenses?>">
						<?=$log_entry["truck"]["truck_number"]?>
					</td>
					<td style="width:45px;">
						Hours
					</td>
					<td style="width:55px;">
						<?=round($total_truck_hours,1)?>
					</td>
					<td style="width:65px;">
						<a target="blank" href="<?=$map_info["route_url"]?>">Map Miles</a>
					</td>
					<td style="width:65px;">
						<?=$map_info["map_miles"]?>
					</td>
					<td style="width:90px;">
						Odometer Miles
					</td>
					<td style="width:75px;">
						<?=$odometer_miles?>
					</td>
					<td style="width:45px;">
						OOR
					</td>
					<td style="width:75px;">
						<?=@number_format((1-$map_info["map_miles"]/$odometer_miles)*100,2)?>%
					</td>
					<td style="width:65px;">
						Miles/Day
					</td>
					<td style="width:75px;">
						<?=number_format($map_info["map_miles"]/$total_truck_hours*24)?>
					</td>
			</table>
		<?php else: ?>
			<?php if(!empty($log_entry["truck_id"])): ?>
				<table style="margin-left:72px;">
					<tr>
						<td style="font-weight:bold; min-width:110px; max-width:110px;" class="ellipsis">
							<?=$log_entry["truck"]["truck_number"]?>
						</td>
						<td style="color:red; font-weight:bold;">
							The system can find the previous End Week and End Leg events for this truck
						</td>
					</tr>
				</table>
			<?php endif; ?>
		<?php endif; ?>
		<?php if(!empty($log_entry["main_driver_id"]) && $driver_1_stats["status"] == "Ready"): ?>
			<table style="margin-left:72px;">
				<tr>
					<td style="width:20px;">
						<img id="open_driver_1_details_<?=$log_entry["id"]?>" title="Open Details" style='cursor:pointer; height:12px; position:relative; bottom:0px; left:0px;' src="/images/open_details.png" onclick="details_arrow_clicked('open_driver_1_details_<?=$log_entry["id"]?>','driver_1_details_<?=$log_entry["id"]?>');"/>
					</td>
					<td style="font-weight:bold; min-width:110px; max-width:110px;" class="ellipsis">
						<?=$log_entry["main_driver"]["client_nickname"] ?>
					</td>
					<td style="width:45px;">
						Hours
					</td>
					<td style="width:55px;">
						<?=round($driver_1_stats["total_truck_hours"],1)?>
					</td>
					<td style="width:65px;">
						<a target="blank" href="<?=$driver_1_stats["map_info"]["route_url"]?>">Map Miles</a>
					</td>
					<td style="width:65px;">
						<?=$driver_1_stats["map_info"]["map_miles"]?>
					</td>
					<td style="width:90px;">
						Odometer Miles
					</td>
					<td style="width:75px;">
						<?=$driver_1_stats["total_odometer_miles"]?>
					</td>
					<td style="width:45px;">
						OOR
					</td>
					<td style="width:75px;">
						<?=@number_format((($driver_1_stats["total_odometer_miles"]-$driver_1_stats["map_info"]["map_miles"])/$driver_1_stats["map_info"]["map_miles"])*100,2)?>%
					</td>
					<td style="width:65px;">
						Miles/Day
					</td>
					<td style="width:75px;">
						<?=number_format($driver_1_stats["map_info"]["map_miles"]/$driver_1_stats["total_truck_hours"]*24)?>
					</td>
					<td style="width:85px;">
						<?php $main_driver_id = $log_entry["main_driver_id"]; ?>
						<a target="_blank" href="<?=base_url("index.php/settlements/load_driver_settlement_view/$log_entry_id/$main_driver_id")?>">Statement</a>
					</td>
				</tr>
			</table>
			<div id="driver_1_details_<?=$log_entry["id"]?>" name="driver_1_details_<?=$log_entry["id"]?>" style="margin-left:72px; margin-bottom:30px; display:none;">
				<table>
					<tr class="heading">
						<td style="width:50px;">
							Leg
						</td>
						<td style="width:130px;">
							Dates
						</td>
						<td style="width:40px; text-align:right;">
							Hours
						</td>
						<td style="width:235px; padding-left:20px;">
							Locations
						</td>
						<td style="width:70px; padding-left:10px;">
							Rate Type
						</td>
						<td style="width:70px; text-align:right;">
							Miles
						</td>
						<td style="width:50px; text-align:right;">
							Rate
						</td>
						<td style="width:90px; text-align:right;">
							Expenses
						</td>
						<td style="width:70px; text-align:right;">
							Profit
						</td>
					</tr>	
					<?php foreach($driver_1_stats["leg_calcs"] as $leg_calc): ?>
						<tr style="font-size:12px;">
							<td>
								<?=$leg_calc["leg_id"]?>
							</td>
							<td>
								<?=$leg_calc["date_range"]?>
							</td>
							<td style="text-align:right;">
								<?=number_format($leg_calc["hours"],1)?>
							</td>
							<td style="padding-left:20px;">
								<?=$leg_calc["locations"]?>
							</td>
							<td style="padding-left:10px;">
								<?=$leg_calc["rate_type"]?>
							</td>
							<td style="text-align:right;">
								<?=number_format($leg_calc["map_miles"])?>
							</td>
							<td style="text-align:right;">
								$<?=number_format($leg_calc["rate"],2)?>
							</td>
							<td style="text-align:right;">
								<?=number_format($leg_calc["carrier_expense"],2)?>
							</td>
							<td style="text-align:right;">
								<?=number_format($leg_calc["carrier_profit"],2)?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		<?php else: ?>
			<?php if($driver_1_stats["status"] == "Not Ready"): ?>
				<table style="margin-left:72px;">
					<tr>
						<td style="font-weight:bold; min-width:110px; max-width:110px;" class="ellipsis">
							<?=$log_entry["main_driver"]["client_nickname"] ?>
						</td>
						<td style="color:red; font-weight:bold;">
							The system can find the previous End Week and End Leg events for this driver
						</td>
					</tr>
				</table>
			<?php endif; ?>
		<?php endif; ?>
		<?php if(!empty($log_entry["codriver_id"])): ?>
			<?php if($driver_2_stats["status"] == "Ready"):?>
				<table style="margin-left:72px;">
					<tr>
						<td style="width:20px;">
							<img id="open_driver_2_details_<?=$log_entry["id"]?>" title="Open Details" style='cursor:pointer; height:12px; position:relative; bottom:0px; left:0px;' src="/images/open_details.png" onclick="details_arrow_clicked('open_driver_2_details_<?=$log_entry["id"]?>','driver_2_details_<?=$log_entry["id"]?>');"/>
						</td>
						<td style="font-weight:bold; min-width:110px; max-width:110px;" class="ellipsis">
							<?=$log_entry["codriver"]["client_nickname"] ?>
						</td>
						<td style="width:45px;">
							Hours
						</td>
						<td style="width:55px;">
							<?=round($driver_2_stats["total_truck_hours"],1)?>
						</td>
						<td style="width:65px;">
							<a target="blank" href="<?=$driver_2_stats["map_info"]["route_url"]?>">Map Miles</a>
						</td>
						<td style="width:65px;">
							<?=$driver_2_stats["map_info"]["map_miles"]?>
						</td>
						<td style="width:90px;">
							Odometer Miles
						</td>
						<td style="width:75px;">
							<?=$driver_2_stats["total_odometer_miles"]?>
						</td>
						<td style="width:45px;">
							OOR
						</td>
						<td style="width:75px;">
							<?=@number_format((($driver_2_stats["total_odometer_miles"]-$driver_2_stats["map_info"]["map_miles"])/$driver_2_stats["map_info"]["map_miles"])*100,2)?>%
						</td>
						<td style="width:65px;">
							Miles/Day
						</td>
						<td style="width:75px;">
							<?=@number_format($driver_2_stats["map_info"]["map_miles"]/$driver_2_stats["total_truck_hours"]*24)?>
						</td>
						<td style="width:85px;">
							<?php $codriver_id = $log_entry["codriver_id"]; ?>
							<a target="_blank" href="<?=base_url("index.php/settlements/load_driver_settlement_view/$log_entry_id/$codriver_id")?>">Statement</a>
						</td>
				</table>
				<div id="driver_2_details_<?=$log_entry["id"]?>" name="driver_2_details_<?=$log_entry["id"]?>" style="margin-left:72px; margin-bottom:30px; display:none;">
					<table>
						<tr class="heading">
							<td style="width:50px;">
								Leg
							</td>
							<td style="width:130px;">
								Dates
							</td>
							<td style="width:40px; text-align:right;">
								Hours
							</td>
							<td style="width:235px; padding-left:20px;">
								Locations
							</td>
							<td style="width:70px; padding-left:10px;">
								Rate Type
							</td>
							<td style="width:70px; text-align:right;">
								Miles
							</td>
							<td style="width:50px; text-align:right;">
								Rate
							</td>
							<td style="width:90px; text-align:right;">
								Expenses
							</td>
							<td style="width:70px; text-align:right;">
								Profit
							</td>
						</tr>	
						<?php foreach($driver_2_stats["leg_calcs"] as $leg_calc): ?>
							<tr style="font-size:12px;">
								<td>
									<?=$leg_calc["leg_id"]?>
								</td>
								<td>
									<?=$leg_calc["date_range"]?>
								</td>
								<td style="text-align:right;">
									<?=number_format($leg_calc["hours"],1)?>
								</td>
								<td style="padding-left:20px;">
									<?=$leg_calc["locations"]?>
								</td>
								<td style="padding-left:10px;">
									<?=$leg_calc["rate_type"]?>
								</td>
								<td style="text-align:right;">
									<?=number_format($leg_calc["map_miles"])?>
								</td>
								<td style="text-align:right;">
									$<?=number_format($leg_calc["rate"],2)?>
								</td>
								<td style="text-align:right;">
									<?=number_format($leg_calc["carrier_expense"],2)?>
								</td>
								<td style="text-align:right;">
									<?=number_format($leg_calc["carrier_profit"],2)?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
			<?php else: ?>
				<table style="margin-left:72px;">
					<tr>
						<td style="font-weight:bold; min-width:110px; max-width:110px;" class="ellipsis">
							<?=$log_entry["codriver"]["client_nickname"] ?>
						</td>
						<td style="font-weight:bold; color:red">
							The system can find the previous End Week and End Leg events for this driver
						</td>
					</tr>
				</table>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
</div>