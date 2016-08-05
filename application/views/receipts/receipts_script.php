<script>
	$(document).ready(function()
	{
		$("#main_content").height($(window).height() - 115);
		$("#scrollable_filter_div").height($(window).height() - 158);
		
		load_filter();
		
		
		
		//ADD NOTE DIALOG
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
						$("#tr_"+selected_row).removeClass('blue_border');
					}
		});//end add notes dialog
		
		//UPLOAD RECEIPT DIALOG
		$( "#upload_receipt").dialog(
		{
				autoOpen: false,
				height: 305,
				width: 450,
				modal: true,
				buttons: 
					[
						{
							text: "Save",
							click: function() 
							{
								upload_receipt();
							},//end add load
						},
						{
							text: "Cancel",
							click: function() 
							{
								//RESET FORM
								$( this ).dialog( "close" );
							}
						}
					],//end of buttons
				
				open: function()
					{
						//RESET FORM
						//reset_upload_receipt_form();
						
					},//end open function
				close: function() 
					{
						//REMOVE ANY BLUE BORDER BOXES
						$("#tr_"+selected_row).removeClass('blue_border');
						
						refresh_row(selected_row);
					}
		});//end upload receipt dialog
	});
	
	var selected_row = 0;
	
	function load_filter()
	{
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#filter_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "";
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/receipts/load_filter_div")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					
					//STOP LOADING ICON
					$("#filter_loading_icon").hide();
					$("#refresh_logs").show();
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
	
	function set_status_checkboxes()
	{
		//GET OUTSTANDING CHECK BOX?
		if($('#outstanding_cb').attr('checked'))
		{
			$("#get_outstanding").val(true);
		}
		else
		{
			$("#get_outstanding").val(false);
		}
		
		//GET SETTLED CHECK BOX?
		if($('#settled_cb').attr('checked'))
		{
			$("#get_settled").val(true);
		}
		else
		{
			$("#get_settled").val(false);
		}
	}
	
	function clear_check_boxes()
	{
		$('#outstanding_cb').removeAttr('checked');
		$('#settled_cb').removeAttr('checked');
		
		load_report();
	}
	
	function select_all_check_boxes()
	{
		$('#outstanding_cb').attr('checked','checked');
		$('#settled_cb').attr('checked','checked');
		
		load_report();
	}
	
	var report_ajax_call;
	function load_report()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		set_status_checkboxes();
		
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
			url: "<?= base_url("index.php/receipts/load_report")?>", // in the quotation marks
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
	
	//AJAX FOR GETTING NOTES
	function open_notes(expense_id)
	{
		//RESET LOADING GIF
		$("#notes_ajax_div").html("");
		
		//SET NOTES_ID TO VALUE OF CLIENT EXPENSE ID
		$("#expense_id").val(expense_id); //this is the hidden field in the add notes form
		
		//OPEN THE DIALOG BOX
		$( "#add_notes").dialog( "open" );
		selected_row = expense_id;
		$("#tr_"+expense_id).addClass("blue_border");
		
		//alert('inside ajax');
				
		// GET THE DIV IN DIALOG BOX
		var notes_ajax_div = $('#notes_ajax_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/receipts/get_notes/")?>"+"/"+expense_id, // in the quotation marks
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
			url: "<?= base_url("index.php/receipts/save_note")?>", // in the quotation marks
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
	
	//WHO PAYS SELECTED
	function who_pays_selected()
	{
		//HIDE OTHER ROWS
		$("#business_user_row").hide();
		$("#expense_account_row").hide();
		$("#load_row").hide();
		$("#fleetprotect_row").hide();
		$("#revenue_acc_row").hide();
		$("#receipt_amount_row").hide();
		$("#document_row").hide();
		$("#more_receipts_row").hide();
		$("#lost_receipt_div").hide();
		$("#driver_expense_div").hide();
		
		//RESET OTHER FIELDS
		$("#business_user_id").val("Select");
		$("#expense_account_id").val("Select");
		$("#load_id").val("Select");
		$("#fp_account_id").val("Select");
		$("#rev_account_id").val("Select");
	
		if($("#who_pays").val() == "Business User")
		{
			$("#business_user_row").show();
		}
		else if($("#who_pays").val() == "Broker")
		{
			$("#load_row").show();
		}
		else if($("#who_pays").val() == "Driver")
		{
			$("#driver_expense_div").show();
		}
		else if($("#who_pays").val() == "FleetProtect")
		{
			$("#fleetprotect_row").show();
		}
		else if($("#who_pays").val() == "Lost Receipt")
		{
			$("#revenue_acc_row").show();
		}
		
	}
	
	//CHECK BOX CHANGED
	function more_receipts_cb_changed()
	{
		if($('#more_receipts_cb').attr('checked'))
		{
			$("#more_receipts").val(true);
		}
		else
		{
			$("#more_receipts").val(false);
		}
	}
	
	//OPEN UPLOAD RECEIPT DIALOG
	function open_upload_receipt(row)
	{
		//OPEN THE DIALOG BOX
		$( "#upload_receipt").dialog('open');
		selected_row = row;
		$("#tr_"+row).addClass("blue_border");
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#upload_receipt');
		
		this_div.html('<div style="width:25px; margin: 0 auto; margin-top:80px;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px;" /></div>');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&client_expense_id="+row;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/receipts/load_upload_receipt_div")?>", // in the quotation marks
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
	
	//VALIDATE AND SUBMIT UPLOAD RECEIPT
	function upload_receipt()
	{
		more_receipts_cb_changed()
		
		var isValid = true;
		
		//VALIDATE WHO PAYS
		if($("#who_pays").val() == "Select")
		{
			isValid = false;
			alert("Who pays must be selected!");
		}
		
		if($("#who_pays").val() == "Business User")
		{
			if($("#business_user_id").val() == "Select")
			{
				isValid = false;
				alert("Business User must be selected!");
			}
			
			if($("#expense_account_id").val() == "Select")
			{
				isValid = false;
				alert("Expense Account must be selected!");
			}
		}
		else if($("#who_pays").val() == "Broker")
		{
			if($("#load_id").val() == "Select")
			{
				isValid = false;
				alert("Load must be selected!");
			}
		}
		else if($("#who_pays").val() == "Driver")
		{
			//$("#avoidable_row").show();
		}
		else if($("#who_pays").val() == "FleetProtect")
		{
			if($("#fp_account_id").val() == "Select")
			{
				isValid = false;
				alert("FleetProtect Account must be selected!");
			}
		}
		else if($("#who_pays").val() == "Lost Receipt")
		{
			if($("#rev_account_id").val() == "Select")
			{
				isValid = false;
				alert("Advance Fee Revenue Account must be selected!");
			}
		}
		
		
		//VALIDATE AMOUNT AND LINK IF NOT - LOST RECEIPT
		if($("#who_pays").val() == "Lost Receipt" || $("#who_pays").val() == "Driver")
		{
		}
		else
		{
			//VALIDATE RECEIPT AMOUNT
			if(!$("#receipt_amount").val() || isNaN($("#receipt_amount").val()))
			{
				alert("Receipt amount must be a number!");
				isValid = false;
			}
			
			//VALIDATE DOCUMENT LINK
			if(!$("#file_guid").val())
			{
				if(!$("#receipt_file").val())
				{
					isValid = false;
					alert("Document Upload must be selected!");
				}
			}
		}
		
		//IF VALID SUBMIT FORM
		if(isValid)
		{
			//SUBMIT FORM
			$("#upload_receipt_form").submit();
			
			$("#business_user").val($("#new_invoice_business_user_id").val());
			
			$("#upload_receipt_form_div").hide();
			$("#uploading_text").show();
			
			setTimeout(function()
			{
				$("#upload_receipt").dialog( "close" );
			},3000);
		}
	}
	
	function business_user_selected()
	{
		$("#receipt_amount_row").hide();
		$("#document_row").hide();
		$("#more_receipts_row").hide();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#expense_account_row');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#upload_receipt_form").serialize();
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/receipts/business_user_selected")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					$("#expense_account_row").show();
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
	
	function show_common_fields()
	{
		$("#receipt_amount_row").show();
		$("#more_receipts_row").show();
		if(!$("#file_guid").val())
		{
			$("#document_row").show();
		}
	}
	
	function rev_account_selected()
	{
		$("#lost_receipt_div").show();
	}
	
	function refresh_row(selected_row)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#tr_'+selected_row);
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "";
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/receipts/refresh_row")?>"+"/"+selected_row, // in the quotation marks
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
	
	function submit_receipt_file(row_id)
	{
		//alert('yo');
		$("#paperclip_"+row_id).hide()
		$("#loading_receipt_"+row_id).show();
		
		var form = $("#row_receipt_upload_form_"+row_id)[0];
		var formData = new FormData(form);
		$.ajax( {
			url: '<?= base_url('index.php/receipts/upload_receipt_file')?>',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			//context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//this_div.html(response);
					//alert(response);
					$("#file_upload_dialog" ).dialog('close');
					refresh_row(row_id);
					
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
		});
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
</script>	