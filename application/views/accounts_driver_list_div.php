<?php foreach ($all_clients as $client): ?>
	<?php $selected = ""; ?>
	<?php if ($client['company']['id'] == $company_id): ?>
		<?php $selected = " color:#DD4B39; font-weight:bold;"//background: #DCDCDC;" ?>
	<?php endif ?>
	
	<div class="left_bar_link_div" style=" <?=$selected?> " onclick="location.href='<?= base_url("index.php/accounts/index/$account_type/".$client['company']['id']."/$company_filter/All");?>'">
		<?=$client["company"]["company_side_bar_name"]?>
	</div>
<?php endforeach; ?>
