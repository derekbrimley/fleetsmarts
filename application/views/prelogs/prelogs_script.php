<script>
	$(document).ready(function()
	{
		$("#main_content").height($(window).height() - 115);
		$("#filter_list").height($(window).height() - 213);
		
		load_log_list();
	});
	
	var load_log_list_ajax_call;
	function load_log_list()
	{
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		//alert("load_log_list");
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		// AJAX!
		if(!(load_log_list_ajax_call===undefined))
		{
			//alert('abort');
			load_log_list_ajax_call.abort();
		}
		load_log_list_ajax_call = $.ajax({
			url: "<?= base_url("index.php/prelogs/load_event_list")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#filter_loading_icon").hide();
					$("#refresh_logs").show();
					//alert('loaded');
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
	
	function gp_row_mouseover(gp_id)
	{
		$("#order_up_arrow_"+gp_id).show();
		$("#order_down_arrow_"+gp_id).show();
		$("#edit_gp_icon_span_"+gp_id).show();
	}
	
	function gp_row_mouseout(gp_id)
	{
		$("#order_up_arrow_"+gp_id).hide();
		$("#order_down_arrow_"+gp_id).hide();
		$("#edit_gp_icon_span_"+gp_id).hide();
	}
	
	function order_gp(gp_id,direction)
	{
		//alert('order_gp');
		$('.gp_exp_details_'+gp_id).css({"display":"none"});
		$('.gp_exp_loading_'+gp_id).css({"display":"block"});
		
		var dataString = "&gp_id="+gp_id+"&direction="+direction;
		var this_div = null;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/prelogs/change_gp_order")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, 
			statusCode: {
				200: function(response){
					// Success!
					//alert(response);
					//this_div.html(response);
					load_log_list();
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
		fill_in_locations("gp_location_text_"+log_entry_id,$("#temp_gp_gps_"+log_entry_id).val());
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
	
	function save_goalpoint(gp_id)
	{
		var isValid = true;
		
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
		
		if($("#gp_location_"+gp_id).html() == '')
		{
			isValid = false;
			alert("There must be a location associated with the GPS coordinates!");
		}
		else
		{
			$("#gp_location_hidden_"+gp_id).val($("#gp_location_"+gp_id).html());
		}
		
		if(isValid)
		{
			$("#save_gp_icon_"+gp_id).hide();
			$("#loading_icon_save_gp_"+gp_id).show();
			
			
			var dataString = $("#edit_gp_form_"+gp_id).serialize();
			var this_div = $("#goalpoints_div_"+gp_id);
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/prelogs/save_goalpoint")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						alert(response);
						//this_div.html(response);
						load_log_list()
						
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
	
	function delete_goalpoint(gp_id,log_entry_id)
	{
		if(confirm("Are you sure you want to delete this Goalpoint??"))
		{
			$("#edit_gp_id_"+log_entry_id).val(gp_id);
			$("#edit_expected_time_"+log_entry_id).val($("#edit_expected_time_"+gp_id).val());
			$("#edit_gp_type_"+log_entry_id).val($("#edit_gp_type_"+gp_id).val());
			$("#edit_gp_gps_"+log_entry_id).val($("#edit_gp_gps_"+gp_id).val());
			$("#edit_gp_location_"+log_entry_id).val($("#gp_location_"+gp_id).html());
			$("#edit_gp_notes_"+log_entry_id).val($("#edit_gp_notes_"+gp_id).val());
			
			var dataString = $("#edit_gp_form_"+log_entry_id).serialize();
			var this_div = $("#goalpoints_div_"+log_entry_id);
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/logs/delete_goalpoint")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, 
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						//this_div.html(response);
						load_goalpoints_div(log_entry_id)
						
						$("#edit_gp_id_"+log_entry_id).val("");
						$("#edit_expected_time_"+log_entry_id).val("");
						$("#edit_gp_type_"+log_entry_id).val("");
						$("#edit_gp_gps_"+log_entry_id).val("");
						$("#edit_gp_location_"+log_entry_id).val("");
						$("#edit_gp_notes_"+log_entry_id).val("");
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