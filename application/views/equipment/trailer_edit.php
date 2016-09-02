<script>
	$("#scrollable_content").height($(window).height() - 155);

	//VALIDATE TRAILER NUMBER TO BE UNIQUE ON EDIT
	function trailer_number_edit_entered()
	{
		var this_div = $('#trailer_number_error');
		
		if($("#edit_trailer_number").val())
		{
			if($("#edit_trailer_number").val() != "<?=$trailer["trailer_number"]?>")
			{
				var dataString = "&trailer_number="+$("#edit_trailer_number").val();
				
				//alert(dataString.substring(1));
				
				//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
				// GET THE DIV IN DIALOG BOX
				
				//POST DATA TO PASS BACK TO CONTROLLER
				
				// AJAX!
				$.ajax({
					url: "<?= base_url("index.php/equipment/validate_edit_trailer_number")?>", // in the quotation marks
					type: "POST",
					data: dataString,
					cache: false,
					context: this_div, // use a jquery object to select the result div in the view
					statusCode: {
						200: function(response){
							// Success!
							this_div.html(response);
							
							//alert($('#trailer_number_edit_is_valid').val());
							
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
			else //IF IT HAS BEEN CHANGED BACK TO WHAT IT WAS ORIGINALLY
			{
				this_div.html("*");
				$('#trailer_number_edit_is_valid').val('true');
				//alert($('#trailer_number_edit_is_valid').val());
			}
		}
		else //IF TRAILER NUMBER IS BLANK
		{
			this_div.html("*");
			$('#trailer_number_edit_is_valid').val('false');
			//alert($('#trailer_number_edit_is_valid').val());
		}
	}//end truck_number_entered()
	
	//VALIDATE SAVE EDIT TRAILER
	function validate_save_edit_trailer()
	{
		$("#back_btn").hide();
		$("#edit_trailer").hide();
		$("#save_trailer").hide();
		$("#loading_img").show();
		
		var isValid = true;
		
		//CHECK TRAILER NUMBER
		if(!$("#edit_trailer_number").val())
		{
			isValid = false;
			alert("Trailer Number must be entered for this trailer!");
		}
		else
		{
			if($("#trailer_number_edit_is_valid").val() == "false")
			{
				isValid = false;
				alert("This Trailer Number already exists in the system!");
			}
		}
		
		//VALIDATE TRAILER VALUE
		if($("#value").val())
		{
			if(isNaN($("#value").val()))
			{
				isValid = false;
				alert("Trailer Value must be a number!");
			}
		}
		
		//VALIDATE RENTAL RATE
		if($("#rental_rate").val())
		{
			if(isNaN($("#rental_rate").val()))
			{
				isValid = false;
				alert("Rental Rate must be a number!");
			}
		}
		
		//VALIDATE MILEAGE RATE
		if($("#mileage_rate").val())
		{
			if(isNaN($("#mileage_rate").val()))
			{
				isValid = false;
				alert("Mileage Rate must be entered a number!");
			}
		}
		
		//VALIDATE LAST INSPECTION
		if($("#last_inspection").val())
		{
			if(isNaN($("#last_inspection").val()))
			{
				isValid = false;
				alert("Last Inspection must be a number!");
			}
		}
		
		//VALIDATE LAST SERVICE
		if($("#last_service").val())
		{
			if(isNaN($("#last_service").val()))
			{
				isValid = false;
				alert("Last Service must be a number!");
			}
		}
		
		if(isValid)
		{
			var dataString = "";
			
			$("#trailer_edit_form select").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#trailer_edit_form input").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#trailer_edit_form textarea").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			//alert(dataString.substring(1));
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var main_content = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/equipment/save_trailer")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: main_content, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						main_content.html(response);
						
						// $("#loading_img").hide();
						// $("#edit_trailer").show();
						// $("#back_btn").show();
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
		else //IF DOESN'T PASS VALIDATION
		{
			// $("#loading_img").hide();
			// $("#back_btn").show();
			// $("#edit_trailer").show();
			// $("#save_trailer").show();
		}//end if_isvalid()
		
	}
</script>
<style>
	.edit_box
	{
		width:161px;
		height:21px;
	}
	.error_div
	{
		color:red;
		margin-left:5px;
	}
</style>
<div id="main_content_header">
	<span style="font-weight:bold;">Edit Trailer <?=$trailer["trailer_number"]?></span>
	<img src="/images/save.png" style="cursor:pointer;float:right;margin-top:4px;height:20px; margin-left:10px;" id="save_trailer" onclick="validate_save_edit_trailer()"/>
	<img src="/images/back.png" style="cursor:pointer;float:right;margin-top:4px;height:20px;" id="back_btn" onclick="load_trailer_details('<?=$trailer["id"]?>')"/>
	<img src="/images/loading.gif" style="display:none;float:right;margin-top:4px;height:20px;" id="loading_img"/>
</div>

<div id="scrollable_content" class="scrollable_div">
	<?php $attributes = array('id' => 'trailer_edit_form'); ?>
	<?=form_open('equipment/save_trailer',$attributes)?>
	<input type="hidden" id="trailer_id" name="trailer_id" value="<?=$trailer["id"]?>">
	<input type="hidden" id="trailer_number_edit_is_valid" name="trailer_number_edit_is_valid" value="true">
		<div style="margin:20px;">
			<table id="trailer_view" style="font-size: 14px;">
				<tr>
					<td>Trailer Status</td>
					<td>
						<?php $options = array(
						'On the road'  	=> 'On the road',
						'In the shop'  => 'In the shop',
						'Retired'  => 'Retired',
						); ?>
						<?php echo form_dropdown('trailer_status',$options,$trailer['trailer_status'],'id="trailer_status" style="width:161px; height:21px;"');?>
					</td>
				</tr>
				<tr>
					<td>Dropdown Status:</td>
					<td>
						<?php $options = array(
							'Show'  => 'Show',
							'Hide'  => 'Hide',
							); 
						?>
						<?php echo form_dropdown('dropdown_status',$options,$trailer['dropdown_status'],'id="dropdown_status" style="width:161px; height:21px;"');?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td>Client</td>
					<td>
						<?php echo form_dropdown('edit_client',$client_dropdown_options,$trailer["client_id"],'id="edit_client" style="width:161px; height:21px;"');?>
					</td>
				</tr>
				<tr>
					<td>Lease Company</td>
					<td>
						<?php echo form_dropdown('edit_leasing_company',$vendor_dropdown_options,$trailer['vendor']["company_side_bar_name"],'id="edit_leasing_company" style="width:161px; height:21px;"');?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Trailer Number</td>
					<td><input value="<?=$trailer['trailer_number'];?>"id="edit_trailer_number" name="edit_trailer_number" type="text" class="edit_box" onblur="trailer_number_edit_entered()"></td>
					<td>
						<div class="error_div" id="trailer_number_error">*</div>
					</td>
				</tr>
				<tr>
					<td>Trailer Type</td>
					<td>
						<?php $options = array(
						'Select'  	=> 'Select',
						'Dry Van'  	=> 'Dry Van',
						'Reefer'  => 'Reefer',
						); ?>
						<?php echo form_dropdown('trailer_type',$options,$trailer['trailer_type'],'id="trailer_type" style="width:161px; height:21px;"');?>
					</td>
				</tr>
				<tr>
					<td>Length</td>
					<td><input value="<?=$trailer['length'];?>"id="length" name="length" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>Door Type</td>
					<td>
						<?php $options = array(
						'Select'  	=> 'Select',
						'Swing'  	=> 'Swing',
						'Roll'  => 'Roll',
						); ?>
						<?php echo form_dropdown('door_type',$options,$trailer['door_type'],'id="door_type" style="width:161px; height:21px;"');?>
					</td>
				</tr>
				<tr>
					<td>Tire Model</td>
					<td><input value="<?=$trailer['tire_model'];?>"id="tire_model" name="tire_model" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>Tire Make</td>
					<td><input value="<?=$trailer['tire_make'];?>"id="tire_make" name="tire_make" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>Tire Size</td>
					<td><input value="<?=$trailer['tire_size'];?>"id="tire_size" name="tire_size" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>Insulation</td>
					<td>
						<?php $options = array(
						'Select'  	=> 'Select',
						'Old Foam'  	=> 'Old Foam',
						'Wood'  => 'Wood',
						'Uninsulated'  => 'Uninsulated',
						); ?>
						<?php echo form_dropdown('insulation_type',$options,$trailer['insulation_type'],'id="insulation_type" style="width:161px; height:21px;"');?>
					</td>
				</tr>
				<tr>
					<td>Vents</td>
					<td>
						<?php $options = array(
						'Select'  	=> 'Select',
						'Ghetto'  	=> 'Ghetto',
						'Professional'  => 'Professional',
						'Unvented'  => 'Unvented',
						); ?>
						<?php echo form_dropdown('vent_type',$options,$trailer['vent_type'],'id="vent_type" style="width:161px; height:21px;"');?>
					</td>
				</tr>
				<tr>
					<td>E Tracks</td>
					<td>
						<?php $options = array(
						'Select'  	=> 'Select',
						'E tracks'  	=> 'E tracks',
						'None'  => 'None',
						); ?>
						<?php echo form_dropdown('etracks',$options,$trailer['etracks'],'id="etracks" style="width:161px; height:21px;"');?>
					</td>
				</tr>
				<tr>
					<td>Suspension</td>
					<td>
						<?php $options = array(
						'Select'  	=> 'Select',
						'Air Ride'  	=> 'Air Ride',
						'Spring'  => 'Spring',
						); ?>
						<?php echo form_dropdown('suspension_type',$options,$trailer['suspension_type'],'id="suspension_type" style="width:161px; height:21px;"');?>
					</td>
				</tr>
				<tr>
					<td>Trailer Make</td>
					<td><input value="<?=$trailer['make'];?>"id="make" name="make" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>Trailer Model</td>
					<td><input value="<?=$trailer['model'];?>"id="model" name="model" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>Trailer Year</td>
					<td><input value="<?=$trailer['year'];?>"id="year" name="year" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>Trailer Value</td>
					<td><input value="<?=$trailer['value'];?>"id="value" name="value" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>VIN</td>
					<td><input value="<?=$trailer['vin'];?>"id="vin" name="vin" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>Plate Number</td>
					<td><input value="<?=$trailer['plate_number'];?>"id="plate_number" name="plate_number" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>Plate State</td>
					<td><input value="<?=$trailer['plate_state'];?>"id="plate_state" name="plate_state" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>Insurance Policy</td>
					<td><input value="<?=$trailer['insurance_policy'];?>"id="insurance_policy" name="insurance_policy" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>Rental Rate</td>
					<td>
						<input value="<?=$trailer['rental_rate'];?>"id="rental_rate" name="rental_rate" type="text" class="edit_box" style="width:57px">
						per 
						<?php $options = array(
						'Select'  	=> 'Select',
						'Day'  	=> 'Day',
						'Week'  => 'Week',
						'Month'  => 'Month',
						); ?>
						<?php echo form_dropdown('trailer_rental_period',$options,$trailer["rental_period"],'id="trailer_rental_period" class="edit_box" style="width:70px; margin-left:5px;"');?>
					</td>
				</tr>
				<tr>
					<td>Mileage Rate</td>
					<td><input value="<?=$trailer['mileage_rate'];?>"id="mileage_rate" name="mileage_rate" type="text" class="edit_box"></td>
				</tr>
				<tr>
				<td>Current Registration</td>
					<td>
						<?php if(!empty($trailer['registration_link'])):?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$trailer["registration_link"]?>" onclick="">Current Registration</a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td>Current Insurance</td>
					<td>
						<?php if(!empty($trailer['insurance_link'])):?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$trailer["insurance_link"]?>" onclick="">Current Insurance</a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td>Current Lease Agreement</td>
					<td>
						<?php if(!empty($trailer['lease_agreement_link'])):?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$trailer["lease_agreement_link"]?>" onclick="">Current Lease Agreement</a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td>Last Inspection (mileage)</td>
					<td><input value="<?=$trailer['last_inspection'];?>"id="last_inspection" name="last_inspection" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>Last Service (mileage)</td>
					<td><input value="<?=$trailer['last_service'];?>"id="last_service" name="last_service" type="text" class="edit_box"></td>
				</tr>
				<tr>
					<td>iBright ID</td>
					<td><input value="<?=$trailer['ibright_id'];?>"id="ibright_id" name="ibright_id" type="text" class="edit_box"></td>
				</tr>
			</table>
		</div>
	</form>
</div>