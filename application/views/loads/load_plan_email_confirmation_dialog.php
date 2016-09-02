<?php
	date_default_timezone_set('US/Mountain');
?>
<style>
	.email_block
	{
		height:30px;
		line-height:30px;
		padding-left:10px;
		font-size:16px;
		color:gray;
		//margin-bottom:10px;
	}
</style>
<div style="width:930px; margin:auto; margin-top:30px;">
	<div style="min-height:20px;">
	 <?php if(!empty($dispatch_update["email_sent_datetime"])):?>
		<?php
			$time_diff = time() - strtotime($dispatch_update["email_sent_datetime"]);
		?>
		Sent <?=hours_to_text_mixed($time_diff/60/60)?> ago
		<div style="float:right;"><?=date("m/d/y H:i",strtotime($dispatch_update["email_sent_datetime"]))?></div>
	 <?php endif;?>
	</div>
	<div  style=" border:solid 1px gray;">
		<div class="email_block" style="border-bottom:solid 1px gray;">
			<span style="color:black;">TO:</span> <?=$recipients?>
		</div>
		<div class="email_block">
			<span style="color:black;">SUBJECT:</span> Load Plan for Load <?=$load["customer_load_number"]?>
		</div>
	</div>
	<div class="scrollable_div" style="padding:10px; height:570px; border:solid 1px gray; border-top:none;">
		<?=$dispatch_update["email_html"]?>
	</div>
</div>
<input type="hidden" id="dispatch_update_email_dialog_id" name="dispatch_update_email_dialog_id" value="<?=$dispatch_update["id"]?>"/>
<input type="hidden" id="dispatch_email_load_id" value="<?=$load['id']?>"/>