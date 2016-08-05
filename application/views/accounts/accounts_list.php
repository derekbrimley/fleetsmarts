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
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_accounts()" />
		</div>
		<div id="expense_total" class="header_stats"  style="float:right; width:150px; margin-right:20px; font-weight:bold;"></div>
		<div id="count_total" class="header_stats"  style="float:right; width:120px; font-weight:bold;"></div>
		<div style="float:left; font-weight:bold;"><?=$business_user["company_name"]?></div>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:300px; padding-left:5px;" VALIGN="top">Account Name</td>
		<td style="width:300px;" VALIGN="top">Category</td>
		<td style="width:120px;" VALIGN="top">Type</td>
		<td style="width:120px;" VALIGN="top">Class</td>
		<td style="width:100px; text-align:right;" VALIGN="top">Balance</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php 
		$i = 0;
		$expense_total = 0;
	?>
	<?php if(!empty($accounts)):?>
		<?php foreach($accounts as $account):?>
			<?php
				$row = $account["id"];

				$background_color = "";
				if($i%2 == 0)
				{
					$background_color = "background:#F2F2F2;";
					//$background_color = "background:#CFCFCF;";
				}
				
				$i++;
			?>
			<div id="tr_<?=$row?>" name="tr_<?=$row?>" style=" margin-left:5px; margin-right:5px; min-height:30px; <?=$background_color?>"  class="clickable_row">
				<?php include("account_row.php"); ?>
			</div>
			<div id="sub_accounts_div_<?=$row?>" style="display:none; padding:1px; <?=$background_color?> margin-left:5px; margin-right:5px;">
				<!-- AJAX GOES HERE !-->
			</div>
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
</script>
