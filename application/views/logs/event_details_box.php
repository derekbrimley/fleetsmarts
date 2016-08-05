<script>
	function create_new_leg(id)
	{
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#main_content');
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&event_id="+id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/create_new_leg")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					//this_div.html(response);
					load_log_list();
					//alert(response);
				},
				404: function(){
					// Page not found
					alert('page not found');
				},
				500: function(response){
					// Internal server error
					alert("500 error!")
					this_div.html(response);
				}
			}
		});//END AJAX
	}
</script>

<div style="font-size:14px; height:30px; padding:10px; margin-left:5px; margin-right:5px; background:#EFEFEF;">
	<img title="New Leg" style='cursor:pointer; height:15px; float:right; position:relative; bottom:1px; left:1px; margin-left:5px; margin-right:5px;' src="/images/log_end_leg.png" onclick="create_new_leg('<?=$log_entry_id?>')"/>
	<span style="float:right;">New Leg button ------> </span>
</div>