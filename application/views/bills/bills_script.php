<script>
	$(document).ready(function()
	{
		$("#main_content").height($(window).height() - 115);
		$("#scrollable_filter_div").height($(window).height() - 316);
		
		$('#after_date_filter').datepicker({ showAnim: 'blind' });
		$('#before_date_filter').datepicker({ showAnim: 'blind' });
		
		$('#new_bill_after_date_filter').datepicker({ showAnim: 'blind' });
		$('#new_bill_before_date_filter').datepicker({ showAnim: 'blind' });
		
		
		
		//CREATE NEW ACCOUNT DIALOG
		$( "#create_new_invoice").dialog(
		{
			autoOpen: false,
			height: 530,
			width: 470,
			modal: true,
			buttons: 
				[
					{
						text: "Save",
						click: function() 
						{
							validate_new_invoice_form($("#new_invoice_bill_holder_id").val());
						},
					},
					{
						text: "Close",
						click: function() 
						{
							$( this ).dialog( "close" );
						}
					}
				],//end of buttons
			
			open: function()
				{
					$('#pre_new_invoice_div').show();
					$('#new_relationship_selection_div').show();
					$('#success_div').hide();
					
					if($("#bill_holder_payer_id").val())
					{
						$("#business_user_id").val($("#bill_holder_payer_id").val());
						new_invoice_business_user_selected();
						
					}
					else
					{
						$('#payer_row').show();
					}
					
				},//end open function
			close: function() 
				{
					//HIDE ROWS
					$("#invoice_type_row").hide();
					$("#bill_type_row").hide();
					$("#payment_method_row").hide();
					$("#member_or_business_row").hide();
					$("#member_row").hide();
				
					$('#new_relationship_selection_div').html("");
				
					//RESET ALL FEILDS
					$("#business_user_id").val('Select');
					$("#action").val('Select');
					$("#new_invoice_type").val('Select');
					$("#bill_type").val('Select');
					$("#payment_method").val('Select');
					
					//alert(selected_row);
					//REMOVE ANY BLUE BORDER BOXES
					$("#tr_"+selected_row).removeClass('blue_border');
					
					//UNCHECK CHECK BOX ON CANCEL
					$("#bill_holder_cb_"+selected_row).removeAttr('checked');
					
					//CLEAR HIDDEN FIELDS WITH BILL HOLDER VALUES
					$("#bill_holder_payer_id").val("");
					$("#bill_holder_id").val("");
					
				}
		});//end add notes dialog
		
		//CREATE NEW ACCOUNT DIALOG
		$( "#view_payment").dialog(
		{
			autoOpen: false,
			height: 700,
			width: 600,
			modal: true,
			buttons: 
				[
					{
						text: "Close",
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
		});//end add notes dialog
		
		//ADD INVOICE NOTE DIALOG
		$( "#add_invoice_notes").dialog(
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
									save_note();
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
		
		//LOAD REPORT ON PAGE LOAD
		load_report();
	});
	
	
	var report_ajax_call;
	function load_report()
	{
		//RESET TOTAL FOR PAYMENT DIALOG
		payment_total = 0;
		
		//alert('load report');
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#back").hide();
		$("#filter_loading_icon").show();
		//alert('load_report');
		if($("#business_user").val() != "Select")
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#filter_form").serialize();
			
			// AJAX!
			if(!(report_ajax_call===undefined))
			{
				//alert('abort');
				report_ajax_call.abort();
			}
			report_ajax_call = $.ajax({
				url: "<?= base_url("index.php/bills/load_report")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						load_payment_view();
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
	
	function load_payment_view()
	{
		//alert("load payment view");
		//alert($("#relationship_account_id").val());
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#view_payment');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		
		// AJAX!
		if(!(report_ajax_call===undefined))
		{
			//alert('abort');
			report_ajax_call.abort();
		}
		$.ajax({
			url: "<?= base_url("index.php/bills/load_payment_view")?>", // in the quotation marks
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
	
	//LEFT BAR FILTER PAYER SELECTED
	function business_user_selected()
	{
		//alert('business_user_selected');
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		if($("#business_user").val() != "Select")
		{
			var dataString = $("#filter_form").serialize();
				
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var ajax_div = $('#relationship_dropdown_div');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/bills/business_user_selected")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: ajax_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						ajax_div.html(response);
						//ajax_div.show();
						//alert(response);
						relationship_filter_selected();
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
	
	function relationship_filter_selected()
	{
		//alert('relationship filter selected');
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		var dataString = $("#filter_form").serialize();
		//alert($("#business_user_id").val());
			
		//alert(dataString.substring(1));
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var ajax_div = $('#account_dropdown_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/bills/relationship_selected")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					ajax_div.html(response);
					load_report();
					//ajax_div.show();
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

		if($("#relationship_id").val() == "All")
		{
			$('#view_payment_button').removeClass('jq_button').addClass('jq_button_disabled');
			$('#view_payment_button').prop("disabled",true);
		}
		else
		{
			$('#view_payment_button').removeClass('jq_button_disabled').addClass('jq_button');
			$('#view_payment_button').prop("disabled",false);
		}
		
	}

	//NEW BILL - PAYER SELECTED
	function new_invoice_business_user_selected()
	{
		//HIDE ROWS
		$("#member_or_business_row").hide();
		$("#member_row").hide();
		$("#bill_type_row").hide();
		$("#new_bill_ticket_row").hide();
			
		var dataString = $("#customer_vendor_selection_form").serialize();
			
		//alert(dataString.substring(1));
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var ajax_div = $('#member_selection_table');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/bills/new_invoice_business_user_selected")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					ajax_div.html(response);
					$('#payer_row').show();
					//ajax_div.show();
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
		
		//$("#action_row").show();
	}
	
	function member_or_business_selected()
	{
		//HIDE ROWS
		$("#member_row").hide();
	
		if($("#member_or_business").val() == "Member")
		{
			$("#bill_type_row").hide();
			$("#member_row").show();
		}
		else
		{
			//alert('business');
			$("#bill_type_row").show();
		}
	}
	
	function member_selected()
	{
		$("#new_bill_ticket_row").hide();
		
		$("#payment_method_row").show();
	}
	
	function bill_type_selected()
	{
		$("#new_bill_ticket_row").hide();
		
		if($("#bill_type").val() == 'Ticket Expense')
		{
			$("#new_bill_ticket_row").show();
		}
		else
		{
			load_customer_vendor_selection_div();
		}
	}
	
	
	
	//AFTER BILL TYPE IS SELECTED
	function load_customer_vendor_selection_div()
	{
		var isValid = true;
		
		if($("#business_user_id").val() == 'Select')
		{
			isValid = false;
		}
		
		if($("#action").val() == 'Select')
		{
			isValid = false;
		}
		
		if(isValid)
		{
			$('#new_relationship_selection_div').show();
			//alert($("#member_or_business").val());
			var dataString = $("#customer_vendor_selection_form").serialize();
				
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var ajax_div = $('#new_relationship_selection_div');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/bills/load_customer_vendor_selection_div")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: ajax_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						ajax_div.html(response);
						//ajax_div.show();
						//alert(response);
						
						if($("#relationship_selected_relationship_id").val() != "Select")
						{
							relationship_selected();
						}
						else
						{
							$("#vendor_relationship_row").show();
						}
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
	
	function relationship_selected()
	{
		var isValid = true;
		
		if($("#relationship_selected_relationship_id").val() == 'Select')
		{
			isValid = false;
		}
		
		if(isValid)
		{
			$('#new_invoice_form_div').show();
			
			var dataString = $("#load_new_invoice_form").serialize();
				
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var ajax_div = $('#new_invoice_form_div');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/bills/customer_vendor_selected")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: ajax_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						ajax_div.html(response);
						$("#vendor_relationship_row").show();
						//ajax_div.show();
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
	
	function balance_sheet_account_selected()
	{
		//alert($("#action").val());
		//alert($("#new_invoice_type").val());
		//alert($("#bill_type").val());
		
		$("#income_statement_account_id_row").hide();
		$("#deposit_account_id_row").hide();
		
		if($("#member_or_business").val() == "Member")
		{
			show_more_fields();
		}
		else
		{
			if($("#bill_type").val() == "Business Expense")
			{
				$("#income_statement_account_id_row").show();
			}
			else if($("#bill_type").val() == "Deposit Requested")
			{
				$("#deposit_account_id_row").show();
			}
			else if($("#bill_type").val() == "Ticket Expense")
			{
				show_more_fields();
			}
		}
	}
	
	function show_more_fields()
	{
		//SHOW THE EDITABLE ROWS IF NEW BILL, SHOW THE NON-EDITABLE ROWS IF NEW BILL
		
		
		$("#new_invoice_date_row").show();
		$("#new_invoice_desc_row").show();
		$("#new_invoice_amount_row").show();
		$("#generate_invoice_row").show();
		$("#file_row").show();
		
		$("#new_invoice_number_row").show();
	}
	
	function validate_new_invoice_form(bill_holder_id)
	{
		var isValid = true;
		
		if(!$("#balance_sheet_id").val())
		{
			isValid = false;
			
			if($("#new_invoice_action").val() == "Generate Invoice")
			{
				alert("Receivable Account must be selected!");
			}
			else if($("#new_invoice_action").val() == "Upload Bill")
			{
				alert("Payable Account must be selected!");
			}
		}
		
		if(!$("#deposit_account_id").val())
		{
			isValid = false;
			
			if($("#new_invoice_action").val() == "Generate Invoice")
			{
				alert("Revenue Account must be selected!");
			}
			else if($("#new_invoice_action").val() == "Upload Bill")
			{
				alert("Expense Account must be selected!");
			}
		}
		
		if(!$("#new_invoice_date").val())
		{
			isValid = false;
			alert("Invoice Date must be entered!");
		}
		
		if(!$("#new_invoice_number").val())
		{
			isValid = false;
			alert("Invoice Number must be entered!");
		}
		
		if(!$("#new_invoice_desc").val())
		{
			isValid = false;
			alert("Description must be entered!");
		}
		
		if(!$("#new_invoice_amount").val())
		{
			isValid = false;
			alert("Invoice Amount must be entered!");
		}
		else
		{
			if(isNaN($("#new_invoice_amount").val()))
			{
				isValid = false;
				alert("Invoice Amount must be a number!");
			}
		}
		
		if(!$("#bill_holder_file_guid").val())
		{
			if(!$("#invoice_file").val())
			{
				isValid = false;
				alert("Document must be selected!");
			}
		}
		
		if($("#new_invoice_bill_type") == "Ticket Expense")
		{
			if(!$("#new_invoice_new_bill_ticket").val())
			{
				isValid = false;
				alert("Ticket must be selected!");
			}
		}
		
		if(isValid)
		{
			//SUBMIT FORM
			$("#final_new_invoice_form").submit();
			
			//CHANGE FILTER TO PAYER OF NEW BILL
			$("#business_user").val($("#new_invoice_business_user_id").val());
			
			//CLOSE DIALOG
			$("#create_new_invoice").dialog( "close" );
			
			//START THE REFRESH ICON
			$("#refresh_logs").hide();
			$("#back").hide();
			$("#filter_loading_icon").show();
			
			setTimeout(function()
			{
				//alert(bill_holder_id);
				if(bill_holder_id)
				{
					load_new_bills_report();
				}
				else
				{
					load_report();
				}
			},3000);
		}
	}
	
	function load_invoice_details(id)
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "invoice_id="+id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/bills/load_invoice_details")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					$("#create_new_invoice").dialog('close');
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
	
	function load_new_bill_details(id)
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "bill_holder_id="+id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/bills/load_new_bill_details")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					$("#create_new_invoice").dialog('close');
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
	
	//AJAX FOR GETTING BILLING NOTES
	function open_invoice_notes(invoice_id)
	{
		//RESET LOADING GIF
		$("#invoice_notes_ajax_div").html("");
		
		//SET NOTES_ID TO VALUE OF LOAD ID
		//$("#notes_id").val(truck_id);
		$("#invoice_id").val(invoice_id); //this is the hidden field in the add notes form
		
		//OPEN THE DIALOG BOX
		$( "#add_invoice_notes").dialog( "open" );
		
		//alert('inside ajax');
				
		// GET THE DIV IN DIALOG BOX
		var invoice_notes_ajax_div = $('#invoice_notes_ajax_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/bills/get_invoice_notes/")?>"+"/"+invoice_id, // in the quotation marks
			type: "POST",
			cache: false,
			context: invoice_notes_ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					invoice_notes_ajax_div.html(response);
					
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
	
	//VALIDATE AND SAVE BILLING NOTE
	function save_note()
	{
		var dataString = "";
		
		$("#add_invoice_note_form select").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#add_invoice_note_form input").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#add_invoice_note_form textarea").each(function() {
			//alert(this.id);
			//alert(this.value);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		//alert(dataString.substring(1));
		
		//CLEAR TEXT AREA
		$("#new_note").val("");
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var invoice_notes_ajax_div = $('#invoice_notes_ajax_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/bills/save_note")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: invoice_notes_ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					invoice_notes_ajax_div.html(response);
					$("#notes_details").html(response);
					//alert($("#invoice_id").val());
					refresh_row($("#invoice_id").val());
					
					//update_notes_td($("#billing_note_load_id").val());
					
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
		
	}//end save_note()
			
	function refresh_row(row)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#tr_'+row);
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "invoice_id="+row+"&relationship_id="+$("#relationship_id").val();
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/bills/refresh_row")?>", // in the quotation marks
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
	
	function load_bills_view(view)
	{
		$("#old_bills").css({'font-weight' : ''});
		$("#new_bills").css({'font-weight' : ''});
		
		var div_id;
		if(view == "Old Bills")
		{
			//CHANGE LIST ITEM TO BOLD
			div_id = "old_bills"
			$("#"+div_id).css({'font-weight' : 'bold'});
			
			//HIDE OTHER FILTER
			$("#new_bills_filter_div").hide();
			
			//SHOW PROPER FILTER
			$("#filter_div").show();
			
			load_report();
		}
		else if(view == "New Bills")
		{
			//CHANGE LIST ITEM TO BOLD
			div_id = "new_bills"
			$("#"+div_id).css({'font-weight' : 'bold'});
			
			//HIDE OTHER FILTER
			$("#filter_div").hide();
			
			//SHOW PROPER FILTER
			$("#new_bills_filter_div").show();
			
			load_new_bills_report();
		}
	}
	
	function load_new_bills_report()
	{
		//LOAD NEW REPORT
			//alert('load report');
			//SHOW LOADING ICON
			$("#refresh_logs").hide();
			$("#back").hide();
			$("#filter_loading_icon").show();
			
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#new_bills_filter_form").serialize();
			//var dataString = "";
			
			// AJAX!
			if(!(report_ajax_call===undefined))
			{
				//alert('abort');
				report_ajax_call.abort();
			}
			report_ajax_call = $.ajax({
				url: "<?= base_url("index.php/bills/load_new_bills_report")?>", // in the quotation marks
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
	
	var payment_total = 0;
	function cb_changed(row)
	{
		if($("#payment_approval_cb_"+row).is(":checked"))
		{
			$("#payment_view_row_"+row).show();
			payment_total = parseFloat(payment_total) + parseFloat($("#bill_amount_"+row).val());
		}
		else
		{
			$("#payment_view_row_"+row).hide();
			payment_total = parseFloat(payment_total) - parseFloat($("#bill_amount_"+row).val());
		}
		//alert(payment_total);
		
		$("#payment_total_span").html(addCommas(parseFloat(Math.round(payment_total * 100) / 100).toFixed(2)));
	}
	
	var selected_row = 0;
	function new_bill_cb_clicked(row,payer_id)
	{
		$("#tr_"+row).addClass("blue_border");
		selected_row = row;
		//$('#new_bill_dialog').html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
		//$( "#new_bill_dialog").dialog("open");
		
		//POPULATE HIDDEN FIELDS WITH BILL HOLDER VALUES
		$("#bill_holder_payer_id").val(payer_id);
		$("#bill_holder_id").val(row);
		
		$('#payer_row').hide();
		
		$('#create_new_invoice').dialog('open');
		//load_new_bill_dialog(row);
	}
	
	function load_new_bill_dialog(row)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#new_bill_dialog');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "bill_holder_id="+row;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/bills/load_new_bill_dialog")?>", // in the quotation marks
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
	
	function generate_invoice()
	{
		$("#gi_invoice_relationship_id").val($("#new_invoice_relationship_id").val());
		$("#gi_invoice_date").val($("#new_invoice_date").val());
		$("#gi_invoice_amount").val($("#new_invoice_amount").val());
		$("#gi_invoice_number").val($("#new_invoice_number").val());
		$("#gi_invoice_desc").val($("#new_invoice_desc").val());
		
		var isValid = true;
		
		
		if(!$("#gi_invoice_relationship_id").val())
		{
			alert('Customer must be selected!');
			isValid = false;
		}
		
		if(!$("#gi_invoice_date").val())
		{
			alert('Invoice Date must be selected!');
			isValid = false;
		}
		
		if(!$("#gi_invoice_amount").val())
		{
			alert('Invoice Amount must be selected!');
			isValid = false;
		}
		else
		{
			if(isNaN($("#gi_invoice_amount").val()))
			{
				isValid = false;
				alert("Invoice Amount must be a number!");
			}
		}
		
		if(!$("#gi_invoice_number").val())
		{
			alert('Invoice Number must be selected!');
			isValid = false;
		}
		
		if(!$("#gi_invoice_desc").val())
		{
			alert('Description must be selected!');
			isValid = false;
		}
		
		//IF VALID SUBMIT FORM TO GENERATE INVOICE
		if(isValid)
		{
			$("#generate_invoice_form").submit();
		}
		
	}
	
	
	
	
	
	
	
	
</script>