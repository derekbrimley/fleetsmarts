<script>
	function create_new_leg(id)
	{
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		//var this_div = $('#main_content');
		var this_div = $('#script_div_'+id);
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&event_id="+id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/create_new_leg")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					//load_log_list();
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error!")
					this_div.html(response);
				}
			}
		});//END AJAX
	}
	
	function oor_clicked(log_entry_id)
	{
		if($("#oor_cb_"+log_entry_id).is(":checked"))
		{
			var is_oor = "yes";
		}
		else
		{
			var is_oor = "no";
		}
		//alert("hello");
		var dataString = "&is_oor="+is_oor+"&log_entry_id="+log_entry_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/mark_oor")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			statusCode: {
				200: function(response){
					// Success!
					open_event_details(log_entry_id);
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error!")
				}
			}
		});//END AJAX
		
	}
	
</script>
<div id="script_div_<?=$log_entry_id?>">
	<!-- AJAX SCRIPT GOES HERE !-->
</div>
<div style="font-size:12px;">
	<?php if(empty($log_entry["locked_datetime"])): ?>
		<div style="height: 80px; width:20px; float:right;">
			<img id="refresh_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:13px; position:relative; left:0px;" src="/images/refresh.png" title="Refresh" onclick="this.src='/images/loading.gif'; open_event_details('<?=$log_entry_id?>')"/>
			<?php if(empty($log_entry["sync_entry_id"])): ?>
				<img title="End Leg" style='display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; bottom:1px; left:1px;' src="/images/make_new_leg.png" onclick="create_new_leg('<?=$log_entry_id?>')"/>
			<?php endif; ?>
			<img title="Estimate Odometer" style='display:block; margin-bottom:12px; cursor:pointer; height:13px; position:relative; bottom:0px; right:1px;' src="/images/odometer.png" onclick="estimate_odometer('<?=$log_entry_id?>','<?=$log_entry["sync_entry_id"]?>')"/>
			<img id="attachment_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:18px; position:relative; left:3px;" src="/images/paper_clip2.png" title="Attach Document" onclick="open_file_upload('<?=$log_entry_id?>')"/>
			<img id="new_checkpoint" style="margin-bottom:13px; margin-right:15px; cursor:pointer; height:16px; position:relative; left:2px;" src="/images/new_checkpoint.png" title="New Checkpoint" onclick="this.src='/images/loading.gif'; create_new_checkpoint('<?=$log_entry["id"]?>');"/>
			<img id="delete_icon" style="display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/trash.png" title="Delete" onclick="delete_event('<?=$log_entry_id?>')"/>
		</div>
	<?php else: ?>
		<div style="height: 45px; width:20px; float:right;">
			<img title="End Leg" style='display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; bottom:1px; left:1px;' src="/images/make_new_leg.png" onclick="create_new_leg('<?=$log_entry_id?>')"/>
		</div>
	<?php endif;?>
	<div style="width:60px; float:left;">
		<div style="font-size:12px; font-weight:bold; width:80px;">
			Driver In
		</div>
	</div>
	<div style="margin-left:120px;">
		<div class="heading">Details</div>
		<hr style="width:715px;">
	</div>
	<div style="margin-left:120px;margin-bottom:10px;">OOR
		<input <?=$checked?> onclick="oor_clicked('<?=$log_entry_id?>')" type="checkbox" id="oor_cb_<?=$log_entry_id?>" name="oor_cb_<?=$log_entry_id?>"/>
	</div>
	<table style="margin-left:120px; margin-top:5px; margin-bottom:10px; line-height:16px;">
		<tr style="height:40px;">
			<td style="font-weight:bold; width:175px;">
				Notes
			</td>
			<td style="width:550px;">
				<?=$log_entry["entry_notes"]?>
			</td>
		</tr>
		<tr style="height:40px;">
			<td style="font-weight:bold; width:175px;">
				Rental Agreement
			</td>
			<td style="width:550px;">
				<?php if(!empty($driver_in["rental_agreement_guid"])):?>
					<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$driver_in["rental_agreement_guid"]?>" onclick="">Equipment Rental Agreement</a>
				<?php endif;?>
			</td>
		</tr>
		<tr style="height:40px;">
			<td style="font-weight:bold; width:175px;">
				OO Lease Agreement
			</td>
			<td style="width:550px;">
				<?php if(!empty($driver_in["oo_lease_agreement_guid"])):?>
					<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$driver_in["oo_lease_agreement_guid"]?>" onclick="">OO Lease Agreement</a>
				<?php endif;?>
			</td>
		</tr>
	</table>
	<div id="attachment_div" style="margin-left:120px;">
		<div class="heading" style="">Attachments</div>
		<hr style="width:715px;">
		<?php if(!empty($attachments)):?>
				<?php foreach($attachments as $attachment):?>
					<div class="attachment_box" style="float:left;margin:5px;">
						<a target="_blank" style="text-decoration:none;color:#4e77c9;" href="<?=base_url("/index.php/documents/download_file")."/".$attachment["file_guid"]?>" onclick=""><?=$attachment["attachment_name"]?></a>&nbsp;
					</div>
				<?php endforeach;?>
		<?php endif;?>
	</div>
	<div style="clear:both;"></div>
</div>