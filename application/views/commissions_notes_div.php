
<span style="font-weight:bold;">Commission Notes for Load <?=$load["customer_load_number"];?>:</span>
<br>
<br>
<div style="height:230px; overflow:auto">
	<?=str_replace("\n","<br>",$load["commission_notes"]);?>
</div>
