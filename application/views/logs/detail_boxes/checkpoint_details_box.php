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
	
	function oor_clicked(log_entry_id)
	{
		if($("#oor_cb_"+log_entry_id).is(":checked"))
		{
			var is_oor = "yes";
		}
		else
		{
			var is_oor = "no";
		}
		//alert("hello");
		var dataString = "&is_oor="+is_oor+"&log_entry_id="+log_entry_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/mark_oor")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			statusCode: {
				200: function(response){
					// Success!
					open_event_details(log_entry_id);
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
	
</script>
<div id="script_div_<?=$log_entry_id?>">
	<!-- AJAX SCRIPT GOES HERE !-->
</div>
<div style="height:125px; font-size:12px;">
	<?php if(empty($log_entry["locked_datetime"])): ?>
		<div style="height: 45px; width:20px; float:right;">
			<img title="Estimate Odometer" style='display:block; margin-bottom:12px; cursor:pointer; height:13px; position:relative; bottom:0px; right:1px;' src="/images/odometer.png" onclick="estimate_odometer('<?=$log_entry_id?>','<?=$log_entry["sync_entry_id"]?>')"/>
			<?php if($log_entry["entry_type"] == "Checkpoint" && empty($log_entry["sync_entry_id"])): ?>
				<img title="Attach End Leg" style='display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; bottom:1px; left:1px;' src="/images/make_new_leg.png" onclick="this.src='/images/loading.gif'; create_new_leg('<?=$log_entry_id?>')"/>
				<img title="Create Fuel Estimate" style='display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; bottom:1px; left:0px;' src="/images/make_fuel_estimate.png" onclick="this.src='/images/loading.gif'; create_fuel_estimate('<?=$log_entry_id?>')"/>
			<?php endif; ?>
			<img id="new_checkpoint" style="margin-bottom:13px; margin-right:15px; cursor:pointer; height:16px; position:relative; left:2px;" src="/images/new_checkpoint.png" title="New Checkpoint" onclick="this.src='/images/loading.gif'; create_new_checkpoint('<?=$log_entry["id"]?>');"/>
			<img id="delete_icon" style="display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; left:1px;" src="/images/trash.png" title="Delete" onclick="delete_event('<?=$log_entry_id?>')"/>
		</div>
		<?php if(empty($log_entry["sync_entry_id"])): ?>
			<div style="margin-left:72px;margin-bottom:10px;">OOR
				<input <?=$checked?> onclick="oor_clicked('<?=$log_entry_id?>')" type="checkbox" id="oor_cb_<?=$log_entry_id?>" name="oor_cb_<?=$log_entry_id?>"/>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<div style="height: 45px; width:20px; float:right;">
			<img title="End Leg" style='display:block; margin-bottom:12px; cursor:pointer; height:15px; position:relative; bottom:1px; left:1px;' src="/images/make_new_leg.png" onclick="create_new_leg('<?=$log_entry_id?>')"/>
		</div>
	<?php endif;?>
	<div style="margin-left:72px; width:50px; float:left;">Notes:</div><div style="width:700px; margin-left:130px;"><?=$log_entry["entry_notes"]?></div>
</div>