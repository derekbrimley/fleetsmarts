<html>
	<head>
		<title><?php echo $title;?></title>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
		<link href='http://fonts.googleapis.com/css?family=Homemade+Apple' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
		
		<?php include("settings/settings_script.php"); ?>

	</head>
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" style="width:175px">
				<button class='left_bar_button jq_button' id="log_entry" onclick="open_new_ticket_dialog()">New Setting</button>
				<br>
				<?php $attributes = array('name'=>'filter_form','id'=>'filter_form','onkeypress'=>'return event.keyCode != 13;')?>
				<?=form_open('settings/load_report',$attributes);?>
					<div style="margin-top:15px;" id="scrollable_filter_div"  class="scrollable_div">
						<span style="font-weight:bold;">Filters</span>
						<hr>
						<br><br>
						<span style="font-weight:bold;">User</span>
						<hr>
						<?php echo form_dropdown('user_input',$user_options,'Select','onChange="load_user_report()" id="user_input" class="left_bar_input"');?>
						<br><br>
						<span style="font-weight:bold;">Permission</span>
						<hr>
						<?php echo form_dropdown('permission_input',$permission_options,'Select','onChange="load_permission_report()" id="permission_input" class="left_bar_input"');?>
						<br><br>
					</div>
				</form>
			</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header">
					<span style="font-weight:bold;">Settings</span>
					<div style="float:right; width:25px;">
						<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
						<img id="refresh_settings" name="refresh_settings" src="/images/refresh.png" title="Refresh Settings" style="cursor:pointer; float:right; height:20px; padding-top:5px;"  />
					</div>
				</div>
				<!-- REPORT GOES HERE !-->
			</div>
			
		</div>
	</body>