<style>
	.audit_image
	{
		height:16px;
		position:relative;
		bottom:3px;
	}
</style>
<?php
	//GET USER
	$where = null;
	$where["id"] = $process_audit["user_id"];
	$user = db_select_user($where);
?>
<table style="margin:auto; margin-top:30px;">
	<tr>
		<td style="width:150px;">
			Audit Date
		</td>
		<td style="width:100px; text-align:center;">
			<?=date("m/d/y H:i",strtotime($process_audit["audit_datetime"]))?>
		</td>
	</tr>
	<tr>
		<td>
			Auditor
		</td>
		<td style="text-align:center;">
			<?=$user["person"]["full_name"]?>
		</td>
	</tr>
	<tr>
		<td>
			Defer to Tarriff
		</td>
		<td style="text-align:center;">
			<?php if($process_audit["defer_to_tarriff"] == "Pass"):?>
				<img class="audit_image" src="/images/green_checkmark.png">
			<?php elseif($process_audit["defer_to_tarriff"] == "Fail"):?>
				<img class="audit_image" src="/images/red_exclamation_mark.png">
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td>
			Ontime According to RC
		</td>
		<td style="text-align:center;">
			<?php if($process_audit["ontime_by_rc"] == "Pass"):?>
				<img class="audit_image" src="/images/green_checkmark.png">
			<?php elseif($process_audit["ontime_by_rc"] == "Fail"):?>
				<img class="audit_image" src="/images/red_exclamation_mark.png">
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td>
			Shipper Load and Count
		</td>
		<td style="text-align:center;">
			<?php if($process_audit["shipper_load_and_count"] == "Pass"):?>
				<img class="audit_image" src="/images/green_checkmark.png">
			<?php elseif($process_audit["shipper_load_and_count"] == "Fail"):?>
				<img class="audit_image" src="/images/red_exclamation_mark.png">
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td>
			Seal Pic (Departure)
		</td>
		<td style="text-align:center;">
			<?php if($process_audit["seal_pic_depart"] == "Pass"):?>
				<img class="audit_image" src="/images/green_checkmark.png">
			<?php elseif($process_audit["seal_pic_depart"] == "Fail"):?>
				<img class="audit_image" src="/images/red_exclamation_mark.png">
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td>
			Load Pic (Departure)
		</td>
		<td style="text-align:center;">
			<?php if($process_audit["load_pic_depart"] == "Pass"):?>
				<img class="audit_image" src="/images/green_checkmark.png">
			<?php elseif($process_audit["load_pic_depart"] == "Fail"):?>
				<img class="audit_image" src="/images/red_exclamation_mark.png">
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td>
			Seal Number on Bills
		</td>
		<td style="text-align:center;">
			<?php if($process_audit["seal_number"] == "Pass"):?>
				<img class="audit_image" src="/images/green_checkmark.png">
			<?php elseif($process_audit["seal_number"] == "Fail"):?>
				<img class="audit_image" src="/images/red_exclamation_mark.png">
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td>
			Seal Pic (Arrival)
		</td>
		<td style="text-align:center;">
			<?php if($process_audit["seal_pic_arrive"] == "Pass"):?>
				<img class="audit_image" src="/images/green_checkmark.png">
			<?php elseif($process_audit["seal_pic_arrive"] == "Fail"):?>
				<img class="audit_image" src="/images/red_exclamation_mark.png">
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td>
			Load Pic (Arrival)
		</td>
		<td style="text-align:center;">
			<?php if($process_audit["load_pic_arrive"] == "Pass"):?>
				<img class="audit_image" src="/images/green_checkmark.png">
			<?php elseif($process_audit["load_pic_arrive"] == "Fail"):?>
				<img class="audit_image" src="/images/red_exclamation_mark.png">
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td>
			Seal Intact on Bills
		</td>
		<td style="text-align:center;">
			<?php if($process_audit["seal_intact"] == "Pass"):?>
				<img class="audit_image" src="/images/green_checkmark.png">
			<?php elseif($process_audit["seal_intact"] == "Fail"):?>
				<img class="audit_image" src="/images/red_exclamation_mark.png">
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td>
			Clean Bills
		</td>
		<td style="text-align:center;">
			<?php if($process_audit["clean_bills"] == "Pass"):?>
				<img class="audit_image" src="/images/green_checkmark.png">
			<?php elseif($process_audit["clean_bills"] == "Fail"):?>
				<img class="audit_image" src="/images/red_exclamation_mark.png">
			<?php endif;?>
		</td>
	</tr>
</table>
