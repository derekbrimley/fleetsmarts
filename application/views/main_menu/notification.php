<script>
	<?php if(!empty($notification)):?>
		display_notification('<?=$notification["title"]?>','<?=$notification["text"]?>','<?=$notification["image_path"]?>', '<?=base_url($notification["action_url"])?>') 
	<?php endif;?>
</script>