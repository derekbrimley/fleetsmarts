<html>
	<head>
		<meta name='viewport' content='initial-scale=1.0, user-scalable=0'>
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<style>
			.jq_button
			{
				height:40px;
				width:200px;
				background-color:red;
			}
			
			.heading
			{
				color:#DD4B39;
				font-family:arial;
				font-weight:bold;
			}
			
			td
			{
				 vertical-align:middle;
			}
		</style>
	</head>
	<body style="font-family:arial;">
		<div style="margin:auto; width:345px;">
			<table>
				<tr style="background-color:#F2F2F2; height:40px;">
					<td colspan='2' style="text-align:center;font-weight:bold;font-color:#DD4B39;">
						PURCHASE ORDER <?=$po["id"]?> DETAILS
					</td>
				</tr>
				<tr>
					<td style="width:150px;">
						Amount
					</td>
					<td style="width:195px; text-align:right; font-size:16px; font-weight:bold;">
						$<?=number_format($po["expense_amount"],2)?>
					</td>
				</tr>
				<tr>
					<td style="">
						Issuer
					</td>
					<td style="text-align:right;">
						<?=$po["issuer"]["full_name"]?>
					</td>
				</tr>
				<tr>
					<td style="">
						Account
					</td>
					<td style="text-align:right;">
						<?=$po["account"]["account_name"]?>
					</td>
				</tr>
				<tr>
					<td style="">
						Owner
					</td>
					<td style="text-align:right;">
						<?=$po["owner"]["company_side_bar_name"]?>
					</td>
				</tr>
				<tr>
					<td style="">
						To be Approved by
					</td>
					<td style="text-align:right;">
						<?=$po["approved_by"]["full_name"]?>
					</td>
				</tr>
				<tr>
					<td style="">
						Category
					</td>
					<td style="text-align:right;">
						<?=$po["category"]?>
					</td>
				</tr>
				<tr>
					<td style="">
						Description
					</td>
					<td style="text-align:right;">
						<?=$po["po_notes"]?>
					</td>
				</tr>
			</table>
			<div style="margin-top:25px;">
				<a style="border-radius:2px; text-align:center; text-decoration:none; width:130px; height:60px; line-height:60px; background-color:green; color:white; font-weight:bold; float:left;" href="http://fleetsmarts.net/index.php/purchase_orders/approve_po/<?=$po["id"]?>">Approve</a>
				<a style="border-radius:2px; text-align:center; text-decoration:none; width:130px; height:60px; line-height:60px; background-color:red; color:white; font-weight:bold; float:right;" href="http://fleetsmarts.net/index.php/purchase_orders/deny_po/<?=$po["id"]?>">Deny</a>
			</div>
			<div style="clear:both;"></div>
			<div style="height:25px; line-height:25px; margin-top:25px; clear:both; text-align:center; font-weight:bold; background:#F2F2F2">
				Attachments
			</div>
			<?php if(!empty($attachments)):?>
					<?php foreach($attachments as $attachment):?>
						<div class="attachment_box" style="float:left;margin:5px;">
							<a target="_blank" style="text-decoration:none;color:#4e77c9;" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>" onclick=""><?=$attachment["attachment_name"]?></a>&nbsp;
						</div>
					<?php endforeach;?>
			<?php endif;?>
		</div>
	</body>
</html>