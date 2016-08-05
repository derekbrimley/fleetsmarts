<style>
	.event_details_table tr
	{
		height:20px;
	}
	
	.event_details_text_box
	{
		width:60px;
		font-size:12px;
		height: 18px;
		position: relative;
		bottom: 3px;
		text-align:right;
	}
	
	.edit_<?=$log_entry_id?>
	{
		display:none;
	}
</style>
<script>
	
	refresh_event('<?=$log_entry_id?>');
	
	//DIALOG: LEG CALCULATIONS DIALOG
	$( "#leg_calculations_dialog_<?=$log_entry_id?>" ).dialog(
	{
			autoOpen: false,
			height: 280,
			width: 860,
			modal: true,
			buttons: 
				[
					<?php if(empty($log_entry["locked_datetime"])): ?>
						{
							text: "Lock",
							click: function() 
							{
								//VALIDATE THAT AMOUNTS ALLOCATED MATCH THE ALLOCATIONS IN THE DB
								var dataString = "&log_entry_id=" + <?=$log_entry_id?>;
			
								
								//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
								// GET THE DIV IN DIALOG BOX
								var this_div = $('#leg_calc_error_div_<?=$log_entry_id?>');
								
								//POST DATA TO PASS BACK TO CONTROLLER
								
								// AJAX!
								$.ajax({
									url: "<?= base_url("index.php/logs/validate_fuel_allocations")?>", // in the quotation marks
									type: "POST",
									data: dataString,
									cache: false,
									context: this_div, // use a jquery object to select the result div in the view
									statusCode: {
										200: function(response){
											// Success!
											this_div.html(response);
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
						},
					<?php endif; ?>
					{
						text: "Cancel",
						click: function() 
						{
							$( this ).dialog( "close" );
						}
					}
				],//end of buttons
			
			open: function()
				{
					$( "#leg_calculations_dialog_<?=$log_entry_id?>" ).html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" height="20px" style="height:20px; margin-left:400px; margin-top:95px;" />');
				},//end open function
			close: function() 
				{
					$( "#leg_calculations_dialog_<?=$log_entry_id?>" ).html("");
				}
	});//end dialog form
	
	//DIALOG: EXPORT LEG DIALOG
	$( "#export_leg_dialog_<?=$log_entry_id?>" ).dialog(
	{
			autoOpen: false,
			height: 430,
			width: 200,
			modal: true,
			buttons: 
				[
					{
						text: "Cancel",
						click: function() 
						{
							$( this ).dialog( "close" );
						}
					}
				],//end of buttons
			
			open: function()
				{
				},//end open function
			close: function() 
				{
				}
	});//end dialog form
	
	function edit_event(log_entry_id)
	{
		$('.edit_'+log_entry_id).css({"display":"block"});
		$('.details_'+log_entry_id).css({"display":"none"});
	}
	
	function save_leg(log_entry_id)
	{
	
		//VALIDATE FIELDS
		var isValid = true;
		
		var main_driver_split = $("#main_driver_split_"+log_entry_id).val();
		var codriver_split = $("#codriver_split_"+log_entry_id).val();
		var fuel_expense = $("#fuel_expense_"+log_entry_id).val();
		var gallons_used = $("#gallons_used_"+log_entry_id).val();
		var rate_type = $("#rate_type_"+log_entry_id).val();
		var fm_id = $("#fm_id_"+log_entry_id).val();
		var carrier_id = $("#carrier_id_"+log_entry_id).val();
		
		if(rate_type == 'Personal')
		{
			<?php if($leg["truck_id"] != 0): ?>
				isValid = false;
				alert("Drivers can't have personal time while in a truck!");
			<?php endif; ?>
		}
		else
		{
			<?php if($leg["truck_id"] == 0): ?>
				isValid = false;
				alert("If there is no truck on the event, it must be logged as Personal!");
			<?php endif; ?>
		}
		//alert(rate_type);
		if(rate_type != 'In Shop')
		{
			if(isNaN(main_driver_split))
			{
				isValid = false;
				alert("Main Driver Split must be a number!");
			}
			else
			{
				if(main_driver_split < 0 || main_driver_split > 100)
				{
					isValid = false;
					alert("Main Driver Split must be between 0 and 100!");
				}
			}
			
			if(codriver_split)
			{
				if(isNaN(codriver_split))
				{
					isValid = false;
					alert("Co-Driver Split must be a number!");
				}
				else
				{
					if(codriver_split < 0 || codriver_split > 100)
					{
						isValid = false;
						alert("Co-Driver Split must be between 0 and 100!");
					}
				}
		
			}
			
			if((Number(codriver_split) + Number(main_driver_split)) != 100)
			{
				isValid = false;
				alert("Main Driver Split and Co-Driver Split must add up to 100!");
			}
		}
		
		//VALIDATE FLEET MANAGER
		if(fm_id == "Select")
		{
			isValid = false;
			alert("Fleet Manager must be assigned to the leg!");
		}
		
		//VALIDATE CARRIER
		if(carrier_id == "Select")
		{
			isValid = false;
			alert("Carrier must be assigned to the leg!");
		}
		
			
		if(isValid)
		{
			var form_name = "edit_leg_"+log_entry_id;	
			var dataString = $("#"+form_name).serialize();
			
			//alert("load_log_list");
			
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var main_content = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/save_leg")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: main_content, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						//main_content.html(response);
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
		else
		{
			$("#save_icon_"+log_entry_id).attr("src","/images/save.png");
		}
	}
	
	function get_leg_calculations(leg_id,log_entry_id)
	{
		//VALIDATE FIELDS
		var isValid = true;
		
		var main_driver_split = $("#main_driver_split_"+log_entry_id).val();
		var codriver_split = $("#codriver_split_"+log_entry_id).val();
		var rate_type = $("#rate_type_"+log_entry_id).val();
		var fm_id = $("#fm_id_"+log_entry_id).val();
		var carrier_id = $("#carrier_id_"+log_entry_id).val();
		
		if(rate_type != 'In Shop')
		{
			if(isNaN(main_driver_split))
			{
				isValid = false;
				alert("Main Driver Split must be a number!");
			}
			else
			{
				if(main_driver_split < 0 || main_driver_split > 100)
				{
					isValid = false;
					alert("Main Driver Split must be between 0 and 100!");
				}
			}
			
			if(codriver_split)
			{
				if(isNaN(codriver_split))
				{
					isValid = false;
					alert("Co-Driver Split must be a number!");
				}
				else
				{
					if(codriver_split < 0 || codriver_split > 100)
					{
						isValid = false;
						alert("Co-Driver Split must be between 0 and 100!");
					}
				}
		
			}
			
			if((Number(codriver_split) + Number(main_driver_split)) != 100)
			{
				isValid = false;
				alert("Main Driver Split and Co-Driver Split must add up to 100!");
			}
		}
		
		//VALIDATE FLEET MANAGER
		if(fm_id == "Select")
		{
			isValid = false;
			alert("Fleet Manager must be assigned to the leg!");
		}
		
		//VALIDATE FLEET MANAGER
		if(carrier_id == "Select")
		{
			isValid = false;
			alert("Carrier must be assigned to the leg!");
		}

		if(isValid)
		{
			open_event_details(log_entry_id);
			$('#leg_calculations_dialog_'+log_entry_id).dialog("open");
		
			var main_content = $('#leg_calculations_dialog_'+log_entry_id);
		
			var dataString = "&log_entry_id="+log_entry_id;
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/get_leg_calculations")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: main_content, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						main_content.html(response);
						
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
	
	}
	
	function get_leg_export(log_entry_id)
	{
		open_event_details(log_entry_id);
		$('#export_leg_dialog_'+log_entry_id).dialog("open");
	
		var this_div = $('#export_leg_dialog_'+log_entry_id);
	
		var dataString = "&log_entry_id="+log_entry_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/get_leg_export")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, 
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					this_div.html(response);
					
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
<div style="height:95px;">
	<?php if(empty($log_entry["locked_datetime"])): ?>
		<div style="width:20px; float:right;">
			<img id="refresh_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:13px; position:relative; left:0px;" src="/images/refresh.png" title="Refresh" onclick="this.src='/images/loading.gif'; open_event_details('<?=$log_entry_id?>')"/>
			<img id="edit_icon" class="details_<?=$log_entry_id?>" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:0px;" src="/images/edit.png" title="Edit" onclick="edit_event('<?=$log_entry_id?>')"/>
			<img id="save_icon_<?=$log_entry_id?>" class="edit_<?=$log_entry_id?>" style="display:none; margin-bottom:13px; margin-right:15px; cursor:pointer; height:14px; position:relative; left:1px;" src="/images/save.png" title="Save" onclick="this.src='/images/loading.gif';save_leg('<?=$log_entry_id?>');"/>
			<img id="unlocked_icon" style="display:block; margin-bottom:12px; margin-right:15px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/unlocked.png" title="Lock" onclick="get_leg_calculations('<?=$leg["id"];?>','<?=$log_entry_id?>')"/>
			<img id="delete_icon" style="display:block; margin-right:15px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/trash.png" title="Delete" onclick="delete_event('<?=$log_entry_id?>')"/>
		</div>
		<div style="font-weight:bold; font-size:12px; float:left;">
			<img height="15px;" style="cursor:pointer;" src="<?=$validation_icon?>" onclick="alert('<?=$leg_validation_alert?>')"/>
			<span style="position:relative; bottom:4px;">Leg <?=$leg["id"];?></span>
		</div>
	<?php else: ?>
		<div style="width:60px; float:left;">
			<div style="font-size:12px; font-weight:bold;">
				Leg <?=$leg["id"];?>
			</div>
			<div style="font-size:12px; margin-top:67px;">
				<a  href="javascript:void" onclick="get_leg_export('<?=$log_entry_id?>')">Export</a>
			</div>
		</div>
		<div style="width:20px; float:right;">
			<img id="locked_icon" style="display:block; cursor:pointer; margin-bottom:12px; margin-right:15px; height:15px; position:relative; left:2px;" src="/images/locked.png" onclick="get_leg_calculations('<?=$leg["id"];?>','<?=$log_entry_id?>')" title="Locked <?=date("n/j H:i",strtotime($log_entry["locked_datetime"]))?>"/>
		</div>
	<?php endif; ?>
	<form id="edit_leg_<?=$log_entry_id?>" name="edit_leg_<?=$log_entry_id?>" >
		<input type="hidden" id="log_entry_id" name="log_entry_id" value="<?=$log_entry_id?>" />
		<table style="font-size:12px; margin-left:75px;" class="event_details_table">
			<tr>
				<td style="width:90px;">
					Fleet Manager
				</td>
				<td class="details_<?=$log_entry_id?>" style="width:110px; text-align:right;">
					<?=substr($leg["fleet_manager"]["company_side_bar_name"],0,strpos($leg["fleet_manager"]["company_side_bar_name"]," ")+2)?>
				</td>
				<td class="edit_<?=$log_entry_id?>" style="width:110px; text-align:right;">
					<?php echo form_dropdown("fm_id",$fm_options,$leg["fm_id"],"id='fm_id_$log_entry_id' class='event_details_text_box' style='width:70px;'");?>
				</td>
				<td style="width:130px;">
				</td>
				<td style="width:90px;">
					Fuel Expense
				</td>
				<td style="width:70px; text-align:right;">
					$<?=number_format($leg["fuel_expense"],2)?>
				</td>
				
				<td style="width:130px;">
				</td>
				<td style="width:100px;">
					Rate Type
				</td>
				<td class="details_<?=$log_entry_id?>" style="width:70px; text-align:right;">
					<?php if($leg["rate_type"] == "Auto"):?>
						<?=$leg_details["rate_type"];?>
					<?php else: ?>
						<?=$leg["rate_type"];?>
					<?php endif; ?>
				</td>
				<td class="edit_<?=$log_entry_id?>" style="width:70px; text-align:right;">
					<?php
						$options = array(
							"Auto" => "Auto",
							"Loaded" => "Loaded",
							"Light Freight" => "Light Freight",
							"Reefer" => "Reefer",
							"Dead Head" => "Dead Head",
							"Bobtail" => "Bobtail",
							"In Shop" => "In Shop",
							"Personal" => "Personal",
						);
					?>
					<?php echo form_dropdown("rate_type",$options,$leg["rate_type"],"id='rate_type_$log_entry_id' class='event_details_text_box' style='width:70px;'");?>
				</td>
			</tr>
			<tr>
				<td style="">
					Carrier
				</td>
				<td class="details_<?=$log_entry_id?>" style="text-align:right;">
					<?=$leg["carrier"]["company_side_bar_name"];?>
				</td>
				<td class="edit_<?=$log_entry_id?>" style="text-align:right;">
					<?php echo form_dropdown("carrier_id",$carrier_options,$leg["carrier_id"],"id='carrier_id_$log_entry_id' class='event_details_text_box' style='width:70px;'");?>
				</td>
				<td style="">
				</td>
				<td style="">
					Gallons Used
				</td>
				<td style=" text-align:right;">
					<?=number_format($leg["gallons_used"],2)?>
				</td>
				<td style="">
				</td>
				<td style="">
					Rate
				</td>
				<td class="details_<?=$log_entry_id?>" style="text-align:right;">
					<?php if($leg["rate_type"] == "Auto"):?>
						$<?=number_format($leg_details["revenue_rate"],2);?>
					<?php else: ?>
						$<?=number_format($leg["revenue_rate"],2);?>
					<?php endif; ?>
				</td>
				<td class="edit_<?=$log_entry_id?>" style="width:70px; text-align:right;">
					<?php if($leg["rate_type"] == "Auto"):?>
						Auto
					<?php else: ?>
						$<?=number_format($leg["revenue_rate"],2);?>
					<?php endif; ?>
					<input type="hidden" id="natl_fuel_avg" name = "natl_fuel_avg" value="<?=$leg["natl_fuel_avg"];?>" />
				</td>
			</tr>
			<tr>
				<td style="">
					Load
				</td>
				<td style="text-align:right;">
					<?=$leg["allocated_load"]["customer_load_number"];?>
				</td>
				<td style="">
				</td>
				<td style="">
					Main Driver Split
				</td>
				<td class="details_<?=$log_entry_id?>" style=" text-align:right;">
					<?=number_format($leg_details["existing_leg"]["main_driver_split"])?>%
				</td>
				<td class="edit_<?=$log_entry_id?>" style=" text-align:right;">
					<input id="main_driver_split_<?=$log_entry_id?>" name="main_driver_split_<?=$log_entry_id?>" class="event_details_text_box" type="text" value="<?=$leg_details["existing_leg"]["main_driver_split"];?>">
				</td>
				<td style="">
				</td>
				<td style="">
					Odometer Miles
				</td>
				<td style="text-align:right;">
					<?=number_format($leg["odometer_miles"]);?>
					<input type="hidden" id="odometer_miles" name = "odometer_miles" value="<?=$leg["odometer_miles"];?>" />
				</td>
			</tr>
			<tr>
				<td style="">
					Drivers
				</td>
				<td style="width:80px; text-align:right;">
					<?=substr($leg["main_driver"]["client_nickname"],0,strpos($leg["main_driver"]["client_nickname"]," "))?> / <?=substr($leg["codriver"]["client_nickname"],0,strpos($leg["codriver"]["client_nickname"]," "))?>
				</td>
				<td style="">
				</td>
				<td style="">
					Co-Driver Split
				</td>
				<td class="details_<?=$log_entry_id?>" style=" text-align:right;">
					<?=number_format($leg_details["existing_leg"]["codriver_split"])?>%
				</td>
				<td class="edit_<?=$log_entry_id?>" style=" text-align:right;">
					<input id="codriver_split_<?=$log_entry_id?>" name="codriver_split_<?=$log_entry_id?>" class="event_details_text_box" type="text" value="<?=$leg_details["existing_leg"]["codriver_split"];?>">
				</td>
				<td style="">
				</td>
				<td style="">
					Map Miles
				</td>
				<td style="text-align:right;">
					<?=number_format($leg["map_miles"]);?>
					<input type="hidden" id="map_miles" name = "map_miles" value="<?=$leg["map_miles"];?>" />
				</td>
			</tr>
			<tr>
				<td style="">
					Truck/Trailer
				</td>
				<td style="text-align:right;">
					<?=$leg["truck"]["truck_number"];?> / <?=$leg["trailer"]["trailer_number"];?>
				</td>
				<td style="">
				</td>
				<td style="">
					Hours
				</td>
				<td style=" text-align:right;">
					<?=number_format($leg["hours"],2);?>
					<input type="hidden" id="hours" name = "hours" value="<?=round($leg["hours"],2);?>" />
				</td>
				<td style="">
				</td>
				<td style="">
					OOR %
				</td>
				<td style="text-align:right;">
					<?=$leg_details["oor"];?>%
				</td>
			</tr>
		</table>
	</form>
</div>

<div id="export_leg_dialog_<?=$log_entry_id?>" name="export_leg_dialog_<?=$log_entry_id?>" title="Export Leg Calculations" style="display:none;" >
	<!-- AJAX GOES HERE !-->
</div>


<div id="leg_calculations_dialog_<?=$log_entry_id?>" name="leg_calculations_dialog_<?=$log_entry_id?>" title="Leg Calculations">
	<!-- AJAX GOES HERE !-->
</div>
