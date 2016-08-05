<div style="clear:both;"></div>
	<div class="ins_box" style="padding:10px; width:920px; margin:0 auto;">
		<div style="width:900px;">
			<span class="heading">Attachments</span>
		</div>
	</div>
	<div style="width:920px; margin:0 auto;">
		<?php if(!empty($attachments)): ?>
			<?php foreach($attachments as $attachment): ?>
				<div class="attachment_box" style="float:left;margin:5px; margin-bottom:30px;">
					<a target="_blank" title="Download <?=$attachment['attachment_name']?>" style="text-decoration:none;color:#4e77c9;" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>"><?=$attachment['attachment_name']?></a>
				</div>
			<?php endforeach ?>
		<?php else:?>
			<div style="height:30px;"></div>
		<?php endif ?>
	</div>