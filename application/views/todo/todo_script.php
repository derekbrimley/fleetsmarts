<script>
	$(document).ready(function()
	{
		$("#main_content").height($(window).height() - 115);
		$("#scrollable_filter_div").height($(window).height() - 278);
		
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
					$("#tr_"+selected_row).addClass("blue_border");
				},//end open function
			close: function() 
				{
					//REMOVE ANY BLUE BORDER BOXES
					$("#tr_"+selected_row).removeClass('blue_border');
				}
		});//end add notes dialog
		
	});
	
	var selected_row = 0;
	
	function create_new_todo()
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
	
	function load_filter()
	{
		//alert('load po');
		
		//LOAD FILTER DIV
		var dataString = "";
		
		var this_div = $('#filter_div');
		
		$.ajax({
			url: "<?= base_url("index.php/todo/load_filter")?>", // in the quotation marks
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
					
					load_report();
					
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
	
	var load_report_ajax_call;
	function load_report()
	{

		//SHOW LOADING ICON
		$("#save_icon").hide();
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		//alert('load po');
		//LOAD PO FILTER DIV
		var dataString = $("#filter_form").serialize();
		
		var this_div = $('#main_content');
		
		// AJAX!
		if(!(load_report_ajax_call===undefined))
		{
			//alert('abort');
			load_report_ajax_call.abort();
		}
		load_report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/todo/load_report")?>", // in the quotation marks
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
	
	//AJAX FOR GETTING NOTES
	function open_notes(row_id)
	{
		selected_row = row_id;
		
		//RESET LOADING GIF
		$("#notes_ajax_div").html("");
		
		//SET NOTES_ID TO VALUE OF LOAD ID
		//$("#notes_id").val(truck_id);
		
		$("#row_id").val(row_id); //this is the hidden field in the add notes form
		
		//OPEN THE DIALOG BOX
		$( "#add_notes").dialog( "open" );
		//$("#tr_"+row_id).addClass("blue_border");
		
		//alert('inside ajax');
				
		// GET THE DIV IN DIALOG BOX
		var notes_ajax_div = $('#notes_ajax_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/todo/get_notes/")?>"+"/"+row_id, // in the quotation marks
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
			url: "<?= base_url("index.php/todo/save_note")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: notes_ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					notes_ajax_div.html(response);
					$("#action_item_notes_"+expense_id).attr("title",response.replace(/<br>/gi,"\n"));
					$("#action_item_notes_"+expense_id).attr("src","/images/add_notes.png");
					
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
</script>