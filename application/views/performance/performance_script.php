<script>
	$(document).ready(function()
	{
		//load_list();
		
		$("#main_content").height($(window).height() - 115);
		$("#filter_list").height($(window).height() - 237);
		//$("#scrollable_content").height($(window).height() - 155);
		
		$('#start_date_filter').datepicker({ showAnim: 'blind' });
		$('#end_date_filter').datepicker({ showAnim: 'blind' });
		
	
	});
	
	ajax_pool = [];
	function abort_ajax_requests()
	{
		ajax_pool.forEach(function(request)
		{
			request.abort();
		});
	}
	
	//LOAD LOG LIST
	var load_list_ajax_call;
	function load_list()
	{
		abort_ajax_requests();
		
		$("#message_response_div").hide();
		$("#refresh_list").hide();
		$("#filter_loading_icon").show();
		
		//load_summary_stats();
	
		var dataString = $("#filter_form").serialize();
		
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
			url: "<?= base_url("index.php/performance/load_list")?>", // in the quotation marks
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
					//load_summary_stats();
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
	var summary_stats_ajax_call;
	function load_summary_stats()
	{
		$("#refresh_list").hide();
		$("#filter_loading_icon").show();
		
		var form_name = "filter_form";	
		var dataString = $("#filter_form").serialize();
		
		//alert("load_list");
		
		//alert(dataString.substring(1));
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#summary_stats_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		if(!(summary_stats_ajax_call===undefined))
		{
			//alert('abort');
			summary_stats_ajax_call.abort();
		}
		summary_stats_ajax_call = $.ajax({
			url: "<?= base_url("index.php/performance/get_summary_stats")?>", // in the quotation marks
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
					//alert('hey');
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
		ajax_pool.push(summary_stats_ajax_call);
	}
	
	//EVENT ICON CLICKED
	function status_icon_clicked(row_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#details_'+row_id);
		//alert("hello");
		if(this_div.is(":visible"))
		{
			this_div.hide();
			this_div.html('<img id="loading_icon" name="loading_icon" height="20px" src="/images/loading.gif" style="margin-left:450px; margin-top:5px;" />');
		}
		else
		{
			open_row_details(row_id);
		}
	}
	
	//OPEN ROW DETAILS
	function open_row_details(row_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#details_'+row_id);
	
		this_div.show();
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&performance_id="+row_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/performance/open_details")?>", // in the quotation marks
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

	//REFRESH SINGLE EVENT ROW
	function refresh_row(row_id)
	{
	
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var dataString = "&performance_id="+row_id;
		var this_div = $('#row_'+row_id);
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		var ajax_request = $.ajax({
			url: "<?= base_url("index.php/performance/refresh_row")?>", // in the quotation marks
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
		ajax_pool.push(ajax_request);
	}

	//OPEN EDIT MODE
	function edit_performance_review(pr_id)
	{
		$('.edit_'+pr_id).css({"display":"block"});
		$('.details_'+pr_id).css({"display":"none"});
	}
	
	//VALIDATE AND SAVE PERFORMANCE DETAILS
	function save_performance_details(pr_id)
	{
		var isValid = true;
		
		//VALIDATE
		if($("#solo_or_team_"+pr_id).val() == "Select")
		{
			isValid = false;
			alert('You must select Solo or Team!');
		}
		
		if($("#pr_fm_"+pr_id).val() == "Select")
		{
			isValid = false;
			alert('You must select Fleet Manager!');
		}
		
		if($("#pr_dm_"+pr_id).val() == "Select")
		{
			isValid = false;
			alert('You must select a Driver Manager!');
		}
		
		
		if(isValid)
		{
			$("#save_icon_"+pr_id).attr("src","/images/loading.gif");
			
			// GET THE DIV IN DIALOG BOX
			var dataString = $("#pr_details_form_"+pr_id).serialize();
			var this_div = "";
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/performance/save_pr_details")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						//alert(response);
						//this_div.html(response);
						open_row_details(pr_id)
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