<script>
	$("#scrollable_content").height($("#body").height() - 195);
</script>
<style>
	.clickable_row:hover
	{
		background:#D5D5D5!important;
	}
</style>
<div id="main_content_header">
	<span style="font-weight:bold;">Invoices</span>
	<div style="float:right; width:25px;">
		<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
		<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_report()" />
	</div>
	<div id="expense_total" class="header_stats"  style="text-align:right; float:right; padding-right:15px; width:150px; margin-right:0px; font-weight:bold;"></div>
	<div id="count_total" class="header_stats"  style="float:right; width:120px; font-weight:bold;"></div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:70px;" VALIGN="top">Date</td>
		<td style="width:80px;" VALIGN="top">Payee</td>
		<td style="width:80px; padding-left:5px;" VALIGN="top">Customer</td>
		<td style="width:120px; padding-left:5px;" VALIGN="top">Invoice</td>
		<td style="width:120px; padding-left:5px;" VALIGN="top">Category</td>
		<td style="width:270px; padding-left:10px;" VALIGN="top">Description</td>
		<td style="width:60px; text-align:right; padding-left:15px;" VALIGN="top">Amount</td>
		<td style="width:85px; text-align:right;" VALIGN="top">Balance</td>
		<td style="width:30px;" VALIGN="top"></td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php 
		$i = 0;
		$expense_total = 0;
	?>
	<?php if(!empty($invoices)):?>
		<?php foreach($invoices as $invoice):?>
			<?php if((user_has_permission('view invoices for assigned business') && user_is_assigned_to_business($invoice["business_id"])) || user_has_permission('view all invoices')):?>
				<?php
					$row = $invoice["id"];

					$background_color = "";
					if($i%2 == 0)
					{
						$background_color = "background:#F2F2F2;";
						//$background_color = "background:#CFCFCF;";
					}
					
					$i++;
					$expense_total = $expense_total + get_invoice_balance($invoice);
				?>
				<div id="tr_<?=$row?>" name="tr_<?=$row?>" style=" margin-left:5px; margin-right:5px; min-height:30px; <?=$background_color?>" onclick="load_invoice_details(<?=$row?>)"  class="clickable_row">
					<?php include("invoice_row.php"); ?>
				</div>
			<?php endif;?>
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

