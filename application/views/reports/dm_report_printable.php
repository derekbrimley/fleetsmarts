<html>
	<head>
		<title>DM Report</title>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
		<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
	</head>
	<script>
		$(document).ready(function()
		{
			window.print();
		});
	</script>
	<body>
		<?php
			date_default_timezone_set('America/Denver');
			$now_datetime = date("n/d/y H:i");
		?>
		<?php include("dm_report_contents.php"); ?>
		<div id="sig_div" style="width:750px;height:50px; margin:auto; margin-top:25px;">
			<span style="float:left;">Fleet Manager ________________________________</span>
			<span style="float:right;">Driver Manager ________________________________</span>
		</div>
	</body>
</html>