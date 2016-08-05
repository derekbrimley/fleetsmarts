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
	<div id="plain_header" style="font-size:16px;">
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_po_report()" />
		</div>
		<div id="expense_total" class="header_stats"  style="float:right; width:150px; margin-right:20px; font-weight:bold;"></div>
		<div id="count_total" class="header_stats"  style="float:right; width:120px; font-weight:bold;"></div>
		<div style="float:left; font-weight:bold;">Purchase Orders</div>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:30px; padding-left:5px;" VALIGN="top"></td>
		<td style="width:70px;" VALIGN="top">PO Date</td>
		<td style="width:40px;" VALIGN="top">PO #</td>
		<td style="width:50px;" VALIGN="top">Issuer</td>
		<td style="width:70px;" VALIGN="top">Approver</td>
		<td style="width:90px;" VALIGN="top">Owner</td>
		<td style="width:125px;" VALIGN="top">Category</td>
		<td style="width:225px; padding-left:5px;" VALIGN="top">Notes</td>
		<td style="width:75px;" VALIGN="top">Approved</td>
		<td style="width:105px;" VALIGN="top">Account</td>
		<td style="width:50px; text-align:right;" VALIGN="top">Amount</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php 
		$i = 0;
		$expense_total = 0;
		$person_id = $this->session->userdata('person_id');
	?>
	<?php if(!empty($purchase_orders)):?>
		<?php foreach($purchase_orders as $po):?>
			<?php if(($person_id == $po["issuer_id"] || $person_id == $po["approved_by_id"]) || (user_has_permission('view all purchase orders for assigned business') && user_is_assigned_to_business($po["owner_id"])) || user_has_permission('view all purchase orders')):?>
				<?php
					$row = $po["id"];

					$background_color = "";
					if($i%2 == 0)
					{
						$background_color = "background:#F2F2F2;";
						//$background_color = "background:#CFCFCF;";
					}
					
					$i++;
					
					//CHECK IF PO IS COMPLETE
					if	(
						empty($po["expense_datetime"]) ||
						empty($po["expense_amount"]) ||
						empty($po["owner_id"]) ||
						empty($po["category"]) ||
						empty($po["account_id"]) ||
						empty($po["approved_by_id"]) ||
						empty($po["po_notes"])
					)
					{
						$po_is_complete = false;
					}
					else
					{
						$po_is_complete = true;
					}
					
					$expense_total = $expense_total + $po["expense_amount"];
				?>
				<div id="tr_<?=$row?>" name="tr_<?=$row?>" style=" margin-left:5px; margin-right:5px; height:30px; <?=$background_color?>"  class="clickable_row">
					<?php include("po_row.php"); ?>
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
