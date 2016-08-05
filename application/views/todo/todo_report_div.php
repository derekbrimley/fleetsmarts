<script>
	$("#scrollable_content").height($("#body").height() - 195);
</script>
<style>
	.clickable_row:hover
	{
		background:#D5D5D5!important;
	}
</style>
<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_report()" />
		</div>
		<div id="count_total" class="header_stats"  style="float:right; width:120px; font-weight:bold;"></div>
		<div style="float:left; font-weight:bold;">ToDos</div>
	</div>
</div>
<table  style="table-layout:fixed; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:30px; padding-left:5px;" VALIGN="top"></td>
		<td style="width:70px;" VALIGN="top">Owner</td>
		<td style="width:70px; padding-left:5px;" VALIGN="top">Manager</td>
		<td style="width:50px; padding-left:5px;" VALIGN="top">Type</td>
		<td style="min-width:570px; max-width:570px; padding-left:5px;" VALIGN="top">Description</td>
		<td style="width:75px; padding-left:5px;" VALIGN="top">Due Date</td>
		<td style="width:75px;" VALIGN="top">Complete</td>
		<td style="width:30px;" VALIGN="top"></td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php 
		$i = 0;
		$expense_total = 0;
		$this_user_id = $this->session->userdata('user_id');
	?>
	<?php if(!empty($action_items)):?>
		<?php foreach($action_items as $action_item):?>
			<?php if(($this_user_id == $action_item["owner_id"] || $this_user_id == $action_item["manager_id"]) || user_has_permission('view all ToDos')):?>
				<?php
					$row = $action_item["id"];

					$background_color = "";
					if($i%2 == 0)
					{
						$background_color = "background:#F2F2F2;";
						//$background_color = "background:#CFCFCF;";
					}
					
					$i++;
				?>
				<div id="tr_<?=$row?>" name="tr_<?=$row?>" style=" margin-left:5px; margin-right:5px; height:30px; <?=$background_color?>"  class="clickable_row">
					<?php include("todo_row.php"); ?>
				</div>
			<?php endif;?>
		<?php endforeach;?>
	<?php else: ?>
		<table  style="table-layout:fixed; margin:5px; font-size:12px;">
			<tr>
				<td style="font-weight:bold; padding-left:40px;">
					There are no results for this filter set
				</td>
			</tr>
		</table>
	<?php endif; ?>
</div>
<script>
	$("#count_total").html("Count <?=$i?>");
</script>
