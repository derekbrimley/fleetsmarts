<html>
	<style>

	</style>
	<body>
		<table style="font-family:arial;">
			<?php if(!empty($po["approved_datetime"])):?>
				<tr style="height:40px; background:green">
					<td colspan='2' style="text-align:center;font-weight:bold; font-size:24px; color:white">
						APPROVED!
					</td>
				</tr>
			<?php else:?>
				<tr style="height:40px; background:RED">
					<td colspan='2' style="text-align:center;font-weight:bold; font-size:24px; color:white">
						DENIED!
					</td>
				</tr>
			<?php endif;?>
			<tr style="background-color:#F2F2F2; height:40px;">
			
				<td colspan='2' style="text-align:center;font-weight:bold;font-color:#DD4B39">
					PURCHASE ORDER <?=$po["id"]?> DETAILS
				</td>
			</tr>
			<tr>
				<td style="width:200px;">
					Amount
				</td>
				<td style="text-align:right; font-size:16px; font-weight:bold;">
					$<?=number_format($po["expense_amount"],2)?>
				</td>
			</tr>
			<tr>
				<td style="width:200px;">
					Issuer
				</td>
				<td style="text-align:right;">
					<?=$po["issuer"]["full_name"]?>
				</td>
			</tr>
			<tr>
				<td style="width:200px;">
					Account
				</td>
				<td style="text-align:right;">
					<?=$po["account"]["account_name"]?>
				</td>
			</tr>
			<tr>
				<td style="width:200px;">
					Owner
				</td>
				<td style="text-align:right;">
					<?=$po["owner"]["company_side_bar_name"]?>
				</td>
			</tr>
			<tr>
				<td style="width:200px;">
					To be Approved by
				</td>
				<td style="text-align:right;">
					<?=$po["approved_by"]["full_name"]?>
				</td>
			</tr>
			<tr>
				<td style="width:200px;">
					Category
				</td>
				<td style="text-align:right;">
					<?=$po["category"]?>
				</td>
			</tr>
			<tr>
				<td style="width:200px;">
					Description
				</td>
				<td style="text-align:right;">
					<?=$po["po_notes"]?>
				</td>
			</tr>
		</table>
	</body>
</html>