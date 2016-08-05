<script>
	$("#scrollable_content").height($("#body").height() - 195);
</script>
<style>
</style>
<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:left; font-weight:bold;">Expense Report</div>
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_expense_report()" />
		</div>
		<div id="total" class="header_stats"  style="width:150px; margin-right:20px; float:right; font-weight:bold; text-align:right;">Total </div>
		<div id="count" class="header_stats"  style="width:150px; margin-right:20px; float:right; font-weight:bold; text-align:right;">Expenses </div>
		<div id="categories" class="header_stats"  style="width:150px; margin-right:20px; float:right; font-weight:bold; text-align:right;">Categories </div>
	</div>
</div>

<div id="scrollable_content" class="scrollable_div" style="padding:15px;">
	<div style="font-size:20px; font-weight:bold; margin-left:30px; margin-bottom:20px;">
		<?=$owner["company_side_bar_name"]?>
	</div>
	<table style="margin-left:30px;">
		<?php
			$total = 0;
			$i = 1;
			$count_total = 0;
		?>
		<tr class="heading">
			<td style="width:300px;">
				Category
			</td>
			<td style="width:300px; text-align:right;">
				Count
			</td>
			<td style="width:300px; text-align:right;">
				Total
			</td>
		</tr>
		<?php foreach($expense_categories as $category):?>
			<?php
				$total = $total + $category["expense_total"];
				$count_total = $count_total + $category["expense_count"];
				$i++;
			?>
			<tr>
				<?php if(empty($category["category"])):?>
					<td>
						Unassigned
					</td>
				<?php else:?>
					<td>
						<?=$category["category"]?>
					</td>
				<?php endif; ?>
				<td style="text-align:right;">
					<?=$category["expense_count"]?>
				</td>
				<td style="text-align:right;">
					<?=number_format($category["expense_total"],2)?>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
	
	
</div>
<script>
	$("#total").html("Total <?=number_format($total,2)?>");
	$("#count").html("Expenses <?=number_format($count_total)?>");
	$("#categories").html("Categories <?=$i?>");
</script>