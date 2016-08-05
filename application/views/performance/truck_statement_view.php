<title><?php echo $title;?></title>
<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
<style>
	.indent
	{
		padding-left:15px;
	}
	
	.odd_row
	{
		background-color: #F8F8F8;
	}
	
	.legs_table
	{
		line-height:12px;
	}
	
	.legs_table_td
	{
		padding-top:5px;
	}
	.statement_table tr
	{
		line-height:18px;
	}
	
	.red
	{
		color:red;
	}
	
	.statement_heading
	{
		font-size:18px;
		font-weight:bold;
	}
	
	.box_label
	{	
		width:80px;
		font-size:14px;
		float:left;
		padding-top:5px;
	}
	
	.colored_box
	{
		float:left;
		height:40px;
		width:90px;
		line-height:35px;
	}
</style>
<script>
	function switch_to_profit_based()
	{
		//HIDE ELEMENTS
		$("#lobos_logo_img").hide();
		$("#trainee_stipend_performance_table").hide();
		$("#trainee_settlement").hide();
		$("#trainee_legs_div").hide();
		$("#trainee_signature_block").hide();
		
		//SHOW ELEMENTS
		$("#umcc_logo_img").show();
		$("#profit_performance_table").show();
		$("#truck_pl_statement").show();
		$("#profit_based_legs_div").show();
		$("#cooperative_member_settlement").show();
		$("#member_signature_block").show();
	}
	
	function switch_to_training_stipend()
	{
		//HIDE ELEMENTS
		$("#umcc_logo_img").hide();
		$("#profit_performance_table").hide();
		$("#truck_pl_statement").hide();
		$("#profit_based_legs_div").hide();
		$("#cooperative_member_settlement").hide();
		$("#member_signature_block").hide();
		
		//SHOW ELEMENTS
		$("#lobos_logo_img").show();
		$("#trainee_stipend_performance_table").show();
		$("#trainee_settlement").show();
		$("#trainee_legs_div").show();
		$("#trainee_signature_block").show();
		
	}
</script>
<?php
	$average_miles_per_day = number_format($stats["total_map_miles"]/$stats["total_in_truck_hours"]*24);
	$average_miles_per_gallon = number_format($stats["total_odometer_miles"]/($stats["total_gallons"] - $stats["total_reefer_gallons"]),2);
	$oor_percentage = number_format(($stats["total_odometer_miles"]-$stats["total_map_miles"])/$stats["total_map_miles"]*100,2);
	$fuel_price_comparison = round($stats["average_fuel_price"] - calculate_average_fuel_price($stats["previous_end_week_end_leg"]["entry_datetime"],$stats["this_end_week_end_leg"]["entry_datetime"]),2);

	//ESTABLISH GOALS FOR EACH KEY METRIC
	$miles_per_day_goal = 800;
	$mpg_goal = 7.5;
	$oor_goal = 1;
	$fuel_price_goal = -0.30;
	$student_reward_total = 0;
	
	$mileage_goal_met = false;
	$mpg_goal_met = false;
	$oor_goal_met = false;

	$mpd_color_style = "";
	//DEFINE BACKGROUND COLORS FOR EACH BOX
	if($average_miles_per_day < 550)
	{
		$mpd_color_style = "background-color:red;";
	}
	elseif($average_miles_per_day >= $miles_per_day_goal)
	{
		$mpd_color_style = "background-color:#00ff00;";
		$student_reward_total = $student_reward_total + 50;
		$mileage_goal_met = true;
	}
	else
	{
		$mpd_color_style = "background-color:yellow;";
	}
	
	$mpg_color_style = "";
	//DEFINE BACKGROUND COLORS FOR EACH BOX
	if($average_miles_per_gallon < 6.5)
	{
		$mpg_color_style = "background-color:red;";
	}
	elseif($average_miles_per_gallon >= $mpg_goal)
	{
		$mpg_color_style = "background-color:#00ff00;";//GREEN
		$student_reward_total = $student_reward_total + 50;
		$mpg_goal_met = true;
	}
	else
	{
		$mpg_color_style = "background-color:yellow;";
	}
	
	$oor_color_style = "";
	//DEFINE BACKGROUND COLORS FOR EACH BOX
	if($oor_percentage > 3)
	{
		$oor_color_style = "background-color:red;";
	}
	elseif($oor_percentage <= $oor_goal)
	{
		$oor_color_style = "background-color:#00ff00;";//GREEN
		$student_reward_total = $student_reward_total + 50;
		$oor_goal_met = true;
	}
	else
	{
		$oor_color_style = "background-color:yellow;";
	}
	
	$fpc_color_style = "";
	//DEFINE BACKGROUND COLORS FOR EACH BOX
	if($fuel_price_comparison > -0.15)
	{
		$fpc_color_style = "background-color:red;";
	}
	elseif($fuel_price_comparison <= $fuel_price_goal)
	{
		$fpc_color_style = "background-color:#00ff00;";//GREEN
	}
	else
	{
		$fpc_color_style = "background-color:yellow;";
	}
	
	if($mileage_goal_met && $mpg_goal_met && $oor_goal_met)
	{
		$student_reward_total = $student_reward_total + 150;
	}
	
	//CALCULATE WHAT TRAINEE PAY-BEFORE-ADVANCES SHOULD BE
	$settlement_before_advances = 300 + $student_reward_total;

	
	$umcc_logo_style = "";
	$trainee_stipend_performance_table_style = "";
	$profit_performance_table_style = "";
	$lobos_logo_style = "";
	$truck_pl_statement_style = "";
	$profit_based_legs_div_style = "";
	$trainee_legs_div_style = "";
	$cooperative_member_settlement_style = "";
	$trainee_settlement_style = "";
	$member_signature_block_style = "";
	$trainee_signature_block_style = "";
	
	$lobos_logo_style = "display:none;";
	$trainee_stipend_performance_table_style = "display:none;";
	$trainee_settlement_style = "display:none;";
	$trainee_legs_div_style = "display:none;";
	$trainee_signature_block_style = "display:none;";

?>
<div style="width:850px; padding-left:25px;">
	<table style="border:1px solid black; margin:auto; margin-top:10px;">
		<tr style="background:#DCDCDC; line-height:30px;">
			<td style="width:150px;border:1px solid black; text-align:center;">
				Truck
			</td>
			<td style="width:150px;border:1px solid black; text-align:center;">
				Non-shop Hours
			</td>
			<td style="width:150px;border:1px solid black; text-align:center;">
				Bonus Point
			</td>
			<td style="width:150px;border:1px solid black; text-align:center;">
				Truck Profit
			</td>
			<td style="width:150px;border:1px solid black; text-align:center;">
				Commission
			</td>
		</tr>
		<tr style="line-height:30px;">
			<td style="border:1px solid black; text-align:center;">
				<?=$truck["truck_number"]?>
			</td>
			<td style="border:1px solid black; text-align:center;">
				<?= number_format($stats["total_in_truck_hours"] - $stats["total_shop_hours"],1) ?>
			</td>
			<td style="border:1px solid black; text-align:center;">
				$<?= number_format(($stats["total_in_truck_hours"] - $stats["total_shop_hours"])*6,2)?>
			</td>
			<td style="border:1px solid black; text-align:center;">
				$<?=number_format($stats["total_carrier_profit"],2)?>
			</td>
			<td style="border:1px solid black; text-align:center;">
				$<?=number_format(($stats["total_carrier_profit"] - (($stats["total_in_truck_hours"] - $stats["total_shop_hours"])*6))*.2,2)?>
			</td>
		</tr>
		
		
	</table>
	
</div>
<div style="width:850px; padding:25px;">
	<div style="height:130px;">
		<div style="float:left; width:300px;">
			<span class="statement_heading" title="">Weekly Statement</span>
		</div>
		<div style="float:left; width:250px;">
			<img id="umcc_logo_img" src="/images/umcc_logo_small.jpg" style="height:125px; position:relative; left:65px; <?=$umcc_logo_style ?>" onclick="switch_to_training_stipend()"/>
			<img id="lobos_logo_img" src="/images/lobos_logo.jpg" style="height:100px; position:relative; right:35px; <?=$lobos_logo_style ?>" onclick="switch_to_profit_based()"/>
		</div>
		<div style="float:left; width:300px; text-align:right;">
			<span class="statement_heading"><?=$title?></span>
		</div>
	</div>
	<div style="font-size:12px;">
		<div style="float:left; width:500px;">
			<span class="heading statement_heading" style="">PERFORMANCE REVIEW</span>
		</div>
		<div style="float:left; width:350px; text-align:right; line-height:30px;">
			<span style="font-weight:bold; margin-left:40px;"><?=date("n/j/y H:i",strtotime($stats["previous_end_week_end_leg"]["entry_datetime"])) ?></span>
			 through 
			<span style="font-weight:bold;"><?=date("n/j/y H:i",strtotime($stats["this_end_week_end_leg"]["entry_datetime"])) ?></span>
		</div>
		<br><br><hr>
	</div>
	<table id="profit_performance_table" style="margin-top:20px; <?=$profit_performance_table_style ?>">
		<tr>
			<td style="width:203px">
				<div class="box_label">
					Average<br>Miles/Day
				</div>
				<div id="avg_miles_per_day" class="colored_box" style=" <?=$mpd_color_style?>">
					<div style="font-size:24px; font-weight:bold; width:90px; margin:0 auto; margin-top:3px; text-align:center;">
						<?=$average_miles_per_day?>
					</div>
				</div>
			</td>
			<td style="width:203px; padding-left:23px;">
				<div class="box_label">
					Average<br>Miles/Gallon
				</div>
				<div id="avg_miles_per_day"  class="colored_box" style=" <?=$mpg_color_style?>">
					<div style="font-size:24px; font-weight:bold; width:90px; margin:0 auto; margin-top:3px; text-align:center;">
						<?=$average_miles_per_gallon?>
					</div>
				</div>
			</td>
			<td style="width:204px; padding-left:23px;">
				<div class="box_label">
					OOR<br>Percentage
				</div>
				<div id="avg_miles_per_day"  class="colored_box" style=" <?=$oor_color_style?>">
					<div style="font-size:24px; font-weight:bold; width:90px; margin:0 auto; margin-top:3px; text-align:center;">
						<?=$oor_percentage?>%
					</div>
				</div>
			</td>
			<td style="width:170px; padding-left:23px;">
				<div class="box_label">
					Fuel Price
					<br>Comparison
				</div>
				<div id="avg_miles_per_day"  class="colored_box" style=" <?=$fpc_color_style?>">
					<div style="font-size:24px; font-weight:bold; width:90px; margin:0 auto; margin-top:3px; text-align:center;">
						<?=number_format($fuel_price_comparison,2)?>
					</div>
				</div>
			</td>
		</tr>
		<tr style="line-height:30px;">
			<td>
				Total M Miles: <?=number_format($stats["total_map_miles"])?>
			</td>
			<td>
			</td>
			<td style=" padding-left:23px;">
				Total O Miles: <?=number_format($stats["total_odometer_miles"])?>
			</td>
		</tr>
	</table>
	<div id="truck_pl_statement" name="truck_pl_statement" style=" <?=$truck_pl_statement_style?> ">
		<div style="margin-top:50px">
			<span class="heading statement_heading" style="">TRUCK P&L STATEMENT</span>
			<br><hr>
		</div>
		<table class="statement_table" style="margin-top:0px;">
			<tr>
				<td class="statement_heading" style="vertical-align:bottom; width:260px">
					TRUCK REVENUE
				</td>
				<td style="width:500px">
				</td>
				<td  class="statement_heading" style="vertical-align:bottom; width:90px; text-align:right;">
					$<?=number_format($stats["total_carrier_revenue"],2)?>
				</td>
			</tr>
			<tr class="odd_row" style="height:20px;">
				<td class="indent">
					Reefer Miles
				</td>
				<td>
					<?php if(!empty($stats["total_reefer_miles"])):?>
						<?=$stats["total_reefer_miles"]?> Reefer miles (loaded) @ $<?=number_format($stats["total_reefer_rev"]/$stats["total_reefer_miles"],2)?> per mile
					<?php else: ?>
						<?=$stats["total_reefer_miles"]?> Reefer miles (loaded)
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_reefer_rev"],2)?>
				</td>
			</tr>
			<tr style="height:20px;">
				<td class="indent">
					Loaded Miles
				</td>
				<td>
					<?php if(!empty($stats["total_loaded_miles"])):?>
						<?=$stats["total_loaded_miles"]?> Loaded miles @ $<?=number_format(($stats["total_loaded_rev"])/($stats["total_loaded_miles"]),2)?> per mile
					<?php else: ?>
						<?=$stats["total_loaded_miles"]?> Loaded miles
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_loaded_rev"],2)?>
				</td>
			</tr>
			<tr class="odd_row" style="height:20px;">
				<td class="indent">
					Light Freight
				</td>
				<td>
					<?php if(!empty($stats["total_light_miles"])):?>
						<?=$stats["total_light_miles"]?> Light Freight miles @ $<?=number_format(($stats["total_light_rev"])/($stats["total_light_miles"]),2)?> per mile
					<?php else: ?>
						<?=$stats["total_light_miles"]?> Light Freight miles
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_light_rev"],2)?>
				</td>
			</tr>
			<tr style="height:20px;">
				<td class="indent">
					Bobtail Miles
				</td>
				<td>
					<?php if(!empty($stats["total_bobtail_miles"])):?>
						<?=$stats["total_bobtail_miles"]?> Bobtail miles @ $<?=number_format($stats["total_bobtail_rev"]/$stats["total_bobtail_miles"],2)?> per mile
					<?php else: ?>
						<?=$stats["total_bobtail_miles"]?> Bobtail miles
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_bobtail_rev"],2)?>
				</td>
			</tr>
			<tr class="odd_row" style="height:20px;">
				<td class="indent">
					Deadhead Miles
				</td>
				<td>
					<?php if(!empty($stats["total_deadhead_miles"])):?>
						<?=$stats["total_deadhead_miles"]?> Deadhead miles @ $<?=number_format($stats["total_deadhead_rev"]/$stats["total_deadhead_miles"],2)?> per mile
					<?php else: ?>
						<?=$stats["total_deadhead_miles"]?> Deadhead miles
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_deadhead_rev"],2)?>
				</td>
			</tr>
			<tr style="height:45px;">
				<td class="statement_heading" style="vertical-align:bottom;">
					LESS TRUCK EXPENSES
				</td>
				<td style="">
				</td>
				<td class="statement_heading" style="vertical-align:bottom; text-align:right;">
					$<?=number_format($stats["total_carrier_expenses"],2)?>
				</td>
			</tr>
			<tr class="odd_row" style="height:20px;">
				<td class="indent" style="">
					Truck Fuel
				</td>
				<td style="">
					<?php if(!empty($stats["total_gallons"])):?>
							Fuel <?=number_format($stats["total_gallons"] - $stats["total_reefer_gallons"],2)?> gallons @ $<?=number_format(($stats["total_fuel_expense"] - $stats["total_reefer_fuel_expense"])/($stats["total_gallons"] - $stats["total_reefer_gallons"]),2)?> per gallon
					<?php endif;?>
				</td>
				<td style=" text-align:right;">
					$<?=number_format($stats["total_fuel_expense"] - $stats["total_reefer_fuel_expense"],2)?>
				</td>
			</tr>
			<tr style="height:20px;">
				<td class="indent" style="">
					Reefer Fuel
				</td>
				<td style="">
					<?php if(!empty($stats["total_reefer_gallons"])):?>
							Fuel <?=number_format($stats["total_reefer_gallons"],2)?> gallons @ $<?=number_format($stats["total_reefer_fuel_expense"]/$stats["total_reefer_gallons"],2)?> per gallon
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_reefer_fuel_expense"],2)?>
				</td>
			</tr>
			<tr class="odd_row" style="height:20px;">
				<td class="indent" style="">
					Insurance
				</td>
				<td style="">
					<?php if(!empty($stats["total_in_truck_hours"])):?>
						Equipment Insurance <?=number_format($stats["total_in_truck_hours"],2)?> hours @ $<?=number_format($stats["total_insurance_expense"]/$stats["total_in_truck_hours"],2)?> per hour
					<?php else:?>
						Equipment Insurance
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_insurance_expense"],2)?>
				</td>
			</tr>
			<tr style="height:20px;">
				<td class="indent" style="">
					Direct Lease
				</td>
				<td style="">
					<?php if(!empty($stats["total_in_truck_hours"])):?>
						Truck Rental - hourly <?=number_format($stats["total_in_truck_hours"],2)?> hours @ $<?=number_format($stats["total_truck_rental_expense"]/$stats["total_in_truck_hours"],2)?> per hour
					<?php else:?>
						Truck Rental - hourly
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_truck_rental_expense"],2)?>
				</td>
			</tr>
			<tr class="odd_row"  style="height:20px;">
				<td class="indent" style="">
					Direct Lease
				</td>
				<td style="">
					<?php if(!empty($stats["total_odometer_miles"])):?>
						Truck Rental - Mileage <?=number_format($stats["total_odometer_miles"])?> miles @ $<?=number_format($stats["total_truck_mileage_expense"]/$stats["total_odometer_miles"],2)?> per mile
					<?php else:?>
						Truck Rental - Mileage
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_truck_mileage_expense"],2)?>
				</td>
			</tr>
			<tr style="height:20px;">
				<td class="indent" style="">
					Direct Lease
				</td>
				<td style="">
					<?php if(!empty($stats["total_in_truck_hours"])):?>
						Trailer Rental <?=number_format($stats["total_in_truck_hours"],2)?> hours @ $<?=number_format($stats["total_trailer_rental_expense"]/$stats["total_in_truck_hours"],2)?> per hour
					<?php else:?>
						Trailer Rental
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_trailer_rental_expense"],2)?>
				</td>
			</tr>
			<tr class="odd_row"  style="height:20px;">
				<td class="indent" style="">
					Direct Lease
				</td>
				<td style="">
					<?php if(!empty($stats["total_odometer_miles"])):?>
						Trailer Maintenance Program <?=number_format($stats["total_odometer_miles"])?> miles @ $<?=number_format($stats["total_trailer_mileage_expense"]/$stats["total_odometer_miles"],2)?> per mile
					<?php else:?>
						Trailer Maintenance Program
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_trailer_mileage_expense"],2)?>
				</td>
			</tr>
			<tr style="height:20px;">
				<td class="indent" style="width:250px">
					Direct Lease
				</td>
				<td style="">
					<?php if(!empty($stats["total_odometer_miles"])):?>
						Deposit Alternative <?=number_format($stats["total_odometer_miles"])?> miles @ $<?=number_format($stats["total_damage_expense"]/$stats["total_odometer_miles"],2)?> per mile
					<?php else:?>
						Deposit Alternative
					<?php endif;?>
				</td>
				<td style=" text-align:right;">
					$<?=number_format($stats["total_damage_expense"],2)?>
				</td>
			</tr>
			<tr class="odd_row" style="height:20px;">
				<td class="indent" style="">
					Lobos Interstate Services
				</td>
				<td style="width:400px;">
					<?php if(!empty($stats["total_odometer_miles"])):?>
						Compliance & Consulting <?=number_format($stats["total_odometer_miles"])?> miles @ $<?=number_format($stats["total_compliance_consulting_expense"]/$stats["total_odometer_miles"],2)?> per mile
					<?php else:?>
						Compliance & Consulting
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_compliance_consulting_expense"],2)?>
				</td>
			</tr>
			<tr style="height:20px;">
				<td class="indent" style="">
					Lobos Interstate Services
				</td>
				<td style="">
					<?php if(!empty($stats["total_odometer_miles"])):?>
						Authority Maintenance <?=number_format($stats["total_odometer_miles"])?> miles @ $<?=number_format($stats["total_authority_expense"]/$stats["total_odometer_miles"],2)?> per mile
					<?php else:?>
						Authority Maintenance
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_authority_expense"],2)?>
				</td>
			</tr>
			<tr class="odd_row" style="height:20px;">
				<td class="indent" style="">
					United Cooperative
				</td>
				<td style="">
					<?php if(!empty($stats["total_odometer_miles"])):?>
						Cooperative Membership <?=number_format($stats["total_odometer_miles"])?> miles @ $<?=number_format($stats["total_membership_expense"]/$stats["total_odometer_miles"],2)?> per mile
					<?php else:?>
						Cooperative Membership
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_membership_expense"],2)?>
				</td>
			</tr>
			<tr style="height:20px;">
				<td class="indent" style="">
					United Cooperative
				</td>
				<td style="">
					<?php if(!empty($stats["total_map_miles"])):?>
						Non-recourse Quick Pay <?=number_format($stats["total_map_miles"])?> miles @ $<?=number_format(($stats["total_factoring_expense"] + $stats["total_bad_debt_expense"])/$stats["total_map_miles"],2)?> per map mile
					<?php else:?>
						Non-recourse Quick Pay
					<?php endif;?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_factoring_expense"] + $stats["total_bad_debt_expense"],2)?>
				</td>
			</tr>
			<tr style="height:45px;">
				<td class="statement_heading" style="vertical-align:bottom;width:250px">
					TRUCK PROFIT
				</td>
				<td style="">
				</td>
				<td class="statement_heading" style="vertical-align:bottom; text-align:right;">
					$<?=number_format($stats["total_carrier_profit"],2)?>
				</td>
			</tr>
		</table>
	</div>
	<div id="profit_based_legs_div" style="margin-top:40px; <?=$profit_based_legs_div_style ?>">
		<span class="heading statement_heading" style="">LEGS</span>
		<br><hr>
		<table>
			<tr class="heading" style="font-size:10px; color:black;">
				<td style="width:45px;">
					Leg
				</td>
				<td style="width:125px;" class="ellipsis">
					Drivers
				</td>
				<td style="width:60px;">
					Dates
				</td>
				<td style="min-width:100px; max-width:100px; padding-left:15px;" class="ellipsis">
					Locations
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
				<td style="width:50px; text-align:right;">
					OOR
				</td>
				<td style="width:50px; text-align:right;">
					O Miles<br>/Hour
				</td>
				<td style="width:70px; text-align:right;">
					Profit<br>/Hour
				</td>
				<td style="width:40px; text-align:right;">
					Hours
				</td>
				<td style="width:70px; text-align:right;">
					Profit
				</td>
			</tr>
			<?php
				$i = 0;
			?>
			<?php foreach($stats["leg_calcs"] as $leg_calc):?>
				<?php
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
						$main_driver = db_select_client($where);
						$main_driver_text = $main_driver["client_nickname"];
					}
					if(!empty($leg_calc["leg"]["codriver_id"]))
					{
						$where = null;
						$where["id"] = $leg_calc["leg"]["codriver_id"];
						$codriver = db_select_client($where);
						$codriver_text = $codriver["client_nickname"];
					}
				?>
				<tr class=" legs_table <?=$class?>" style="font-size:10px; margin-top:10px;">
					<td class="legs_table_td">
						<?=$leg_calc["leg_id"]?>
					</td>
					<td class="legs_table_td">
						<?=$main_driver_text?><br><?=$codriver_text?>
					</td>
					<td class="legs_table_td">
						<?=str_replace(" - ","<br>",$leg_calc["date_range"])?>
					</td>
					<td style="min-width:100px; max-width:100px; padding-left:15px;" class="ellipsis legs_table_td">
						<?=str_replace(" - ","<br>",$leg_calc["locations"])?>
					</td>
					<td class="legs_table_td" style="padding-left:10px;">
						<?=$leg_calc["rate_type"]?>
					</td>
					<td class="legs_table_td" style="text-align:right;">
						$<?=number_format($leg_calc["rate"],2)?>
					</td>
					<td class="legs_table_td" style="text-align:right;">
						<?=number_format($leg_calc["map_miles"])?>
					</td>
					<td class="legs_table_td" style="text-align:right;">
						<?=number_format($leg_calc["odometer_miles"])?>
					</td>
					<td class="legs_table_td" style="text-align:right;">
						<?php if($leg_calc["map_miles"] == 0):?>
							0%
						<?php else:?>
							<?=number_format(($leg_calc["odometer_miles"]-$leg_calc["map_miles"])/$leg_calc["map_miles"]*100,2)?>%
						<?php endif;?>
					</td>
					<td class="legs_table_td" style="text-align:right;">
						<?=number_format($leg_calc["odometer_miles"]/$leg_calc["hours"],2)?>
					</td>
					<td class="legs_table_td" style="text-align:right;">
						$<?=number_format($leg_calc["carrier_profit"]/$leg_calc["hours"],2)?>
					</td>
					<td class="legs_table_td" style="text-align:right;">
						<?=number_format($leg_calc["hours"],1)?>
					</td>
					<td class="legs_table_td" style="text-align:right;">
						$<?=number_format($leg_calc["carrier_profit"],2)?>
					</td>
				</tr>
			<?php endforeach;?>
			<tr class=" legs_table " style="font-weight:bold; font-size:12px; margin-top:10px; line-height:26px;">
				<td>
				</td>
				<td>
				</td>
				<td>
				</td>
				<td style=" padding-left:15px;" class="ellipsis">
				</td>
				<td style="padding-left:10px;">
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_carrier_revenue"]/$stats["total_map_miles"],2)?>
				</td>
				<td style="text-align:right;">
					<?=number_format($stats["total_map_miles"])?>
				</td>
				<td style="text-align:right;">
					<?=number_format($stats["total_odometer_miles"])?>
				</td>
				<td style="text-align:right;">
					<?=$oor_percentage?>%
				</td>
				<td style="text-align:right;">
					<?=number_format($stats["total_odometer_miles"]/$stats["total_in_truck_hours"],2)?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_carrier_profit"]/$stats["total_in_truck_hours"],2)?>
				</td>
				<td style="text-align:right;">
					<?=number_format($stats["total_in_truck_hours"],1)?>
				</td>
				<td style="text-align:right;">
					$<?=number_format($stats["total_carrier_profit"],2)?>
				</td>
			</tr>
		</table>
	</div>
	<div id="member_signature_block" style=" <?=$member_signature_block_style ?>">
		<div id="sig_div" style="margin-top:30px; font-size:12px;">
		
		</div>
		<div id="sig_div" style="margin-top:60px;">
			Driver Manager _______________________________ Fleet Manager _______________________________ Date ______________
		</div>
	</div>
</div>