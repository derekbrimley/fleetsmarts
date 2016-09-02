<script>
	$(document).ready(function()
	{
		$("#main_content").height($(window).height() - 115);
		$("#scrollable_left_bar").height($(window).height() - 182);
		$('.datepicker').datepicker({ showAnim: 'blind' });
	
		//LOAD INITIAL LIST OF LOADS
		load_list();
		
		setInterval(function() {
			if(tv_mode == 'on')
			{
				load_list();
			}
		}, 30000);//1000 = 1 second
		
		//ADD NEW LOAD DIALOG
		$( "#add_load_dialog" ).dialog(
		{
			autoOpen: false,
			height: 510,
			width: 325,
			modal: true,
			buttons: 
				[
					{
						style: "",
						id: "clear_all_load_info_button",
						text: "Clear All",
						click: function()
						{
							clear_load_info();
						},
					},
					{
						style: "margin-left:70px;",
						text: "Add Load",
						click: function() 
						{
							validate_add_load_form();
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
					//$("#focus_stealer").hide();
				},//end open function
			close: function() 
				{
					//clear_load_info();
				}
		});//end dialog form
	
		//RATE CON RECEIVED DIALOG
		$( "#rate_con_received_dialog" ).dialog(
		{
				autoOpen: false,
				height: 700,
				width: 488,
				modal: true,
				buttons: 
					{
						"Save": function() 
							{
								validate_rate_con();
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
	
		//RATE CON RECEIVED DIALOG
		$( "#load_dispatch_dialog" ).dialog(
		{
				autoOpen: false,
				height: 700,
				width: 970,
				modal: true,
				buttons: 
					{
						"Save": function() 
							{
								validate_dispatch_update();
							},//end create an user
						
						Cancel: function() 
							{
								$( this ).dialog( "close" );
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
								var row_id = $("#row_id").val();
								save_note(row_id);
							}
						},//end add ticket
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
				close: function() 
				{
					//REMOVE ANY BLUE BORDER BOXES
					$("#row_"+selected_row).removeClass('blue_border');
				}
        });//end add notes dialog
	
		//CANCEL LOAD REASON DIALOG
		$( "#cancel_load_reason_dialog" ).dialog(
		{
			autoOpen: false,
			height: 200,
			width: 530,
			modal: true,
			buttons: 
				[
					{
						text: "Confirm",
						click: function() 
						{
							//VALIDATE THAT A REASON IS GIVEN
							validate_cancel_load();
						},//end add load
					},
					{
						text: "Never Mind",
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
					//RESET HIDDEN FIELD
					$("#cancelled_load_id").removeAttr('value');
					$("#load_cancel_reason").val('');
					
					//REMOVE ANY BLUE BORDER BOXES
					$("#row_"+selected_row).removeClass('blue_border');
				}
		});//end dialog form
			
		//RATE CON RECEIVED DIALOG
		$( "#mark_goalpoint_complete_dialog" ).dialog(
		{
				autoOpen: false,
				height: 700,
				width: 900,
				modal: true,
				buttons: 
					{
						"Save": function() 
							{
								mark_goalpoint_complete();
							},//end create an user
						
						Cancel: function() 
							{
								$( this ).dialog( "close" );
							}
					},//end of buttons
				
				open: function()
					{
					},//end open function
				close: function() 
					{
					}//end close function
		});//end dialog form
		
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
							$("#upload_file_form").submit();
							$( this ).dialog( "close" );
							
							setTimeout(function()
							{
								open_row_details(selected_row);
							},3000);
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
		
		//LOAD PLAN EMAIL DIALOG
		$( "#load_plan_email_dialog" ).dialog(
		{
				autoOpen: false,
				height: 800,
				width: 1030,
				modal: true,
				buttons: 
					{
						"Send": function() 
							{
								send_load_plan_email();
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
		
	});
	
	//CREATE MC NUMBER ARRAY
	var mc_number_array = [
	<?php 	
			$array_string = "";
			foreach($brokers as $broker)
			{
				$value = $broker['mc_number'];
				$array_string = $array_string.'"'.$value.'",';
			}
			echo substr($array_string,0,-1);
	?>];
	
	
	//CREATE BROKERS ARRAY
	var broker_auto_complete = [
	<?php 	
			$array_string = "";
			foreach($brokers as $broker)
			{
				$broker_name = $broker['customer_name'];
				$array_string = $array_string.'"'.$broker_name.'",';
			}
			echo substr($array_string,0,-1);
	?>];
	
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
	function load_list()
	{
		abort_ajax_requests();
		
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
			url: "<?= base_url("index.php/loads/load_list")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					
					//GET UPDATE TIME
					var today = new Date();
					var m = today.getMonth()+1;
					var d = today.getDate();
					var h = today.getHours();
					var i = today.getMinutes();
					
					if(m < 10)
					{
						m = "0"+m;
					}
					
					if(i < 10)
					{
						i = "0"+i;
					}
					
					$("#last_update").html("Updated: "+m+"/"+d+" "+h+":"+i);
					//$("#filter_loading_icon").hide();
					//$("#refresh_list").show();
					//load_summary_stats();
					//alert(response);
					if(tv_mode == 'on')
					{
						$("#main_content").css('height','auto');
						$("#scrollable_content").css('height','auto');
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
	
	function open_add_new_load_dialog()
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#add_load_dialog');
			
		this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:20px; margin-left:150px; margin-top:150px;" />');
		
		this_div.dialog('open');
		
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/loads/load_add_new_load_dialog")?>", // in the quotation marks
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
					//alert(response);
					alert("500 error!");
					//$("#main_content").html(response);
				}
			}
		});//END AJAX
	}
	
	function open_cancel_load_dialog(row_id)
	{
		$("#cancelled_load_id").val(row_id);
		
		$( "#cancel_load_reason_dialog" ).dialog('open');
		
		//ADD BLUE BOX
        selected_row = row_id;
        $("#row_"+row_id).addClass("blue_border");
	}
	
	function validate_cancel_load()
	{
		var isValid = true;
		
		if(!$("#load_cancel_reason").val())
		{
			isValid = false;
			alert('Reason for load cancel must be entered!');
		}
		
		if(isValid)
		{
			
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#details_'+selected_row);
			//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
				
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#cancel_load_form").serialize();
			$( "#cancel_load_reason_dialog" ).dialog('close');
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/loads/cancel_load")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						//this_div.html(response);
						open_row_details(selected_row);
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
			url: "<?= base_url("index.php/loads/open_details")?>", // in the quotation marks
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
			url: "<?= base_url("index.php/loads/load_file_upload_dialog")?>", // in the quotation marks
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
	
	//REFRESH ROW
	function refresh_row(row_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#row_'+row_id);
			
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&load_id="+row_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/loads/refresh_row")?>", // in the quotation marks
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
		
		
		if($("#truck_is_already_assigned").val() == "yes")
		{
			isValid = false;
			alert("This Truck is already assigned to an active load!");
		}
		
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
		
		if($("#edit_is_reefer_"+row_id).val() == "Reefer")
		{
			if(!$("#edit_reefer_low_set_"+row_id).val() || !$("#edit_reefer_low_set_"+row_id).val())
			{
				isValid = false;
				alert("Reefer Temps must be set!");
			}
			else if(isNaN($("#edit_reefer_low_set_"+row_id).val()) || isNaN($("#edit_reefer_high_set_"+row_id).val()))
			{
				isValid = false;
				alert("Reefer Temps must be numbers!");
			}
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
				url: "<?= base_url("index.php/loads/save_load_edit")?>", // in the quotation marks
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
	
	//CHECK FOR ASSIGNED LOADS FOR THIS TRUCK
	function check_if_truck_is_assigned(row_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#ajax_script_div');
			
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&truck_id="+$("#edit_truck_"+row_id).val()+"&load_id="+row_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/loads/check_if_truck_is_assigned")?>", // in the quotation marks
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
					//alert(response);
					alert("500 error!");
					//$("#main_content").html(response);
				}
			}
		});//END AJAX
	}
	
	var selected_row = 0;
	//AJAX FOR GETTING NOTES
    function open_notes(row_id)
    {
        //RESET LOADING GIF
        $("#notes_ajax_div").html("");
        
        $("#row_id").val(row_id); //this is the hidden field in the add notes form
        
        //OPEN THE DIALOG BOX
        $("#add_notes").dialog("open");
		
		//ADD BLUE BOX
        selected_row = row_id;
        $("#row_"+row_id).addClass("blue_border");
        
        //alert('inside ajax');
                
        // GET THE DIV IN DIALOG BOX
        var notes_ajax_div = $('#notes_ajax_div');
        
        //POST DATA TO PASS BACK TO CONTROLLER
        
        // AJAX!
        $.ajax({
            url: "<?= base_url("index.php/loads/get_notes/")?>"+"/"+row_id, // in the quotation marks
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
	
	function save_note(row_id)
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
            url: "<?= base_url("index.php/loads/save_note")?>", // in the quotation marks
            type: "POST",
            data: dataString,
            cache: false,
            context: notes_ajax_div, // use a jquery object to select the result div in the view
            statusCode: {
                200: function(response){
                    // Success!
                    notes_ajax_div.html(response);
                    $("#notes_icon_"+row_id).attr("title",response.replace(/<br>/gi,"\n"));
                    $("#notes_icon_"+row_id).attr("src","/images/add_notes.png");
                    
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
	
	//ADD NEW LOAD DIALOG: BROKER IS NEW CHECKBOX CLICKED
	function broker_is_new_clicked()
	{
		if ($('#broker_is_new').is(':checked'))
		{
			$("#broker_found_tr").hide();
			$("#broker_name_row").show();
		}
		else
		{
			$("#broker_name_row").hide();
			$("#broker_found_tr").show();
		}
	}
	
	//AJAX FOR MARK LOAD PICK DIALOG
	function search_for_broker()
	{
		//alert('searching for broker');
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#broker_found_span');
		this_div.hide();
		
		var mc_number = $("#broker_mc").val();
		
		if(mc_number)
		{
			//POST DATA TO PASS BACK TO CONTROLLER
			var data = "mc_number=" + mc_number;
			
			// AJAX!
			$.ajax({
				url: "<?= base_url('index.php/loads/search_for_broker')?>", // in the quotation marks
				type: "POST",
				data: data,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						this_div.show();
						
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
	
	//CLEAR ALL THE FIELDS FOR NEW LOAD DIALOG
	function clear_load_info()
	{
		open_add_new_load_dialog();
		
		//alert($("#add_load_dialog").height());
		
		//HIDE ALERTS	

		// $("#client_alert").hide();
		// $("#broker_alert").hide();
		// $("#expected_revenue_alert").hide();
		// $("#broker_not_found_alert").hide();
		// $("#broker_not_new_alert").hide();
		// $("#expected_revenue_nan_alert").hide();
	
		// //CLEAR ALL FIELDS
		// $("#new_load_fm_dropdown").val("Select");
		// $("#carrier_id").val("Select");
		// $("#client_id").val("Select");
		// $("#originals_required").val("Select");
		// $("#broker").val("");
		// $("#expected_revenue").val("");
		// $("#load_notes").val("");
		// $("#pick_internal_notes_1").val("");
		// $("#drop_internal_notes_1").val("");
		// $("#rate_con_link").val("");
		
		// current_drop = 1;
		// current_pick = 1;
		// noalv = 0;
	
		//$("#add_load_dialog").height(267);
	}//end clear_load_info()
	
	//VALIDATION FOR ADD LOAD
	function validate_add_load_form()
	{
		
		$("#fm_alert").hide();
		$("#client_alert").hide();
		$("#broker_alert").hide();
		$("#broker_not_found_alert").hide();
		$("#broker_not_new_alert").hide();
		$("#expected_revenue_alert").hide();
		$("#expected_revenue_nan_alert").hide();
		
		var isvalid = true;
		
		if($("#new_load_fm_dropdown").val() == 'Select')
		{
			isvalid = false;
			alert("You must select a Fleet Manager!");
		}
		
		if($("#carrier_id").val() == "Select")
		{
			isvalid = false;
			alert("You must select Booked Under!");
		}
		
		if($("#originals_required").val() == 'Select')
		{
			isvalid = false;
			alert("You must select a Originals Required!");
		}
		
		if($("#originals_required").val() == 'No')
		{
			if(!$("#proof_notes").val())
			{
				isvalid = false;
				alert("You must enter in instructions to the dispatchers of where to find Proof from the broker that no originals are required for payment!");
			}
		}
		
		//VALIDATE BROKER
		if($("#broker_mc").val() == "")
		{
			isvalid = false;
			alert("You must enter in a Broker MC!");
		}
		else
		{
			//DOES THE BROKER EXIST?
			var broker_found = false;
			for(var broker_mc in mc_number_array)
			{
				if($("#broker_mc").val() == mc_number_array[broker_mc])
				{
					broker_found = true;
					break;
				}
			}
			//IF THIS BROKER DOESN'T EXIST IN THE SYSTEM
			if(!broker_found)
			{
				//IF THE NEW BOX IS CHECKED
				if($("#broker_is_new").is(':checked'))
				{
					$("#broker_is_new").val("new");
				}
				else
				{
					isvalid = false;
					alert("Broker not found in the system!");
				}
			}
			else //IF BROKER ALREADY EXISTS
			{
				//AND THE USER HAS CHECKED THE NEW BROKER BOX
				if($("#broker_is_new").is(':checked'))
				{
					//ALERT THE USER THAT THIS IS NOT A NEW BROKER
					isvalid = false;
					alert("This is not a New Broker!");
				}
			}
		}
		
		//VALIDATE EXPECTED MILES
		if($("#expected_miles").val() == "")
		{
			isvalid = false;
			alert("You must enter in an Expected Miles!");
		}
		else
		{
			//IS THE "EXPECTED REVENUE" A NUMBER?
			if (isNaN($("#expected_miles").val()))
			{
				isvalid = false;
				alert("Expected Miles must be a number!");
			}
		}
		
		//VALIDATE EXPECTED REVENUE
		if($("#expected_revenue").val() == "")
		{
			isvalid = false;
			alert("You must enter in an Expected Rate!");
		}
		else
		{
			//IS THE "EXPECTED REVENUE" A NUMBER?
			if (isNaN($("#expected_revenue").val()))
			{
				isvalid = false;
				alert("Expected Rate must be a number!");
			}
		}
		
		//IF THE INPUTS ARE VALID THEN SUBMIT!
		if (isvalid)
		{	
			$('#add_load_form').submit();
			$("#add_load_dialog").dialog("close");
			//alert('good');
			
			setTimeout(load_list,3000);
		}
	}//end validate_add_load_form()
	
	//CHANGE LOAD STATUS
	function load_status_changed(row_id,action)
	{
		//alert('load_status_changed');
		$("#load_number").val(row_id);
		
		//IF ACTION = OPEN LOAD DETAILS (LOAD IS DROPPED OR CANCELLED)
		if(action == "open_load_details")
		{
			open_row_details(row_id)
		}
		
		//IF ACTION = RATE CON RECEIVED
		if(action == "open_rc_dialog")
		{
			rate_con_received(row_id)
		}
		
		//IF ACTION = DISPATCH LOAD
		if(action == "open_dispatch_dialog")
		{
			open_load_dispatch_dialog(row_id)
		}
		
		//IF ACTION = MARK GOALPOINT COMPLETE
		if(action == "open_complete_goalpoint_dialog")
		{
			var gp_id = $("#next_pick_drop_goalpoint_id_"+row_id).val();
			if(gp_id)
			{
			open_mark_goalpoint_complete_dialog(gp_id,row_id)
			}
			else
			{
				alert('There is no next picks or drops to mark complete!');
			}
		}
		
		//IF ACTION = 
		if(action == "alert_missing_gp_gps")
		{
			alert('Goalpoint must have GPS before marking complete!');
		}
		
		//IF ACTION = 
		if(action == "alert_missing_gp_truck")
		{
			alert('Goalpoint must have a truck before marking complete!');
		}
		
		
	}
	
	//OPEN LOAD DISPATCH DIALOG
	function open_load_dispatch_dialog(load_number)
	{
		var row_id = load_number;
		
		//alert('inside ajax');
		$("#load_dispatch_dialog").html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px; margin-left:445px; margin-top:250px;" />');
		$("#load_dispatch_dialog" ).dialog( "open" );
		
		selected_row = row_id;
		$("#row_"+row_id).addClass("blue_border");
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#load_dispatch_dialog');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var data = "load_number=" + load_number; //use & to separate values
		
		// AJAX!
		$.ajax({
			url: "<?= base_url('index.php/loads/open_load_dispatch_dialog')?>", // in the quotation marks
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
	}//END RATE CON RECIEVED()
	
	//OPEN RATE CON RECEIVED DIALOG
	function rate_con_received(load_number)
	{
		var row_id = load_number;
		
		//alert('inside ajax');
		$("#rate_con_received_dialog").html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px; margin-left:230px; margin-top:250px;" />');
		$("#rate_con_received_dialog" ).dialog( "open" );
		
		selected_row = row_id;
		$("#row_"+row_id).addClass("blue_border");
		
		// GET THE DIV IN DIALOG BOX
		var rate_con_received_div = $('#rate_con_received_dialog');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var data = "load_number=" + load_number; //use & to separate values
		
		// AJAX!
		$.ajax({
			url: "<?= base_url('index.php/loads/rate_con_receieved_ajax')?>", // in the quotation marks
			type: "POST",
			data: data,
			cache: false,
			context: rate_con_received_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					rate_con_received_div.html(response);
					
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
		
		
	}//END RATE CON RECIEVED()
	
	//RATE CON RECEIVED DIALOG: FREIGHT TYPE SELECTED
	function new_load_orignals_required_selected()
	{
		if($("#originals_required").val() == "Yes")
		{
			$("#new_load_proof_notes_row").hide();
		}
		else
		{
			$("#new_load_proof_notes_row").show();
		}
	}
	
	//RATE CON RECEIVED DIALOG: FREIGHT TYPE SELECTED
	function rcr_orignals_required_selected()
	{
		if($("#rcr_originals_required").val() == "Yes")
		{
			$("#original_proof_row").hide();
		}
		else
		{
			$("#original_proof_row").show();
		}
	}
	
	//RATE CON RECEIVED DIALOG: FREIGHT TYPE SELECTED
	function rcr_freight_type_selected()
	{
		if($("#rcr_is_reefer").val() == "Reefer")
		{
			$("#rcr_reefer_temp_row").show();
		}
		else
		{
			$("#rcr_reefer_temp_row").hide();
		}
	}
	
	function fill_in_rcr_address_fields(pick_drop,i)
	{
		//alert('reverse geocoding');
		var cell_value = $("#rcr_"+pick_drop+"_gps_"+i).val();
		
		if(cell_value)
		{
			var stripped_address = cell_value.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
			//alert(stripped_address);
			if(stripped_address && !isNaN(stripped_address))
			{
				var latlng_array = cell_value.split(",");
				var lat = latlng_array[0];
				var lng =  latlng_array[1];
				//alert(lat);
				//alert(lng);
				var latlng = new google.maps.LatLng(lat, lng);
				
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode( { 'location': latlng}, function(results, status)
				{
					//alert(status);
					if (status == google.maps.GeocoderStatus.OK) 
					{
						var location_type = results[0].geometry.location_type;
						
						//alert("Google Approves =)");
						// var geo_city = results[0]['address_components'][2]['long_name'];
						// var geo_state = results[0]['address_components'][4]['short_name'];
						
						//alert(geo_city);
						//alert(geo_state);
						
						//$("#gps_city").val(results[0]['address_components'][2]['long_name']);
						//$("#gps_state").val(results[0]['address_components'][4]['short_name']);
						
						var street_number = extractFromAdress(results[0].address_components, "street_number");
						var street = extractFromAdress(results[0].address_components, "route");
						var geo_city = extractFromAdress(results[0].address_components, "locality");//CITY
						var geo_state = extractFromAdress(results[0].address_components, "administrative_area_level_1");//STATE
						
						//alert(geo_city);
						//alert(geo_state);
						
						$("#rcr_"+pick_drop+"_address_"+i).html(street_number+" "+street);
						$("#rcr_"+pick_drop+"_city_"+i).val(geo_city);
						$("#rcr_"+pick_drop+"_state_"+i).val(geo_state);
					} 
					else 
					{
						alert('Uh oh!Google returned the following: ' + status);
					}
				});
			}
		}
		else
		{
			//$("#"+div).html("");
		}
	}
	
	//VALIDATE RATE CON RECEIVED DIALOG SAVE
	function validate_rate_con()
	{
		var isvalid = true;
	
		//VALIDATE BILLING INFO
		var client = $("#rcr_client_dropdown").val();
		var billed_under = $("#rcr_billed_under_dropdown").val();
		var billing_method = $("#rcr_billing_method_dropdown").val();
		var natl_fuel_avg = $("#natl_fuel_avg").val();
		
		if(billed_under == 0)
		{
			isvalid = false;
			alert('Driver must be selected!');
		}
		
		if(billing_method == "Select")
		{
			isvalid = false;
			alert('Billing Method must be selected!');
		}
		
		if($("#rcr_originals_required").val() == "No")
		{
			if(!$("#rcr_proof_of_no_org").val())
			{
				isvalid = false;
				alert('Proof of No Orignals Required must be chosen!');
			}
		}
		
		if (!natl_fuel_avg || isNaN(natl_fuel_avg))
		{
			isvalid = false;
			alert("Nat'l Fuel Avg must be entered and must be a number!");
		}
	
		//VALIDATE LOAD INFO
		var load_number = $("#rcr_load_number").val();
		var fleet_manager = $("#rcr_fleet_manager_dropdown").val();
		var broker = $("#rcr_broker").val();
		var contact_info = $("#rcr_contact_info").val();
		var expected_revenue = $("#rcr_expected_revenue").val();
		
		if(!load_number)
		{
			isvalid = false;
			alert("Load Number must be entered!");
		}

		if(fleet_manager == 0)
		{
			isvalid = false;
			alert('Fleet Manager must be selected!');
		}
		
		if($("#rcr_driver_manager_dropdown").val() == 'Select')
		{
			isvalid = false;
			alert('Driver Manager must be selected!');
		}
		
		if(broker)
		{
			//DOES THE BROKER EXIST IN THE SYSTEM?
			var broker_found = false;
			for(var ibroker in rcr_broker_auto_complete)
			{
				if(broker == rcr_broker_auto_complete[ibroker])
				{
					broker_found = true;
					break;
				}
			}
			
			if(!broker_found)
			{
				isvalid = false;
				alert("Broker not found in the system!");
			}
		}
		else
		{
			isvalid = false;
			alert("Broker must be entered!");
		}
		
		if(!contact_info)
		{
			isvalid = false;
			alert("Contact Info must be entered!");
		}
		
		
		if (!expected_revenue || isNaN(expected_revenue))
		{
			isvalid = false;
			alert("Expected Revenue must be entered and must be a number!");
		}
	
		if(client == 0)
		{
			isvalid = false;
			alert('Driver must be selected!');
		}
		
		if($("#rcr_is_reefer").val() == "Reefer")
		{
			if (!$("#rcr_reefer_low_set").val() || isNaN($("#rcr_reefer_low_set").val()) || !$("#rcr_reefer_high_set").val() || isNaN($("#rcr_reefer_high_set").val()))
			{
				isvalid = false;
				alert("Reefer Temp Range must be entered and must be a number!");
			}
		}
		
		//FOR EACH PICK
		for(var i = 1; i <=5 ;i++)
		{
			//IF DIV IS VISABLE, VALIDATE
			if (!$("#rcr_pick_div_"+i).is(":hidden"))
			{
				//GET ALL FIELD INFO
				var date = $("#rcr_pick_date_"+i).val();
				var app_hour = $("#rcr_pick_app_hour_"+i).val();
				var app_minute = $("#rcr_pick_app_minute_"+i).val();
				var app_ampm = $("#rcr_pick_app_ampm_"+i).val();
				var app_timezone = $("#rcr_pick_app_timezone_"+i).val();
				var app_time_tba = $("#rcr_pick_app_time_tba_"+i);
				var city = $("#rcr_pick_city_"+i).val();
				var state = $("#rcr_pick_state_"+i).val();
				var address = $("#rcr_pick_address_"+i).val();
				var pu_number = $("#rcr_pick_pu_number_"+i).val();
				
				//VALIDATE DATE
				if(!date)
				{
					isvalid = false;
					alert('Date must be entered for Pick '+i+'!');
				}else
				{
					if(!isDate(date))
					{
						isvalid = false;
						alert('Date must be entered in a valid format (mm/dd/yyyy) for Pick '+i+'!');
					}
				}
				
				//VALIDATE APPOITNMENT TIME
				if(app_hour == '--' || app_minute == '--' || app_ampm == '--' || app_timezone == '---')
				{
					//IF THE TBA BOX ISN'T CHECKED
					if(!(app_time_tba.is(':checked')))
					{
						isvalid = false;
						alert('Appointment Time must be entered for Pick '+i+'!');
					}
				}
				
				//VALIDATE CITY
				if(!city)
				{
					isvalid = false;
					alert('City must be entered for Pick '+i+'!');
				}
				
				//VALIDATE STATE
				if(!state)
				{
					isvalid = false;
					alert('State must be entered for Pick '+i+'!');
				}
				
				//VALIDATE ADDRESS
				if(!address)
				{
					isvalid = false;
					alert('Address must be entered for Pick '+i+'!');
				}
				
				//VALIDATE PU NUMBER
				//if(!pu_number)
				//{
				//	isvalid = false;
				//	$("#pu_number_alert_pick_"+i).show();
				//}
				
			}//END IF DIV IS VISABLE
		
		}//END FOR EACH PICK
		
		//FOR EACH DROP
		for(var i = 1; i <=5 ;i++)
		{
			//IF DIV IS VISABLE, VALIDATE
			if (!$("#rcr_drop_div_"+i).is(":hidden"))
			{
				//GET ALL FIELD INFO
				var date = $("#rcr_drop_date_"+i).val();
				var app_hour = $("#rcr_drop_app_hour_"+i).val();
				var app_minute = $("#rcr_drop_app_minute_"+i).val();
				var app_ampm = $("#rcr_drop_app_ampm_"+i).val();
				var app_timezone = $("#rcr_drop_app_timezone_"+i).val();
				var app_time_tba = $("#rcr_drop_app_time_tba_"+i);
				var city = $("#rcr_drop_city_"+i).val();
				var state = $("#rcr_drop_state_"+i).val();
				var address = $("#rcr_drop_address_"+i).val();
				var ref_number = $("#rcr_drop_ref_number_"+i).val();
				
				//VALIDATE DATE
				if(!date)
				{
					isvalid = false;
					alert('Date must be entered for Drop '+i+'!');
				}
				else
				{
					if(!isDate(date))
					{
						isvalid = false;
						alert('Date must be entered in a valid format (mm/dd/yyyy) for Drop '+i+'!');
					}
				}
				
				//VALIDATE APPOINTMENT TIME
				if(app_hour == '--' || app_minute == '--' || app_ampm == '--' || app_timezone == '---')
				{
					//IF THE TBA BOX ISN'T CHECKED
					if(!(app_time_tba.is(':checked')))
					{
						isvalid = false;
						alert('Appointment Time must be entered for Drop '+i+'!');
					}
				}
				
				//VALIDATE CITY
				if(!city)
				{
					isvalid = false;
					alert('City must be entered for Drop '+i+'!');
				}
				
				//VALIDATE STATE
				if(!state)
				{
					isvalid = false;
					alert('State must be entered for Drop '+i+'!');
				}
				
				//VALIDATE ADDRESS
				if(!address)
				{
					isvalid = false;
					alert('Address must be entered for Drop '+i+'!');
				}
				
				//VALIDATE REF NUMBER
				//if(!ref_number)
				//{
				//	isvalid = false;
				//	$("#ref_number_alert_drop_"+i).show();
				//}
				
			}//END IF DIV IS VISABLE
		
		}//END FOR EACH DROP
		
		//IF IS_VALID, SUBMIT FORM
		if (isvalid)
		{
			$("#status_icon_"+row_id).attr('src','/images/loading.gif');
			
			$("#rcr_save_form").submit();
			
			$("#rate_con_received_dialog").dialog("close");
			
			setTimeout(load_list,6000);
		}
	}
	
	//VALIDATE AND SUBMIT DISPATCH UPDATE
	function validate_dispatch_update()
	{
		var isvalid = true;
		
		if(missing_deadline)
		{
			isvalid = false;
			alert("This load plan is doesn't have any deadlines! Update the load plan with some deadlines and goals for the driver before proceeding.")
		}
		
		if(!$("#current_geopoint_goalpoint_id").val())
		{
			isvalid = false;
			alert('There must be a Current Location for this Check Call!');
		}
		
		if($("#truck_codes_status").val() == "Select")
		{
			isvalid = false;
			alert('Truck Codes must be selected!');
		}
		
		if(!$("#truck_code_guid").val())
		{
			isvalid = false;
			alert('A screenshot proving the Truck Codes status must be selected!');
		}
		
		if($("#driver_answer").val() == "Yes")
		{
			if($("#truck_fuel").val() == "Select")
			{
				isvalid = false;
				alert('Truck Fuel Level must be selected!');
			}
		}
		
		if($("#truck_fuel").val() != "Select")
		{
			if(needs_load_plan)
			{
				isvalid = false;
				alert('This truck needs a Fuel Plan!');
			}
		}
		
		if($("#driver_answer").val() == "Select")
		{
			isvalid = false;
			alert('Did Driver Answer must be selected!');
		}
		
		if($("#driver_answer").val() == "Answered" || $("#driver_answer").val() == "No Answer")
		{
			if(!$("#audio_guid").val())
			{
				isvalid = false;
				alert('An audio file of the call must be selected!');
			}
		}
		
		
		var row_id = $("#dispatch_update_load_id").val();
		//alert(row_id);
		
		//alert(isvalid);
		//IF IS_VALID, SUBMIT FORM
		if(isvalid)
		{
			$("#refresh_load_details_icon_"+row_id).attr('src','/images/loading.gif');
			$("#indicator_icon_"+row_id).attr('src','/images/loading.gif');
			
			
			//$("#load_dispatch_update_form").submit();
			var form = $("#load_dispatch_update_form")[0];
			var formData = new FormData(form);
			$("#load_dispatch_dialog").dialog("close");
			//$("#indicator_icon_"+selected_row).attr('src','/images/loading.gif');
			$.ajax( {
				url: '<?= base_url('index.php/loads/save_check_call')?>',
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
			
			// setTimeout(function()
			// {
				// open_row_details(row_id);
			// },10000);//10 seconds
		}
	}
	
	function auto_fill_dispatch_update_location()//FOR EDIT GP
	{
		//alert($("#edit_gp_gps_"+gp_id).val());
		fill_in_locations("dispatch_current_location",$("#dispatch_gps").val());
	}
	
	function save_dispatch_update(row_id)
	{
		$("#save_load_update_"+row_id).attr('src','/images/loading.gif');
		$("#save_load_update_"+row_id).css('height','14px');
		$("#save_load_update_"+row_id).css('left','23px');
		
		var dataString = $("#load_update_form_"+row_id).serialize();
		//var this_div = $("#goalpoints_div_"+row_id);
		var this_div;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/loads/save_load_update")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, 
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					//this_div.html(response);
					open_row_details(row_id);
					
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
	
	function open_load_plan_email_dialog(du_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#load_plan_email_dialog');
		
		//alert('inside ajax');
		this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px; margin-left:445px; margin-top:250px;" />');
		this_div.dialog( "open" );
		
		//selected_row = row_id;
		//$("#row_"+row_id).addClass("blue_border");
		
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var data = "du_id=" + du_id; //use & to separate values
		
		// AJAX!
		$.ajax({
			url: "<?= base_url('index.php/loads/display_dispatch_email')?>", // in the quotation marks
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
	
	function send_load_plan_email()
	{
		//alert('here');
		var row_id = $("#dispatch_email_load_id").val();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#load_plan_email_dialog');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var data = "du_id=" + $("#dispatch_update_email_dialog_id").val(); //use & to separate values
		$("#refresh_load_details_icon_"+row_id).attr('src','/images/loading.gif');
		this_div.html("Sending Email Now...");
		// AJAX!
		$.ajax({
			url: "<?= base_url('index.php/loads/send_dispatch_email')?>", // in the quotation marks
			type: "POST",
			data: data,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//this_div.html(response);
					this_div.dialog('close');
					open_row_details(row_id)
					alert(response);
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
	
	function send_driver_hold_report_email(client_id)
	{
		//alert('here');
		// GET THE DIV IN DIALOG BOX
		var this_div;
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var data = "client_id=" + client_id; //use & to separate values
		
		// AJAX!
		$.ajax({
			url: "<?= base_url('index.php/loads/send_driver_hold_report_email')?>", // in the quotation marks
			type: "POST",
			data: data,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//this_div.html(response);
					//this.dialog('close');
					alert(response);
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
	
	
	var tv_mode = 'off';
	function tv_icon_clicked()
	{
		if(tv_mode == 'off')
		{
			$("#main_menu_box").hide();
			$("#left_bar").hide();
			$("#main_content").css('height','auto');
			$("#scrollable_content").css('height','auto');
			tv_mode = 'on';
		}
		else if(tv_mode == 'on')
		{
			$("#main_menu_box").show();
			$("#left_bar").show();
			$("#main_content").height($(window).height() - 115);
			$("#scrollable_content").height($(window).height() - 195);
			tv_mode = 'off';
		}
	}
	
</script>