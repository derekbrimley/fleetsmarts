<script>
	//ADD NEW ENTRY DIALOG
	$(document).ready(function()
	{
		$( "#add_new_entry").dialog(
		{
				autoOpen: false,
				height: 485,
				width: 420,
				modal: true,
				buttons: 
					[
						{
							text: "Save",
							click: function() 
							{
								
								submit_expense_allocation();
								
							},//end save
						},
						{
							text: "Cancel",
							click: function() 
							{
								//reset_all_account_entry_inputs();
								
								$( this ).dialog( "close" );
							}//end cancel
						}
					],//end of buttons
				
				open: function()
					{
					},//end open function
				close: function() 
					{
						//reset_all_account_entry_inputs();
						
						//REMOVE ANY BLUE BORDER BOXES
						$("#tr_"+selected_expense).removeClass('blue_border');
						
						//UNCHECK CHECK BOX ON CANCEL
						$("#recorded_cb_"+selected_expense).removeAttr('checked');
					}
		});//end add new entry dialog
	});
	
	//OPEN ALLOCATION DIALOG WHEN CHECK BOX IS CLICKED
	function cb_changed(row)
	{
		$("#tr_"+row).addClass("blue_border");
		selected_expense = row;
		$('#add_new_entry').html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
		$( "#add_new_entry").dialog("open");
		load_expense_allocation_div(row);
	}
	
	//AJAX TO LOAD THE ALLOCATION DIV
	function load_expense_allocation_div(expense_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#add_new_entry');
		
		$('#add_new_entry').html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&expense_id="+expense_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/expenses/load_expense_allocation_dialog")?>", // in the quotation marks
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
	
	//EXPENSE TYPE SELECTED -- BUSIENSS EXPENSE, MEMBER EXPENSE, INVOICE PAID, INVOICE PAYEMNT RECEIVED, FUNDING PAYMENT RECEIVED, CASH TO CASH TRANSFER
	function transaction_type_selected()
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#transaction_form_div');
		
		this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		//var dataString = "&expense_id="+allocated_expense_id;
		var dataString = $("#expense_type_form").serialize();
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/expenses/transaction_type_selected")?>", // in the quotation marks
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
	
	function matching_transfer_selected()
	{
		if($("#matching_expense").val() == "No Match")
		{
			$("#corresponding_account_row").show();
		}
	}
	
	function payable_account_selected()
	{
		var isValid = true;
		//VALIDATE THAT PAYABLE_ACCOUNT IS SELECTED
		if($("#payable_account_id").val() == 'Select')
		{
			alert('You must select an Invoice Payable Account!');
			isValid = false;
		}
		
		if(isValid)
		{
			$("#invoices_paid_total_div").hide();
			total_bills_paid_amount = 0;
			
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#invoice_paid_form_div');
			this_div.show();
			this_div.html('<div style="width:25px; margin: 0 auto; margin-top:15px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			//var dataString = "&expense_id="+allocated_expense_id;
			var dataString = $("#payable_account_form").serialize();
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/expenses/payable_account_selected")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						$("#invoices_paid_total_div").show();
						total_bills_paid_amount = 0;
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
	
	function receivable_account_selected()
	{
		var isValid = true;
		//VALIDATE THAT PAYABLE_ACCOUNT IS SELECTED
		if($("#receivable_account_id").val() == 'Select')
		{
			alert('You must select an Invoice Payable Account!');
			isValid = false;
		}
		
		if(isValid)
		{
			$("#invoices_paid_total_div").hide();
			total_bills_paid_amount = 0;
			
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#invoice_payment_received_form_div');
			this_div.show();
			this_div.html('<div style="width:25px; margin: 0 auto; margin-top:15px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			//var dataString = "&expense_id="+allocated_expense_id;
			var dataString = $("#receivable_account_form").serialize();
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/expenses/receivable_account_selected")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						$("#invoices_paid_total_div").show();
						total_invoices_paid_amount = 0;
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

	function member_selected()
	{
		$("#payment_method_row").show();
	}
	
	function payment_method_selected()
	{
		$("#owner_row").show();
		$("#category_row").show();
		$("#save_to_continue_div").show();
	}
	
	
	function me_type_selected()
	{
		//alert('me_type_selected');
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#member_expense_form_div');
		this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		//var dataString = "&expense_id="+allocated_expense_id;
		var dataString = $("#me_type_form").serialize();
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/expenses/me_type_selected")?>", // in the quotation marks
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
	
	function receipt_required_selected()
	{
		if($("#receipt_required").val() == "No")
		{
			alert("This will charge the full expense amount to the driver's statement!");
		}
	}
	
	function billing_method_selected()
	{
		$("#billed_under_row").show();
	}
	
	function billed_under_selected()
	{
		//$("#finance_exp_acc_row").show(); //no longer needed, replaced with default account
		
		get_funded_loads();
		
		$("#gross_pay_row").show();
		$("#deductions_div").show();
		$("#total_deductions_row").show();
		$("#reimbursements_div").show();
		$("#total_reimbursements_row").show();
		$("#calculated_total_row").show();
		$("#cash_load_amount_row").show();
		
		funded_amount = 0;
	}
	
	function get_funded_loads()
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#funded_loads_div');
		
		this_div.html('<div style="width:25px; margin: 0 auto; margin-top:15px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:20px;" /></div>');
		$("#funded_loads_div").show();
		//alert('wait');
		//POST DATA TO PASS BACK TO CONTROLLER
		//var dataString = "&expense_id="+expense_id;
		var dataString = $("#load_payment_received_form").serialize();
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/expenses/get_funded_loads")?>", // in the quotation marks
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
	
	var current_deduction = 1;
	function add_deduction()
	{
		current_deduction = current_deduction + 1;
		
		$("#deduction_row_"+current_deduction).show();
	}
	
	var current_reimbursement = 1;
	function add_reimbursement()
	{
		current_reimbursement += 1;
		
		$("#reimbursement_row_"+current_reimbursement).show();
	}
	
	//FUNCTION FOR TOTALLING BILLS PAID
	var total_bills_paid_amount = 0;
	function paid_invoice_clicked(invoice_id)
	{
		var this_amount = $("#paid_bill_amount_"+invoice_id).val();
		if($("#paid_bill_checkbox_"+invoice_id).is(':checked'))
		{
			total_bills_paid_amount = Math.round((total_bills_paid_amount + Number(this_amount))*100)/100;
		}
		else
		{
			total_bills_paid_amount = Math.round((total_bills_paid_amount - Number(this_amount))*100)/100;
		}
		
		//alert(total_bills_paid_amount);
		$("#total_bills_paid_amount").val(addCommas(total_bills_paid_amount.toFixed(2)));
		
		compare_paid_invoices_to_cash_load_amount();
	}
	
	// //FUNCTION FOR TOTALLING BILLS PAID
	// var total_invoices_paid_amount = 0;
	// function paid_invoice_clicked(invoice_id)
	// {
		// var this_amount = $("#paid_bill_amount_"+invoice_id).val();
		// if($("#paid_bill_checkbox_"+invoice_id).is(':checked'))
		// {
			// total_bills_paid_amount = Math.round((total_bills_paid_amount + Number(this_amount))*100)/100;
		// }
		// else
		// {
			// total_bills_paid_amount = Math.round((total_bills_paid_amount - Number(this_amount))*100)/100;
		// }
		
		// //alert(total_bills_paid_amount);
		// $("#total_bills_paid_amount").val(addCommas(total_bills_paid_amount.toFixed(2)));
		
		// compare_paid_invoices_to_cash_load_amount();
	// }
	
	function onfocus_paid_invoice_amount(invoice_id)
	{
		var this_amount = $("#paid_bill_amount_"+invoice_id).val();
		if($("#paid_bill_checkbox_"+invoice_id).is(':checked'))
		{
			total_bills_paid_amount = Math.round((total_bills_paid_amount - Number(this_amount))*100)/100;
		}
	}
	
	function onblur_paid_invoice_amount(invoice_id)
	{
		var this_amount = $("#paid_bill_amount_"+invoice_id).val();
		if($("#paid_bill_checkbox_"+invoice_id).is(':checked'))
		{
			total_bills_paid_amount = Math.round((total_bills_paid_amount + Number(this_amount))*100)/100;
		}
		
		$("#total_bills_paid_amount").val(addCommas(total_bills_paid_amount.toFixed(2)));
		
		compare_paid_invoices_to_cash_load_amount();
	}
	
	function compare_paid_invoices_to_cash_load_amount()
	{
		var net_total_compare = Math.round((Number(total_bills_paid_amount))*100)/100;
		var cash_load_compare = Math.round((Number($("#hidden_cash_load_amount").val()))*100)/100;

		//alert('net_total: '+net_total_compare+' cash_load_amount: '+cash_load_compare);
		
		if(net_total_compare == cash_load_compare)
		{
			$("#total_bills_paid_amount").css("color","green");
		}
		else
		{
			$("#total_bills_paid_amount").css("color","red");
		}
	}
	
	
	
	//FUNCTIONS FOR TOTALLING FREIGHT PAYMENT RECEIVED
	var funded_amount = 0;
	function unpaid_invoice_clicked(invoice_id)
	{
		var this_amount = $("#invoice_amount_"+invoice_id).val();
		if($("#invoice_checkbox_"+invoice_id).is(':checked'))
		{
			funded_amount = Math.round((funded_amount + Number(this_amount))*100)/100;
		}
		else
		{
			funded_amount = Math.round((funded_amount - Number(this_amount))*100)/100;
		}
		
		//alert(funded_amount);
		$("#funded_amount").val(addCommas(funded_amount.toFixed(2)));
		
		compare_cash_load_amount();
	}
	
	function onfocus_load_amount(invoice_id)
	{
		var this_amount = $("#invoice_amount_"+invoice_id).val();
		if($("#invoice_checkbox_"+invoice_id).is(':checked'))
		{
			funded_amount = Math.round((funded_amount - Number(this_amount))*100)/100;
		}
	}
	
	function onblur_load_amount(invoice_id)
	{
		var this_amount = $("#invoice_amount_"+invoice_id).val();
		if($("#invoice_checkbox_"+invoice_id).is(':checked'))
		{
			funded_amount = Math.round((funded_amount + Number(this_amount))*100)/100;
		}
		
		$("#funded_amount").val(addCommas(funded_amount.toFixed(2)));
		
		compare_cash_load_amount();
	}
	
	function calc_total_deductions()
	{
		var total_deductions = 0;
		for (i = 1; i<10; i++)
		{
			total_deductions += Number($("#d_amount_"+i).val());
		}
		
		$("#total_deduction_amount").val(addCommas(total_deductions.toFixed(2)));
		
		compare_cash_load_amount();
	}
	
	function calc_total_reimbursements()
	{
		var total_reimbursements = 0;
		for (i = 1; i<10; i++)
		{
			total_reimbursements += Number($("#r_amount_"+i).val());
		}
		
		$("#total_reimbursement_amount").val(addCommas(total_reimbursements.toFixed(2)));
		
		compare_cash_load_amount();
	}
	
	function compare_cash_load_amount()
	{
		var total_deductions = 0;
		for (i = 1; i<10; i++)
		{
			total_deductions += Number($("#d_amount_"+i).val());
		}
		
		var total_reimbursements = 0;
		for (i = 1; i<10; i++)
		{
			total_reimbursements += Number($("#r_amount_"+i).val());
		}
		
		var net_total = funded_amount - total_deductions + total_reimbursements;
		
		$("#calculated_total").val(addCommas(net_total.toFixed(2)));
		
		
		var net_total_compare = Math.round((Number(net_total))*100)/100;
		var cash_load_compare = Math.round((Number($("#hidden_cash_load_amount").val()))*100)/100;

		//alert('net_total: '+net_total_compare+' cash_load_amount: '+cash_load_compare);
		
		if(net_total_compare == cash_load_compare)
		{
			$("#cash_load_amount").css("color","green");
		}
		else
		{
			$("#cash_load_amount").css("color","red");
		}
		
	}
	
			
	//VALIDATE NEW ENTRY
	function submit_expense_allocation()
	{
		var isValid = true;
		var row = $("#allocated_expense_id").val();
		
		var transaction_type = $("#transaction_type_dropdown").val();
		//alert(transaction_type);
		
		if(transaction_type == "Business Expense")
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#tr_'+row);
			
			//this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			//var dataString = "&expense_id="+allocated_expense_id;
			var dataString = $("#business_expense_form").serialize();
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/expenses/record_business_expense")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						$("#add_new_entry").dialog('close');
						$("#recorded_cb_"+selected_expense).attr('checked', true);
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
		else if(transaction_type == "Fuel Purchase")
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#tr_'+row);
			
			//this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			//var dataString = "&expense_id="+allocated_expense_id;
			var dataString = $("#business_expense_form").serialize();
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/expenses/record_fuel_purchase")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						$("#add_new_entry").dialog('close');
						$("#recorded_cb_"+selected_expense).attr('checked', true);
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
		else if(transaction_type == "Member Expense")
		{
			if($("#member_relationship_id").val() == "Select")
			{
				alert("You must select a Member!");
				isValid = false;
			}
			
			if($("#payment_method").val() == "Select")
			{
				alert("You must select a Payment Method!");
				isValid = false;
			}
			
			
			if(isValid)
			{
				// GET THE DIV IN DIALOG BOX
				var this_div = $('#tr_'+row);
				
				//this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
				
				//POST DATA TO PASS BACK TO CONTROLLER
				//var dataString = "&expense_id="+allocated_expense_id;
				var dataString = $("#business_expense_form").serialize();
				
				// AJAX!
				$.ajax({
					url: "<?= base_url("index.php/expenses/record_me_expense")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: this_div, // use a jquery object to select the result div in the view
					statusCode: {
						200: function(response){
							// Success!
							this_div.html(response);
							$("#add_new_entry").dialog('close');
							$("#recorded_cb_"+selected_expense).attr('checked', true);
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
			
		
			/**
				if($("#me_type").val() == "BA - Non-Standard")
				{
					// GET THE DIV IN DIALOG BOX
					var this_div = $('#tr_'+row);
					
					//this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
					
					//POST DATA TO PASS BACK TO CONTROLLER
					//var dataString = "&expense_id="+allocated_expense_id;
					var dataString = $("#me_ba_ns_form").serialize();
					
					// AJAX!
					$.ajax({
						url: "<?= base_url("index.php/expenses/record_ba_ns_expense")?>", // in the quotation marks
						type: "POST",
						data: dataString,
						cache: false,
						context: this_div, // use a jquery object to select the result div in the view
						statusCode: {
							200: function(response){
								// Success!
								this_div.html(response);
								$("#add_new_entry").dialog('close');
								$("#recorded_cb_"+selected_expense).attr('checked', true);
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
				else if($("#me_type").val() == "BA - Standard")
				{
					// GET THE DIV IN DIALOG BOX
					var this_div = $('#tr_'+row);
					
					//this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
					
					//POST DATA TO PASS BACK TO CONTROLLER
					//var dataString = "&expense_id="+allocated_expense_id;
					var dataString = $("#me_ba_s_form").serialize();
					
					// AJAX!
					$.ajax({
						url: "<?= base_url("index.php/expenses/record_ba_s_expense")?>", // in the quotation marks
						type: "POST",
						data: dataString,
						cache: false,
						context: this_div, // use a jquery object to select the result div in the view
						statusCode: {
							200: function(response){
								// Success!
								this_div.html(response);
								$("#add_new_entry").dialog('close');
								$("#recorded_cb_"+selected_expense).attr('checked', true);
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
				else if($("#me_type").val() == "Personal Advance")
				{
					// GET THE DIV IN DIALOG BOX
					var this_div = $('#tr_'+row);
					
					//this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
					
					//POST DATA TO PASS BACK TO CONTROLLER
					//var dataString = "&expense_id="+allocated_expense_id;
					var dataString = $("#me_pa_form").serialize();
					
					// AJAX!
					$.ajax({
						url: "<?= base_url("index.php/expenses/record_personal_advance")?>", // in the quotation marks
						type: "POST",
						data: dataString,
						cache: false,
						context: this_div, // use a jquery object to select the result div in the view
						statusCode: {
							200: function(response){
								// Success!
								this_div.html(response);
								$("#add_new_entry").dialog('close');
								$("#recorded_cb_"+selected_expense).attr('checked', true);
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
			**/
		
		
		
		}
		else if(transaction_type == "Invoice Paid")
		{
			var net_total_compare = Math.round((Number(total_bills_paid_amount))*100)/100;
			var cash_load_compare = Math.round((Number($("#hidden_cash_load_amount").val()))*100)/100;
			
			if(net_total_compare != cash_load_compare)
			{
				isValid = false;
				alert("The Calculated Total must equal the Cash Load Amount!");
			}
			
			if(isValid)
			{
				// GET THE DIV IN DIALOG BOX
				var this_div = $('#tr_'+row);
				
				//this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
				
				//POST DATA TO PASS BACK TO CONTROLLER
				//var dataString = "&expense_id="+allocated_expense_id;
				var dataString = $("#invoice_paid_form").serialize();
				
				// AJAX!
				$.ajax({
					url: "<?= base_url("index.php/expenses/record_invoice_paid")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: this_div, // use a jquery object to select the result div in the view
					statusCode: {
						200: function(response){
							// Success!
							$("#add_new_entry").dialog('close');
							$("#recorded_cb_"+selected_expense).attr('checked', true);
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
		else if(transaction_type == "Invoice Payment Received")
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#tr_'+row);
			
			//this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			//var dataString = "&expense_id="+allocated_expense_id;
			var dataString = $("#invoice_payment_received_form").serialize();
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/expenses/record_invoice_payment_received")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						$("#add_new_entry").dialog('close');
						$("#recorded_cb_"+selected_expense).attr('checked', true);
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
		else if(transaction_type == "Load Payment Received")
		{
			
			/**
			//no longer needed, replaced with default account
			if($("#finance_exp_acc").val() == "Select")
			{
				isValid = false;
				alert("Finace Exp Account must be selected!");
			}
			**/
				
			var total_deductions = 0;
			for (i = 1; i<10; i++)
			{
				total_deductions += Number($("#d_amount_"+i).val());
			}
			
			var total_reimbursements = 0;
			for (i = 1; i<10; i++)
			{
				total_reimbursements += Number($("#r_amount_"+i).val());
			}
			
			var net_total = funded_amount - total_deductions + total_reimbursements;
			
			var net_total_compare = Math.round((Number(net_total))*100)/100;
			var cash_load_compare = Math.round((Number($("#hidden_cash_load_amount").val()))*100)/100;

			if(net_total_compare != cash_load_compare)
			{
				isValid = false;
				alert("The Calculated Total must equal the Cash Load Amount!");
			}
			
			//VALIDATE THAT ACCOUNTS ARE SELECTED FOR DEDUCTIONS AND REIMBURSEMENTS
			for(i=1;i<=10;i++)
			{
				//alert($("#r_amount_"+i).val());
				if($("#d_amount_"+i).val())
				{
					if(!$("#d_notes_"+i).val())
					{
						isValid = false;
						alert("Deduction "+i+" Notes must be entered!");
					}
				}
				
				if($("#r_amount_"+i).val())
				{
					if(!$("#r_notes_"+i).val())
					{
						isValid = false;
						alert("Reimbursement "+i+" Notes must be entered!");
					}
				}
			}
			
			//isValid = false;
			if(isValid)
			{
				// GET THE DIV IN DIALOG BOX
				var this_div = $('#tr_'+row);
				
				//this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
				
				//POST DATA TO PASS BACK TO CONTROLLER
				//var dataString = "&expense_id="+allocated_expense_id;
				var dataString = $("#load_payment_received_form").serialize();
				
				//$("#add_new_entry").dialog('close');
				$("#add_new_entry").html("Processing transaction ...");
				
				// AJAX!
				$.ajax({
					url: "<?= base_url("index.php/expenses/record_load_payment_received")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: this_div, // use a jquery object to select the result div in the view
					statusCode: {
						200: function(response){
							// Success!
							$("#add_new_entry").dialog('close');
							$("#recorded_cb_"+selected_expense).attr('checked', true);
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
		else if(transaction_type == "Cash to Cash Transfer")
		{
			if($("#matching_expense").val() == "No Match")
			{
				if($("#corresponding_account").val() == "Select")
				{
					isValid = false;
					alert("You must select a Corresponding Account!");
				}
			}
			
			if(isValid)
			{
				// GET THE DIV IN DIALOG BOX
				var this_div = $('#tr_'+row);
				
				//this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
				
				//POST DATA TO PASS BACK TO CONTROLLER
				//var dataString = "&expense_id="+allocated_expense_id;
				var dataString = $("#cash_to_cash_form").serialize();
				
				// AJAX!
				$.ajax({
					url: "<?= base_url("index.php/expenses/record_cash_to_cash")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: this_div, // use a jquery object to select the result div in the view
					statusCode: {
						200: function(response){
							// Success!
							$("#add_new_entry").dialog('close');
							$("#recorded_cb_"+selected_expense).attr('checked', true);
							//this_div.html(response);
							load_report();
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
		else if(transaction_type == "Ticket Expense")
		{
			
			if($("#ticket_id").val() == "Select")
			{
				isValid = false;
				alert("You must select a Ticket!");
			}
				
			if(isValid)
			{	
				// GET THE DIV IN DIALOG BOX
				var this_div = $('#tr_'+row);
				
				//this_div.html('<div style="width:25px; margin: 0 auto; margin-top:180px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
				
				//POST DATA TO PASS BACK TO CONTROLLER
				//var dataString = "&expense_id="+allocated_expense_id;
				var dataString = $("#ticket_expense_form").serialize();
				
				// AJAX!
				$.ajax({
					url: "<?= base_url("index.php/expenses/record_ticket_expense")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: this_div, // use a jquery object to select the result div in the view
					statusCode: {
						200: function(response){
							// Success!
							this_div.html(response);
							$("#add_new_entry").dialog('close');
							$("#recorded_cb_"+selected_expense).attr('checked', true);
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
		
		
		
		
		
		
		
		
		
		
		
		
	}//end validate_new_entry
	
	
	
</script>