<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $title;?></title>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
		<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>

	</head>
	<?php include("clock_in_script.php"); ?>
	<body style="font-family:arial;">
		<div id="main_window" name="main_window" style="padding:10px;">
			<div style="width:180px; text-align:center; margin:auto;">
				<img style="height:180px;" src="/images/time_keep_logo.png"/>
			</div>
			<div id="form_container">
				<div style="margin: 20px auto;text-align: center; width: 500px;">Upload a screenshot of your desktop computer. (Must include date and time).</div>
				<form style="width:500px;margin:10px auto;" id="upload_file_form" enctype="multipart/form-data" action="time_clock/verify_clock_in">
					<input type="hidden" value="<?= $clock_in_id ?>" id="clock_in_id" name="clock_in_id"/>
					<input style="padding-left: 188px;" type="file" id="attachment_name" name="attachment_name" class="" />
					<button onClick="submit_form()" style="display: block;margin: 0 auto;margin-top: 10px;width: 100px;height: 30px;background: #6295FC;border: none;color: #FFF;font-size: 12pt;cursor: pointer;" type="button">Submit</button>
				</form>
			</div>
			<div id="response_container" style="width: 500px;text-align: center;margin: 20px auto;">
			</div>
		</div>
	</body>
</html>


