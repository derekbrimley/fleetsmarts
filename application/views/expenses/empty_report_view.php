<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
<title>SmartPay Report</title>
<div style="width:900px; margin:auto; margin-top:25px; font-family:arial;">
	<div style="width:310px; margin:auto; font-size:14px; font-weight:bold;">
		<?=$report_name?> | <?=$account["account_name"]?>
		<br><br><br>
		<button id="upload_transactions" name="upload_transactions" class="jq_button_disabled" style="width:200px; height:50px; margin:auto;">Upload to Database</button>
		<br><br><br>
		<span style="font-weight:normal;">
			This report is empty. <a href='<?=base_url("index.php/expenses")?>'>Click here to continue</a>
		</span>
	</div>
</div>






















