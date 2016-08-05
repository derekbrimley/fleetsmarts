<style>
	.event_details_table tr
	{
		height:20px;
	}
	
	.event_details_text_box
	{
		width:60px;
		font-size:12px;
		height: 18px;
		position: relative;
		bottom: 3px;
		text-align:right;
	}
	
	.edit_<?=$log_entry_id?>
	{
		display:none;
	}
</style>
<script>
	
	refresh_event('<?=$log_entry_id?>');
	
	
	
	function edit_event(log_entry_id)
	{
		$('.edit_'+log_entry_id).css({"display":"block"});
		$('.details_'+log_entry_id).css({"display":"none"});
	}
	
	
	
</script>

<div style="height:160;">
	<?php if(empty($log_entry["locked_datetime"])): ?>
		<div style="width:20px; float:right;">
			<img id="refresh_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:13px; position:relative; left:0px;" src="/images/refresh.png" title="Refresh" onclick="this.src='/images/loading.gif'; open_event_details('<?=$log_entry_id?>')"/>
			<img id="edit_icon" class="details_<?=$log_entry_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:0px;" src="/images/edit.png" title="Edit" onclick="edit_event('<?=$log_entry_id?>')"/>
			<img id="save_icon_<?=$log_entry_id?>" class="edit_<?=$log_entry_id?>" style="display:none; margin-bottom:13px; margin-right:15px; cursor:pointer; height:14px; position:relative; left:1px;" src="/images/save.png" title="Save" onclick="this.src='/images/loading.gif';"/>
			<img id="attachment_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:18px; position:relative; left:3px;" src="/images/paper_clip2.png" title="Attach Document" onclick="open_file_upload('<?=$log_entry_id?>')"/>
			<img id="unlocked_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/unlocked.png" title="Lock" onclick="lock_event('<?=$log_entry_id?>')"/>
			<img id="new_checkpoint" style="margin-bottom:13px; margin-right:15px; cursor:pointer; height:16px; position:relative; left:2px;" src="/images/new_checkpoint.png" title="New Checkpoint" onclick="this.src='/images/loading.gif'; create_new_checkpoint('<?=$log_entry["id"]?>');"/>
			<img id="delete_icon" style="display:block; margin-right:15px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/trash.png" title="Delete" onclick="delete_event('<?=$log_entry_id?>')"/>
		</div>
		<div style="font-weight:bold; font-size:12px; float:left;">
			<span style="position:relative; bottom:4px;"><?=$log_entry["entry_type"];?></span>
		</div>
	<?php else: ?>
		<div style="width:70px; float:left;">
			<div style="font-size:12px; font-weight:bold;">
				<?=$log_entry["entry_type"];?>
			</div>
		</div>
		<div style="width:20px; float:right;">
			<img id="locked_icon" style="display:block; cursor:pointer; margin-bottom:12px; margin-right:15px; height:15px; position:relative; left:2px;" src="/images/locked.png" title="Locked <?=date("n/j H:i",strtotime($log_entry["locked_datetime"]))?>"/>
			<img id="attachment_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:18px; position:relative; left:3px;" src="/images/paper_clip2.png" title="Attach Document" onclick="open_file_upload('<?=$log_entry_id?>')"/>
		</div>
	<?php endif; ?>
	<div style="margin-left:72px; width:50px; float:left;">
		Notes:
	</div>
	<div style="width:700px; margin-left:130px; height:100px;">
		<?=$log_entry["entry_notes"]?>
	</div>
	<div id="attachment_div" style="margin-left:140px;">
		<?php if(!empty($attachments)):?>
			<?php foreach($attachments as $attachment):?>
				<a href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>" onclick=""><?=$attachment["attachment_name"]?></a>&nbsp;
			<?php endforeach;?>
		<?php endif;?>
	</div>
</div>
