<script type="text/javascript">
	$(document).ready(function(){
		
		//SET HEIGHT OF MAIN CONTENT AND SCROLLABLE CONTENT
		//$("#body").height($(window).height() - 15);
		$("#main_content").height($(window).height() - 115);
		$("#scrollable_content").height($(window).height() - 195);
		$("#scrollable_left_bar").height($(window).height() - 182);
		//alert($(window).height());
		//alert($("#scrollable_content").height());
		//alert($("#main_content").height());
		
		//DIALOG: ADD NEW TRUCK
		$( "#new_equipment_dialog" ).dialog(
		{
				autoOpen: false,
				height: 580,
				width: 455,
				modal: true,
				buttons: 
					[
						{
							text: "Save",
							click: function() 
							{
								//VALIDATE ADD EQUIPMENT
								if($("#equipment_type").val() == 'Truck')
								{
									validate_add_truck();
								}
								else if($("#equipment_type").val() == 'Trailer')
								{
									validate_add_trailer();
								}
								
							},//end add load
						},
						{
							text: "Cancel",
							click: function() 
							{
								//RESIZE DIALOG BOX
								$( this ).dialog( "close" );
								
								//RESET ALL FIELDS IN DIALOG FORM
								$("#add_company_name").val(null);
								$("#add_company_short_name").val(null);
								$("#add_address").val(null);
								$("#add_city").val(null);
								$("#add_state").val(null);
								$("#add_status").val("Good");
								$("#add_zip").val(null);
								$("#add_contact").val(null);
								$("#add_email").val(null);
								$("#add_phone").val(null);
								$("#add_fax").val(null);
								$("#add_email").val(null);
								$("#add_notes").val(null);
							}
						}
					],//end of buttons
				
				open: function()
					{
						//CLEAR ALL TRUCK FIELDS
						$("#truck_number").val("");
						
						//CLEAR ALL TRAILER FIELDS
						$("#trailer_number").val("");
						
					},//end open function
				close: function() 
					{
						
					}
		});//end dialog form
		
		//DIALOG: ADD NEW TRUCK
		$( "#new_quote_dialog" ).dialog(
		{
				autoOpen: false,
				height: 300,
				width: 420,
				modal: true,
				buttons: 
					[
						{
							text: "Save",
							click: function() 
							{
								//VALIDATE ADD QUOTE
								create_new_policy();
							},//end add load
						},
						{
							text: "Cancel",
							click: function() 
							{
								//RESIZE DIALOG BOX
								$( this ).dialog( "close" );
								
								//RESET ALL FIELDS IN DIALOG FORM
							}
						}
					],//end of buttons
				
				open: function()
					{
						//LOAD NEW QUOTE DIV
						load_new_quote_dialog_div();
						
					},//end open function
				close: function() 
					{
					}
		});//end dialog form
		
		//DIALOG: UPLOAD FILE DIALOG
		$( "#file_upload_dialog" ).dialog(
		{
			autoOpen: false,
			height: 300,
			width: 450,
			modal: true,
			buttons: 
			[
				{
					text: "Upload",
					click: function() 
					{
						
						var isValid = true;
					
						//VALIDATE UPLOAD TYPE IS SELECTED
						if($("#upload_type").val() == 'Select')
						{
							isValid = false;
							alert('You must select an Upload Type!');
						}
					
						//VALIDATE ATTACHMENT NAME
						if(!$("#attachment_name").val())
						{
							isValid = false;
							alert('You must enter in an Attachment Name!');
						}
						
						//VALIDATE FILE CHOOSER
						if(!$("#equipment_attachment_file").val())
						{
							isValid = false;
							alert('You must choose a File!');
						}
						
						if(isValid)
						{
							//SUBMIT FORM
							$("#upload_file_form").submit();
							$( this ).dialog( "close" );
							
							setTimeout(function()
							{
								//alert($("#attachment_equipment_type").val());
								if($("#attachment_equipment_type").val() == 'truck')
								{
									//alert('load_truck_details');
									load_truck_details($("#equipment_id").val());
								}
								else if($("#attachment_equipment_type").val() == 'trailer')
								{
									//alert('load_trailer_details');
									load_trailer_details($("#equipment_id").val());
								}
							},2000);
						}
						
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
	});//END DOCUMENT READY

	ajax_pool = [];
	function abort_ajax_requests()
	{
		ajax_pool.forEach(function(request)
		{
			request.abort();
		});
	}
	
	//CREATE TRUCK ARRAY - TRUCK NUMBER
	var truck_validation_list = [
	<?php 	
			$array_string = "";
			foreach($trucks as $truck)
			{
				$truck_number = $truck['truck_number'];
				$array_string = $array_string.'"'.$truck_number.'",';
			}
			echo substr($array_string,0,-1);
	?>];
	
	//EQUIPMENT TYPE SELECTED IN ADD NEW EQUIPMENT
	function equipment_type_selected()
	{
		abort_ajax_requests();
		
		//HIDE ALL THE NEW EQUIPMENT DIVS
		$("#new_truck_div").hide();
		$("#new_trailer_div").hide();
		
		//SHOW THE SELECTED NEW EQUIPMENT DIV
		if($("#equipment_type").val() == 'Truck')
		{
			$("#new_truck_div").show();
		}
		else if($("#equipment_type").val() == 'Trailer')
		{
			$("#new_trailer_div").show();
		}
	}
	
	
	//LOAD TRUCK LIST AND SUMMARY VIEW
	function load_trucks()
	{
		abort_ajax_requests();
		
		$("#back_btn").hide();
		$("#edit_trailer").hide();
		$("#save_trailer").hide();
		$("#attachment_btn").hide();
		$("#loading_img").show();
		//alert("start of load_trucks()");
		previously_selected_truck_id = 0;
		
		//CHANGE BUTTONS
		$("#new_equipment").show();
		$("#new_quote_button").hide();
		
		//CLEAR ALL LINK STYLES
		$("#trailer_left_bar_link_div").attr('class', 'left_bar_link_div');
		$("#truck_left_bar_link_div").attr('class', 'left_bar_link_div');
		$("#insurance_left_bar_link_div").attr('class', 'left_bar_link_div');
		
		//CHANGE LINK COLOR TO SELECTED STYLE
		$("#truck_left_bar_link_div").attr('class', 'left_bar_link_div left_bar_link_selected');
		
		//AJAX TO LOAD TRUCK LIST
		load_truck_filter();
		
		//AJAX TO LOAD TRUCK LIST
		//load_truck_list();
		
		//AJAX TO LOAD TRUCK SUMMARY
		//load_truck_summary();
	}
	
	//SHOW TRUCK FILTER DIV
	function load_truck_filter()
	{
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#filter_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/load_truck_filter")?>", // in the quotation marks
			type: "POST",
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					
					//AJAX TO LOAD TRUCK LIST
					load_truck_list();
					
					//AJAX TO LOAD TRUCK SUMMARY
					//load_truck_summary();
					
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
	
	//LOAD TRUCK LIST
	var truck_list_ajax_call;
	function load_truck_list()
	{
		abort_ajax_requests();
		
		$("#back_btn").hide();
		$("#edit_truck").hide();
		$("#attachment_btn").hide();
		$("#refresh_list").hide();
		$("#loading_img").show();
		
		//AJAX TO LOAD TRUCK SUMMARY
		load_truck_summary();
	
		//LOADING ICON IN THE EQUIPMENT LIST DIV
		
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var equipment_list_div = $('#equipment_list_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#truck_filter_form").serialize();
		
		// AJAX!
		if(!(truck_list_ajax_call===undefined))
		{
			//alert('abort');
			truck_list_ajax_call.abort();
		}
		truck_list_ajax_call = $.ajax({
			url: "<?= base_url("index.php/equipment/load_truck_list")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: equipment_list_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					equipment_list_div.html(response);
					
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
	}//end load_truck_list
	
	var previously_selected_truck_id;
	//LOAD TRUCK SUMMARY
	var truck_summary_ajax_call;
	function load_truck_summary()
	{
		abort_ajax_requests();
		
		$("#back_btn").hide();
		$("#edit_truck").hide();
		$("#attachment_btn").hide();
		$("#refresh_list").hide();
		$("#loading_img").show();
		
		$("#truck_link_"+previously_selected_truck_id).css({'font-weight' : ''});
		
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//LOADING ICON IN THE MAIN CONTENT DIV
		//main_content.html('<div id="main_content_header"><span style="font-weight:bold;"></span></div><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-top:300px; margin-left:480px;" />');
		
		
		//-------------- AJAX TO LOAD TRUCK SUMMARY -------------------
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#truck_filter_form").serialize();
		
		// AJAX!
		if(!(truck_summary_ajax_call===undefined))
		{
			//alert('abort');
			truck_summary_ajax_call.abort();
		}
		truck_summary_ajax_call = $.ajax({
			url: "<?= base_url("index.php/equipment/load_truck_summary")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					main_content.show();
					$("#back_btn").show();
					$("#edit_truck").show();
					$("#attachment_btn").show();
					$("#loading_img").hide();
					//alert("load truck summary success");
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
	
	
	}//end load_truck_summary
	
	//LOAD TRUCK DETAILS
	function load_truck_details(truck_id)
	{
		abort_ajax_requests();
		
		$("#back_btn").hide();
		$("#edit_truck").hide();
		$("#save_truck").hide();
		$("#attachment_btn").hide();
		$("#refresh_list").hide();
		$("#loading_img").show();
		
		//MAKE TRUCK LINK BOLD WHEN SELECTED
		$("#truck_link_"+previously_selected_truck_id).css({'font-weight' : ''});
		$("#truck_link_"+truck_id).css({'font-weight' : 'bold'});
		previously_selected_truck_id = truck_id;
		
		//LOADING ICON IN THE MAIN CONTENT DIV
		
		
		//-------------- AJAX TO LOAD TRUCK SUMMARY -------------------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		if(!(truck_summary_ajax_call===undefined))
		{
			//alert('abort');
			truck_summary_ajax_call.abort();
		}
		var ajax_request = $.ajax({
			url: "<?= base_url("index.php/equipment/load_truck_details")?>"+"/"+truck_id, // in the quotation marks
			type: "POST",
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#back_btn").show();
					$("#edit_truck").show();
					$("#attachment_btn").show();
					$("#loading_img").hide();
					//alert("load truck details success");
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
		ajax_pool.push(ajax_request);
	
	
	}//end load_truck_details
	
	//GET CURRENT ODOMETER FOR TRUCK
	var odom_requests_array = new Array();
	function get_current_odometer_for_truck(truck_id)
	{
		//-------------- AJAX TO LOAD TRUCK SUMMARY -------------------
		// GET THE DIV IN DIALOG BOX
		var response_div = $('#odomter_td_'+truck_id);
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&truck_id="+truck_id;
		
		var ajax_request = $.ajax({
			url: "<?= base_url("index.php/equipment/get_current_odometer_for_truck")?>"+"/"+truck_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: response_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					response_div.html(response);
					// $("#back_btn").show();
					// $("#edit_truck").show();
					// $("#attachment_btn").show();
					// $("#loading_img").hide();
					//alert("load truck details success");
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
		ajax_pool.push(ajax_request);
	
	}
	
	//GET MILES TILL SERVICE
	function get_miles_till_next_service(truck_id)
	{
		//-------------- AJAX TO LOAD TRUCK SUMMARY -------------------
		// GET THE DIV IN DIALOG BOX
		var response_div = $('#miles_till_service_td_'+truck_id);
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&truck_id="+truck_id;
		
		var ajax_request = $.ajax({
			url: "<?= base_url("index.php/equipment/get_miles_till_next_service")?>"+"/"+truck_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: response_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					response_div.html(response);
					// $("#back_btn").show();
					// $("#edit_truck").show();
					// $("#attachment_btn").show();
					// $("#loading_img").hide();
					//alert("load truck details success");
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
		ajax_pool.push(ajax_request);
	}
	
	//GET INSURANCE STATUS
	function get_insurance_status(truck_id)
	{
		//-------------- AJAX TO LOAD TRUCK SUMMARY -------------------
		// GET THE DIV IN DIALOG BOX
		var response_div = $('#ins_status_'+truck_id);
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&truck_id="+truck_id;
		
		var ajax_request = $.ajax({
			url: "<?= base_url("index.php/equipment/get_insurance_status")?>"+"/"+truck_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: response_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					response_div.html(response);
					// $("#back_btn").show();
					// $("#edit_truck").show();
					// $("#attachment_btn").show();
					// $("#loading_img").hide();
					//alert("load truck details success");
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
		ajax_pool.push(ajax_request);
	}
	
	//GET INSURANCE STATUS
	function get_insurance_audit_row(truck_id)
	{
		//-------------- AJAX TO LOAD TRUCK SUMMARY -------------------
		// GET THE DIV IN DIALOG BOX
		var response_div = $('#audit_row_'+truck_id);
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&truck_id="+truck_id;
		
		var ajax_request = $.ajax({
			url: "<?= base_url("index.php/equipment/get_insurance_audit_row")?>"+"/"+truck_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: response_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//alert('done');
					response_div.html(response);
					// $("#back_btn").show();
					// $("#edit_truck").show();
					// $("#attachment_btn").show();
					// $("#loading_img").hide();
					//alert("load truck details success");
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
		ajax_pool.push(ajax_request);
	}
	
	
	//LOAD TRUCK EDIT
	function load_truck_edit(truck_edit_id)
	{
		$("#back_btn").hide();
		$("#edit_truck").hide();
		$("#attachment_btn").hide();
		$("#refresh_list").hide();
		$("#loading_img").show();
		
		//MAKE TRUCK LINK BOLD WHEN SELECTED
		$("#truck_link_"+previously_selected_truck_id).css({'font-weight' : ''});
		$("#truck_link_"+truck_edit_id).css({'font-weight' : 'bold'});
		previously_selected_truck_id = truck_edit_id;
		
		//LOADING ICON IN THE MAIN CONTENT DIV
		
		
		//-------------- AJAX TO LOAD TRUCK SUMMARY -------------------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/load_truck_edit")?>"+"/"+truck_edit_id, // in the quotation marks
			type: "POST",
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					
					//run_on_truck_edit_load();
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
	
	
	}//end load_truck_edit
	
	//LOAD TRAILER LIST AND SUMMARY VIEW
	function load_trailers()
	{
		abort_ajax_requests();
		
		$("#back_btn").hide();
		$("#edit_trailer").hide();
		$("#save_trailer").hide();
		$("#attachment_btn").hide();
		$("#loading_img").show();
		previously_selected_trailer_id = 0;
		
		//CHANGE BUTTONS
		$("#new_equipment").show();
		$("#new_quote_button").hide();
		
		//CLEAR ALL LINK STYLES
		$("#trailer_left_bar_link_div").attr('class', 'left_bar_link_div');
		$("#truck_left_bar_link_div").attr('class', 'left_bar_link_div');
		$("#insurance_left_bar_link_div").attr('class', 'left_bar_link_div');
		
		//CHANGE LINK COLOR TO SELECTED STYLE
		$("#trailer_left_bar_link_div").attr('class', 'left_bar_link_div left_bar_link_selected');
		
		//AJAX TO LOAD TRUCK LIST
		load_trailer_filter();
		
		//AJAX TO LOAD TRUCK LIST
		//load_trailer_list();
		
		//AJAX TO LOAD TRUCK SUMMARY
		//load_trailer_summary();
	}
	
	//SHOW TRUCK FILTER DIV
	function load_trailer_filter()
	{
		abort_ajax_requests();
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#filter_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/load_trailer_filter")?>", // in the quotation marks
			type: "POST",
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					
					//AJAX TO LOAD TRUCK LIST
					load_trailer_list();
					
					//AJAX TO LOAD TRUCK SUMMARY
					//load_truck_summary();
					
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
	
	//LOAD TRAILER LIST
	function load_trailer_list()
	{
		abort_ajax_requests();
		
		$("#back_btn").hide();
		$("#edit_trailer").hide();
		$("#save_trailer").hide();
		$("#attachment_btn").hide();
		$("#loading_img").show();
		
		//AJAX TO LOAD TRUCK SUMMARY
		load_trailer_summary();
		
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var equipment_list_div = $('#equipment_list_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/load_trailer_list")?>", // in the quotation marks
			type: "POST",
			cache: false,
			context: equipment_list_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					equipment_list_div.html(response);
					
					$("#loading_img").hide();
					$("#back_btn").show();
					$("#edit_trailer").show();
					$("#save_trailer").show();
					$("#attachment_btn").show();
					
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
	}//end load_trailer_list
	
	var previously_selected_trailer_id;
	//LOAD TRAILER SUMMARY
	var trailer_summary_ajax_call;
	function load_trailer_summary()
	{
		abort_ajax_requests();
		
		$("#back_btn").hide();
		$("#edit_trailer").hide();
		$("#attachment_btn").hide();
		$("#loading_img").show();
		
		//CLEAR ALL TRAILER LINK WHEN THE SUMMARY IS LOADED
		$("#trailer_link_"+previously_selected_trailer_id).attr('class', 'left_bar_link_div');
		
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//LOADING ICON IN THE MAIN CONTENT DIV
		//main_content.html('<div id="main_content_header"><span style="font-weight:bold;"></span></div><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-top:300px; margin-left:480px;" />');
		
		
		//-------------- AJAX TO LOAD TRUCK SUMMARY -------------------
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#trailer_filter_form").serialize();
		
		// AJAX!
		if(!(trailer_summary_ajax_call===undefined))
		{
			//alert('abort');
			trailer_summary_ajax_call.abort();
		}
		trailer_summary_ajax_call = $.ajax({
			url: "<?= base_url("index.php/equipment/load_trailer_summary")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					main_content.show();
					//alert(response);
					$("#back_btn").show();
					$("#edit_trailer").show();
					$("#attachment_btn").show();
					$("#loading_img").hide();
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
	
	
	}//end load_truck_summary
	
	//LOAD TRAILER DETAILS
	function load_trailer_details(trailer_id)
	{
		abort_ajax_requests();
		
		$("#back_btn").hide();
		$("#edit_trailer").hide();
		$("#save_trailer").hide();
		$("#attachment_btn").hide();
		$("#loading_img").show();
		
		//MAKE TRUCK LINK BOLD WHEN SELECTED
		$("#trailer_link_"+previously_selected_trailer_id).attr('class', 'left_bar_link_div');
		$("#trailer_link_"+trailer_id).attr('class', 'left_bar_link_div left_bar_link_selected');
		previously_selected_trailer_id = trailer_id;
		
		//LOADING ICON IN THE MAIN CONTENT DIV
		
		
		//-------------- AJAX TO LOAD TRUCK SUMMARY -------------------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/load_trailer_details")?>"+"/"+trailer_id, // in the quotation marks
			type: "POST",
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#back_btn").show();
					$("#edit_trailer").show();
					$("#save_trailer").hide();
					$("#attachment_btn").show();
					$("#loading_img").hide();
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
	
	
	}//end load_trailer_details
	
	//LOAD TRAILER EDIT
	function load_trailer_edit(trailer_id)
	{
		$("#back_btn").hide();
		$("#edit_trailer").hide();
		$("#attachment_btn").hide();
		$("#loading_img").show();
		
		//MAKE TRUCK LINK BOLD WHEN SELECTED
		//$("#trailer_link_"+previously_selected_trailer_id).attr('class', 'left_bar_link_div');
		//$("#truck_link_"+trailer_id).attr('class', 'left_bar_link_div left_bar_link_selected');
		//previously_selected_trailer_id = trailer_id;
		
		//LOADING ICON IN THE MAIN CONTENT DIV
		
		
		//-------------- AJAX TO LOAD TRUCK SUMMARY -------------------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/load_trailer_edit")?>"+"/"+trailer_id, // in the quotation marks
			type: "POST",
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					
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
	
	
	}//end load_trailer_details
	
	function open_file_upload(equipment_id,type)
	{
		//-------------- AJAX -------------------
		// GET THE DIV IN DIALOG BOX
		var target_div = $('#file_upload_dialog');
		target_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="width:25px; height:25px; margin-top:50px;" /></div>');
		$('#file_upload_dialog').dialog('open');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = 'equipment_id='+equipment_id+'&type='+type;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/load_equipment_file_upload")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: target_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					target_div.html(response);
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
	
	//VALIDATE TRAILER NUMBER TO BE UNIQUE ON NEW TRAILER
	function trailer_number_entered()
	{
		var this_div = $('#trailer_error_div');
		
		if($("#trailer_number").val())
		{
			var dataString = "&trailer_number="+$("#trailer_number").val();
			
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/validate_new_trailer_number")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						
						//alert($('#trailer_number_is_valid').val());
						
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
			this_div.html("*");
			$('#trailer_number_is_valid').val('false');
		}
	
	}//end truck_number_entered()
	
	
	var original_truck_number;
	//VALIDATE SAVE TRUCK EDIT
	function validate_save_truck()
	{
		$("#back_btn").hide();
		$("#edit_truck").hide();
		$("#save_truck").hide();
		$("#attachment_btn").hide();
		$("#loading_img").show();
		
		isValid = true;
		
		//CHECK STATUS
		if($("#edit_truck_status").val() == "Select")
		{
			isValid = false;
			alert("Status must be selected for the truck!");
			$("#loading_img").hide();
			$("#edit_truck").hide();
			$("#attachment_btn").show();
			$("#back_btn").show();
			$("#save_truck").show();
		}
		
		//CHECK TRUCK NUMBER, MAKE SURE ITS UNIQUE
		if(!$("#edit_truck_number").val())
		{
			isValid = false;
			alert("You must enter in a Truck Number!");
			$("#loading_img").hide();
			$("#edit_truck").hide();
			$("#attachment_btn").show();
			$("#back_btn").show();
			$("#save_truck").show();
		}else
		{
		
			//IF THE TRUCK NUMBER HASN'T BEEN CHANGED
			if ($("#edit_truck_number").val() != original_truck_number)
			{
				//DOES THE TRUCK ALREADY EXIST IN THE DB
				var truck_found = false;
				for (var vendor in truck_validation_list)
				{
					if($("#edit_truck_number").val() == truck_validation_list[vendor])
					{
						truck_found = true;
						break;
					}
				}
				//IF THE TRUCK ALREADY EXISTS IN THE DB
				if(truck_found)
				{
					isValid = false;
					alert("This Truck already exists in the database!");
					$("#loading_img").hide();
					$("#edit_truck").hide();
					$("#attachment_btn").show();
					$("#back_btn").show();
					$("#save_truck").show();
				}
			}
		}
		
		//CHECK RENTAL RATE
		if(!$("#edit_rental_rate").val() || isNaN($("#edit_rental_rate").val()))
		{
			alert("Rental Rate must be entered as a number!")
			isValid = false;
			$("#loading_img").hide();
			$("#edit_truck").hide();
			$("#attachment_btn").show();
			$("#back_btn").show();
			$("#save_truck").show();
		}
		
		//CHECK RENTAL RATE PERIOD
		if($("#edit_rental_rate_period").val() == "Select")
		{
			isValid = false;
			alert("Rental Period must be selected for the truck!");
			$("#loading_img").hide();
			$("#edit_truck").hide();
			$("#attachment_btn").show();
			$("#back_btn").show();
			$("#save_truck").show();
		}
		
		//CHECK RENTAL RATE
		if(!$("#edit_mileage_rate").val() || isNaN($("#edit_mileage_rate").val()))
		{
			alert("Mileage Rate must be entered as a number!")
			isValid = false;
			$("#loading_img").hide();
			$("#edit_truck").hide();
			$("#attachment_btn").show();
			$("#back_btn").show();
			$("#save_truck").show();
		}
		
		//CHECK FOR FLEET MANAGER
		if($("#edit_fm").val() == "Select")
		{
			isValid = false;
			alert("Fleet Manager must be selected for the truck!");
			$("#loading_img").hide();
			$("#edit_truck").hide();
			$("#attachment_btn").show();
			$("#back_btn").show();
			$("#save_truck").show();
		}
		
		//CHECK FOR TRAILER
		if($("#edit_trailer").val() == "Select")
		{
			isValid = false;
			alert("Pulling Trailer must be selected for the truck!");
			$("#loading_img").hide();
			$("#edit_truck").hide();
			$("#attachment_btn").show();
			$("#back_btn").show();
			$("#save_truck").show();
		}
	
		if(isValid)
		{
			var dataString = "";
			
			$("#truck_edit_form select").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#truck_edit_form input").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#truck_edit_form textarea").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var main_content = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/save_truck")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: main_content, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						main_content.html(response);
						$("#back_btn").show();
						$("#edit_truck").show();
						$("#attachment_btn").show();
						$("#save_truck").hide();
						$("#loading_img").hide();
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
			
		}//end if_isvalid()
		
		
		
	}//END VALIDATE SAVE TRUCK
	
	//VALIDATE ADD TRUCK
	function validate_add_truck()
	{
		isValid = true;
		
		//CHECK STATUS
		if($("#truck_status").val() == "Select")
		{
			isValid = false;
			alert("Status must be selected for the truck!");
		}
		
		//CHECK TRUCK NUMBER, MAKE SURE ITS UNIQUE
		if(!$("#truck_number").val())
		{
			isValid = false;
			alert("You must enter in a Truck Number!");
		}else
		{
		
			//DOES THE CUSTOMER ALREADY EXIST IN THE DB
			var truck_found = false;
			for (var truck in truck_validation_list)
			{
				if($("#truck_number").val() == truck_validation_list[truck])
				{
					truck_found = true;
					break;
				}
			}
			//IF THE TRUCK ALREADY EXISTS IN THE DB
			if(truck_found)
			{
				isValid = false;
				alert("This Truck already exists in the database!");
			}
		}
		
		//CHECK RENTAL RATE
		if(!$("#rental_rate").val() || isNaN($("#rental_rate").val()))
		{
			alert("Rental Rate must be entered as a number!")
			isValid = false;
		}
		
		//CHECK RENTAL RATE PERIOD
		if($("#rental_rate_period").val() == "Select")
		{
			isValid = false;
			alert("Rental Period must be selected for the truck!");
		}
		
		//CHECK RENTAL RATE
		if(!$("#mileage_rate").val() || isNaN($("#mileage_rate").val()))
		{
			alert("Mileage Rate must be entered as a number!")
			isValid = false;
		}
	
		//IF ALL THE INPUTS ARE VALID THEN SUBMIT AND SAVE
		if(isValid)
		{
			$('#new_equipment_dialog').dialog('close');
			
			var dataString = "";
			
			$("#add_truck_form select").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#add_truck_form input").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#add_truck_form textarea").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var main_content = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/add_truck")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: main_content, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						main_content.html(response);
						
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
	}//end validate_add_truck
	
	//VALIDATE ADD TRAILER
	function validate_add_trailer()
	{
		isValid = true;
		
		//CHECK STATUS
		if($("#trailer_status").val() == "Select")
		{
			isValid = false;
			alert("Status must be selected for the trailer!");
		}
		
		//CHECK LEASING COMPANY
		if($("#trailer_leasing_company").val() == "Select")
		{
			isValid = false;
			alert("Leasing Company must be selected for the trailer!");
		}
		
		//CHECK TRAILER NUMBER
		if(!$("#trailer_number").val())
		{
			isValid = false;
			alert("Trailer Number must be entered for this trailer!");
		}
		else
		{
			if($("#trailer_number_is_valid").val() == "false")
			{
				isValid = false;
				alert("This Trailer Number already exists in the system!");
			}
		}
		
		//CHECK RENTAL RATE
		if(!$("#trailer_rental_rate").val() || isNaN($("#trailer_rental_rate").val()))
		{
			alert("Rental Rate must be entered as a number!")
			isValid = false;
		}
		
		//CHECK RENTAL RATE PERIOD
		if($("#trailer_rental_period").val() == "Select")
		{
			isValid = false;
			alert("Rental Period must be selected for the trailer!");
		}
		
		//CHECK RENTAL RATE
		if(!$("#trailer_mileage_rate").val() || isNaN($("#trailer_mileage_rate").val()))
		{
			alert("Mileage Rate must be entered as a number!")
			isValid = false;
		}
	
		//IF ALL THE INPUTS ARE VALID THEN SUBMIT AND SAVE
		if(isValid)
		{
			$('#new_equipment_dialog').dialog('close');
			
			var dataString = "";
			
			$("#add_trailer_form select").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#add_trailer_form input").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#add_trailer_form textarea").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var main_content = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/add_trailer")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: main_content, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						main_content.html(response);
						
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
	}//end validate_add_trailer
	
	
	//LOAD INSURANCE VIEW
	function load_insurance()
	{
		abort_ajax_requests();
		
		$("#refresh_list").hide();
		$("#loading_img").show();
		$("#new_equipment").hide();
		$("#new_quote_button").show();
		
		//CLEAR ALL LINK STYLES
		$("#trailer_left_bar_link_div").attr('class', 'left_bar_link_div');
		$("#truck_left_bar_link_div").attr('class', 'left_bar_link_div');
		$("#insurance_left_bar_link_div").attr('class', 'left_bar_link_div');
	
		//CHANGE LINK COLOR TO SELECTED STYLE
		$("#insurance_left_bar_link_div").attr('class', 'left_bar_link_div left_bar_link_selected');
		
		//CLEAR EQUIPMENT LIST DIV
		$("#equipment_list_div").html("");
		load_insurance_filter();
	}
	
	//SHOW INSURANCE FILTER DIV
	function load_insurance_filter()
	{
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#filter_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/load_insurance_filter")?>", // in the quotation marks
			type: "POST",
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					//load_ins_by_unit_summary();
					load_ins_by_policy_summary();
					//AJAX TO LOAD TRUCK LIST
					//load_truck_list();
					
					//AJAX TO LOAD TRUCK SUMMARY
					//load_truck_summary();
					
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
	
	function insurance_group_by_item_selected(selection)
	{
		$('#scrollable_content').html('');
		$("#loading_img").show();
		
		$("#group_by_selection").val(selection);
		
		//CLEAR ALL LINK STYLES
		$("#unit_left_bar_link_div").attr('class', 'left_bar_link_div');
		$("#policy_left_bar_link_div").attr('class', 'left_bar_link_div');
		
		if(selection == 'units')
		{
			//CHANGE LINK COLOR TO SELECTED STYLE
			$("#unit_left_bar_link_div").attr('class', 'left_bar_link_div left_bar_link_selected');
			
			load_ins_by_unit_summary();
		}
		else if(selection == 'policies')
		{
			//CHANGE LINK COLOR TO SELECTED STYLE
			$("#policy_left_bar_link_div").attr('class', 'left_bar_link_div left_bar_link_selected');
			
			load_ins_by_policy_summary()
		}
		
	}
	
	//BACK BUTTON ON POLICY DETAILS PAGE CLICKED
	function policy_details_back_pressed()
	{
		if($("#group_by_selection").val() == "unit")
		{
			load_ins_by_unit_summary();
		}
		else if($("#group_by_selection").val() == "policy")
		{
			load_ins_by_policy_summary();
		}
	}
	
	var unit_ins_ajax_call;
	function load_ins_by_unit_summary()
	{
		$("#refresh_icon").hide();
		$("#loading_img").show();
		$("#back_btn").hide();
		$("#edit_trailer").hide();
		$("#attachment_btn").hide();
		
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//LOADING ICON IN THE MAIN CONTENT DIV
		//main_content.html('<div id="main_content_header"><span style="font-weight:bold;"></span></div><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-top:300px; margin-left:480px;" />');
		
		
		//-------------- AJAX TO LOAD TRUCK SUMMARY -------------------
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#insurance_filter_form").serialize();
		
		// AJAX!
		if(!(unit_ins_ajax_call===undefined))
		{
			//alert('abort');
			unit_ins_ajax_call.abort();
		}
		unit_ins_ajax_call = $.ajax({
			url: "<?= base_url("index.php/equipment/load_ins_by_unit_summary")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					main_content.show();
					
					$("#back_btn").show();
					$("#edit_truck").show();
					$("#attachment_btn").show();
					$("#loading_img").hide();
					$("#refresh_icon").show();
					//alert("load truck summary success");
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
	
	//LOAD INSURANCE VIEW FOR POLICY LIST
	function load_ins_by_policy_summary()
	{
		$("#refresh_icon").hide();
		$("#loading_img").show();
		$("#back_btn").hide();
		$("#edit_trailer").hide();
		$("#attachment_btn").hide();
		
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//LOADING ICON IN THE MAIN CONTENT DIV
		//main_content.html('<div id="main_content_header"><span style="font-weight:bold;"></span></div><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-top:300px; margin-left:480px;" />');
		
		
		//-------------- AJAX TO LOAD TRUCK SUMMARY -------------------
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#insurance_filter_form").serialize();
		
		// AJAX!
		if(!(unit_ins_ajax_call===undefined))
		{
			//alert('abort');
			unit_ins_ajax_call.abort();
		}
		unit_ins_ajax_call = $.ajax({
			url: "<?= base_url("index.php/equipment/load_ins_by_policy_summary")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					main_content.show();
					
					$("#back_btn").show();
					$("#edit_truck").show();
					$("#attachment_btn").show();
					$("#loading_img").hide();
					//alert("load truck summary success");
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
	
	//AJAX TO LOAD NEW QUOTE DIALOG
	function load_new_quote_dialog_div()
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#new_quote_dialog');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/load_new_quote_dialog")?>", // in the quotation marks
			type: "POST",
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
	
	//HIDE AND SHOW PROPER ROWS IN NEW QUOTE DIALOG
	function quote_or_policy_selected()
	{
		$("#policy_number_row").hide();
		$("#quote_id_row").hide();
		$("#active_since_row").hide();
		
		if($("#quote_or_policy").val() == 'Quote')
		{
			$("#quote_id_row").show();
		}
		else if($("#quote_or_policy").val() == 'Policy')
		{
			$("#policy_number_row").show();
			$("#active_since_row").show();
		}
	}
	
	//SAVE PRESSED ON NEW QUOTE DIALOG
	function create_new_policy()
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $('#new_quote_form').serialize();
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/create_new_policy")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					$('#new_quote_dialog').dialog('close');
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
	
	function refresh_policy_details(policy_id)
	{
		load_policy_details_view(policy_id,$("#snapshot_date").val());
	}
	
	//UNIT COVERAGE ROW CLICKED -- OPEN POLICY DETAILS PAGE
	function load_policy_details_view(policy_id,snapshot_date)
	{
		$("#refresh_icon").hide();
		$("#loading_img").show();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		//var dataString = $('#new_quote_form').serialize();
		var dataString = "&policy_id="+policy_id+"&snapshot_date="+snapshot_date;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/load_policy_details_view")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					$('#new_quote_dialog').dialog('close');
					//alert(response);
					
					$("#loading_img").hide();
					$("#refresh_icon").show();
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