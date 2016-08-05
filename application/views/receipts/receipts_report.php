<script>
	$("#scrollable_content").height($("#body").height() - 195);
</script>
<style>
	.header_stats
	{
		font-size:16px;
	}
	
	.editable_cell:hover
	{
		/*background:#EFEFEF;*/
		background:#F7F7F7;
		cursor:default;
	}
</style>

<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_report()" />
		</div>
		<div id="expense_total" class="header_stats"  style="float:right; width:150px; margin-right:20px; font-weight:bold;"></div>
		<div id="count_total" class="header_stats"  style="float:right; width:120px; font-weight:bold;"></div>
		<div style="float:left; font-weight:bold;">Receipts</div>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:30px; padding-left:5px;" VALIGN="top"></td>
		<td style="width:60px;" VALIGN="top">Date</td>
		<td style="width:100px;" VALIGN="top">Owner</td>
		<td style="width:100px; padding-left:5px;" VALIGN="top">Client</td>
		<td style="width:120px; padding-left:5px;" VALIGN="top">Category</td>
		<td style="width:290px; padding-left:5px;" VALIGN="top">Description</td>
		<td style="width:50px; line-height:14px; padding-left:5px;" VALIGN="top">Receipt Date</td>
		<td style="width:60px; line-height:14px; text-align:right;" VALIGN="top">Receipt Amount</td>
		<td style="width:60px; line-height:14px; text-align:right;" VALIGN="top">Advance Balance</td>
		<td style="width:40px; line-height:14px;" VALIGN="top"></td>
		<td style="width:30px; text-align:right;" VALIGN="top"></td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	
	<?php 
		$i = 0;
		$expense_total = 0;
	?>
	<?php if(!empty($client_expenses)):?>
		<?php foreach($client_expenses as $client_expense):?>
			<?php
				$row = $client_expense["id"];

				$background_color = "";
				if($i%2 == 0)
				{
					$background_color = "background-color:#F2F2F2;";
				}
				$i++;
				
				$expense_total = $expense_total + $client_expense["expense_amount"];
			?>
			<div id="tr_<?=$row?>" name="tr_<?=$row?>" style=" margin-left:5px; margin-right:5px; <?=$background_color?>" >
				<?php include("receipt_row.php"); ?>
			</div>
		<?php endforeach;?>
	<?php else: ?>
		<table  style="table-layout:fixed; margin:5px; font-size:12px;">
			<tr>
				<td style="font-weight:bold; padding-left:40px;">
					There are no results for this filter set
				</td>
			</tr>
		</table>
	<?php endif; ?>
</div>
<script>
	$("#expense_total").html("Total $<?=number_format($expense_total,2)?>");
	$("#count_total").html("Count <?=$i?>");
</script>