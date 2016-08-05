<html>
	<!-- 	TODO: 								!-->
	<!-- 	SEARCH FUNCTION 					!-->
	<!-- 	ACTIVE/INACTIVE FILTER BOX			!-->
	<!-- 	FLEET MANAGER FILTER BOX			!-->
	<head>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
		<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>

		<script type="text/javascript">
		$(document).ready(function(){
			//SET HEIGHT OF MAIN CONTENT AND SCROLLABLE CONTENT
			$("#main_content").height($("#body").height() - 130);
			$("#scrollable_content").height($("#body").height() - 170);
			//alert($("#main_content").height());
			
			//PLACE DATE PICKERS ON ALL THE DATE BOXES
			<?php foreach($all_stops as $stop): ?>
				$('#date_<?=$stop["id"]?>').datepicker({ showAnim: 'blind' });
			<?php endforeach; ?>
			//HANDLE BUTTONS
			$("#cancel_edits").attr( "disabled", true );
			$("#save_edits").attr( "disabled", true );
			
			$("#cancel_edits").click(function()
			{
				window.location = "<?= base_url('index.php/driver_logs/index/'.$this_client['id']);?>"
			});
			
			$("#save_edits").click(function()
			{
				var is_valid = true;
				<?php $i = 1; ?>
				<?php foreach ($all_stops as $stop): ?>
					
					if($("#should_update_<?=$stop["id"]?>").val() == "Yes")
					{
						//VALIDATE DATE
						if(!isDate($("#date_<?=$stop["id"]?>").val()))
						{
							is_valid = false;
							alert("Date must be valid on row <?=$i?>")
						}
						
						//VALIDATE TIME
						if(!isTime($("#time_<?=$stop["id"]?>").val()))
						{
							is_valid = false;
							alert("Time must be valid on row <?=$i?>")
						}
						
						//VALIDATE CITY
						if(!$("#city_<?=$stop["id"]?>").val())
						{
							is_valid = false;
							alert("City must be entered on row <?=$i?>")
						}
						
						//VALIDATE STATE
						if(!$("#state_<?=$stop["id"]?>").val())
						{
							is_valid = false;
							alert("State must be entered on row <?=$i?>")
						}
						
						//VALIDATE LOCATION
						if(!$("#location_<?=$stop["id"]?>").val())
						{
							is_valid = false;
							alert("Location must be entered on row <?=$i?>")
						}
						
						//VALIDATE ADDRESS
						if(!$("#address_<?=$stop["id"]?>").val())
						{
							is_valid = false;
							alert("Address must be entered on row <?=$i?>")
						}
						
						//VALIDATE ODOMETER
						if(isNaN($("#odometer_<?=$stop["id"]?>").val()))
						{
							is_valid = false;
							alert("Odometer must be valid on row <?=$i?>")
						}
						<?php if ($stop["stop_type"] == "Fuel - Fill" || $stop["stop_type"] == "Fuel - Partial"):?>
							//VALIDATE GALLONS
							if(isNaN($("#gallons_<?=$stop["id"]?>").val()))
							{
								is_valid = false;
								alert("Gallons must be valid on row <?=$i?>")
							}
							
							//VALIDATE INVOICE
							if(isNaN($("#invoice_<?=$stop["id"]?>").val()))
							{
								is_valid = false;
								alert("Invoice must be valid on row <?=$i?>")
							}
						<?php endif; ?>
						
						<?php $i++;?>
					}
				<?php endforeach; ?>
			
				if (is_valid)
				{
					$("#save_edit").submit();
					//alert("save");
				}
			});//end save stop
			
			$("#new_log_entry").click(function()
			{
				$( "#add_stop_dialog" ).dialog( "open" );
			});
			
			//ADD NEW STOP DIALOG
			$( "#add_stop_dialog" ).dialog(
			{
					autoOpen: false,
					height: 530,
					width: 380,
					modal: true,
					buttons: 
						[
							{
								text: "Save",
								click: function() 
								{
									validate_add_stop_form();
								},//end add load
							},
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
							clear_all_fields_for_new_stop();
							$("#stop_type_dropdown").val("Select Stop");
							$("#client_dropdown").val("Select Client");
						}
			});//end dialog form
			
			
			<?php foreach($all_stops as $stop): ?>
				//ADD STOP NOTE	
				$( "#add_stop_notes_<?=$stop["id"]?>" ).dialog(
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
										if(!$("#new_note_<?=$stop["id"]?>").val())
										{
											alert("You didn't enter a new note!");
										}
										else
										{
											$("#note_text").val($("#new_note_<?=$stop["id"]?>").val());
											$("#notes_stop_id").val("<?=$stop["id"]?>");
											$("#add_stop_note").submit();
										}
										
										
										
									},//end add load
								},
								{
									text: "Cancel",
									click: function() 
									{
										//clear_load_info();
										
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
				});//end billing checklist dialog
				
			<?php endforeach; ?>
		
		});//end document ready
		
		//SHOWS THE EDIT ROW ON ROW CLICK
		function view_row_clicked(stop_id)
		{
			//HIDE VIEW ROW AND SHOW EDIT ROW
			$("#view_row_"+stop_id).hide();
			$("#edit_row_"+stop_id).show();
			
			//CHANGE HIDDEN FIELD TO YES
			$("#should_update_"+stop_id).val("YES");
			
			//ENABLE CANCEL BUTTON
			$("#cancel_edits").removeClass("jq_button_disabled").addClass("jq_button");
			$("#cancel_edits").attr( "disabled", false );
			
			//ENABLE SAVE BUTTON
			$("#save_edits").removeClass("jq_button_disabled").addClass("jq_button");
			$("#save_edits").attr( "disabled", false );
		}
		
		//OPEN ADD NOTES DIALOG ON CLICK
		function open_add_notes(stop_id)
		{
			$( "#add_stop_notes_"+stop_id ).dialog( "open" );
			
		}//end open_add_notes_dialog()
		
		//CONFIRM DELETE FOR DELETE STOP
		function delete_stop(stop_id)
		{
			if (confirm("Are you sure you want to delete this stop?")) 
			{ 
				$("#stop_id").val(stop_id);
				$("#delete_stop").submit();
			}
			
			
		}
		
		//STEP BY STEP FOR NEW STOP FORM
		function client_selected()
		{
			$("#stop_type_row").show();
		}
		
		//STEP BY STEP FOR NEW STOP FORM
		function stop_selected()
		{
			//alert("hello");
			clear_all_fields_for_new_stop();
			
			$("#stop_type_row").show();
			$("#date_row").show();
			$("#time_row").show();
			$("#odometer_row").show();
			$("#location_row").show();
			$("#city_row").show();
			$("#state_row").show();
			$("#address_row").show();
			$("#notes_row").show();
			
			if($("#stop_type_dropdown").val() == "Pick" || $("#stop_type_dropdown").val() == "Drop")
			{
				$("#load_id_row").show();
			}
			else if($("#stop_type_dropdown").val() == "Fuel Stop")
			{
				$("#fill_or_partial_row").show();
				$("#gallons_row").show();
				$("#invoice_amount_row").show();
			}
			
		}//END STOP SELECTED
		
		//CLEAR ALL FIELDS FOR NEW STOP
		function clear_all_fields_for_new_stop()
		{
			$("#stop_type_row").hide();
			$("#date_row").hide();
			$("#time_row").hide();
			$("#odometer_row").hide();
			$("#location_row").hide();
			$("#city_row").hide();
			$("#state_row").hide();
			$("#address_row").hide();
			$("#notes_row").hide();
			$("#load_id_row").hide();
			$("#fill_or_partial_row").hide();
			$("#gallons_row").hide();
			$("#invoice_amount_row").hide();
			
			$("#date").val("");
			$("#time").val("");
			$("#location").val("");
			$("#city").val("");
			$("#state").val("");
			$("#address").val("");
			$("#notes").val("");
			$("#load_id").val("");
			$("#fill_or_partial").val("Select");
			$("#gallons").val("");
			$("#invoice_amount").val("");
		}
		
		//VALIDATE ADD STOP FORM
		function validate_add_stop_form()
		{	
			var isValid = true;
			
			//VALIDATE FOR ALL THE COMMON STOP FEILDS
			if($("#client_dropdown").val() == "Select Client")
			{
				alert("Client must be selected!")
				isValid = false;
				return;
			}
			
			if($("#stop_type_dropdown").val() == "Select Stop")
			{
				alert("Stop Type must be selected!")
				isValid = false;
				return;
			}
			
			if(!isDate($("#date").val()))
			{
				alert("Date must be entered correctly!")
				isValid = false;
			}
			
			if(!isTime($("#time").val()))
			{
				alert("Time must be entered correctly!")
				isValid = false;
			}
			
			if(!$("#odometer").val() || isNaN($("#odometer").val()))
			{
				alert("Odometer must be entered as a number!")
				isValid = false;
			}
				
			if(!$("#location").val())
			{
				alert("Location must be entered!")
				isValid = false;
			}
			
			if(!$("#city").val())
			{
				alert("City must be entered!")
				isValid = false;
			}
			
			if(!$("#state").val())
			{
				alert("State must be entered!")
				isValid = false;
			}
			
			if(!$("#address").val())
			{
				alert("Address must be entered!")
				isValid = false;
			}
			
			//CHECK THE FEILDS FOR EACH DIFFERENT STOP
		
			if($("#stop_type_dropdown").val() == "Pick" || $("#stop_type_dropdown").val() == "Drop")
			{
				
				if(!$("#load_id").val())
				{
					alert("Load # must be entered!")
					isValid = false;
				}
				
			}
			else if($("#stop_type_dropdown").val() == "Fuel Stop")
			{
				$("#fill_or_partial_row").show();
				$("#gallons_row").show();
				$("#invoice_amount_row").show();
				
				if($("#fill_or_partial").val() == "Select")
				{
					alert("Fill or Partial must be selected!")
					isValid = false;
				}
				
				if(!$("#gallons").val() || isNaN($("#gallons").val()))
				{
					alert("Gallons must be entered as a number!")
					isValid = false;
				}
				
				if(!$("#invoice_amount").val() || isNaN($("#invoice_amount").val()))
				{
					alert("Invoice Amount must be entered as a number!")
					isValid = false;
				}
			}
			
			if(isValid)
			{
				$("#new_stop").submit();
			}
		}
		
		</script>

		<title><?php echo $title;?></title>
	</head>
	
	<?php $attributes = array('id' => 'delete_stop'); ?>
	<?=form_open('driver_logs/delete_stop',$attributes)?>
	<input type="hidden" id="client_id" name="client_id" value="<?=$this_client["id"]?>"/>
	<input type="hidden" id="stop_id" name="stop_id"/>
	</form>
	
	<?php $form_attributes = array('name'=>'add_stop_note','id'=>'add_stop_note', )?>
	<?=form_open('driver_logs/add_stop_note/',$form_attributes);?>
		<input type="hidden" name="client_id" id="client_id" value="<?=$client_id?>" />
		<input type="hidden" name="note_text" id="note_text" value="" />
		<input type="hidden" name="notes_stop_id" id="notes_stop_id" value="" />
	</form>
	
	<body id="body">
	
		<?php include("main_menu.php"); ?>
		
		<div id="main_div">
			<div id="space_header">
			</div>
			
			<div id="left_bar">
				<button class='left_bar_button jq_button' id="new_log_entry">New Log Entry</button>
				<br>
				<br>
				<span class="heading">Search</span>
				<hr/>
				<input type="text" id="left_bar_search" name="left_bar_search"  class="left_bar_input"/>
				<br>
				<br>
				<br>
				<span class="heading">Clients</span>
				<hr/>
				<?php $attributes = array('name'=>'driver_type_form','ID'=>'driver_type_form' )?>
				<!-- //OPEN FORM HERE !-->
					<?php $options = array(
						'All' => 'All',
						'Active'  => 'Active',
						'Inactive'    => 'Inactive',
						'just_my_guys'    => 'Just My Guys',
						); 
					?>
					<?php echo form_dropdown('driver_type_dropdown',$options,"just_my_guys",'onChange="submit()" class="left_bar_input"');?>
				<!-- </form> !-->
				<br>
				<br>
				<?php foreach ($all_clients as $client): ?>
					
					<?php $selected = ""; ?>
					<?php if ($client['id'] == $this_client["id"]): ?>
					<?php $selected = " color:#DD4B39; font-weight:bold;"//background: #DCDCDC;" ?>
					<?php endif ?>
					
					<div class="left_bar_link_div" style=" <?=$selected?> " onclick="location.href='<?= base_url("index.php/driver_logs/index/".$client['id']);?>'">
						<?=$client["company"]["company_side_bar_name"]?>
					</div>
				<?php endforeach; ?>
				
				
			</div>
			
			<?php if ($this_client["id"] != 0):?>
				<div id="main_content">
					<div id="main_content_header">
						<span style="font-weight:bold;"><?="Driver Log | ".$this_client["company"]["company_name"] ?></span>
						<button class='jq_button_disabled' style="float:right;  width:80px;" id="save_edits">Save</button>
						<button class='jq_button_disabled' style="float:right;  width:80px; margin-right:10px;" id="cancel_edits">Cancel</button>
					</div>
					<div id="scrollable_content" style="overflow:auto;">
						<table style="table-layout:fixed; margin:5px;">
							<tr class="heading" style="line-height:30px; font-size:12px;">
								<td style="max-width:100px; min-width:100px;">
									Load #
								</td>
								<td style="width:100px;">
									Stop
								</td>
								<td style="width:65px;">
									Date
								</td>
								<td style="width:50px;">
									Time
								</td>
								<td style="min-width:120px; max-width:120px;">
									City, State
								</td>
								<td style="width:110px;">
									Location
								</td>
								<td style="width:130px;">
									Address
								</td>
								<td style="width:60px; text-align:right;">
									Odometer
								</td>
								<td style="width:60px; text-align:right;">
									Gallons
								</td>
								<td style="width:70px; text-align:right;">
									Invoice
								</td>
								<td style="width:60px; text-align:right;">
									MPG
								</td>
								<td style="width:40px; padding-left:10px;">
									Notes
								</td>
							</tr>
							<?php $attributes = array('id' => 'save_edit'); ?>
							<?=form_open('driver_logs/save_edit',$attributes)?>
							<input type="hidden" id="client_id" name="client_id" value="<?=$this_client["id"]?>" />
							<?php $i=0?>
							
							<?php foreach($all_stops as $stop): ?>
								<?php $class='' ?>
								<?php if($i % 2 == 0){$class='alt_row';}; ?>
								<?php $i++; ?>
								<tr id="view_row_<?=$stop["id"]?>" style="font-size:12px;" class="<?=$class?>">
									<?php
										$pick_drop = null;
										$pd_number = null;
										if ($stop["stop_type"] == "Pick")
										{
											$pick_where["stop_id"] = $stop["id"];
											$pick_drop = db_select_pick($pick_where);
											$load_id = $pick_drop["load_id"];
										}
										if ($stop["stop_type"] == "Drop")
										{
											$drop_where["stop_id"] = $stop["id"];
											$pick_drop = db_select_drop($drop_where);
											$load_id = $pick_drop["load_id"];
										}
										$where = null;
										$where["id"] = $load_id;
										$load = db_select_load($where);
										$pd_number = $load["customer_load_number"];
										
									?>
									<td style="padding-top:5px; padding-bottom:5px;" oncontextmenu="view_row_clicked('<?=$stop["id"]?>');return false;">
										<div style="width: 100px;">
											<?=$pd_number?>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;" oncontextmenu="view_row_clicked('<?=$stop["id"]?>');return false;">
										<div style="width: 100px;">
											<?=$stop["stop_type"]?>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;" oncontextmenu="view_row_clicked('<?=$stop["id"]?>');return false;">
										<div style="width: 65px;">
											<?=date("m/d/y",strtotime($stop["stop_datetime"]))?>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;" oncontextmenu="view_row_clicked('<?=$stop["id"]?>');return false;">
										<div style="width: 50px;">
											<?=date("H:i",strtotime($stop["stop_datetime"]))?>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;" oncontextmenu="view_row_clicked('<?=$stop["id"]?>');return false;">
										<div style="width: 120px;">
											<?=$stop["city"].", ".$stop["state"]?>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;" oncontextmenu="view_row_clicked('<?=$stop["id"]?>');return false;">
										<div style="width: 120px;">
											<?=$stop["location_name"]?>
										<div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;" oncontextmenu="view_row_clicked('<?=$stop["id"]?>');return false;">
										<div style="width: 130px;">
											<?=$stop["address"]?>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px; text-align:right;" oncontextmenu="view_row_clicked('<?=$stop["id"]?>');return false;">
										<div style="width: 60px;">
											<?=$stop["odometer"]?>
										</div>
									</td>
									
									<?php 
										$stop_id = $stop["id"];
										$fuel_stop = null;
										if ($stop["stop_type"] == "Fuel - Fill" || $stop["stop_type"] == "Fuel - Partial")
										{
											$fuel_stop_where["stop_id"] = $stop["id"];
											$fuel_stop = db_select_fuel_stop($fuel_stop_where);
										}
									?>
									<td style="padding-top:5px; padding-bottom:5px;text-align:right;" oncontextmenu="view_row_clicked('<?=$stop["id"]?>');return false;">
										<div style="width: 60px;">
											<?=@$fuel_stop["gallons"]?>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;text-align:right;" oncontextmenu="view_row_clicked('<?=$stop["id"]?>');return false;">
										<div style="width: 70px;">
											<?php if(!empty($fuel_stop["invoice_amount"])): ?>
												$<?=@number_format($fuel_stop["invoice_amount"],2)?>
											<?php endif; ?>
										</div>
									</td>
									<?php 
										if ($fuel_mpg["$stop_id"] < 5.8)
										{
											$mpg_style ="color: red;";
										}
										else if ($fuel_mpg["$stop_id"] > 6.5)
										{
											$mpg_style ="color: green;";
										}
										else
										{
											$mpg_style ="color: yellow;";
										}
										
										//DETERMINE NOTES IMAGE
										if(empty($stop["notes"]))
										{
											$notes_img = "/images/add_notes_empty.png";
										}
										else
										{
											$notes_img = "/images/add_notes.png";
										}
									?>
									<td style="padding-top:5px; padding-bottom:5px; text-align:right; font-weight:bold; <?=$mpg_style?>" oncontextmenu="view_row_clicked('<?=$stop["id"]?>');return false;">
										<div style="width: 40px; float:right; ">
											<?php if (!empty($fuel_mpg["$stop_id"])): ?>
												<?=@number_format($fuel_mpg["$stop_id"],1)?>
											<?php endif; ?>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;padding-left:8px;">
										<div style="width: 40px;">
											<img title="<?=$stop["notes"]?>" oncontextmenu="view_row_clicked('<?=$stop["id"]?>');return false;" style=" float:right; margin-right:7px; cursor:pointer; position:relative; left:2px; height:16px; width:16px" src="<?=$notes_img?>" />
										</div>
									</td>
								</tr>
								
								
								
								<tr id="edit_row_<?=$stop["id"]?>" style="font-size: 12px; display:none;" class="<?=$class?>">
									<?php
										$pick_drop = null;
										if ($stop["stop_type"] == "Pick")
										{
											$pick_where["stop_id"] = $stop["id"];
											$pick_drop = db_select_pick($pick_where);
										}
										if ($stop["stop_type"] == "Drop")
										{
											$drop_where["stop_id"] = $stop["id"];
											$pick_drop = db_select_drop($drop_where);
										}
										$stop_id = $stop["id"];
									?>
									<input type="hidden" id="should_update_<?=$stop["id"]?>" name="should_update_<?=$stop["id"]?>" value="NO" />
									<td style="padding-top:5px; padding-bottom:5px;">
										<div style="width: 100px;">
											<?php if(!($stop["stop_type"] == "Pick" || $stop["stop_type"] == "Drop")): ?>
												<img title="Delete" onclick="delete_stop('<?=$stop["id"]?>')" style="cursor:pointer;" src="/images/delete_icon.png"/>
											<?php endif; ?>
											<?php if ($stop["stop_type"] == "Pick" || $stop["stop_type"] == "Drop"): ?>
												<span style="position:relative;"><?=$pd_number?></span>
											<?php endif; ?>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;">
										<?php if ($stop["stop_type"] == "Fuel - Fill" || $stop["stop_type"] == "Fuel - Partial"): ?>
											<?php $options = array(
													'Fill'    => 'Fuel - Fill',
													'Partial'    => 'Fuel - Partial',
											); ?>
											<div style="width: 100px;">
												<?php echo form_dropdown('stop_type_'.$stop["id"],$options,substr($stop['stop_type'],7),"style='width:95px; font-size:10px;' id='stop_type_$stop_id'"); ?>
											</div>
										<?php else:?>
											<div style="width: 90px;">
												<?= $stop["stop_type"]; ?>
											</div>
										<?php endif; ?>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;">
										<div style="width: 65px;">
											<input style="width:55px; font-size:10px;" type="text" id="date_<?=$stop["id"]?>" name="date_<?=$stop["id"]?>" value="<?=date("m/d/y",strtotime($stop["stop_datetime"]))?>"></input>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;">
										<div style="width: 50;">
											<input style="width:46px; font-size:10px;" type="text" id="time_<?=$stop["id"]?>" name="time_<?=$stop["id"]?>" value="<?=date("H:i",strtotime($stop["stop_datetime"]))?>"></input>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;">
										<div style="width: 120px;">
											<input style="width:85px; font-size:10px;" type="text" id="city_<?=$stop["id"]?>" name="city_<?=$stop["id"]?>" value="<?=$stop["city"]?>"></input>
											<input style="width:25px; font-size:10px;" type="text" id="state_<?=$stop["id"]?>" name="state_<?=$stop["id"]?>" value="<?=$stop["state"]?>"></input>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;">
										<div style="width: 120;">
											<input style="width:116px; font-size:10px;" type="text" id="location_<?=$stop["id"]?>" name="location_<?=$stop["id"]?>" value="<?=$stop["location_name"]?>"></input>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;">
										<div style="width: 130px;">
											<input	 style="width:106px; font-size:10px;" type="text" id="address_<?=$stop["id"]?>" name="address_<?=$stop["id"]?>" value="<?=$stop["address"]?>"></input>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px; text-align:right;">
										<div style="width: 60px;">
											<input style="width:55px; text-align:right; font-size:10px;" type="text" id="odometer_<?=$stop["id"]?>" name="odometer_<?=$stop["id"]?>" value="<?=$stop["odometer"]?>"></input>
										</div>
									</td>
									
									<?php 
										$fuel_stop = null;
										if ($stop["stop_type"] == "Fuel - Fill" || $stop["stop_type"] == "Fuel - Partial")
										{
											$fuel_stop_where["stop_id"] = $stop["id"];
											$fuel_stop = db_select_fuel_stop($fuel_stop_where);
										}
									?>
									<td style="padding-top:5px; padding-bottom:5px;text-align:right;">
										<?php if ($stop["stop_type"] == "Fuel - Fill" || $stop["stop_type"] == "Fuel - Partial"): ?>
											<div style="width: 60px;">
												<input style="width:55px;text-align:right; font-size:10px;" type="text" id="gallons_<?=$stop["id"]?>" name="gallons_<?=$stop["id"]?>" value="<?=@$fuel_stop["gallons"]?>"></input>
											</div>
										<?php endif; ?>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;text-align:right;">
										<div style="width: 70px;">
											<?php if ($stop["stop_type"] == "Fuel - Fill" || $stop["stop_type"] == "Fuel - Partial"): ?>
												<input style="width:65px;text-align:right; font-size:10px;" type="text" id="invoice_<?=$stop["id"]?>" name="invoice_<?=$stop["id"]?>" value="<?=@$fuel_stop["invoice_amount"]?>"></input>
											<?php endif; ?>
										</div>
									</td>
									<?php 
										if ($fuel_mpg["$stop_id"] < 5.8)
										{
											$mpg_style ="color: red;";
										}
										else if ($fuel_mpg["$stop_id"] > 6.5)
										{
											$mpg_style ="color: green;";
										}
										else
										{
											$mpg_style ="color: yellow;";
										}
										
										//DETERMINE NOTES IMAGE
										if(empty($stop["notes"]))
										{
											$notes_img = "/images/add_notes_empty.png";
										}
										else
										{
											$notes_img = "/images/add_notes.png";
										}
									?>
									<td style="padding-top:5px; padding-bottom:5px; text-align:right; font-weight:bold; <?=$mpg_style?>">
										<div style="width: 40px; float:right; ">
											<?php if (!empty($fuel_mpg["$stop_id"])): ?>
												<?=@number_format($fuel_mpg["$stop_id"],1)?>
											<?php endif; ?>
										</div>
									</td>
									<td style="padding-top:5px; padding-bottom:5px;padding-left:8px;">
										<div style="width: 40px;">
											<img title="<?=$stop["notes"]?>" onclick="open_add_notes('<?=$stop["id"]?>')" style=" float:right; margin-right:7px; cursor:pointer; position:relative; left:2px; height:16px; width:16px" src="<?=$notes_img?>" />
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
							</form>
						</table>
					</div>
				</div>
			<?php endif;?>
			
		</div>
		
	</body>
	
	<div id="add_stop_dialog" style="display: none; margin:0 auto;">
		<?php $attributes = array('name'=>'new_stop','ID'=>'new_stop' )?>
		<?=form_open('driver_logs/create_new_stop',$attributes);?>
		<input type="hidden" id="client_id" name="client_id" value="<?=$this_client["id"]?>" />
		<table style="margin-left:15px;">
			<tr style="line-height:30px;">
				<td style="width:160px">Client</td>
				<td style="width:180px">
					<?php echo form_dropdown('client_dropdown',$client_options,0,'id="client_dropdown" onchange="client_selected()" style="width:156px;"');?>
				</td>
			</tr>
			<tr id="stop_type_row" style="height:30px; display:none;">
				<td>Stop Type</td>
				<td>
					<?php $options = array(
							'Select Stop' => 'Select Stop',
							'Fuel Stop'    => 'Fuel Stop',
							'Checkpoint'    => 'Checkpoint',
					); ?>
					<?php echo form_dropdown('stop_type_dropdown',$options,$this_client["id"],'id="stop_type_dropdown" onchange="stop_selected()" style="width:156px;"');?>
				</td>
			</tr>
			<tr id="date_row" style="height:30px; display:none;">
				<td>Date</td>
				<td>
					<input type="text" id="date" name="date" style="width:156px;">
				</td>
			</tr>
			<tr id="time_row" style="height:30px; display:none;">
				<td>Time</td>
				<td>
					<input type="text" id="time" name="time" style="width:156px;">
				</td>
			</tr>
			<tr id="odometer_row" style="height:30px; display:none;">
				<td>Odometer</td>
				<td>
					<input type="text" id="odometer" name="odometer" style="width:156px;">
				</td>
			</tr>
			<tr id="location_row" style="height:30px; display:none;">
				<td>Location Name</td>
				<td>
					<input type="text" id="location" name="location" style="width:156px;">
				</td>
			</tr>
			<tr id="city_row" style="height:30px; display:none;">
				<td>City</td>
				<td>
					<input type="text" id="city" name="city" style="width:156px;">
				</td>
			</tr>
			<tr id="state_row" style="height:30px; display:none;">
				<td>State</td>
				<td>
					<input type="text" id="state" name="state" style="width:156px;">
				</td>
			</tr>
			<tr id="address_row" style="height:30px; display:none;">
				<td style="vertical-align:top;">Address</td>
				<td>
					<textarea id="address" name="address" rows="2" style="width:156px;"></textarea>
				</td>
			</tr>
			<tr id="fill_or_partial_row" style="height:30px; display:none;">
				<td>Fill or Partial?</td>
				<td>
					<?php $options = array(
							'Select' => 'Select',
							'Fill' => 'Fill',
							'Partial'  => 'Partial',
					); ?>
					<?php echo form_dropdown('fill_or_partial',$options,"Select",'id="fill_or_partial" style="width:156px;"');?>
				</td>
			</tr>
			<tr id="gallons_row" style="height:30px; display:none;">
				<td>Gallons</td>
				<td>
					<input type="text" id="gallons" name="gallons" style="width:156px;">
				</td>
			</tr>
			<tr id="invoice_amount_row" style="height:30px; display:none;">
				<td>Invoice Amount</td>
				<td>
					<input type="text" id="invoice_amount" name="invoice_amount" style="width:156px;">
				</td>
			</tr>
			<tr id="load_id_row" style="height:30px; display:none;">
				<td>Load #</td>
				<td>
					<input type="text" id="load_id" name="load_id" style="width:156px;">
				</td>
			</tr>
			<tr id="notes_row" style="height:30px; display:none;">
				<td style="vertical-align:top;">Notes</td>
				<td>
					<textarea id="notes" name="notes" rows="2" style="width:156px;"></textarea>
				</td>
			</tr>
		</table>
	</div>
	
	
	
	<?php foreach($all_stops as $stop): ?>
		<?php
			$pick_drop = null;
			$pd_number = null;
			if ($stop["stop_type"] == "Pick")
			{
				$pick_where["stop_id"] = $stop["id"];
				$pick_drop = db_select_pick($pick_where);
				$pd_number = $pick_drop["pick_number"];
			}
			if ($stop["stop_type"] == "Drop")
			{
				$drop_where["stop_id"] = $stop["id"];
				$pick_drop = db_select_drop($drop_where);
				$pd_number = $pick_drop["drop_number"];
			}
			
		?> 
			<div id="add_stop_notes_<?=$stop["id"]?>" title="<?=$stop["stop_type"]?> <?=date("m/d/y",strtotime($stop["stop_datetime"]))?> <?=$stop["city"]?> - Notes" style="display:none; padding:10px;" >
				<div style="height:230px; overflow:auto">
					<?=str_replace("\n","<br>",$stop["notes"]);?>
				</div>
				<div style="position:absolute; bottom:0">
					Add Note:
					<textarea style="width:400px;" rows="3" id="new_note_<?=$stop["id"]?>" name="new_note_<?=$stop["id"]?>"></textarea>
				</div>
			</div>
	<?php endforeach; ?>
</html>