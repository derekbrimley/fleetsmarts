<script>
	$(document).ready(function()
	{
		$("#main_content").height($(window).height() - 115);
		$("#scrollable_filter_div").height($(window).height() - 278);
	
		//CREATE NEW ACCOUNT DIALOG
		$( "#create_new_account").dialog(
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
								validate_new_account_form();
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
						$('#pre_new_account_div').show();
						$('#success_div').hide();
					},//end open function
				close: function() 
					{
						//RESET ALL FEILDS
						$("#business_user_id").val('Select');
						$("#account_with").val('Select');
						
						$('#new_account_form_div').html("");
					}
		});//end add notes dialog
	});
	
	var report_ajax_call;
	function load_accounts()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
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
			url: "<?= base_url("index.php/accounts/load_report")?>", // in the quotation marks
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
	
	//SIMPLY OPENS THE DIALOG
	function open_new_account_dialog()
	{
		$( "#create_new_account").dialog("open");
	}
	
	//VALIDATE BUSINESS USER AND ACCOUNT WITH AS SELECTED AND LOAD NEW ACCOUNT FORM
	function load_new_account_form_div()
	{
		var isValid = true;
		
		if($("#business_user_id").val() == 'Select')
		{
			isValid = false;
		}
		
		if($("#account_with").val() == 'Select')
		{
			isValid = false;
		}
		
		if(isValid)
		{
			$('#new_account_form_div').show();
			
			var dataString = $("#pre_new_account_form").serialize();
				
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var ajax_div = $('#new_account_form_div');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/accounts/load_new_account_form")?>", // in the quotation marks
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
	
	function validate_new_account_form()
	{
		//alert("hi");
		var isValid = true;
		
		//VALIDATE ACCOUNT CLASS
		if($("#account_class").val() == 'Select')
		{
			isValid = false;
			alert("Account Class must be selected!");
		}
		
		//VALIDATE PARENT ACOUNT SELECTED AND CATEGORY INPUTTED
		if($("#account_class").val() == 'Asset')
		{
			if($("#parent_asset_account").val() == 'Select')
			{
				isValid = false;
				alert("Parent Account must be selected!");
			}
			
			if($("#asset_category").val() == 'Select')
			{
				isValid = false;
				alert("You must enter a Category for the account!");
			}
			else if(!$("#asset_category").val() == "New Category")
			{
				if(!$("#account_category").val())
				{
					isValid = false;
					alert("You must enter a Category for the account!");
				}
			}
		}
		else if($("#account_class").val() == 'Liability')
		{
			if($("#parent_liability_account").val() == 'Select')
			{
				isValid = false;
				alert("Parent Account must be selected!");
			}
			
			if($("#liability_category").val() == 'Select')
			{
				isValid = false;
				alert("You must enter a Category for the account!");
			}
			else if(!$("#liability_category").val() == "New Category")
			{
				if(!$("#account_category").val())
				{
					isValid = false;
					alert("You must enter a Category for the account!");
				}
			}
		}
		else if($("#account_class").val() == 'Revenue')
		{
			if($("#parent_revenue_account").val() == 'Select')
			{
				isValid = false;
				alert("Parent Account must be selected!");
			}
			
			if($("#revenue_category").val() == 'Select')
			{
				isValid = false;
				alert("You must enter a Category for the account!");
			}
			else if(!$("#revenue_category").val() == "New Category")
			{
				if(!$("#account_category").val())
				{
					isValid = false;
					alert("You must enter a Category for the account!");
				}
			}
		}
		else if($("#account_class").val() == 'Expense')
		{
			if($("#parent_expense_account").val() == 'Select')
			{
				isValid = false;
				alert("Parent Account must be selected!");
			}
			
			if($("#expense_category").val() == 'Select')
			{
				isValid = false;
				alert("You must enter a Category for the account!");
			}
			else if(!$("#expense_category").val() == "New Category")
			{
				if(!$("#account_category").val())
				{
					isValid = false;
					alert("You must enter a Category for the account!");
				}
			}
		}
		
		
		//VALIDATE ACCOUNT NAME IS ENTERED
		if(!$("#account_name").val())
		{
			isValid = false;
			alert("You must enter in an Account Name!");
		}
		
		//IF VALID, SUBMIT FORM AND CREATE ACCOUNT
		if(isValid)
		{
			var business_user = $("#business_user_company_id").val();
			
			$('#pre_new_account_div').hide();
			$('#new_account_form_div').hide();
			$('#success_div').show();
				
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var ajax_div = $('#new_account_form_div');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#new_account_form").serialize();
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/accounts/create_new_account")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: ajax_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						//ajax_div.html(response);
						//ajax_div.show();
						//alert(response);
						
						//SET BUSINESS USER AND RELOAD ACCOUNTS
						//alert(business_user);
						$("#business_user").val(business_user);
						
						
						$("#create_new_account").dialog("close");
						
						load_accounts();
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

	function open_sub_accounts(account_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#sub_accounts_div_'+account_id);
		if(this_div.is(":visible"))
		{
			this_div.hide();
			this_div.html('<img id="loading_icon" name="loading_icon" height="20px" src="/images/loading.gif" style="margin-left:450px; margin-top:5px;" />');
		}
		else
		{
			this_div.show();
			
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#sub_accounts_div_'+account_id);
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = "account_id="+account_id;
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/accounts/load_sub_accounts")?>", // in the quotation marks
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
	
	function load_account_details(account_id)
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "account_id="+account_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/accounts/load_account_details")?>", // in the quotation marks
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