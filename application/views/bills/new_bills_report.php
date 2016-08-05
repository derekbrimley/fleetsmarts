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
	<span style="font-weight:bold;">New Bills</span>
	<div style="float:right; width:25px;">
		<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
		<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_new_bills_report()" />
	</div>
	<div id="expense_total" class="header_stats"  style="float:right; width:139px; margin-right:0px; font-weight:bold;"></div>
	<div id="count_total" class="header_stats"  style="float:right; width:120px; font-weight:bold;"></div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:30px; padding-left:5px;" VALIGN="top"></td>
		<td style="width:80px;" VALIGN="top">Bill Date</td>
		<td style="width:100px;" VALIGN="top">Payer</td>
		<td style="width:100px;" VALIGN="top">Sent From</td>
		<td style="width:100px;" VALIGN="top">Invoice</td>
		<td style="width:450px; padding-left:15px;" VALIGN="top">Description</td>
		<td style="width:60px; text-align:right; padding-left:15px;" VALIGN="top">Amount</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php 
		$i = 0;
		$expense_total = 0;
	?>
	<?php if(!empty($bill_holders)):?>
		<?php foreach($bill_holders as $bill_holder):?>
			<?php if((user_has_permission('view bills for assigned business') && user_is_assigned_to_business($bill_holder["company_id"])) || user_has_permission('view all bills')):?>
				<?php
					$row = $bill_holder["id"];

					$background_color = "";
					if($i%2 == 0)
					{
						$background_color = "background:#F2F2F2;";
						//$background_color = "background:#CFCFCF;";
					}
					
					//GET INVOICE
					$where = null;
					$where["id"] = $bill_holder["invoice_id"];
					$invoice = db_select_invoice($where);
					
					$i++;
					$expense_total = $expense_total + $bill_holder["amount"];
				?>
				<div id="tr_<?=$row?>" name="tr_<?=$row?>" style=" margin-left:5px; margin-right:5px; min-height:30px; <?=$background_color?>" onclick="load_new_bill_details(<?=$row?>)"  class="clickable_row">
					<?php include("new_bill_row.php"); ?>
				</div>
			<?php endif;?>
		<?php endforeach;?>
	<?php else: ?>
		<table  style="table-layout:fixed; margin:5px; font-size:12px;">
			<tr>
				<td style="font-weight:bold; padding-left:40px;">
					There are no new bills
				</td>
			</tr>
		</table>
	<?php endif; ?>
</div>
<script>
	$("#expense_total").html("Total $<?=number_format($expense_total,2)?>");
	$("#count_total").html("Count <?=$i?>");
</script>

