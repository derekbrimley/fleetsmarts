<script>
	$("#scrollable_content").height($("#body").height() - 195);
</script>
<style>
	.entry_rows td
	{
		padding-top:10px;
		padding-bottom:10px;
	}
</style>
<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:left; font-weight:bold;">FM Expense Report</div>
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_fm_expense_report()" />
		</div>
		<div id="total_deductions" class="header_stats"  style="width:150px; margin-right:20px; float:right; font-weight:bold; text-align:right;">Total </div>
		<div id="count" class="header_stats"  style="width:150px; margin-right:20px; float:right; font-weight:bold; text-align:right;">Count </div>
	</div>
</div>

<div id="scrollable_content" class="scrollable_div" style="padding:15px;">
	<div style="width:550px; float:left;">
		<table  style="table-layout:fixed; font-size:14px;">
			<tr>
				<td style="width:200px;">
					Fleet Manager
				</td>
				<td style="width:200px; text-align:right;">
					<?=$fm_company["company_side_bar_name"]?>
				</td>
			</tr>
			<tr>
				<td>
					Period
				</td>
				<td style="text-align:right;">
					<?=$start_date?> - <?=$end_date?>
				</td>
			</tr>
			<tr>
				<td>
					Deductions
				</td>
				<td style="text-align:right;">
					<?=number_format($fm_profit_stats["total_deductions"],2)?>
				</td>
			</tr>
			<tr>
				<td>
					Non-standard Expenses
				</td>
				<td style="text-align:right;">
					<?=number_format($fm_profit_stats["non_standard_expenses"],2)?>
				</td>
			</tr>
			<tr>
				<td>
					Settlements/Advances (Plus 7)
				</td>
				<td style="text-align:right;">
					<?=number_format($fm_profit_stats["total_pay_out"],2)?>
				</td>
			</tr>
			<tr>
				<td>
					Kick In (Plus 7)
				</td>
				<td style="text-align:right;">
					<?=number_format($fm_profit_stats["total_kick_in_next_week"],2)?>
				</td>
			</tr>
			<tr></tr>
			<tr>
				<td>
					Settlements/Advances (Plus 10)
				</td>
				<td style="text-align:right;">
					<?=number_format($fm_profit_stats["total_pay_out_ten"],2)?>
				</td>
			</tr>
			<tr>
				<td>
					Kick In (Plus 10)
				</td>
				<td style="text-align:right;">
					<?=number_format($fm_profit_stats["total_kick_ten"],2)?>
				</td>
			</tr>
		</table>
	</div>
	<div style="">
		<table  style="table-layout:fixed; font-size:14px;">
			<tr>
				<td style="width:200px;">
					SHARED EXPENSES
				</td>
				<td style="width:200px; text-align:right;">
					
				</td>
			</tr>
			<tr>
				<td>
					Recruiting Pool
				</td>
				<td style="text-align:right;">
					<?=number_format($fm_profit_stats["total_recruiting_pool"],2)?>
				</td>
			</tr>
			<tr>
				<td>
					Unclaimed FM Expenses
				</td>
				<td style="text-align:right;">
					<?=number_format($fm_profit_stats["total_unclaimed_fm_expenses"],2)?>
				</td>
			</tr>
		</table>
		<div style="font-size:16px; color:red; margin-top:25px;">
			There are <span style="font-size:24px; font-weight:bold;"><?=$fm_profit_stats["unlocked_count"]?></span> unlocked expenses on the week
		</div>
	</div>
	<div style="width:500px; display:none;">
		THIS WEEK
		<?php 
			echo "<br>Total Expense ".$fm_profit_stats["total_expenses"];
			echo "<br>Settlement Pay (this week) ".$fm_profit_stats["total_pay_out_this_week"];
			echo "<br>Personal Advances (this week) ".$fm_profit_stats["total_advances_this_week"];
			echo "<br>Kick In(this week) ".$fm_profit_stats["total_kick_in_this_week"];
			echo "<br>Total Non-Standard Expenses ".$fm_profit_stats["non_standard_expenses"];
		?>
		<br>
		<br>
		NEXT WEEK
		<?php
			echo "<br>Settlement Pay (next week) ".$fm_profit_stats["total_pay_out_next_week"];
			echo "<br>Personal Advances (next week) ".$fm_profit_stats["total_advances_next_week"];
			echo "<br>Kick In (next week) ".$fm_profit_stats["total_kick_in_next_week"];
		?>
	</div>
	<div id="non_standard_expenses" style="clear:both; margin-top:20px;">
		<div style="width:960px;">
			<span class="heading">Non-standard Expenses</span>
			<span style="float:right; font-weight:bold;">$<?=number_format($fm_profit_stats["non_standard_expenses"],2)?></span>
		</div>
		<hr style="width:960px;">
		<table>
			<?php
				$total = 0;
				$i = 1;
			?>
			<?php if(!empty($non_standard_expenses)):?>
				<?php foreach($non_standard_expenses as $expense):?>
					<?php
						$i++;
						$alt_row = "";
						if($i%2 == 0)
						{
							$alt_row = "background-color:#F2F2F2";
						}
						$expense_date_text = "";
						if(!empty($expense["expense_datetime"]))
						{
							$expense_date_text = date("n/d/y",strtotime($expense["expense_datetime"]));
						}
						
						$total = $total + $expense["expense_amount"];
					?>
						<tr style="<?=$alt_row?>">
							<td style="width:100px;padding-left:10px;">
								<?=$expense_date_text?>
							</td>
							<td style="width:180px;">
								<?=$expense["category"]?>
							</td>
							<td style="max-width:560px;" class="ellipsis">
								<?=$expense["description"]?>
							</td>
							<td style="width:100px; padding-right:10px; text-align:right;">
								<?=number_format($expense["expense_amount"],2)?>
							</td>
						</tr>
				<?php endforeach;?>
			<?php endif; ?>
		</table>
	</div>
</div>
<script>
	$("#total_deductions").html("Total <?=number_format($total,2)?>");
	$("#count").html("Count <?=$i?>");
</script>