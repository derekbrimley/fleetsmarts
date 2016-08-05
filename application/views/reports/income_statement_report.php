<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:right; width:25px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_statement" name="refresh_statement" src="/images/refresh.png" title="Refresh Income Statement" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_income_statement()" />
		</div>
		<div style="float:left; font-weight:bold;"><?=$business_user['company_name']?> Income Statement</div>
	</div>
</div>
<script>
	$("#income_statement_container").height($(window).height() - 165)
</script>
<div style="padding-left:10px;padding-top:10px;overflow:auto;" id="income_statement_container" name="income_statement_container">
	<?php if(!empty($business_user)): ?>
		<div id="statement_header" style="font-size:18;font-weight:bold;padding-left:330px;">
			<?=$business_user['company_name']?> | <?=date('m/d/y',strtotime($start_date))?> - <?=date('m/d/y',strtotime($end_date))?>
		</div>
		<hr style="width:915px;">
		<span style="font-size:16;font-weight:bold;">Revenues</span>
		<hr>
		<?php
			$i = 1;
		?>
		<?php foreach($revenue_category_infos as $revenue_category_info): ?>
			<table style="width:900px;margin-left:15px;">
				<tr style="cursor:pointer;" onclick="open_transactions(<?=$i?>)">
					<td style="font-weight:bold;"><?=$revenue_category_info["category_name"] ?></td>
					<?php if($revenue_category_info["net"] < 0): ?>
						<td style="font-weight:bold;text-align:right;">(<?=number_format(ltrim($revenue_category_info["net"],'-'),2,'.',',') ?>)</td>
					<?php elseif($revenue_category_info["net"] >= 0): ?>
						<td style="font-weight:bold;text-align:right;"><?=number_format(($revenue_category_info["net"]),2,'.',',') ?></td>
					<?php endif ?>
				</tr>
			</table>
			<div id="tr_<?=$i?>" style="margin-bottom:20px; padding-left:30px;display:none;">
				<table style="width:850px;">
					<?php if(!empty($revenue_category_info["account_entries_by_category"])):?>
						<?php foreach($revenue_category_info["account_entries_by_category"] as $account_entry): ?>
							<tr>
								<td><?=date('m/d/y',strtotime($account_entry["entry_datetime"])) ?></td>
								<td style="padding-left:15px;"><?=$account_entry["account_name"] ?></td>
								<td style="padding-left:15px;"><?=$account_entry["entry_description"] ?></td>
								<td style="text-align:right; padding-left:15px;">
									<?php if($account_entry["debit_credit"]=="Debit"): ?>
										(<?=number_format(ltrim($account_entry["entry_amount"],'-'),2,'.',',') ?>)
									<?php elseif($account_entry["debit_credit"]=="Credit"): ?>
										<?=number_format($account_entry["entry_amount"],2,'.',',') ?>
									<?php endif ?>
								</td>
							</tr>
						<?php endforeach ?>
					<?php endif ?>
				</table>
			</div>
			<?php
				$i++;
			?>
		<?php endforeach ?>
		<table style="width:900px;font-weight:bold;margin-left:15px;">
			<tr>
				<td style="width:744px;"><hr></td>
				<td style="text-align:right;"><hr></td>
			</tr>
		</table>
		<table style="width:900px;font-weight:bold;margin-left:15px;">
			<tr>
				<td>Total</td>
				<td style="text-align:right;">
					<?php if($revenue_total < 0): ?>
						(<?=number_format(ltrim($revenue_total,'-'),2,'.',',')?>)
					<?php elseif($revenue_total >= 0): ?>
						<?=number_format($revenue_total,2,'.',',')?>
					<?php endif ?>
				</td>
			</tr>
		</table><br>
		<span style="font-size:16;font-weight:bold;">Expenses</span>
		<hr>
		<?php foreach($expense_category_infos as $expense_category_info): ?>
			<table style="width:900px;margin-left:15px;">
				<tr style="cursor:pointer;" onclick="open_transactions(<?=$i?>)">
					<td style="font-weight:bold;"><?=$expense_category_info["category_name"] ?></td>
					<?php if($expense_category_info["net"] < 0): ?>
						<td style="font-weight:bold;text-align:right;"><?=number_format(ltrim($expense_category_info["net"],'-'),2,'.',',') ?></td>
					<?php elseif($expense_category_info["net"] > 0): ?>
						<td style="font-weight:bold;text-align:right;">(<?=number_format($expense_category_info["net"],2,'.',',') ?>)</td>
					<?php elseif($expense_category_info["net"] == 0): ?>
						<td style="font-weight:bold;text-align:right;"><?=number_format($expense_category_info["net"],2,'.',',') ?></td>
					<?php endif ?>
				</tr>
			</table>
			<div id="tr_<?=$i?>" style="margin-bottom:20px; padding-left:30px;display:none;">
				<table style="width:850px;">
					<?php if(!empty($expense_category_info["account_entries_by_category"])): ?>
						<?php foreach($expense_category_info["account_entries_by_category"] as $account_entry): ?>
							<tr>
								<td><?=date('m/d/y',strtotime($account_entry["entry_datetime"])) ?></td>
								<td style="padding-left:15px;"><?=$account_entry["account_name"] ?></td>
								<td style="padding-left:15px;"><?=$account_entry["entry_description"] ?></td>
								<td style="text-align:right; padding-left:15px;">
									<?php if($account_entry["debit_credit"]=="Debit"): ?>
										<?=number_format($account_entry["entry_amount"],2,'.',',') ?>
									<?php elseif($account_entry["debit_credit"]=="Credit"): ?>
										(<?=number_format($account_entry["entry_amount"],2,'.',',') ?>)
									<?php endif ?>
								</td>
							</tr>
						<?php endforeach ?>
					<?php endif ?>
				</table>
			</div>
			<?php
				$i++;
			?>
		<?php endforeach ?>
		<table style="width:900px;font-weight:bold;margin-left:15px;">
			<tr>
				<td style="width:744px;"><hr></td>
				<td style="text-align:right;"><hr></td>
			</tr>
		</table>
		<table style="width:900px;font-weight:bold;margin-left:15px;">
			<tr>
				<td>Total</td>
				<td style="text-align:right;">
					<?php if($expense_total < 0): ?>
						(<?=number_format(ltrim($expense_total,'-'),2,'.',',')?>)
					<?php elseif($expense_total >= 0): ?>
						<?=number_format($expense_total,2,'.',',')?>
					<?php endif ?>
				</td>
			</tr>
		</table>
		<table style="width:900px;font-weight:bold;margin-left:15px;">
			<tr>
				<td style="width:744px;"><hr></td>
				<td style="text-align:right;"><hr></td>
			</tr>
		</table>
		<table style="width:915px;">
			<tr>
				<td style="font-size:16;font-weight:bold;">Net Income</td>
				<td style="font-size:16px;font-weight:bold;text-align:right;">
					<?php if($revenue_total - $expense_total < 0): ?>
						(<?=number_format(ltrim($revenue_total - $expense_total,'-'),2,'.',',')?>)
					<?php elseif($revenue_total - $expense_total >= 0): ?>
						<?=number_format(($revenue_total - $expense_total),2,'.',',')?>
					<?php endif ?>
				</td>
			</tr>
		</table>
	<?php endif ?>
</div>
