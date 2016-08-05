<?php if($includes_estimate): ?>
	<div style="margin-left:75px;margin-top:15px; color:orange">
		These allocations take into consideration an estimated fuel fill (Ghost Fill)
	</div>
<?php endif;?>
<?php
	$total_miles_allocated = 0;
	$total_percentage_allocated = 0;
	if($includes_estimate)
	{
		$total_gallons_allocated = $previous_fuel_fill_fuel_stop["gallons"];
		$total_expense_allocated = $previous_fuel_fill_fuel_stop["fuel_expense"];
	}
	else
	{
		$total_gallons_allocated = 0;
		$total_expense_allocated = 0;
	}
?>
<table style="margin-left:75px; margin-top:15px;" class="event_details_table">
	<tr style="font-weight:bold;">
		<td style="">
			Leg
		</td>
		<td style="padding-left:78px;">
			Miles Allocated
		</td>
		<td style="padding-left:78px;">
			Percentage Allocated
		</td>
		<td style="padding-left:78px;">
			Gallons Allocated
		</td>
		<td style="padding-left:78px;">
			Expense Allocated
		</td>
	</tr>
	<?php foreach($fuel_allocations as $allocation): ?>
		<?php
			$total_miles_allocated = $total_miles_allocated + $allocation["miles"];
			$total_percentage_allocated = $total_percentage_allocated + $allocation["percentage"];
			$total_gallons_allocated = $total_gallons_allocated + $allocation["gallons"];
			$total_expense_allocated = $total_expense_allocated + $allocation["expense"];
		?>
		<tr>
			<td style="">
				<?=$allocation["leg_id"]?>
			</td>
			<td style="padding-left:78px; text-align:right;">
				<?=number_format($allocation["miles"])?>
			</td>
			<td style="padding-left:78px; text-align:right;">
				<?=number_format(round($allocation["percentage"]*100,2),2)?>%
			</td>
			<td style="padding-left:78px; text-align:right;">
				<?=number_format($allocation["gallons"],2)?>
			</td>
			<td style="padding-left:78px; text-align:right;">
				$<?=number_format($allocation["expense"],2)?>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php if($includes_estimate): ?>
		<tr>
			<td style="" title="Fuel <?=$previous_fuel_fill_fuel_stop["id"]?>">
				Fuel Estimate
			</td>
			<td style="padding-left:78px; text-align:right;">
				0
			</td>
			<td style="padding-left:78px; text-align:right;">
				0%
			</td>
			<td style="padding-left:78px; text-align:right;">
				<?=number_format($previous_fuel_fill_fuel_stop["gallons"],2)?>
			</td>
			<td style="padding-left:78px; text-align:right;">
				$<?=number_format($previous_fuel_fill_fuel_stop["fuel_expense"],2)?>
			</td>
		</tr>
	<?php endif;?>
	<?php
		$miles_style = "color:red; font-weight:bold;";
		$percentage_style = "color:red; font-weight:bold;";
		$gallons_style = "color:red; font-weight:bold;";
		$expense_style = "color:red; font-weight:bold;";
		
		if($total_miles_allocated == $fuel_stop["odom_miles"])
		{
			$miles_style= "color:green; font-weight:bold;";
		}
		
		if(round($total_percentage_allocated,2) == 1)
		{
			$percentage_style= "color:green; font-weight:bold;";
		}
		
		if(round($total_gallons_allocated,2) == round($fuel_stop["fill_to_fill_gallons"],2))
		{
			$gallons_style= "color:green; font-weight:bold;";
		}
		//echo round($total_expense_allocated)." "
		if(round($total_expense_allocated,2) == round($fuel_stop["fill_to_fill_expense"],2))
		{
			$expense_style= "color:green; font-weight:bold;";
		}
		
		
	?>
	<tr>
		<td style="">
			TOTAL
		</td>
		<td style="<?=$miles_style?> padding-left:78px; text-align:right;">
			<?=number_format($total_miles_allocated)?>
		</td>
		<td style="<?=$percentage_style?> padding-left:78px; text-align:right;">
			<?=number_format(round($total_percentage_allocated*100,2),2)?>%
		</td>
		<td style="<?=$gallons_style?> padding-left:78px; text-align:right;">
			<?=$total_gallons_allocated?>
		</td>
		<td style="<?=$expense_style?> padding-left:78px; text-align:right;">
			$<?=number_format($total_expense_allocated,2)?>
		</td>
	</tr>
</table>