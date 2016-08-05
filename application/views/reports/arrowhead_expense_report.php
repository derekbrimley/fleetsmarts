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
		<div style="float:left; font-weight:bold;">Arrowhead Expense Report</div>
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_arrowhead_expense_report()" />
		</div>
	</div>
</div>

<div id="scrollable_content" class="scrollable_div" style="padding:15px;">
	<div style="width:550px; float:left;">
		<table  style="table-layout:fixed; font-size:14px;">
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
					Reimbursements
				</td>
				<td style="text-align:right;">
					<?=number_format($arrowhead_stats["total_reimbursements"],2)?>
				</td>
			</tr>
			<tr>
				<td>
					Deductions
				</td>
				<td style="text-align:right;">
					<?=number_format($arrowhead_stats["total_deductions"],2)?>
				</td>
			</tr>
			<tr>
				<td>
					Arrowhead Expenses
				</td>
				<td style="text-align:right;">
					<?=number_format($arrowhead_stats["total_expenses"],2)?>
				</td>
			</tr>
			<tr>
				<td>
					Non-Standard Client Expenses
				</td>
				<td style="text-align:right;">
					<?=number_format($arrowhead_stats["total_client_expenses"],2)?>
				</td>
			</tr>
			<tr>
				<td>
					Settlements (2 weeks later)
				</td>
				<td style="text-align:right;">
					<?=number_format($arrowhead_stats["total_settlements_ten"],2)?>
				</td>
			</tr>
			<tr>
				<td>
					Kick In (2 weeks later)
				</td>
				<td style="text-align:right;">
					<?=number_format($arrowhead_stats["total_kick_ten"],2)?>
				</td>
			</tr>
		</table>
	</div>
	<div style="">
		<table  style="table-layout:fixed; font-size:14px;">
			<tr>
				<td style="width:200px;">
					CHECK LIST
				</td>
				<td style="width:200px; text-align:right;">
					
				</td>
			</tr>
			<tr>
				<td>
					Unfunded Loads
				</td>
				<td style="text-align:right;">
					<?=count($arrowhead_stats["loads"])?>
				</td>
			</tr>
			<tr>
				<td>
					Unlocked Log Events
				</td>
				<td style="text-align:right;">
					<?=number_format($arrowhead_stats["unlocked_event_count"])?>
				</td>
			</tr>
			<tr>
				<td>
					Unlocked Expenses (3 weeks)
				</td>
				<td style="text-align:right;">
					<?=number_format($arrowhead_stats["unlocked_count"])?>
				</td>
			</tr>
			<tr>
				<td>
					Unrecorded Expenses (3 weeks)
				</td>
				<td style="text-align:right;">
					<?=number_format($arrowhead_stats["unrecorded_count"])?>
				</td>
			</tr>
		</table>
	</div>
	<div style="width:500px; display:none;">
		THIS WEEK
		<?php 
			echo "<br>Total Expense ".$arrowhead_stats["total_expenses"];
			echo "<br>Settlement Pay (this week) ".$arrowhead_stats["total_pay_out_this_week"];
			echo "<br>Personal Advances (this week) ".$arrowhead_stats["total_advances_this_week"];
			echo "<br>Kick In(this week) ".$arrowhead_stats["total_kick_in_this_week"];
			echo "<br>Total Non-Standard Expenses ".$arrowhead_stats["total_expenses"];
		?>
		<br>
		<br>
		NEXT WEEK
	</div>
	<div id="non_standard_expenses" style="clear:both; margin-top:50px;">
		<div style="width:960px;">
			<span class="heading">Arrowhead Expenses</span>
			<span style="float:right; font-weight:bold;">$<?=number_format($arrowhead_stats["total_expenses"],2)?></span>
		</div>
		<hr style="width:960px;">
		<table>
			<?php
				$total = 0;
				$i = 1;
			?>
			<?php if(!empty($arrowhead_stats["arrowhead_expenses"])):?>
				<?php foreach($arrowhead_stats["arrowhead_expenses"] as $expense):?>
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
	<div id="non_standard_expenses" style="clear:both; margin-top:50px;">
		<div style="width:960px;">
			<span class="heading">Non-Standard Client Expenses</span>
			<span style="float:right; font-weight:bold;">$<?=number_format($arrowhead_stats["total_client_expenses"],2)?></span>
		</div>
		<hr style="width:960px;">
		<table>
			<?php
				$total = 0;
				$i = 1;
			?>
			<?php if(!empty($arrowhead_stats["client_expenses"])):?>
				<?php foreach($arrowhead_stats["client_expenses"] as $expense):?>
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
