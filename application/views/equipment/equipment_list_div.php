<span class="heading"><?=$equipment_type?></span>
<hr/>
<?php if($equipment_type == "Trucks"): ?>
	
	<script>
		$("#truck_list").height($("#body").height() - 490);
	</script>
	<div id="truck_list" >
		<?php if(!empty($trucks)): ?>
			<?php foreach ($trucks as $truck): ?>
				<div id="truck_link_<?=$truck["id"]?>" class="left_bar_link_div" style="width:130px;" onclick="load_truck_details('<?=$truck["id"]?>')">
					<?=$truck["truck_number"] ?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<script>
		$("#truck_link_"+previously_selected_truck_id).attr('class', 'left_bar_link_div left_bar_link_selected');
	</script>
<?php elseif($equipment_type == "Trailers"): ?>
	<script>
		$("#trailer_list").height($("#body").height() - 490);
	</script>
	<div id="trailer_list">
		<?php foreach ($trailers as $trailer): ?>
			<div id="trailer_link_<?=$trailer["id"]?>" class="left_bar_link_div" style="width:130px;" onclick="load_trailer_details('<?=$trailer["id"]?>')">
				<?=$trailer["trailer_number"] ?>
			</div>
		<?php endforeach; ?>
	</div>
	<script>
		$("#trailer_link_"+previously_selected_trailer_id).attr('class', 'left_bar_link_div left_bar_link_selected');
	</script>
<?php endif; ?> 