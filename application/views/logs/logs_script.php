<script>
	$(document).ready(function()
	{
		$("#main_content").height($(window).height() - 115);
		$("#filter_list").height($(window).height() - 213);
		//$("#scrollable_content").height($(window).height() - 155);
				
		load_log_list();
	
		//HANDLE BUTTONS
		$("#log_entry").click(function()
		{
			 load_new_event_dialog();
		});
	
		$('#start_date_filter').datepicker({ showAnim: 'blind' });
		$('#end_date_filter').datepicker({ showAnim: 'blind' });
		
		//DIALOG: UPLOAD SIGNATURE DIALOG
		$( "#file_upload_dialog" ).dialog(
		{
			autoOpen: false,
			height: 300,
			width: 400,
			modal: true,
			buttons: 
			[
				{
					text: "Upload",
					click: function() 
					{
						var attach_le_id = $("#attachment_log_entry_id").val();
						//alert(attach_le_id);
					
						//SUBMIT FORM
						$("#upload_file_form").submit();
						$( this ).dialog( "close" );
						setTimeout(function()
						{
							//alert('delay');
							open_event_details(attach_le_id);
						},1000);
					},//end add load
				},
				{
					text: "Cancel",
					click: function() 
					{
						//RESIZE DIALOG BOX
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
		
		//DIALOG: ADD NEW EVENT
		$( "#log_event_dialog" ).dialog(
		{
				autoOpen: false,
				height: 550,
				width: 350,
				modal: true,
				buttons: 
					[
						{
							text: "Save",
							click: function() 
							{
								validate_new_event();
							
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
						$('#log_event_dialog').html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:160px; margin-top:200px; height:25px;" />');
					},//end open function
				close: function() 
					{
						//$('#log_event_dialog').html("");
					}
		});//end dialog form
		
	});
	
	//LOAD LOG LIST
	var load_log_list_ajax_call;
	function load_log_list()
	{
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		set_event_filter_fields()
	
		var form_name = "filter_form";	
		var dataString = "";
		$("#"+form_name+" select").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#"+form_name+" input").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#"+form_name+" textarea").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		//alert("load_log_list");
		
		//alert(dataString.substring(1));
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		if(!(load_log_list_ajax_call===undefined))
		{
			//alert('abort');
			load_log_list_ajax_call.abort();
		}
		load_log_list_ajax_call = $.ajax({
			url: "<?= base_url("index.php/logs/load_log")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#filter_loading_icon").hide();
					$("#refresh_logs").show();
					
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
	
	//LOAD NEW EVENT DIALOT
	function load_new_event_dialog()
	{
	
		var dataString = "";
	
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#log_event_dialog');
		$( "#log_event_dialog").dialog( "open" );
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/load_new_event_form")?>", // in the quotation marks
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
	
	//POPULATE HIDDEN FIELDS FOR EVENT FILTER CHECK BOXES
	function set_event_filter_fields()
	{
		//GET PICKS?
		if($('#pick_cb').attr('checked'))
		{
			$("#get_picks").val(true);
		}
		else
		{
			$("#get_picks").val(false);
		}
		
		//GET DROPS?
		if($('#drop_cb').attr('checked'))
		{
			$("#get_drops").val(true);
		}
		else
		{
			$("#get_drops").val(false);
		}
		
		//GET FUEL FILLS?
		if($('#fuel_fill_cb').attr('checked'))
		{
			$("#get_fuel_fills").val(true);
		}
		else
		{
			$("#get_fuel_fills").val(false);
		}
		
		//GET FUEL PARTIALS?
		if($('#fuel_partial_cb').attr('checked'))
		{
			$("#get_fuel_partials").val(true);
		}
		else
		{
			$("#get_fuel_partials").val(false);
		}
		
		//GET CHECKP0INTS?
		if($('#checkpoint_cb').attr('checked'))
		{
			$("#get_checkpoints").val(true);
		}
		else
		{
			$("#get_checkpoints").val(false);
		}
		
		//GET DRIVER INS?
		if($('#driver_in_cb').attr('checked'))
		{
			$("#get_driver_ins").val(true);
		}
		else
		{
			$("#get_driver_ins").val(false);
		}
		
		//GET DRIVER OUTS?
		if($('#driver_out_cb').attr('checked'))
		{
			$("#get_driver_outs").val(true);
		}
		else
		{
			$("#get_driver_outs").val(false);
		}
		
		//GET PICK TRAILERS?
		if($('#pick_trailer_cb').attr('checked'))
		{
			$("#get_pick_trailers").val(true);
		}
		else
		{
			$("#get_pick_trailers").val(false);
		}
		
		//GET DROP TRAILERS?
		if($('#drop_trailer_cb').attr('checked'))
		{
			$("#get_drop_trailers").val(true);
		}
		else
		{
			$("#get_drop_trailers").val(false);
		}
		
		//GET CHECK CALLS?
		if($('#check_call_cb').attr('checked'))
		{
			$("#get_check_calls").val(true);
		}
		else
		{
			$("#get_check_calls").val(false);
		}
		
		//GET DRY SERVICES?
		if($('#dry_service_cb').attr('checked'))
		{
			$("#get_dry_services").val(true);
		}
		else
		{
			$("#get_dry_services").val(false);
		}
		
		//GET WET SERVICES?
		if($('#wet_service_cb').attr('checked'))
		{
			$("#get_wet_services").val(true);
		}
		else
		{
			$("#get_wet_services").val(false);
		}
		
		//GET CREDIT CARDS?
		if($('#shift_report_cb').attr('checked'))
		{
			$("#get_shift_reports").val(true);
		}
		else
		{
			$("#get_shift_reports").val(false);
		}
		
		//GET END LEGS?
		if($('#end_leg_cb').attr('checked'))
		{
			$("#get_end_legs").val(true);
		}
		else
		{
			$("#get_end_legs").val(false);
		}
		
		//GET END WEEKS?
		if($('#end_week_cb').attr('checked'))
		{
			$("#get_end_weeks").val(true);
		}
		else
		{
			$("#get_end_weeks").val(false);
		}
		
		//GET GEOPOINTS?
		if($('#geopoint_cb').attr('checked'))
		{
			$("#get_geopoints").val(true);
		}
		else
		{
			$("#get_geopoints").val(false);
		}
		
		//GET GEOPOINTS?
		if($('#geopoint_stop_cb').attr('checked'))
		{
			$("#get_geopoints_stop").val(true);
		}
		else
		{
			$("#get_geopoints_stop").val(false);
		}
		
		
	}
	
	//UNCHECK ALL EVENTS
	function clear_events()
	{
		$("#pick_cb").attr("checked",false);
		$("#drop_cb").attr("checked",false);
		$("#fuel_fill_cb").attr("checked",false);
		$("#fuel_partial_cb").attr("checked",false);
		$("#checkpoint_cb").attr("checked",false);
		$("#driver_in_cb").attr("checked",false);
		$("#driver_out_cb").attr("checked",false);
		$("#pick_trailer_cb").attr("checked",false);
		$("#drop_trailer_cb").attr("checked",false);
		$("#check_call_cb").attr("checked",false);
		$("#dry_service_cb").attr("checked",false);
		$("#wet_service_cb").attr("checked",false);
		$("#shift_report_cb").attr("checked",false);
		$("#end_leg_cb").attr("checked",false);
		$("#end_week_cb").attr("checked",false);
		
		load_log_list();
	}
	
	//CHECK ALL EVENTS
	function select_all_events()
	{
		$("#pick_cb").attr("checked",true);
		$("#drop_cb").attr("checked",true);
		$("#fuel_fill_cb").attr("checked",true);
		$("#fuel_partial_cb").attr("checked",true);
		$("#checkpoint_cb").attr("checked",true);
		$("#driver_in_cb").attr("checked",true);
		$("#driver_out_cb").attr("checked",true);
		$("#pick_trailer_cb").attr("checked",true);
		$("#drop_trailer_cb").attr("checked",true);
		$("#check_call_cb").attr("checked",true);
		$("#dry_service_cb").attr("checked",true);
		$("#wet_service_cb").attr("checked",true);
		$("#shift_report_cb").attr("checked",true);
		$("#end_leg_cb").attr("checked",true);
		$("#end_week_cb").attr("checked",true);
		
		load_log_list();
	}
	
	//VALIDATE AND CREATE NEW EVENT
	function validate_new_event()
	{
		var isValid = true;
		
		//VALIDATE EVENT TYPE
		if($("#event_type").val() == "Select")
		{
			isValid = false;
			alert("You must select an Event Type!");
		}
		
		//VALIDATE MAIN DRIVER
		if($("#main_driver_id").val() == "Select")
		{
			isValid = false;
			alert("You must select a Main Driver!");
		}
		
		//VALIDATE CO-DRIVER
		if($("#codriver_id").val() == "Select")
		{
			isValid = false;
			alert("You must select a Co-Driver!");
		}
		
		//VALIDATE TRUCK
		if($("#truck_id").val() == "Select")
		{
			isValid = false;
			alert("You must select a Truck!");
		}
		
		//VALIDATE THAT DRIVER AND CO-DRIVER ARE NOT THE SAME
		if($("#main_driver_id").val() == $("#codriver_id").val())
		{
			if($("#main_driver_id").val() != 'None')
			{
				isValid = false;
				alert("Main Driver and Co-Driver cannot be the same!");
			}
		}
		
		//VALIDATE OTHER INFO IF EVENT IS NOT CHECK CALL
		if($("#event_type").val() != "Check Call")
		{
		
			//VALIDATE LOAD NUMBER
			if(!$("#load_number").val())
			{	
				if($("#dead_head_cb").is(":checked"))
				{
					//ALLOW LOAD NUMBER TO BE BLANK
				}
				else
				{
					isValid = false;
					alert("You must enter in a Load Number!");
				}
			}
			else if($("#load_number_is_valid").val() == "false")
			{
				isValid = false;
				alert("This Load Number was not found in the system!");
			}
			
			//VALIDATE TRAILER
			if($("#trailer_id").val() == "Select")
			{
				isValid = false;
				alert("You must select a Trailer!");
			}
			
			//VALIDATE DATE
			if(!isDate($("#date").val()))
			{
				isValid = false;
				alert("You must enter in a valid Date!");
			}
			
			//VALIDATE TIME
			if(!isTime($("#time").val()))
			{
				isValid = false;
				alert("You must enter in a valid Time!");
			}
			
			//VALIDATE ODOMETER IF ENTERED
			if($("#odometer").val())
			{
				if(isNaN($("#odometer").val()))
				{
					isValid = false;
					alert("You must enter in a valid Odometer!");
				}
			}
		}
		
		
		//VALIDATE FUEL STOP DETAILS
		if($("#event_type").val() == "Fuel Stop")
		{
			if($("#is_fill").val() == "Select")
			{
				isValid = false;
				alert("You must select Yes or No on Is Fill!");
			}
			
			if($("#source").val() == "Select")
			{
				isValid = false;
				alert("You must select a Source!");
			}
			
			if($("#gallons").val())
			{
				if(isNaN($("#gallons").val()))
				{
					isValid = false;
					alert("You must enter in a valid number for Gallons!");
				}
			}
			else
			{
				isValid = false;
				alert("You must enter in a valid number for Gallons!");
			}
			
			if($("#fuel_price").val())
			{
				if(isNaN($("#fuel_price").val()))
				{
					isValid = false;
					alert("You must enter in a valid number for Fuel Price!");
				}
			}
			else
			{
				isValid = false;
				alert("You must enter in a valid number for Fuel Price!");
			}
			
			if($("#fuel_expense").val())
			{
				if(isNaN($("#fuel_expense").val()))
				{
					isValid = false;
					alert("You must enter in a valid number for Fuel Expense!");
				}
			}
			else
			{
				isValid = false;
				alert("You must enter in a valid number for Fuel Expense!");
			}
		}
		
		if(isValid)
		{
			
			//VALIDATE GOOGLE ADDRESS IF NOT A CHECK CALL
			if($("#event_type").val() != "Check Call")
			{
				//CHECK TO SEE IF LOCATION CAN BE FOUND BY GOOGLE
				var location = $("#address").val()+" "+$("#city").val()+" "+$("#state").val();
				var address = location;
				var cell_value = $("#address").val();
				var geocoder = new google.maps.Geocoder();
				
				var stripped_address = cell_value.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
				//alert(stripped_address);
				//IF LOCATION IS GPS COORDINATE
				if(stripped_address && !isNaN(stripped_address))
				{
					var latlng_array = cell_value.split(",");
					var lat = latlng_array[0];
					var lng =  latlng_array[1];
					//alert(lat);
					//alert(lng);
					var latlng = new google.maps.LatLng(lat, lng);
					
					geocoder.geocode( { 'location': latlng}, function(results, status)
					{
						//alert(status);
						if (status == google.maps.GeocoderStatus.OK) 
						{
							//var location_type = results[0].geometry.location_type;
							
							//alert(location_type);
							
							//alert("Google Approves =)");
							// var geo_city = results[0]['address_components'][2]['long_name'];
							// var geo_state = results[0]['address_components'][4]['short_name'];
							
							//alert(geo_city);
							//alert(geo_state);
							
							//$("#gps_city").val(results[0]['address_components'][2]['long_name']);
							//$("#gps_state").val(results[0]['address_components'][4]['short_name']);
							
							//var postCode = extractFromAdress(results[0].address_components, "postal_code");
							//var street = extractFromAdress(results[0].address_components, "route");
							//var geo_city = extractFromAdress(results[0].address_components, "locality");//CITY
							//var geo_state = extractFromAdress(results[0].address_components, "administrative_area_level_1");//STATE
							
							//alert(geo_city);
							//alert(geo_state);
							
							submit_new_event();
						} 
						else 
						{
							alert('Uh oh!Google is struggling and returned the following: ' + status);
						}
					});
				}
				else //IF LOCATION IS NOT GPS COORDINATES
				{
					geocoder.geocode( { 'address': address}, function(results, status) 
					{
						if (status == google.maps.GeocoderStatus.OK) 
						{
							var location_type = results[0].geometry.location_type;
							
							//alert(location_type);
							if(location_type == "ROOFTOP" || location_type == "RANGE_INTERPOLATED")
							{
								submit_new_event();
								//alert('found!');
							}
							else//IF LOCATION IS NOT FOUND
							{
								alert("Not even Google knows where this location is!");
							}
						} 
						else 
						{
							alert('Google returned the following status: ' + status);
						}
					});
					
				}			
				
			}
			else //IF CHECK CALL
			{
				submit_new_event();
			}
		}
		
	}
	
	function auto_fill_new_event_city_state_from_gps_coordinates()
	{
		var cell_value = $("#address").val();
		
		var stripped_address = cell_value.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
		//alert(stripped_address);
		if(stripped_address && !isNaN(stripped_address))
		{
			var latlng_array = cell_value.split(",");
			var lat = latlng_array[0];
			var lng =  latlng_array[1];
			//alert(lat);
			//alert(lng);
			var latlng = new google.maps.LatLng(lat, lng);
			
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode( { 'location': latlng}, function(results, status)
			{
				//alert(status);
				if (status == google.maps.GeocoderStatus.OK) 
				{
					var location_type = results[0].geometry.location_type;
					
					//alert(location_type);
					//if(location_type == "ROOFTOP" || location_type == "RANGE_INTERPOLATED")
					//{
						//alert("Google Approves =)");
						// var geo_city = results[0]['address_components'][2]['long_name'];
						// var geo_state = results[0]['address_components'][4]['short_name'];
						
						//alert(geo_city);
						//alert(geo_state);
						
						//$("#gps_city").val(results[0]['address_components'][2]['long_name']);
						//$("#gps_state").val(results[0]['address_components'][4]['short_name']);
						
						//var postCode = extractFromAdress(results[0].address_components, "postal_code");
						//var street = extractFromAdress(results[0].address_components, "route");
						var geo_city = extractFromAdress(results[0].address_components, "locality");//CITY
						var geo_state = extractFromAdress(results[0].address_components, "administrative_area_level_1");//STATE
						
						//alert(geo_city);
						//alert(geo_state);
						
						$("#city").val(geo_city);
						$("#state").val(geo_state);
						
					//}
					//else//IF LOCATION IS NOT FOUND
					//{
						//alert("Oops! Google cannot find this location.");
						
					//}
				} 
				else 
				{
					alert('Uh oh!Google returned the following: ' + status);
				}
			});
		}
	}
	
	function fill_in_locations(div,gps_coordinates)
	{
		//alert('reverse geocoding');
		
		var cell_value = gps_coordinates;
		
		if(cell_value)
		{
			var stripped_address = cell_value.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
			//alert(stripped_address);
			if(stripped_address && !isNaN(stripped_address))
			{
				var latlng_array = cell_value.split(",");
				var lat = latlng_array[0];
				var lng =  latlng_array[1];
				//alert(lat);
				//alert(lng);
				var latlng = new google.maps.LatLng(lat, lng);
				
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode( { 'location': latlng}, function(results, status)
				{
					//alert(status);
					if (status == google.maps.GeocoderStatus.OK) 
					{
						var location_type = results[0].geometry.location_type;
						
						//alert(location_type);
						//if(location_type == "ROOFTOP" || location_type == "RANGE_INTERPOLATED")
						//{
							//alert("Google Approves =)");
							// var geo_city = results[0]['address_components'][2]['long_name'];
							// var geo_state = results[0]['address_components'][4]['short_name'];
							
							//alert(geo_city);
							//alert(geo_state);
							
							//$("#gps_city").val(results[0]['address_components'][2]['long_name']);
							//$("#gps_state").val(results[0]['address_components'][4]['short_name']);
							
							//var postCode = extractFromAdress(results[0].address_components, "postal_code");
							//var street = extractFromAdress(results[0].address_components, "route");
							var geo_city = extractFromAdress(results[0].address_components, "locality");//CITY
							var geo_state = extractFromAdress(results[0].address_components, "administrative_area_level_1");//STATE
							
							//alert(geo_city);
							//alert(geo_state);
							
							$("#"+div).html(geo_city+", "+geo_state);
							
							
						//}
						//else//IF LOCATION IS NOT FOUND
						//{
							//alert("Oops! Google cannot find this location.");
							
						//}
					} 
					else 
					{
						alert('Uh oh!Google returned the following: ' + status);
					}
				});
			}
		}
		else
		{
			$("#"+div).html("");
		}
	}
	
	//HELPER FUNCTION TO GET PROPER ADDRESS COMPONENTS FROM GOOGLE GEOCODER
	function extractFromAdress(components, type)
	{
		for (var i=0; i<components.length; i++)
			for (var j=0; j<components[i].types.length; j++)
				if (components[i].types[j]==type) return components[i].short_name;
		return "";
	}
	
	//SUBMITS NEW EVENT WHEN NEW EVENT IS DETERMINED TO BE VALID
	function submit_new_event()
	{
		//CLOSE DIALOG
		$( "#log_event_dialog" ).dialog("close");
		
		var form_name = "new_event_form";	
		var dataString = "";
		$("#"+form_name+" select").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#"+form_name+" input").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#"+form_name+" textarea").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		if($("#event_type").val() == "Fuel Stop")
		{
			$('#scrollable_content').html("<span style='margin-left:30px; margin-top:30px;'>Fleetsmarts is recalculating all unlocked fuel stops. This could take up to 60 seconds or so ...</span>");
		}
		
		//alert(dataString.substring(1));
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/create_new_event")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					load_log_list();
					$( "#log_event_dialog" ).dialog("close");
					
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}

	//CHECK TO SEE IF GOOGLE CAN FIND THE LOCATION
	function check_google_for_location(location)
	{
		//PASS POST DATA
		var dataString = "&location="+location;
		var main_content = $('#main_content');
		$.ajax({
			url: "<?= base_url("index.php/logs/check_google_for_location")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					alert(response);
					
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error! "+response);
				}
			}
		});//END AJAX
		
		return false;
	}
	
	//EVENT ICON CLICKED
	function event_icon_clicked(event_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#log_event_details_'+event_id);
		//alert("hello");
		if(this_div.is(":visible"))
		{
			this_div.hide();
			this_div.html('<img id="loading_icon" name="loading_icon" height="20px" src="/images/loading.gif" style="margin-left:450px; margin-top:5px;" />');
		}
		else
		{
			open_event_details(event_id);
		}
	}
	
	//OPEN EVENT DETAILS
	function open_event_details(event_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#log_event_details_'+event_id);
	
		this_div.show();
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&event_id="+event_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/open_event_details")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					refresh_event(event_id);
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					//alert(response);
					alert("500 error!");
					//$("#main_content").html(response);
				}
			}
		});//END AJAX
	}
	
	//ESTIMATE ODOMETER
	function estimate_odometer(log_entry_id, sync_entry_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#log_event_details_'+log_entry_id);
	
		this_div.show();
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&log_entry_id="+log_entry_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/estimate_odometer")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//this_div.html(response);
					refresh_event(log_entry_id);
					if(sync_entry_id)
					{
						refresh_event(sync_entry_id);
					}
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					//alert(response);
					alert("500 error!");
					//$("#main_content").html(response);
				}
			}
		});//END AJAX
	}
	
	//REFRESH SINGLE EVENT ROW
	function refresh_event(event_id)
	{
	
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var dataString = "&event_id="+event_id;
		var this_div = $('#log_entry_row_'+event_id);
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/refresh_event")?>", // in the quotation marks
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
	
	//ONCHANGE EVENT FOR DROPDOWN ON CELL EDIT
	function cell_edit_dropdown_changed()
	{
		$("#cell_value").val($("#cell_edit_dropdown").val());
	}
	
	//EDIT CELL IN LOG
	var previous_log_entry_id;
	var previous_field_name;
	function edit_cell(log_entry_id,field_name)
	{
		var cell_value = $("#hidden_"+field_name+"_"+log_entry_id).val();
		//alert(cell_value);
		
		//RESET PREVIOUS CELL FORMATTING
		$("#"+previous_field_name+"_"+previous_log_entry_id).css("border","none");
		$("#city_state_"+previous_log_entry_id).css("border","none");
		$("#entry_datetime_"+previous_log_entry_id).css("border","none");
	
		//HIGHLIGHT CELL TO EDIT
		$("#"+field_name+"_"+log_entry_id).css("border","solid");
		$("#"+field_name+"_"+log_entry_id).css("border-color","#6295FC");
		
		if(field_name == "city" || field_name == "state")
		{
			$("#city_state_"+log_entry_id).css("border","solid");
			$("#city_state_"+log_entry_id).css("border-color","#6295FC");
		}
		
		if(field_name == "time" || field_name == "date")
		{
			$("#entry_datetime_"+log_entry_id).css("border","solid");
			$("#entry_datetime_"+log_entry_id).css("border-color","#6295FC");
		}
		
		
		
		
		//alert(log_entry_id+" "+field_name+" "+cell_value);
		
		
		if(field_name == "main_driver_id" || field_name == "codriver_id" || field_name == "truck_id" || field_name == "trailer_id")
		{
						
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#edit_cell_header_dropdown');
			
			var dataString = dataString = "&field_name="+field_name;
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/get_dropdown")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						
						$("#cell_edit_dropdown").val(cell_value);
						
						$("#plain_header").hide();
						$("#edit_cell_header").hide();
						$('#edit_cell_header_dropdown').show();
						
						$("#cell_value").val(cell_value);
						
						
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
		else 
		{
			$("#cell_value").val(cell_value);
			
			$("#plain_header").hide();
			$("#edit_cell_header_dropdown").hide();
			$("#edit_cell_header").show();
			
			
			if(field_name == "date")
			{
				$('#cell_value').datepicker({ showAnim: 'blind' });
			}
			else
			{
				$("#cell_value").datepicker("destroy");
				$("#cell_value").focus();
			}
		}
		
		//SET VALUES OF EDIT CELL FORM
		$("#log_entry_id").val(log_entry_id);
		$("#field_name").val(field_name);
		
		
		previous_log_entry_id = log_entry_id;
		previous_field_name = field_name;
	}
	
	//CANCEL EDIT CELL
	function cancel_edit_cell()
	{
		//RESET PREVIOUS CELL FORMATTING
		$("#"+previous_field_name+"_"+previous_log_entry_id).css("border","none");
		$("#city_state_"+previous_log_entry_id).css("border","none");
		$("#entry_datetime_"+previous_log_entry_id).css("border","none");
	
		$("#edit_cell_header_dropdown").hide();
		$("#edit_cell_header").hide();
		$("#plain_header").show();
	}
	
	//SAVE EDIT CELL
	function save_edit_cell()
	{
		var log_entry_id = $("#log_entry_id").val();
		var field_name = $("#field_name").val();
		var cell_value = $("#cell_value").val();
		
		var address = $("#hidden_address_"+log_entry_id).val();
		var city = $("#hidden_city_"+log_entry_id).val();
		var state = $("#hidden_state_"+log_entry_id).val();
		
		//$("#gps_city").val("test_city");
		//$("#gps_state").val("test_state");
		
		//var dataString = $("#edit_cell_form").serialize();
		//alert(dataString.toString());
		
		var isValid = true;
		//VALIDATE THE CELL VALUE
		if(field_name == "date")
		{
			//VALIDATE DATE
			if(!isDate($("#cell_value").val()))
			{
				isValid = false;
				alert("You must enter in a valid Date!");
			}
		}
		
		if(field_name == "time")
		{
			//VALIDATE DATE
			if(!isTime($("#cell_value").val()))
			{
				isValid = false;
				alert("You must enter in a valid Time!");
			}
		}
		
		if(field_name == "odometer")
		{
			if(isNaN(cell_value))
			{
				isValid = false;
				alert("Odometer must be a number!");
			}
		}
		
		if(field_name == "miles")
		{
			if(isNaN(cell_value))
			{
				isValid = false;
				alert("Miles must be a number!");
			}
		}
		
		//alert(field_name);
		var use_gps_coordinates;
		if(field_name == "address" || field_name == "city" || field_name == "state")
		{
			if(field_name == "address")
			{
				
				//var stripped_address = cell_value.replace(/\s+/g,'').replace(/-/g,'').replace(/./g,'').replace(/,/g,'');
				var stripped_address = cell_value.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
				//alert(stripped_address);
				if(isNaN(stripped_address))
				{
					var location = cell_value+" "+city+" "+state;
					use_gps_coordinates = false;
				}
				else
				{
					var latlng_array = cell_value.split(",");
					var lat = latlng_array[0];
					var lng =  latlng_array[1];
					//alert(latlng_array[1]);
					var latlng = new google.maps.LatLng(lat, lng);
					use_gps_coordinates = true;
				}
				
				
				
			}
			else if(field_name == "city")
			{
				var location = address+" "+cell_value+" "+state;
			}
			else if(field_name == "state")
			{
				var location = address+" "+city+" "+cell_value;
			}
			
			gps_or_address = 'address';
			if(use_gps_coordinates)
			{
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode( { 'location': latlng}, function(results, status)
				{
					if (status == google.maps.GeocoderStatus.OK) 
					{
						var location_type = results[0].geometry.location_type;
						
						//alert(location_type);
						//if(location_type == "ROOFTOP" || location_type == "RANGE_INTERPOLATED")
						//{
							alert("Google Approves =)");
							//var geo_city = results[0]['address_components'][2]['long_name'];
							//var geo_state = results[0]['address_components'][4]['short_name'];
							
							var geo_city = extractFromAdress(results[0].address_components, "locality");//CITY
							var geo_state = extractFromAdress(results[0].address_components, "administrative_area_level_1");//STATE
							
							//alert(geo_city);
							//alert(geo_state);
							
							//$("#gps_city").val(results[0]['address_components'][2]['long_name']);
							//$("#gps_state").val(results[0]['address_components'][4]['short_name']);
							
							$("#gps_city").val(geo_city);
							$("#gps_state").val(geo_state);
							
							submit_ajax_for_cell_edit();
						//}
						//else//IF LOCATION IS NOT FOUND
						//{
							//alert("Oops! Google cannot find this location.");
							//$("#cell_value").focus();
							
						//}
					} 
					else 
					{
						alert('Uh oh!Google returned the following: ' + status);
						$("#cell_value").focus();
					}
				});
			}
			else
			{
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode( { 'address': location}, function(results, status)
				{
					if (status == google.maps.GeocoderStatus.OK) 
					{
						var location_type = results[0].geometry.location_type;
						
						//alert(location_type);
						if(location_type == "ROOFTOP" || location_type == "RANGE_INTERPOLATED")
						{
							alert("Google Approves =)");
							submit_ajax_for_cell_edit();
						}
						else//IF LOCATION IS NOT FOUND
						{
							alert("Shoot! Google cannot find this location.");
							$("#cell_value").focus();
							
						}
					} 
					else 
					{
						alert('Hmm... Google returned the following: ' + status);
						$("#cell_value").focus();
					}
				});
			}
		}
		
		//isValid = false;
		if(isValid)
		{
			submit_ajax_for_cell_edit();
			
			// var id = $("#log_entry_id").val();
			
			// var dataString = $("#edit_cell_form").serialize();
			// //alert(dataString.toString());
			// alert('submitting ajax');
			
			// //-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// // GET THE DIV IN DIALOG BOX
			// var this_div = $('#log_entry_row_'+id);
			// //POST DATA TO PASS BACK TO CONTROLLER
			
			// // AJAX!
			// $.ajax({
				// url: "<?= base_url("index.php/logs/save_edit_cell")?>", // in the quotation marks
				// type: "POST",
				// data: dataString,
				// cache: false,
				// context: this_div, // use a jquery object to select the result div in the view
				// statusCode: {
					// 200: function(response){
						// // Success!
						// this_div.html(response);
						
						// //alert(response);
					// },
					// 404: function(){
						// // Page not found
						// alert('page not found');
					// },
					// 500: function(response){
						// // Internal server error
						// alert("500 error!")
						// this_div.html(response);
					// },
					// 600: function(){
							// alert("600 error!");
						// }
				// }
			// });//END AJAX
		}
		else
		{
			$("#cell_value").focus();
		}
		
	}
	
	function submit_ajax_for_cell_edit()
	{
		var id = $("#log_entry_id").val();
			
		var dataString = $("#edit_cell_form").serialize();
		//alert(dataString.toString());
		//alert('submitting ajax');
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#log_entry_row_'+id);
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/save_edit_cell")?>", // in the quotation marks
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
					this_div.html(response);
				},
				600: function(){
						alert("600 error!");
					}
			}
		});//END AJAX
	}

	//DELETE EVENT
	function delete_event(log_entry_id)
	{
		if(confirm("Are you sure you want to delete this event??"))
		{
			//alert("hello");
			var dataString = "&log_entry_id="+log_entry_id;
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/delete_event")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				statusCode: {
					200: function(response){
						// Success!
						load_log_list();
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
	}

	function lock_event(event_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#log_event_details_'+event_id);
	
		this_div.show();
		this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px; height:20px" />');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&event_id="+event_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/open_event_details")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					refresh_event(event_id);
					lock_event_ajax(event_id)
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error!");
				}
			}
		});//END AJAX
	}
	
	//FUNCTION TO BE RAN ON SUCCESS OF AJAX CALL IN LOCK_EVENT
	function lock_event_ajax(log_entry_id)
	{
		if(confirm("Are you sure you want to lock this event and any other events associated with it?"))
		{
			//alert('hello');
			$( "#leg_calculations_dialog_"+log_entry_id ).html('<div style="width:360px; margin: 0 auto; margin-top:45px;">This could take a while. Fleetsmarts is going to town on this leg...</div>');
			var this_div = $('#log_entry_row_'+log_entry_id);
			var dataString = "&log_entry_id="+log_entry_id;
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/lock_event")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//load_log_list();
						//alert(response);
						
						$( "#leg_calculations_dialog_"+log_entry_id ).dialog('close');
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
	}
	
	//OPEN FUEL ALLOCATIONS DETAILS
	function open_fuel_allocations(log_entry_id)
	{
		$( "#fuel_allocations_"+log_entry_id ).html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style=" height:25px; margin-left:460px; margin-top:10px;" />');
		var this_div = $('#fuel_allocations_'+log_entry_id);
		var dataString = "&log_entry_id="+log_entry_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/open_fuel_allocations")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, 
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					//load_log_list();
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
	
	//UNLOCK EVENT
	function unlock_event(log_entry_id)
	{
		var this_div = $('#log_entry_row_'+log_entry_id);
		var dataString = "&log_entry_id="+log_entry_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/unlock_event")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, 
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					//$( "#leg_calculations_dialog_"+log_entry_id ).dialog('close');
					//load_log_list();
					//this_div.html(response);
					refresh_event(log_entry_id)
					
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
	
	//CREATE NEW CHECK POINT FROM FUEL STOP
	function create_new_checkpoint(event_id)
	{
	
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var dataString = "&event_id="+event_id;
		var this_div = $('#log_entry_row_'+event_id);
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/create_new_checkpoint")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					load_log_list();
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
	
	//CREATE NEW EVENT FROM GEOPOINT
	function create_new_event_from_geopoint(event_id,event_type)
	{
	
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var dataString = "&event_id="+event_id+"&event_type="+event_type;
		var this_div = $('#log_entry_row_'+event_id);
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/create_new_event_from_geopoint")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					load_log_list();
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
	
	function open_file_upload(entry_id)
	{
		//alert('hello');
		//-------------- AJAX -------------------
		// GET THE DIV IN DIALOG BOX
		var target_div = $('#file_upload_dialog');
		target_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="width:25px; height:25px; margin-top:50px;" /></div>');
		$('#file_upload_dialog').dialog('open')
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = 'entry_id='+entry_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/load_file_upload")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: target_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					target_div.html(response);
					
					
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

	
	//*************************** SHIFT REPORTS FUNCTIONS ****************************
	//LOAD GOALPOINTS DIV
	function load_goalpoints_div(log_entry_id)
	{
		var dataString = "&log_entry_id="+log_entry_id;
		var this_div = $("#goalpoints_div_"+log_entry_id);
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px; height:20px;" />');
		
		$('.gp_exp_details_'+log_entry_id).css({"display":"none"});
		$('.gp_exp_loading_'+log_entry_id).css({"display":"block"});
		
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/load_goalpoints_div")?>", // in the quotation marks
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
	
	//OPENS EDIT VIEW
	function edit_event(log_entry_id)
	{
		$('.edit_'+log_entry_id).css({"display":"block"});
		$('.details_'+log_entry_id).css({"display":"none"});
	}
	
	//SAVE SHIFT REPORT EDIT
	function save_shift_report(log_entry_id)
	{
		var isValid = true;
	
		//VALIDATE INPUTS FOR SAVE
		if($("#client_id_"+log_entry_id).val() == 'Select')
		{
			isValid = false;
			alert("Driver must be selected!");
		}
		
		var start_gps = $("#shift_s_gps_"+log_entry_id).val();
		var stripped_start_gps = start_gps.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
		//alert(stripped_address);
		if(isNaN(stripped_start_gps))
		{
			isValid = false;
			alert("Start Location must be in GPS format!");
		}
		
		var end_gps = $("#shift_e_gps_"+log_entry_id).val();
		var stripped_end_gps = end_gps.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
		//alert(stripped_address);
		if(isNaN(stripped_end_gps))
		{
			isValid = false;
			alert("End Location must be in GPS format!");
		}
		
		if(isNaN($("#shift_s_odometer_"+log_entry_id).val()))
		{
			isValid = false;
			alert("Starting Odometer must be a number!");
		}
		
		if(isNaN($("#shift_e_odometer_"+log_entry_id).val()))
		{
			isValid = false;
			alert("Ending Odometer must be a number!");
		}
		
		/**
		if(isNaN($("#idle_time_"+log_entry_id).val()))
		{
			isValid = false;
			alert("Engine Idle Time must be a number!");
		}
		**/
		
		if(isValid)
		{
			
			var dataString = $("#shift_report_form_"+log_entry_id).serialize();
			var this_div;
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/save_shift_report")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						open_event_details(log_entry_id);
						
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
	
	function has_deadline_changed(log_entry_id)
	{
		if($("#has_deadline_cb_"+log_entry_id).is(":checked"))
		{
			//alert('checked');
			$("#temp_deadline_"+log_entry_id).val('');
			$("#temp_deadline_"+log_entry_id).show();
		}
		else
		{
			$("#temp_deadline_"+log_entry_id).hide();
			$("#temp_deadline_"+log_entry_id).val('');
		}
	}
	
	function gp_row_mouseover(gp_id)
	{
		$("#order_up_arrow_"+gp_id).show();
		$("#order_down_arrow_"+gp_id).show();
	}
	
	function gp_row_mouseout(gp_id)
	{
		$("#order_up_arrow_"+gp_id).hide();
		$("#order_down_arrow_"+gp_id).hide();
	}
	
	function order_gp(log_entry_id,gp_id,direction)
	{
		//alert('order_gp');
		$('.gp_exp_details_'+log_entry_id).css({"display":"none"});
		$('.gp_exp_loading_'+log_entry_id).css({"display":"block"});
		
		var dataString = "&log_entry_id="+log_entry_id+"&gp_id="+gp_id+"&direction="+direction;
		var this_div = $("#goalpoints_div_"+log_entry_id);
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/change_gp_order")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, 
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					//this_div.html(response);
					load_goalpoints_div(log_entry_id)
					
					// //RESET TEMP INPUT FEILDS
					// $("#temp_deadline_"+log_entry_id).val("");
					// $("#temp_gp_type_"+log_entry_id).val("Select");
					// $("#temp_gp_gps_"+log_entry_id).val("");
					// $("#gp_location_text_"+log_entry_id).html("");
					// $("#temp_gp_notes_"+log_entry_id).val("");
					
					
					// //RESETS THE REAL FORM TO BLANKS
					// $("#deadline_"+log_entry_id).val($("#temp_deadline_"+log_entry_id).val());
					// $("#gp_type_"+log_entry_id).val($("#temp_gp_type_"+log_entry_id).val());
					// $("#gp_gps_"+log_entry_id).val($("#temp_gp_gps_"+log_entry_id).val());
					// $("#gp_location_"+log_entry_id).val($("#gp_location_text_"+log_entry_id).html());
					// $("#gp_notes_"+log_entry_id).val($("#temp_gp_notes_"+log_entry_id).val());
					
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
	
	function auto_fill_goalpoint_location(log_entry_id)//FOR ADD GP
	{
		fill_in_locations("gp_location_text_"+log_entry_id,$("#temp_gp_gps_"+log_entry_id).val());
	}
	
	function auto_fill_goalpoint_edit_location(gp_id)//FOR EDIT GP
	{
		fill_in_locations("gp_location_"+gp_id,$("#edit_gp_gps_"+gp_id).val());
	}
	
	function add_goalpoint(log_entry_id)
	{
		//alert('hello');
		var isValid = true;
		
		//VALIDATE DATE TIME
		if($("#temp_deadline_"+log_entry_id).val() && $("#temp_deadline_"+log_entry_id).val() != 'none')
		{
			var date_time_array = $("#temp_deadline_"+log_entry_id).val().split(' ');
			
			if(date_time_array[0])
			{
				//VALIDATE DATE
				if(!isDate(date_time_array[0]))
				{
					isValid = false;
					alert("You must enter in a valid Date!");
				}
			}
			else
			{
				isValid = false;
				alert("You must enter in a valid Date!");
			}
			
			if(date_time_array[1])
			{
				//VALIDATE TIME
				if(!isTime(date_time_array[1]))
				{
					isValid = false;
					alert("You must enter in a valid Time!");
				}
			}
			else
			{
				isValid = false;
				alert("You must enter in a valid Time!");
			}
		}
		else
		{
			if($("#has_deadline_cb_"+log_entry_id).is(":checked"))
			{
				isValid = false;
				alert("Deadline must be entered!");
			}
			
		}
		
		
		if($("#temp_gp_type_"+log_entry_id).val() == "Select")
		{
			isValid = false;
			alert("You must select a Goalpint Type!");
		}
		
		var gp_gps = $("#temp_gp_gps_"+log_entry_id).val();
		if(gp_gps)
		{
			var stripped_gp_gps = gp_gps.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
			//alert(stripped_address);
			if(isNaN(stripped_gp_gps))
			{
				isValid = false;
				alert("GPS must be in GPS format!");
			}
		}
		else
		{
			isValid = false;
			alert("You must enter in a GPS coordinate!");
		}
		
		if(isValid)
		{
			$("#deadline_"+log_entry_id).val($("#temp_deadline_"+log_entry_id).val());
			$("#gp_type_"+log_entry_id).val($("#temp_gp_type_"+log_entry_id).val());
			$("#gp_gps_"+log_entry_id).val($("#temp_gp_gps_"+log_entry_id).val());
			$("#gp_location_"+log_entry_id).val($("#gp_location_text_"+log_entry_id).html());
			$("#gp_notes_"+log_entry_id).val($("#temp_gp_notes_"+log_entry_id).val());
			
			var dataString = $("#new_gp_form_"+log_entry_id).serialize();
			var this_div = $("#goalpoints_div_"+log_entry_id);
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/add_new_goalpoint")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						//this_div.html(response);
						load_goalpoints_div(log_entry_id)
						
						//RESET TEMP INPUT FEILDS
						$("#temp_deadline_"+log_entry_id).hide();
						
						$("#temp_deadline_"+log_entry_id).val('');
						$("#temp_gp_type_"+log_entry_id).val("Select");
						$("#temp_gp_gps_"+log_entry_id).val("");
						$("#gp_location_text_"+log_entry_id).html("");
						$("#temp_gp_notes_"+log_entry_id).val("");
						
						
						//RESETS THE REAL FORM TO BLANKS
						$("#deadline_"+log_entry_id).val($("#temp_deadline_"+log_entry_id).val());
						$("#gp_type_"+log_entry_id).val($("#temp_gp_type_"+log_entry_id).val());
						$("#gp_gps_"+log_entry_id).val($("#temp_gp_gps_"+log_entry_id).val());
						$("#gp_location_"+log_entry_id).val($("#gp_location_text_"+log_entry_id).html());
						$("#gp_notes_"+log_entry_id).val($("#temp_gp_notes_"+log_entry_id).val());
						$("#has_deadline_cb_"+log_entry_id).attr('checked', false); 
						
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
	
	function validate_incomplete_goalpoints_dialog(log_entry_id)
	{
		//alert('starting validation');
		$("#incomplete_goalpoints_dialog_overlay_"+log_entry_id).show();
		$("#missed_goalpoints_save_button_"+log_entry_id).attr('disabled',true);
		//USE AJAX TO VALIDATE MISSING GOALPOINTS DIALOG
		var dataString = "&log_entry_id="+log_entry_id+"&new_ca_time="+$("#new_ca_time_"+log_entry_id).val();
		var this_div = $("#validation_response_"+log_entry_id);
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/validate_incomplete_goalpoints_dialog")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, 
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					//$("#incomplete_goalpoints_dialog_overlay_"+log_entry_id).hide();
					//$("#missed_goalpoints_save_button_"+log_entry_id).removeAttr('disabled');
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
	
	//CHANGE VIEW TO EDIT GOALPOINT ROW
	var last_edit_gp_id;
	function edit_goalpoint(gp_id)
	{
		//CHANGE BACK CURRENT EDIT ROW
		if(last_edit_gp_id)
		{
			//alert(last_edit_gp_id);
			$(".gp_row_edit_"+last_edit_gp_id).hide();
			$(".gp_row_details_"+last_edit_gp_id).show();
		}
		
		//CHANGE NEWLY SELECTED ROW
		$(".gp_row_details_"+gp_id).hide();
		$(".gp_row_edit_"+gp_id).show();
		
		last_edit_gp_id = gp_id;
	}
	
	function save_goalpoint(gp_id,log_entry_id)
	{
		var isValid = true;
		
		//VALIDATE DATE TIME
		// if($("#edit_expected_time_"+gp_id).val())
		// {
			// var date_time_array = $("#edit_expected_time_"+gp_id).val().split(' ');
			
			// if(date_time_array[0])
			// {
				// //VALIDATE DATE
				// if(!isDate(date_time_array[0]))
				// {
					// isValid = false;
					// alert("You must enter in a valid Date!");
				// }
			// }
			// else
			// {
				// isValid = false;
				// alert("You must enter in a valid Date!");
			// }
			
			// if(date_time_array[1])
			// {
				// //VALIDATE TIME
				// if(!isTime(date_time_array[1]))
				// {
					// isValid = false;
					// alert("You must enter in a valid Time!");
				// }
			// }
			// else
			// {
				// isValid = false;
				// alert("You must enter in a valid Time!");
			// }
		// }
		// else
		// {
			// isValid = false;
			// alert("Expected Time must be entered!");
		// }
		
		
		var gp_gps = $("#edit_gp_gps_"+gp_id).val();
		if(gp_gps)
		{
			var stripped_gp_gps = gp_gps.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
			//alert(stripped_address);
			if(isNaN(stripped_gp_gps))
			{
				isValid = false;
				alert("GPS must be in GPS format!");
			}
		}
		else
		{
			isValid = false;
			alert("You must enter in a GPS coordinate!");
		}
		
		if($("#gp_location_"+gp_id).html() == '')
		{
			isValid = false;
			alert("There must be a location associated with the GPS coordinates!");
		}
		
		if(isValid)
		{
			$("#save_gp_icon_"+gp_id).hide();
			$("#loading_icon_save_gp_"+gp_id).show();
			
			//$("#edit_gp_id_"+log_entry_id).val(gp_id);
			//$("#edit_expected_time_"+log_entry_id).val($("#edit_expected_time_"+gp_id).val());
			$("#edit_gp_type_"+log_entry_id).val($("#edit_gp_type_"+gp_id).val());
			$("#edit_gp_gps_"+log_entry_id).val($("#edit_gp_gps_"+gp_id).val());
			$("#edit_gp_location_"+log_entry_id).val($("#gp_location_"+gp_id).html());
			$("#edit_gp_notes_"+log_entry_id).val($("#edit_gp_notes_"+gp_id).val());
			
			var dataString = $("#edit_gp_form_"+log_entry_id).serialize();
			var this_div = $("#goalpoints_div_"+log_entry_id);
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/edit_goalpoint")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						//this_div.html(response);
						load_goalpoints_div(log_entry_id);
						
						//$("#edit_gp_id_"+log_entry_id).val("");
						//$("#edit_expected_time_"+log_entry_id).val("");
						$("#edit_gp_type_"+log_entry_id).val("");
						$("#edit_gp_gps_"+log_entry_id).val("");
						$("#edit_gp_location_"+log_entry_id).val("");
						$("#edit_gp_notes_"+log_entry_id).val("");
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
	
	function open_complete_goalpoint_dialog(gp_id,log_entry_id,expected_datetime)
	{
		
		$("#complete_gp_expected_time_text_"+log_entry_id).html(expected_datetime);
		$("#complete_gp_id_"+log_entry_id).val(gp_id);
		$("#complete_gp_time_"+log_entry_id).val("");
		$("#gp_complete_dialog_"+log_entry_id).dialog('open');
	}
	
	function mark_gp_complete(log_entry_id)
	{
		var isValid = true;
		
		if($("#complete_gp_time_"+log_entry_id).val())
		{
			var date_time_array = $("#complete_gp_time_"+log_entry_id).val().split(' ');
			
			if(date_time_array[0])
			{
				//VALIDATE DATE
				if(!isDate(date_time_array[0]))
				{
					isValid = false;
					alert("You must enter in a valid Date!");
				}
			}
			else
			{
				isValid = false;
				alert("You must enter in a valid Date!");
			}
			
			if(date_time_array[1])
			{
				//VALIDATE TIME
				if(!isTime(date_time_array[1]))
				{
					isValid = false;
					alert("You must enter in a valid Time!");
				}
			}
			else
			{
				isValid = false;
				alert("You must enter in a valid Time!");
			}
		}
		else
		{
			isValid = false;
			alert("Completion Datetime must be entered!");
		}
		
		if(isValid)
		{
			//alert(log_entry_id);
			var dataString = $("#complete_gp_form_"+log_entry_id).serialize();
			var this_div = "";
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/mark_goalpoint_complete")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						//this_div.html(response);
						$("#gp_complete_dialog_"+log_entry_id).dialog('close');
						load_goalpoints_div(log_entry_id)
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
	
	function delete_goalpoint(gp_id,log_entry_id)
	{
		if(confirm("Are you sure you want to delete this Goalpoint??"))
		{
			$("#edit_gp_id_"+log_entry_id).val(gp_id);
			$("#edit_expected_time_"+log_entry_id).val($("#edit_expected_time_"+gp_id).val());
			$("#edit_gp_type_"+log_entry_id).val($("#edit_gp_type_"+gp_id).val());
			$("#edit_gp_gps_"+log_entry_id).val($("#edit_gp_gps_"+gp_id).val());
			$("#edit_gp_location_"+log_entry_id).val($("#gp_location_"+gp_id).html());
			$("#edit_gp_notes_"+log_entry_id).val($("#edit_gp_notes_"+gp_id).val());
			
			var dataString = $("#edit_gp_form_"+log_entry_id).serialize();
			var this_div = $("#goalpoints_div_"+log_entry_id);
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/delete_goalpoint")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						//this_div.html(response);
						load_goalpoints_div(log_entry_id)
						
						$("#edit_gp_id_"+log_entry_id).val("");
						$("#edit_expected_time_"+log_entry_id).val("");
						$("#edit_gp_type_"+log_entry_id).val("");
						$("#edit_gp_gps_"+log_entry_id).val("");
						$("#edit_gp_location_"+log_entry_id).val("");
						$("#edit_gp_notes_"+log_entry_id).val("");
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
	
	//LOAD CONTACT ATTEMPTS
	function load_contact_attempts_div(log_entry_id)
	{
		var dataString = "&log_entry_id="+log_entry_id;
		var this_div = $("#contact_attempts_div_"+log_entry_id);
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/load_contact_attempts_div")?>", // in the quotation marks
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
	
	function did_event_happen_selected(gp_id)
	{
		if($("#did_gp_happen_"+gp_id).val() == "Yes")
		{
			$(".what_time_row_"+gp_id).show();
		}
		else
		{
			$(".what_time_row_"+gp_id).hide();
		}
	}
	
	function add_contact_attempt(log_entry_id)
	{
		var isValid = true;
		
		//VALIDATE DATE TIME
		if($("#temp_ca_time_"+log_entry_id).val())
		{
			var date_time_array = $("#temp_ca_time_"+log_entry_id).val().split(' ');
			
			if(date_time_array[0])
			{
				//VALIDATE DATE
				if(!isDate(date_time_array[0]))
				{
					isValid = false;
					alert("You must enter in a valid Date!");
				}
			}
			else
			{
				isValid = false;
				alert("You must enter in a valid Date!");
			}
			
			if(date_time_array[1])
			{
				//VALIDATE TIME
				if(!isTime(date_time_array[1]))
				{
					isValid = false;
					alert("You must enter in a valid Time!");
				}
			}
			else
			{
				isValid = false;
				alert("You must enter in a valid Time!");
			}
		}
		else
		{
			isValid = false;
			alert("Time must be entered!");
		}
		
		var gp_gps = $("#temp_ca_gps_"+log_entry_id).val();
		if(gp_gps)
		{
			var stripped_gp_gps = gp_gps.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
			//alert(stripped_address);
			if(isNaN(stripped_gp_gps))
			{
				isValid = false;
				alert("GPS must be in GPS format!");
			}
		}
		else
		{
			isValid = false;
			alert("You must enter in a GPS coordinate!");
		}
		
		if($("#temp_ca_method_"+log_entry_id).val() == "Select")
		{
			isValid = false;
			alert("You must select a Form of Contact!");
		}
		
		if($("#temp_ca_result_"+log_entry_id).val() == "Select")
		{
			isValid = false;
			alert("You must select a Result!");
		}
		
		if(!$("#temp_ca_notes_"+log_entry_id).val())
		{
			isValid = false;
			alert("You must enter in some Notes!");
		}
		
		if(isValid)
		{
			var next_gp_datetime = $("#next_gp_time_"+log_entry_id).val();
			
			//alert($("#temp_ca_time_"+log_entry_id).val());
			//alert(next_gp_datetime);
			
			var ca_date = new Date($("#temp_ca_time_"+log_entry_id).val());
			var next_gp_date = new Date(next_gp_datetime);
			
			//alert(ca_date.getTime());
			//alert(next_gp_date.getTime());
			
			//IF INCOMPLETE GP DIALOG NEEDS TO BE LOADED
			if(ca_date >= next_gp_date)
			{
				$("#new_ca_time_"+log_entry_id).val($("#temp_ca_time_"+log_entry_id).val());
				$("#new_ca_gps_"+log_entry_id).val($("#temp_ca_gps_"+log_entry_id).val());
				$("#new_ca_method_"+log_entry_id).val($("#temp_ca_method_"+log_entry_id).val());
				$("#new_ca_result_"+log_entry_id).val($("#temp_ca_result_"+log_entry_id).val());
				$("#new_ca_notes_"+log_entry_id).val($("#temp_ca_notes_"+log_entry_id).val());
				
				var dataString = $("#new_ca_form_"+log_entry_id).serialize();
				var this_div = $("#missed_goalpoints_dialog_"+log_entry_id);
				// AJAX!
				$.ajax({
					url: "<?= base_url("index.php/logs/load_dialog_of_missed_goalpoints")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: this_div, 
					statusCode: {
						200: function(response){
							// Success!
							//alert(response);
							this_div.html(response);
							$("#missed_goalpoints_dialog_"+log_entry_id).dialog( "open" );
							//alert('success');
							
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
				//alert('submitting form for new ca');
				$("#new_ca_time_"+log_entry_id).val($("#temp_ca_time_"+log_entry_id).val());
				$("#new_ca_gps_"+log_entry_id).val($("#temp_ca_gps_"+log_entry_id).val());
				$("#new_ca_method_"+log_entry_id).val($("#temp_ca_method_"+log_entry_id).val());
				$("#new_ca_result_"+log_entry_id).val($("#temp_ca_result_"+log_entry_id).val());
				$("#new_ca_notes_"+log_entry_id).val($("#temp_ca_notes_"+log_entry_id).val());
				
				var dataString = $("#new_ca_form_"+log_entry_id).serialize();
				var this_div = "";
				// AJAX!
				$.ajax({
					url: "<?= base_url("index.php/logs/add_new_contact_attempt")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: this_div, 
					statusCode: {
						200: function(response){
							// Success!
							//alert(response);
							//this_div.html(response);
							load_contact_attempts_div(log_entry_id)
							load_goalpoints_div(log_entry_id)
							//alert('success');
							
							//RESET INPUT FEILDS
							$("#temp_ca_time_"+log_entry_id).val("");
							$("#temp_ca_gps_"+log_entry_id).val("");
							$("#temp_ca_method_"+log_entry_id).val("Select");
							$("#temp_ca_result_"+log_entry_id).val("");
							$("#temp_ca_notes_"+log_entry_id).val("");
							
							
							//RESETS THE REAL FORM TO BLANKS
							$("#new_ca_time_"+log_entry_id).val($("#temp_ca_time_"+log_entry_id).val());
							$("#new_ca_gps_"+log_entry_id).val($("#temp_ca_gps_"+log_entry_id).val());
							$("#new_ca_method_"+log_entry_id).val($("#temp_ca_method_"+log_entry_id).val());
							$("#new_ca_result_"+log_entry_id).val($("#temp_ca_result_"+log_entry_id).val());
							$("#new_ca_notes_"+log_entry_id).val($("#temp_ca_notes_"+log_entry_id).val());
							
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
	}
	
	function ca_row_mouseover(ca_id)
	{
		$("#ca_trash_"+ca_id).show();
	}
	
	function ca_row_mouseout(ca_id)
	{
		$("#ca_trash_"+ca_id).hide();
	}
	
	function delete_contact_attempt(ca_id,log_entry_id)
	{
		if(confirm("Are you sure you want to delete this Contact Attempt??"))
		{
			var dataString = "&log_entry_id="+log_entry_id+"&ca_id="+ca_id;
			var this_div = "";
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/delete_contact_attempt")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						//this_div.html(response);
						load_contact_attempts_div(log_entry_id)
						load_goalpoints_div(log_entry_id)
						
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
	
	function calculate_efficiency(log_entry_id)
	{
		var expected_miles = $("#temp_ca_exp_miles_"+log_entry_id).val();
		var actual_miles = $("#temp_ca_actual_miles_"+log_entry_id).val();
		
		//alert(expected_miles);
		//alert(actual_miles);
		//alert(!isNaN(actual_miles));
		//alert(!isNaN(expected_miles));

		if(actual_miles&&expected_miles&&!isNaN(actual_miles)&&!isNaN(expected_miles)&&expected_miles != 0)
		{
			//alert('calc');
			var efficiency_rating = Math.round(actual_miles/expected_miles*100);
			$("#ca_efficienty_text_"+log_entry_id).html(efficiency_rating+"%");
		}
		else
		{
			$("#temp_ca_actual_miles_"+log_entry_id).html("");
		}
	}
</script>