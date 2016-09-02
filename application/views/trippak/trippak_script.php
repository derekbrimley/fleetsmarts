<script>
	$(function(){
		load_trippak_report();
		$('.datepicker').datepicker({ showAnim: 'blind' });
		
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
	});
	
	var load_trippak_ajax_call;
	
	function load_trippak_report()
	{
		
		$("#refresh_list").hide();
		$("#filter_loading_icon").show();
		
		var dataString = $("#filter_form").serialize();
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var main_content = $('#main_content');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		if(!(load_trippak_ajax_call===undefined))
		{
			load_trippak_ajax_call.abort();
		}
		load_trippak_ajax_call = $.ajax({
			url: "<?= base_url("index.php/trippak/load_trippak_report")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
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
		$("#refresh_trippak_details_icon_"+row_id).attr('src','/images/loading.gif');
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#details_'+row_id);
			
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&trippak_id="+row_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/trippak/open_details")?>", // in the quotation marks
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
		var dataString = "&trippak_id="+row_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/trippak/refresh_row")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
//					change_view();
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
	function save_trippak_edit(row_id)
	{
		var isValid = true;
		
		if($("#edit_load_number_"+row_id).val())
		{
			validate_load(row_id);
			console.log("load number: " + $("#edit_load_number_"+row_id).val());
		}
		if($("#edit_odometer_"+row_id).val())
		{
			
			if(isNaN($("#edit_odometer_"+row_id).val()))
			{
				isValid = false;
				console.log(isValid);
				alert("Odometer must be a number!");
			}
		}
		if($("#edit_lumper_amount_"+row_id).val())
		{
			console.log($("#edit_odometer_"+row_id).val());
			if(isNaN($("#edit_lumper_amount_"+row_id).val()))
			{
				isValid = false;
				alert("Lumper Amount must be a number!");
			}
		}
		if(isValid)
		{
			$("#save_icon_"+row_id).attr('src','/images/loading.gif');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#trippak_details_form_"+row_id).serialize();
			console.log(dataString);
			var this_div = $('#details_'+row_id);
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/trippak/save_trippak_edit")?>", // in the quotation marks
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
	function validate_load(row_id)
	{
		
		if($("#edit_load_number_"+row_id).val())
		{
			console.log($("#edit_load_number_"+row_id).val());
			var load_number = $("#edit_load_number_"+row_id).val()
			var dataString = "&load_number="+load_number;
			
			var this_div = $('#details_'+row_id);
			
			$.ajax({
				url: "<?= base_url("index.php/trippak/validate_load")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						if(response=='False')
						{
							$("#edit_load_number_"+row_id).val('');
							alert("Load does not exist!");
						}
						
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
			});
		}
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
			url: "<?= base_url("index.php/trippak/load_file_upload_dialog")?>", // in the quotation marks
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
			url: '<?= base_url('index.php/trippak/upload_trippak_attachment')?>',
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
</script>