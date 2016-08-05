<script>
	//$("#people_list_div").height($("#main_content").height() - 449);
</script>

<?php foreach ($people as $client): ?>
	<div id="<?=$client["id"]?>" title="<?="client_id ".$client["id"]?>" class="left_bar_link_div" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 120px;" onclick="load_driver_details('<?=$client["id"]?>')">
		<?=$client["client_nickname"]?>
	</div>
<?php endforeach; ?>
