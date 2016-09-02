<script>
	$(document).ready(function()
	{
		$("#main_content").height($(window).height() - 115);
		$("#filter_list").height($(window).height() - 213);
		
	});
	
	function download_leg_csv()
	{
		console.log("clicked");
		$("#main_content").html("");
	}
	
	
	function load_all_drivers_report()
	{
		var startDate = $("#start_date_filter");
		var endDate = $("#end_date_filter");
		
		var dataString = $("#all_drivers_form").serialize();
		var this_div = $('#main_content');
		console.log("DataString: "+dataString);
		$.ajax({
			url: "<?= base_url("index.php/reports/load_all_drivers_report")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
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
	
	//LOADS THE LEFT BAR DIV OF THE SELECTED REPORT
	function load_report()
	{
		//SHOW LOADING ICON
		$("#refresh_statement").hide();
		$("#filter_loading_icon").show();
		//$("#main_content_header").html("");
		
		if(!(fuel_report_ajax_call===undefined))
		{
			//alert('abort');
			fuel_report_ajax_call.abort();
		}
	
		report_type = $("#report_dropdown").val();
		//alert(report_type);
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#report_left_bar');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&report_type="+report_type;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/reports/load_report")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					$("#filter_loading_icon").hide();
					//$("#refresh_logs").show();
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
	
	var reefer_report_ajax_call;
	function load_reefer_report()
	{
		$("#report_loading_icon").show();
		$("#refresh_icon").hide();
		
		var startDate = $("#start_date_filter");
		var endDate = $("#end_date_filter");
		
		var dataString = $("#reefer_report_form").serialize();
		var this_div = $('#main_content');
		console.log("DataString: "+dataString);
		if(!(reefer_report_ajax_call===undefined))
		{
			//alert('abort');
			fuel_report_ajax_call.abort();
		}
		fuel_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_reefer_report")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					$("#refresh_icon").show();
					$("#report_loading_icon").hide();
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
	
	var carrier_report_ajax_call;
	function load_carrier_driver_report()
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(carrier_report_ajax_call===undefined))
		{
			//alert('abort');
			carrier_report_ajax_call.abort();
		}
		carrier_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_carrier_driver_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	//LOAD FUEL REPORT
	var fuel_report_ajax_call;
	function load_fuel_report()
	{
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(fuel_report_ajax_call===undefined))
		{
			//alert('abort');
			fuel_report_ajax_call.abort();
		}
		fuel_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_fuel_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	//LOAD FUNDING REPORT
	var funding_report_ajax_call;
	function load_funding_report()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
	
		//alert("load funding report");
		//POPULATE HIDDEN FIELDS FOR FILTER CHECK BOXES
		//GET FACTORS?
		if($('#factor_cb').attr('checked'))
		{
			$("#get_factors").val(true);
		}
		else
		{
			$("#get_factors").val(false);
		}
		
		//GET DIRECT BILLS?
		if($('#direct_bill_cb').attr('checked'))
		{
			$("#get_direct_bills").val(true);
		}
		else
		{
			$("#get_direct_bills").val(false);
		}
		
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(funding_report_ajax_call===undefined))
		{
			//alert('abort');
			funding_report_ajax_call.abort();
		}
		funding_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_funding_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	//LOAD INCOME STATEMENT
	var income_statement_ajax_call;
	function load_income_statement()
	{
		var business_user = $("#business_user_dropdown").val();
		var start_date = $("#start_date_filter").val();
		var end_date = $("#end_date_filter").val();
		var is_valid = true;
		
		if(start_date=="" || end_date=="")
		{
			is_valid = false;
		}
		if(is_valid)
		{
			//SHOW LOADING ICON
			$("#refresh_statement").hide();
			$("#filter_loading_icon").show();
			
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#filter_form").serialize();
			console.log(dataString);
			// AJAX!
			if(!(income_statement_ajax_call===undefined))
			{
				//alert('abort');
				income_statement_ajax_call.abort();
			}
			income_statement_ajax_call = $.ajax({
				url: "<?= base_url("index.php/reports/load_income_statement")?>", // in the quotation marks
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
						alert("500 error! "+response);
					}
				}
			});//END AJAX
		}
	}
	
	//LOAD FUNDING REPORT
	var missing_paperwork_report_ajax_call;
	function load_missing_paperwork_report()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
	
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(missing_paperwork_report_ajax_call===undefined))
		{
			//alert('abort');
			missing_paperwork_report_ajax_call.abort();
		}
		missing_paperwork_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_missing_paperwork_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	var pivot_table_report_ajax_call;
	//LOAD PIVOT TABLE REPORT
	function load_leg_report()
	{
		$("#main_content").html("");
		var start_date = $("#start_date_filter").val();
		var end_date = $("#end_date_filter").val();
		console.log(start_date+end_date);
		if(start_date!=''&&end_date!='')
		{
			//SHOW LOADING ICON
			$("#refresh_logs").hide();
			$("#filter_loading_icon").show();
		
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#filter_form").serialize();
			
			// AJAX!
			if(!(pivot_table_report_ajax_call===undefined))
			{
				//alert('abort');
				pivot_table_report_ajax_call.abort();
			}
			pivot_table_report_ajax_call = $.ajax({
				url: "<?= base_url("index.php/reports/load_pivot_table_report")?>", // in the quotation marks
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
						alert("500 error! "+response);
					}
				}
			});//END AJAX
		}
	}
	
	function populate_hidden_checkbox_fields()
	{
		//POPULATE HIDDEN FIELDS FOR FILTER CHECK BOXES
		//GET FACTORS?
		if($('#factor_cb').attr('checked'))
		{
			$("#get_factors").val(true);
		}
		else
		{
			$("#get_factors").val(false);
		}
	}
	
	function load_funding_report_print()
	{
		$("#filter_loading_icon").show();
		
		//alert("load funding report");
		populate_hidden_checkbox_fields();
		
		//GET DIRECT BILLS?
		if($('#direct_bill_cb').attr('checked'))
		{
			$("#get_direct_bills").val(true);
		}
		else
		{
			$("#get_direct_bills").val(false);
		}
		
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(funding_report_ajax_call===undefined))
		{
			//alert('abort');
			funding_report_ajax_call.abort();
		}
		funding_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_funding_report/print")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	function load_reimbursement_report()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/reports/load_reimbursement_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	function load_dm_report()
	{
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/reports/load_dm_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	function load_driver_accounts()
	{
		
		$("#filter_loading_icon").show();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/reports/load_driver_accounts")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	//LOAD DEDUCTION REPORT
	var deduction_report_ajax_call;
	function load_deduction_report()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
	
		//alert("load deduction report");
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(deduction_report_ajax_call===undefined))
		{
			//alert('abort');
			deduction_report_ajax_call.abort();
		}
		deduction_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_deduction_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	//LOAD DEDUCTION REPORT
	var expense_report_ajax_call;
	function load_expense_report()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
	
		//alert("load deduction report");
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(expense_report_ajax_call===undefined))
		{
			//alert('abort');
			expense_report_ajax_call.abort();
		}
		expense_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_expense_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	//LOAD DEDUCTION REPORT
	var fm_expense_report_ajax_call;
	function load_fm_expense_report()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
	
		//alert("load deduction report");
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(fm_expense_report_ajax_call===undefined))
		{
			//alert('abort');
			fm_expense_report_ajax_call.abort();
		}
		fm_expense_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_fm_expense_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	var financial_report_ajax_call;
	function load_financial_report()
	{
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(financial_report_ajax_call===undefined))
		{
			//alert('abort');
			financial_report_ajax_call.abort();
		}
		financial_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_financial_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	var arrowhead_expense_report_ajax_call;
	function load_arrowhead_expense_report()
	{
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(arrowhead_expense_report_ajax_call===undefined))
		{
			//alert('abort');
			arrowhead_expense_report_ajax_call.abort();
		}
		arrowhead_expense_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_arrowhead_expense_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	//LOAD DEDUCTION REPORT
	var time_and_attendance_report_ajax_call;
	function load_time_and_attendance_report()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
	
		//alert("load deduction report");
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(time_and_attendance_report_ajax_call===undefined))
		{
			//alert('abort');
			time_and_attendance_report_ajax_call.abort();
		}
		time_and_attendance_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_time_and_attendance_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	//LOAD TIME CLOCK REPORT
	var time_clock_report_ajax_call;
	function load_time_clock_report()
	{
		//SHOW LOADING ICON
		$("#refresh_icon").hide();
		$("#report_loading_icon").show();
	
		//alert("load deduction report");
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(time_clock_report_ajax_call===undefined))
		{
			//alert('abort');
			time_clock_report_ajax_call.abort();
		}
		time_clock_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_time_clock_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	function open_transactions(i)
	{
		var this_div = $("#tr_"+i);
		console.log(this_div);
		if(this_div.is(":visible"))
		{
			this_div.hide();
		}
		else
		{
			this_div.show();
		}
		
	}
	
	//LOAD FLEETPROTECT ACCOUNT REPORT -- SHOWS FLEET PROTECT ACCOUNT FOR SELECTED DRIVER
	function load_fleetprotect_account_report()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
	
		//alert("load deduction report");
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(fm_expense_report_ajax_call===undefined))
		{
			//alert('abort');
			fm_expense_report_ajax_call.abort();
		}
		fm_expense_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_fleetprotect_account_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
	
	//LOAD FLEETPROTECT ACCOUNT REPORT -- SHOWS FLEET PROTECT ACCOUNT FOR SELECTED DRIVER
	var driver_hold_report_ajax_call;
	function load_driver_hold_report()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
	
		//alert("load deduction report");
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(driver_hold_report_ajax_call===undefined))
		{
			//alert('abort');
			driver_hold_report_ajax_call.abort();
		}
		driver_hold_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/reports/load_driver_hold_report")?>", // in the quotation marks
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
					alert("500 error! "+response);
				}
			}
		});//END AJAX
	}
</script>