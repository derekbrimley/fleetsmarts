<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
<title>Fuel Upload</title>
<script>
	
	function validate_fuel_report()
	{
		var isValid = true;
		
		var number_of_trans = $("#number_of_trans").val();
		for(i = 1;i <= number_of_trans; i++)
		{
			//ONLY VALIDATE IF ENTRY DOESN'T EXIST IN DB ALREADY
			if($("#exists_"+i).val() == "no")
			{
				/**
				//VALIDATE DRIVER
				if($("#fm_"+i).val() == "Select")
				{
					isValid = false;
					alert("Fleet Manager row "+i);
				}
				**/
				
				//VALIDATE DRIVER
				if($("#card_name_"+i).val() == "Select")
				{
					isValid = false;
					alert("Main Driver row "+i);
				}
			
				//VALIDATE DRIVER
				if($("#card_name_"+i).val() == "Select")
				{
					isValid = false;
					alert("Main Driver row "+i);
				}
				
				//VALIDATE TRUCK
				if($("#truck_number_"+i).val() == "Select")
				{
					isValid = false;
					alert("Truck row "+i);
				}
				
				//VALIDATE TRAILER
				if($("#trailer_number_"+i).val() == "Select")
				{
					isValid = false;
					alert("Trailer row "+i);
				}
				
				//VALIDATE ODOMETER
				if(isNaN($("#odometer_"+i).val()))
				{
					isValid = false;
					alert("Odometer row "+i);
				}
				
				//VALIDATE IS_FILL
				if($("#is_fill_"+i).val() == "Select")
				{
					isValid = false;
					alert("Fill? row "+i);
				}
			}
			
		}//end for loop
		
		//IF VALID SAVE
		if(isValid)
		{
			//alert("save");
			$("#upload_fuel_report_form").submit();
		}
		else
		{
			alert("Something is wrong... make sure there is no red!");
		}
	}
</script>
<div style="width:1100px; margin:auto; margin-top:25px; font-family:arial;">
	<div style="width:221px; margin:auto; font-size:16px; font-weight:bold;">
		<?=$source?> Fuel Report Upload
		<br><br><br>
		<button id="upload_transactions" name="upload_transactions" class="jq_button" style="width:200px; height:50px; margin:auto;" onclick="validate_fuel_report()">Upload to Database</button>
	</div>
	<?php $attributes = array('name'=>'upload_fuel_report_form','id'=>'upload_fuel_report_form')?>
	<?=form_open('logs/add_fuel_stops',$attributes);?>
	<input type="hidden" id="source" name="source" value="<?=$source?>" />
	<input type="hidden" id="file_name" name="file_name" value="<?=$file_name?>" />
		<br>
		<span style="font-size:12px;">* right click to edit a red entry</span>
		<table style="margin-top:10px; font-size:12px;">
			<tr class="heading" style="line-height:30px;">
				<!-- <td style="width:70px;" VALIGN="top">FM</td> !-->
				<td style="width:100px;" VALIGN="top">Main Driver</td>
				<td style="width:70px;" VALIGN="top">Truck</td>
				<td style="width:60px;" VALIGN="top">Trailer</td>
				<td style="width:130px;" VALIGN="top">Datetime</td>
				<td style="width:150px;" VALIGN="top">Address</td>
				<td style="width:85px;" VALIGN="top">City</td>
				<td style="width:35px;" VALIGN="top">State</td>
				<td style="width:70px; text-align:right;" VALIGN="top">Odometer</td>
				<td style="width:55px; text-align:right;" VALIGN="top">Gallons</td>
				<td style="width:60px; text-align:right;" VALIGN="top">Fill?</td>
				<td style="width:60px; text-align:right;" VALIGN="top">Expense</td>
				<td style="width:100px; padding-left:15px;" VALIGN="top">Notes</td>
			</tr>
			<?php 
				$i = 0;
			?>
			<?php foreach($entries as $entry): ?>
				<?php
					
					$i++;
					$exists = "no";
					$row_style = "";
					if(fuel_stop_exists($entry))
					{
						$exists = "yes";
						$row_style = "color:#CFCFCF;";
					}
					
					
					
					//VALIDATE DRIVER
					$driver_style = "";
					if(!in_array($entry["card_name"],$driver_array))
					{
						$driver_style = "color:red; font-weight:bold;";
					}
					
					//VALIDATE TRUCK
					$truck_style = "";
					if(!in_array($entry["truck_number"],$truck_array))
					{
						$truck_style = "color:red; font-weight:bold;";
					}
					
					//VALIDATE TRUCK
					$trailer_style = "";
					if(!in_array($entry["trailer_number"],$trailer_array))
					{
						$trailer_style = "color:red; font-weight:bold;";
					}
					
					//VALIDATE IS_FILL
					$is_fill_style = "";
					if(!($entry["is_fill"] == "Y" || $entry["is_fill"] == "N"))
					{
						$is_fill_style = "color:red; font-weight:bold;";
					}
					
					// $fm_style = "";
					// //GET FLEET MANAGER FOR DRIVER
					// $driver_id = @$reverse_driver_array[$entry["card_name"]];
					// $where = null;
					// $where["id"] = $driver_id;
					// $driver = db_select_client($where);
					
					// $fleet_manager = $driver["fleet_manager"];
					
					// if(empty($fleet_manager))
					// {
						// $fm_style = "color:red; font-weight:bold;";
						// $fm_account["id"] = "Select";
						// $fleet_manager["f_name"] = "Select";
					// }
					// else
					// {
						// $where = null;
						// $where["person_id"] = $fleet_manager["id"];
						// $where["type"] = "Fleet Manager";
						// $fm_company = db_select_company($where);
					
						// $where = null;
						// $where["company_id"] = $fm_company["id"];
						// $where["category"] = "Pay";
						// $fm_account = db_select_account($where);
						
						// if(empty($fm_account))
						// {
							// $fm_style = "color:red; font-weight:bold;";
						// }
					// }
					
				?>
				<input id="exists_<?=$i?>" name="exists_<?=$i?>" type="hidden" value="<?=$exists?>"/>
				<input id="guid_<?=$i?>" name="guid_<?=$i?>" type="hidden" value="<?=$entry["guid"]?>"/>
				<tr style="<?=$row_style?> font-size:10px;">
					<?php
						/**
						<td style="<?=$fm_style?>">
							<div id="fm_text_<?=$i?>" style="display:inline;" oncontextmenu="$('#fm_text_<?=$i?>').hide(); $('#fm_<?=$i?>').show(); return false;" ><?=$fleet_manager["f_name"]?></div>
							<div oncontextmenu="$('#fm_text_<?=$i?>').show(); $('#fm_<?=$i?>').hide(); return false;"><?php echo form_dropdown("fm_$i",$fm_dropdown_options,$fm_account["id"],"id='fm_$i' style='font-size:10px; height:18px; width55px; display:none;' ");?></div>
						</td>
						**/
					?>
					<td style="<?=$driver_style?>">
						<div id="driver_text_<?=$i?>" style="display:inline;" oncontextmenu="$('#driver_text_<?=$i?>').hide(); $('#card_name_<?=$i?>').show(); return false;" ><?=$entry["card_name"]?></div>
						<div oncontextmenu="$('#driver_text_<?=$i?>').show(); $('#card_name_<?=$i?>').hide(); return false;"><?php echo form_dropdown("card_name_$i",$main_driver_dropdown_options,@$reverse_driver_array[$entry["card_name"]],"id='card_name_$i' style='font-size:10px; height:18px; width:95px; display:none;' ");?></div>
					</td>
					<td style="<?=$truck_style?>">
						<div id="truck_text_<?=$i?>" style="display:inline;" oncontextmenu="$('#truck_text_<?=$i?>').hide(); $('#truck_number_<?=$i?>').show(); return false;" ><?=$entry["truck_number"]?></div>
						<div oncontextmenu="$('#truck_text_<?=$i?>').show(); $('#truck_number_<?=$i?>').hide(); return false;"><?php echo form_dropdown("truck_number_$i",$truck_dropdown_options,@$reverse_truck_array[$entry["truck_number"]],"id='truck_number_$i' style='font-size:10px; height:18px; width:65px; display:none;' ");?></div>
					</td>
					<td style="<?=$trailer_style?>">
						<div id="trailer_text_<?=$i?>" style="display:inline;" oncontextmenu="$('#trailer_text_<?=$i?>').hide(); $('#trailer_number_<?=$i?>').show(); return false;" ><?=$entry["trailer_number"]?></div>
						<div oncontextmenu="$('#trailer_text_<?=$i?>').show(); $('#trailer_number_<?=$i?>').hide(); return false;"><?php echo form_dropdown("trailer_number_$i",$trailer_dropdown_options,@$reverse_trailer_array[$entry["trailer_number"]],"id='trailer_number_$i' style='font-size:10px; height:18px; width:60px; display:none;' ");?></div>
					</td>
					<td>
						<?=$entry["entry_datetime"]?>
						<input id="entry_datetime_<?=$i?>" name="entry_datetime_<?=$i?>" type="hidden" value="<?=$entry["entry_datetime"]?>"/>
					</td>
					<td class="ellipsis">
						<?=$entry["address"]?>
						<input id="address_<?=$i?>" name="address_<?=$i?>" type="hidden" value="<?=$entry["address"]?>"/>
					</td>
					<td>
						<?=$entry["city"]?>
						<input id="city_<?=$i?>" name="city_<?=$i?>" type="hidden" value="<?=$entry["city"]?>"/>
					</td>
					<td>
						<?=$entry["state"]?>
						<input id="state_<?=$i?>" name="state_<?=$i?>" type="hidden" value="<?=$entry["state"]?>"/>
					</td>
					<td style="text-align:right;">
						<input id="odometer_<?=$i?>" name="odometer_<?=$i?>" type="text" value="<?=$entry["odometer"]?>" style="text-align:right; font-size:10px; width:60; height:18px;"/>
					</td>
					<td style="text-align:right;">
						<?=$entry["gallons"]?>
						<input id="gallons_<?=$i?>" name="gallons_<?=$i?>" type="hidden" value="<?=$entry["gallons"]?>"/>
					</td>
					<td style="text-align:right; <?=$is_fill_style?> ">
						<?php
							$options = array
							(
								"Select" => "Select",
								"Y" => "Yes",
								"N" => "No",
								"R" => "Reefer",
							);
						?>
						<?php echo form_dropdown("is_fill_$i",$options,$entry["is_fill"],"id='is_fill_$i' style='font-size:12px; height:18px; width:50px;' ");?>
					</td>
					<td style="text-align:right;">
						<?=$entry["fuel_expense"]?>
						<input id="fuel_expense_<?=$i?>" name="fuel_expense_<?=$i?>" type="hidden" value="<?=$entry["fuel_expense"]?>"/>
						<input id="rebate_amount_<?=$i?>" name="rebate_amount_<?=$i?>" type="hidden" value="<?=$entry["rebate_amount"]?>"/>
					</td>
					<td style=" padding-left:15px;">
						<input id="notes_<?=$i?>" name="notes_<?=$i?>" type="text" value="<?=$entry["entry_notes"]?>" style="font-size:10px; width:100px; height:18px;"/>
					</td>
				</tr>
			<?php endforeach; ?>
			
			<input type="hidden" id="number_of_trans" name="number_of_trans" value="<?=$i?>">
			
		</table>
	</form>
	
</div>