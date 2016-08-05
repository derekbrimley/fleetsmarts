<script>
	$("#scrollable_content").height($(window).height() - 195);
</script>
<div id="main_content_header" style="">
	<span style="font-weight:bold;">Loads</span>
	<span id="last_update" style="font-size:14px; font-weight:normal;">Updated: </span>
	<div style="float:right; width:25px;">
		<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
		<img id="refresh_list" name="refresh_list" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_list()" />
	</div>
	<div style="float:right; width:25px; margin-right:5px;">
		<img id="tv_icon" name="tv_icon" src="/images/grey_tv_icon.png" title="TV Mode" style="cursor:pointer; float:right; height:23px; padding-top:2px;" onclick="tv_icon_clicked()" />
	</div>
	<div style="float:right; margin-right:5px;">
		<span style="font-size:14px; font-weight:normal;">Total</span> <span style="font-weight:bold; margin-right:15px;"><?=count($loads)?></span>
		<span style="font-size:14px; font-weight:normal;">Active</span> <span style="font-weight:bold; margin-right:15px;"><?=$active_count?></span>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px;">
	<tr class="heading" style="font-size:12px; line-height:30px;">
		<td style="width:30px;" VALIGN="top"></td>
		<td style="width:40px;" VALIGN="top">FM</td>
		<td style="width:40px;" VALIGN="top">DM</td>
		<td style="width:70px;" VALIGN="top">Carrier</td>
		<td style="width:55px; padding-left:5px;;" VALIGN="top">Driver</td>
		<td style="width:45px;" VALIGN="top">Truck</td>
		<td style="width:45px;" VALIGN="top">Trailer</td>
		<td style="width:60px;" VALIGN="top">Load #</td>
		<td style="width:60px;" VALIGN="top">Broker</td>
		<td style="width:60px; padding-right:10px; text-align:right;" VALIGN="top">Rate</td>
		<td style="width:30px;" VALIGN="top">RC</td>
		<td style="width:60px;" VALIGN="top">Pick Date</td>
		<td style="width:60px;" VALIGN="top">Drop Date</td>
		<td style="width:85px;" VALIGN="top">Pick</td>
		<td style="width:85px; padding-left:5px;" VALIGN="top">Drop</td>
		<td style="width:50px; padding-left:5px;" VALIGN="top">Update</td>
		<td style="width:30px;" VALIGN="top"></td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php if(!empty($loads)): ?>
		<?php $row = 0; ?>
		<?php foreach($loads as $load): ?>
			<?php
				$row_background_style = "";
				if($row%2 == 0)
				{
					$row_background_style = "background-color:#F7F7F7;";
				}
				$row++;
			?>
			<div id="row_<?=$load["id"]?>" style=" <?=$row_background_style?>">
				<?php include("load_row.php"); ?>
			</div>
			<div id="details_<?=$load["id"]?>" style="display:none; font-size:12px; width:945px; margin-left:15px; min-height:30px; padding:10px; background:#EFEFEF;">
				<!-- AJAX GOES HERE !-->
				<img id="loading_icon" name="loading_icon" height="20px" src="/images/loading.gif" style="margin-left:450px; margin-top:5px;" />
			</div>
		<?php endforeach; ?>
	<?php else: ?>
		<div id="message_response_div" style="margin:0 auto; margin-top:100px; width:230px;">There are no results for this search</div>
	<?php endif; ?>
</div>