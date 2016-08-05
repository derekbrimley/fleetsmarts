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
		<div style="float:left; font-weight:bold;">Driver Hold Report - <?=$hold_report["client"]["client_nickname"]?></div>
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_driver_hold_report()" />
		</div>
	</div>
</div>
<div id="scrollable_content" class="scrollable_div">
	<div style="margin-left:20px; margin-top:20px;">
		<a href="https://docs.google.com/document/d/1qWatW0PRT3_UYR3ZWZt5qOFeZLh9D0-Q1o_2c28nN30/edit" target="_blank">How do I fix Missing BOL Pics?</a><br><br>
		<a href="https://docs.google.com/document/d/10K7iJhmOPbK8huZJtCK-PV9vKqcAO9te6biwUJaaQZQ/edit" target="_blank">How do I fix Missing BOL Scans?</a><br><br>
		<a href="https://docs.google.com/document/d/1w-nXJwIAN5AVpFlF_aLTJrt-A31aUYBdLSGjFklyQL0/edit" target="_blank">How do I fix Missing Receipts?</a><br><br>
	</div>
	<?php if($hold_report["hold_status"] == "No Hold"):?>
		<div style="background-color:green; color:white; margin-top:20px; width:100%; text-align:center; font-size:40px; font-weight:bold;">
			NO HOLD
		</div>
	<?php else:?>
		<div style="background-color:red; color:white; margin-top:20px; width:100%; text-align:center; font-size:40px; font-weight:bold;">
			HOLD
		</div>
	<?php endif;?>
	<div style="margin:20px;">
		<table style="float:left;">
			<tr class="heading">
				<td style="width:200px;">
					Missing BOL Pics (<?=count($hold_report["loads_missing_dc"])?>)
				</td>
			</tr>
			<?php if(!empty($hold_report["loads_missing_dc"])):?>
				<?php foreach($hold_report["loads_missing_dc"] as $load):?>
					<tr>
						<td>
							<span title="<?=get_final_drop_goalpoint($load["id"])["location"]?>"><?=$load["customer_load_number"]?></span>
						<td>
					</tr>
				<?php endforeach;?>
			<?php endif;?>
		</table>
		<table style="float:left;">
			<tr class="heading">
				<td style="width:200px;">
					Missing BOL Scan (<?=count($hold_report["loads_missing_hc"])?>)
				</td>
			</tr>
			<?php if(!empty($hold_report["loads_missing_hc"])):?>
				<?php foreach($hold_report["loads_missing_hc"] as $load):?>
					<tr>
						<td>
							<span title="<?=get_final_drop_goalpoint($load["id"])["location"]?>"><?=$load["customer_load_number"]?></span>
						<td>
					</tr>
				<?php endforeach;?>
			<?php endif;?>
		</table>
		<table style="float:left;">
			<tr class="heading">
				<td style="width:200px;">
					Missing Receipts (<?=count($hold_report["client_expenses"])?>)
				</td>
			</tr>
			<?php if(!empty($hold_report["client_expenses"])):?>
				<?php foreach($hold_report["client_expenses"] as $ce):?>
					<tr>
						<td>
							<span title="<?=date("m/d/y",strtotime($ce["expense_datetime"]))?> <?=$ce["description"]?>">$<?=number_format($ce["expense_amount"],2)?></span>
						<td>
					</tr>
				<?php endforeach;?>
			<?php endif;?>
		</table>
		<div style="clear:both;"></div>
	</div>
</div>