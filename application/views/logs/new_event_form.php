<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
<style>
	table#form td
	{
		vertical-align:middle;
	}
</style>
<script>
	
	function event_type_selected()
	{
		$("#load_number_row").show();
		show_more_fields($("#event_type").val());
		
		$("#card_type_row").hide();
		$("#red_star").hide();
		$("#check_point_img").hide();
		$("#check_call_img").hide();
		$("#driver_in_img").hide();
		$("#driver_out_img").hide();
		$("#pick_trailer_img").hide();
		$("#drop_trailer_img").hide();
		$("#shift_report_img").hide();
		$("#dry_service_img").hide();
		$("#wet_service_img").hide();
		$("#shift_report_img").hide();
		$("#fuel_report_img").hide();
		$("#fuel_stop_div").hide();
		$("#end_week_img").hide();
		
		$("#fuel_report_div").hide();
		
		var entry_type = $("#event_type").val();
		if(entry_type == "Checkpoint")
		{
			$("#check_point_img").show();
		}
		else if(entry_type == "Driver In")
		{
			$("#driver_in_img").show();
		}
		else if(entry_type == "Driver Out")
		{
			$("#driver_out_img").show();
		}
		else if(entry_type == "Pick Trailer")
		{
			$("#pick_trailer_img").show();
		}
		else if(entry_type == "Drop Trailer")
		{
			$("#drop_trailer_img").show();
		}
		else if(entry_type == "Check Call")
		{
			$("#check_call_img").show();
			
			$("#load_number_row").hide();
			$("#trailer_row").hide();
			$("#date_row").hide();
			$("#time_row").hide();
			$("#address_row").hide();
			$("#city_row").hide();
			$("#state_row").hide();
			$("#odometer_row").hide();
			$("#notes_row").hide();
		}
		else if(entry_type == "Fuel Stop")
		{
			$("#fuel_report_img").show();
			
			$("#fuel_stop_div").show();
		}
		else if(entry_type == "Wet Service")
		{
			$("#dry_service_img").show();
		}
		else if(entry_type == "Dry Service")
		{
			$("#wet_service_img").show();
		}
		else if(entry_type == "Shift Report")
		{
			$("#shift_report_img").show();
		}
		else if(entry_type == "End Week")
		{
			$("#end_week_img").show();
		}
		else if(entry_type == "Upload Fuel")
		{
			$("#load_number_row").hide();
			$("#main_driver_row").hide();
			$("#codriver_row").hide();
			$("#truck_row").hide();
			$("#trailer_row").hide();
			$("#date_row").hide();
			$("#time_row").hide();
			$("#address_row").hide();
			$("#city_row").hide();
			$("#state_row").hide();
			$("#odometer_row").hide();
			$("#notes_row").hide();
			
			$("#fuel_report_img").show();
			$("#card_type_row").show();
			
		}
		else
		{
			$("#red_star").show();
		}
		
	}
	
	function card_type_selected()
	{
		var card_type  = $("#card_type").val();
		
		$("#fuel_report_div").hide();
		
		if(card_type == 'Comdata')
		{
			$("#card_type_hidden").val('Comdata');
			$("#fuel_report_div").show();
		}
		else if(card_type == 'Ultimate')
		{
			$("#card_type_hidden").val('Ultimate');
			$("#fuel_report_div").show();
		}
		else if(card_type == 'EFS')
		{
			$("#card_type_hidden").val('EFS');
			$("#fuel_report_div").show();
		}
	}
	
	function load_number_entered()
	{
		
		
		if($("#load_number").val())
		{
		
			var dataString = "";
			
			dataString = dataString+"&load_number="+$("#load_number").val();
			
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#load_error_div');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/check_load")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						$("#dead_head_row").hide();
						$("#dead_head_cb").attr("checked",false);
						this_div.html(response);
						this_div.show();
						$("#main_driver_id").focus();
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
			$("#dead_head_cb").attr("checked",false);
			$("#dead_head_row").show();
			var this_div = $('#load_error_div');
			var response = "<span style='font-weight:bold; color:red;'>Not Found</span>";
			this_div.html(response);
			$("#main_driver_id").focus();
		}
	}
	
	//SHOWS REMAINING FIELDS AFTER LOAD NUMBER IS ENTERED
	function show_more_fields(event_type)
	{
		$("#main_driver_row").show();
		$("#codriver_row").show();
		$("#truck_row").show();
		$("#trailer_row").show();
		$("#date_row").show();
		$("#time_row").show();
		$("#address_row").show();
		$("#city_row").show();
		$("#state_row").show();
		$("#odometer_row").show();
		$("#notes_row").show();
	}
	
	//DEAD HEAD CHECK BOX CLICKED
	function dead_head_clicked()
	{
		if($("#dead_head_cb").is(":checked"))
		{
			//alert("checked");
			$('#load_error_div').html("<span style='font-weight:bold; color:red;'>*</span>");
			
		}
		else
		{
			//alert("not checked");
			$('#load_error_div').html("<span style='font-weight:bold; color:red;'>Not Found</span>");
		}
	}
	
</script>
<br>
<?php $attributes = array('name'=>'new_event_form','id'=>'new_event_form', )?>
<?=form_open('loads/create_new_event',$attributes);?>
	<table id="form" style="margin-left:10px;">
		<tr style="height:30px;">
			<td  style="width: 100px;">Event Type</td>
			<td>
				<?php
					$options = array
					(
						"Select" => "Select",
						"Shift Report" => "Shift Report",
						"Check Call" => "Check Call",
						"Checkpoint" => "Checkpoint",
						"Driver In" => "Driver In",
						"Driver Out" => "Driver Out",
						"Pick Trailer" => "Pick Trailer",
						"Drop Trailer" => "Drop Trailer",
						"Dry Service" => "Dry Service",
						"Wet Service" => "Wet Service",
						"End Week" => "End Week",
						"Upload Fuel" => "Upload Fuel",
					);
				?>
				<?php echo form_dropdown('event_type',$options,"Select","id='event_type' style='font-size:12px; height:21px; float:right; width:150px;' onchange='event_type_selected()' ");?>
			</td>
			<td style="padding-left:5px; color:red;" >
				<div id="red_star">*</div>
				<div id="check_point_img" style="display:none;"><img style='height:16px; position:relative; bottom:2px; margin-left:5px; margin-right:5px;' src="/images/log_checkpoint.png"/></div>
				
				<div id="driver_in_img" style="display:none;"><img style='height:18px; position:relative; bottom:2px; margin-left:5px; margin-right:5px;' src="/images/driver_in.png"/></div>
				<div id="driver_out_img" style="display:none;"><img style='height:18px; position:relative; bottom:2px; margin-left:5px; margin-right:5px;' src="/images/driver_out.png"/></div>
				<div id="pick_trailer_img" style="display:none;"><img style='height:15px; position:relative; bottom:2px; margin-left:5px; margin-right:5px;' src="/images/pick_trailer.png"/></div>
				<div id="drop_trailer_img" style="display:none;"><img style='height:15px; position:relative; bottom:2px; margin-left:5px; margin-right:5px;' src="/images/drop_trailer.png"/></div>
				
				<div id="check_call_img" style="display:none;"><img style='height:18px; position:relative;  bottom:2px; margin-left:5px; margin-right:5px;' src="/images/log_check_call.png"/></div>
				<div id="dry_service_img" style="display:none;"><img style='height:20px; position:relative; bottom:2px; margin-left:5px; margin-right:5px;' src="/images/log_service.png"/></div>
				<div id="wet_service_img" style="display:none;"><img style='height:20px; position:relative; bottom:2px; margin-left:5px; margin-right:5px;' src="/images/log_service.png"/></div>
				<div id="shift_report_img" style="display:none;"><img style='height:15px; position:relative; bottom:1px; margin-left:5px; margin-right:5px;' src="/images/log_shift_report.png"/></div>
				<div id="fuel_report_img" style="display:none;"><img style='height:17px; position:relative; top:0px; margin-left:5px; margin-right:5px;' src="/images/log_fuel_fill.png"/></div>
				<div id="end_week_img" style="display:none;"><img style='height:17px; position:relative; top:0px; margin-left:5px; margin-right:5px;' src="/images/end_week.png"/></div>
			</td>
		</tr>
		<tr id="card_type_row" style="height:30px;">
			<td  style="width: 100px;">Card Type</td>
			<td>
				<?php
					$options = array
					(
						"Select" => "Select",
						"Comdata" => "Comdata",
						"Ultimate" => "Ultimate Platinum",
						"EFS" => "EFS",
					);
				?>
				<?php echo form_dropdown('card_type',$options,"Select","id='card_type' style='font-size:12px; height:21px; float:right; width:150px;' onchange='card_type_selected()'");?>
			</td>
		</tr>
		<tr id="load_number_row" style="height:30px;">
			<td>
				Load Number
			</td>
			<td>
				<input type="text" id="load_number" name="load_number" style="float:right; width:150px;" onblur="load_number_entered()"/>
				<input type="hidden" id="load_number_is_valid" name="load_number_is_valid" value="false"/>
			</td>
			<td id="load_error_td" style="padding-left:5px;">
				<div id="load_error_div">
					<!-- AJAX GOES HERE !-->
				</div>
			</td>
		</tr>
		<tr id="dead_head_row" style="display:none; height:30px;">
			<td>
				No Load
			</td>
			<td>
				<input id="dead_head_cb" name="dead_head_cb" type="checkbox" onclick="dead_head_clicked()">
			</td>
		</tr>
		<tr id="main_driver_row" style="height:30px;">
			<td  style="width: 100px;">Main Driver</td>
			<td>
				<?php echo form_dropdown('main_driver_id',$main_driver_dropdown_options,"Select","id='main_driver_id' style='font-size:12px; height:21px; float:right; width:150px;'");?>
			</td>
			<td style="padding-left:5px; color:red;" >
				*
			</td>
		</tr>
		<tr id="codriver_row" style="height:30px;">
			<td  style="width: 100px;">Co-Driver</td>
			<td>
				<?php echo form_dropdown('codriver_id',$codriver_dropdown_options,"Select","id='codriver_id' style='font-size:12px; height:21px; float:right; width:150px;'");?>
			</td>
			<td style="padding-left:5px; color:red;" >
				*
			</td>
		</tr>
		<tr id="truck_row" style="height:30px;">
			<td  style="width: 100px;">Truck</td>
			<td>
				<?php echo form_dropdown('truck_id',$truck_dropdown_options,"Select","id='truck_id' style='font-size:12px; height:21px; float:right; width:150px;'");?>
			</td>
			<td style="padding-left:5px; color:red;" >
				*
			</td>
		</tr>
		<tr id="trailer_row" style="height:30px;">
			<td  style="width: 100px;">Trailer</td>
			<td>
				<?php echo form_dropdown('trailer_id',$trailer_dropdown_options,"Select","id='trailer_id' style='font-size:12px; height:21px; float:right; width:150px;'");?>
			</td>
			<td style="padding-left:5px; color:red;" >
				*
			</td>
		</tr>
		<tr id="date_row" style="height:30px;">
			<td>
				Date
			</td>
			<td>
				<input type="text" id="date" name="date" style="float:right; width:150px;" onchange=""/>
			</td>
			<td style="padding-left:5px; color:red;" >
				*
			</td>
		</tr>
		<tr id="time_row" style="height:30px;">
			<td>
				Time
			</td>
			<td>
				<input type="text" id="time" name="time" style="float:right; width:150px;"/>
			</td>
			<td style="padding-left:5px; color:red;" >
				*
			</td>
		</tr>
		<tr id="address_row" style="height:30px;">
			<td>
				Address
			</td>
			<td>
				<input type="text" id="address" name="address" style="float:right; width:150px;" onblur="auto_fill_new_event_city_state_from_gps_coordinates()"/>
			</td>
			<td style="padding-left:5px; color:red;" >
				*
			</td>
		</tr>
		<tr id="city_row" style="height:30px;">
			<td>
				City
			</td>
			<td>
				<input type="text" id="city" name="city" style="float:right; width:150px;"/>
			</td>
			<td style="padding-left:5px; color:red;" >
				*
			</td>
		</tr>
		<tr id="state_row" style="height:30px;">
			<td>
				State
			</td>
			<td>
				<input type="text" id="state" name="state" style="float:right; width:150px;"/>
			</td>
			<td style="padding-left:5px; color:red;" >
				*
			</td>
		</tr>
		<tr id="odometer_row" style="height:30px;">
			<td>
				Odometer
			</td>
			<td>
				<input type="text" id="odometer" name="odometer" style="float:right; width:150px;"/>
			</td>
			<td style="padding-left:5px; color:red;" >
				*
			</td>
		</tr>
		<tr id="notes_row" style="height:30px;">
			<td>
				Notes
			</td>
			<td>
				<textarea  id="notes" rows="3" style="float:right; width:150px;"></textarea>
			</td>
		</tr>
	</table>
	<div id="fuel_stop_div" style="display:none;">
		<br>
		<br>
		<div class="heading" style="margin-left:10px;">
			Fuel Stop
			<hr style="width:255px;">
		</div>
		<table id="form" style="margin-left:10px;">
			<tr style="height:30px;">
				<td  style="width: 100px;">Is Fill?</td>
				<td>
					<?php
						$options = array
						(
							"Select" => "Select",
							"Yes" => "Yes",
							"No" => "No",
						);
					?>
					<?php echo form_dropdown('is_fill',$options,"Select","id='is_fill' style='font-size:12px; height:21px; float:right; width:150px;' ");?>
				</td>
			</tr>
			<tr style="height:30px;">
				<td  style="width: 100px;">Source</td>
				<td>
					<?php
						$options = array
						(
							"Select" => "Select",
							"Estimate" => "Estimate",
						);
					?>
					<?php echo form_dropdown('source',$options,"Select","id='source' style='font-size:12px; height:21px; float:right; width:150px;' ");?>
				</td>
			</tr>
			<tr id="gallons_row" style="height:30px;">
				<td>
					Gallons
				</td>
				<td>
					<input type="text" id="gallons" name="gallons" style="float:right; width:150px;"/>
				</td>
			</tr>
			<tr id="gallons_row" style="height:30px;">
				<td>
					Fuel Price
				</td>
				<td>
					<input type="text" id="fuel_price" name="fuel_price" style="float:right; width:150px;"/>
				</td>
			</tr>
			<tr id="gallons_row" style="height:30px;">
				<td>
					Fuel Expense
				</td>
				<td>
					<input type="text" id="fuel_expense" name="fuel_expense" style="float:right; width:150px;"/>
				</td>
			</tr>
		</table>
	</div>
</form>
<div id="fuel_report_div" name="fuel_report_div" style="display:none;">
	<?php $attributes = array('name'=>'fuel_upload_form','id'=>'fuel_upload_form', )?>
	<?php echo form_open_multipart('logs/upload_fuel_report',$attributes);?>
		<input type="hidden" id="card_type_hidden" name="card_type_hidden" />
		<div style="margin-left:8px; margin-top:25px;">
			<input type="file" name="userfile" class="" />
			<button onclick="" style="width:95px;" class="jq_button">Upload</button>
		</div>
	</form>
</div>

<script>
	$('#date').datepicker({ showAnim: 'blind' });
</script>