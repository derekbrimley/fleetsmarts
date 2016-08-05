<script>
	function create_fuel_estimate(id)
	{
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		//var this_div = $('#main_content');
		var this_div = $('#script_div_'+id);
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = "&event_id="+id;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/create_fuel_estimate")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					this_div.html(response);
					//load_log_list();
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
<div id="script_div_<?=$log_entry_id?>">
	<!-- AJAX SCRIPT GOES HERE !-->
</div>
<div style="height:175px; font-size:12px;">
	<div style="height: 45px; width:20px; float:right;">
		<img id="new_checkpoint" style="display:block; margin-bottom:12px;margin-right:15px; cursor:pointer; height:16px; position:relative; left:2px;" src="/images/new_checkpoint.png" title="New Checkpoint" onclick="this.src='/images/loading.gif'; create_new_checkpoint('<?=$log_entry["id"]?>');"/>
		<img title="Create Shift Report" style='display:block; margin-bottom:12px;  margin-right:15px;cursor:pointer; height:15px; position:relative; bottom:1px; right:0px;' src="/images/make_shift_report.png" onclick="this.src='/images/loading.gif'; create_new_event_from_geopoint('<?=$log_entry_id?>','Shift Report')"/>
		<img title="Create Fuel Estimate" style='display:block; margin-bottom:12px;  margin-right:15px;cursor:pointer; height:15px; position:relative; bottom:1px; left:0px;' src="/images/make_fuel_estimate.png" onclick="this.src='/images/loading.gif'; create_fuel_estimate('<?=$log_entry_id?>')"/>
		<img title="Create Driver In" style='display:block; margin-bottom:12px;  margin-right:15px;cursor:pointer; height:15px; position:relative; bottom:1px; left:3px;' src="/images/create_driver_in.png" onclick="this.src='/images/loading.gif'; create_new_event_from_geopoint('<?=$log_entry_id?>','Driver In')"/>
		<img title="Create Driver Out" style='display:block; margin-bottom:12px;  margin-right:15px;cursor:pointer; height:15px; position:relative; bottom:1px; left:3px;' src="/images/create_driver_out.png" onclick="this.src='/images/loading.gif'; create_new_event_from_geopoint('<?=$log_entry_id?>','Driver Out')"/>
		<img title="Create Pick Trailer" style='display:block; margin-bottom:12px;  margin-right:15px;cursor:pointer; height:15px; position:relative; bottom:1px; right:5px;' src="/images/create_pick_trailer.png" onclick="this.src='/images/loading.gif'; create_new_event_from_geopoint('<?=$log_entry_id?>','Pick Trailer')"/>
		<img title="Create Drop Trailer" style='display:block; margin-bottom:12px;  margin-right:15px;cursor:pointer; height:15px; position:relative; bottom:1px; right:5px;' src="/images/create_drop_trailer.png" onclick="this.src='/images/loading.gif'; create_new_event_from_geopoint('<?=$log_entry_id?>','Drop Trailer')"/>
	</div>
	<div style="margin-left:72px; width:50px; float:left;">Notes:</div><div style="width:700px; margin-left:130px;"><?=$log_entry["entry_notes"]?></div>
</div>