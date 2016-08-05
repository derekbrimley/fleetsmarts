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
		<div style="float:left; font-weight:bold;">Transactions</div>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:30px; padding-left:5px;" VALIGN="top"></td>
		<td style="width:20px;" VALIGN="top"></td>
		<td style="width:70px;" VALIGN="top">Date</td>
		<td style="width:115px; padding-left:5px;" VALIGN="top">Issuer</td>
		<td style="width:115px;" VALIGN="top">Owner</td>
		<td style="width:125px;" VALIGN="top">Category</td>
		<td style="width:345px;" VALIGN="top">Description</td>
		<td style="width:80px; text-align:right;" VALIGN="top">Amount</td>
		<td style="width:30px; text-align:right;" VALIGN="top"></td>
	</tr>
</table>
<?php 
	$i = 0;
	$expense_total = 0;
?>
<div id="scrollable_content" class="scrollable_div">
	
	<?php if(!empty($expenses)):?>
		<?php foreach($expenses as $expense):?>
			<?php
				$row = $expense["id"];

				$background_color = "";
				if($i%2 == 0)
				{
					$background_color = "background-color:#F2F2F2;";
				}
				//$i++;
				
				//$expense_total = $expense_total + $expense["expense_amount"];
			?>
			<div id="tr_<?=$row?>" name="tr_<?=$row?>" style=" margin-left:5px; margin-right:5px; <?=$background_color?>" >
				<?php include("expense_row.php"); ?>
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
