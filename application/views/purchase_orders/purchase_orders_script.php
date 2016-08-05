<script>
	$(document).ready(function()
	{
		$("#main_content").height($(window).height() - 115);
		$("#scrollable_filter_div").height($(window).height() - 278);
		
		load_po_filter();
		
		//DIALOG: BILLING CHECKLIST --- NOT USING ANYMORE
		$("#lumper_dialog" ).dialog(
		{
				autoOpen: false,
				height: 230,
				width: 300,
				modal: true,
				buttons: 
					{
						"Save": function() 
							{
								validate_lumper_load_number();//function found in po_view_div.php
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
		
		//DIALOG: BILLING CHECKLIST --- NOT USING ANYMORE
		$("#pa_dialog" ).dialog(
		{
				autoOpen: false,
				height: 230,
				width: 300,
				modal: true,
				buttons: 
					{
						"Save": function() 
							{
								validate_pa_dialog();//function found in po_view_div.php
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
		
	});
	
	function create_new_po()
	{
		//SHOW LOADING ICON
		$("#save_icon").hide();
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		//alert('load po');
		//LOAD PO FILTER DIV
		var dataString = "";
		
		var this_div = $('#main_content');
		
		$.ajax({
			url: "<?= base_url("index.php/purchase_orders/create_new_po")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					
					//SHOW LOADING ICON
					//$("#filter_loading_icon").hide();
					//$("#refresh_logs").show();
					load_po_view(response);
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
	
	function load_po_filter()
	{
		//CHANGE THE BOLD
		$("#transactions_filter_link").css({'font-weight' : ''});
		$("#po_filter_link").css({'font-weight' : 'bold'});

		//alert('load po');
		//LOAD PO FILTER DIV
		var dataString = "";
		
		var this_div = $('#filter_div');
		
		$.ajax({
			url: "<?= base_url("index.php/purchase_orders/load_po_filter")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					
					//SHOW LOADING ICON
					//$("#filter_loading_icon").hide();
					//$("#refresh_logs").show();
					
					this_div.html(response);
					//alert(response);
					
					load_po_report();
					
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
	
	var load_po_report_ajax_call;
	function load_po_report()
	{
		//CHANGE THE BOLD
		$("#transactions_filter_link").css({'font-weight' : ''});
		$("#po_filter_link").css({'font-weight' : 'bold'});

		//SHOW LOADING ICON
		$("#save_icon").hide();
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		//alert('load po');
		//LOAD PO FILTER DIV
		var dataString = $("#filter_form").serialize();
		
		var this_div = $('#main_content');
		
		// AJAX!
		if(!(load_po_report_ajax_call===undefined))
		{
			//alert('abort');
			load_po_report_ajax_call.abort();
		}
		load_po_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/purchase_orders/load_po_report")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					
					//SHOW LOADING ICON
					//$("#filter_loading_icon").hide();
					//$("#refresh_logs").show();
					
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
	
	function load_po_view(id)
	{
		if(!(load_po_report_ajax_call===undefined))
		{
			//alert('abort');
			load_po_report_ajax_call.abort();
		}
		
		//SHOW LOADING ICON
		$("#save_icon").hide();
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		//alert('load po');
		//LOAD PO FILTER DIV
		var dataString = "&po_id="+id;
		
		var this_div = $('#main_content');
		
		$.ajax({
			url: "<?= base_url("index.php/purchase_orders/load_po_view")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					
					//SHOW LOADING ICON
					//$("#filter_loading_icon").hide();
					//$("#refresh_logs").show();
					
					this_div.html(response);
					fill_in_locations("po_location",$("#po_gps").val());
					if($("#po_gps").val())
					{
						$("#po_gps").hide()
						$("#po_location").show()
					}
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
	
	//APPROVE PO FROM LIST VIEW
	function approve_po_from_list(po_id)
	{
				
		// GET THE DIV IN DIALOG BOX
		var ajax_div = $('#tr_'+po_id);
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/purchase_orders/approve_po_from_list/")?>"+"/"+po_id, // in the quotation marks
			type: "POST",
			cache: false,
			context: ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					ajax_div.html(response);
					
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
</script>