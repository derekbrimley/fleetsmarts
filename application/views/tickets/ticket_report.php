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
			<img id="refresh_tickets" name="refresh_tickets" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="reset_filters()" />
		</div>
		<div id="count_total" class="header_stats"  style="float:right; margin-right:7px; font-weight:bold;"><?=$ticket_count?></div>
		<div style="float:left; font-weight:bold;">Tickets</div>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:15px;">
		<td style="min-width:50px;max-width:50px;padding-left:5px;" VALIGN="top">Ticket #</td>
		<td style="min-width:57px;max-width:57px;" VALIGN="top">Unit #</td>
		<td style="min-width:70px;max-width:70px;" VALIGN="top">Category</td>
		<td style="min-width:192px;max-width:192px;padding-right:5px;" VALIGN="top">Description</td>
		<td style="min-width:114px;max-width:114px;" VALIGN="top">Responsible<br>Party</td>
		<td style="min-width:75px;max-width:75px;"  VALIGN="top">Incident<br>Date</td>
		<td style="min-width:75px;max-width:75px;" VALIGN="top">Next<br>Action Date</td>
		<td style="min-width:75px;max-width:75px;" VALIGN="top">Estimated Repair Date</td>
		<td style="min-width:75px;max-width:75px;" VALIGN="top">Date<br>Closed</td>
		<td style="min-width:75px;max-width:75px;text-align:right;" VALIGN="top">Original Amount</td>
		<td style="min-width:75px;max-width:75px;text-align:right;" VALIGN="top">Balance</td>
		<td style="min-width:47px;max-width:47px;text-align:right;" VALIGN="top">Notes</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div" style="height:707px;">
	<?php 
		$i = 0;
		$expense_total = 0;
	?>
	<?php if(!empty($tickets)):?>
		<?php foreach($tickets as $ticket):?>
			<?php
				$row = $ticket["id"];

				$background_color = "";
				if($i%2 == 0)
				{
					$background_color = "background:#F2F2F2;";
					//$background_color = "background:#CFCFCF;";
				}
				
				$i++;
				
			?>
			<div id="tr_<?=$row?>" name="tr_<?=$row?>" style="margin-left:5px; margin-right:5px; min-height:30px; <?=$background_color?>"  class="clickable_row">
				<?php include("ticket_row.php"); ?>
			</div>
			<div id="sub_ticket_div_<?=$row?>" style="display:none;padding-bottom:10px; margin-left:5px; margin-right:5px;">
				<div style="width:970px;margin-left:10px;margin-bottom:10px;height:200px;background-color:rgb(239, 239, 239);">
					<img style="height:20px;position:relative;left:450px;top:75px;"src="<?=base_url("images/loading.gif")?>"/>
				</div>
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
</script>
