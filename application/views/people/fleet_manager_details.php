<script>
	$("#scrollable_content").height($("#main_content").height() - 40);
</script>

<div id="main_content_header">
	<span style="font-weight:bold;" title="person_id: <?=$company["person_id"]?> company_id: <?=$company["id"]?> user_id: <?=$user["id"]?>"><?=$company["company_name"]?></span>
	<img src="<?=base_url("images/edit.png")?>" style="cursor:pointer;float:right;margin-top:4px;height:20px; margin-left:10px;" id="edit_fleet_manager" onclick="load_fleet_manager_edit('<?=$company["id"]?>')" />
	<img src="<?=base_url("images/paper_clip2.png")?>" style="cursor:pointer;float:right;margin-right:10px;margin-top:2px;width:11px;" id="attachment_btn" onclick="open_file_upload(<?=$company["id"]?>,'fleet_manager')" />
	<img src="<?=base_url("images/loading.gif")?>" style="display:none;float:right;margin-top:4px;height:20px;" id="loading_img"/>
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
				<?php if(user_has_permission('View personal staff info')):?>
						<tr>
							<td>Link to Social/License</td>
							<td>
								<?php if(!empty($company["person"]["link_ss_card"])): ?>
									<a href="<?= $company["person"]["link_ss_card"];?>" target="_blank">Social Security Card</a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endif; ?>
			</table>
		</div>
		<div id="user_info_edit" style="margin-bottom:20px; margin-top:20px;">
			<span class="section_heading">User Info</span>
			<hr/>
			<br>
			<table id="main_content_table">
				<tr>
					<td style="width:300px;">Username</td>
					<td>
						XXXXXXXXX
					</td>
				</tr>
				<tr>
					<td>Password</td>
					<td>
						XXXXXXXXX
					</td>
				</tr>
			</table>
		</div>
		<div id="corpoarte_card_div" style="margin-bottom:20px; margin-top:20px;">
			<div class="section_heading" style="height:30px;">
				Corporate Cards
				<button class="jq_button" style="float:right; width:80px; margin-left:20px;" id="save_staff" onclick="open_new_card_dialog('<?=$company["id"]?>')">Add</button>
			</div>
			<hr/>
			<br>
			<table id="main_content_table">
				<?php if(!empty($cards)):?>	
					<?php foreach($cards as $card):?>
						<tr>
							<td style="width:200px;">
								<?=$card["account"]["account_name"]?>
							</td>
							<td style="width:200px;">
								<?=$card["card_name"]?>
							</td>
							<td style="width:200px;">
								xxx<?=$card["last_four"]?>
							</td>
						</tr>
					<?php endforeach;?>
				<?php endif;?>
			</table>
		</div>
		<div id="fleet_manager_attachments" style="margin-top:15px;">
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