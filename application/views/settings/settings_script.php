<script>
	$(function(){
		$("#main_content").height($(window).height() - 114);

	})
	function add_permission(user_id,permission_id)
	{
		$.ajax({
			url: "<?= base_url("index.php/settings/add_permission")?>", // in the quotation marks
			type: "POST",
			data: {user_id:user_id,permission_id:permission_id},
			cache: false,
			statusCode: {
				200: function(){
					// Success!
					load_user_report();
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
	
	function add_user(user_id,permission_id)
	{
		
		$.ajax({
			url: "<?= base_url("index.php/settings/add_user")?>", // in the quotation marks
			type: "POST",
			data: {user_id:user_id,permission_id:permission_id},
			cache: false,
			statusCode: {
				200: function(){
					// Success!
					load_permission_report();
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
	
	function delete_permission(user_id,permission_id)
	{
		console.log("deleting...");
		
		$.ajax({
			url: "<?= base_url("index.php/settings/delete_permission")?>", // in the quotation marks
			type: "POST",
			data: {user_id:user_id,permission_id:permission_id},
			cache: false,
			statusCode: {
				200: function(){
					// Success!
					load_user_report();
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
	
	function delete_user(user_id,permission_id)
	{
		console.log("deleting...");
		
		$.ajax({
			url: "<?= base_url("index.php/settings/delete_user")?>", // in the quotation marks
			type: "POST",
			data: {user_id:user_id,permission_id:permission_id},
			cache: false,
			statusCode: {
				200: function(){
					// Success!
					load_permission_report();
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
	
	var report_ajax_call;
	
	function load_permission_report()
	{
		$("#user_input").val("Select");
		
		//SHOW LOADING ICON
		$("#refresh_settings").hide();
		$("#filter_loading_icon").show();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		var permission_id = $("#permission_input").val();
		var dataString = 'permission_id='+permission_id;
		
		// AJAX!
		if(!(report_ajax_call===undefined))
		{
			//alert('abort');
			report_ajax_call.abort();
		}
		report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/settings/load_permission_report")?>", // in the quotation marks
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
	
	function load_user_report()
	{
		$("#permission_input").val("Select");
		
		//SHOW LOADING ICON
		$("#refresh_settings").hide();
		$("#filter_loading_icon").show();
		
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		var user_id = $("#user_input").val();
		var dataString = 'user_id='+user_id;
		
		// AJAX!
		if(!(report_ajax_call===undefined))
		{
			//alert('abort');
			report_ajax_call.abort();
		}
		report_ajax_call = $.ajax({
			url: "<?= base_url("index.php/settings/load_user_report")?>", // in the quotation marks
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
	
</script>