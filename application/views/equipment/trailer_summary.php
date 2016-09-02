<script>
	//SET HEIGHT OF MAIN CONTENT AND SCROLLABLE CONTENT
	$("#main_content").height($(window).height() - 115);
	$("#scrollable_content").height($("#main_content").height() - 80);
</script>

<div id="main_content_header">
	<span style="font-weight:bold;">Trailer Summary View</span>
	<img src="/images/loading.gif" style="display:none;float:right;margin-top:4px;height:20px;" id="loading_img"/>
</div>

<div id="scrollable_content"  class="scrollable_div" style="font-size:12px; padding:20px;">
	<table style="margin-left:3px;">
		<tr class="heading" style="vertical-align:bottom; height:14px;">
			<td style="width:20px;">
			</td>
			<td style="width:60px;">
				Number
			</td>
			<td style="width:45px;">
				FM
			</td>
			<td style="width:85px;">
				Client
			</td>
			<td style="width:120px;">
				Vendor
			</td>
			<td style="width:140px;">
				VIN
			</td>
			<td style="width:100px;">
				Vents
			</td>
			<td style="width:70px;">
				Insulation
			</td>
			<td style="width:50px;">
				Rental
			</td>
			<td style="width:50px;">
				Period
			</td>
			<td style="width:50px; text-align:right;">
				Mileage
			</td>
		</tr>
	</table>
	<?php if(!empty($trailers)):?>
		<?php foreach($trailers as $trailer):?>
			<?php
				if($trailer["trailer_status"] == "On the road")
				{
					$img = "/images/on_the_road.png";
				}
				else if($trailer["trailer_status"] == "In the shop")
				{
					$img = "/images/in_the_shop.png";
				}
				else if($trailer["trailer_status"] == "Retired")
				{
					$img = "/images/turned_in.png";
				}
			?>
			<div style="padding-top:7px;padding-bottom:7px;" class="clickable_row">
				<table style="margin-left:3px; font-size:12px;">
					<tr>
						<td style="min-width:20px; max-width:20px;">
							<img title="<?=$trailer["trailer_status"]?>" style="height:15px; position:relative; bottom:2px" src="<?=$img?>"/>
						</td>
						<td style="min-width:60px; max-width:60px;" onclick="load_trailer_details('<?=$trailer["id"]?>')">
							<?=$trailer["trailer_number"]?>
						</td>
						<td style="min-width:45px; max-width:45px;" onclick="load_trailer_details('<?=$trailer["id"]?>')">
							<?=$trailer["fleet_manager"]["f_name"]?>
						</td>
						<td style="min-width:85px; max-width:85px;" onclick="load_trailer_details('<?=$trailer["id"]?>')" class='ellipsis'>
							<?=$trailer["client"]["client_nickname"]?>
						</td>
						<td style="min-width:120px; max-width:120px;" onclick="load_trailer_details('<?=$trailer["id"]?>')" class='ellipsis'>
							<?=$trailer["vendor"]["company_side_bar_name"]?>
						</td>
						<td style="min-width:140px; max-width:140px;" onclick="load_trailer_details('<?=$trailer["id"]?>')">
							<?=$trailer["vin"]?>
						</td>
						<td style="min-width:100px; max-width:100px;" onclick="load_trailer_details('<?=$trailer["id"]?>')">
							<?=$trailer["vent_type"]?>
						</td>
						<td style="min-width:70px; max-width:70px;" onclick="load_trailer_details('<?=$trailer["id"]?>')">
							<?=$trailer["insulation_type"]?>
						</td>
						<td style="min-width:50px; max-width:50px;" onclick="load_trailer_details('<?=$trailer["id"]?>')">
							<?=$trailer["rental_rate"]?>
						</td>
						<td style="min-width:50px; max-width:50px;" onclick="load_trailer_details('<?=$trailer["id"]?>')">
							<?=$trailer["rental_period"]?>
						</td>
						<td style="min-width:50px; max-width:50px; text-align:right;" onclick="load_trailer_details('<?=$trailer["id"]?>')">
							<?=$trailer["mileage_rate"]?>
						</td>
					</tr>
				</table>
			</div>
		<?php endforeach; ?>
	<?php else:?>
		No Results
	<?php endif;?>
</div>