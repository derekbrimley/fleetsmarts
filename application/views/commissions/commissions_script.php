<script>
	$(document).ready(function()
	{
		load_list();
		
		$("#main_content").height($(window).height() - 115);
		$("#filter_list").height($(window).height() - 237);
		//$("#scrollable_content").height($(window).height() - 155);
		
		$('#start_date_filter').datepicker({ showAnim: 'blind' });
		$('#end_date_filter').datepicker({ showAnim: 'blind' });
		
		
		
	
	});
	
	//LOAD LOG LIST
	var load_list_ajax_call;
	function load_list()
	{
		$("#booking_stats_box_1").hide();
		$("#booking_stats_box_2").hide();
		$("#booking_stats_box_3").hide();
		$("#booking_stats_box_4").hide();
		
		$("#filter_loading_icon").show();
		
		set_list_filter_fields();
	
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
			url: "<?= base_url("index.php/commissions/load_list")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: main_content, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					main_content.html(response);
					$("#filter_loading_icon").hide();
					
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
		//GET IN TRANSIT?
		if($('#in_transit_cb').attr('checked'))
		{
			$("#get_in_transit").val(true);
		}
		else
		{
			$("#get_in_transit").val(false);
		}
		
		//GET PENDING FUNDING?
		if($('#pending_funding_cb').attr('checked'))
		{
			$("#get_pending_funding").val(true);
		}
		else
		{
			$("#get_pending_funding").val(false);
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
		
		//GET FUEL CLOSED?
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
		$("#in_transit_cb").attr("checked",false);
		$("#pending_funding_cb").attr("checked",false);
		$("#pending_settlement_cb").attr("checked",false);
		$("#closed_cb").attr("checked",false);
		
		load_list();
	}
	
	//CHECK ALL EVENTS
	function select_all_events()
	{
		$("#in_transit_cb").attr("checked",true);
		$("#pending_funding_cb").attr("checked",true);
		$("#pending_settlement_cb").attr("checked",true);
		$("#closed_cb").attr("checked",true);
		
		load_list();
	}
	
	//EVENT ICON CLICKED
	function event_icon_clicked(load_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#commission_details_'+load_id);
		//alert("hello");
		if(this_div.is(":visible"))
		{
			this_div.hide();
			this_div.html('<img id="loading_icon" name="loading_icon" height="20px" src="/images/loading.gif" style="margin-left:450px; margin-top:5px;" />');
		}
		else
		{
			open_commission_details(load_id);
		}
	}
	
	//OPEN EVENT DETAILS
	function open_commission_details(load_id)
	{
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#commission_details_'+load_id);
	
		this_div.show();
		//this_div.html('<img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="margin-left:460px; margin-top:10px;" />');
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&load_id="+load_id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/commissions/open_commission_details")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					refresh_row(load_id);
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
	function refresh_row(load_id)
	{
	
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var dataString = "&load_id="+load_id;
		var this_div = $('#row_'+load_id);
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/commissions/refresh_row")?>", // in the quotation marks
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
	
	//APPROVE COMMISSION
	function approve_commission(load_id)
	{
		if(confirm("Are you sure you want to approve this settlement?"))
		{
			// GET THE DIV IN DIALOG BOX
			var dataString = "&load_id="+load_id;
			var this_div = $('#row_'+load_id);
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/commissions/approve_commission")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: this_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						this_div.html(response);
						open_commission_details(load_id);
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
	}
	
</script>