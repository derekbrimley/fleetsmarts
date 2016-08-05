<?php foreach ($clients as $client): ?>
	<?php $selected = " font-weight:bold;"//background: #DCDCDC;" ?>
	<?php $selected = ""; ?>
	
	<div class="left_bar_link_div" style="with:145px; <?=$selected?> " onclick="location.href='<?= base_url("index.php/clients/index/details/".$client['id']);?>'">
		<?=$client["client_nickname"]?>
	</div>
<?php endforeach; ?>