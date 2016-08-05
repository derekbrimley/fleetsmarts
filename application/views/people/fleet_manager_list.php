<?php foreach ($fleet_managers as $fm_company): ?>
	<div  id="<?=$fm_company["id"]?>" title="<?=$fm_company["company_side_bar_name"]?>" class="left_bar_link_div" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 120px;" onclick="load_fleet_manager_details('<?=$fm_company["id"]?>')">
		<?=$fm_company["company_side_bar_name"]?>
	</div>
<?php endforeach; ?>