<script>
	$(document).ready(function()
	{
		
	});
	
	//LOAD GOALPOINTS DIV
	function load_goalpoints_div(row_id)
	{
		//alert('load_goalpoint_div');
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#goalpoints_div_'+row_id);
			
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:20px; margin-left:440px; margin-top:10px;" />');
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&load_id="+row_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/loads/load_goalpoints_div")?>", // in the quotation marks
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
	
	function add_new_goalpoint(row_id)
	{

		var isValid = true;
		
		if($("#temp_gp_type_"+row_id).val() == "Select")
		{
			isValid = false;
			alert('Goalpoint Type must be selected!');
		}
		
		if(!$("#temp_gp_location_name_"+row_id).val())
		{
			isValid = false;
			alert('Goalpoint Location Name must be entered!');
		}
		
		
		if(isValid)
		{
			$("#new_gp_location_"+row_id).val($("#gp_location_text_"+row_id).text());
			
			// GET THE DIV IN DIALOG BOX
			var this_div = $('#goalpoints_div_'+row_id);
				
			//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#new_goalpoint_form_"+row_id).serialize();
			clear_add_goalpoint_fields(row_id);
			//AJAX!
			$.ajax({
				url: "<?= base_url("index.php/loads/add_new_goalpoint")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						//this_div.html(response);
						load_goalpoints_div(row_id);
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
	
	//CHECKBOX CLIKED
	function has_deadline_changed(row_id)
	{
		if($("#has_deadline_cb_"+row_id).is(":checked"))
		{
			$("#temp_deadline_"+row_id).show();
		}
		else
		{
			$("#temp_deadline_"+row_id).hide();
		}
	}
	
	function gp_row_mouseover(gp_id)
	{
		$("#order_up_arrow_"+gp_id).show();
		$("#order_down_arrow_"+gp_id).show();
		//$("#edit_gp_icon_span_"+gp_id).show();
	}
	
	function gp_row_mouseout(gp_id)
	{
		$("#order_up_arrow_"+gp_id).hide();
		$("#order_down_arrow_"+gp_id).hide();
		//$("#edit_gp_icon_span_"+gp_id).hide();
	}
	
	function order_gp(row_id,gp_id,direction)
	{
		//alert('order_gp');
		$('.gp_exp_details_'+row_id).css({"display":"none"});
		$('.gp_exp_loading_'+row_id).css({"display":"block"});
		
		var dataString = "&gp_id="+gp_id+"&direction="+direction;
		var this_div = null;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/loads/change_gp_order")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, 
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					//this_div.html(response);
					load_goalpoints_div(row_id);
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
	
	function auto_fill_goalpoint_location(log_entry_id)//FOR ADD GP
	{
		$("#gp_location_text_"+log_entry_id).css('color','black');
		fill_in_locations("gp_location_text_"+log_entry_id,$("#temp_gp_gps_"+log_entry_id).val());
	}
	
	function clear_add_goalpoint_fields(row_id)
	{
		//alert('clearing fields');
		$("#temp_gp_type_"+row_id).val('Select');
		$("#temp_gp_gps_"+row_id).val('');
		$("#temp_gp_location_name_"+row_id).val('');
		$("#gp_location_text_"+row_id).css('color','#808080d6');
		$("#gp_location_text_"+row_id).html('City, State');
		$("#temp_gp_notes_"+row_id).val('');
		$("#temp_deadline_"+row_id).val('');
		$("#has_deadline_cb_"+row_id).attr('checked',false);
		has_deadline_changed(row_id);
	}
	
	function auto_fill_goalpoint_edit_location(gp_id)//FOR EDIT GP
	{
		//alert($("#edit_gp_gps_"+gp_id).val());
		fill_in_locations("gp_location_"+gp_id,$("#edit_gp_gps_"+gp_id).val());
	}
	
	//CHANGE VIEW TO EDIT GOALPOINT ROW
	var last_edit_gp_id;
	function edit_goalpoint(gp_id)
	{
		//CHANGE BACK CURRENT EDIT ROW
		if(last_edit_gp_id)
		{
			//alert(last_edit_gp_id);
			$(".gp_row_edit_"+last_edit_gp_id).hide();
			$(".gp_row_details_"+last_edit_gp_id).show();
		}
		
		//CHANGE NEWLY SELECTED ROW
		$(".gp_row_details_"+gp_id).hide();
		$(".gp_row_edit_"+gp_id).show();
		
		last_edit_gp_id = gp_id;
	}
	
	function cancel_edit_gp(gp_id)
	{
		//CHANGE NEWLY SELECTED ROW
		$(".gp_row_edit_"+gp_id).hide();
		$(".gp_row_details_"+gp_id).show();
	}
	
	function save_goalpoint(gp_id,row_id)
	{
		var isValid = true;
		
		if(!$("#edit_gp_duration_"+gp_id).val() || isNaN($("#edit_gp_duration_"+gp_id).val()))
		{
			isValid = false;
			alert("Expected Duration must be entered and must be a number!");
		}
		
		var gp_gps = $("#edit_gp_gps_"+gp_id).val();
		if(gp_gps)
		{
			var stripped_gp_gps = gp_gps.replace(/\./g,'').replace(/\,/g,'').replace(/\s+/g,'').replace(/-/g,'');
			//alert(stripped_address);
			if(isNaN(stripped_gp_gps))
			{
				isValid = false;
				alert("GPS must be in GPS format!");
			}
		}
		else
		{
			isValid = false;
			alert("You must enter in a GPS coordinate!");
		}
		
		if(!$("#edit_gp_location_name_"+gp_id).val())
		{
			isValid = false;
			alert("Location Name must be entered!");
		}
		
		if(isValid)
		{
			$("#save_gp_icon_"+gp_id).attr('src','/images/loading.gif');
			$("#save_gp_icon_"+gp_id).css('height','14px');
			
			var dataString = $("#edit_gp_form_"+gp_id).serialize();
			var this_div = $("#goalpoints_div_"+gp_id);
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/loads/save_goalpoint")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						//this_div.html(response);
						load_goalpoints_div(row_id);
						
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
	
	function delete_goalpoint(gp_id,row_id)
	{
		
		if(confirm("Are you sure you want to delete this goalpoint??"))
		{
			$("#save_gp_icon_"+gp_id).attr('src','/images/loading.gif');
			$("#save_gp_icon_"+gp_id).css('height','14px');
			
			var dataString = "&gp_id="+gp_id+"&row_id="+row_id;
			var this_div = $("#goalpoints_div_"+gp_id);
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/loads/delete_goalpoint")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						//alert('yo');
						//this_div.html(response);
						load_goalpoints_div(row_id);
						
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
	
	function open_mark_goalpoint_complete_dialog(gp_id,row_id)
	{
		//alert('inside ajax');
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#mark_goalpoint_complete_dialog');
		
		
		this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="height:25px; margin-left:425px; margin-top:250px;" />');
		this_div.dialog( "open" );
		
		selected_row = row_id;
		//alert(selected_row);
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var data = "&gp_id=" + gp_id; //use & to separate values
		
		// AJAX!
		$.ajax({
			url: "<?= base_url('index.php/loads/open_mark_goalpoint_complete_dialog')?>", // in the quotation marks
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
	
	function mark_goalpoint_complete()
	{
		//alert('hi');
		
		$("#codriver_id").val($("#temp_codriver_id").val());
		
		//VALIDATE FORM IS COMPLETE
		var isValid = true;
		
		if($("#codriver_id").val() == "Select")
		{
			isValid = false;
			alert('Codriver must be selected!');
		}
		
		
		if($("#geopoint_dropdown").val() == "Not Found")
		{
			if(!$("#gp_complete_date").val())
			{
				isValid = false;
				alert('Date must be selected!');
			}
			
			if($("#gp_complete_time").val())
			{
				if(!isTime($("#gp_complete_time").val()))
				{
					alert('Time must be entered in HH:MM format!');
					isValid = false;
				}
			}
			else
			{
				isValid = false;
				alert('Time must be entered!');
			}
			
			if($("#gp_complete_gps").val())
			{
				if($("#gps_isvalid").val() == "no")
				{
					isValid = false;
					alert('GPS coordinates are invalid!');
				}
			}
			else
			{
				isValid = false;
				alert('GPS coordinates must be entered!');
			}
			
			if(!$("#gp_complete_odometer").val() || isNaN($("#gp_complete_odometer").val()))
			{
				isValid = false;
				alert('Odometer must be entered as a number!');
			}
			
		}
		
		if($("#is_lumper").val() == "Yes")
		{
			if(!$("#gp_complete_lumper_amount").val() || isNaN($("#gp_complete_lumper_amount").val()))
			{
				isValid = false;
				alert('If there is a lumper on this load, the amount must be entered and it must be a number!!');
			}
		}
		
		if($("#load_isLate").val() == "yes")
		{
			if($("#why_late").val() == "Select")
			{
				isValid = false;
				alert('Why Late must be selected!');
			}
			
			if(!$("#late_explanation").val())
			{
				isValid = false;
				alert('Late Explanation must be selected!');
			}
		}
		
		if(isValid)
		{
			var dataString = $("#mark_goalpoint_complete_form").serialize();
			$('#mark_goalpoint_complete_dialog').dialog('close');
			var this_div = null;
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/loads/mark_goalpoint_complete")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						//alert(selected_row);
						//this_div.html(response);
						load_goalpoints_div(selected_row);
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