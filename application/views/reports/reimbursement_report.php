<script>
	$("#scrollable_content").height($("#body").height() - 195);
</script>
<style>
</style>
<div id="main_content_header">
	<div id="plain_header">
		<span style="font-weight:bold;">Reimbursement Report</span>
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_reimbursement_report()" />
		</div>
		<span style="float:right; margin-left:40px; margin-right:20px; font-size:16px;">Total: $<?=number_format($total,2)?> </span>
	</div>
</div>
</div>
<table  style="table-layout:fixed; margin-top:5px; margin-left:10px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:100px; padding-left:5px;" VALIGN="top">Date</td>
		<td style="width:120px;" VALIGN="top">Driver</td>
		<td style="width:680px;" VALIGN="top">Description</td>
		<td style="width:70px; padding-right:5px; text-align:right;" VALIGN="top">Amount</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php if(!empty($unfunded_advances)): ?>
		<table id="log_table" style="margin-left:10px; font-size:12px;">
		<?php
			$i = 0;
		?>
		<?php foreach($unfunded_advances as $advance): ?>
			<?php
				//GET TIME 72 HOURS AGO
				date_default_timezone_set('America/Denver');
				$hours = 48;
				$receipt_deadline = time() - 3600 * $hours;
				$receipt_datetime = strtotime($advance["entry_datetime"]);
				
				//COLOR LATE RECEIPTS RED
				$date_style = "";
				if($receipt_deadline > $receipt_datetime)
				{
					$date_style = " color:red; font-weight:bold; ";
				}
			
				//ALTERNATE BACKGROUND COLOR
				$background_color = "";
				if($i%2 == 0)
				{
					$background_color = "background-color:#F2F2F2;";
				}
				$i++;
			?>
				<tr style="height:30px; line-height:30px; <?=$background_color?>">
					<td style=" <?=$date_style?> overflow:hidden; min-width:100px;  max-width:100px; padding-left:5px;" VALIGN="top"><?=date("m/d/y H:i",strtotime($advance["entry_datetime"]))?></td>
					<td id=""  style="overflow:hidden; min-width:120px;  max-width:120px;" VALIGN="top" title="<?=$advance["account"]["company"]["company_side_bar_name"]?>"><?=$advance["account"]["company"]["company_side_bar_name"]?></td>
					<td class="ellipsis"  style="overflow:hidden; min-width:680px;  max-width:680px;" VALIGN="top" title="<?=$advance["entry_description"]?>"  ><?=$advance["entry_description"]?></td>
					<td id=""  style="overflow:hidden; min-width:70px;  max-width:70px; text-align:right; padding-right:5px;" VALIGN="top" title="<?=$advance["entry_amount"]?>"  >$<?=number_format($advance["entry_amount"],2)?></td>
				</tr>
		<?php endforeach; ?>	
		</table>
	<?php else: ?>
		<div style="margin:0 auto; margin-top:100px; width:230px;">There are no results for this search</div>
	<?php endif; ?>
</div>
