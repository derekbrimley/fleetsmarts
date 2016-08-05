<script>
	//alert('validation response');
	var log_entry_id = <?=$log_entry_id?>;
	var isValid = true;
	
	//VALIDATE
	<?php foreach($incomplete_goalpoints as $gp):?>
		if($("#did_gp_happen_"+<?=$gp["id"]?>).val() == 'Select')
		{
			alert('You must indicate whether the <?=$gp["gp_type"]?> in <?=$gp["location"]?> at <?=date("H:i",strtotime($gp["expected_time"]))?> happend or not!');
			isValid = false;
		}
		else
		{
			if($("#did_gp_happen_"+<?=$gp["id"]?>).val() == 'Yes')
			{
				if($("#gp_completion_date_"+<?=$gp["id"]?>).val())
				{
					var date_time_array = $("#gp_completion_date_"+<?=$gp["id"]?>).val().split(' ');
					
					if(date_time_array[0])
					{
						//VALIDATE DATE
						if(!isDate(date_time_array[0]))
						{
							isValid = false;
							alert("You must enter in a valid Date for the the <?=$gp["gp_type"]?> in <?=$gp["location"]?> at <?=date("H:i",strtotime($gp["expected_time"]))?>!");
						}
					}
					else
					{
						isValid = false;
						alert("You must enter in a valid Date for the the <?=$gp["gp_type"]?> in <?=$gp["location"]?> at <?=date("H:i",strtotime($gp["expected_time"]))?>!");
					}
					
					if(date_time_array[1])
					{
						//VALIDATE TIME
						if(!isTime(date_time_array[1]))
						{
							isValid = false;
							alert("You must enter in a valid Time for the the <?=$gp["gp_type"]?> in <?=$gp["location"]?> at <?=date("H:i",strtotime($gp["expected_time"]))?>!");
						}
					}
					else
					{
						isValid = false;
						alert("You must enter in a valid Time for the the <?=$gp["gp_type"]?> in <?=$gp["location"]?> at <?=date("H:i",strtotime($gp["expected_time"]))?>!");
					}
				}
				else
				{
					isValid = false;
					alert("Date Time must be entered for the the <?=$gp["gp_type"]?> in <?=$gp["location"]?> at <?=date("H:i",strtotime($gp["expected_time"]))?>!");
				}
			}
		}
		
	<?php endforeach;?>
	
	if(isValid)
	{
		$('.gp_exp_details_'+log_entry_id).css({"display":"none"});
		$('.gp_exp_loading_'+log_entry_id).css({"display":"block"});
		
		var dataString = $("#incomplete_goalpoints_form_<?=$log_entry_id?>").serialize();
		
		$("#missed_goalpoints_dialog_<?=$log_entry_id?>").dialog('close');
		$("#incomplete_goalpoints_dialog_overlay_"+log_entry_id).hide();
		$("#missed_goalpoints_save_button_"+log_entry_id).removeAttr('disabled');
		
		//alert(dataString);
		//var this_div = $("#missed_goalpoints_dialog_"+log_entry_id);
		var this_div = null;
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/logs/update_missing_goalpoints")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: this_div, 
			statusCode: {
				200: function(response){
					// Success!
					alert(response);
					//this_div.html(response);
					//alert('success');
					load_contact_attempts_div(<?=$log_entry_id?>);
					load_goalpoints_div(<?=$log_entry_id?>);
					
					
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
	else
	{
		$("#incomplete_goalpoints_dialog_overlay_"+log_entry_id).hide();
		$("#missed_goalpoints_save_button_"+log_entry_id).removeAttr('disabled');
	}
</script>