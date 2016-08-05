<html>
	<!-- 	TODO: 								!-->
	<!-- 	SEARCH FUNCTION 					!-->
	<!-- 	ACTIVE/INACTIVE FILTER BOX			!-->
	<!-- 	FLEET MANAGER FILTER BOX			!-->
	<head>
		<title><?php echo $title;?></title>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
		<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
		
		<?php include("people/people_script.php"); ?>

	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div">
			<div id="space_header">
			</div>
			
			<div id="left_bar">
				<button class='left_bar_button jq_button' id="new_client" onclick="$('#new_person_dialog').dialog('open');">New Contact</button>
				<br>
				<br>
				<div id="scrollable_left_bar" class="scrollable_div" style="width:165px;overflow-x:hidden;">
					<span class="heading">Contact Type</span>
					<hr/>
					<div id="Brokers" class="left_bar_link_div" style="with:145px;" onclick="load_upper_list('Broker')">
						Brokers
					</div>
					<?php if(user_has_permission("view and edit all business users")): ?>
						<div id="Business_Users" class="left_bar_link_div" style="with:145px;" onclick="load_upper_list('Business User')">
							Business Users
						</div>
					<?php endif ?>
					<div id="Carrier" class="left_bar_link_div" style="with:145px;" onclick="load_upper_list('Carrier')">
						Carriers
					</div>
					<div id="Customer_Vendors" class="left_bar_link_div" style="with:145px;" onclick="load_upper_list('customer-vendor')">
						Customer/Vendors
					</div>
					<div id="Main_Driver" class="left_bar_link_div" style="with:145px;" onclick="load_upper_list('Main Driver')">
						Drivers
					</div>
					<?php if(user_has_permission("view fleet managers in contacts tab")): ?>
						<div id="Fleet_Manager" class="left_bar_link_div" style="with:145px;" onclick="load_upper_list('Fleet Manager')">
							Fleet Managers
						</div>
					<?php endif ?>
					<?php if(user_has_permission("view staff in contacts tab")): ?>
						<div id="Staff" class="left_bar_link_div" style="with:145px;" onclick="load_upper_list('Staff')">
							Staff
						</div>
					<?php endif ?>
					<div id="Insurance_Agents" class="left_bar_link_div" style="with:145px;" onclick="load_upper_list('Insurance Agent')">
						Insurance Agents
					</div>
					<br>
					
					<div>
						<div id="upper_list_div" name="upper_list_div" style="margin-bottom:6px;">
							<?php
								$status_options = array(
									'All'=> 'All',
								);
							?>
							<?php echo form_dropdown('status_dropdown',$status_options,"All",'id="status_dropdown" style="display:none;" class="left_bar_input"');?>
						</div>
						<div id="people_list_div" name="people_list_div" style="width: 155px;">
							<!--THIS IS WHERE THE LIST OF PEOPLE GO --  AJAX !-->
						</div>
					</div>
				</div>
			</div>
			
			<div id="main_content" style="display:none;">
			</div>
			
		</div>
	</body>
	
	<div id="new_person_dialog" title="Add New Contact" style="display:none;">
		<?php $text_box_style = "width:161px; margin-left:2px;";?>
		<div id="saving_message" style="margin:20px; display:none;">
			Adding new contact to the database...
		</div>
		<div id="new_person_dialog_content" style="margin:20px;">
			<table id="truck_view" style="font-size: 14px;">
				<tr>
					<td style="width:180px;">Contact Type</td>
					<td>
						<?php $options = array(
						'Select'  			=> 'Select',
						'Broker'  			=> 'Broker',
						'Business'  		=> 'Business User',
						'Carrier'  			=> 'Carrier',
						'customer_vendor'  	=> 'Customer/Vendor',
						'Driver'  			=> 'Driver',
						'Staff'  			=> 'Staff',
						'Insurance Agent'  	=> 'Insurance Agent',
						); ?>
						<?php echo form_dropdown('person_type',$options,'Select','onchange="person_type_selected()" id="person_type" style="width:161px; height:21px; margin-left:2px;"');?>
					</td>
					<td style="color:red; width:10px; text-align:right;">
						*
					</td>
				</tr>
			</table>
			<div id="add_driver_div" style="display:none;">
				<?php $attributes = array('id' => 'add_driver_form', 'name'=>'add_driver_form', 'target'=>'_blank'); ?>
				<?=form_open_multipart('people/add_driver',$attributes)?>
					<table style="font-size: 14px;">
						<tr>
							<td style="width:180px;">Driver Type</td>
							<td>
								<?php $options = array(
								'Select'  	=> 'Select',
								'Main Driver'  	=> 'Main Driver',
								'Co-Driver'  	=> 'Co-Driver',
								); ?>
								<?php echo form_dropdown('driver_type',$options,'Select','id="driver_type" style="width:161px; height:21px; margin-left:2px;"');?>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td style="width:180px;">Driver Status</td>
							<td>
								<?php $options = array(
									'Select'  => 'Select',
									'Active'  => 'Active',
									'Pending Closure' => 'Pending Closure',
									'Closed' => 'Closed',
									); 
								?>
								<?php echo form_dropdown('driver_status',$options,'Select','id="driver_status" style="width:161px; height:21px; margin-left:2px;"');?>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>First Name</td>
							<td>
								<input type="text" id="first_name" name="first_name" style="<?=$text_box_style?>"/>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Last Name</td>
							<td>
								<input type="text" id="last_name" name="last_name" style="<?=$text_box_style?>"/>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Driver Side-Bar Name</td>
							<td>
								<input type="text" id="side_bar_name" name="side_bar_name" style="<?=$text_box_style?>"/>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Social Security Number</td>
							<td>
								<input type="text" id="social" name="social" placeholder="SS# no spaces or dashes" style="<?=$text_box_style?>"/>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td style="vertical-align:middle;">
								Diver Contract
							</td>
							<td style=" padding-top:5px;">
								<input type="file" id="attachment_file" name="attachment_file" style="width:163px;" />
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div id="add_carrier_div" name="add_carrier_div" style="display:none;">
				<?php $attributes = array('id' => 'add_carrier_form'); ?>
				<?=form_open('people/add_carrier',$attributes)?>
					<table style="font-size: 14px;">
						<tr>
							<td style="width:180px;">Company Status</td>
							<td>
								<?php $options = array(
									'Select'  	=> 'Select' ,
									'Pending Setup'  	=> 'Pending Setup',
									'Active'  	=> 'Active',
									'Inactive'  => 'Inactive',
									); ?>
								<?php echo form_dropdown('carrier_status_add',$options,"Select",'id="carrier_status_add" style="width:161px; height:21px; margin-left:2px;"');?>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Company Name</td>
							<td>
								<input type="text" id="company_name_add" name="company_name_add" style="<?=$text_box_style?>"/>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Company Nick Name</td>
							<td>
								<input type="text" id="company_side_bar_name_add" name="company_side_bar_name_add" style="<?=$text_box_style?>"/>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr id="broker_parent_ar_account_row" style="display:none;">
							<td>Parent Coop A/R Account</td>
							<td>
								<?php echo form_dropdown('parent_ar_account',$asset_account_options,"Select",'id="parent_ar_account" style="width:161px; height:21px; margin-left:2px;"');?>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div id="add_staff_div" name="add_staff_div" style="display:none;">
				<?php $attributes = array('id' => 'add_staff_form'); ?>
				<?=form_open('people/add_staff',$attributes)?>
					<table style="font-size: 14px;">
						<tr>
							<td style="width:180px;">First Name</td>
							<td>
								<input type="text" id="staff_first_name" name="staff_first_name" style="<?=$text_box_style?>"/>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Last Name</td>
							<td>
								<input type="text" id="staff_last_name" name="staff_last_name" style="<?=$text_box_style?>"/>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div id="add_insurance_agent_div" name="add_insurance_agent_div" style="display:none;">
				<?php $attributes = array('id' => 'add_insurance_agent_form'); ?>
				<?=form_open('people/add_insurance_agent',$attributes)?>
					<table style="font-size: 14px;">
						<tr>
							<td style="width:180px;">Agency Name</td>
							<td>
								<input type="text" id="agency_name" name="agency_name" style="<?=$text_box_style?>"/>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Contact Name</td>
							<td>
								<input type="text" id="contact_name" name="contact_name" style="<?=$text_box_style?>"/>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Contact Email</td>
							<td>
								<input type="text" id="contact_email" name="contact_email" style="<?=$text_box_style?>"/>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Contact Phone</td>
							<td>
								<input type="text" id="contact_phone" name="contact_phone" style="<?=$text_box_style?>"/>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Notes</td>
							<td>
								<textarea  id="agent_company_notes" name="agent_company_notes" style="<?=$text_box_style?>"></textarea>
							</td>
							<td style="color:red; width:10px; text-align:right;">
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	
	<div id="add_cust_vendor_dialog" title="New Customer/Vendor" style="display:none;">
		<!-- AJAX GOES HERE !-->
	</div>
	
	<div id="new_card_dialog" title="New Card" style="display:none;">
		<div style="margin:0 auto; margin-top:20px;">
			<?php $attributes = array('id' => 'add_card', 'name'=>'add_card'); ?>
			<?=form_open('people/add_card',$attributes)?>
				<input type="hidden" id="new_card_company_id" name="new_card_company_id" value="">
				<table id="" style="font-size: 14px;">
					<tr>
						<td style="width:180px;">
							Account
						</td>
						<td>
							<?php echo form_dropdown('new_card_account',$cash_accounts_options,"Select",'id="new_card_account" class="main_content_dropdown" style="" ');?>
						</td>
					</tr>
					<tr>
						<td>
							Last 4 Digits
						</td>
						<td>
							<input type="text" id="last_four" name="last_four" class="main_content_dropdown"/>
						</td>
					</tr>
					<tr>
						<td>
							Card Name
						</td>
						<td>
							<input type="text" id="card_name" name="card_name" class="main_content_dropdown"/>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<div title="Attachment Upload" id="file_upload_dialog" name="file_upload_dialog" style="display:hidden;" >
		<!-- AJAX GOES HERE !-->
	</div>
</html>