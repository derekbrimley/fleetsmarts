<?=str_replace("\n","<br>",$driver_app["applicant_status_log"]);?>
<script>
	$("#notes_header").html("Applicant Status Log - <?=$driver_app["f_name"]?> <?=$driver_app["l_name"]?>");
</script>