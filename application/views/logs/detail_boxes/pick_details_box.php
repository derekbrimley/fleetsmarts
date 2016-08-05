<script>
	function create_new_leg(id)
	{
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		//var this_div = $('#main_content');
		var this_div = $('#script_div_'+id);
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
<div style="height:100px; font-size:12px;">
	<?php if(empty($log_entry["locked_datetime"])): ?>
		<div style="width:20px; height:45px; float:right;">
			<img title="End Leg" style='display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; bottom:1px; left:1px;' src="/images/make_new_leg.png" onclick="create_new_leg('<?=$log_entry_id?>')"/>
			<img title="Estimate Odometer" style='display:block; margin-bottom:12px; cursor:pointer; height:13px; position:relative; bottom:0px; right:1px;' src="/images/odometer.png" onclick="estimate_odometer('<?=$log_entry_id?>','<?=$log_entry["sync_entry_id"]?>')"/>
			<img id="new_checkpoint" style="margin-bottom:13px; margin-right:15px; cursor:pointer; height:16px; position:relative; left:2px;" src="/images/new_checkpoint.png" title="New Checkpoint" onclick="this.src='/images/loading.gif'; create_new_checkpoint('<?=$log_entry["id"]?>');"/>
			<img id="delete_icon" style="display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; left:2px;" src="/images/trash.png" title="Delete" onclick="delete_event('<?=$log_entry_id?>')"/>
		</div>
	<?php else: ?>
		<div style="width:20px; float:right;">
			<img id="locked_icon" style="display:block; margin-bottom:12px; margin-right:15px; height:15px; position:relative; left:2px;" src="/images/locked.png" title="Locked <?=date("n/j H:i",strtotime($log_entry["locked_datetime"]))?>"/>
		</div>
	<?php endif; ?>
	<div style="margin-left:72px; width:50px; float:left;">Notes:</div><div style="width:700px; margin-left:130px;"><?=$log_entry["entry_notes"]?></div>
</div>