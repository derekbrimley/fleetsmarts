<?=str_replace("\n","<br>",$invoice["invoice_notes"]);?>
<script>
	$("#notes_header").html("Invoice Notes - <?=$invoice["invoice_number"];?>");
</script>
