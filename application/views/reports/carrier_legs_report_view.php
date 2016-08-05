<body>
	<table>
			<tr class="heading" style="font-size:10px; color:black;">
				<td style="width:45px;">
					Truck
				</td>
				<td style="width:60px;" class="ellipsis">
					Date
				</td>
				<td style="width:125px;" class="ellipsis">
					Drivers
				</td>
				<td style="width:70px; padding-left:10px;">
					Rate Type
				</td>
				<td style="width:30px; text-align:right;">
					Rate
				</td>
				<td style="width:50px; text-align:right;">
					M Miles
				</td>
				<td style="width:50px; text-align:right;">
					O Miles
				</td>
				<td style="width:40px; text-align:right;">
					Hours
				</td>
				<td style="width:40px; text-align:right;">
					Route
				</td>
			</tr>
			<?php
				$i = 0;
			?>
	<?php foreach($legs as $leg):?>
		<?php
		
			//GET END LEG LOG ENTRY
			$where = null;
			$where["id"] = $leg["log_entry_id"];
			$end_leg_entry = db_select_log_entry($where);
			
			//GET PREVIOUS LEG LOG ENTRY
			if(empty($end_leg_entry["truck_id"]))
			{
				$driver_id = 0;
				if(!empty($end_leg_entry["main_driver_id"]))
				{
					$driver_id = $end_leg_entry["main_driver_id"];
				}
				elseif(!empty($end_leg_entry["codriver_id"]))
				{
					$driver_id = $end_leg_entry["codriver_id"];
				}
				else
				{
					echo "There has to be at least a driver, codriver, or truck on this event!";
				}
			
				$where = null;
				$where = " entry_type = 'End Leg' AND (log_entry.main_driver_id = ".$driver_id." OR log_entry.codriver_id = ".$driver_id.") AND entry_datetime < '".$end_leg_entry["entry_datetime"]."' ";
				//$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
			}
			else
			{
				$where = null;
				$where = " entry_type = 'End Leg' AND truck_id = ".$end_leg_entry["truck_id"]." AND entry_datetime < '".$end_leg_entry["entry_datetime"]."' ";
				//$begin_leg_entry = db_select_log_entry($where,"entry_datetime DESC","1");
			}
		
			$leg_calc = null;
			$leg_calc["leg"] = $leg;
			//$leg_calc["locations"] = $begin_leg_entry["city"].", ".$begin_leg_entry["state"]." - ".$end_leg_entry["city"].", ".$end_leg_entry["state"];
			//$leg_calc["date_range"] = date("m/d H:i",strtotime($begin_leg_entry["entry_datetime"]))." - ".date("m/d H:i",strtotime($end_leg_entry["entry_datetime"]));
		
			$i++;
			$class = "";
			if($i%2 == 1)
			{
				$class = "odd_row";
			}
			
			//GET DRIVERS
			$main_driver_text = "";
			$codriver_text = "";
			if(!empty($leg_calc["leg"]["main_driver_id"]))
			{
				$where = null;
				$where["id"] = $leg_calc["leg"]["main_driver_id"];
				$main_driver = $leg["main_driver"];
				$main_driver_text = $main_driver["client_nickname"];
			}
			if(!empty($leg_calc["leg"]["codriver_id"]))
			{
				$where = null;
				$where["id"] = $leg_calc["leg"]["codriver_id"];
				$codriver = $leg["codriver"];
				$codriver_text = $codriver["client_nickname"];
			}
			
			//GET LOG ENTRY
			$where = null;
			$where["id"] = $leg["log_entry_id"];
			$log_entry = db_select_log_entry($where);
		?>
		<tr class=" legs_table <?=$class?>" style="font-size:10px; margin-top:10px;">
			<td class="legs_table_td">
				<?=$leg["truck"]["truck_number"]?>
			</td>
			<td style="width:60px;" class="ellipsis">
				<?= $log_entry["entry_datetime"]?>
			</td>
			<td class="legs_table_td">
				<?=$main_driver_text?><br><?=$codriver_text?>
			</td>
			<?PHP 
			/**
			<td class="legs_table_td">
				<?=str_replace(" - ","<br>",$leg_calc["date_range"])?>
			</td>
			<td style="min-width:100px; max-width:100px; padding-left:15px;" class="ellipsis legs_table_td">
				<?=str_replace(" - ","<br>",$leg_calc["locations"])?>
			</td>
			**/
			?>
			<td class="legs_table_td" style="padding-left:10px;">
				<?=$leg["rate_type"]?>
			</td>
			<td class="legs_table_td" style="text-align:right;">
				$<?=number_format($leg["revenue_rate"],2)?>
			</td>
			<td class="legs_table_td" style="text-align:right;">
				<?=number_format($leg["map_miles"])?>
			</td>
			<td class="legs_table_td" style="text-align:right;">
				<?=number_format($leg["odometer_miles"])?>
			</td>
			<td class="legs_table_td" style="text-align:right;">
				<?=number_format($leg["hours"],1)?>
			</td>
			<td style="text-align:right;">
				<a href='<?=$log_entry["route"]?>' target='_blank'>Route</a>
			</td>
		</tr>
	<?php endforeach;?>
</body>