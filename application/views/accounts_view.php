<style>
	hr
	{
		width:156px;
		margin:0px;
		margin-top:7px;
		margin-bottom:7px;
	}
	
	.blue_border
	{
		box-shadow: 0 0 0 3px #6295FC inset;
	}
</style>


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
		
		<?php include("accounts/accounts_script.php"); ?>

	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div" >
			<div id="space_header">
			</div>
			<div id="left_bar" style="width:175px">
				<button class='left_bar_button jq_button' id="log_entry" onclick="open_new_account_dialog()">New Account</button>
				<br>
				<br>
				<?php $attributes = array('name'=>'filter_form','id'=>'filter_form')?>
				<?=form_open('accounts/load_report',$attributes);?>
					<div style="margin-top:15px;" id="scrollable_filter_div"  class="scrollable_div">
						<span style="font-weight:bold;">Business User</span>
						<hr/>
						<?php echo form_dropdown('business_user',$business_users_options,'All','id="business_user" onChange="load_accounts()" class="left_bar_input"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Account Type</span>
						<hr/>
						<?php echo form_dropdown('account_type',$account_type_options,'All','onChange="load_accounts()" class="left_bar_input"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Account Class</span>
						<hr/>
						<?php echo form_dropdown('account_class',$account_class_options,'All','onChange="load_accounts()" class="left_bar_input"');?>
						<br>
						<br>
						<span style="font-weight:bold;">Account Category</span>
						<hr/>
						<?php echo form_dropdown('account_category',$account_category_options,'All','onChange="load_accounts()" class="left_bar_input"');?>
						<br>
						<br><span style="font-weight:bold;">Sub Accounts</span>
						<hr/>
						<?php
							$options = array(
								"Hide" => "Hide",
								"Show" => "Show",
								);
						?>
						<?php echo form_dropdown('sub_account_display',$options,'All','onChange="load_accounts()" class="left_bar_input"');?>
						<br>
						<br>
					</div>
				</form>
			</div>
			<div id="main_content" style="margin-left:33;">
				<div id="main_content_header">
					<span style="font-weight:bold;">Accounts</span>
					<div style="float:right; width:25px;">
						<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
						<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_accounts()" />
					</div>
				</div>
				<!-- REPORT GOES HERE !-->
			</div>
			
		</div>
	</body>
		
	<div id="create_new_account" title="Create New Account" style="display:none;">
		<div id="pre_new_account_div">
			<?php $attributes = array('id' => 'pre_new_account_form'); ?>
			<?=form_open('accounts/load_new_account_form',$attributes)?>
				<table style="font-size:14px; width:360px; margin:auto; margin-top:20px;">
					<tr>
						<td style="width:180px;">Business User</td>
						<td>
							<?php echo form_dropdown('business_user_id',$business_users_options,'All','id="business_user_id" onChange="load_new_account_form_div()" class="left_bar_input"');?>
						</td>
					</tr>
					<tr>
						<td>Account with whom?</td>
						<td>
							<?php $options = array(
								'Select'  	=> 'Select' ,
								'Business'  => 'Business',
								'Customer'  => 'Customer',
								'Member'  	=> 'Member',
								'Holding'  	=> 'Holding',
								'Vendor' 	=> 'Vendor',
								); ?>
							<?php echo form_dropdown('account_with',$options,"Select",'id="account_with" onChange="load_new_account_form_div()" class="left_bar_input"');?>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div id="new_account_form_div">
			<!-- AJAX GOES HERE!-->
		</div>
		<div id="success_div" style="font-size:14px; text-align:center; margin-top:25px; display:none;">
			Creating New Account!
		</div>
	</div>
	
</html>