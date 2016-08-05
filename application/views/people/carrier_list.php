<script>
	//$("#people_list_div").height($("#main_content").height() - 342);
</script>
<?php foreach ($carriers as $carrier): ?>
	<div  id="<?=$carrier["id"]?>" title="<?=$carrier["company_side_bar_name"]?>" class="left_bar_link_div" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 120px;" onclick="load_carrier_details('<?=$carrier["id"]?>')">
		<?=$carrier["company_side_bar_name"]?>
	</div>
<?php endforeach; ?>