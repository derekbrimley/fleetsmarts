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
		<div style="float:right;">
			<span style="margin-right:15px;">Balance <?=number_format(get_account_balance($account["id"]),2);?></span>
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_account_details('<?=$account["id"]?>')" />
			<img id="back_icon" name="back_icon" src="/images/back.png" title="Back" style="cursor:pointer; float:right; height:20px; padding-top:5px; margin-right:10px;" onclick="load_accounts()" />
		</div>
		<div id="expense_total" class="header_stats"  style="float:right; width:150px; margin-right:20px; font-weight:bold;"></div>
		<div id="count_total" class="header_stats"  style="float:right; width:120px; font-weight:bold;"></div>
		<div style="float:left; font-weight:bold;" title="<?=$account["id"]?>" onclick=""><?=$company["company_side_bar_name"]?> | <?=$account["account_name"]?></div>
	</div>
</div>
<table  style="table-layout:fixed;font-size:12px; margin:5px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:120px; padding-left:5px;" VALIGN="top">Recorded</td>
		<td style="width:80px;" VALIGN="top">Date</td>
		<td style="width:100px;" VALIGN="top">Transaction</td>
		<td style="width:60px;" VALIGN="top">Link</td>
		<td style="width:350px;" VALIGN="top">Description</td>
		<td style="width:90px; text-align:right;" VALIGN="top">Debit</td>
		<td style="width:90px; text-align:right;" VALIGN="top">Credit</td>
		<td style="width:90px; text-align:right;" VALIGN="top">Balance</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php 
		$i = 0;
	?>
	<?php if(!empty($account_entries)):?>
		<?php foreach($account_entries as $entry):?>
			<?php
				$row = $entry["id"];

				$background_color = "";
				if($i%2 == 0)
				{
					$background_color = "background:#F2F2F2;";
					//$background_color = "background:#CFCFCF;";
				}
				
				$i++;
				
				//GET RECORDER
				$where = null;
				$where["id"] = $entry["recorder_id"];
				$recorder_user = db_select_user($where);
				
				//CREATE DEBIT AND CREDIT TEXT
				$credit_text = "";
				$debit_text = "";
				if($entry["debit_credit"] == "Credit")
				{
					$credit_text = number_format($entry["entry_amount"],2);
				}
				else if($entry["debit_credit"] == "Debit")
				{
					$debit_text = number_format($entry["entry_amount"],2);
				}
				
			?>
			<div id="tr_<?=$row?>" name="tr_<?=$row?>" style=" margin-left:5px; margin-right:5px; min-height:30px; <?=$background_color?>"  class="">
				<table  style="table-layout:fixed; font-size:12px;">
					<tr class="" style="line-height:30px;">
						<td style="width:120px; padding-left:5px;" VALIGN="top" title="<?=$recorder_user["person"]["f_name"]?>"><?=date('m/d/y H:i',strtotime($entry["recorded_datetime"]))?></td>
						<td style="width:80px;" VALIGN="top"><?=date('m/d/y',strtotime($entry["entry_datetime"]))?></td>
						<td style="width:100px;" VALIGN="top">
							<a target="_blank" href="<?=base_url("/index.php/accounts/view_transaction_details")."/".$entry["transaction_id"]?>" onclick=""><?=$entry["transaction_id"]?></a>
						</td>
						<td style="width:60px;" VALIGN="top">
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$entry["file_guid"]?>" onclick="">Link</a>
						</td>
						<td style="width:350px; line-height:20px; padding-top:5px;" VALIGN="top"><?=$entry["entry_description"]?></td>
						<td style="width:90px; text-align:right;" VALIGN="top"><?=$debit_text?></td>
						<td style="width:90px; text-align:right;" VALIGN="top"><?=$credit_text?></td>
						<td style="width:90px; text-align:right;" VALIGN="top"><?=number_format($entry["account_balance"],2)?></td>
					</tr>
				</table>
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
