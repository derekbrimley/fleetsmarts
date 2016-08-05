<script>
	$("#scrollable_content").height($("#main_content").height() - 40);
</script>

<div id="main_content_header">
	<span style="font-weight:bold;"><?=$this_client["client_nickname"] ?></span>
	<img src="<?=base_url("images/edit.png")?>" style="cursor:pointer;float:right;margin-top:4px;height:20px; margin-left:10px;" id="edit_client" onclick="load_driver_edit('<?=$this_client["id"]?>')" />
	<img src="<?=base_url("images/paper_clip2.png")?>" style="cursor:pointer;float:right;margin-right:10px;margin-top:2px;width:11px;" id="attachment_btn" onclick="open_file_upload(<?=$this_client["id"]?>,'driver')" />
	<img src="<?=base_url("images/loading.gif")?>" style="display:none;float:right;margin-top:4px;height:20px;" id="loading_img"/>
</div>
<div id="scrollable_content" class="scrollable_div">
	<div id="personal_info" style="margin:20px;">
		<span class="section_heading">Personal Info</span>
		<hr/>
		<br>
		<table id="main_content_table" style="margin-top:6px;">
			<tr>
				<td style="width:300px;">Driver Side-Bar Name</td>
				<td>
					<?=$this_client["client_nickname"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Driver Type</td>
				<td>
					<?=$this_client["client_type"];?>
				</td>
			</tr>
			<tr>
				<td style="width:130px;">Pay Structure</td>
				<td>
					<?=$this_client["pay_structure"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Profit Split</td>
				<td>
					<?=$this_client["profit_split"];?>%
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Driver Status</td>
				<td>
					<?=$this_client["client_status"];?>
				</td>
			</tr>								
			<tr>
				<td style="width:300px;">Dropdown Status</td>
				<td>
					<?=$this_client["dropdown_status"];?>
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
				<td>Name on Fuel Card</td>
				<td>
					<?= $this_client["fuel_card_name"];?>
				</td>
			</tr>
			<tr>
				<td>Fuel Card Number</td>
				<td>
					<?= $this_client["fuel_card_number"];?>
				</td>
			</tr>
			<tr>
				<td>Ultimate Platinum Card #</td>
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
				<td>Bigroad Username</td>
				<td>
					<?= $this_client["bigroad_username"];?>
				</td>
			</tr>
			<tr>
				<td>Bigroad Password</td>
				<td>
					<?= $this_client["bigroad_password"];?>
				</td>
			</tr>
			<tr>
				<td>Home Phone Number</td>
				<td>
					<?= $this_client["company"]["person"]["home_phone"];?>
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
				<td>Home City</td>
				<td>
					<?= $this_client["company"]["person"]["home_city"];?>
				</td>
			</tr>
			<tr>
				<td>Home State</td>
				<td>
					<?= $this_client["company"]["person"]["home_state"];?>
				</td>
			</tr>
			<tr>
				<td>Home Zip Code</td>
				<td>
					<?= $this_client["company"]["person"]["home_zip"];?>
				</td>
			</tr>
			<tr>
				<td>Date of Birth</td>
				<td>
					<?php if(!empty($this_client["company"]["person"]["date_of_birth"])):?>
						<?= date("n/j/Y",strtotime($this_client["company"]["person"]["date_of_birth"]));?>
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
					<?php if(!empty($this_client["license_expiration"])):?>
						<?= date("n/j/Y",strtotime($this_client["license_expiration"]));?>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>CDL Since</td>
				<td>
					<?php if(!empty($this_client["cdl_since"])):?>
						<?= date("n/j/Y",strtotime($this_client["cdl_since"]));?>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Years of Experience</td>
				<td>
					<?= $this_client["years_of_experience"];?>
				</td>
			</tr>
			<tr>
				<td>Desired Company Name</td>
				<td>
					<?= $this_client["desired_company_name"];?>
				</td>
			</tr>
			<tr>
				<td>SSN</td>
				<td>
					<?= $this_client["company"]["person"]["ssn"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Start Date</td>
				<td>
					<?php if(!empty($this_client["start_date"])):?>
						<?=date("n/j/Y",strtotime($this_client["start_date"]));?>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">End Date</td>
				<td>
					<?php if(!empty($this_client["end_date"])):?>
						<?=date("n/j/Y",strtotime($this_client["end_date"]));?>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">First Full Settlement Date</td>
				<td>
					<?php if(!empty($this_client["first_full_settlement_date"])):?>
						<?=date("n/j/Y",strtotime($this_client["first_full_settlement_date"]));?>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Owner of Carrier</td>
				<td>
					<?php if(!empty($this_client["carrier"])):?>
						<?= $this_client["carrier"]["company_side_bar_name"];?>
					<?php else:?>
						None
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td>Credit Score</td>
				<td>
					<?= $this_client["credit_score"];?>
				</td>
			</tr>
			<tr>
				<td>Link to Credit Score</td>
				<td>
					<?php if(!empty($this_client["credit_score_guid"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["credit_score_guid"]?>" onclick="">Credit Score</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Link to License</td>
				<td>
					<?php if(!empty($this_client["link_license"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["link_license"]?>" onclick="">License</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Link to MVR</td>
				<td>
					<?php if(!empty($this_client["mvr_guid"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["mvr_guid"]?>" onclick="">MVR</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Number of Violations</td>
				<td>
					<?= $this_client["number_of_violations"];?>
				</td>
			</tr>
			<tr>
				<td>Link to Social Security Card</td>
				<td>
					<?php if(!empty($this_client["company"]["person"]["link_ss_card"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["company"]["person"]["link_ss_card"]?>" onclick="">Social Security Card</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Link to Medical Card</td>
				<td>
					<?php if(!empty($this_client["medical_card_link"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["medical_card_link"]?>" onclick="">Medical Card</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Link to Driver Application</td>
				<td>
					<?php if(!empty($this_client["driver_application_link"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["driver_application_link"]?>" onclick="">Driver Application</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Link to Drug Test</td>
				<td>
					<?php if(!empty($this_client["drug_test_link"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["drug_test_link"]?>" onclick="">Drug Test</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Service Contract</td>
				<td>
					<?php if(!empty($this_client["contract_guid"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["contract_guid"]?>" onclick="">Service Contract</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>E Signatures</td>
				<td>
					<?php if(!empty($this_client["company"]["person"]["signature_guid"])):?>
						<a href="<?=base_url("/index.php/documents/download_file")."/".$this_client["company"]["person"]["signature_guid"]?>" onclick="">Signature</a>
					<?php endif;?>
					<?php if(!empty($this_client["company"]["person"]["signature_guid"]) && !empty($this_client["company"]["person"]["initials_guid"])):?>
						| 
					<?php endif;?>
					<?php if(!empty($this_client["company"]["person"]["initials_guid"])):?>
						<a href="<?=base_url("/index.php/documents/download_file")."/".$this_client["company"]["person"]["initials_guid"]?>" onclick="">Initials</a>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td>Client Notes</td>
				<td>
					<?= $this_client["company"]["person"]["person_notes"];?>
				</td>
			</tr>
			<tr>
				<td>Emergency Contact Name</td>
				<td>
					<?= $this_client["company"]["person"]["emergency_contact_name"];?>
				</td>
			</tr>
			<tr>
				<td>Emergency Contact Number</td>
				<td>
					<?= $this_client["company"]["person"]["emergency_contact_phone"];?>
				</td>
			</tr>
		</table>
	</div>
	
	<div id="fm_pay_structure"  style="margin:20px; display:none;">
		<span class="section_heading">Fleet Manager Pay Structure</span>
		<hr/>
		<br>
		<div id="revenue_based_settings" name="revenue_based_settings" style="">
			<table id="main_content_table">
				<tr style="font-weight:bold;">
					<td style="width:130px;">
						Owner
					</td>
					<td style='width:180px;'>
						Description
					</td>
					<td style='width:60px; text-align:right;'>
						Percent
					</td>
					<td style='width:110px; padding-left:30px;'>
						Pay Account
					</td>
				</tr>
				<?php if(!empty($revenue_splits)): ?>
					<?php foreach($revenue_splits as $revenue_split): ?>
						<tr>
							<td style="">
								<?=$revenue_split["owner"]["company_side_bar_name"] ?>
							</td>
							<td style=''>
								<span><?=$revenue_split["description"]?></span>
							</td>
							<td style='text-align:right;'>
								<span><?=number_format($revenue_split["percent"]*100,2)?></span> %
							</td>
							<td style='padding-left:30px;'>
								<span><?=$revenue_split["account"]["account_name"]?></span>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</table>
		</div>
	</div>
	
	<div id="client_fee_settings"  style="margin:20px; display:none;">
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
	
	<div id="user"  style="margin:20px;">
		<span class="section_heading">User Info</span>
		<hr/>
		<br>
		<table>
			<tr>
				<td style="width:300px;">Username</td>
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
		</table>
	</div>
	<div id="driver_attachments" style="margin:20px;">
		<span class="section_heading">Attachments</span>
		<hr>
		<br>
		<?php if(!empty($attachments)): ?>
			<?php foreach($attachments as $attachment): ?>
				<div class="attachment_box" style="float:left;margin:5px;">
					<a title="Download <?=$attachment['attachment_name']?>" style="text-decoration:none;color:#4e77c9;" target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>"><?=$attachment['attachment_name']?></a>
				</div>
			<?php endforeach ?>
		<?php endif ?>
	</div>
</div>