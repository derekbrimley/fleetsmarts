<?php
	$i=0;
?>
<script>
	$("#scrollable_content").height($("#body").height() - 185);
</script>
<div id="main_content_header">
	<span style="font-weight:bold;">
		<?= $title ?>
	</span>
	<span>
		<img id="report_loading_icon" name="report_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
	</span>
	<span>
		<img id="refresh_icon" name="refresh_icon" src="/images/refresh.png" title="Refresh Report" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_reefer_report()" />
	</span>
	<span id="reefer_count" style="float:right; margin-left:40px; margin-right:20px; font-size:16px;">
		<?=$count?>
	</span>
</div>
<div id="scrollable_content" class="scrollable_div" style="width:100%;font-size:9pt;">
	<table style="table-layout: fixed;font-size: 12px; margin:10px;">
		<?php if(!empty($trailer_geopoints)): ?>
			<tr class="heading" style="line-height:30px;">
				<td style="width:120px;padding-left:5px;">Time Occurred</td>
				<td style="width:120px;">Time Pulled</td>
				<td style="width:100px;">Trailer Number</td>
				<td style="width:100px;">Status</td>
				<td style="width:100px; text-align:right; padding-right:10px;">Fuel Level</td>
				<td style="width:150px;">Location</td>
				<td style="width:100px; text-align:right;">Set Temp</td>
				<td style="width:100px; text-align:right;">Return Temp</td>
				<td style="width:100px; text-align:right;">Supply Temp</td>
				<td style="width:100px; text-align:right;">Ambient Temp</td>
			</tr>
			<?php foreach($trailer_geopoints as $trailer_geopoint):?>
				<?php
					$row = $trailer_geopoint["id"];

					$background_color = "";
					if($i%2 == 0)
					{
						$background_color = "background:#F2F2F2;";
					}

					$i++;
					$location = urlencode($trailer_geopoint['location']);
					$pieces = explode(", ", $location);
//					$location = reverse_geocode($trailer_geopoint['latitude'] . "," . $trailer_geopoint['longitude']);
				?>
				<tr style="line-height:30px;<?=$background_color?>">
					<td style="width:50px;padding-left:5px;"><?=date('m/d/Y H:i',strtotime($trailer_geopoint['datetime_occurred']))?></td>
					<td style="width:50px;"><?=date('m/d/Y H:i',strtotime($trailer_geopoint['datetime_added']))?></td>
					<td style="width:45px;"><?=$trailer_geopoint['trailer_number']?></td>
					<td style="width:40px;"><?=$trailer_geopoint['status']?></td>
					<td style="width:35px; text-align:right; padding-right:10px;"><?=$trailer_geopoint['fuel_level']?>%</td>
					<td style="width:85px;">
						<?php if($trailer_geopoint['location'] != null): ?>
							<a target="_blank" href="https://www.google.com/maps?q=<?=$trailer_geopoint['latitude']?>,<?=$trailer_geopoint['longitude']?>">
								<?=$trailer_geopoint['location'] ?>
							</a>
						<?php else: ?>
							<a target="_blank" href="https://www.google.com/maps?q=<?=$trailer_geopoint['latitude']?>,<?=$trailer_geopoint['longitude']?>">
								GPS
							</a>
						<?php endif ?>
					</td>
					<td style="width:40px; text-align:right;"><?=number_format($trailer_geopoint['set_temperature'])?></td>
					<td style="width:40px; text-align:right;"><?=number_format($trailer_geopoint['return_temperature'])?></td>
					<td style="width:40px; text-align:right;"><?=number_format($trailer_geopoint['supply_temperature'])?></td>
					<td style="width:40px; text-align:right;"><?=number_format($trailer_geopoint['ambient_temperature'])?></td>
				</tr>
			<?php endforeach?>
		<?php endif ?>
	</table>
</div>