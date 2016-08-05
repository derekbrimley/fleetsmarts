<script>
	$(document).ready(function()
	{
		$("#main_content").height($(window).height() - 114);
		$("#scrollable_filter_div").height($(window).height() - 184);
		
		$(".dp").datepicker();
		// //CREATE NEW ACCOUNT DIALOG
		$( "#create_new_ticket").dialog(
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
								validate_new_ticket_form();
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
						load_new_ticket_form_div();
						$('#success_div').hide();
					},//end open function
				close: function() 
					{
						//RESET ALL FEILDS
						// $("#business_user_id").val('Select');
						// $("#account_with").val('Select');
						
						// $('#new_account_form_div').html("");
					}
		});//end add notes dialog
		
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
								var ticket_id = $("#ticket_id").val();
								save_note(ticket_id);
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
					$("#tr_"+selected_ticket).removeClass('blue_border');
				}
        });//end add notes dialog
		
		//DIALOG: UPLOAD FILE DIALOG
		$( "#file_upload_dialog" ).dialog(
		{
			autoOpen: false,
			height: 200,
			width: 400,
			modal: true,
			buttons: 
			[
				{
					text: "Upload",
					click: function() 
					{
						//SUBMIT FORM
						$("#upload_file_form").submit();
						$( this ).dialog( "close" );
						setTimeout(function()
						{
							var ticket_id = $("#ticket_id").val();
							save_ticket(ticket_id);
						},3000);
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
		
		//DIALOG: UPLOAD FILE DIALOG
		$( "#inspection_picture_dialog" ).dialog(
		{
			autoOpen: false,
			height: 200,
			width: 400,
			modal: true,
			buttons: 
			[
				{
					text: "Upload",
					click: function() 
					{
						//SUBMIT FORM
						$("#upload_inspection_pic_form").submit();
						$( this ).dialog( "close" );
						setTimeout(function()
						{
							alert('edit');
							var ticket_id = $("#ticket_id").val();
							save_inspection_ticket(ticket_id);
						},5000);
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
		
		load_tickets();
	});
	
	function open_file_upload(ticket_id)
	{
		//-------------- AJAX -------------------
		// GET THE DIV IN DIALOG BOX
		var target_div = $('#file_upload_dialog');
		target_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="width:25px; height:25px; margin-top:50px;" /></div>');
		$('#file_upload_dialog').dialog('open')
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = 'ticket_id='+ticket_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/tickets/load_ticket_file_upload")?>", // in the quotation marks
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
					alert("500 error!")
				}
			}
		});//END AJAX
	}
	
	function open_inspection_picture_dialog(inspection_id,ticket_id,pic_title)
	{
		//-------------- AJAX -------------------
		// GET THE DIV IN DIALOG BOX
		var target_div = $('#inspection_picture_dialog');
		target_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="width:25px; height:25px; margin-top:50px;" /></div>');
		$('#inspection_picture_dialog').dialog('open')
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = 'inspection_id='+inspection_id+'&ticket_id='+ticket_id+'&pic_title='+pic_title;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/tickets/load_inspection_picture_dialog")?>", // in the quotation marks
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
					alert("500 error!")
				}
			}
		});//END AJAX
	}
	
	function add_action_item(ticket_id)
	{
		
		var action_item = $("#note_"+ticket_id).val();
		var due_date = $("#due_date_"+ticket_id).val();
		var is_valid = true;
		
		if(action_item==""||due_date=="")
		{
			is_valid=false;
			alert("Please enter a note and a due date.")
		}
		if(is_valid)
		{
			$("loading_tasks_gif_"+ticket_id).show();
			
			var this_div = $("#sub_tasks");
			var dataString = $("#action_item_form_"+ticket_id).serialize();
			console.log(dataString);
			$.ajax({
				url: "<?= base_url("index.php/tickets/add_action_item")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						refresh_sub_ticket_action(ticket_id);
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
	
	function add_note(ticket_id)
	{
		
		var temp_note = $("#temp_note_text_"+ticket_id).val();
		var is_valid = true;
		
		if(temp_note=="")
		{
			is_valid=false;
			alert("Please enter a note.")
		}
		if(is_valid)
		{
			$("#note_text_"+ticket_id).val(temp_note);
			
			$("#loading_notes_gif_"+ticket_id).show();
			
			var this_div = $("#sub_notes_"+ticket_id);
			var dataString = $("#note_form_"+ticket_id).serialize();
			console.log(dataString);
			$.ajax({
				url: "<?= base_url("index.php/tickets/add_note")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						refresh_sub_ticket_action(ticket_id);
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
	
	function complete_action(action_id)
	{
		var this_div = $("#completion_date_"+action_id);
		$.ajax({
			url: "<?= base_url("index.php/tickets/complete_action")?>", // in the quotation marks
			type: "POST",
			data: {id:action_id},
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
		})
	}
	
	function details_tab_change(ticket_id)
	{
		$("#details_tab_"+ticket_id).removeClass('sub_menu_tab');
		$("#details_tab_"+ticket_id).addClass('sub_menu_tab_selected');
		$("#insurance_tab_"+ticket_id).removeClass('sub_menu_tab_selected');
		$("#insurance_tab_"+ticket_id).addClass('sub_menu_tab');
		$("#inspections_tab_"+ticket_id).removeClass('sub_menu_tab_selected');
		$("#inspections_tab_"+ticket_id).addClass('sub_menu_tab');
		$("#minibox_1_details_content_"+ticket_id).show();
		$("#minibox_1_insurance_content_"+ticket_id).hide();
		$("#minibox_1_inspections_content_"+ticket_id).hide();
		$("#edit_detail_btn_"+ticket_id).show();
		$("#edit_inspection_btn_"+ticket_id).hide();
		$("#insurance_edit_detail_btn_"+ticket_id).hide();
		$("#insurance_save_btn_"+ticket_id).hide();
	}
	
	function edit_inspection(ticket_id)
	{
		$("#edit_inspection_btn_"+ticket_id).hide();
		$("#inspection_save_btn_"+ticket_id).show();
		$(".inspection_detail_"+ticket_id).hide();
		$(".inspection_edit_"+ticket_id).show();
	}
	
	function save_inspection_ticket(ticket_id)
	{
		$("#inspection_save_btn_"+ticket_id).hide();
		$("#loading_detail_gif_"+ticket_id).show();
		
		//$("#edit_inspection_form_"+ticket_id).submit();
		
		var this_div = $("minibox_1_inspections_content_"+ticket_id);
		var dataString = $("#edit_inspection_form_"+ticket_id).serialize();
		
		
		//console.log(dataString);
		$.ajax({
            url: "<?= base_url("index.php/tickets/save_inspection")?>", // in the quotation marks
            type: "POST",
            data: dataString,
            cache: false,
            context: this_div, // use a jquery object to select the result div in the view
            statusCode: {
                200: function(response){
                    // Success!
                    refresh_sub_ticket_inspection(ticket_id);
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
	
	function insurance_sub_tab_clicked(ticket_id)
	{
		$("#insurance_tab_"+ticket_id).removeClass('sub_menu_tab');
		$("#insurance_tab_"+ticket_id).addClass('sub_menu_tab_selected');
		$("#details_tab_"+ticket_id).removeClass('sub_menu_tab_selected');
		$("#details_tab_"+ticket_id).addClass('sub_menu_tab');
		$("#inspections_tab_"+ticket_id).removeClass('sub_menu_tab_selected');
		$("#inspections_tab_"+ticket_id).addClass('sub_menu_tab');
		$("#minibox_1_details_content_"+ticket_id).hide();
		$("#minibox_1_insurance_content_"+ticket_id).show();
		$("#minibox_1_inspections_content_"+ticket_id).hide();
		$("#edit_detail_btn_"+ticket_id).hide();
		$("#edit_inspection_btn_"+ticket_id).hide();
		$("#save_btn_"+ticket_id).hide();
		$("#insurance_edit_detail_btn_"+ticket_id).show();
	}
	
	function inspections_sub_tab_clicked(ticket_id)
	{
		$("#inspections_tab_"+ticket_id).removeClass('sub_menu_tab');
		$("#inspections_tab_"+ticket_id).addClass('sub_menu_tab_selected');
		$("#details_tab_"+ticket_id).removeClass('sub_menu_tab_selected');
		$("#details_tab_"+ticket_id).addClass('sub_menu_tab');
		$("#insurance_tab_"+ticket_id).removeClass('sub_menu_tab_selected');
		$("#insurance_tab_"+ticket_id).addClass('sub_menu_tab');
		$("#minibox_1_details_content_"+ticket_id).hide();
		$("#minibox_1_insurance_content_"+ticket_id).hide();
		$("#minibox_1_inspections_content_"+ticket_id).show();
		$("#edit_detail_btn_"+ticket_id).hide();
		$("#edit_inspection_btn_"+ticket_id).show();
		$("#save_btn_"+ticket_id).hide();
		$("#insurance_edit_detail_btn_"+ticket_id).hide();
	}
	
	function tasks_tab_change(id)
	{
		// $("#details_tab_"+id).css({"background-color":"rgb(239, 239, 239)","color":"black"});
		// $("#tasks_tab_"+id).css({"background-color":"#6295FC","color":"white"});
		// $("#notes_tab_"+id).css({"background-color":"#6295FC","color":"white"});
		
		// $("#sub_details_"+id).show();
		// $("#sub_tasks_"+id).hide();
		// $("#sub_notes_"+id).hide();
		// $("#edit_btn_"+ticket_id).show();
		//console.log("here");
		//alert('hi');
		
		$("#tasks_tab_"+id).removeClass('sub_menu_tab');
		$("#tasks_tab_"+id).addClass('sub_menu_tab_selected');
		$("#payments_tab_"+id).removeClass('sub_menu_tab_selected');
		$("#payments_tab_"+id).addClass('sub_menu_tab');
		$("#minibox_2_payment_history_content_"+id).hide();
		$("#minibox_2_tasks_content_"+id).show();
	}
	
	function payments_sub_tab_cicked(id)
	{
		$("#payments_tab_"+id).removeClass('sub_menu_tab');
		$("#payments_tab_"+id).addClass('sub_menu_tab_selected');
		$("#tasks_tab_"+id).removeClass('sub_menu_tab_selected');
		$("#tasks_tab_"+id).addClass('sub_menu_tab');
		$("#minibox_2_tasks_content_"+id).hide();
		$("#minibox_2_payment_history_content_"+id).show();
	}
	
	function edit_insurance_ticket(ticket_id)
	{
		$(".insurance_"+ticket_id).hide();
		$(".edit_insurance_"+ticket_id).show();
		$("#insurance_edit_detail_btn_"+ticket_id).hide();
		$("#insurance_save_btn_"+ticket_id).show();
	}
	
	function edit_ticket(ticket_id)
	{
		$(".detail_"+ticket_id).hide();
		$(".edit_"+ticket_id).show();
		$("#edit_detail_btn_"+ticket_id).hide();
		$("#save_btn_"+ticket_id).show();
	}
	
	function close_ticket_clicked(ticket_id)
	{
		if(confirm("Are you sure you want to close the ticket?"))
		{
			//$("#save_btn_"+ticket_id).hide();
			//$("#loading_detail_gif_"+ticket_id).show();
			
			var ajax_div = $("#sub_details_"+ticket_id);
			var form = $("#edit_ticket_form_"+ticket_id);
			var dataString = form.serialize();
			//console.log(dataString);
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/tickets/close_ticket")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: ajax_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						// sub_div.html(response);
						refresh_sub_ticket(ticket_id);
						refresh_ticket_row(ticket_id);
						// $(".edit_"+ticket_id).hide();
						// $(".detail_"+ticket_id).show();
						// $("#edit_btn_"+ticket_id).show();
						// $("#save_btn_"+ticket_id).hide();
						
						//$("#sub_ticket_div_"+ticket_id).show();
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						//main_content.html(response);
						//alert(response);
						//alert("500 error!")
					}
				}
			});//END AJAX
		}
	}
	
	function generate_insurance_claim_clicked(ticket_id)
	{
		if(confirm("Are you sure you want to generate an Insurance Claim?"))
		{
			//$("#save_btn_"+ticket_id).hide();
			//$("#loading_detail_gif_"+ticket_id).show();
			
			var ajax_div = $("#sub_details_"+ticket_id);
			var form = $("#edit_ticket_form_"+ticket_id);
			var dataString = form.serialize();
			//console.log(dataString);
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/tickets/generate_insurance_claim")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: ajax_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						// sub_div.html(response);
						//refresh_sub_ticket(ticket_id);
						//refresh_ticket_row(ticket_id);
						load_tickets();
						// $(".edit_"+ticket_id).hide();
						// $(".detail_"+ticket_id).show();
						// $("#edit_btn_"+ticket_id).show();
						// $("#save_btn_"+ticket_id).hide();
						
						//$("#sub_ticket_div_"+ticket_id).show();
					},
					404: function(){
						// Page not found
						alert('page not found');
					},
					500: function(response){
						// Internal server error
						//main_content.html(response);
						//alert(response);
						//alert("500 error!")
					}
				}
			});//END AJAX
		}
	}
	
	function load_tickets_enter(e)
	{
		if(e.keyCode==13)
		{
			load_tickets();
		}
	}
	var report_ajax_call;
	function load_tickets()
	{
		//SHOW LOADING ICON
		$("#refresh_tickets").hide();
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
			url: "<?= base_url("index.php/tickets/load_report")?>", // in the quotation marks
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
	
	function load_unit_number_div()
	{
		
		var unit_type = $("#unit_type").val();
		if(unit_type=="Select")
		{
			$("#truck_number").hide();
			$("#trailer_number").hide();
		}
		else if(unit_type=="Truck")
		{
			$("#truck_number").show();
		}
		else if(unit_type=="Trailer")
		{
			$("#trailer_number").show();
		}
	}
	
	function load_new_ticket_form_div()
	{
		$('#new_ticket_form_div').show();
			
		//alert(dataString.substring(1));
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var ajax_div = $('#new_ticket_form_div');
		
		ajax_div.html("<img style='height:20px;position:relative;left:188px;top:75px;'src='<?=base_url('images/loading.gif')?>'/>");
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/tickets/load_new_ticket_form")?>", // in the quotation marks
			type: "POST",
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
	
	function notes_tab_change(id)
	{
		// $("#notes_tab_"+id).css({"background-color":"rgb(239, 239, 239)","color":"black"});
		// $("#details_tab_"+id).css({"background-color":"#6295FC","color":"white"});
		// $("#tasks_tab_"+id).css({"background-color":"#6295FC","color":"white"});
		
		// $("#sub_notes_"+id).show();
		// $("#sub_details_"+id).hide();
		// $("#sub_tasks_"+id).hide();
		// $("#edit_btn_"+id).hide();
	}
	
	//SIMPLY OPENS THE DIALOG
	function open_new_ticket_dialog()
	{
		
		$( "#create_new_ticket").dialog("open");
	}
	
	var selected_ticket = 0;
	//AJAX FOR GETTING NOTES
    function open_ticket_note(ticket_id)
    {
        //RESET LOADING GIF
        $("#notes_ajax_div").html("");
        
        $("#ticket_id").val(ticket_id); //this is the hidden field in the add notes form
        
        //OPEN THE DIALOG BOX
        $( "#add_notes").dialog( "open" );
        selected_ticket = ticket_id;
        $("#tr_"+ticket_id).addClass("blue_border");
        
        //alert('inside ajax');
                
        // GET THE DIV IN DIALOG BOX
        var notes_ajax_div = $('#notes_ajax_div');
        
        //POST DATA TO PASS BACK TO CONTROLLER
        
        // AJAX!
        $.ajax({
            url: "<?= base_url("index.php/tickets/get_notes/")?>"+"/"+ticket_id, // in the quotation marks
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
	
	function open_sub_ticket(ticket_id)
	{
		
		var this_div = $("#sub_ticket_div_"+ticket_id);
		
		this_div.html("<div style='width:970px;margin-left:10px;margin-bottom:10px;height:200px;background-color:rgb(239, 239, 239);'><img style='height:20px;position:relative;left:450px;top:75px;'src='<?=base_url('images/loading.gif')?>'/></div>");
		
		//console.log(this_div);
		if((this_div).css('display')=='none')
		{
			this_div.show();
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = "ticket_id="+ticket_id;
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/tickets/load_sub_ticket")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						
						// $("#details_tab_"+ticket_id).css({"background-color":"rgb(239, 239, 239)","color":"black"});
						// $("#tasks_tab_"+ticket_id).css({"background-color":"#6295FC","color":"white"});
						// $("#notes_tab_"+ticket_id).css({"background-color":"#6295FC","color":"white"});
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
		else
		{
			this_div.hide();
		}
		
	}
	
	function refresh_sub_ticket(ticket_id)
	{
		var this_div = $("#sub_ticket_div_"+ticket_id);
		var dataString = "ticket_id="+ticket_id;
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/tickets/load_sub_ticket")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						// $("#details_tab_"+ticket_id).css({"background-color":"rgb(239, 239, 239)","color":"black"});
						// $("#tasks_tab_"+ticket_id).css({"background-color":"#6295FC","color":"white"});
						// $("#notes_tab_"+ticket_id).css({"background-color":"#6295FC","color":"white"});
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
	
	function refresh_sub_ticket_inspection(ticket_id)
	{
		var this_div = $("#sub_ticket_div_"+ticket_id);
		var dataString = "ticket_id="+ticket_id;
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/tickets/load_sub_ticket")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						inspections_sub_tab_clicked(ticket_id);
						// $("#details_tab_"+ticket_id).css({"background-color":"rgb(239, 239, 239)","color":"black"});
						// $("#tasks_tab_"+ticket_id).css({"background-color":"#6295FC","color":"white"});
						// $("#notes_tab_"+ticket_id).css({"background-color":"#6295FC","color":"white"});
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
	
	function refresh_sub_ticket_insurance(ticket_id)
	{
		var this_div = $("#sub_ticket_div_"+ticket_id);
		var dataString = "ticket_id="+ticket_id;
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/tickets/load_sub_ticket")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						insurance_sub_tab_clicked(ticket_id);
						// $("#details_tab_"+ticket_id).css({"background-color":"rgb(239, 239, 239)","color":"black"});
						// $("#tasks_tab_"+ticket_id).css({"background-color":"#6295FC","color":"white"});
						// $("#notes_tab_"+ticket_id).css({"background-color":"#6295FC","color":"white"});
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
	
	function refresh_sub_ticket_action(ticket_id)
	{
		var this_div = $("#sub_ticket_div_"+ticket_id);
		var dataString = "ticket_id="+ticket_id;
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/tickets/load_sub_ticket")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						
						// $("#sub_details_"+ticket_id).hide();
						// $("#sub_tasks_"+ticket_id).show();
						// $("#sub_notes_"+ticket_id).hide();
						
						// $("#details_tab_"+ticket_id).css({"background-color":"#6295FC","color":"white"});
						// $("#tasks_tab_"+ticket_id).css({"background-color":"rgb(239, 239, 239)","color":"black"});
						// $("#notes_tab_"+ticket_id).css({"background-color":"#6295FC","color":"white"});

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
	
	function refresh_ticket_row(ticket_id)
	{
		var dataString = "ticket_id="+ticket_id;
		var this_div = $("#tr_"+ticket_id);
		$.ajax({
			url: "<?= base_url("index.php/tickets/load_ticket_row")?>", // in the quotation marks
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
	
	function reset_filters()
	{
		$("#ticket_search_input").val("");
		$("#truck_number_input").val("All");
		$("#trailer_number_input").val("All");
		$("#ticket_category_input").val("All");
		$("#after_incident_date_filter").val("");
		$("#before_incident_date_filter").val("");
		$("#after_action_date_filter").val("");
		$("#before_action_date_filter").val("");
		$("#after_estimated_date_filter").val("");
		$("#before_estimated_date_filter").val("");
		$("#after_completion_date_filter").val("");
		$("#before_completion_date_filter").val("");
		
		load_tickets();
	}
	
	function save_note(ticket_id)
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
            url: "<?= base_url("index.php/tickets/save_note")?>", // in the quotation marks
            type: "POST",
            data: dataString,
            cache: false,
            context: notes_ajax_div, // use a jquery object to select the result div in the view
            statusCode: {
                200: function(response){
                    // Success!
                    notes_ajax_div.html(response);
                    $("#ticket_notes_"+ticket_id).attr("title",response.replace(/<br>/gi,"\n"));
                    $("#ticket_notes_"+ticket_id).attr("src","/images/add_notes.png");
                    
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
	
	function save_insurance_ticket(ticket_id)
	{
		$("#insurance_save_btn_"+ticket_id).hide();
		$("#loading_detail_gif_"+ticket_id).show();
		var sub_div = $("#sub_details_"+ticket_id);
		var form = $("#edit_insurance_ticket_form_"+ticket_id);
		var dataString = form.serialize();
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/tickets/update_insurance")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: sub_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					refresh_sub_ticket_insurance(ticket_id);
					refresh_ticket_row(ticket_id);
					
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					//main_content.html(response);
					//alert(response);
					//alert("500 error!")
				}
			}
		});//END AJAX
	}
	
	function save_ticket(ticket_id)
	{
		$("#save_btn_"+ticket_id).hide();
		$("#loading_detail_gif_"+ticket_id).show();
		
		var sub_div = $("#sub_details_"+ticket_id);
		var form = $("#edit_ticket_form_"+ticket_id);
		var dataString = form.serialize();
		console.log(dataString);
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/tickets/save_ticket")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: sub_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					refresh_sub_ticket(ticket_id);
					refresh_ticket_row(ticket_id);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					//main_content.html(response);
					//alert(response);
					//alert("500 error!")
				}
			}
		});//END AJAX
	}
	
	function trailer_inspection_type_selected(ticket_id)
	{
		console.log("clicked");
		var selection = $("#trailer_inspection_type_"+ticket_id).val();
		console.log(selection);
		if(selection=="Quick")
		{
			$(".full_trailer_inspection_"+ticket_id).hide();
		}
		else
		{
			$(".full_trailer_inspection_"+ticket_id).show();
		}
	}
	
	function truck_inspection_type_selected(ticket_id)
	{
		console.log("clicked");
		var selection = $("#truck_inspection_type_"+ticket_id).val();
		console.log(selection);
		if(selection=="Quick")
		{
			$(".full_truck_inspection_"+ticket_id).hide();
		}
		else
		{
			$(".full_truck_inspection_"+ticket_id).show();
		}
	}
	
	function validate_new_ticket_form()
	{
		var isValid = true;
		
		if($("#unit_type").val()=="Select")
		{
			alert('You must select Unit Type!');
			isValid = false;
		}
		
		if($("#unit_type").val()=="Truck")
		{
			if($("#truck_id_dropdown").val()=="Select")
			{
				alert('You must select Truck Number!');
				isValid = false;
			}
		}
		else if($("#unit_type").val()=="Trailer")
		{
			if($("#trailer_id_dropdown").val()=="Select")
			{
				alert('You must select Trailer Number!');
				isValid = false;
			}
		}
		
		if($("#category_dropdown").val()=="Select")
		{
			alert('You must select a Category!');
			isValid = false;
		}
		
		if($("#description_input").val()=="")
		{
			alert('You must enter a Description!');
			isValid = false;
		}
		
		if(!$("#responsible_party_dropdown").val())
		{
			alert('You must enter a Responsible Party!');
			isValid = false;
		}
		
		if($("#incident_date_input").val()=="")
		{
			alert('You must enter in an Incident Date!');
			isValid = false;
		}
		
		if($("#estimated_completion_date_input").val()=="")
		{
			alert('You must enter in an Estimated Completion Date!');
			isValid = false;
		}
		
		if($("#amount_input").val())
		{
			if(isNaN($("#amount_input").val()))
			{
				alert("Invoice Amount must be a number!");
				isValid = false;
			}
		}
		else
		{
			alert("You must enter in an Amount!");
			isValid = false;
		}
		
		if(isValid)
		{
			$('#new_ticket_form_div').hide();
			$('#success_div').show();
				
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var ajax_div = $('#new_ticket_form_div');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#new_ticket_form").serialize();
			console.log(dataString);
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/tickets/create_new_ticket")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: ajax_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						$("#create_new_ticket").dialog("close");
						
						load_tickets();
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