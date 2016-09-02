<script>
	$("#scrollable_content").height($("#body").height() - 390);
	<?php if(count($performance_reviews) < 50):?>
		load_summary_stats();
	<?php else:?>
		
	<?php endif;?>
</script>
<style>
	table#log_table td
	{
		vertical-align:top;
		line-height:15px;
	}
	
	.row:hover
	{
		background:#FAFAFA;
	}
</style>
<div id="main_content_header">
	<div id="summary_stats_div_0">
		<span style="font-weight:bold; cursor:pointer;" onclick="load_summary_stats();">Performance</span>
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_list" name="refresh_list" src="/images/refresh.png" title="Refresh Log" style="display:none; cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_list()" />
		</div>
	</div>
</div>
<div id="summary_stats_div" style="height:185px; background:#CFCFCF; padding:20px;">
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:12px;">
		<td style="width:30px;" VALIGN="top"></td>
		<td style="width:53px;" VALIGN="top">Truck</td>
		<td style="width:53px;" VALIGN="top">Team<br>Solo</td>
		<td style="width:43px;" VALIGN="top">FM</td>
		<td style="width:43px;" VALIGN="top">DM</td>
		<td style="width:90px;" VALIGN="top">Start</td>
		<td style="width:90px; padding-left:5px;" VALIGN="top">End</td>
		<td style="width:40px; text-align:right;" VALIGN="top">Hours</td>
		<td style="width:40px; text-align:right;" VALIGN="top">Shift Hours</td>
		<td style="width:55px; text-align:right;" VALIGN="top">OOR</td>
		<td style="width:50px; text-align:right;" VALIGN="top">MPG</td>
		<td style="width:55px; text-align:right;" VALIGN="top">Miles</td>
		<td style="width:50px; text-align:right;" VALIGN="top">Book<br>Rate</td>
		<td style="width:50px; text-align:right;" VALIGN="top">Truck<br>Rate</td>
		<td style="width:65px; text-align:right;" VALIGN="top">Revenue</td>
		<td style="width:75px; text-align:right;" VALIGN="top">Truck Profit</td>
		<td style="width:75px; text-align:right;" VALIGN="top">Raw Profit</td>
		<td></td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php if(!empty($performance_reviews)): ?>
		<?php if(count($performance_reviews) < 50):?>
			<?php $i = 0; ?>
			<?php foreach($performance_reviews as $pr): ?>
				<?php
					$row_background_style = "";
					if($i%2 == 0)
					{
						$row_background_style = "background-color:#F7F7F7;";
					}
					$i++;
					
					//$pr_stats = get_performance_stats($pr["end_week_id"]);
				?>
				<div id="row_<?=$pr["id"]?>" class="clickable_row" style=" <?=$row_background_style?> height:20px; overflow:hidden; padding-top:5px; padding-bottom:3px;">
					<script>
						//refresh_row(<?=$pr["id"]?>);
					</script>
					<?php include("performance_row.php"); ?>
				</div>
				<div id="details_<?=$pr["id"]?>" style="display:none; font-size:12px; width:950px; margin-left:15px; min-height:30px; padding:10px; background:#EFEFEF;">
					<!-- AJAX GOES HERE !-->
					<img id="loading_icon" name="loading_icon" height="20px" src="/images/loading.gif" style="margin-left:450px; margin-top:5px;" />
				</div>
			<?php endforeach; ?>
		<?php else:?>
			<div id="message_response_div" style="margin:0 auto; margin-top:100px; width:530px;">This filter set results in more than 50 rows. Please narrow your filter.</div>
		<?php endif;?>
	<?php else: ?>
		<div id="message_response_div" style="margin:0 auto; margin-top:100px; width:230px;">There are no results for this search</div>
	<?php endif; ?>
</div>

