<script>
	//SET HEIGHT OF MAIN CONTENT AND SCROLLABLE CONTENT
	$("#main_content").height($(window).height() - 115);
	$("#scrollable_content").height($(window).height() - 195);
</script>

<div id="main_content_header">
	<span style="font-weight:bold;">Truck Summary View</span>
	<div style="float:right; width:25px;">
		<img id="loading_img" name="loading_img" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
		<img id="refresh_list" name="refresh_list" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_truck_list()" />
	</div>
	<span style="font-weight:bold; float:right; margin-right:25px;"><?=count($trucks)?></span>
</div>

<table style="margin:5px;">
	<tr class="heading" style="font-size:12px; line-height:30px;">
		<td style="width:20px; padding-left:10px;">
		</td>
		<td style="width:60px; padding-left:15px;">
			Number
		</td>
		<td style="width:45px;">
			FM
		</td>
		<td style="width:95px;">
			DM
		</td>
		<td style="width:100px;">
			Make
		</td>
		<td style="width:50px;">
			Year
		</td>
		<td style="width:190px;">
			VIN
		</td>
		<td style="width:60px; padding-left:15px;">
			IFTA
		</td>
		<td style="width:100px;">
			Registration
		</td>
		<td style="width:65px; text-align:right; line-height:12px;">
			Current Odometer
		</td>
		<td style="width:65px; text-align:right; line-height:12px;">
			Miles till Sevice
		</td>
		<td style="width:65px; text-align:right; line-height:12px;">
			Insurance Status
		</td>
	</tr>
</table>
<div id="scrollable_content"  class="scrollable_div" style="font-size:12px;">
	<?php $i = 0; ?>
	<?php foreach($trucks as $truck):?>
		<?php
			$row_background_style = "";
			if($i%2 == 0)
			{
				$row_background_style = "background-color:#F7F7F7;";
			}
			$i++;
		
		
			if($truck["status"] == "On the road")
			{
				$img = "/images/on_the_road.png";
			}
			else if($truck["status"] == "In the shop")
			{
				$img = "/images/in_the_shop2.png";
			}
			else if($truck["status"] == "Subtruck")
			{
				$img = "/images/subtruck.png";
			}
			else if($truck["status"] == "Returned")
			{
				$img = "/images/turned_in.png";
			}
		?>
		<div style="padding-top:7px;padding-bottom:7px; <?=$row_background_style?>" class="clickable_row">
			<table style="margin-left:3px; font-size:12px;">
				<tr>
					<td style="width:20px; padding-left:10px;">
						<img title="<?=$truck["status"]?>" style="height:15px; position:relative; bottom:2px" src="<?=$img?>"/>
					</td>
					<td style="width:60px; padding-left:15px;" onclick="load_truck_details('<?=$truck["id"]?>')">
						<?=$truck["truck_number"]?>
					</td>
					<td style="width:45px;" onclick="load_truck_details('<?=$truck["id"]?>')">
						<?=$truck["fleet_manager"]["f_name"]?>
					</td>
					<td style=" min-width:95px; max-width:95px;" class="ellipsis" onclick="load_truck_details('<?=$truck["id"]?>')">
						<?=$truck["driver_manager"]["f_name"]?>
					</td>
					<td style="width:100px;" onclick="load_truck_details('<?=$truck["id"]?>')">
						<?=$truck["make"]?>
					</td>
					<td style="width:50px;" onclick="load_truck_details('<?=$truck["id"]?>')">
						<?=$truck["year"]?>
					</td>
					<td style="width:190px;" onclick="load_truck_details('<?=$truck["id"]?>')">
						<?=$truck["vin"]?>
					</td>
					<td style="width:60px; padding-left:15px;" onclick="">
						<?php if(!empty($truck['ifta_link'])):?>
							<a target="_blank" title="Open" href="<?=base_url("/index.php/documents/download_file")."/".$truck["ifta_link"]?>" onclick="">IFTA</a>
						<?php endif;?>
					</td>
					<td style="width:100px;" onclick="">
						<?php if(!empty($truck['registration_link'])):?>
							<a target="_blank" title="Open" href="<?=base_url("/index.php/documents/download_file")."/".$truck["registration_link"]?>" onclick="">Registration</a>
						<?php endif;?>
					</td>
					<td id="odomter_td_<?=$truck["id"]?>" style="width:65px; text-align:right;" onclick="load_truck_details('<?=$truck["id"]?>')" title="">
						<img id="" name="" src="/images/loading.gif" style="float:right; height:12px; padding-top:5px;" />
					</td>
					<td id="miles_till_service_td_<?=$truck["id"]?>" style="width:65px; text-align:right;" onclick="load_truck_details('<?=$truck["id"]?>')" title="">
						<img id="next_service_loading_img_<?=$truck["id"]?>" name="next_service_loading_img_<?=$truck["id"]?>" src="/images/loading.gif" style="float:right; height:12px; padding-top:5px;" />
					</td>
					<td id="ins_status_<?=$truck["id"]?>" style="width:65px; text-align:right;">
						<img id="" name="" src="/images/loading.gif" style="float:right; height:12px; padding-top:5px;" />
					</td>
				</tr>
			</table>
		</div>
		<script>get_current_odometer_for_truck('<?=$truck["id"]?>');</script>
		<script>get_miles_till_next_service('<?=$truck["id"]?>');</script>
		<script>get_insurance_status('<?=$truck["id"]?>');</script>
	<?php endforeach; ?>

</div>