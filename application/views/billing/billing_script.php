<script>
	$(document).ready(function()
	{
		$("#filter_list").height($("#body").height() - 225);
		
		load_funding_report();
		
		$('.datepicker').datepicker({ showAnim: 'blind' });
		
		$('#drop_start_date_filter').datepicker({ showAnim: 'blind' });
		$('#drop_end_date_filter').datepicker({ showAnim: 'blind' });
		$('#billing_start_date_filter').datepicker({ showAnim: 'blind' });
		$('#billing_end_date_filter').datepicker({ showAnim: 'blind' });
		$('#funding_start_date_filter').datepicker({ showAnim: 'blind' });
		$('#funding_end_date_filter').datepicker({ showAnim: 'blind' });
		
		//ADD NOTES DIALOG
        $( "#add_notes_dialog").dialog(
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
							//alert($("#new_note").val());
							if(!$("#new_note").val())
							{
								alert("You didn't enter a new note!");
							}
							else
							{
								//alert($("#reraererae").serialize());
								//alert(dataString);
								save_note();
							}
						},//end add ticket
					},
					{
						text: "Close",
						click: function() 
						{
							//CLEAR TEXT AREA
							//$("#new_note").val("");
							$( this ).dialog( "close" );
						}
					}
				],//end of buttons
				close: function() 
				{
					//REMOVE ANY BLUE BORDER BOXES
					$("#row_"+selected_row).removeClass('blue_border');
				}
        });//end add notes dialog
		
		//DIALOG: UPLOAD FILE DIALOG
		$("#file_upload_dialog" ).dialog(
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
						if(!$("#attachment_file").val())
						{
							isValid = false;
							alert('You must choose a File!');
						}
						
						if(isValid)
						{
							//SUBMIT FORM
							upload_attachment_file();
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
				//REMOVE ANY BLUE BORDER BOXES
				$("#row_"+selected_row).removeClass('blue_border');
			}
		});//end dialog form
		
		//DIALOG: BILLING CHECKLIST
		$("#billing_checklist_dialog" ).dialog(
		{
				autoOpen: false,
				height: 550,
				width: 700,
				modal: true,
				buttons: 
					{
						"Save": function() 
							{
								validate_billing_update(selected_row);
							},//end create an user
						
						Cancel: function() 
							{
								$( this ).dialog( "close" );
								//$("#rate_con_received_dialog").height(605);
								//$("#rate_con_received_dialog").width(460);
							}
					},//end of buttons
				
				open: function()
					{
					},//end open function
				close: function() 
					{
						//REMOVE ANY BLUE BORDER BOXES
						$("#row_"+selected_row).removeClass('blue_border');
					}//end close function
		});//end dialog form
		
		//DIALOG: LOAD PROCESS AUDIT
		$("#process_audit_dialog" ).dialog(
		{
				autoOpen: false,
				height: 550,
				width: 400,
				modal: true,
				buttons: 
					{
						"Close": function() 
							{
								$( this ).dialog( "close" );
								//$("#rate_con_received_dialog").height(605);
								//$("#rate_con_received_dialog").width(460);
							}
					},//end of buttons
				
				open: function()
					{
					},//end open function
				close: function() 
					{
						//REMOVE ANY BLUE BORDER BOXES
						$("#row_"+selected_row).removeClass('blue_border');
					}//end close function
		});//end dialog form
		
	});
	
	
	ajax_pool = [];
	function abort_ajax_requests()
	{
		ajax_pool.forEach(function(request)
		{
			request.abort();
		});
	}
	
	//LOAD LOADS LIST
	var load_list_ajax_call;
	function load_funding_report()
	{
		//abort_ajax_requests();
		
		$("#refresh_list").hide();
		$("#filter_loading_icon").show();
		
		//load_summary_stats();
	
		var dataString = $("#filter_form").serialize();
		
		//alert("load_list");
		
		//alert(dataString.substring(1));
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		if(!(load_list_ajax_call===undefined))
		{
			//alert('abort');
			load_list_ajax_call.abort();
		}
		load_list_ajax_call = $.ajax({
			url: "<?= base_url("index.php/billing/load_list")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					change_view();
					//$("#filter_loading_icon").hide();
					//$("#refresh_list").show();
					//load_summary_stats();
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
	
	function change_view()
	{
		//HIDE ALL
		$(".pending_td").hide();
		$(".fm_td").hide();
		$(".dm_td").hide();
		$(".driver1_td").hide();
		$(".driver2_td").hide();
		$(".broker_td").hide();
		$(".billed_td").hide();
		$(".method_td").hide();
		$(".funded_td").hide();
		$(".expect_payment_td").hide();
		$(".drop_city_td").hide();
		$(".age_td").hide();
		$(".short_td").hide();
		$(".last_update_td").hide();
		$(".hold_reason_td").hide();
		$(".notes_td").hide();
		$(".process_audit").hide();
		
		if($("#view_dropdown").val() == 'Invoices')
		{
			$(".pending_td").show();
			$(".fm_td").show();
			$(".dm_td").show();
			$(".driver1_td").show();
			$(".driver2_td").show();
			$(".broker_td").show();
			$(".expect_payment_td").show();
			$(".drop_city_td").show();
			$(".age_td").show();
			$(".billed_td").show();
			$(".method_td").show();
			$(".funded_td").show();
			$(".short_td").show();
			$(".notes_td").show();
		}
		else if($("#view_dropdown").val() == 'Updates')
		{
			$(".broker_td").show();
			$(".billed_td").show();
			$(".method_td").show();
			$(".funded_td").show();
			$(".expect_payment_td").show();
			$(".last_update_td").show();
			$(".hold_reason_td").show();
			$(".notes_td").show();
		}
		else if($("#view_dropdown").val() == 'Process Audit')
		{
			$(".fm_td").show();
			$(".dm_td").show();
			$(".driver1_td").show();
			$(".driver2_td").show();
			$(".process_audit").show();
		}
	}
	
	function row_clicked(row_id)
	{
		var this_div = $('#details_'+row_id);
		
		if(this_div.is(":visible"))
		{
			this_div.hide();
			this_div.html('<img id="loading_icon" name="loading_icon" height="20px" src="/images/loading.gif" style="margin-left:450px; margin-top:5px;" />');
		}
		else
		{
			this_div.show();
			open_row_details(row_id)
		}
	}
	
	//OPEN LOAD DETAILS
	function open_row_details(row_id)
	{
		//alert('opening row details');
		$("#refresh_load_details_icon_"+row_id).attr('src','/images/loading.gif');
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#details_'+row_id);
			
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&load_id="+row_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/billing/open_details")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					refresh_row(row_id);
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
	
	//REFRESH ROW
	function refresh_row(row_id)
	{
		//alert(row_id);
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#row_'+row_id);
			
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&load_id="+row_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/billing/refresh_row")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					change_view();
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
	
	//OPEN EDIT MODE
	function edit_row_details(row_id)
	{
		$('.edit_'+row_id).css({"display":"block"});
		$('.details_'+row_id).css({"display":"none"});
	}
	
	//VALIDATE AND SAVE LOAD EDIT
	function save_load_edit(row_id)
	{
		var isValid = true;
		
		if($("#edit_natl_avg_"+row_id).val())
		{
			if(isNaN($("#edit_natl_avg_"+row_id).val()))
			{
				isValid = false;
				alert("Nat'l Fuel Avg must be a number!");
			}
		}
		else
		{
			isValid = false;
			alert("Nat'l Fuel Avg must be entered!");
		}
		
		if($("#edit_expected_rate_"+row_id).val())
		{
			if(isNaN($("#edit_expected_rate_"+row_id).val()))
			{
				isValid = false;
				alert("Expected Rate must be a number!");
			}
		}
		else
		{
			isValid = false;
			alert("Expected Rate must be entered!");
		}
		
		if($("#edit_expected_miles_"+row_id).val())
		{
			if(isNaN($("#edit_expected_miles_"+row_id).val()))
			{
				isValid = false;
				alert("Expected Miles must be a number!");
			}
		}
		
		if(isValid)
		{
			$("#save_icon_"+row_id).attr('src','/images/loading.gif');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#load_details_form_"+row_id).serialize();
			
			var this_div = $('#details_'+row_id);
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/billing/save_load_edit")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						open_row_details(row_id);
						
						//load_report();
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
		
	}
	
	//OPEN RATE BILLING CHECKLIST DIALOG
	function open_billing_checklist_dialog(load_number)
	{
		var row_id = load_number;
		
		//alert('inside ajax');
		$("#billing_checklist_dialog").html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px; margin-left:350px; margin-top:180px;" />');
		$("#billing_checklist_dialog" ).dialog( "open" );
		
		selected_row = row_id;
		$("#row_"+row_id).addClass("blue_border");
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#billing_checklist_dialog');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var data = "load_number=" + load_number; //use & to separate values
		
		// AJAX!
		$.ajax({
			url: "<?= base_url('index.php/billing/open_billing_checklist_dialog')?>", // in the quotation marks
			type: "POST",
			data: data,
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
		
		return false; 
	}
	
	function checklist_clicked(checkbox,load_id)
	{
		var checking = false;
		if ($(checkbox).attr('checked'))
		{
			checking = true;
		}
		
		//UNCHECK ALL ENABLED CHECKBOXES
		if(!$('#digital_cb_'+load_id).attr('disabled'))
		{
			$('#digital_cb_'+load_id).removeAttr('checked');
		}
		
		if(!$('#process_audit_cb_'+load_id).attr('disabled'))
		{
			$('#process_audit_cb_'+load_id).removeAttr('checked');
		}
		
		if(!$('#envelope_cb_'+load_id).attr('disabled'))
		{
			$('#envelope_cb_'+load_id).removeAttr('checked');
		}
		
		if(!$('#dropbox_cb_'+load_id).attr('disabled'))
		{
			$('#dropbox_cb_'+load_id).removeAttr('checked');
		}
		
		if(!$('#billed_cb_'+load_id).attr('disabled'))
		{
			$('#billed_cb_'+load_id).removeAttr('checked');
		}
		
		if(!$('#funded_cb_'+load_id).attr('disabled'))
		{
			$('#funded_cb_'+load_id).removeAttr('checked');
		}
		
		if(!$('#hc_processed_cb_'+load_id).attr('disabled'))
		{
			$('#hc_processed_cb_'+load_id).removeAttr('checked');
		}
		
		if(!$('#hc_sent_cb_'+load_id).attr('disabled'))
		{
			$('#hc_sent_cb_'+load_id).removeAttr('checked');
		}
		
		if(!$('#hc_received_cb_'+load_id).attr('disabled'))
		{
			$('#hc_received_cb_'+load_id).removeAttr('checked');
		}
		
		if(!$('#hold_cb_'+load_id).attr('disabled'))
		{
			$('#hold_cb_'+load_id).removeAttr('checked');
		}
		
		if(!$('#recoursed_cb_'+load_id).attr('disabled'))
		{
			$('#recoursed_cb_'+load_id).removeAttr('checked');
		}
		
		if(!$('#reimbursed_cb_'+load_id).attr('disabled'))
		{
			$('#reimbursed_cb_'+load_id).removeAttr('checked');
		}
		
		if(!$('#invoice_closed_cb_'+load_id).attr('disabled'))
		{
			$('#invoice_closed_cb_'+load_id).removeAttr('checked');
		}
		
		//KEEP THE CURRENTLY CHECKED BOX CHECKED
		if(checking)
		{
			$(checkbox).attr('checked', 'checked');
		}
		
		//DIGITAL RECEIVED DIV (SHOW AND HIDE)
		if($('#digital_cb_'+load_id).attr('checked'))
		{
			if(!$('#digital_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#digital_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#digital_input_div").hide();
			$("#digital_received_date_"+load_id).val("");
		}
		
		//PROCCESS AUDIT DIV (SHOW AND HIDE)
		if($('#process_audit_cb_'+load_id).attr('checked'))
		{
			if(!$('#process_audit_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#process_audit_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#process_audit_input_div").hide();
		}
		
		//DIGITAL RECEIVED DIV (SHOW AND HIDE)
		if($('#envelope_cb_'+load_id).attr('checked'))
		{
			if(!$('#envelope_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#envelope_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#envelope_input_div").hide();
			$("#envelope_pic_date_"+load_id).val("");
		}
		
		//DIGITAL RECEIVED DIV (SHOW AND HIDE)
		if($('#dropbox_cb_'+load_id).attr('checked'))
		{
			if(!$('#dropbox_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#dropbox_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#dropbox_input_div").hide();
			$("#dropbox_date_"+load_id).val("");
		}
		
		//INVOICE BILLED DIV (SHOW AND HIDE)
		if($('#billed_cb_'+load_id).attr('checked'))
		{
			if(!$('#billed_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#billed_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#billed_input_div").hide();
			$("#invoice_billed_date_"+load_id).val("");
			$("#amount_billed_"+load_id).val("");
		}
		
		//INVOICE FUNDED DIV (SHOW AND HIDE)
		if($('#funded_cb_'+load_id).attr('checked'))
		{
			if(!$('#funded_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#funded_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#funded_input_div").hide();
			//$("#invoice_funded_date_"+load_id).val("");
			$("#amount_funded_"+load_id).val("");
			$("#invoice_number_"+load_id).val("");
		}
		
		//HC PROCESSED DIV (SHOW AND HIDE)
		if($('#hc_processed_cb_'+load_id).attr('checked'))
		{
			if(!$('#hc_processed_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#hc_processed_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#hc_processed_input_div").hide();
			$("#hc_processed_date_"+load_id).val("");
		}
		
		//HC SENT DIV (SHOW AND HIDE)
		if($('#hc_sent_cb_'+load_id).attr('checked'))
		{
			if(!$('#hc_sent_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#hc_sent_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#hc_sent_input_div").hide();
			$("#hc_sent_date_"+load_id).val("");
		}
		
		//HC RECEIVED DIV (SHOW AND HIDE)
		if($('#hc_received_cb_'+load_id).attr('checked'))
		{
			if(!$('#hc_received_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#hc_received_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#hc_received_input_div").hide();
			$("#hc_received_date_"+load_id).val("");
		}
		
		//INVOICE CLOSED DIV (SHOW AND HIDE)
		if($('#hold_cb_'+load_id).attr('checked'))
		{
			if(!$('#hold_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#hold_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#hold_input_div").hide();
			$("#hold_date_"+load_id).val("");
			$("#hold_reason_"+load_id).html("");
		}
		
		//INVOICE CLOSED DIV (SHOW AND HIDE)
		if($('#recoursed_cb_'+load_id).attr('checked'))
		{
			if(!$('#recoursed_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#recoursed_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#recoursed_input_div").hide();
			$("#recourse_date_"+load_id).val("");
			$("#recourse_reason_"+load_id).html("");
		}
		
		//INVOICE CLOSED DIV (SHOW AND HIDE)
		if($('#reimbursed_cb_'+load_id).attr('checked'))
		{
			if(!$('#reimbursed_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#reimbursed_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#reimbursed_input_div").hide();
			$("#reimbursed_date_"+load_id).val("");
		}
		
		//INVOICE CLOSED DIV (SHOW AND HIDE)
		if($('#invoice_closed_cb_'+load_id).attr('checked'))
		{
			if(!$('#invoice_closed_cb_'+load_id).attr('disabled'))
			{
				//SHOW DIV
				$("#invoice_closed_input_div").show();
			}
		}
		else
		{
			//HIDE DIV AND RESET VALUES
			$("#invoice_closed_input_div").hide();
			$("#invoice_closed_date_"+load_id).val("");
		}
		
		
		
	}//end checklist_clicked()
	
	function validate_billing_update(load_id)
	{
		//alert(load_id);
		var isValid = true;
		$("#action_"+load_id).val("");
		//FIGURE OUT WHICH ACTION TO VALIDATE AND UPDATE
		if($('#digital_cb_'+load_id).attr('checked') && !$('#digital_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("digital");
			
			var isValid = true;
			
			//VALIDATE
			if(!$("#digital_received_date_"+load_id).val())
			{
				isValid = false;
				alert("Date must be entered!");
			}
			else if(!isDate($("#digital_received_date_"+load_id).val()))
			{
				isValid = false;
				alert("Date must be valid!");
			}
			
			if(!$("#dc_file_"+load_id).val())
			{
				isValid = false;
				alert("BoL Pic must be selected for upload!");
			}
		}
		
		if($('#process_audit_cb_'+load_id).attr('checked') && !$('#process_audit_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("process_audit");
			
			var isValid = true;
			
			//VALIDATE
			if($("#defer_to_tarriff").val() == 'Select')
			{
				isValid = false;
				alert("Defer to Tarriff must be selected!");
			}
			
			if($("#ontime_by_rc").val() == 'Select')
			{
				isValid = false;
				alert("Ontime According to RC must be selected!");
			}
			
			if($("#shipper_load_and_count").val() == 'Select')
			{
				isValid = false;
				alert("Shipper Load and Count must be selected!");
			}
			
			if($("#seal_pic_depart").val() == 'Select')
			{
				isValid = false;
				alert("Seal Pic (Departure) must be selected!");
			}
			
			if($("#load_pic_depart").val() == 'Select')
			{
				isValid = false;
				alert("Load Pic (Departure) must be selected!");
			}
			
			if($("#seal_number").val() == 'Select')
			{
				isValid = false;
				alert("Seal Number on Bills must be selected!");
			}
			
			if($("#seal_pic_arrive").val() == 'Select')
			{
				isValid = false;
				alert("Seal Pic (Arrival) must be selected!");
			}
			
			if($("#load_pic_arrive").val() == 'Select')
			{
				isValid = false;
				alert("Load Pic (Arrival) must be selected!");
			}
			
			if($("#seal_intact").val() == 'Select')
			{
				isValid = false;
				alert("Seal Intact on Bills must be selected!");
			}
			
			if($("#easy_sign_bills").val() == 'Select')
			{
				isValid = false;
				alert("Easy Signed BoL must be selected!");
			}
			if($("#clean_bills").val() == 'Select')
			{
				isValid = false;
				alert("Clean Bills must be selected!");
			}
		}
		
		if($('#billed_cb_'+load_id).attr('checked')&& !$('#billed_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("billed");
			
			var isValid = true;
			
			//VALIDATE
			if(!$("#invoice_billed_date_"+load_id).val())
			{
				isValid = false;
				alert("Date must be entered!");
			}
			else if(!isDate($("#invoice_billed_date_"+load_id).val()))
			{
				isValid = false;
				alert("Date must be valid!");
			}
			
			if(!$("#amount_billed_"+load_id).val())
			{
				isValid = false;
				alert("Amount Billed must be entered!");
			}
			else if(isNaN($("#amount_billed_"+load_id).val()))
			{
				isValid = false;
				alert("Amount Billed must be a number!");
			}
		}
		
		if($('#funded_cb_'+load_id).attr('checked')&& !$('#funded_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("funded");
			
			var isValid = true;
			
			//VALIDATE
			
			//if(!$("#invoice_funded_date_"+load_id).val())
			//{
			//	isValid = false;
			//	alert("Date must be entered!");
			//}
			//else if(!isDate($("#invoice_funded_date_"+load_id).val()))
			//{
			//	isValid = false;
			//	alert("Date must be valid!");
			//}
			
			if(!$("#amount_funded_"+load_id).val())
			{
				isValid = false;
				alert("Amount Funded must be entered!");
			}
			else if(isNaN($("#amount_funded_"+load_id).val()))
			{
				isValid = false;
				alert("Amount Funded must be a number!");
			}
			
			if(!$("#finance_cost_"+load_id).val())
			{
				isValid = false;
				alert("Finance Cost must be entered!");
			}
			else if(isNaN($("#finance_cost_"+load_id).val()))
			{
				isValid = false;
				alert("Finance Cost must be a number!");
			}
			
			if(!$("#invoice_number_"+load_id).val())
			{
				isValid = false;
				alert("Invoice # must be entered!");
			}
			
			
			
		}
		
		if($('#envelope_cb_'+load_id).attr('checked') && !$('#envelope_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("envelope");
			
			var isValid = true;
			
			//VALIDATE
			if(!$("#envelope_pic_date_"+load_id).val())
			{
				isValid = false;
				alert("Date of Envelope Pic must be entered!");
			}
			else if(!isDate($("#envelope_pic_date_"+load_id).val()))
			{
				isValid = false;
				alert("Date of Envelope Pic must be valid!");
			}
			
			if(!$("#envelope_file_"+load_id).val())
			{
				isValid = false;
				alert("Envelope Pic must be selected for upload!");
			}
		}
		
		if($('#dropbox_cb_'+load_id).attr('checked') && !$('#dropbox_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("dropbox");
			
			var isValid = true;
			
			//VALIDATE
			if(!$("#dropbox_date_"+load_id).val())
			{
				isValid = false;
				alert("Date of Dropbox Pic must be entered!");
			}
			else if(!isDate($("#dropbox_date_"+load_id).val()))
			{
				isValid = false;
				alert("Date of Dropbox Pic must be valid!");
			}
			
			if(!$("#dropbox_file_"+load_id).val())
			{
				isValid = false;
				alert("Dropbox Pic must be selected for upload!");
			}
		}
		
		if($('#hc_processed_cb_'+load_id).attr('checked')&& !$('#hc_processed_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("hc_processed");
			
			var isValid = true;
			
			//VALIDATE
			// if(!$("#bol_link2_"+load_id).val())
			// {
				// isValid = false;
				// alert("BOL Link must be entered!");
			// }
		}
		
		if($('#hc_sent_cb_'+load_id).attr('checked')&& !$('#hc_sent_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("hc_sent");
			
			var isValid = true;
			
			//VALIDATE
			if(!$("#hc_sent_date_"+load_id).val())
			{
				isValid = false;
				alert("Date HC Sent must be entered!");
			}
			else if(!isDate($("#hc_sent_date_"+load_id).val()))
			{
				isValid = false;
				alert("Date HC Sent must be valid!");
			}
			
			if(!$("#hc_sent_proof_"+load_id).val())
			{
				isValid = false;
				alert("Proof of Request to send HC must be selected for upload!");
			}
		}
		
		if($('#hc_received_cb_'+load_id).attr('checked')&& !$('#hc_received_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("hc_received");
			
			var isValid = true;
			
			//VALIDATE
			if(!$("#hc_received_date_"+load_id).val())
			{
				isValid = false;
				alert("Date must be entered!");
			}
			else if(!isDate($("#hc_received_date_"+load_id).val()))
			{
				isValid = false;
				alert("Date must be valid!");
			}
			
			if(!$("#hc_received_proof_"+load_id).val())
			{
				isValid = false;
				alert("Proof of BOL Delivery must be selected for upload!");
			}
		}
		
		if($('#hold_cb_'+load_id).attr('checked')&& !$('#hold_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("hold");
			
			var isValid = true;
			
			//VALIDATE
			if(!$("#hold_date_"+load_id).val())
			{
				isValid = false;
				alert("Date must be entered!");
			}
			else if(!isDate($("#hold_date_"+load_id).val()))
			{
				isValid = false;
				alert("Date must be valid!");
			}
			
			if($("#hold_reason").val() == 'Select')
			{
				isValid = false;
				alert("Hold Reason must be selected!");
			}
			
			if(!$("#hold_notes").val())
			{
				isValid = false;
				alert("Hold Notes must be entered!");
			}
		}
		
		if($('#recoursed_cb_'+load_id).attr('checked')&& !$('#recoursed_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("recoursed");
			
			var isValid = true;
			
			//VALIDATE
			if(!$("#recourse_date_"+load_id).val())
			{
				isValid = false;
				alert("Date Recoursed must be entered!");
			}
			else if(!isDate($("#recourse_date_"+load_id).val()))
			{
				isValid = false;
				alert("Date Recoursed must be valid!");
			}
			
			if($("#recourse_reason").val() == 'Select')
			{
				isValid = false;
				alert("Recourse Reason must be selected!");
			}
			
			if(!$("#recourse_notes").val())
			{
				isValid = false;
				alert("Recourse Notes must be entered!");
			}
		}
		
		if($('#reimbursed_cb_'+load_id).attr('checked')&& !$('#reimbursed_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("reimbursed");
			
			if(!$("#reimbursed_date_"+load_id).val())
			{
				isValid = false;
				alert("Date Recoursed must be entered!");
			}
			else if(!isDate($("#reimbursed_date_"+load_id).val()))
			{
				isValid = false;
				alert("Date Recoursed must be valid!");
			}
			
			if(!$("#amount_reimbursed_"+load_id).val())
			{
				isValid = false;
				alert("Amount Reimbursed must be entered!");
			}
			else if(isNaN($("#amount_reimbursed_"+load_id).val()))
			{
				isValid = false;
				alert("Amount Reimbursed must be a number!");
			}
		}
		
		
		if($('#invoice_closed_cb_'+load_id).attr('checked')&& !$('#invoice_closed_cb_'+load_id).attr('disabled'))
		{
			$("#action_"+load_id).val("invoice_closed");
			
			var isValid = true;
			
			//VALIDATE
			if(!$("#invoice_closed_date_"+load_id).val())
			{
				isValid = false;
				alert("Date must be entered!");
			}
			else if(!isDate($("#invoice_closed_date_"+load_id).val()))
			{
				isValid = false;
				alert("Date must be valid!");
			}
		}
		
		if(!$("#action_"+load_id).val())
		{
			isValid = false;
			alert('You must select a checkbox to submit an update!');
		}
		
		if(isValid)
		{
			if($('#funded_cb_'+load_id).attr('checked') && !$('#funded_cb_'+load_id).attr('disabled'))
			{
				
				var actual_revenue = Number($("#finance_cost_"+load_id).val()) + Number($("#amount_funded_"+load_id).val());
				var amount_billed = Number($("#amount_billed_hidden_"+load_id).val());
				//alert(actual_revenue);
				//alert(amount_billed);
				if(actual_revenue != amount_billed)
				{
					var short_pay = amount_billed - actual_revenue;
					if (confirm("It looks like this load was shortpaid $"+ short_pay +"! Are you sure you want to continue?")) 
					{
						//SUBMIT THE FORM
						//$("#billing_form_"+load_id).submit();
						submit_billing_checklist_update();
					}
				}
				else
				{
					//SUBMIT THE FORM
					//$("#billing_form_"+load_id).submit();
					submit_billing_checklist_update();
				}
			}
			else
			{
				//SUBMIT THE FORM
				//$("#billing_checklist_form").submit();
				submit_billing_checklist_update();
				
				
			}
		
		}
	}
	
	function submit_billing_checklist_update()
	{
		//alert('sending data');
		var form = $("#billing_checklist_form")[0];
		var formData = new FormData(form);
		$("#action_icon_"+selected_row).attr('src','/images/loading.gif');
		$("#billing_checklist_dialog" ).dialog('close');
		$.ajax( {
			url: '<?= base_url('index.php/billing/save_checklist_update')?>',
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
					open_row_details(selected_row);
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
	
	//AJAX FOR GETTING NOTES
    function open_notes(row_id)
    {
        // GET THE DIV IN DIALOG BOX
        var this_div = $('#add_notes_dialog');
        
		//RESET LOADING GIF
        this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px; margin-left:200px; margin-top:150px;" />');
		
		//ADD BLUE BOX
        selected_row = row_id;
        $("#row_"+row_id).addClass("blue_border");
        
        //OPEN THE DIALOG BOX
        $("#add_notes_dialog").dialog("open");
        
        //alert('inside ajax');
        
        //POST DATA TO PASS BACK TO CONTROLLER
        var dataString = "&load_id="+row_id;
        // AJAX!
        $.ajax({
            url: "<?= base_url("index.php/billing/open_notes_dialog/")?>"+"/"+row_id, // in the quotation marks
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
        return false; 
    }//end open_add_notes()
	
	function save_note()
	{
		var dataString = $("#add_note_form").serialize();
		//alert(dataString);
        //CLEAR TEXT AREA
        //$("#new_note").val("");
        
        //-------------- AJAX TO LOAD TRUCK DETAILS -------------------
        // GET THE DIV IN DIALOG BOX
        var this_div = $('#add_notes_dialog');
        
        //POST DATA TO PASS BACK TO CONTROLLER
        
        // AJAX!
        $.ajax({
            url: "<?= base_url("index.php/billing/save_note")?>", // in the quotation marks
            type: "POST",
            data: dataString,
            cache: false,
            context: this_div, // use a jquery object to select the result div in the view
            statusCode: {
                200: function(response){
                    // Success!
                    this_div.html(response);
                    refresh_row(selected_row);
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
	
	function open_process_audit(row_id)
	{
		//-------------- AJAX -------------------
		// GET THE DIV IN DIALOG BOX
		var target_div = $('#process_audit_dialog');
		target_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="width:25px; height:25px; margin-top:250px;" /></div>');
		
		//ADD BLUE BOX
        selected_row = row_id;
        $("#row_"+row_id).addClass("blue_border");
		
		$('#process_audit_dialog').dialog('open');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = '&row_id='+row_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/billing/load_process_audit_dialog")?>", // in the quotation marks
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
	
	function open_file_upload(row_id)
	{
		//-------------- AJAX -------------------
		// GET THE DIV IN DIALOG BOX
		var target_div = $('#file_upload_dialog');
		target_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="width:25px; height:25px; margin-top:50px;" /></div>');
		
		//ADD BLUE BOX
        selected_row = row_id;
        $("#row_"+row_id).addClass("blue_border");
		
		$('#file_upload_dialog').dialog('open');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = '&row_id='+row_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/billing/load_file_upload_dialog")?>", // in the quotation marks
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
	
	function upload_attachment_file()
	{
		//alert('yo');
		var form = $("#upload_file_form")[0];
		var formData = new FormData(form);
		$.ajax( {
			url: '<?= base_url('index.php/billing/upload_load_attachment')?>',
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
					open_row_details(selected_row);
					
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
	
	var print_view = "No";
	function printer_icon_pressed()
	{
		if(print_view == 'Yes')
		{
			$("#scrollable_content").height($("#body").height() - 195);
			print_view = 'No';
		}
		else
		{
			$("#scrollable_content").css('height','auto');
			print_view = 'Yes';
		}
	}
	
</script>