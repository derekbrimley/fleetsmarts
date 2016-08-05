<span style="font-weight:bold;">Billing Notes - <?=$load["customer_load_number"];?></span>
<br>
<br>
<div style="height:215px; overflow:auto">
	<?=str_replace("\n","<br>",$load["billing_notes"]);?>
</div>