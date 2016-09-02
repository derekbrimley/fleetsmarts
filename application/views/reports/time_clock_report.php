<?php
	$i=0;
?>
<script>
	$("#scrollable_content").height($("#body").height() - 185);
</script>
<div id="main_content_header">
	<span style="font-weight:bold;">
		<?= $title ?>
	</span>
	<span>
		<img id="report_loading_icon" name="report_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
	</span>
	<span>
		<img id="refresh_icon" name="refresh_icon" src="/images/refresh.png" title="Refresh Report" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_time_clock_report()" />
	</span>
	<span id="time_clock_count" style="float:right; margin-left:40px; margin-right:20px; font-size:16px;">
		<?=count($clock_in_verifications)?>
	</span>
</div>
<div id="scrollable_content" class="scrollable_div" style="width:100%;font-size:9pt;">
	<table style="table-layout: fixed;font-size: 12px; margin:10px;">
		<tr class="heading" style="line-height:30px;">
			<td style="width:120px;padding-left:5px;">Time Email Sent</td>
			<td style="width:120px;">Time Uploaded</td>
			<td style="width:100px;">User</td>
			<td style="width:100px;">Screenshot</td>
		</tr>
		<?php if(!empty($clock_in_verifications)): ?>
			<?php foreach($clock_in_verifications as $clock_in_verification):?>
				<?php
					$row = $clock_in_verification["id"];

					$background_color = "";
					if($i%2 == 0)
					{
						$background_color = "background:#F2F2F2;";
					}

					$i++;
		
					$user_id = $clock_in_verification['user_id'];
		
					$where = null;
					$where['id'] = $user_id;
					$user = db_select_user($where);
				?>
				<tr style="line-height:30px;<?=$background_color?>">
					<td style="width:50px;padding-left:5px;"><?=date('m/d/Y H:i',strtotime($clock_in_verification['email_sent_datetime']))?></td>
					<td style="width:50px;">
						<?php if($clock_in_verification['screenshot_uploaded_datetime']): ?>
							<?=date('m/d/Y H:i',strtotime($clock_in_verification['screenshot_uploaded_datetime']))?>
						<?php endif ?>
					</td>
					<td style="width:45px;"><?=$user['person']['full_name']?></td>
					<td style="width:40px;">
						<?php if($clock_in_verification['screenshot_guid']): ?>
							<a target="_blank" href="<?=base_url('index.php/documents/download_file/' . $clock_in_verification['screenshot_guid'])?>">Verified</a>
						<?php endif ?>
					</td>
				</tr>
			<?php endforeach?>
		<?php endif ?>
	</table>
</div>