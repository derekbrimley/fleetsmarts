<script>
	$("#scrollable_content").height($("#body").height() - 155);
</script>

<div id="main_content_header">
	<span style="font-weight:bold;"><?=$this_client["company"]["company_name"] ?></span>
	<button class='jq_button' style="float:right;  width:80px;" id="edit_client">Edit</button>
</div>
<div id="scrollable_content" class="scrollable_div">
	<div id="personal_info" style="margin:20px;">
		<span class="section_heading">Personal Info</span>
		<hr/>
		<br>
		<table id="main_content_table" style="margin-top:6px;">
			<tr>
				<td style="width:300px;">Client Short Name</td>
				<td>
					<?=$this_client["client_nickname"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Client Status</td>
				<td>
					<?=$this_client["client_status"];?>
				</td>
			</tr>								
			<tr>
				<td style="width:300px;">Fleet Manager</td>
				<td>
					<?=$this_client["fleet_manager"]["full_name"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">First Name</td>
				<td>
					<?=$this_client["company"]["person"]["f_name"];?>
				</td>
			</tr>
			<tr>
				<td>Last Name</td>
				<td>
					<?= $this_client["company"]["person"]["l_name"];?>
				</td>
			</tr>
			<tr>
				<td>Username</td>
				<td>
					<?= $this_client["user"]["username"];?>
				</td>
			</tr>
			<tr>
				<td>Password</td>
				<td>
					<?= $this_client["user"]["password"];?>
				</td>
			</tr>
			<tr>
				<td>Fuel Card #</td>
				<td>
					<?= $this_client["fuel_card_number"];?>
				</td>
			</tr>
			<tr>
				<td>Pay Card #</td>
				<td>
					<?= $this_client["pay_card_number"];?>
				</td>
			</tr>
			<tr>
				<td>Personal Phone Number</td>
				<td>
					<?= $this_client["company"]["person"]["phone_number"];?>
				</td>
			</tr>
			<tr>
				<td>Phone Carrier</td>
				<td>
					<?= $this_client["company"]["person"]["phone_carrier"];?>
				</td>
			</tr>
			<tr>
				<td>Email</td>
				<td>
					<?= $this_client["company"]["person"]["email"];?>
				</td>
			</tr>
			<tr>
				<td>Home Address</td>
				<td>
					<?= $this_client["company"]["person"]["home_address"];?>
				</td>
			</tr>
			<tr>
				<td>Date of Birth</td>
				<td>
					<?= date("n/j/Y",strtotime($this_client["company"]["person"]["date_of_birth"]));?>
				</td>
			</tr>
			<tr>
				<td>Link to License</td>
				<td>
					<?php if(!empty($this_client["company"]["person"]["link_license"])): ?>
						<a href="<?= $this_client["company"]["person"]["link_license"];?>" target="_blank">License</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>License Number</td>
				<td>
					<?= $this_client["license_number"];?>
				</td>
			</tr>
			<tr>
				<td>License State</td>
				<td>
					<?= $this_client["license_state"];?>
				</td>
			</tr>
			<tr>
				<td>License Expiration</td>
				<td>
					<?= date("n/j/Y",strtotime($this_client["license_expiration"]));?>
				</td>
			</tr>
			<tr>
				<td>CDL Since</td>
				<td>
					<?= date("n/j/Y",strtotime($this_client["cdl_since"]));?>
				</td>
			</tr>
			<tr>
				<td>Link to Social Security Card</td>
				<td>
					<?php if(!empty($this_client["company"]["person"]["link_ss_card"])): ?>
						<a href="<?= $this_client["company"]["person"]["link_ss_card"];?>" target="_blank">View Social Security Card</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>SSN</td>
				<td>
					<?= $this_client["company"]["person"]["ssn"];?>
				</td>
			</tr>
			<tr>
				<td>Link to Service Contract</td>
				<td>
					<?php if(!empty($this_client["link_contract"])): ?>
						<a href="<?= $this_client["link_contract"];?>" target="_blank">Service Contract</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Start Date</td>
				<td>
					<?=date("n/j/Y",strtotime($this_client["start_date"]));?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">End Date</td>
				<td>
					<?=date("n/j/Y",strtotime($this_client["end_date"]));?>
				</td>
			</tr>
			<tr>
				<td>Personal Notes</td>
				<td>
					<?= $this_client["company"]["person"]["person_notes"];?>
				</td>
			</tr>
		</table>
	</div>
	<div id="company_info"  style="margin:20px;">
		<span class="section_heading">Company Info</span>
		<hr/>
		<br>
		<table id="main_content_table">
			<tr>
				<td style="width:300px;">Company Status</td>
				<td>
					<?=$this_client["company"]["company_status"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Company Name</td>
				<td>
					<?=$this_client["company"]["company_name"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Company Side-bar Name</td>
				<td>
					<?=$this_client["company"]["company_side_bar_name"];?>
				</td>
			</tr>
			<tr>
				<td>Link to FEIN</td>
				<td>
					<?php if(!empty($this_client["company"]["link_ein_letter"])): ?>
						<a href="<?= $this_client["company"]["link_ein_letter"];?>" target="_blank">FEIN Letter</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">FEIN</td>
				<td>
					<?=$this_client["company"]["fein"];?>
				</td>
			</tr>
			<tr>
				<td>Link to MC Letter</td>
				<td>
					<?php if(!empty($this_client["company"]["link_mc_letter"])): ?>
						<a href="<?= $this_client["company"]["link_mc_letter"];?>" target="_blank">MC Letter</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">MC Number</td>
				<td>
					<?=$this_client["mc_number"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">DOT Number</td>
				<td>
					<?=$this_client["dot_number"];?>
				</td>
			</tr>
			<tr>
				<td>Link to Docket PIN Letter</td>
				<td>
					<?php if(!empty($this_client["company"]["link_docket_pin_letter"])): ?>
						<a href="<?= $this_client["company"]["link_docket_pin_letter"];?>" target="_blank">Docket PIN Letter</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Docket PIN</td>
				<td>
					<?=$this_client["company"]["docket_pin"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">USDOT PIN</td>
				<td>
					<?=$this_client["company"]["usdot_pin"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Access ID (UT)</td>
				<td>
					<?=$this_client["company"]["access_id"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Entity Number</td>
				<td>
					<?=$this_client["company"]["entity_number"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Factor Loads Login</td>
				<td>
					<?=$this_client["company"]["fl_username"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Factor Loads Password</td>
				<td>
					<?=$this_client["company"]["fl_password"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Insurance Company</td>
				<td>
					<?=$this_client["insurance_company"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Policy Number</td>
				<td>
					<?=$this_client["policy_number"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Company Phone</td>
				<td>
					<?=$this_client["company"]["company_phone"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Company K7 Fax</td>
				<td>
					<?=$this_client["company"]["company_fax"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Company Gmail</td>
				<td>
					<?=$this_client["company_gmail"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Gmail Password</td>
				<td>
					<?=$this_client["gmail_password"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Company Address</td>
				<td>
					<?=$this_client["company"]["address"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">City</td>
				<td>
					<?=$this_client["company"]["city"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">State</td>
				<td>
					<?=$this_client["company"]["state"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Zip Code</td>
				<td>
					<?=$this_client["company"]["zip"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Company Mailing Address</td>
				<td>
					<?=$this_client["company"]["mailing_address"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Oregon Permit</td>
				<td>
					<?=$this_client["oregon_permit"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">UCR Renewal Date</td>
				<td>
					<?=date("n/j/Y",strtotime($this_client["ucr_renewal_date"]));?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Running Since</td>
				<td>
					<?=date("n/j/Y",strtotime($this_client["running_since"]));?>
				</td>
			</tr>
			<tr>
				<td>Link to Articles of Organization</td>
				<td>
					<?php if(!empty($this_client["company"]["link_aoo"])): ?>
						<a href="<?= $this_client["company"]["link_aoo"];?>" target="_blank">Articles of Organization</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Company Notes</td>
				<td>
					<?=$this_client["company"]["company_notes"];?>
				</td>
			</tr>
		</table>
	</div>
	<div id="client_fee_settings"  style="margin:20px;">
		<span class="section_heading">Client Fee Settings</span>
		<hr/>
		<br>
		<table id="main_content_table">
			<tr style="font-weight:bold;">
				<td>
					Name
				</td>
				<td>
					Amount
				</td>
				<td>
				</td>
				<td>
					Type
				</td>
				<td>
					% Tax
				</td>
				<td>
					Expense Account
				</td>
			</tr>
			<?php foreach($this_client["client_fee_settings"] as $setting): ?>
				<tr>
					<td style="width:300px;">
						<?=$setting["fee_description"] ?>
					</td>
					<td style="width:80px; margin-right:27px;">
						<span><?=$setting["fee_amount"]?></span>
					</td>
					<td style="width:40px;">
						Per
					</td>
					<td style='width:160px;'>
						<span><?=$setting["fee_type"]?></span>
					</td>
					<td style='width:90px;'>
						<span><?=$setting["fee_tax"]?></span> %
					</td>
					<td style='width:90px;'>
						<span><?=$setting["account"]["account_name"]?></span>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>