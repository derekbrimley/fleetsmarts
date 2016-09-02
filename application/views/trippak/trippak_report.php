<script>
	$("#scrollable_content").height($("#body").height() - 195);
	
	$("#edit_cell_form").submit(function (e) {
        e.preventDefault(); //prevent default form submit
		save_edit_cell();
    });
</script>
<style>
	.header_stats
	{
		font-size:12px;
	}
</style>
<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_list" name="refresh_list" src="/images/refresh.png" title="Refresh Trippaks" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_trippak_report()" />
		</div>
		<div id="count_total" class="header_stats"  style="margin-right:25px;float:right; font-weight:bold;"><?=count($trippaks)?></div>
		<div style="float:left; font-weight:bold;">Trippak</div>
	</div>
</div>
<table style="table-layout:fixed; margin-top:5px; font-size:12px;">
	<tr class="heading" style="line-height:12px;">
		<td style="min-width:25px;max-width:25px;padding-left:5px;" VALIGN="top"></td>
		<td style="min-width:100px;max-width:100px;padding-right:5px;" class="ellipsis" VALIGN="top">Scan Time</td>
		<td style="min-width:60px;max-width:60px;padding-right:5px;" class="ellipsis" VALIGN="top" class="">Load</td>
		<td style="min-width:150px;max-width:150px;padding-right:5px;" class="ellipsis" VALIGN="top">Carrier</td>
		<td style="min-width:150px;max-width:150px;padding-right:5px;" class="ellipsis" VALIGN="top" class="fm_td">Drop City</td>
		<td style="min-width:75px;max-width:75px;padding-right:5px;" class="ellipsis" VALIGN="top" class="dm_td">Truck</td>
		<td style="min-width:75px;max-width:75px;padding-right:5px;" class="ellipsis" VALIGN="top">Trailer</td>
		<td style="min-width:175px;max-width:175px;padding-right:5px;" class="ellipsis" VALIGN="top" class="driver1_td">Driver 1</td>
		<td style="min-width:175px;max-width:175px;padding-right:5px;" class="ellipsis" VALIGN="top" class="driver2_td">Driver 2</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php 
		$i = 0;
	?>
	<?php if(!empty($trippaks)): ?>
		<?php foreach($trippaks as $trippak):?>
			<?php
				$background_color = "";
				if($i%2 == 0)
				{
					$background_color = "background-color:#F7F7F7;";
				}
				$i++;

			?>
			<div id="row_<?=$trippak["id"]?>" style="height:30px; <?=$background_color?>" class="clickable_row">
				<?php include("trippak_row.php"); ?>
			</div>
			<div id="details_<?=$trippak["id"]?>" style="display:none; font-size:12px; width:945px; margin-left:15px; min-height:30px; padding:10px; background:#EFEFEF;">
				<!-- AJAX GOES HERE !-->
				<img id="loading_icon" name="loading_icon" height="20px" src="/images/loading.gif" style="margin-left:450px; margin-top:5px;" />
			</div>
		<?php endforeach;?>
	<?php else: ?>
		<div style="margin-left:25px;">No results for those filters.</div>
	<?php endif ?>
</div>
