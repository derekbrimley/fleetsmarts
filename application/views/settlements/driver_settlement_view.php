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
		line-height:26px;
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

	//GET FLEET MANAGER
	$where = null;
	$where["id"] = $stats["settlement"]["fm_id"];
	$fleet_manager = db_select_person($where);
	
	
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
	
	//DETERMINE WHAT TO SHOW BASED OFF OF PAY STRUCTURE
	if($driver["pay_structure"] == "Training Stipend")
	{
		$umcc_logo_style = "display:none;";
		$profit_performance_table_style = "display:none;";
		$truck_pl_statement_style = "display:none;";
		$profit_based_legs_div_style = "display:none;";
		$cooperative_member_settlement_style = "display:none;";
		$trainee_settlement_style = "display:block;";
		$member_signature_block_style = "display:none;";
	}
	else if($driver["pay_structure"] == "Profit Based")
	{
		$lobos_logo_style = "display:none;";
		$trainee_stipend_performance_table_style = "display:none;";
		$trainee_settlement_style = "display:none;";
		$trainee_legs_div_style = "display:none;";
		$trainee_signature_block_style = "display:none;";
	}

?>
<div style="width:750px; padding:25px;">
	<div style="height:130px;">
		<div style="float:left; width:300px;">
			<span class="statement_heading" title="<?=$driver["pay_structure"]?>">Weekly Statement</span>
		</div>
		<div style="float:left; width:150px;">
			<img id="umcc_logo_img" src="/images/umcc_logo_small.jpg" style="height:125px; position:relative; left:15px; <?=$umcc_logo_style ?>" onclick="switch_to_training_stipend()"/>
			<img id="lobos_logo_img" src="/images/lobos_logo.jpg" style="height:100px; position:relative; right:35px; <?=$lobos_logo_style ?>" onclick="switch_to_profit_based()"/>
		</div>
		<div style="float:left; width:300px; text-align:right;">
			<span class="statement_heading"><?=$driver["client_nickname"]?></span>
		</div>
	</div>
	<div style="font-size:12px;">
		<div style="float:left; width:400px;">
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
			<td style="width:170px">
				<div class="box_label">
					Average<br>Miles/Day
				</div>
				<div id="avg_miles_per_day" class="colored_box" style=" <?=$mpd_color_style?>">
					<div style="font-size:24px; font-weight:bold; width:90px; margin:0 auto; margin-top:3px; text-align:center;">
						<?=$average_miles_per_day?>
					</div>
				</div>
			</td>
			<td style="width:170px; padding-left:23px;">
				<div class="box_label">
					Average<br>Miles/Gallon
				</div>
				<div id="avg_miles_per_day"  class="colored_box" style=" <?=$mpg_color_style?>">
					<div style="font-size:24px; font-weight:bold; width:90px; margin:0 auto; margin-top:3px; text-align:center;">
						<?=$average_miles_per_gallon?>
					</div>
				</div>
			</td>
			<td style="width:170px; padding-left:23px;">
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
	</table>
	<table id="trainee_stipend_performance_table" style="margin-top:20px; <?=$trainee_stipend_performance_table_style ?>">
		<tr>
			<td style="width:170px">
				<div class="box_label">
					Average<br>Miles/Day
				</div>
				<div id="avg_miles_per_day" class="colored_box" style=" <?=$mpd_color_style?>">
					<div style="font-size:24px; font-weight:bold; width:90px; margin:0 auto; margin-top:3px; text-align:center;">
						<?=$average_miles_per_day?>
					</div>
				</div>
			</td>
			<td style="width:230px; padding-left:150px;">
				<div class="box_label">
					Average<br>Miles/Gallon
				</div>
				<div id="avg_miles_per_day"  class="colored_box" style=" <?=$mpg_color_style?>">
					<div style="font-size:24px; font-weight:bold; width:90px; margin:0 auto; margin-top:3px; text-align:center;">
						<?=$average_miles_per_gallon?>
					</div>
				</div>
			</td>
			<td style="width:170px; padding-left:23px;">
				<div class="box_label">
					OOR<br>Percentage
				</div>
				<div id="avg_miles_per_day"  class="colored_box" style=" <?=$oor_color_style?>">
					<div style="font-size:24px; font-weight:bold; width:90px; margin:0 auto; margin-top:3px; text-align:center;">
						<?=$oor_percentage?>%
					</div>
				</div>
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
				<td style="width:400px">
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
						Truck Rental - mileage <?=number_format($stats["total_odometer_miles"])?> miles @ $<?=number_format($stats["total_truck_mileage_expense"]/$stats["total_odometer_miles"],2)?> per mile
					<?php else:?>
						Truck Rental - mileage
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
	<div id="cooperative_member_settlement" style=" <?=$cooperative_member_settlement_style?>">
		<div style="margin-top:50px">
			<span class="heading statement_heading" style="">COOPERATIVE MEMBER SETTLEMENT</span>
			<br><hr>
		</div>
		<table>
			<tr style="">
				<td class="statement_heading" style="vertical-align:bottom; width:260px">
					SHARE OF TRUCK PROFIT
				</td>
				<td style="vertical-align:bottom; width:390">
					Based on percentage split of truck profit with co-driver
				</td>
				<td class="statement_heading" style="vertical-align:bottom; width:100px; text-align:right;">
					$<?=number_format($stats["total_profit_share"],2)?>
				</td>
			</tr>
			<tr style="height:45px;">
				<td class="statement_heading" style="vertical-align:bottom;">
					ADD SETTLEMENT LOAN
				</td>
				<td style="vertical-align:bottom; ">
					Interest free loan from cooperative to supplement settlement
				</td>
				<td class="statement_heading" style="vertical-align:bottom; text-align:right;">
					$<?=number_format($stats["settlement"]["kick_in"],2)?>
				</td>
			</tr>
			<tr style="height:45px;">
				<td class="statement_heading" style="vertical-align:bottom;">
					ADD CREDITS
				</td>
				<td style="">
				</td>
				<td class="statement_heading" style="vertical-align:bottom; text-align:right;">
					$<?=number_format($stats["total_statement_credits"],2)?>
				</td>
			</tr>
		</table>
		<table style="margin-top:5px;">	
			<?php if(!empty($stats["statement_credits"])): ?>
				<?php
					$i = 0;
				?>
				<?php foreach($stats["statement_credits"] as $statement_credit):?>
					<?php
						$i++;
						$class = "";
						if($i%2 == 1)
						{
							$class = "odd_row";
						}
								
						//GET ACCOUNT
						$where = null;
						$where["id"] = $statement_credit["debited_account_id"];
						$vendor_credit_account = db_select_account($where);
						
						if($vendor_credit_account["relationship_id"] == 0)
						{
							//GET COMPANY
							$where = null;
							$where["id"] = $vendor_credit_account["company_id"];
							$vendor_credits_company = db_select_company($where);
						}
						else
						{
							
							//GET RELATIONSHIP
							$where = null;
							$where["id"] = $vendor_credit_account["relationship_id"];
							$vendor_credit_relationship = db_select_business_relationship($where);
							
							//GET COMPANY
							$where = null;
							$where["id"] = $vendor_credit_relationship["related_business_id"];
							$vendor_credits_company = db_select_company($where);
						}
					?>
					<tr class=" legs_table <?=$class?>" style="">
						<td style="vertical-align:top; padding-left:20px; line-height:14px;  width:240px;">
							<?=$vendor_credits_company["company_side_bar_name"]?>
						</td>
						<td style="vertical-align:top; line-height:14px; width:390px;">
							<?=$statement_credit["credit_description"]?>
						</td>
						<td style="vertical-align:top; text-align:right; line-height:14px;  width:100px;">
							$<?=number_format($statement_credit["credit_amount"],2)?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr style="font-size:12px;">
					There are no credits for this statement
				</tr>
			<?php endif; ?>
		</table>
		<table>
			<tr style="height:45px;">
				<td class="statement_heading" style="vertical-align:bottom; width:260px;">
					LESS MEMBER ADVANCES
				</td>
				<td style="width:390px;">
				</td>
				<td class="statement_heading" style="vertical-align:bottom; width:100px; text-align:right;">
					$<?=number_format($stats["total_client_expenses"],2)?>
				</td>
			</tr>
		</table>
		<table style="margin-top:5px;">	
			<?php if(!empty($stats["client_expenses"])): ?>
				<?php
					$i = 0;
				?>
				<?php foreach($stats["client_expenses"] as $client_expense):?>
					<?php
						$i++;
						$class = "";
						if($i%2 == 1)
						{
							$class = "odd_row";
						}
						
						if($client_expense["is_reimbursable"] == "Yes")
						{
							$class = $class." red";
						}
					?>
					<tr class=" legs_table <?=$class?>" style="">
						<td style="vertical-align:top; padding-left:20px; line-height:14px;  width:55px;">
							<?=date("m/d/y",strtotime($client_expense["expense_datetime"]))?>
						</td>
						<td style="vertical-align:top; padding-left:30px; line-height:14px;  width:150px;">
							<?=$client_expense["category"]?>
						</td>
						<td style="vertical-align:top; padding-left:5px; line-height:14px; width:430px;">
							<?=$client_expense["description"]?>
						</td>
						<td style="vertical-align:top; text-align:right; line-height:14px;  width:60px;">
							$<?=number_format($client_expense["expense_amount"],2)?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr style="font-size:12px;">
					There are no client expenses for this statement
				</tr>
			<?php endif; ?>
		</table>
		<table>
			<tr style="height:45px;">
				<td class="statement_heading"  style="vertical-align:bottom; font-weight:bold; width:260px;">
					SETTLEMENT AMOUNT
				</td>
				<td style="width:390px;">
				</td>
				<td class="statement_heading"  style="vertical-align:bottom; text-align:right; width:100px;">
					$<?=number_format($stats["statement_amount"]+$stats["settlement"]["kick_in"],2)?>
				</td>
			</tr>
		</table>
	</div>
	<div id="trainee_settlement" style=" <?=$trainee_settlement_style?>">
		<div style="margin-top:50px">
			<span class="heading statement_heading" style="">TRAINEE SETTLEMENT</span>
			<br><hr>
		</div>
		<table>
			<tr style="">
				<td class="statement_heading" style="vertical-align:top; width:260px">
					WEEKLY LIVING STIPEND
				</td>
				<td style="vertical-align:top; width:390">
					Provided by Lobos to help trainees with living expenses while in the truck
				</td>
				<td style="vertical-align:top; font-weight:bold; font-size:26px;text-align:right; width:100px;">
					$300.00
				</td>
			</tr>
			<tr style="height:45px;">
				<td class="statement_heading" style="vertical-align:bottom;">
					PERFORMANCE PRIZES
				</td>
				<td style="">
				</td>
				<td class="statement_heading" style="vertical-align:bottom; text-align:right;">
					$<?=number_format($student_reward_total,2)?>
				</td>
			</tr>
			<?php if($average_miles_per_day > $miles_per_day_goal):?>
				<?php
					$i = 0;
				
					$i++;
					$class = "";
					if($i%2 == 1)
					{
						$class = "odd_row";
					}
				?>
				<tr style="line-height:30px;" class=" <?=$class?> ">
					<td class="" style="vertical-align:center; padding-left:20px;">
						Average Miles/Day
					</td>
					<td style="vertical-align:center;">
						Truck acheived over <?=$miles_per_day_goal?> miles/day
					</td>
					<td class="" style="vertical-align:center; text-align:right;">
						$50.00
					</td>
				</tr>
			<?php endif;?>
			<?php if($average_miles_per_gallon > $mpg_goal):?>
				<?php
					$i++;
					$class = "";
					if($i%2 == 1)
					{
						$class = "odd_row";
					}
				?>
				<tr style="line-height:30px;" class=" <?=$class?> ">
					<td class="" style="vertical-align:center; padding-left:20px;">
						Average Miles/Gallon
					</td>
					<td style="vertical-align:center;">
						Truck acheived over <?=$mpg_goal?> miles/gallon
					</td>
					<td class="" style="vertical-align:center; text-align:right;">
						$50.00
					</td>
				</tr>
			<?php endif;?>
			<?php if($oor_percentage < $oor_goal):?>
				<?php
					$i++;
					$class = "";
					if($i%2 == 1)
					{
						$class = "odd_row";
					}
				?>
				<tr style="line-height:30px;" class=" <?=$class?> ">
					<td class="" style="vertical-align:center; padding-left:20px;">
						Out of Route
					</td>
					<td style="vertical-align:center;">
						Truck acheived under <?=$oor_goal?>% out of route
					</td>
					<td class="" style="vertical-align:center; text-align:right;">
						$50.00
					</td>
				</tr>
			<?php endif;?>
			<?php if($mileage_goal_met && $mpg_goal_met && $oor_goal_met):?>
				<?php
					$i++;
					$class = "";
					if($i%2 == 1)
					{
						$class = "odd_row";
					}
				?>
				<tr style="line-height:30px;" class=" <?=$class?> ">
					<td class="" style="vertical-align:center; padding-left:20px;">
						Exeptional Performance
					</td>
					<td style="vertical-align:center;">
						The truck reached all three key-metric goals
					</td>
					<td class="" style="vertical-align:center; text-align:right;">
						$150.00
					</td>
				</tr>
			<?php endif;?>
			<tr style="height:45px;">
				<td class="statement_heading" style="vertical-align:bottom;">
					LESS ADVANCES
				</td>
				<td style="">
				</td>
				<td class="statement_heading" style="vertical-align:bottom; text-align:right;">
				</td>
			</tr>
		</table>
		<table style="margin-top:5px;">	
			<?php if(!empty($stats["client_expenses"])): ?>
				<?php
					$i = 0;
				?>
				<?php foreach($stats["client_expenses"] as $client_expense):?>
					<?php
						$i++;
						$class = "";
						if($i%2 == 1)
						{
							$class = "odd_row";
						}
						
						if($client_expense["is_reimbursable"] == "Yes")
						{
							$class = $class." red";
						}
					?>
					<tr id="ce_<?=$client_expense["id"]?>" class=" legs_table <?=$class?>" style="" onclick="$('#ce_<?=$client_expense["id"]?>').hide();">
						<td style="vertical-align:top; padding-left:20px; line-height:14px;  width:55px;">
							<?=date("m/d/y",strtotime($client_expense["expense_datetime"]))?>
						</td>
						<td style="vertical-align:top; padding-left:30px; line-height:14px;  width:150px;">
							<?=$client_expense["category"]?>
						</td>
						<td style="vertical-align:top; padding-left:5px; line-height:14px; width:430px;">
							<?=$client_expense["description"]?>
						</td>
						<td style="vertical-align:top; text-align:right; line-height:14px;  width:60px;">
							$<?=number_format($client_expense["expense_amount"],2)?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr style="font-size:12px;">
					There are no trainee expenses for this statement
				</tr>
			<?php endif; ?>
		</table>
		<table>
			<tr style="height:45px;">
				<td class="statement_heading"  style="vertical-align:bottom; font-weight:bold; width:260px;">
					SETTLEMENT AMOUNT
				</td>
				<td style="width:390px;">
				</td>
				<td class="statement_heading"  style="vertical-align:bottom; text-align:right; width:100px;">
					$<?=number_format($stats["statement_amount"]+$stats["settlement"]["kick_in"],2)?>
				</td>
			</tr>
		</table>
	</div>
	<div id="notes_from_fm" style="margin-top:30px;">
		<span class="heading statement_heading" style="">CONSULTING NOTES</span>
		<br><hr>
		<div>
			<?=$stats["settlement"]["notes_to_driver"]?>
		</div>
	</div>	
	<div id="profit_based_legs_div" style="margin-top:40px; <?=$profit_based_legs_div_style ?>">
		<span class="heading statement_heading" style="">LEGS</span>
		<br><hr>
		<table>
			<tr class="heading" style="color:black;">
				<td style="width:45px;">
					Leg
				</td>
				<td style="width:145px;">
					Dates
				</td>
				<td style="width:45px; text-align:right;">
					Hours
				</td>
				<td style="min-width:235px; max-width:235px; padding-left:15px;" class="ellipsis">
					Locations
				</td>
				<td style="width:75px; padding-left:10px;">
					Rate Type
				</td>
				<td style="width:50px; text-align:right;">
					Rate
				</td>
				<td style="width:50px; text-align:right;">
					Miles
				</td>
				<td style="width:80px; text-align:right;">
					Revenue
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
				?>
				<tr class=" legs_table <?=$class?>" style="font-size:12px; margin-top:10px;">
					<td>
						<?=$leg_calc["leg_id"]?>
					</td>
					<td>
						<?=$leg_calc["date_range"]?>
					</td>
					<td style="text-align:right;">
						<?=number_format($leg_calc["hours"],1)?>
					</td>
					<td style="min-width:235px; max-width:235px; padding-left:15px;" class="ellipsis">
						<?=$leg_calc["locations"]?>
					</td>
					<td style="padding-left:10px;">
						<?=$leg_calc["rate_type"]?>
					</td>
					<td style="text-align:right;">
						$<?=number_format($leg_calc["rate"],2)?>
					</td>
					<td style="text-align:right;">
						<?=number_format($leg_calc["map_miles"])?>
					</td>
					<td style="text-align:right;">
						$<?=number_format($leg_calc["carrier_revenue"],2)?>
					</td>
				</tr>
			<?php endforeach;?>
		</table>
	</div>
	<div id="trainee_legs_div" style="margin-top:40px; <?=$trainee_legs_div_style?>">
		<span class="heading statement_heading" style="">LEGS</span>
		<br><hr>
		<table>
			<tr class="heading" style="color:black;">
				<td style="width:45px;">
					Leg
				</td>
				<td style="width:145px;">
					Dates
				</td>
				<td style="width:45px; text-align:right;">
					Hours
				</td>
				<td style="min-width:360px; max-width:360px; padding-left:15px;" class="ellipsis">
					Locations
				</td>
				<td style="width:75px; padding-left:10px;">
					Rate Type
				</td>
				<td style="width:50px; text-align:right;">
					Miles
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
				?>
				<tr class=" legs_table <?=$class?>" style="font-size:12px; margin-top:10px;">
					<td>
						<?=$leg_calc["leg_id"]?>
					</td>
					<td>
						<?=$leg_calc["date_range"]?>
					</td>
					<td style="text-align:right;">
						<?=number_format($leg_calc["hours"],1)?>
					</td>
					<td style="min-width:235px; max-width:235px; padding-left:15px;" class="ellipsis">
						<?=$leg_calc["locations"]?>
					</td>
					<td style="padding-left:10px;">
						<?=$leg_calc["rate_type"]?>
					</td>
					<td style="text-align:right;">
						<?=number_format($leg_calc["map_miles"])?>
					</td>
				</tr>
			<?php endforeach;?>
		</table>
	</div>
	<div id="member_signature_block" style=" <?=$member_signature_block_style ?>">
		<div id="sig_div" style="margin-top:30px; font-size:12px;">
			I Agree that this statement represents a fair and acurate record and account of the operations of my business. As such, I agree that upon collection of the above settlement amount, all amounts due to me by the United Motor Carrier Cooperative in association with the indicated time period will be considered paid in full and settled. Further, I reaffirm the authorizations that I have given to the United Motor Carrier Cooperative to collect and manage the funds of my business and to distribute those funds to the proper parties including the ones indicated in this statement.								
		</div>
		<div id="sig_div" style="margin-top:60px;">
			Member _______________________ Signature ____________________________________ Date ______________
		</div>
	</div>
	<div id="trainee_signature_block" style="<?=$trainee_signature_block_style ?>">
		<div id="sig_div" style="margin-top:30px; font-size:12px;">
			I agree that this statement represents a fair and acurate record and account of my experience enrolled in the Lobos training program. Further, I affirm that I am working as a contractor for a motor carrier and am not performing services for Lobos as a contractor or as an employee. I also aknowledge that remittance of this settlement is contingent upon my presense in a truck and participation in the Lobos training program.
		</div>
		<div id="sig_div" style="margin-top:60px;">
			Member _______________________ Signature ____________________________________ Date ______________
		</div>
	</div>
</div>