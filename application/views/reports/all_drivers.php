<script>
	$("#scrollable_content").height($("#body").height() - 185);
</script>
<div id="main_content_header">
	<span style="font-weight:bold;">Drivers in trucks <?=date('m/d/Y',strtotime($start_date))?> - <?=date('m/d/Y',strtotime($end_date))?></span>
	<span style="float:right; margin-left:40px; margin-right:20px; font-size:16px;"><?=$count?></span>
</div>
<div id="scrollable_content" class="scrollable_div" style="padding:15px;">
	<table>
		<?php if(!empty($drivers)): ?>
			<?php foreach($drivers as $driver):?>
				<tr>
					<td><?=$driver?></td>
				</tr>
			<?php endforeach?>
		<?php endif ?>
	</table>
</div>