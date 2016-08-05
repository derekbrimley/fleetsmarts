<script>
	$("#scrollable_content").height($("#main_content").height() - 40);
</script>

<div id="main_content_header">
	<span style="font-weight:bold;"><?=$company["company_name"]?></span>
	<img src="<?=base_url("images/edit.png")?>" style="margin-top:4px;cursor:pointer;float:right;height:20px;" id="edit_vendor" onclick="load_broker_edit('<?=$company["id"]?>')"/>
	<img src="<?=base_url("images/loading.gif")?>" style="display:none;margin-top:4px;cursor:pointer;float:right;height:20px;" id="loading_icon"/>
	<img src="<?=base_url("images/paper_clip2.png")?>" style="cursor:pointer;float:right;margin-right:10px;margin-top:2px;width:11px;" id="attachment_btn" onclick="open_file_upload(<?=$company["id"]?>,'broker')" />
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
		<div id="broker_specific" style="margin-bottom:20px; margin-top:20px;">
			<div class="section_heading" style="height:30px;">
				Broker Specific Info
			</div>
			<hr/>
			<br>
			<table id="main_content_table" style="font-size:14px;">
				<tr>
					<td style="width:300px;">MC Number</td>
					<td>
						<?=$customer['mc_number'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Broker Status</td>
					<td>
						<?=$customer['status'];?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Form of Payment</td>
					<td>
						<?=$customer['form_of_payment'];?>
					</td>
				</tr>
			</table>
		</div>
		<div id="broker_attachments" style="margin-top:15px;">
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