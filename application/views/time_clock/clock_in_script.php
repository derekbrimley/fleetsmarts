<script>

	var submit_form = function(){
		
		var form = $("#upload_file_form")[0];
		var formData = new FormData(form);
		console.log(formData);
		$.ajax({
			url: "<?= base_url("index.php/time_clock/verify_clock_in")?>",
			type: "POST",
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			statusCode: {
				200: function(response){
					// Success!
					$("#form_container").hide();
					$("#response_container").html(response);
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
	
//	$.ajax({
//			url: "<?= base_url("index.php/purchase_orders/create_new_po")?>", // in the quotation marks
//			type: "POST",
//			data: dataString,
//			cache: false,
//			context: this_div, // use a jquery object to select the result div in the view
//			statusCode: {
//				200: function(response){
//					// Success!
//					
//					//SHOW LOADING ICON
//					//$("#filter_loading_icon").hide();
//					//$("#refresh_logs").show();
//					load_po_view(response);
//					//this_div.html(response);
//					//alert(response);
//					
//				},
//				404: function(){
//					// Page not found
//					alert('page not found');
//				},
//				500: function(response){
//					// Internal server error
//					alert("500 error! "+response);
//				}
//			}
//		});//END AJAX
	
</script>