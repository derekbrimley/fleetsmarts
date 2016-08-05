<script type="text/javascript">
	$(document).ready(function()
	{
		
		//SET HEIGHT OF MAIN CONTENT AND SCROLLABLE CONTENT
		//alert($("#body").height());
		$("#main_content").height($("#body").height() - 115);
		$("#scrollable_content").height($("#main_content").height() - 40);
		$("#scrollable_left_bar").height($("#main_content").height() - 70);
		//alert($("#main_content").height());
		
		//PLACE DATE PICKERS ON ALL THE DATE BOXES
		$('#dob_edit').datepicker({ showAnim: 'blind' });
		$('#license_expiration_edit').datepicker({ showAnim: 'blind' });
		$('#cdl_since_edit').datepicker({ showAnim: 'blind' });
		$('#ucr_edit').datepicker({ showAnim: 'blind' });
		$('#running_since_edit').datepicker({ showAnim: 'blind' });
		$('#start_date_edit').datepicker({ showAnim: 'blind' });
		$('#end_date_edit').datepicker({ showAnim: 'blind' });
		
		//DIALOG: ADD NEW TRUCK
		$( "#new_person_dialog" ).dialog(
		{
				autoOpen: false,
				height: 400,
				width: 425,
				modal: true,
				buttons: 
					[
						{
							text: "Save",
							id: "save_new_contact",
							click: function() 
							{
								validate_new_person();
							
							},//end add load
						},
						{
							text: "Cancel",
							click: function() 
							{
								//RESIZE DIALOG BOX
								$( this ).dialog( "close" );
								$("#person_type").val("Select");
						
								clear_form('add_driver_form');
								$("#add_driver_div").hide();
								
								clear_form('add_carrier_form');
								$("#add_carrier_div").hide();
								
							}
						}
					],//end of buttons
				
				open: function()
					{
						$("#save_new_contact").prop("disabled",false);
					},//end open function
				close: function() 
					{
						$("#person_type").val('Select');
						
						//RESET NEW DRIVER FIELDS
						$("#add_driver_div").hide();
						$("#driver_type").val('Select');
						$("#driver_status").val('Select');
						$("#first_name").val('');
						$("#last_name").val('');
						$("#side_bar_name").val('');
						
						//RESET ALL NEW CARRIER FIELDS
						$("#add_carrier_div").hide();
						$("#carrier_status_add").val('Select');
						$("#company_name_add").val('');
						$("#company_side_bar_name_add").val('');
						
						//RESET ALL NEW CARRIER FIELDS
						$("#add_staff_div").hide();
						$("#staff_first_name").val('');
						$("#staff_last_name").val('');
						
						
					}
		});//end dialog form
		
		//DIALOG: ADD CUSTOMER/VENDOR RELATIONSHIP TO BUSINESS USER
		$( "#add_cust_vendor_dialog" ).dialog(
		{
			autoOpen: false,
			height: 190,
			width: 450,
			modal: true,
			buttons: 
			[
				{
					text: "Submit",
					click: function() 
					{
						//VALIDATE CREATE CC ACCOUNT
						validate_add_cust_vendor();
						
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
				$("#cc_number").val("");
			},//end open function
			close: function() 
			{
			}
		});//end dialog form
		
		//DIALOG: ADD NEW CORPORATE CARD TO STAFF
		$( "#new_card_dialog" ).dialog(
		{
			autoOpen: false,
			height: 220,
			width: 450,
			modal: true,
			buttons: 
			[
				{
					text: "Submit",
					click: function() 
					{
						//VALIDATE CREATE CC ACCOUNT
						validate_add_card();
						
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
				//RESET FEILDS
				$("#new_card_account").val("Select");
				$("#card_name").val("");
				$("#last_four").val("");
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
						if(!$("#people_attachment_file").val())
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
								if($("#entity_type").val() == 'carrier')
								{
									load_carrier_details($("#entity_id").val());
								}
								else if($("#entity_type").val() == 'driver')
								{
									load_driver_details($("#entity_id").val());
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
		
	});//end document ready
	
	//"ADD FEE SETTING" LINK
	var last_visible_fee = 1;
	function add_fee_setting_link()
	{
		for (i=0;i<=10;i++)
		{
			if(last_visible_fee == i)
			{
				$("#add_fee_setting_row_"+i).show();
				last_visible_fee = i+1;
				var height = $('#scrollable_content')[0].scrollHeight;
				$('#scrollable_content').scrollTop(height);
				break;
			}
			
			if(last_visible_fee == 11)
			{
				alert("You can only add 10 additional rows before saving!");
				break;
			}
		}
	}
	
	//"ADD FEE SETTING" LINK
	var last_visible_rs = 1;
	function add_revenue_split_row()
	{
		for (i=0;i<=10;i++)
		{
			if(last_visible_rs == i)
			{
				$("#add_rs_row_"+i).show();
				last_visible_rs = i+1;
				break;
			}
			
			if(last_visible_rs == 11)
			{
				alert("You can only add 10 additional rows before saving!");
				break;
			}
		}
	}
	
	
	function delete_fee_setting_row(row_id,fee_description_id)
	{
		$("#"+row_id).hide();
		$("#"+fee_description_id).val("");
	}
	
	function cannot_delete_fee_setting()
	{
		alert("You do not have permission to delete existing fee settings");
	}
	
	//PERSON TYPE SELECTED (ADD NEW PERSON)
	function person_type_selected()
	{
		//HIDE ALL THE OTHER DIVS
		$("#add_driver_div").hide();
		$("#add_carrier_div").hide();
		$("#add_staff_div").hide();
		$("#add_insurance_agent_div").hide();
		
		if($("#person_type").val() == "Driver")
		{
			$("#add_driver_div").show();
		}
		else if($("#person_type").val() == "Carrier" | $("#person_type").val() == "Business"  | $("#person_type").val() == "customer_vendor" | $("#person_type").val() == "Broker")
		{
			$("#broker_parent_ar_account_row").hide();
			
			if($("#person_type").val() == "Broker")
			{
				$("#broker_parent_ar_account_row").show();
			}
			
			$("#add_carrier_div").show();
		}
		else if($("#person_type").val() == "Staff")
		{
			$("#add_staff_div").show();
		}
		else if($("#person_type").val() == "Insurance Agent")
		{
			$("#add_insurance_agent_div").show();
		}
	}
	
	//LOAD UPPER LIST
	function load_upper_list(people_type)
	{
		load_people_summary(people_type);
	
		//CLEAR FORMATTING FOR PEOPLE TYPE
		$("#Brokers").css({'font-weight' : ''});
		$("#Business_Users").css({'font-weight' : ''});
		$("#Carrier").css({'font-weight' : ''});
		$("#Customer_Vendors").css({'font-weight' : ''});
		$("#Main_Driver").css({'font-weight' : ''});
		$("#Fleet_Manager").css({'font-weight' : ''});
		$("#Co-Driver").css({'font-weight' : ''});
		$("#Staff").css({'font-weight' : ''});
		
		var div_id;
		if(people_type == "Broker")
		{
			div_id = "Brokers"
		}
		else if(people_type == "Business User")
		{
			div_id = "Business_Users"
		}
		else if(people_type == "Main Driver")
		{
			div_id = "Main_Driver"
		}
		else if(people_type == "Co-Driver")
		{
			div_id = "Co-Driver"
		}
		else if(people_type == "Applicant")
		{
			div_id = "Applicant"
		}
		else if(people_type == "Carrier")
		{
			div_id = "Carrier"
		}
		else if(people_type == "customer-vendor")
		{
			div_id = "Customer_Vendors"
		}
		else if(people_type == "Fleet Manager")
		{
			div_id = "Fleet_Manager"
		}
		else if(people_type == "Staff")
		{
			div_id = "Staff"
		}
		else if(people_type == "Insurance Agent")
		{
			div_id = "Insurance_Agents"
		}
		
		$("#"+div_id).css({'font-weight' : 'bold'});
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var upper_list_div = $('#upper_list_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&people_type="+people_type;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_upper_list")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: upper_list_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					var upper_response = response;
					
					//-------------- AJAX TO LOAD PEOPLE LIST ---------
					// GET THE DIV IN DIALOG BOX
					var people_list_div = $('#people_list_div');
					
					//POST DATA TO PASS BACK TO CONTROLLER
					var dataString = "&status=Active&driver_search=&people_type="+people_type;
					// AJAX!
					$.ajax({
						url: "<?= base_url("index.php/people/load_people_list")?>", // in the quotation marks
						type: "POST",
						data: dataString,
						cache: false,
						context: people_list_div, // use a jquery object to select the result div in the view
						statusCode: {
							200: function(response){
								// Success!
								upper_list_div.html(upper_response);
								people_list_div.html(response);
								
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
	}//end load_people_list
	
	
	//LOAD PEOPLE LIST
	function load_people_list(people_type)
	{
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var people_list_div = $('#people_list_div');
		var status_dropdown;
		var driver_search = "test";
		
		//alert(people_type);
		
		if(people_type == "Main Driver")
		{
			//people_type = $("#type_dropdown").val();
			
			if(people_type == 'Main Driver' || people_type == 'Co-Driver')
			{
				status_dropdown = $("#driver_status_dropdown").val();
				driver_search = $("#driver_search").val();
				
				if(driver_search)
				{
					$("#driver_status_dropdown").val('All');
				}
				
				//alert(driver_search);
				// $("#applicant_status_dropdown").hide();
				// $("#driver_status_dropdown").show();
				
			}
			// else //IF APPLICANT
			// {
				// load_people_summary(people_type);
				
				// status_dropdown = $("#driver_status_dropdown").val();
				// $("#driver_status_dropdown").hide();
				// $("#applicant_status_dropdown").show();
			// }
		}
		else
		{
			status_dropdown = $("#status_dropdown").val();
		}
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&status="+status_dropdown+"&people_type="+people_type+"&driver_search="+driver_search;
		//alert(people_type);
		//alert(status_dropdown);
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_people_list")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: people_list_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					people_list_div.html(response);
					$("#upper_list_div").show();
					$('#people_list_div').show();
					
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
	}//end load_people_list
	
	//LOAD PEOPLE SUMMARY VIEW
	function load_people_summary(people_type)
	{
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&people_type="+people_type;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_people_summary_view/")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
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
	}//end load_people_summary
	
	var last_link_id;
	//LOAD DRIVER DETAILS
	function load_driver_details(client_id)
	{
		//BOLD AND UNBOLD LEFT BAR LINKS
		$("#"+last_link_id).css({'font-weight' : ''});
		$("#"+client_id).css({'font-weight' : 'bold'});
		last_link_id = client_id;
		
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&client_id="+client_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_driver_details/")?>"+"/"+client_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
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
	}//end load_people_list
	
	//LOAD DRIVER EDIT
	function load_driver_edit(client_id)
	{
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&client_id="+client_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_driver_edit")?>", // in the quotation marks
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
	}//end load_driver_edit()
	
	//LOAD CARRIER DETAILS
	function load_carrier_details(carrier_id)
	{
		//BOLD AND UNBOLD LEFT BAR LINKS
		$("#"+last_link_id).css({'font-weight' : ''});
		$("#"+carrier_id).css({'font-weight' : 'bold'});
		last_link_id = carrier_id;
		
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&carrier_id="+carrier_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_carrier_details/")?>"+"/"+carrier_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
					
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
	}//end load_carrier_details
	
	//LOAD CARRIER DETAILS
	function load_carrier_edit(carrier_id)
	{
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&carrier_id="+carrier_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_carrier_edit")?>", // in the quotation marks
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
	}//end load_carrier_edit
	
	//LOAD FLEET MANAGER DETAILS
	function load_fleet_manager_details(company_id)
	{
		//BOLD AND UNBOLD LEFT BAR LINKS
		$("#"+last_link_id).css({'font-weight' : ''});
		$("#"+company_id).css({'font-weight' : 'bold'});
		last_link_id = company_id;
		
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&company_id="+company_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_fleet_manager_details/")?>"+"/"+company_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
					
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
	}//end load_fleet_manager_details
	
	//LOAD FLEET MANAGER EDIT
	function load_fleet_manager_edit(company_id)
	{
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&company_id="+company_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_fleet_manager_edit/")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
					
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
	}//end load_fleet_manager_details
	
	//LOAD STAFF DETAILS
	function load_staff_details(company_id)
	{
		//BOLD AND UNBOLD LEFT BAR LINKS
		$("#"+last_link_id).css({'font-weight' : ''});
		$("#"+company_id).css({'font-weight' : 'bold'});
		last_link_id = company_id;
		
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&company_id="+company_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_staff_details/")?>"+"/"+company_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
					
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
	}//end load_staff_details
	
	//LOAD STAFF EDIT
	function load_staff_edit(company_id)
	{
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&company_id="+company_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_staff_edit/")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
					
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
	}//end load_fleet_manager_details
	
	//LOAD BUSINESS USER DETAILS
	function load_business_user_details(company_id)
	{
		//BOLD AND UNBOLD LEFT BAR LINKS
		$("#"+last_link_id).css({'font-weight' : ''});
		$("#"+company_id).css({'font-weight' : 'bold'});
		last_link_id = company_id;
		
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&company_id="+company_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_business_user_details/")?>"+"/"+company_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
					
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
	}//end business_user_details
	
	//LOAD BUSINESS USER EDIT
	function load_business_user_edit(company_id)
	{
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&company_id="+company_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_business_user_edit/")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
					
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
	}//end business_user_edit
	
	
	//LOAD CUSTOMER/VENDOR DETAILS
	function load_customer_vendor_details(company_id)
	{
		//BOLD AND UNBOLD LEFT BAR LINKS
		$("#"+last_link_id).css({'font-weight' : ''});
		$("#"+company_id).css({'font-weight' : 'bold'});
		last_link_id = company_id;
		
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&company_id="+company_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_customer_vendor_details/")?>"+"/"+company_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
					
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
	}//end load_customer_vendor_details
	
	//LOAD CUSTOMER/VENDOR EDIT
	function load_customer_vendor_edit(company_id)
	{
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&company_id="+company_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_customer_vendor_edit/")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
					
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
	}//end load_customer_vendor_edit
	
	//LOAD BROKER DETAILS
	function load_broker_details(company_id)
	{
		//BOLD AND UNBOLD LEFT BAR LINKS
		$("#"+last_link_id).css({'font-weight' : ''});
		$("#"+company_id).css({'font-weight' : 'bold'});
		last_link_id = company_id;
		
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&company_id="+company_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_broker_details/")?>"+"/"+company_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
					
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
	}//end broker_details
	
	
	//LOAD BROKER EDIT
	function load_broker_edit(company_id)
	{
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&company_id="+company_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_broker_edit/")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
					
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
	}//end load_broker_edit
	
	
	//LOAD INSURANCE AGENT DETAILS -- not being used... just usingload_customer_vendor_detailsload_customer
	function load_insurance_agent_details(company_id)
	{
		//BOLD AND UNBOLD LEFT BAR LINKS
		$("#"+last_link_id).css({'font-weight' : ''});
		$("#"+company_id).css({'font-weight' : 'bold'});
		last_link_id = company_id;
		
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&company_id="+company_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_insurance_agent_details/")?>"+"/"+company_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#main_content").show();
					
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
	
	//UPDATE LIST
	function load_client_list()
	{
		//LOADING ICON IN THE EQUIPMENT LIST DIV
		
		
		//-------------- AJAX TO LOAD TRUCK LIST ---------
		// GET THE DIV IN DIALOG BOX
		var client_list_div = $('#client_list_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var driver_status = $("#driver_type_dropdown").val();
		var dataString = "&driver_type_dropdown="+driver_status;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/client_status_selected")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: client_list_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					client_list_div.html(response);
					
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
	
	function open_file_upload(company_id,person_type)
	{
		//-------------- AJAX -------------------
		// GET THE DIV IN DIALOG BOX
		var target_div = $('#file_upload_dialog');
		target_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="width:25px; height:25px; margin-top:50px;" /></div>');
		$('#file_upload_dialog').dialog('open');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = 'company_id='+company_id+'&person_type='+person_type;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_people_file_upload")?>", // in the quotation marks
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
	
	//CLEAR NEW DRIVER FORM
	function clear_form(form_name)
	{
		$("#"+form_name+" select").each(function() {
			//alert(this.id);
			$("#"+this.id).val("Select");
		});
		
		$("#"+form_name+" input").each(function() {
			//alert(this.id);
			$("#"+this.id).val("");
		});
		
		$("#"+form_name+" textarea").each(function() {
			//alert(this.id);
			$("#"+this.id).val("");
		});
	}
	
	//VALIDATE NEW PERSON
	function validate_new_person()
	{
		var isValid = true;
		
		//VALIDATE CARRIER NAME
		if($("#person_type").val() == "Select")
		{
			isValid = false;
			alert("Person Type must be selected!");
		}
		
		if($("#person_type").val() == "Driver")
		{
			//VALIDATE DRIVER TYPE
			if($("#driver_type").val() == "Select")
			{
				isValid = false;
				alert("Driver Type must be selected!");
			}
			
			//VALIDATE DRIVER STATUS
			if($("#driver_status").val() == "Select")
			{
				isValid = false;
				alert("Driver Status must be selected!");
			}
			
			//VALIDATE FIRST NAME
			if(!$("#first_name").val())
			{
				isValid = false;
				alert("First Name must be entered!");
			}
			
			//VALIDATE LAST NAME
			if(!$("#last_name").val())
			{
				isValid = false;
				alert("Last Name must be entered!");
			}
			
			//VALIDATE SIDE-BAR NAME
			if(!$("#side_bar_name").val())
			{
				isValid = false;
				alert("Driver Side-Bar Name must be entered!");
			}
			
			//VALIDATE SOCIAL SECURITY NUMBER
			$("#social").val($("#social").val().replace(/\D/g,''));
			if(!$("#social").val())
			{
				isValid = false;
				alert("Social Security Number must be entered!");
			}
			else
			{
				if(isNaN($("#social").val()))
				{
					isValid = false;
					alert("Social Security Number must be a number, no spaces or dashes!");
				}
			}
			
			//VALIDATE CONTRACT FILE UPLOAD
			if(!$("#attachment_file").val())
			{
				isValid = false;
				alert("Driver Conract must be selected!");
			}
			
			if(isValid)
			{
				
				$("#add_driver_form").submit();
				
				$("#new_person_dialog_content").hide();
				$("#saving_message").show();
				
				$("#save_new_contact").prop("disabled",true);
				
				setTimeout(function(){
					
					var dataString = "social="+$("#social").val();
					
					//alert(dataString.substring(1));
					
					//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
					// GET THE DIV IN DIALOG BOX
					var main_content = $('#main_content');
					
					//POST DATA TO PASS BACK TO CONTROLLER
					
					// AJAX!
					$.ajax({
						url: "<?= base_url("index.php/people/load_driver_details_by_social/")?>"+"/"+$("#social").val(), // in the quotation marks
						type: "POST",
						data: dataString,
						cache: false,
						context: main_content, // use a jquery object to select the result div in the view
						statusCode: {
							200: function(response){
								// Success!
								$( "#new_person_dialog" ).dialog('close');
								main_content.html(response);
								main_content.show();
								
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
					
				},5000);
				
			}//end if_isvalid()
			
		}
		else if($("#person_type").val() == "Carrier" | $("#person_type").val() == "Business" | $("#person_type").val() == "Broker"  | $("#person_type").val() == "customer_vendor")
		{
			//VALIDATE CARRIER STATUS
			if($("#carrier_status_add").val() == "Select")
			{
				isValid = false;
				alert("Carrier Status must be selected!");
			}
			
			//VALIDATE FIRST NAME
			if(!$("#company_name_add").val())
			{
				isValid = false;
				alert("Carrier Name must be entered!");
			}
			
			//VALIDATE LAST NAME
			if(!$("#company_side_bar_name_add").val())
			{
				isValid = false;
				alert("Carrier Side-Bar Name must be entered!");
			}
			
			//VALIDATE PARENT COOP A/R ACCOUNT FOR BROKERS
			if($("#person_type").val() == "Broker")
			{
				if($("#parent_ar_account").val() == "Select")
				{
					isValid = false;
					alert("Parent Coop A/R Account must be selected!");
				}
			}
			
			if(isValid)
			{
				//GET ALL THE DATA FROM THE FORM
				var dataString = "";
				
				$("#add_carrier_form select").each(function() {
					//alert(this.id);
					dataString = dataString+"&"+this.id+"="+this.value;
				});
				
				$("#add_carrier_form input").each(function() {
					//alert(this.id);
					dataString = dataString+"&"+this.id+"="+this.value;
				});
				
				$("#add_carrier_form textarea").each(function() {
					//alert(this.id);
					dataString = dataString+"&"+this.id+"="+this.value;
				});
				
				//COMPANY TYPE
				dataString = dataString+"&company_type="+$("#person_type").val();
				
				
				//alert(dataString.substring(1));
				
				//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
				// GET THE DIV IN DIALOG BOX
				var main_content = $('#main_content');
				
				//POST DATA TO PASS BACK TO CONTROLLER
				
				// AJAX!
				$.ajax({
					url: "<?= base_url("index.php/people/create_new_company")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: main_content, // use a jquery object to select the result div in the view
					statusCode: {
						200: function(response){
							// Success!
							$( "#new_person_dialog" ).dialog('close');
							main_content.html(response);
							main_content.show();
							
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
			
		}
		else if($("#person_type").val() == "Staff")
		{
			var isValid = true;
		
			//VALIDATE FIRST NAME
			if(!$("#staff_first_name").val())
			{
				isValid = false;
				alert("First Name must be entered!");
			}
			
			//VALIDATE LAST NAME
			if(!$("#staff_last_name").val())
			{
				isValid = false;
				alert("Last Name must be entered!");
			}
			
			if(isValid)
			{
				var dataString = $("#add_staff_form").serialize();
				
				//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
				// GET THE DIV IN DIALOG BOX
				var main_content = $('#main_content');
				
				//POST DATA TO PASS BACK TO CONTROLLER
				
				// AJAX!
				$.ajax({
					url: "<?= base_url("index.php/people/add_staff")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: main_content, // use a jquery object to select the result div in the view
					statusCode: {
						200: function(response){
							// Success!
							$( "#new_person_dialog" ).dialog('close');
							main_content.html(response);
							main_content.show();
							
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
		else if($("#person_type").val() == "Insurance Agent")
		{
			var isValid = true;
		
			//VALIDATE AGENCY NAME
			if(!$("#agency_name").val())
			{
				isValid = false;
				alert("Agency Name must be entered!");
			}
			
			//VALIDATE CONTACT NAME
			if(!$("#contact_name").val())
			{
				isValid = false;
				alert("Contact Name must be entered!");
			}
			
			//VALIDATE EMAIL
			if(!$("#contact_email").val())
			{
				isValid = false;
				alert("Contact Email must be entered!");
			}
			
			//VALIDATE PHONE
			if(!$("#contact_phone").val())
			{
				isValid = false;
				alert("Contact Phone must be entered!");
			}
			
			
			if(isValid)
			{
				var dataString = $("#add_insurance_agent_form").serialize();
				
				//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
				// GET THE DIV IN DIALOG BOX
				var main_content = $('#main_content');
				
				//POST DATA TO PASS BACK TO CONTROLLER
				
				// AJAX!
				$.ajax({
					url: "<?= base_url("index.php/people/add_insurance_agent")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: main_content, // use a jquery object to select the result div in the view
					statusCode: {
						200: function(response){
							// Success!
							$( "#new_person_dialog" ).dialog('close');
							//load_insurance_agents();
							main_content.html(response);
							main_content.show();
							
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
		}//end if
	}
	
	//VALIDATE SAVE DRIVER
	function validate_save_driver()
	{
		//VALIDATE DRIVER TYPE
		if($("#driver_type").val() == "Select")
		{
			isValid = false;
			alert("Driver Type must be selected!");
		}
		
		//VALIDATE DRIVER STATUS
		if($("#driver_status").val() == "Select")
		{
			isValid = false;
			alert("Driver Status must be selected!");
		}
		
		//VALIDATE FIRST NAME
		if(!$("#first_name").val())
		{
			isValid = false;
			alert("First Name must be entered!");
		}
		
		//VALIDATE LAST NAME
		if(!$("#last_name").val())
		{
			isValid = false;
			alert("Last Name must be entered!");
		}
		
		//VALIDATE SIDE-BAR NAME
		if(!$("#side_bar_name").val())
		{
			isValid = false;
			alert("Driver Side-Bar Name must be entered!");
		}
		
		if(isValid)
		{
			$( "#new_person_dialog" ).dialog('close');
			
			var dataString = "";
			
			$("#add_driver_form select").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#add_driver_form input").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#add_driver_form textarea").each(function() {
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
				url: "<?= base_url("index.php/people/add_driver")?>", // in the quotation marks
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
	}
	
	//OPEN ADD CC TO COMPANY DIALOG
	function open_add_cc_dialog(company_id)
	{
		//SET COMPANY ID FOR ADD CC FORM
		$("#add_cc_company_id").val(company_id);
		
		//OPEN DIALOG
		$('#create_cc_account').dialog('open');
		
		//alert('open dialog');
	}
	
	//ADD CREDIT CARD ACCOUNT TO VENDOR
	function validate_add_card()
	{
		//var company_id = $("#new_card_company_id").val();
		var isValid = true;
	
		//VALIDATE THE CC NUMBER
		cc_number = $("#last_four").val();
		
		if($("#new_card_account").val() == 'Select')
		{
			isValid = false;
			alert("Account must be selected!")
		}
		
		if(cc_number.length != 4)
		{
			isValid = false;
			alert("Credit Card Number must be a 4 digit number!")
		}
	
		if(isValid)
		{
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var main_content = $('#main_content');
			
			//alert($("#add_cc_company_id").val());
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#add_card").serialize();
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/people/add_card")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: main_content, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						$( "#new_card_dialog" ).dialog('close');
						main_content.html(response);
						main_content.show();
						
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
	
	//OPEN ADD CUSTOMER/VENDOR DIALOG
	function open_new_cust_vendor_dialog(company_id)
	{
		// GET THE DIV IN DIALOG BOX
		var ajax_div = $('#add_cust_vendor_dialog');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&company_id="+company_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_new_relationship_dialog")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					ajax_div.html(response);
					$('#add_cust_vendor_dialog').dialog('open');
					
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
	
	function relationship_type_selected()
	{
		//HIDE ALL ROWS
		$("#customer_vendor_row").hide();
		$("#staff_row").hide();
		$("#member_row").hide();
		
		if($("#relationship").val() == "Customer")
		{
			$("#customer_vendor_row").show();
		}
		else if($("#relationship").val() == "Vendor")
		{
			$("#customer_vendor_row").show();
		}
		else if($("#relationship").val() == "Staff")
		{
			$("#staff_row").show();
		}
		else if($("#relationship").val() == "Member")
		{
			$("#member_row").show();
		}
		else if($("#relationship").val() == "Fleet Manager")
		{
			$("#fleet_manager_row").show();
		}
	}
	
	//OPEN ADD CUSTOMER/VENDOR DIALOG
	function open_new_card_dialog(company_id)
	{
		//SET COMPANY ID FOR ADD CC FORM
		$("#new_card_company_id").val(company_id);
		
		//OPEN DIALOG
		$('#new_card_dialog').dialog('open');
		
		//alert('open dialog');
	}
	
	//VALIDATE AND SUBMIT ADDING CUSTOMER OR VENDOR TO BUSINESS USER
	function validate_add_cust_vendor()
	{
		var company_id = $("#business_id").val();
	
		var isValid = true;
		
		//VALIDATE RELATIONSHIP SELECTED
		if($("#relationship").val() == "Select")
		{
			isValid = false;
			alert("Relationship must be selected!");
		}
		
		if($("#relationship").val() == "Customer")
		{
			if($("#customer_vendor").val() == "Select")
			{
				isValid = false;
				alert("Customer/Vendor must be selected!");
			}
		}
		else if($("#relationship").val() == "Vendor")
		{
			if($("#customer_vendor").val() == "Select")
			{
				isValid = false;
				alert("Customer/Vendor must be selected!");
			}
		}
		else if($("#relationship").val() == "Staff")
		{
			if($("#staff").val() == "Select")
			{
				isValid = false;
				alert("Staff must be selected!");
			}
		}
		else if($("#relationship").val() == "Member")
		{
			if($("#member").val() == "Select")
			{
				isValid = false;
				alert("Member must be selected!");
			}
		}
		
		if(isValid)
		{
			var dataString = $("#add_customer_vendor").serialize();
				
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var main_content = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/people/add_customer_vendor")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: main_content, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						$( "#add_cust_vendor_dialog" ).dialog('close');
						//main_content.html(response);
						//main_content.show();
						load_business_user_details(company_id);
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
	
</script>