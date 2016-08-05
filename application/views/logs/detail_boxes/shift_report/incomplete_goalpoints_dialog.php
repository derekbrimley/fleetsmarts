<script>
</script>
<div id="incomplete_goalpoints_dialog_overlay_<?=$log_entry_id?>"  style="display:none; height:100%; width:100%; position:absolute; top:0px; left:0; z-index:10; background:rgba(191,191,191,0.9);">
	<div style="width:100%; text-align:center; height:14px; margin-top:150px;">
		Waiting for validation...
	</div>
</div>

	<form id="incomplete_goalpoints_form_<?=$log_entry_id?>">
		<input type="hidden" name="log_entry_id" value="<?=$log_entry_id?>"/>
		<input type="hidden" id="new_ca_time_<?=$log_entry_id?>" name="new_ca_time" value="<?=$new_ca_time?>"/>
		<input type="hidden" name="new_ca_gps" value="<?=$new_ca_gps?>"/>
		<input type="hidden" name="new_ca_method" value="<?=$new_ca_method?>"/>
		<input type="hidden" name="new_ca_result" value="<?=$new_ca_result?>"/>
		<input type="hidden" name="new_ca_notes" value="<?=$new_ca_notes?>"/>
		<?php foreach($incomplete_goalpoints as $gp):?>
			<?php
				$gp_id = $gp["id"];
			?>
			<hr style="width:450px;">
			<div id="incomplete_gp_<?=$gp["id"]?>" style="padding:20px;">
				<div style="color:black;">
					This driver was suppose to make a <?=$gp["gp_type"]?>  <b><?=$gp["arrival_departure"]?></b> in <?=$gp["location"]?> at <?=date("m/d/y H:i",strtotime($gp["expected_time"]))?>.
				</div>
				<table style="margin-top:20px;">
					<tr>
						<td style="width:150px; vertical-align:middle;">
							Did this event happen?
						</td>
						<td style=" vertical-align:middle;">
							<?php
								$options = array(
									"Select" => "Select",
									"Yes" => "Yes",
									"No" => "No",
								);
							?>
							<?php echo form_dropdown("did_gp_happen_$gp_id",$options,"Select","id='did_gp_happen_$gp_id' class='' style='width:100px; height:24px;' onchange='did_event_happen_selected($gp_id)'");?>
						</td>
					</tr>
					<tr style="">
						<td class="what_time_row_<?=$gp_id?>" style=" vertical-align:middle; display:none;">
							What time?
						</td>
						<td class="what_time_row_<?=$gp_id?>" style=" vertical-align:middle; display:none;">
							<input type="text" placeholder="<?=date("m/d/y H:i",strtotime($gp["expected_time"]))?>" id="gp_completion_date_<?=$gp_id?>" name="gp_completion_date_<?=$gp_id?>" style="width:100px; height:24px;"/>
						</td>
					</tr>
				</table>
			</div>
		<?php endforeach;?>
	</form>
	<div style="display:none;" id="validation_response_<?=$log_entry_id?>">
		<!-- AJAX GOES HERE -- JAVASCRIPT RESPONSE!-->
	</div>