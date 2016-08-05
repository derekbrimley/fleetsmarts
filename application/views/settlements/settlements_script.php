<script>
	$(document).ready(function()
	{
		load_list();
		
		$("#main_content").height($(window).height() - 115);
		$("#filter_list").height($(window).height() - 237);
		//$("#scrollable_content").height($(window).height() - 155);
		
		$('#start_date_filter').datepicker({ showAnim: 'blind' });
		$('#end_date_filter').datepicker({ showAnim: 'blind' });
		
		//DIALOG: ADD NEW EVENT
		$( "#fm_settlement_details" ).dialog(
		{
				autoOpen: false,
				height: 230,
				width: 350,
				modal: true,
				buttons: 
					[
						<?php if(user_has_permission("approve commissions")): ?>
							{
								text: "Settle",
								click: function() 
								{
									validate_fm_settlement();
								
								},//end add load
							},
						<?php endif; ?>
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
						
					},//end open function
				close: function() 
					{
						//$('#log_event_dialog').html("");
					}
		});//end dialog form
		
	
	});
	
	//LOAD LOG LIST
	var load_list_ajax_call;
	function load_list()
	{
		$("#refresh_list").hide();
		$("#filter_loading_icon").show();
		
		set_list_filter_fields()
	
		var form_name = "filter_form";	
		var dataString = "";
		$("#"+form_name+" select").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#"+form_name+" input").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#"+form_name+" textarea").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
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
			url: "<?= base_url("index.php/settlements/load_list")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					//$("#filter_loading_icon").hide();
					//$("#refresh_list").show();
					load_summary_stats();
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
	
	//LOAD SUMMARY STATS
	function load_summary_stats()
	{
		$("#refresh_list").hide();
		$("#filter_loading_icon").show();
		
		set_list_filter_fields()
	
		var form_name = "filter_form";	
		var dataString = "";
		$("#"+form_name+" select").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#"+form_name+" input").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		$("#"+form_name+" textarea").each(function() {
			//alert(this.id);
			dataString = dataString+"&"+this.id+"="+this.value;
		});
		
		//alert("load_list");
		
		//alert(dataString.substring(1));
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#summary_stats_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/settlements/get_summary_stats")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					$("#filter_loading_icon").hide();
					$("#refresh_list").show();
					
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
	
	//POPULATE HIDDEN FIELDS FOR EVENT FILTER CHECK BOXES
	function set_list_filter_fields()
	{
		//GET IN KICK IN?
		if($('#pending_kick_in_cb').attr('checked'))
		{
			$("#get_pending_kick_in").val(true);
		}
		else
		{
			$("#get_pending_kick_in").val(false);
		}
		
		//GET PENDING APPROVAL?
		if($('#pending_approval_cb').attr('checked'))
		{
			$("#get_pending_approval").val(true);
		}
		else
		{
			$("#get_pending_approval").val(false);
		}
		
		//GET PENDING SETTLEMENT?
		if($('#pending_settlement_cb').attr('checked'))
		{
			$("#get_pending_settlement").val(true);
		}
		else
		{
			$("#get_pending_settlement").val(false);
		}
		
		//GET CLOSED?
		if($('#closed_cb').attr('checked'))
		{
			$("#get_closed").val(true);
		}
		else
		{
			$("#get_closed").val(false);
		}
	}
	
	//UNCHECK ALL EVENTS
	function clear_events()
	{
		$("#pending_kick_in_cb").attr("checked",false);
		$("#pending_approval_cb").attr("checked",false);
		$("#pending_settlement_cb").attr("checked",false);
		$("#closed_cb").attr("checked",false);
		
		load_list();
	}
	
	//CHECK ALL EVENTS
	function select_all_events()
	{
		$("#pending_kick_in_cb").attr("checked",true);
		$("#pending_approval_cb").attr("checked",true);
		$("#pending_settlement_cb").attr("checked",true);
		$("#closed_cb").attr("checked",true);
		
		load_list();
	}

	//EVENT ICON CLICKED
	function status_icon_clicked(settlement_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#settlement_details_'+settlement_id);
		//alert("hello");
		if(this_div.is(":visible"))
		{
			this_div.hide();
			this_div.html('<img id="loading_icon" name="loading_icon" height="20px" src="/images/loading.gif" style="margin-left:450px; margin-top:5px;" />');
		}
		else
		{
			open_row_details(settlement_id);
		}
	}
	
	//OPEN ROW DETAILS
	function open_row_details(settlement_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#settlement_details_'+settlement_id);
	
		this_div.show();
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&settlement_id="+settlement_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/settlements/open_settlement_details")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					refresh_row(settlement_id);
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

	//REFRESH SINGLE EVENT ROW
	function refresh_row(settlement_id)
	{
	
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var dataString = "&settlement_id="+settlement_id;
		var this_div = $('#row_'+settlement_id);
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/settlements/refresh_row")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					this_div.html(response);
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
	
	//SAVE SETTLEMENT
	function save_settlement(settlement_id)
	{
		$('#save_icon_'+settlement_id).attr('src','/images/loading.gif');
		
		var isValid = true;
		
		//VALIDATE THAT KICK IN IS A NUMBER
		var target_pay = $("#target_pay_"+settlement_id).val();
		
		if(isNaN(target_pay))
		{
			isValid = false;
			alert('Target Pay must be a number!');
		}
		
		
		if(isValid)
		{
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#settlement_details_'+settlement_id);
		
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#settlement_details_form_"+settlement_id).serialize();
			//alert(dataString);
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/settlements/save_settlement_edit")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						open_row_details(settlement_id)
						refresh_row(settlement_id);
						load_summary_stats();
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
	
	function unlock_settlement(settlement_id)
	{
		//LOADING GIF
		$('#unlock_icon_'+settlement_id).attr('src','/images/loading.gif');
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#settlement_details_'+settlement_id);
	
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "";
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/settlements/unlock_settlement")?>/"+settlement_id, // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					open_row_details(settlement_id)
					refresh_row(settlement_id);
					//load_summary_stats();
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
	
	//APPROVE SETTLEMENT
	function approve_settlement(settlement_id)
	{
		//CHANGE ICON TO LOADING GIF
		$("#approve_icon").attr('src','/images/loading.gif');
	
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var dataString = "&settlement_id="+settlement_id;
		var this_div = $('#row_'+settlement_id);
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/settlements/approve_settlement")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					open_row_details(settlement_id);
					refresh_row(settlement_id);
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
	
	//SETTLE SETTLEMENT ICON CLICKEDF
	function settle_settlement(settlement_id)
	{
		if(confirm("Are you sure you want to settle? All changes will be FINAL!"))
		{
		
			//CHANGE ICON TO LOADING GIF
			$("#settle_icon_"+settlement_id).attr('src','/images/loading.gif');
			$("#settle_icon_"+settlement_id).css({'height':'15px',"width":"15px","top":"0px","right":"0px"});
		
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var dataString = "&settlement_id="+settlement_id;
			var this_div = $('#row_'+settlement_id);
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/settlements/settle_settlement")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						open_row_details(settlement_id);
						refresh_row(settlement_id);
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
	
	//ADD STATEMENT CREDIT
	function add_statement_credit(settlement_id)
	{
		//CHANGE ICON TO LOADING GIF
		$("#refresh_icon").attr('src','/images/loading.gif');
		
		//VALIDATE INPUTS
		var isValid = true;
		
		//VALIDATE DESCRIPTION
		if($("#invoiced_company_dd_"+settlement_id).val() == "Select")
		{
			isValid = false;
			alert('Business User for credit must be entered!');
		}
		
		//VALIDATE DESCRIPTION
		if(!$("#credit_description_"+settlement_id).val())
		{
			isValid = false;
			alert('Credit Description must be entered!');
		}
		
		//VALIDATE AMOUNT
		if($("#credit_amount_"+settlement_id).val())
		{
			if(isNaN($("#credit_amount_"+settlement_id).val()))
			{
				isValid = false;
				alert('Target Pay must be a number!');
			}
		}
		else
		{
			isValid = false;
			alert('Credit Amount must be entered!');
		}
		
		
		if(isValid)
		{
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var dataString = $("#add_credit_form_"+settlement_id).serialize();
			var this_div = $('#row_'+settlement_id);
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/settlements/add_statement_credit")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						open_row_details(settlement_id);
						refresh_row(settlement_id);
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