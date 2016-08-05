<script>
	$("#scrollable_content").height($(window).height() - 155);
	
	previously_selected_trailer_id = "<?=$trailer["id"]?>";
	
	//load_trailer_list();
	
	$("#main_content").show();
</script>

<div id="main_content_header">
	<span style="font-weight:bold;">Trailer <?=$trailer["trailer_number"]?></span>
	<img src="<?=base_url("images/edit.png")?>" style="cursor:pointer;float:right;margin-top:4px;height:20px; margin-left:10px;" id="edit_trailer" onclick="load_trailer_edit('<?=$trailer["id"]?>')" />
	<img src="<?=base_url("images/back.png")?>" style="cursor:pointer;float:right;margin-top:4px;height:20px;" id="back_btn" onclick="load_trailer_summary()" />
	<img src="<?=base_url("images/paper_clip2.png")?>" style="cursor:pointer;float:right;margin-right:10px;margin-top:2px;width:11px;" id="attachment_btn" onclick="open_file_upload(<?=$trailer["id"]?>,'trailer')" />
	<img src="<?=base_url("images/loading.gif")?>" style="display:none;float:right;margin-top:4px;height:20px;" id="loading_img"/>
</div>

<div id="scrollable_content" class="scrollable_div">
	<div style="margin:20px;">
		<table id="trailer_view" style="font-size: 14px;">
			<tr>
				<td>Trailer Status</td>
				<td><?=$trailer['trailer_status'];?></td>
			</tr>
			<tr>
				<td>Dropdown Status</td>
				<td><?=$trailer['dropdown_status'];?></td>
			</tr>
			<tr>
				<td>Client</td>
				<td><?=$trailer['client']["client_nickname"];?></td>
			</tr>
			<tr>
				<td>Lease Company</td>
				<td><?=$trailer['vendor']["company_side_bar_name"];?></td>
			</tr>
			<tr>
				<td style="width:300px;">Trailer Number</td>
				<td><?=$trailer['trailer_number'];?></td>
			</tr>
			<tr>
				<td>Trailer Type</td>
				<td><?=$trailer['trailer_type'];?></td>
			</tr>
			<tr>
				<td>Length</td>
				<td><?=$trailer['length'];?></td>
			</tr>
			<tr>
				<td>Door Type</td>
				<td><?=$trailer['door_type'];?></td>
			</tr>
			<tr>
				<td>Tire Model</td>
				<td><?=$trailer['tire_model'];?></td>
			</tr>
			<tr>
				<td>Tire Make</td>
				<td><?=$trailer['tire_make'];?></td>
			</tr>
			<tr>
				<td>Tire Size</td>
				<td><?=$trailer['tire_size'];?></td>
			</tr>
			<tr>
				<td>Insulation</td>
				<td><?=$trailer['insulation_type'];?></td>
			</tr>
			<tr>
				<td>Vents</td>
				<td><?=$trailer['vent_type'];?></td>
			</tr>
			<tr>
				<td>E Tracks</td>
				<td><?=$trailer['etracks'];?></td>
			</tr>
			<tr>
				<td>Suspension</td>
				<td><?=$trailer['suspension_type'];?></td>
			</tr>
			<tr>
				<td>Trailer Make</td>
				<td><?=$trailer['make'];?></td>
			</tr>
			<tr>
				<td>Trailer Model</td>
				<td><?=$trailer['model'];?></td>
			</tr>
			<tr>
				<td>Trailer Year</td>
				<td><?=$trailer['year'];?></td>
			</tr>
			<tr>
				<td>Trailer Value</td>
				<td>
					<?php
						if(empty($trailer['value']))
						{
							$trailer_value = "";
						}
						else
						{
							$trailer_value = "$".number_format($trailer['value'],2);
						}
					?>
					<?=$trailer_value?>
				</td>
			</tr>
			<tr>
				<td>VIN</td>
				<td><?=$trailer['vin'];?></td>
			</tr>
			<tr>
				<td>Plate Number</td>
				<td><?=$trailer['plate_number'];?></td>
			</tr>
			<tr>
				<td>Plate State</td>
				<td><?=$trailer['plate_state'];?></td>
			</tr>
			<tr>
				<td>Insurance Policy</td>
				<td><?=$trailer['insurance_policy'];?></td>
			</tr>
			<tr>
				<td>Rental Rate</td>
				<td><?=$trailer['rental_rate'];?> per <?=$trailer['rental_period'];?></td>
			</tr>
			<tr>
				<td>Mileage Rate</td>
				<td><?=$trailer['mileage_rate'];?></td>
			</tr>
			<tr>
				<td>Current Registration</td>
				<td>
					<?php if(!empty($trailer['registration_link'])):?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$trailer["registration_link"]?>" onclick="">Current Registration</a>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td>Current Insurance</td>
				<td>
					<?php if(!empty($trailer['insurance_link'])):?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$trailer["insurance_link"]?>" onclick="">Current Insurance</a>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td>Current Lease Agreement</td>
				<td>
					<?php if(!empty($trailer['lease_agreement_link'])):?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$trailer["lease_agreement_link"]?>" onclick="">Current Lease Agreement</a>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td>Last Inspection (mileage)</td>
				<td><?=number_format($trailer['last_inspection']); ?></td>
			</tr>
			<tr>
				<td>Last Service (mileage)</td>
				<td><?=number_format($trailer['last_service']); ?></td>
			</tr>
		</table>
		<div id="trailer_attachments" style="margin-top:15px;">
			<span class="section_heading">Attachments</span>
			<hr>
			<br>
			<?php if(!empty($attachments)): ?>
				<?php foreach($attachments as $attachment): ?>
					<div class="attachment_box" style="float:left;margin:5px;margin-bottom:30px;">
						<a target="_blank" title="Download <?=$attachment['attachment_name']?>" style="text-decoration:none;color:#4e77c9;" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>"><?=$attachment['attachment_name']?></a>
					</div>
				<?php endforeach ?>
			<?php endif ?>
		</div>
	</div>
</div>