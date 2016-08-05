<script>
	//$("#people_list_div").height($("#main_content").height() - 342);
</script>
<?php foreach ($companies as $company): ?>
	<div  id="<?=$company["id"]?>" title="<?=$company["company_side_bar_name"]." ".$company["id"]?>" class="left_bar_link_div" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 120px;" onclick="load_business_user_details('<?=$company["id"]?>')">
		<?=$company["company_side_bar_name"]?>
	</div>
<?php endforeach; ?>