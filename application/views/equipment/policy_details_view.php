<script>
	$('.datepicker').datepicker({ showAnim: 'blind' });
	$('#snapshot_date').datepicker({ showAnim: 'blind' });
	
	//DIALOG: ADD NEW TRUCK
	$( "#duplicate_uc_dialog").dialog(
	{
		autoOpen: false,
		height: 300,
		width: 420,
		modal: true,
		buttons: 
			[
				{
					text: "Duplicate",
					click: function() 
					{
						//VALIDATE ADD QUOTE
						validate_duplicate_uc();
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
				//RESET ALL FIELDS
				
			},//end open function
		close: function() 
			{
				//RESET ALL DIV
				$("#duplicate_uc_dialog").html("Loading...");
			}
	});//end dialog form

	//SET HEIGHT OF MAIN CONTENT AND SCROLLABLE CONTENT
	$("#main_content").height($(window).height() - 115);
	$("#scrollable_content").height($("#main_content").height() - 40);
	
	//EDIT PROFILE BUTTON CLICKED -- CHANGE TO EDIT VIEW
	function edit_policy()
	{
		//$("#edit_profile_icon").hide();
		//$("#save_profile_icon").show();
		
		$('.policy_edit').css({"display":"block"});
		$('.policy_details').css({"display":"none"});
	}
	
	//POLICY BACK BUTTON CLICKED -- CHANGE TO EDIT VIEW
	function po_back()
	{
		//$("#edit_profile_icon").hide();
		//$("#save_profile_icon").show();
		
		$('.policy_details').css({"display":"block"});
		$('.policy_edit').css({"display":"none"});
	}
	
	//SAVE PROFILE EDIT
	function save_policy()
	{
		//alert('save');
		
		//VALIDATE ALL THE PROFILE INPUTS
		var isValid = true;
	
		if(!$("#current_since_date").val())
		{
			isValid = false;
			alert('Current Since date must be entered!');
		}
	
		if(isValid)
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#ajax_script_response');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $('#policy_edit_form').serialize();
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/update_ins_policy")?>", // in the quotation marks
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
						//refresh_policy_details('<?=$policy_id?>');
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
	
	//EDIT PROFILE BUTTON CLICKED -- CHANGE TO EDIT VIEW
	function edit_profile(ins_profile_id)
	{
		//$("#edit_profile_icon").hide();
		//$("#save_profile_icon").show();
		
		$('.profile_edit').css({"display":"block"});
		$('.profile_details').css({"display":"none"});
	}
	
	//PROFILE BACK BUTTON CLICKED -- CHANGE TO EDIT VIEW
	function pr_back(ins_profile_id)
	{
		//$("#edit_profile_icon").hide();
		//$("#save_profile_icon").show();
		
		$('.profile_details').css({"display":"block"});
		$('.profile_edit').css({"display":"none"});
	}
	
	//SAVE PROFILE EDIT
	function save_profile(ins_profile_id)
	{
		//alert('save');
		
		//VALIDATE ALL THE PROFILE INPUTS
		var isValid = true;
	
		if(!$("#profile_current_since_date").val())
		{
			isValid = false;
			alert('Current Since date must be entered!');
		}

		if(isValid)
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#ajax_script_response');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $('#profile_edit_form').serialize();
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/save_ins_profile")?>", // in the quotation marks
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
						//refresh_policy_details('<?=$policy_id?>');
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
	
	//OPEN CANCEL POLICY DIALOG AND SET POLICY ID VALUE
	function open_cancel_dialog(policy_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#cancel_policy_dialog');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&policy_id="+policy_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/load_cancel_dialog")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: 
			{
				200: function(response){
					// Success!
					this_div.html(response);
					$('#cancel_policy_dialog').dialog('open');
					//alert(response);
					//refresh_policy_details('<?=$policy_id?>');
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
	
	//CANCEL PROFILE
	function cancel_policy()
	{
		//alert('save');
		
		//VALIDATE ALL THE PROFILE INPUTS
		var isValid = true;
	
		if(!$("#cancel_date").val())
		{
			isValid = false;
			alert('Cancel Date must be entered!');
		}
	
		if(isValid)
		{
			$('#cancel_policy_dialog').dialog('close');
			
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#ajax_script_response');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $('#cancel_policy_form').serialize();
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/cancel_ins_policy")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						//alert(response);
						//refresh_policy_details('<?=$policy_id?>');
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
	
	//EDIT POLICY COVERAGE BUTTON CLICKED -- CHANGE TO EDIT VIEW
	function edit_policy_coverage(ins_profile_id)
	{
		//$("#edit_profile_icon").hide();
		//$("#save_profile_icon").show();
		
		$('.pc_edit').css({"display":"block"});
		$('.pc_details').css({"display":"none"});
	}
	
	//POLICY COVERAGE BACK BUTTON CLICKED -- CHANGE TO EDIT VIEW
	function pc_back(ins_profile_id)
	{
		//$("#edit_profile_icon").hide();
		//$("#save_profile_icon").show();
		
		$('.pc_details').css({"display":"block"});
		$('.pc_edit').css({"display":"none"});
	}
	
	function save_policy_coverage(ins_profile_id)
	{
		//VALIDATE ALL THE PROFILE INPUTS
		var isValid = true;
	
		if(!$("#pc_current_since_date").val())
		{
			isValid = false;
			alert('Current Since date must be entered!');
		}
		
		//VALIDATE ALL THE NUMBER INPUTS
		if(isNaN($("#term").val()))
		{
			isValid = false;
			alert("Term must be a number!");
		}
		
		if(isNaN($("#cargo_limit").val()))
		{
			isValid = false;
			alert("Cargo Limit must be a number!");
		}
		
		if(isNaN($("#cargo_ded").val()))
		{
			isValid = false;
			alert("Cargo Deductible must be a number!");
		}
		
		if(isNaN($("#cargo_prem").val()))
		{
			isValid = false;
			alert("Cargo Premium must be a number!");
		}
		
		if(isNaN($("#rbd_limit").val()))
		{
			isValid = false;
			alert("Reefer Breakdown Limit must be a number!");
		}
		
		if(isNaN($("#rbd_ded").val()))
		{
			isValid = false;
			alert("Reefer Breakdown Deductible must be a number!");
		}
		
		if(isNaN($("#rbd_prem").val()))
		{
			isValid = false;
			alert("Reefer Breakdown Premium must be a number!");
		}
		
		if(isNaN($("#fees").val()))
		{
			isValid = false;
			alert("Fees must be a number!");
		}
		
		if(isNaN($("#total_cost").val()))
		{
			isValid = false;
			alert("Total Cost must be a number!");
		}
		
		

		if(isValid)
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#ajax_script_response');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $('#pc_edit_form').serialize();
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/save_ins_policy_coverage")?>", // in the quotation marks
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
						//refresh_policy_details('<?=$policy_id?>');
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
	
	//EDIT PROFILE BUTTON CLICKED -- CHANGE TO EDIT VIEW
	function edit_unit_coverage(uc_id)
	{
		
		$('.uc_edit_'+uc_id).css({"display":"block"});
		$('.uc_details_'+uc_id).css({"display":"none"});
		
		$("#trailer_id_"+uc_id).hide();
		$("#truck_id_"+uc_id).hide();
		//alert($("#unit_type_"+uc_id).val());
		if($("#unit_type_"+uc_id).val() == "Truck")
		{
			$("#truck_id_"+uc_id).show();
		}
		else if($("#unit_type_"+uc_id).val() == "Trailer")
		{
			$("#trailer_id_"+uc_id).show();
		}
		
	}
	
	//UNIT COVERAGE BACK BUTTON CLICKED -- CHANGE TO DETAILS VIEW
	function uc_back(uc_id)
	{
		$('.uc_details_'+uc_id).css({"display":"block"});
		$('.uc_edit_'+uc_id).css({"display":"none"});
	}
	
	function save_unit_coverage(uc_id)
	{
		//VALIDATE ALL THE PROFILE INPUTS
		var isValid = true;
	
		if(!$("#current_since_date_"+uc_id).val())
		{
			isValid = false;
			alert('Current Since date must be entered!');
		}
		
		//VALIDATE ALL THE NUMBER INPUTS
		if(isNaN($("#pd_limit_"+uc_id).val()))
		{
			isValid = false;
			alert("Value must be a number!");
		}
		
		if(isNaN($("#al_um_bi_limit_"+uc_id).val()))
		{
			isValid = false;
			alert("UM BI Limit must be a number!");
		}
		
		if(isNaN($("#al_uim_bi_limit_"+uc_id).val()))
		{
			isValid = false;
			alert("UIM BI Limit must be a number!");
		}
		
		if(isNaN($("#al_pip_limit_"+uc_id).val()))
		{
			isValid = false;
			alert("PIP Limit must be a number!");
		}
		
		if(isNaN($("#al_prem_"+uc_id).val()))
		{
			isValid = false;
			alert("Liability Premium must be a number!");
		}
		
		if(isNaN($("#al_um_bi_prem_"+uc_id).val()))
		{
			isValid = false;
			alert("UM BI Premium must be a number!");
		}
		
		if(isNaN($("#al_uim_bi_prem_"+uc_id).val()))
		{
			isValid = false;
			alert("UIM BI Premium must be a number!");
		}
		
		if(isNaN($("#al_pip_prem_"+uc_id).val()))
		{
			isValid = false;
			alert("PIP Premium must be a number!");
		}
		
		if(isNaN($("#pd_comp_ded_"+uc_id).val()))
		{
			isValid = false;
			alert("Phys Dam Comp Deductible must be a number!");
		}
		
		if(isNaN($("#pd_comp_prem_"+uc_id).val()))
		{
			isValid = false;
			alert("Phys Dam Comp Prem must be a number!");
		}
		
		if(isNaN($("#pd_coll_ded_"+uc_id).val()))
		{
			isValid = false;
			alert("Phys Dam Collision Deductible must be a number!");
		}
		
		if(isNaN($("#pd_coll_prem_"+uc_id).val()))
		{
			isValid = false;
			alert("Phys Dam Collision Premium must be a number!");
		}
		
		if(isNaN($("#pd_rental_daily_limit_"+uc_id).val()))
		{
			isValid = false;
			alert("Rental Reimbursement Daily Limit must be a number!");
		}
		
		if(isNaN($("#pd_rental_max_limit_"+uc_id).val()))
		{
			isValid = false;
			alert("Rental Reimbursement Max Limit must be a number!");
		}
		
		if(isNaN($("#pd_rental_prem_"+uc_id).val()))
		{
			isValid = false;
			alert("Rental Reimbursement Premium must be a number!");
		}

		if(isValid)
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#ajax_script_response');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $('#uc_edit_form_'+uc_id).serialize();
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/save_ins_unit_coverage")?>", // in the quotation marks
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
						//refresh_policy_details('<?=$policy_id?>');
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
	
	//EDIT LISTED DRIVERS BUTTON CLICKED -- CHANGE TO EDIT VIEW
	function edit_listed_drivers()
	{
		//$("#edit_profile_icon").hide();
		//$("#save_profile_icon").show();
		
		$('.ld_edit').css({"display":"block"});
		$('.ld_details').css({"display":"none"});
	}
	
	//LISTED DRIVER BACK BUTTON CLICKED -- CHANGE TO EDIT VIEW
	function ld_back()
	{
		//$("#edit_profile_icon").hide();
		//$("#save_profile_icon").show();
		
		$('.ld_details').css({"display":"block"});
		$('.ld_edit').css({"display":"none"});
	}
	
	//ADD ADDITIONAL INSURED LINK CLICKED
	function open_additional_insured_dialog(profile_id)
	{
		var isValid = true;
	
		if(!$("#profile_current_since_date").val())
		{
			isValid = false;
			alert('Current Since date must be entered!');
		}
		
		if(isValid)
		{
			$("#add_additional_insured_dialog").dialog('open');
		}
	}
	
	//VALIDATE NEW ADDITIONAL INSURED FROM DIALOG
	function validate_new_additional_insured()
	{
		var isValid = true;
		
		if(!$("#ai_name").val())
		{
			isValid = false;
			alert("You must enter a Name!");
		}
		
		if(isValid)
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#ajax_script_response');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $('#profile_edit_form').serialize()+"&"+$('#add_ai_form').serialize();
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/add_additional_insured")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						$('#add_additional_insured_dialog').dialog('close');
						//alert(response);
						this_div.html(response);
						//refresh_policy_details('<?=$policy_id?>');
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
	
	//ADD LISTED DRIVER LINK CLICKED
	function open_listed_driver_dialog()
	{
		var isValid = true;
	
		if(!$("#ld_current_since_date").val())
		{
			isValid = false;
			alert('Current Since date must be entered!');
		}
		
		if(isValid)
		{
			$("#add_listed_driver_dialog").dialog('open');
		}
	}
	
	//VALIDATE NEW LISTED DRIVERS FROM DIALOG
	function validate_add_listed_driver()
	{
		$("#ld_client_id").val($("#ld_client_id_input").val());
		
		var isValid = true;
		
		if(!$("#ld_client_id").val())
		{
			isValid = false;
			alert("You must select a driver!");
		}
		
		if(isValid)
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#ajax_script_response');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $('#add_listed_driver_form').serialize();
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/add_listed_driver")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						$('#add_listed_driver_dialog').dialog('close');
						//alert(response);
						this_div.html(response);
						//refresh_policy_details('<?=$policy_id?>');
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
	
	
	//ADD LOSS PAYEE BUTTON CLICKED
	function open_loss_payee_dialog(uc_id)
	{
		var isValid = true;
	
		if(!$("#current_since_date_"+uc_id).val())
		{
			isValid = false;
			alert('Current Since date must be entered!');
		}
		
		if(isValid)
		{
			$("#add_loss_payee_dialog_"+uc_id).dialog('open');
		}
	}
	
	//VALIDATE NEW ADDITIONAL INSURED FROM DIALOG
	function validate_new_loss_payee(uc_id)
	{
		//alert(uc_id);
		//alert($("#lp_name_"+uc_id).val());
		
		var isValid = true;
		
		if(!$("#lp_name_"+uc_id).val())
		{
			isValid = false;
			alert("You must enter a Name!");
		}
		
		if(isValid)
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#ajax_script_response');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $('#uc_edit_form_'+uc_id).serialize()+"&"+$('#add_lp_form_'+uc_id).serialize();
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/add_loss_payee")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						$('#add_loss_payee_dialog_'+uc_id).dialog('close');
						//alert(response);
						this_div.html(response);
						//refresh_policy_details('<?=$policy_id?>');
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
	
	//CREATE NEW UNIT COVERAGE
	function add_new_unit_coverage()
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#ajax_script_response');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $('#new_uc_form').serialize();
		//alert(dataString);
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/add_new_unit_coverage")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					//this_div.html(response);
					refresh_policy_details('<?=$policy_id?>');
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
	
	//DELETE NEW UNIT COVERAGE
	function delete_unit_coverage(uc_id)
	{
		if(confirm("Are you sure you want to DELETE this unit coverage??"))
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#ajax_script_response');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = "&uc_id="+uc_id;
			//alert(dataString);
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/delete_unit_coverage")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						//this_div.html(response);
						refresh_policy_details('<?=$policy_id?>');
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
	
	//REMOVE UNIT COVERAGE FROM POLICY
	function remove_unit_coverage(uc_id)
	{
		//VALIDATE ALL THE PROFILE INPUTS
		var isValid = true;
	
		if(!$("#current_since_date_"+uc_id).val())
		{
			isValid = false;
			alert('Current Since date must be entered!');
		}
		
		if(isValid)
		{
			if(confirm("Are you sure you want to REMOVE this unit coverage??"))
			{
				// GET THE DIV IN DIALOG BOX
				var this_div = $('#ajax_script_response');
				
				//POST DATA TO PASS BACK TO CONTROLLER
				var dataString = $('#uc_edit_form_'+uc_id).serialize();
				//var dataString = "&uc_id="+uc_id;
				//alert(dataString);
				// AJAX!
				$.ajax({
					url: "<?= base_url("index.php/equipment/remove_unit_coverage")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: this_div, // use a jquery object to select the result div in the view
					statusCode: {
						200: function(response){
							// Success!
							//alert(response);
							this_div.html(response);
							//refresh_policy_details('<?=$policy_id?>');
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
	
	//DUPLICATE UC ICON CLICKED - AJAX CALL TO LOAD DIALOG
	function load_duplicate_uc_dialog(uc_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#duplicate_uc_dialog');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		//var dataString = $('#uc_edit_form_'+uc_id).serialize();
		var dataString = "&uc_id="+uc_id;
		//alert(dataString);
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/equipment/load_duplicate_uc_dialog")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					this_div.html(response);
					$("#duplicate_uc_dialog").dialog('open');
					
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
	
	// TRUCK OR TRAILER SELECTED AS UNIT TYPE IN DUPLICATE DIALOG
	function unit_type_changed_for_duplicate_dialog()
	{
		$("#duplicate_uc_truck_id").hide();
		$("#duplicate_uc_trailer_id").hide();
		
		if($("#duplicate_uc_unit_type").val() == "Truck")
		{
			$("#duplicate_uc_truck_id").show();
		}
		else if($("#duplicate_uc_unit_type").val() == "Trailer")
		{
			$("#duplicate_uc_trailer_id").show();
		}
	}
	
	//VALIDATE DUPLICATE UNIT COVERAGE DIALOG -- AJAX TO CREATE DUPLICATE
	function validate_duplicate_uc()
	{
		var isValid = true;
	
		if($("#duplicate_uc_unit_type").val() == "Truck")
		{
			if($("#duplicate_uc_truck_id").val() == "Select")
			{
				isValid = false;
				alert('Truck must be selected!');
			}
		}
		else if($("#duplicate_uc_unit_type").val() == "Trailer")
		{
			if($("#duplicate_uc_trailer_id").val() == "Select")
			{
				isValid = false;
				alert('Trailer must be selected!');
			}
		}
		else if($("#duplicate_uc_unit_type").val() == "Select")
		{
			isValid = false;
			alert('Unit Type must be selected!');
		}
		
		if(!$("#duplicate_uc_date").val())
		{
			isValid = false;
			alert('Coverage Added Date must be entered');
		}
		
		if(isValid)
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#ajax_script_response');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $('#duplicate_unit_coverage_form').serialize();
			//var dataString = "&uc_id="+uc_id;
			$('#duplicate_uc_dialog').html("Duplicating...");
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/duplicate_unit_coverage")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						this_div.html(response);
						$("#duplicate_uc_dialog").dialog('close');
						
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
<style>
	.ins_box
	{
		background-color:#CFCFCF;
	}
	
	tr
	{
		height:20px;
	}
</style>
<?php
	// //GET AGENT
	// $where = null;
	// $where["id"] = $ins_profile["agent_id"];
	// $agent_company = db_select_company($where);
	
	// //GET INSURED
	// $where = null;
	// $where["id"] = $ins_profile["insured_company_id"];
	// $insured_company = db_select_company($where);
	
	// //GET INSURER
	// $where = null;
	// $where["id"] = $ins_profile["insurer_id"];
	// $insurer_company = db_select_company($where);
	
	// //GET FG CLIENT
	// $where = null;
	// $where["id"] = $ins_profile["fg_id"];
	// $fg_client = db_select_client($where);
	
	// //GET FG PERSON
	// $where = null;
	// $where["id"] = $fg_client["company"]["person_id"];
	// $fg_person = db_select_person($where);
	
	// //GET ADDITIONAL INSURED
	// $where = null;
	// $where["ins_policy_id"] = $ins_policy["id"];
	// $where["role"] = "Additional Insured";
	// $additional_insured_players = db_select_ins_players($where);
	
	// //GET PROFILE CURRENT TILL TEXT
	// if(empty($ins_profile["profile_current_till"]))
	// {
		// $profile_current_till_text = "Current";
	// }
	// else
	// {
		// $profile_current_till_text = date("m/d/y",strtotime($ins_policy['profile_current_till']));
	// }
	
	// //GET USER WHO REQUESTED QUOTE
	// $where = null;
	// $where["id"] = $ins_policy["quoted_by_id"];
	// $quote_user = db_select_user($where);
	
?>
<div id="main_content_header">
	<span style="font-weight:bold;" title="Refresh" onclick="refresh_policy_details('<?=$policy_id?>')">Policy Details - <?=$ins_policy["policy_number"]?></span>
	<div style="float:right; width:22px;">
		<img id="refresh_icon" src="/images/refresh.png" style="display:none;cursor:pointer;float:right; margin-top:4px;height:20px;" title="Refresh" onclick="refresh_policy_details('<?=$policy_id?>')"/>
		<img id="loading_img" src="/images/loading.gif" style="float:right;margin-top:4px; height:20px;"/>
	</div>
	<img src="/images/back.png" style="cursor:pointer;float:right; margin-right:13px; margin-top:4px;height:20px;" id="back_btn" onclick="policy_details_back_pressed()" />
	<img src="/images/paper_clip2.png" style="cursor:pointer;float:right;margin-right:15px;margin-top:2px;width:11px;" id="attachment_btn" onclick="open_file_upload(<?=$ins_policy["id"]?>,'policy')" />
</div>
<div id="scrollable_content" class="scrollable_div">
	<div style="width:940px; margin:0 auto; text-align:center; margin-top:20px;">
		The following shows the profile and coverages as of 
		<input type="text" id="snapshot_date" name="snapshot_date" value="<?=date("m/d/y",strtotime($snapshot_date))?>" style=" margin-left:20px; text-align:center; width:100px; height:30px;" onchange="refresh_policy_details('<?=$policy_id?>')"/>
		<input type="text" id="snapshot_hour" name="snapshot_hour" value="<?=date("H",strtotime($snapshot_date))?>" style=" margin-left:20px; text-align:center; width:30px; height:30px;" onchange="refresh_policy_details('<?=$policy_id?>')"/> : 
		<input type="text" id="snapshot_minute" name="snapshot_minute" value="<?=date("i",strtotime($snapshot_date))?>" style="text-align:center; width:30px; height:30px;" onchange="refresh_policy_details('<?=$policy_id?>')"/>
		<hr>
	</div>
	<div id="policy_details_box">
		<?php include("application/views/equipment/insurance/policy_details_box.php"); ?>
	</div>
	<div id="change_log_box">
		<?php include("application/views/equipment/insurance/change_log_box.php"); ?>
	</div>
	<?php if($show_cancelled_view == false):?>
		<div id="profile_box">
			<?php include("application/views/equipment/insurance/profile_box.php"); ?>
		</div>
		<div id="policy_coverage_box">
			<?php include("application/views/equipment/insurance/policy_coverage_box.php"); ?>
		</div>
		<div id="listed_drivers_box">
			<?php include("application/views/equipment/insurance/listed_drivers_box.php"); ?>
		</div>
		
		<?php if(!empty($unit_coverages)):?>
			<?php foreach($unit_coverages as $unit_coverage):?>
				<div style="clear:both; margin-bottom:30px;"></div>
				<?php include("application/views/equipment/insurance/unit_coverage_box.php"); ?>
			<?php endforeach;?>
		<?php endif;?>
		
		<?php if(empty($ins_policy["policy_cancelled_date"])):?>
			<div style="clear:both; margin-bottom:30px;"></div>
			<div class="ins_box" style="padding:10px; width:920px; margin:0 auto;">
				<form id="new_uc_form" name="new_uc_form">
					<input type="hidden" id="ins_policy_id" name="ins_policy_id" value="<?=$policy_id?>"/>
					<div style="width:920px;">
						<span class="heading">Unit Coverage</span>
						<span style="float:right; position:relative; right:361px;">
						Current as of 
						<input class="datepicker" type="text" id="new_uc_current_since_date" name="current_since_date" style="text-align:center; width:70px; height:20px; font-size:12px;" placeholder="<?=date("m/d/y",strtotime($snapshot_date))?>"/>
						<?php
							$options = array();
							for($i=0; $i<=12; $i++)
							{
								if($i<10)
								{
									$minute = "0".$i;
								}
								else
								{
									$minute = $i;
								}
								$options[$minute] = $minute;
							}
						?>
						<?php echo form_dropdown("current_since_hour",$options,"00","id='new_uc_current_since_hour' class='' style='width:40px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
						<span style="margin-left:-3px; margin-right:-3;">:</span>
						<?php
							$options = array();
							for($i=0; $i<=60; $i++)
							{
								if($i<10)
								{
									$second = "0".$i;
								}
								else
								{
									$second = $i;
								}
								$options[$second] = $second;
							}
						?>
						<?php echo form_dropdown("current_since_minute",$options,"00","id='new_uc_current_since_minute' class='' style='width:40px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
					</span>
					</div>
				</form>
			</div>
			<div style="padding:10px; width:920px; margin:0 auto; margin-bottom:25px;">
				<span class="link" onclick="add_new_unit_coverage()">Add a New Unit Coverage</span>
			</div>
		<?php endif;?>
	<?php endif;?>
	<?php include("application/views/equipment/insurance/ins_attachment_box.php"); ?>
	
</div>

<div id="duplicate_uc_dialog" title="Duplicate Unit Coverage">
	Loading...
	<!-- AJAX GOES HERE! -->
</div>

<div style="display:none;" id="ajax_script_response"></div>