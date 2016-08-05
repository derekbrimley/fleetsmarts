<head>
	<title>Invoice_<?=str_replace(":","_",$invoice_number)?></title>
	<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
	<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
	<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
	<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
	<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
	<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
</head>
<style>
	.rounded_corners
	{
		border-radius: 25px;
	}
</style>
<div style="width:800px; font-family:arial; margin-left:20px;">
	<div style="min-height:100px; margin-top:20px;">
		<div style="float:left; margin-left:10px;">
			<?php if(!empty($business_company["logo_img_src"])):?>
				<img src="<?=$business_company["logo_img_src"]?>" style="max-height:105px; max-width:300px; margin-left:10px; margin-bottom:10px;"/>
			<?php endif;?>
			<div style="margin:10px; color:#2D2D2D; font-weight:bold; font-size:14px;">
				<?=$business_company["company_name"]?>
			</div>
		</div>
		<div style="float:right; text-align:right;">
			<div style="color:grey; margin-right:10px; margin-top:5px; font-size:30px;">INVOICE</div>
			<br>
			<span style="color:grey; margin-right:10px; font-size:16px;"><?=$invoice_number?></span>
		</div>
	</div>
	<table style="width:780px; margin-top:80px; margin-left:20px;">
		<tr>
			<td style="width:400px; line-height:35px;">
				<span style="color:grey;">Bill To:</span>
			</td>
			<td style="width:400px; text-align:right; padding-right:20px;">
				<span style="color:grey;">Date:</span> <span style="color:#2D2D2D;"><?=date('M j, Y',strtotime($invoice_date))?></span>
			</td>
		</tr>
		<tr style="">
			<td style="vertical-align:middle;">
				<span style="color:#2D2D2D; font-weight:bold; font-size:14px;"><?=$related_company["company_name"]?></span>
			</td>
			<td style="width:400px; text-align:right;">
				<div style="float:right; text-align:center; line-height:35px; height:35px; font-weight:bold; width:350px; background-color:rgba(128, 128, 128, 0.18); border-radius: 5px;">
					Balance Due: $<?=number_format($invoice_amount,2)?>
				</div>
			</td>
		</tr>
	</table>
	<div style="background-color:#2D2D2D; border-radius:5px; height:25px; margin-top:80px;">
		<table>
			<tr style="color:white; line-height:25px;">
				<td style="width:700px; padding-left:20px;">
					Item
				</td>
				<td style="width:100px; text-align:right; padding-right:20px;">
					Amount
				</td>
			</tr>
		</table>
	</div>
	<div style="margin-top:10px;">
		<table>
			<tr style="color:#2D2D2D; line-height:25px;">
				<td style="width:700px; padding-left:20px;">
					<?=$invoice_desc?>
				</td>
				<td style="width:100px; text-align:right; padding-right:20px;">
					$<?=number_format($invoice_amount,2)?>
				</td>
			</tr>
			<tr style="color:#2D2D2D; line-height:25px;">
				<td style="width:700px; padding-top:200px;  padding-left:20px; text-align:right;">
					<span style="color:grey;">Subtotal:</span> $<?=number_format($invoice_amount,2)?>
				</td>
				<td style="width:100px;">
					
				</td>
			</tr>
			<tr style="color:#2D2D2D; line-height:25px;">
				<td style="padding-left:20px; text-align:right;">
					<span style="color:grey;">Total:</span> $<?=number_format($invoice_amount,2)?>
				</td>
				<td style="">
					
				</td>
			</tr>
		</table>
	</div>
</div>
<script>
	$(document).ready(function()
	{
		//alert('hi');
		window.print();
	});
</script>