<?php
	//FORMAT FUNDED AMOUNT AND FUNDED DATETIME
	$funded_amount = "Not Funded";
	$funded_datetime = "";
	if(!empty($load["funded_datetime"]))
	{
		$funded_amount = "$".number_format($load["amount_funded"] + $load["financing_cost"],2);
		$funded_datetime = date("n/d/y",strtotime($load["funded_datetime"]));
	}
?>

<div style="min-height:95px;">
	<div style="width:20px; float:right;">
		<?php if($is_good && empty($load["commission_approved_datetime"])): ?>
			<img id="unlocked_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/approve_commission.png" title="Approve" onclick="approve_commission('<?=$load["id"];?>')"/>
		<?php endif; ?>
	</div>
	<div style="font-size:12px;">
		<table>
			<tr>
				<td style="width:20px;">
				</td>
				<td style="width:150px; font-weight:bold;">
					Load <?=$load["customer_load_number"];?>
				</td>
				<td style="width:60px; font-weight:bold;">
					Funded
				</td>
				<td style="width:65px;">
					<?=$funded_amount?>
				</td>
				<td style="width:40px; text-align:right;">
					<?=$funded_datetime?>
				</td>
				<td style="width:65px;">
				</td>
				<td style="width:135px;  text-align:right; font-weight:bold;">
					Approved
				</td>
				<td style="width:75px; text-align:right;">
				<?php if(empty($load["commission_approved_datetime"])): ?>
					Pending
				<?php else: ?>
					<?=date("n/d/y",strtotime($load["commission_approved_datetime"]))?>
				<?php endif; ?>
				</td>
			</tr>
		</table>
		<div id="two_tables" style="width:900px; overflow:hidden;">
			<div id="carrier_legs_div" style="float:left; width:440px;">
				<div style="margin-left:20px; margin-top:15px;" class="heading">
					Carrier Legs<br>
					<hr style="width:400px;">
				</div>
				<table style="margin-left:20px; margin-top:15px;">
					<tr>
						<td style="width:65px; font-weight:bold;">
							Leg
						</td>
						<td style="width:65px; font-weight:bold;">
							Rate Type
						</td>
						<td style="width:55px; text-align:right; font-weight:bold;">
							Rate
						</td>
						<td style="width:65px; text-align:right; font-weight:bold;">
							Hours
						</td>
						<td style="width:60px; text-align:right; font-weight:bold;">
							Miles
						</td>
						<td style="width:80px; text-align:right; font-weight:bold;">
							Carrier Rev
						</td>
					</tr>
					<?php if(empty($these_legs)): ?>
						<tr>
							<td colspan="4">There are no locked legs for this load yet<td>
						</tr>
					<?php else: ?>
						<?php
							//GET CARRIER REV, HOURS, AND MAP MILES
							$total_cr = 0;
							$total_hours = 0;
							$total_map_miles = 0;
						?>
						<?php foreach($these_legs as $leg): ?>
							<?php
								//SUM UP HOURS, MILES, AND REV
								//SUM CARRIER REVENUE
								$total_cr = $total_cr + ($leg["revenue_rate"] * $leg["map_miles"]);
								
								//SUM HOURS
								$total_hours = $total_hours + $leg["hours"];
								
								//SUM MAP MILES
								$total_map_miles = $total_map_miles + $leg["map_miles"];
							?>
							<tr>
								<td>
									Leg <?=$leg["id"]?>
								</td>
								<td style="">
									<?=$leg["rate_type"]?>
								</td>
								<td style="text-align:right;">
									$<?=number_format($leg["revenue_rate"],2)?>
								</td>
								<td style="text-align:right;">
									<?=number_format($leg["hours"],2)?>
								</td>
								<td style="text-align:right;">
									<?=number_format($leg["map_miles"])?>
								</td>
								<td style="text-align:right;">
									$<?=number_format($leg["map_miles"]*$leg["revenue_rate"],2)?>
								</td>
							</tr>
						<?php endforeach; ?>
						<tr style="font-weight:bold;">
							<td style="">
								LOAD TOTAL
							</td>
							<td style="">
							</td>
							<td style="text-align:right;">
								$<?=number_format($total_cr/$total_map_miles,2)?>
							</td>
							<td style="text-align:right;">
								<?=number_format($total_hours,2)?>
							</td>
							<td style="text-align:right;">
								<?=$total_map_miles?>
							</td>
							<td style="text-align:right;">
								$<?=number_format($total_cr,2)?>
							</td>
						</tr>
					<?php endif; ?>
				</table>
			</div>
			<div id="load_expenses_div" style="float:right; width:440px;">
				<div style="margin-left:20px; margin-top:15px;" class="heading">
					Load Expenses<br>
					<hr style="width:400px;">
				</div>
				<table style="margin-left:20px; margin-top:15px;">
					<tr>
						<td style="width:285px; font-weight:bold;">
							Description
						</td>
						<td style="width:50px; font-weight:bold;">
							Link
						</td>
						<td style="width:65px; text-align:right; font-weight:bold;">
							Amount
						</td>
					</tr>
					<?php if(empty($load_expenses)): ?>
						<tr>
							<td colspan="3">There are no load expenses for this load<td>
						</tr>
					<?php else: ?>
						<?php
							//GET CARRIER REV, HOURS, AND MAP MILES
							$total_expenses = 0;
							
							
						?>
						<?php foreach($load_expenses as $expense): ?>
							<?php
								$total_expenses = $total_expenses + $expense["expense_amount"];
								
								//STYLE AMOUNT RED AND BOLD IF LOAD EXPENSE HAS NO RECEIPT DATETIME
								$amount_style = "";
								if(empty($expense["receipt_datetime"]))
								{
									$amount_style = "font-weight:bold; color:red; ";
								}
							?>
						<tr>
							<td style="">
								<?=$expense["explanation"]?>
							</td>
							<td style="">
								<?php if(!empty($expense["link"])):?>
									<a href="<?=$expense["link"]?>">Link</a>
								<?php endif; ?>
							</td>
							<td style="text-align:right; <?=$amount_style?>">
								$<?=number_format($expense["expense_amount"],2)?>
							</td>
						</tr>
						<?php endforeach; ?>
						<tr>
							<td style="font-weight:bold;">
								LOAD TOTAL
							</td>
							<td style="font-weight:bold;">
							</td>
							<td style="text-align:right; font-weight:bold;">
								$<?=number_format($total_expenses,2)?>
							</td>
						</tr>
					<?php endif; ?>
				</table>
			</div>
		</div>
	
	</div>
</div>