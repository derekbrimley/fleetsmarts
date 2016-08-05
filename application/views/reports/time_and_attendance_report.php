<script>
	$("#scrollable_content").height($("#body").height() - 195);
</script>
<style>
	.entry_rows td
	{
		padding-top:10px;
		padding-bottom:10px;
	}
</style>
<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:left; font-weight:bold;">Time and Attendance Report</div>
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_time_and_attendance_report()" />
		</div>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:40px;" VALIGN="top"></td>
		<td style="width:100px;" VALIGN="top">Type</td>
		<td style="width:150px;" VALIGN="top">Employee</td>
		<td style="width:150px;" VALIGN="top">Datetime</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<table  style="table-layout:fixed; margin:5px; font-size:12px;">
		<?php
			$i = 0;
		?>
		<?php foreach($punches as $punch):?>
			<?php
				$i++;
				
				$background_color = "";
				if($i%2 == 1)
				{
					$background_color = "background-color:#F2F2F2;";
				}
				
				//GET USER
				$where = null;
				$where["id"] = $punch["user_id"];
				$user = db_select_user($where);
			?>
			<tr class="entry_rows" style="<?=$background_color?> min-height:30px;">
				<td style="width:40px;">
					<?php if($punch["in_out"] == "In"):?>
						<img src="/images/green_dot.png" style="height:15px; position:relative; top:0px;"/>
					<?php else:?>
						<img src="/images/red_dot.png" style="height:15px; position:relative; top:0px;"/>
					<?php endif;?>
				</td>
				<td style="width:100px;">
					<?=$punch["in_out"]?>
				</td>
				<td style="width:150px;">
					<?=$user["person"]["full_name"]?>
				</td>
				<td style="width:150px;">
					<?=date('m/d/y H:i:s',strtotime($punch["datetime"]))?>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
</div>