<script>
	$(document).ready(function()
	{
		$("#main_content").height($(window).height() - 115);
		$("#scrollable_filter_div").height($(window).height() - 198);
		
		load_filter();
		
		//ADD NOTES DIALOG
		$( "#add_notes").dialog(
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
									var expense_id = $("#expense_id").val();
									save_note(expense_id);
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
						//REMOVE ANY BLUE BORDER BOXES
						$("#tr_"+selected_expense).removeClass('blue_border');
					}
		});//end add notes dialog
		
		//SPLIT EXPENSE DIALOG
		$( "#split_expense_dialog").dialog(
		{
				autoOpen: false,
				height: 330,
				width: 480,
				modal: true,
				buttons: 
					[
						{
							id: "split_expense_save_button",
							text: "Save",
							click: function() 
							{
								validate_expense_split(selected_expense);
							},//end add load
						},
						{
							text: "Cancel",
							click: function() 
							{
								//CLEAR TEXT AREA
								$( this ).dialog( "close" );
							}
						}
					],//end of buttons
				
				open: function()
					{	
						$("#add_allocation").blur();
					},//end open function
				close: function() 
					{
						//RESET ALL SPLIT AMOUNT FIELDS
						for(i=1;i<=5;i++)
						{
							$("#allocation_amount_"+i).val("");
							$("#allocation_notes_"+i).val("");
							
							if(i>2)
							{
								$("#allocation_row_"+i).hide();
							}
						}
						
						//RESET EXPENSE AMOUNT
						expense_amount = 0;
						
						//RESET HTML ON TOTAL
						$("#total_allocations").html("$"+expense_amount.toFixed(2));
						
						//REMOVE ANY BLUE BORDER BOXES
						$("#tr_"+selected_expense).removeClass('blue_border');
						
						$("#split_expense_save_button").attr('disabled',false);
					}
		});//end add notes dialog
		
		//LOCK EXPENSE DIALOG
		$( "#lock_expense").dialog(
		{
				autoOpen: false,
				height: 350,
				width: 450,
				modal: true,
				buttons: 
					[
						{
							text: "Submit",
							click: function() 
							{
								
								validate_lock_expense();
								
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
						$('#lock_expense').html('<div style="width:25px; margin: 0 auto; margin-top:80px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
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
		
		//NEW TRANSACTION DIALOG
		$( "#new_transaction_dialog").dialog(
		{
				autoOpen: false,
				height: 485,
				width: 450,
				modal: true,
				buttons: 
					[
						{
							text: "Submit",
							click: function() 
							{
								alert('There is nothing to submit');
								//validate_lock_expense();
								
							},//end save
						},
						{
							text: "Cancel",
							click: function() 
							{
								reset_new_transaction_dialog();
								
								$( this ).dialog( "close" );
							}//end cancel
						}
					],//end of buttons
				
				open: function()
					{
						 reset_new_transaction_dialog();
						//$('#lock_expense').html('<div style="width:25px; margin: 0 auto; margin-top:80px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
					},//end open function
				close: function() 
					{
					}
		});//end add new entry dialog
		
	});
	
	function open_new_transaction_dialog()
	{
		$("#new_transaction_dialog").dialog('open');
	}
	
	function reset_new_transaction_dialog()
	{
	
		//HIDE ALL FIELDS
		$("#report_type_row").hide();
		$("#sp_cash_load_report_div").hide();
		$("#sparks_cc_report_div").hide();
		$("#money_code_report_div").hide();
		$("#tab_bank_div").hide();
		$("#venture_cc_report_div").hide();
		
		//RESET VALUES
		$("#new_transaction_type_dropdown").val('Select');
		$("#report_type_dropdown").val('Select');
		$("#tab_account_dropdown").val('Select');
		$("#mc_smartpay_account_dropdown").val('Select');
	}
	
	function new_transaction_type_selected()
	{
		//HIDE ALL THE ROWS
		
		
		if($("#new_transaction_type_dropdown").val() == "Transaction Upload")
		{
			$("#report_type_row").show();
		}
	
	}
	
	function report_type_selected()
	{
		//HIDE ALL THE UPLOAD DIVs
		$("#sp_cash_load_report_div").hide();
		$("#sparks_cc_report_div").hide();
		$("#money_code_report_div").hide();
		$("#tab_bank_div").hide();
		$("#venture_cc_report_div").hide();
		
		if($("#report_type_dropdown").val() == "Comdata Transaction")
		{
			$("#comdata_report_div").show();
		}
		else if($("#report_type_dropdown").val() == "SmartPay Cash Load")
		{
			$("#sp_cash_load_report_div").show();
		}
		else if($("#report_type_dropdown").val() == "Sparks CC")
		{
			$("#sparks_cc_report_div").show();
		}
		else if($("#report_type_dropdown").val() == "Money Code Use")
		{
			$("#money_code_report_div").show();
		}
		else if($("#report_type_dropdown").val() == "TAB Bank")
		{
			$("#tab_bank_div").show();
		}
		else if($("#report_type_dropdown").val() == "Venure (Main) CC")
		{
			$("#venture_cc_report_div").show();
		}
		
		
	}
	
	//VALIDATE AND SUBMIT COMDATA LOAD UPLOAD
	function submit_comdata_upload()
	{
		//VALIDATE SMARTPAY UPLOAD
		var isValid = true;
		
		if($("#comdata_account_dropdown").val() == 'Select')
		{
			isValid = false;
			alert("You must select a Comdata Account!")
		}
		
		if(isValid)
		{
			$("#comdata_upload_form").submit();
		}
	}
	
	//VALIDATE AND SUBMIT SMARTPAY CASH LOAD UPLOAD
	function submit_smartpay_upload()
	{
		//VALIDATE SMARTPAY UPLOAD
		var isValid = true;
		
		if($("#smartpay_account_dropdown").val() == 'Select')
		{
			isValid = false;
			alert("You must select a SmartPay Account!")
		}
		
		if(isValid)
		{
			$("#smartpay_upload_form").submit();
		}
	}
	
	//VALIDATE AND SUBMIT MONEY CODE REPORT UPLOAD
	function submit_money_code_upload()
	{
		//VALIDATE SMARTPAY UPLOAD
		var isValid = true;
		
		if($("#mc_smartpay_account_dropdown").val() == 'Select')
		{
			isValid = false;
			alert("You must select a SmartPay Account!")
		}
		
		if(isValid)
		{
			$("#mc_upload_form").submit();
		}
	}
	
	//VALIDATE AND SUBMIT MONEY CODE REPORT UPLOAD
	function submit_tab_upload()
	{
		//VALIDATE SMARTPAY UPLOAD
		var isValid = true;
		
		if($("#tab_account_dropdown").val() == 'Select')
		{
			isValid = false;
			alert("You must select a TAB Account!")
		}
		
		if(isValid)
		{
			$("#tab_upload_form").submit();
		}
	}
			
	
	function set_source_checkboxes()
	{
		<?php foreach($source_accounts_options as $source_account): ?>
			//GET CHECK BOX?
			if($('#<?=$source_account["account_id"]?>_cb').attr('checked'))
			{
				$("#get_<?=$source_account["account_id"]?>").val(true);
			}
			else
			{
				//alert('<?=$source_account["account_id"]?>');
				$("#get_<?=$source_account["account_id"]?>").val(false);
			}
		<?php endforeach; ?>
	}
	
	function load_filter()
	{
		//CHANGE THE BOLD
		$("#po_filter_link").css({'font-weight' : ''});
		$("#transactions_filter_link").css({'font-weight' : 'bold'});
		
		var expense_type = $("#expense_type_dropdown").val();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#filter_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&expense_type="+expense_type;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/expenses/load_unallocated_filter_div")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					
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
		
	var report_ajax_call;
	function load_report()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		var expense_type = $("#expense_type_dropdown").val();
		//if(expense_type == 'Unallocated')
		//{
			set_source_checkboxes();
		//}
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
			url: "<?= base_url("index.php/expenses/load_report")?>", // in the quotation marks
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
	
	function save_row(row)
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
	
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#tr_'+row);
		
		if($("#recorded_cb_"+row).attr('checked'))
		{
			$("#recorded_"+row).val("recorded");
		}
		else
		{
			$("#recorded_"+row).val("unrecorded");
		}
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#expense_form_"+row).serialize();
		// AJAX!
		if(!(report_ajax_call===undefined))
		{
			//alert('abort');
			report_ajax_call.abort();
		}
		report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/expenses/save_expense")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					
					//SHOW LOADING ICON
					$("#filter_loading_icon").hide();
					$("#refresh_logs").show();
					
					this_div.html(response);
					//alert(response);
					$( "#lock_expense").dialog("close");
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
	
	var selected_expense = 0;
	function split_expense(row,this_expense_amount)
	{
		//alert(row);
		//box-shadow: 0 0 0 3px #6295FC inset;
		$("#tr_"+row).addClass("blue_border");
		//$("#tr_"+row).hide();
		selected_expense = row;
		expense_amount = this_expense_amount;
		$("#split_expense_dialog").dialog("open");
	}
	
	function open_lock_expense_dialog(row)
	{
		selected_expense = row;
		$( "#lock_expense").dialog("open");
		$("#tr_"+row).addClass("blue_border");
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var ajax_div = $('#lock_expense');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		//var dataString = $("#add_note_form").serialize();
		var dataString = "&id="+row;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/expenses/match_po")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					ajax_div.html(response);
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
	
	function po_action_selected()
	{
		$("#po_match_row").hide();
		$("#lock_expense_div").hide();
		$("#po_match_id").val('Select');
		
		$("#po_action").val($("#lock_action").val());
	
		if($("#lock_action").val() == "Match PO")
		{
			$("#po_match_row").show();
		}
		else if($("#lock_action").val() == "Create PO" || $("#lock_action").val() == "Skip PO")
		{
			load_lock_expense_form()
		}
	}
		
	function load_lock_expense_form()
	{
		//alert('hi');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var ajax_div = $('#lock_expense_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#match_po_form").serialize();
		//var dataString = "&id="+row;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/expenses/po_match_selected")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					ajax_div.html(response);
					$("#lock_expense_div").show();
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
	
	function validate_lock_expense()
	{
		var row = $("#lock_expense_id").val();
		//alert(row);
		var isValid = true;
		
		if($("#lock_action").val() == "Select")
		{
			alert("PO Action must be selected!");
			isValid = false;
		}
		
		if($("#lock_action").val() == "Match PO")
		{
			if($("#po_match_id").val() == "Select")
			{
				alert("Purchase Order must be selected!");
				isValid = false;
			}
		}
		
		if($("#lock_action").val() == "Skip PO")
		{
			if($("#expense_owner").val() == "")
			{
				alert("Owner must be assigned to the expense before skipping the PO!");
				isValid = false;
			}
			
			if($("#expense_category").val() == "")
			{
				alert("Category must be assinged to the expense before skipping the PO!");
				isValid = false;
			}
		}
		
		
		if(isValid)
		{
			//alert(isValid);
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var ajax_div = $('#tr_'+row);
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#lock_expense_form").serialize();
			//var dataString = "&id="+row;
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/expenses/perform_po_action")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: ajax_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						ajax_div.html(response);
						$( "#lock_expense").dialog("close");
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
	
	function unlock_expense(row)
	{
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&id="+row;
		
		var this_div = $('#tr_'+row);
		
		$.ajax({
			url: "<?= base_url("index.php/expenses/unlock_expense")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					
					//SHOW LOADING ICON
					$("#filter_loading_icon").hide();
					$("#refresh_logs").show();
					
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
	
	function clear_sources()
	{
		<?php foreach($source_accounts_options as $source_account): ?>
			$('#<?=$source_account["account_id"]?>_cb').removeAttr('checked');
		<?php endforeach; ?>
		
		load_report();
	}
	
	function select_all_sources()
	{
		<?php foreach($source_accounts_options as $source_account): ?>
			$('#<?=$source_account["account_id"]?>_cb').attr('checked','checked');
		<?php endforeach; ?>
		
		load_report();
	}
	
	//AJAX FOR GETTING NOTES
	function open_notes(expense_id)
	{
		//RESET LOADING GIF
		$("#notes_ajax_div").html("");
		
		//SET NOTES_ID TO VALUE OF LOAD ID
		//$("#notes_id").val(truck_id);
		
		$("#expense_id").val(expense_id); //this is the hidden field in the add notes form
		
		//OPEN THE DIALOG BOX
		$( "#add_notes").dialog( "open" );
		selected_expense = expense_id;
		$("#tr_"+expense_id).addClass("blue_border");
		
		//alert('inside ajax');
				
		// GET THE DIV IN DIALOG BOX
		var notes_ajax_div = $('#notes_ajax_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/expenses/get_notes/")?>"+"/"+expense_id, // in the quotation marks
			type: "POST",
			cache: false,
			context: notes_ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					notes_ajax_div.html(response);
					
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
	function save_note(expense_id)
	{
		var dataString = $("#add_note_form").serialize();
		
		//CLEAR TEXT AREA
		$("#new_note").val("");
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var notes_ajax_div = $('#notes_ajax_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/expenses/save_note")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: notes_ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					notes_ajax_div.html(response);
					$("#expense_notes_"+expense_id).attr("title",response.replace(/<br>/gi,"\n"));
					$("#expense_notes_"+expense_id).attr("src","/images/add_notes.png");
					
					//$("#notes_details").html(response);
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
	
	//ADD ALLOCATION ROW
	var last_row = 2;
	function add_allocation_row()
	{
		last_row = last_row + 1;
		//alert(last_row);
		$("#allocation_row_"+last_row).show();
	}
	
	var expense_amount = 0;
	function add_allocations()
	{
		//alert(expense_amount);
		
		isValid = true;
		
		var total_allocations = 0;
		for(i = 1;i<=5;i++)
		{
			var this_amount = $("#allocation_amount_"+i).val();
			
			if(isNaN(this_amount))
			{
				alert("Split "+i+" is not a number!");
			}
			else
			{
				total_allocations = Math.round((total_allocations + Number(this_amount))*100)/100;
			}
		}
		
		//alert(total_allocations);
		if(total_allocations != expense_amount)
		{
			$("#total_allocations").css('color','red');
		}
		else if(total_allocations == expense_amount)
		{
			$("#total_allocations").css('color','green');
		}
		
		//$("#these_allocations").val(total_allocations);
		$("#total_allocations").html("$"+total_allocations.toFixed(2));
	}
	
	function validate_expense_split(selected_expense)
	{
		//alert("validate splits "+selected_expense);
		
		isValid = true;
		
		var total_allocations = 0;
		
		//VALIDATE THAT ALL AMOUNTS ARE NUMBERS AND TOTAL THE AMOUNTS
		for(i = 1;i<=5;i++)
		{
			var this_amount = $("#allocation_amount_"+i).val();
			var this_note = $("#allocation_notes_"+i).val();
			
			if(isNaN(this_amount))
			{
				alert("Split "+i+" is not a number!");
				isValid = false;
			}
			else
			{
				total_allocations = Math.round((total_allocations + Number(this_amount))*100)/100;
			}
			
			//VALIDATE THAT NOTES HAVE BEEN ENTERED
			if(this_amount)
			{
				if(!this_note)
				{
					alert("You must enter notes for split "+i);
					isValid = false;
				}
			}
			
		}
		//alert(total_allocations);
		
		//VALIDATE THAT TOAL MATCHES THE EXPENSE AMOUNT
		if(total_allocations != expense_amount)
		{
			alert("The total does not match the original expense! "+total_allocations)
			isValid = false;
		}
		
		
		if(isValid)
		{
			submit_expense_splits(selected_expense);
		}
	}
	
	function submit_expense_splits(selected_expense)
	{
		//alert("Valid! "+selected_expense);
		//$("#split_expense_dialog").hide();
		
		$("#split_expense_save_button").attr('disabled',true);
		
		//alert('is it disabled');
		
		$("#split_expense_id").val(selected_expense);
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#split_expense_form").serialize();
		
		var this_div = $('#filter_div');

		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/expenses/split_expense")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					
					//CLOSE DIALOG - AFTER DATASTRING HAS BEEN SERIALIZED
					$( "#split_expense_dialog").dialog('close');
					
					load_report();
					
					//this_div.html(response);
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
	
	//I DON'T THINK THIS IS USED ANYMORE
	function change_expense_type(row)
	{
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&id="+row;
		
		var this_div = $('#tr_'+row);
		
		$.ajax({
			url: "<?= base_url("index.php/expenses/change_expense_type")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					
					//SHOW LOADING ICON
					$("#filter_loading_icon").hide();
					$("#refresh_logs").show();
					
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