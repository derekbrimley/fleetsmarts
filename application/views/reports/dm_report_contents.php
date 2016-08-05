<div style="width:750px; margin:auto; padding:20px;">
	<div style="width:750px; margin-bottom:15px;">
		<div style="float:left;">
			<?=$fm_company["company_side_bar_name"]?>
		</div>
		<div style="float:right;">
			<?=$now_datetime?>
		</div>
		<div style="font-size:20px; width:200px; margin: auto;">
			Daily Manager Report
		</div>
	</div>
	<div id="rate_cons" style="margin-bottom:20px;">
		<div class="heading" style="font-weight:bold;">Paperwork</div>
		<hr style="width:750px;"/>
		<table  style="table-layout:fixed; margin:5px; margin-left:10px; font-size:12px;">
			<tr style="line-height:30px;">
				<td class="indent" style="width:200px;" VALIGN="top">Missing BOLs</td>
				<td style="width:130px;" VALIGN="top"></td>
				<td style="width:130px;" VALIGN="top"></td>
				<td style="width:130px; text-align:right;" VALIGN="top" title="">$<?=number_format($stats["missing_bols_total"],2)?></td>
				<td style="width:130px; text-align:right;" VALIGN="top"><?=$stats["missing_bols"]?></td>
			</tr>
		</table>
	</div>
	<div id="logs" style="margin-bottom:20px;">
		<div class="heading" style="font-weight:bold;">Logs</div>
		<hr style="width:750px;"/>
		<table  style="table-layout:fixed; margin:5px; margin-left:10px; font-size:12px;">
			<tr style="font-weight:bold; line-height:30px;">
				<td class="indent" style="width:200px;" VALIGN="top">Truck</td>
				<td style="width:130px;" VALIGN="top">Check Call</td>
				<td style="width:130px;" VALIGN="top">Locks</td>
				<td style="width:130px; text-align:right;" VALIGN="top">Complete Till</td>
				<td style="width:130px; text-align:right;" VALIGN="top">Days Behind</td>
			</tr>
			<?php
				$total_days = 0;
			?>
			<?php foreach($stats["log_stats"] as $log_stat):?>
				<?php
					if(empty($log_stat["log_entry"]))
					{
						$entry_datetime_text = "Complete";
						$days_behind = 0;
					}
					else
					{
						$entry_datetime_text = date("m/d/y H:i",strtotime($log_stat["log_entry"]["entry_datetime"]));
						$days_behind = round((time() - strtotime($log_stat["log_entry"]["entry_datetime"]))/(60*60*24));
					}
					
					$total_days = $total_days + $days_behind;
				?>
				<tr style="line-height:30px;">
					<td class="indent" style="width:200px;" VALIGN="top"><?=$log_stat["truck"]["truck_number"]?></td>
					<td style="width:130px;" VALIGN="top"></td>
					<td style="width:130px;" VALIGN="top"></td>
					<td style="width:130px; text-align:right;" VALIGN="top" title="<?=$log_stat["log_entry"]["entry_type"]." ".$log_stat["log_entry"]["city"];?>"><?=$entry_datetime_text?></td>
					<td style="width:130px; text-align:right;" VALIGN="top"><?=$days_behind?></td>
				</tr>
			<?php endforeach;?>
			<tr style="border-top: solid #CFCFCF 1px; font-weight:bold; line-height:30px;">
				<td class="indent" style="width:200px;" VALIGN="top">TOTAL</td>
				<td style="width:130px;" VALIGN="top"></td>
				<td style="width:130px;" VALIGN="top"></td>
				<td style="width:130px; text-align:right;" VALIGN="top" title=""></td>
				<td style="width:130px; text-align:right;" VALIGN="top"><?=$total_days?></td>
			</tr>
		</table>
	</div>
	<div id="rate_cons" style="margin-bottom:20px;">
		<div class="heading" style="font-weight:bold;">Rate Cons</div>
		<hr style="width:750px;"/>
		<table  style="table-layout:fixed; margin:5px; margin-left:10px; font-size:12px;">
			<tr style="line-height:30px;">
			<td class="indent" style="width:200px;" VALIGN="top">Missing Rate Cons</td>
			<td style="width:130px;" VALIGN="top"></td>
			<td style="width:130px;" VALIGN="top"></td>
			<td style="width:130px; text-align:right;" VALIGN="top" title="">$<?=number_format($stats["missing_rate_cons_total"],2)?></td>
			<td style="width:130px; text-align:right;" VALIGN="top"><?=$stats["missing_rate_cons"]?></td>
		</tr>
		</table>
	</div>
	<div id="rate_cons" style="margin-bottom:20px;">
		<div class="heading" style="font-weight:bold;">Expenses</div>
		<hr style="width:750px;"/>
		<table  style="table-layout:fixed; margin:5px; margin-left:10px; font-size:12px;">
			<tr style="line-height:30px;">
				<td class="indent" style="width:200px;" VALIGN="top">Unlocked Expenses</td>
				<td style="width:130px;" VALIGN="top"></td>
				<td style="width:130px;" VALIGN="top"></td>
				<td style="width:130px; text-align:right;" VALIGN="top" title="">$<?=number_format($stats["unlocked_expense_total"],2)?></td>
				<td style="width:130px; text-align:right;" VALIGN="top"><?=$stats["unlocked_expense_count"]?></td>
			</tr>
			<tr style="line-height:30px;">
				<td class="indent" style="width:200px;" VALIGN="top">Unrecorded Expenses</td>
				<td style="width:130px;" VALIGN="top"></td>
				<td style="width:130px;" VALIGN="top"></td>
				<td style="width:130px; text-align:right;" VALIGN="top" title="">$<?=number_format($stats["unrecorded_expense_total"],2)?></td>
				<td style="width:130px; text-align:right;" VALIGN="top"><?=$stats["unrecorded_expense_count"]?></td>
			</tr>
		</table>
	</div>
	<div id="rate_cons" style="margin-bottom:20px;">
		<div class="heading" style="font-weight:bold;">Receipts</div>
		<hr style="width:750px;"/>
		<table  style="table-layout:fixed; margin:5px; margin-left:10px; font-size:12px;">
			<tr style="line-height:30px;">
				<td class="indent" style="width:200px;" VALIGN="top">Missing Receipts</td>
				<td style="width:130px;" VALIGN="top"></td>
				<td style="width:130px;" VALIGN="top"></td>
				<td style="width:130px; text-align:right;" VALIGN="top" title="">$<?=number_format($stats["missing_receipts_total"],2)?></td>
				<td style="width:130px; text-align:right;" VALIGN="top"><?=$stats["missing_receipts"]?></td>
			</tr>
		</table>
	</div>
	<div id="rate_cons" style="margin-bottom:20px;">
		<div class="heading" style="font-weight:bold;">Statements</div>
		<hr style="width:750px;"/>
		<table  style="table-layout:fixed; margin:5px; margin-left:10px; font-size:12px;">
			<tr style="line-height:30px;">
				<td class="indent" style="width:200px;" VALIGN="top">Unapproved Statements</td>
				<td style="width:130px;" VALIGN="top"></td>
				<td style="width:130px;" VALIGN="top"></td>
				<td style="width:130px; text-align:right;" VALIGN="top" title=""></td>
				<td style="width:130px; text-align:right;" VALIGN="top"><?=$stats["unapproved_statements"]?></td>
			</tr>
		</table>
	</div>
</div>