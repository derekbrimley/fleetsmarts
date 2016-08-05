<script>
	$("#scrollable_content").height($("#main_content").height() - 40);
</script>

<div id="main_content_header">
	<span style="font-weight:bold;"><?=$carrier["company_name"] ?></span>
	<img src="<?=base_url("images/edit.png")?>" style="cursor:pointer;float:right;margin-top:4px;height:20px; margin-left:10px;" id="edit_truck" onclick="load_carrier_edit('<?=$carrier["id"]?>')" />
	<img src="<?=base_url("images/paper_clip2.png")?>" style="cursor:pointer;float:right;margin-right:10px;margin-top:2px;width:11px;" id="attachment_btn" onclick="open_file_upload(<?=$carrier["id"]?>,'carrier')" />
	<img src="<?=base_url("images/loading.gif")?>" style="display:none;float:right;margin-top:4px;height:20px;" id="loading_img"/>
</div>
<div id="scrollable_content" class="scrollable_div">
	<div id="company_info"  style="margin:20px;">
		<table id="main_content_table">
			<tr>
				<td style="width:300px;">Company Name</td>
				<td>
					<?=$carrier["company_name"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Company Side-bar Name</td>
				<td>
					<?=$carrier["company_side_bar_name"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Company Status</td>
				<td>
					<?=$carrier["company_status"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Company DBA</td>
				<td>
					<?=$carrier["dba"];?>
				</td>
			</tr>
			<tr>
				<td>Carrier Packet</td>
				<td>
					<?php if(!empty($carrier["carrier_packet_guid"])): ?>
						<a href="<?=base_url("/index.php/documents/download_file")."/".$carrier["carrier_packet_guid"]?>" onclick="">Carrier Packet</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Insurance Cert</td>
				<td>
					<?php if(!empty($carrier["insurance_cert_guid"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["insurance_cert_guid"]?>" onclick="">Insurance Cert</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Oregon Permit</td>
				<td>
					<?php if(!empty($carrier["oregon_permit_guid"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["oregon_permit_guid"]?>" onclick="">Oregon Permit</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">FEIN</td>
				<td>
					<?=$carrier["fein"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">MC Number</td>
				<td>
					<?=$carrier["mc_number"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">DOT Number</td>
				<td>
					<?=$carrier["dot_number"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Docket PIN</td>
				<td>
					<?=$carrier["docket_pin"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">USDOT PIN</td>
				<td>
					<?=$carrier["usdot_pin"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Access ID (UT)</td>
				<td>
					<?=$carrier["access_id"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Entity Number</td>
				<td>
					<?=$carrier["entity_number"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Factor Loads Login</td>
				<td>
					<?=$carrier["fl_username"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Factor Loads Password</td>
				<td>
					<?=$carrier["fl_password"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Insurance Company</td>
				<td>
					<?=$carrier["insurance_company"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Policy Number</td>
				<td>
					<?=$carrier["policy_number"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Carrier Phone</td>
				<td>
					<?=$carrier["company_phone"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Carrier Fax</td>
				<td>
					<?=$carrier["company_fax"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Carrier Gmail</td>
				<td>
					<?=$carrier["company_gmail"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Gmail Password</td>
				<td>
					<?=$carrier["gmail_password"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Carrier Address</td>
				<td>
					<?=$carrier["address"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">City</td>
				<td>
					<?=$carrier["city"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">State</td>
				<td>
					<?=$carrier["state"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Zip Code</td>
				<td>
					<?=$carrier["zip"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Carrier Mailing Address</td>
				<td>
					<?=$carrier["mailing_address"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Carrier Mailing City</td>
				<td>
					<?=$carrier["mailing_city"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Carrier Mailing State</td>
				<td>
					<?=$carrier["mailing_state"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Carrier Mailing Zip Code</td>
				<td>
					<?=$carrier["mailing_zip"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Oregon Permit</td>
				<td>
					<?=$carrier["oregon_permit"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">UCR Renewal Date</td>
				<td>
					<?php if(!empty($carrier["ucr_renewal_date"])):?>
						<?=date("n/j/Y",strtotime($carrier["ucr_renewal_date"]));?>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Running Since</td>
				<td>
					<?php if(!empty($carrier["running_since"])):?>
						<?=date("n/j/Y",strtotime($carrier["running_since"]));?>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td>Articles of Organization</td>
				<td>
					<?php if(!empty($carrier["link_aoo"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["link_aoo"]?>" onclick="">Articles of Organization</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Buy-Sell Chain</td>
				<td>
					<?php if(!empty($carrier["buy_sell_chain_guid"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["buy_sell_chain_guid"]?>" onclick="">Buy-Sell Chain</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Docket PIN Letter</td>
				<td>
					<?php if(!empty($carrier["link_docket_pin_letter"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["link_docket_pin_letter"]?>" onclick="">Docket PIN Letter</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>FEIN Letter</td>
				<td>
					<?php if(!empty($carrier["link_ein_letter"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["link_ein_letter"]?>" onclick="">FEIN Letter</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>MC Authority Letter</td>
				<td>
					<?php if(!empty($carrier["link_mc_letter"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["link_mc_letter"]?>" onclick="">MC Authority Letter</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>MCS-150</td>
				<td>
					<?php if(!empty($carrier["mcs_150_guid"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["mcs_150_guid"]?>" onclick="">MCS-150</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>OP-1</td>
				<td>
					<?php if(!empty($carrier["op_1_guid"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["op_1_guid"]?>" onclick="">OP-1</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>OSBR Info</td>
				<td>
					<?php if(!empty($carrier["link_osbr"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["link_osbr"]?>" onclick="">OSBR Info</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>Proof of PPB</td>
				<td>
					<?php if(!empty($carrier["proof_of_ppb_guid"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["proof_of_ppb_guid"]?>" onclick="">Proof of PPB</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>UCR</td>
				<td>
					<?php if(!empty($carrier["ucr_guid"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["ucr_guid"]?>" onclick="">UCR</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>USDOT PIN Letter</td>
				<td>
					<?php if(!empty($carrier["link_usdot_pin_letter"])): ?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$carrier["link_usdot_pin_letter"]?>" onclick="">USDOT PIN Letter</a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Carrier Notes</td>
				<td>
					<?=$carrier["company_notes"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Carrier Owners</td>
				<td>
					<?php 
						$owners_string = "";
						foreach($owners as $owner)
						{
							$owners_string = $owners_string.$owner["company"]["person"]["full_name"].", ";
						}
						$owners_string = substr($owners_string,0,-2);
					?>
					<?=$owners_string?>
				</td>
			</tr>
		</table>
		<div id="carrier_attachments" style="margin-top:15px;">
			<span class="section_heading">Attachments</span>
			<hr>
			<br>
			<?php if(!empty($attachments)): ?>
				<?php foreach($attachments as $attachment): ?>
					<div class="attachment_box" style="float:left;margin:5px;margin-bottom:30px;">
						<a title="<?=$attachment['attachment_name']?>" style="text-decoration:none;color:#4e77c9;" target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>"><?=$attachment['attachment_name']?></a>
					</div>
				<?php endforeach ?>
			<?php endif ?>
		</div>
	</div>
</div>