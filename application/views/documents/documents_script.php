<script>
	$(document).ready(function()
	{
		$("#main_content").height($(window).height() - 115);
		$("#filter_list").height($(window).height() - 213);
		$("#scrollable_left_bar").height($("#main_content").height() - 70);
		$(".dp").datepicker();
		
		//ADD NEW ENTRY DIALOG
		$("#new_doc_dialog").dialog(
		{
			autoOpen: false,
			height: 360,
			width: 600,
			modal: true,
			buttons: 
				[
					{
						text: "Submit",
						click: function() 
						{
							
							validate_upload();
							
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
				},//end open function
			close: function() 
				{
					//reset_all_account_entry_inputs();
				}
		});//end add new entry dialog
		
		load_esign_docs_report();
	});
	
	function load_documents_enter(e)
	{
		if(e.keyCode==13)
		{
			load_esign_docs_report();
		}
	}
	
	function load_documents()
	{
		
	}
	
	function validate_upload()
	{
		doc_type = $("#doc_type_dropdown").val();
		recipient = $("#recipient_dropdown").val();
		doc_title = $("#doc_title").val();
		unsigned_doc = $("#unsigned_doc").val();
		signed_doc = $("#signed_doc").val();
		
		isValid = true;
		if(recipient == "Select")
		{
			alert("You must select a Recipient Signer!")
			isValid = false;
		}
		
		if(doc_type == "Select")
		{
			alert("You must select a Document Type!")
			isValid = false;
		}
		
		if(!doc_title)
		{
			alert("You must enter a Document Title!")
			isValid = false;
		}
		
		/**
		if(!signed_doc)
		{
			alert("You must choose a Signed Doc!")
			isValid = false;
		}
		**/
		if(isValid)
		{
			$("#add_new_doc_form").submit();
		}
	}
	
	var report_ajax_call;
	function load_esign_docs_report()
	{
		//SHOW LOADING ICON
		$("#refresh_logs").hide();
		$("#filter_loading_icon").show();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = $("#filter_form").serialize();
		// var dataString;
		console.log(dataString);
		
		
		// AJAX!
		if(!(report_ajax_call===undefined))
		{
			//alert('abort');
			report_ajax_call.abort();
		}
		report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/documents/load_esign_docs_report")?>", // in the quotation marks
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
	
	function download_file(guid)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		//var dataString = $("#filter_form").serialize();
		var dataString = "guid="+guid;
		
		
		// AJAX!
		if(!(report_ajax_call===undefined))
		{
			//alert('abort');
			report_ajax_call.abort();
		}
		report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/documents/download_file")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//this_div.html(response);
					
					alert(response);
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