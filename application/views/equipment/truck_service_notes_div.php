<span style="font-weight:bold;">Service Log - Truck <?=$truck["truck_number"];?></span>
<br>
<br>
<div style="height:215px; overflow:auto">
	<?=str_replace("\n","<br>",$truck["service_log_notes"]);?>
</div>
