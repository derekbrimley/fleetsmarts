<html style="font-family:arial; font-size:12px;">
	<body>
		<div>
			<table>
				<tr>
					<td style="font-weight:bold; width:200px;">
						Load Number
					</td>
					<td>
						<?=$load["customer_load_number"]?>
					</td>
				</tr>
				<tr>
					<td style="font-weight:bold;">
						Carrier
					</td>
					<td>
						<?=$load["billed_under_carrier"]["company_name"]?>
					</td>
				</tr>
				<tr>
					<td style="font-weight:bold;">	
						Fleet Manager
					</td>
					<td style="max-width:150px;" class="ellipsis" title="<?=$load["fleet_manager"]["f_name"]." ".$load["fleet_manager"]["l_name"]?>">
						<?=$load["fleet_manager"]["f_name"]?>
					</td>
				</tr>
				<tr>
					<td style="font-weight:bold;">		
						Driver Manager
					</td>
					<td>
						<?=$load["driver_manager"]["f_name"]?>
					</td>
				</tr>
				<tr>
					<td style="font-weight:bold;">		
						Reefer Temp
					</td>
					<td>
						<?=$load["reefer_low_set"]?> to <?=$load["reefer_high_set"]?>
					</td>
				</tr>
			</table>
			<table style="margin-top:30px;">
				<tr style="height:35px; color:#DD4B39">
					<td style="min-width:110px; max-width:110px; padding-top:5px;" class="">
						<span class="">Event</span>
					</td>
					<td style="min-width:150px; max-width:150px; padding-right:5px; padding-top:5px;" class="" title="">
						<span class="">Location</span>
					</td>
					<td style="min-width:100px; max-width:100px; padding-top:5px;">
						<span>Time</span>
					</td>
					<td style="min-width:100px; max-width:100px; padding-top:5px;"  class="" title="">
						<span class="">Driver</span>
					</td>
					<td style="min-width:50px; max-width:50px; padding-top:5px;" class="">
						<span class="">Truck</span>
					</td>
					<td style="min-width:50px; max-width:50px; padding-top:5px;" class="">
						<span class="">Trailer</span>
					</td>
					<td style="min-width:340px; max-width:340px; padding-top:5px; padding-right:5px;" class="" title="">
						<span class="">Notes</span>
					</td>
				</tr>
				<?php
					$i = 0;
				?>
				<?php foreach($goalpoints as $goalpoint):?>
					<?php
						$i++;
						$row_style = "";
						if($i%2 == 1)
						{
							$row_style = "background:#E0E0E0;";
						}
						
						//GET DRIVER
						$where = null;
						$where["id"] = $goalpoint["client_id"];
						$client = db_select_client($where);
						
						//GET TRUCK NUMBER
						$where = null;
						$where["id"] = $goalpoint["truck_id"];
						$truck = db_select_truck($where);
						
						//GET TRUCK NUMBER
						$where = null;
						$where["id"] = $goalpoint["trailer_id"];
						$trailer = db_select_trailer($where);
						
						$replace_these = array("Arrival","Departure");
						$replace_with = array("<br>Arrival","<br>Departure");
						$goalpoint_type_text = str_replace($replace_these,$replace_with,$goalpoint["gp_type"]);
						
						$map_link = $goalpoint["dispatch_notes"];
					?>
					<tr style="<?=$row_style?> height:35px;">
						<td style="min-width:110px; max-width:110px; padding-top:5px;" class="">
							<span class=""><?=$goalpoint_type_text?> <?php if(!empty($goalpoint["duration"])):?>(<?=hours_to_text($goalpoint["duration"]/60)?>)<?php endif;?><br><?=$goalpoint["arrival_departure"]?></span>
						</td>
						<td style="min-width:150px; max-width:150px; padding-right:5px; padding-top:5px;" class="" title="<?=$goalpoint["location_name"]?> <?=$goalpoint["location"]?>">
							<div id="gp_location_<?=$goalpoint["id"]?>" class="link" style="padding-right;5px;"><a target="_blank" style="color:blue;" href="http://maps.google.com/maps?q=<?=$goalpoint["gps"]?>" title="<?=$goalpoint["gps"]?>"><?=$goalpoint["location_name"]?></a></div>
							<div id="gp_location_<?=$goalpoint["id"]?>" class=""><?=$goalpoint["location"]?></div>
						</td>
						<td style="padding-top:5px;">
							<a class="link" target="_blank" style=" color:blue;" href="<?=$goalpoint["dispatch_notes"]?>"><?php //echo str_replace(' ','<br>',date("m/d/y H:i",strtotime($goalpoint["expected_time"])))?></a>
						</td>
						<td style="min-width:100px; max-width:100px; padding-top:5px;"  class="" title="<?=$client["client_nickname"]?>">
							<span class=""><?=$client["client_nickname"]?></span>
						</td>
						<td style="min-width:50px; max-width:50px; padding-top:5px;" class="">
							<span class=""><?=$truck["truck_number"]?></span>
						</td>
						<td style="min-width:50px; max-width:50px; padding-top:5px;" class="">
							<span class=""><?=$trailer["trailer_number"]?></span>
						</td>
						<td style="padding-top:5px; padding-right:5px;" class="" title="<?=$goalpoint["dm_notes"]?>">
							<span class=""><?=$goalpoint["dm_notes"]?></span>
						</td>
					</tr>
				<?php endforeach;?>
			</table>
			<div style="margin-top:30px;">
				<a class="link" target="_blank" style="margin-top:30px;font-size:26px; color:blue;" href="<?=$map_link?>">View Map</a>
			</div>
		</div>
		<div style="margin-top:20px;">
			Press REPLY ALL to this email and confirm that you received and accecpt the load. Drive safe!
		</div>
	</body>
</html>