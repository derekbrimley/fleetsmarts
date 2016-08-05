<script>
	$("#scrollable_content").height($("#body").height() - 190);
</script>

<div id="main_content_header">
	<span style="font-weight:bold;">Applicants</span>
</div>
<div style="margin-top:5px;">
	<table style="margin-left:15px;">
		<tr class="heading">
			<td style="width:130px;">
				App Date
			</td>
			<td style="max-width:150px; min-width:150px;">
				Applicant
			</td>
			<td style="width:130px; padding-left:10px;">
				Status
			</td>
			<td style="max-width:555px; min-width:555px;">
				Applicant Log
			</td>
		</tr>
	</table>
</div>
<div id="scrollable_content" class="scrollable_div">
<?php foreach($applications as $app):?>
	<?php 
		//GET CLIENT
		$where = null;
		$where["id"] = $app["client_id"];
		$client = db_select_client($where);
	?>
	<div class="clickable_row" style="line-height:30px; height:30px;" onclick="load_driver_details('<?=$client["id"]?>')">
		<table style="margin-left:15px;">
			<tr>
				<td style="width:130px;">
					<?=date("m/d/y H:i",strtotime($app["application_datetime"]))?>
				</td>
				<td style="max-width:150px; min-width:150px;" class="ellipsis" title="<?=$app["f_name"]." ".$app["l_name"]?>">
					<?=$app["f_name"]." ".$app["l_name"]?>
				</td>
				<td style="width:130px; padding-left:10px;">
					<?=$client["client_status"]?>
				</td>
				<td style="max-width:555px; min-width:555px;" class="ellipsis" title="<?=$app["applicant_status_log"]?>">
					<?=$app["applicant_status_log"]?>
				</td>
			</tr>
		</table>
	</div>
<?php endforeach;?>
</div>