<script>
	$("#scrollable_content").height($("#body").height() - 155);
</script>
<?php
	$CI =& get_instance();
	
	//echo $start_date."<br>";
	//echo $end_date;
	
	$end_date = date("Y-m-d G:i:s",strtotime($end_date)+24*60*60);
	
	//GET ALL LEGS,LOG ENTRIES BETWEEN DATES
	$sql = "SELECT
			*
			FROM `leg`,log_entry
			WHERE `leg`.log_entry_id = log_entry.id
			AND leg.allocated_load_id IS NOT NULL
			AND log_entry.entry_datetime >= '".$start_date."' 
			AND log_entry.entry_datetime < '".$end_date."'
			ORDER BY log_entry.entry_datetime";
	$query_user = $CI->db->query($sql);
	
	$main_leg = array();
	$main_legs = array();
	foreach ($query_user->result() as $row)
	{
		$main_leg['id'] = $row->id;
		$main_leg['allocated_load_id'] = $row->allocated_load_id;
		$main_leg['map_miles'] = $row->map_miles;
		$main_leg['datetime'] = $row->entry_datetime;
		
		$main_legs[] = $main_leg;
	}
	
	$total_funded = 0;
	$total_revenue = 0;
	$total_total_miles = 0;
?>
<div id="main_content_header">
	<div id="plain_header">
		<div style="float:left; font-weight:bold;">Financial Report</div>
		<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
		<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_financial_report()" />
	</div>
</div>
<div id="scrollable_content" class="scrollable_div">
	<div style="margin-left:50px;">
		<table>
			<tr class="heading">
				<td>
					Load Number
				</td>
				<td style="text-align:right;">
					Datetime
				</td>
				<td style="text-align:right;">
					Miles
				</td>
				<td style="text-align:right;">
					Rate
				</td>
				<td style="text-align:right;">
					Expected Revenue
				</td>
				<td style="text-align:right;">
					Funded Revenue
				</td>
				<td style="text-align:right;">
					Difference
				</td>
			</tr>
			<?php
			//FOR EACH LEG
			foreach($main_legs as $main_leg):
			?>
			<?php
				//TOTAL MAP MILES
				$total_total_miles = $total_total_miles + $main_leg['map_miles'];
			
				//GET LOAD
				$where = null;
				$where["id"] = $main_leg["allocated_load_id"];
				$load = db_select_load($where);
				
				//GET ALL LEGS ALLOCATED TO LOAD
				$where = null;
				$where["allocated_load_id"] = $main_leg["allocated_load_id"];
				$legs = db_select_legs($where);
				
				//FOREACH LEGS
				$total_miles = 0;
				foreach($legs as $leg)
				{
					//ADD UP TOTAL MILES FOR LOAD
					$total_miles = $total_miles + $leg["map_miles"];
					
				}
				
				//CALULATE RATE FOR LOAD
				//$funded_rate = $load["amount_funded"]/$total_miles;
				$rate = $load["expected_revenue"]/$total_miles;
				$funded_rate = ($load["amount_funded"]+$load["financing_cost"])/$total_miles;
				
				//MULTIPLY RATE BY MAP MILES FOR REVENUE
				$revenue = $rate * $main_leg["map_miles"];
				$funded_revenue = $funded_rate * $main_leg["map_miles"];
				
				//ADD REVENUE TO TOTAL REVENUE
				$total_revenue = $total_revenue + $revenue;
				$total_funded = $total_funded + $funded_revenue;
				
				//echo "$total_miles X $rate = $revenue<br>";
				
				$diff = $revenue - $funded_revenue;
				$diff_color = "";
				if($diff < -0.005)
				{
					$diff_color = "color:red; font-weight:bold;";
				}
				if($diff > .005)
				{
					$diff_color = "color:green; font-weight:bold;";
				}
			?>
			<tr>
				<td style="width:100px;">
					<?=$load['customer_load_number']?>
				</td>
				<td style="width:100px;text-align:right;">
					<?=date('n/d H:i',strtotime($main_leg['datetime']))?>
				</td>
				<td style="width:100px;text-align:right;">
					<?=$main_leg["map_miles"]?>
				</td>
				<td style="width:100px;text-align:right;">
					<?=number_format($rate,3)?>
				</td>
				<td style="width:180px;text-align:right;">
					<?=number_format($revenue,2)?>
				</td>
				<td style="width:150px;text-align:right;">
					<?=number_format($funded_revenue,2)?>
				</td>
				<td style="width:130px;text-align:right; <?=$diff_color;?>">
					<?=number_format($diff,2)?>
				</td>
			</tr>
			<?php endforeach;?>
			<?php
				if($total_total_miles == 0)
				{
					$avg_rate = 0;
				}
				else
				{
					$avg_rate = $total_revenue/$total_total_miles;
				}
				
				
				
			?>
			<tr style="font-size:18px; font-weight:bold;">
				<td>
					TOTALS
				</td>
				<td>
				</td>
				<td style="text-align:right;">
					<?=number_format($total_total_miles)?>
				</td>
				<td style="text-align:right;">
					<?=number_format($avg_rate,2)?>
				</td>
				<td style="text-align:right;">
					<?=number_format($total_revenue,2)?>
				</td>
				<td style="text-align:right;">
					<?=number_format($total_funded,2)?>
				</td>
				<td style="text-align:right;">
					<?=number_format($total_revenue - $total_funded,2)?>
				</td>
			</tr>
		</table>
	</div>
</div>