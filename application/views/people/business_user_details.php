<script>
	$("#scrollable_content").height($("#main_content").height() - 40);
</script>

<div id="main_content_header">
	<span style="font-weight:bold;"><?=$company["company_name"]?></span>
	<img src="<?=base_url("images/edit.png")?>" style="margin-top:4px;cursor:pointer;float:right;height:20px;" id="edit_vendor" onclick="load_business_user_edit('<?=$company["id"]?>')"/>
	<img src="<?=base_url("images/loading.gif")?>" style="display:none;margin-top:4px;cursor:pointer;float:right;height:20px;" id="loading_icon"/>
	<img src="<?=base_url("images/paper_clip2.png")?>" style="cursor:pointer;float:right;margin-right:10px;margin-top:2px;width:11px;" id="attachment_btn" onclick="open_file_upload(<?=$company["id"]?>,'business_user')" />
</div>

<div id="scrollable_content"  class="scrollable_div">
	<div style="margin:20px;">
		<div>
			<table id="main_content_table" style="font-size:14px;">
				<tr>
					<td style="width:300px;">Company Name</td>
					<td>
						<?=$company['company_name'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Short Name</td>
					<td>
						<?=$company['company_side_bar_name'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Status</td>
					<td>
						<?=$company['company_status'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Address</td>
					<td>
						<?=$company['address'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">City</td>
					<td>
						<?=$company['city'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">State</td>
					<td>
						<?=$company['state'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Zip Code</td>
					<td>
						<?=$company['zip'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Contact</td>
					<td>
						<?=$company['contact'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Email</td>
					<td>
						<?=$company['company_email'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Phone Number</td>
					<td>
						<?=$company['company_phone'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Fax Number</td>
					<td>
						<?=$company['company_fax'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Notes</td>
					<td>
						<?=$company['company_notes'];?>
					</td>
				</tr>
			</table>
		</div>
		<div id="user_info_edit" style="margin-bottom:20px; margin-top:20px;">
			<div class="section_heading" style="height:30px;">
				Relationships
				<button class="jq_button" style="float:right; width:80px; margin-left:20px;" id="save_staff" onclick="open_new_cust_vendor_dialog('<?=$company["id"]?>')">Add</button>
			</div>
			<hr/>
			<br>
			<div style="font-size:16px; font-weight:bold;">Customers</div>
			<br>
			<table id="main_content_table">
				<?php if(!empty($related_customers)):?>	
					<?php foreach($related_customers as $related_cust):?>
						<tr>
							<td style="width:300px;"><?=$related_cust["related_business"]["company_name"]?></td>
						</tr>
					<?php endforeach;?>
				<?php endif;?>
			</table>
			<br>
			<div style="font-size:16px; font-weight:bold;">Vendors</div>
			<br>
			<table id="main_content_table">
				<?php if(!empty($related_vendors)):?>
					<?php foreach($related_vendors as $related_vendor):?>
						<tr>
							<td style="width:300px;">
								<?=$related_vendor["related_business"]["company_name"]?>
							</td>
						</tr>
					<?php endforeach;?>
				<?php endif;?>
			</table>
			<br>
			<div style="font-size:16px; font-weight:bold;">Staff</div>
			<br>
			<table id="main_content_table">
				<?php if(!empty($related_staff)):?>
					<?php foreach($related_staff as $staff):?>
						<tr>
							<td style="width:300px;">
								<?=$staff["related_business"]["company_name"]?>
							</td>
						</tr>
					<?php endforeach;?>
				<?php endif;?>
			</table>
			<br>
			<?php if($company["category"] == "Coop"):?>
				<div style="font-size:16px; font-weight:bold;">Members</div>
				<br>
				<table id="main_content_table">
					<?php if(!empty($related_members)):?>
						<?php foreach($related_members as $related_member):?>
							<tr>
								<td style="width:300px;">
									<?=$related_member["related_business"]["company_name"]?>
								</td>
							</tr>
						<?php endforeach;?>
					<?php endif;?>
				</table>
			<?php endif;?>
		</div>
		<div id="truck_attachments" style="margin-top:15px;">
			<span class="section_heading">Attachments</span>
			<hr>
			<br>
			<?php if(!empty($attachments)): ?>
				<?php foreach($attachments as $attachment): ?>
					<div class="attachment_box" style="float:left;margin:5px;margin-bottom:30px;">
						<a title="Download <?=$attachment['attachment_name']?>" style="text-decoration:none;color:#4e77c9;" target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>"><?=$attachment['attachment_name']?></a>
					</div>
				<?php endforeach ?>
			<?php endif ?>
		</div>
	</div>
</div>