<script>
	$(document).ready(function(){
		$("#scrollable_content").height($("#body").height() - 155);
		
		previously_selected_truck_id = "<?=$truck["id"]?>";
	
		//load_truck_list();
		
		$("#main_content").show();
		
		//ADD SERVICE NOTE DIALOG
		$( "#add_service_notes").dialog(
		{
				autoOpen: false,
				height: 400,
				width: 420,
				modal: true,
				buttons: 
					[
						{
							text: "Save",
							click: function() 
							{
								if(!$("#new_note").val())
								{
									alert("You didn't enter a new note!");
								}
								else
								{
									save_truck_service_note();
								}
								
								
								
							},//end add load
						},
						{
							text: "Close",
							click: function() 
							{
								//CLEAR TEXT AREA
								$("#new_note").val("");
								$( this ).dialog( "close" );
							}
						}
					],//end of buttons
				
				open: function()
					{
					},//end open function
				close: function() 
					{
						//clear_load_info();
					}
		});//end settlement add notes dialog
		
	});//end document load
	
	
	//AJAX FOR GETTING SETTLEMENT NOTES
	function open_add_notes(truck_id)
	{
		//RESET LOADING GIF
		//$("#service_notes_ajax_div").html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-top:70px; margin-left:180px;" />');
		$("#service_notes_ajax_div").html("");
		
		//SET NOTES_ID TO VALUE OF LOAD ID
		//$("#notes_id").val(truck_id);
		$("#truck_id").val(truck_id); //this is the hidden form in the add notes form
		
		//OPEN THE DIALOG BOX
		$( "#add_service_notes").dialog( "open" );
		
		//alert('inside ajax');
				
		// GET THE DIV IN DIALOG BOX
		var service_notes_ajax_div = $('#service_notes_ajax_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/get_truck_service_notes/")?>"+"/"+truck_id, // in the quotation marks
			type: "POST",
			cache: false,
			context: service_notes_ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					service_notes_ajax_div.html(response);
					
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
		
		return false; 
		
	}//end open_add_notes()
	
	//VALIDATE AND SAVE TRUCK SERVICE NOTE
	function save_truck_service_note()
	{
		var dataString = "";
		
		$("#add_truck_service_note_form select").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#add_truck_service_note_form input").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#add_truck_service_note_form textarea").each(function() {
			//alert(this.id);
			//alert(this.value);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		//alert(dataString.substring(1));
		
		//CLEAR TEXT AREA
		$("#new_note").val("");
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var service_notes_ajax_div = $('#service_notes_ajax_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/add_truck_service_note")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: service_notes_ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					service_notes_ajax_div.html(response);
					
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
<div id="main_content_header">
	<span style="font-weight:bold;">Truck <?=$truck["truck_number"]?></span>
	<img src="/images/edit.png" style="cursor:pointer;float:right;margin-top:4px;height:20px; margin-left:10px;" id="edit_truck" onclick="load_truck_edit('<?=$truck["id"]?>')" />
	<img src="/images/back.png" style="cursor:pointer;float:right;margin-top:4px;height:20px;" id="back_btn" onclick="load_truck_summary()" />
	<img src="/images/paper_clip2.png" style="cursor:pointer;float:right;margin-right:10px;margin-top:2px;width:11px;" id="attachment_btn" onclick="open_file_upload(<?=$truck["id"]?>,'truck')" />
	<img src="/images/loading.gif" style="display:none;float:right;margin-top:4px;height:20px;" id="loading_img"/>
</div>

<div id="scrollable_content" class="scrollable_div">
	<div style="margin:20px;">
		<table id="truck_view" style="font-size: 14px;">
			<tr>
				<td>Truck Status</td>
				<td><?=$truck['status'];?></td>
			</tr>
			<tr>
				<td>Dropdown Status</td>
				<td><?=$truck['dropdown_status'];?></td>
			</tr>
			<tr>
				<td>Fleet Manager</td>
				<td><?=$truck['fleet_manager']["f_name"];?></td>
			</tr>
			<tr>
				<td>Driver Manager</td>
				<td><?=$truck['driver_manager']["f_name"];?></td>
			</tr>
			<tr>
				<td>Client</td>
				<td><?=$truck['client']["client_nickname"];?></td>
			</tr>
			<tr>
				<td>Co-Driver</td>
				<td><?=$truck['codriver']["client_nickname"];?></td>
			</tr>
			<tr>
				<td style="width:300px;">Pulling Trailer</td>
				<td><?=$truck['trailer']["trailer_number"];?></td>
			</tr>
			<tr>
				<td>Company Stickers</td>
				<td><?=$truck['company']["company_side_bar_name"];?></td>
			</tr>
			<tr>
				<td>Lease Company</td>
				<td><?=$truck['vendor']["company_side_bar_name"];?></td>
			</tr>
			<tr>
				<td style="width:300px;">Truck Number</td>
				<td><?=$truck['truck_number'];?></td>
			</tr>
			<tr>
				<td>Make</td>
				<td><?=$truck['make'];?></td>
			</tr>
			<tr>
				<td>Model</td>
				<td><?=$truck['model'];?></td>
			</tr>
			<tr>
				<td>Year</td>
				<td><?=$truck['year'];?></td>
			</tr>
			<tr>
				<td>Value</td>
				<td>$<?=number_format($truck['value'],2);?></td>
			</tr>
			<tr>
				<td>VIN</td>
				<td><?=$truck['vin'];?></td>
			</tr>
			<tr>
				<td>Plate Number</td>
				<td><?=$truck['plate_number'];?></td>
			</tr>
			<tr>
				<td>Insurance Policy</td>
				<td><?=$truck['insurance_policy'];?></td>
			</tr>
			<tr>
				<td>Rental Rate</td>
				<td><?=$truck['rental_rate'];?> per <?=$truck['rental_rate_period'];?></td>
			</tr>
			<tr>
				<td>Mileage Rate</td>
				<td><?=$truck['mileage_rate'];?></td>
			</tr>
			<tr>
				<td>Current Registration</td>
				<td>
					<?php if(!empty($truck['registration_link'])):?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$truck["registration_link"]?>" onclick="">Registration</a>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td>Current Insurance</td>
				<td>
					<?php if(!empty($truck['insurance_link'])):?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$truck["insurance_link"]?>" onclick="">Insurance</a>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td>Current IFTA</td>
				<td>
					<?php if(!empty($truck['ifta_link'])):?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$truck["ifta_link"]?>" onclick="">IFTA</a>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td>Current Lease Agreement</td>
				<td>
					<?php if(!empty($truck['lease_agreement_link'])):?>
						<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$truck["lease_agreement_link"]?>" onclick="">Lease Agreement</a>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td>Wet Service Interval</td>
				<td><?=number_format($truck['next_wet_service']); ?></td>
			</tr>
			<tr>
				<td>Dry Service Interval</td>
				<td><?=number_format($truck['next_dry_service']); ?></td>
			</tr>
			<tr>
				<td>Tank Capacity</td>
				<td><?=number_format($truck['fuel_tank_capacity']); ?> gallons</td>
			</tr>
			<tr>
				<td>Notes</td>
				<td><?=$truck['truck_notes'];?></td>
			</tr>
			<tr>
				<td>Service Log</td>
				<td>
					<button class="jq_button" style="width:140px;" id="edit_truck" onclick="open_add_notes('<?=$truck["id"]?>')">Service Log</button>
				</td>
			</tr>
		</table>
		<div id="truck_attachments" style="margin-top:15px;">
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

