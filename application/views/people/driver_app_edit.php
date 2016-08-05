<script>
	$("#scrollable_content").height($("#body").height() - 155);
	
	//ADD INVOICE NOTE DIALOG
	$( "#add_applicant_notes").dialog(
	{
		autoOpen: false,
		height: 400,
		width: 420,
		modal: true,
		buttons: 
			[
				{
					text: "Save",
					click: function() 
					{
						if(!$("#new_note").val())
						{
							alert("You didn't enter a new note!");
						}
						else
						{
							save_note();
						}
						
						
						
					},//end add load
				},
				{
					text: "Close",
					click: function() 
					{
						//CLEAR TEXT AREA
						$("#new_note").val("");
						$( this ).dialog( "close" );
					}
				}
			],//end of buttons
		
		open: function()
			{
			},//end open function
		close: function() 
			{
				//clear_load_info();
			}
	});//end settlement add notes dialog
	
	
	//AJAX FOR GETTING NOTES
	function open_applicant_notes(application_id)
	{
		//alert(application_id);
		//RESET LOADING GIF
		$("#notes_ajax_div").html("");
		
		//SET NOTES_ID TO VALUE OF LOAD ID
		//$("#notes_id").val(truck_id);
		$("#application_id").val(application_id); //this is the hidden field in the add notes form
		
		//OPEN THE DIALOG BOX
		$( "#add_applicant_notes").dialog( "open" );
		
		//alert('inside ajax');
				
		// GET THE DIV IN DIALOG BOX
		var this_div = $('#notes_ajax_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/get_app_notes/")?>"+"/"+application_id, // in the quotation marks
			type: "POST",
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
					alert("500 error!")
				}
			}
		});//END AJAX
		
		return false; 
		
	}//end open_add_notes()
	
	//VALIDATE AND SAVE NOTE
	function save_note()
	{
		var dataString = "";
		
		dataString = $("#add_note_form").serialize();
		
		//CLEAR TEXT AREA
		$("#new_note").val("");
		
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
		// GET THE DIV IN DIALOG BOX
		var notes_ajax_div = $('#notes_ajax_div');
		
		//POST DATA TO PASS BACK TO CONTROLLER
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/save_note")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: notes_ajax_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					notes_ajax_div.html(response);
					$("#notes_details").html(response);
					//update_notes_td($("#billing_note_load_id").val());
					
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
		
	}//end save_note()
	
	//VALIDATE AND SAVE CLIENT
	function validate_and_save_applicant()
	{
		var isValid = true;
		
		if($("#fleet_manager_edit").val() == 0)
		{
			isValid = false;
			alert("You must assign a Fleet Manager!");
		}
		
		//VALIDATE APPLICATION DATE TIME
		if ($("#app_datetime_edit").val().length != 14)
		{
			isValid = false;
			alert("Application Datetime must be in the correct format! (mm/dd/yy hh:mm)");
		}
		else
		{
			var date = $("#app_datetime_edit").val().substring(0,8);
			var time = $("#app_datetime_edit").val().substring(9,14);
			
			if(!isDate(date) || !isTime(time))
			{
				isValid = false;
				alert("Application Datetime must be in the correct format! (mm/dd/yy hh:mm)");
			}
		}

		
		//IF EVERY INPUT IS VALID FOR SUBMISSION
		if(isValid)
		{
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#save_applicant_form").serialize();
			
			//alert(dataString);
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var main_content = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/people/save_applicant")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: main_content, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						main_content.html(response);
						main_content.show();
						
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
		else
		{
			//alert("You missed something! Scroll up to see what it was!");
		}
		
	}//end validate_and_save_client()
	
</script>

<div id="main_content_header">
	<span style="font-weight:bold;"><?=$this_client["client_nickname"] ?></span>
	<button class="jq_button" style="float:right; width:80px; margin-left:20px;" onclick="validate_and_save_applicant()">Save</button>
	<button class="jq_button" style="float:right; width:80px; margin-left:20px;" onclick="load_driver_details('<?=$this_client["id"]?>')">Cancel</button>
</div>
<div id="scrollable_content" class="scrollable_div">
	<?php $attributes = array('id' => 'save_applicant_form', 'name' => 'save_applicant_form'); ?>
	<?=form_open('people/save_applicant',$attributes)?>
		<input type="hidden" id="client_id" name="client_id" value="<?=$this_client["id"]?>">
		<div id="personal_info" style="margin:20px;">
			<div>
				<span class="section_heading">Applicant Status Log</span>
				<span style="float:right;"><a href="javascript:open_applicant_notes('<?=$driver_app["id"]?>')">Add Note</a><span>
			</div>
			<hr/>
			<div id="notes_details">
				<?=str_replace("\n","<br>",$driver_app["applicant_status_log"]);?>
			</div>
		</div>
		<div id="user"  style="margin:20px;">
			<span class="section_heading">Client Info</span>
			<hr/>
			<br>
			<table>
				<tr>
					<td>Driver Type</td>
					<td>
						<?php $options = array(
							'Main Driver'  => 'Main Driver',
							'Co-Driver'    => 'Co-Driver',
							'Applicant'    => 'Applicant',
							); 
						?>
						<?php echo form_dropdown('client_type_edit',$options,$this_client["client_type"],'id="client_type_edit" class="main_content_dropdown"');?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Applicant Status</td>
					<td>
						<?php $options = array(
							'Replied to Ad' => 'Replied to Ad',
							'Submitted App'=> 'Submitted App',
							'Communicating'=> 'Communicating',
							'Committed'=> 'Committed',
							'On the Truck'=> 'On the Truck',
							'Closed' => 'Closed',
							); 
						?>
						<?php echo form_dropdown('client_status_edit',$options,$this_client['client_status'],'id="client_status_edit" class="main_content_dropdown"');?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td>Application Datetime</td>
					<td>
						<input type="text" id="app_datetime_edit" name="app_datetime_edit" class="main_content_dropdown" value="<?=date('m/d/y H:i',strtotime($driver_app["application_datetime"]))?>">
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Assigned FM</td>
					<td>
						<?php echo form_dropdown('fleet_manager_edit',$fleet_manager_dropdown_options,$this_client["fleet_manager_id"],'id="fleet_manager_edit" class="main_content_dropdown"');?>
					</td>
					<td style="color:red; width:5px;">
					</td>
				</tr>
			</table>
		</div>
		<div id="user"  style="margin:20px;">
			<span class="section_heading">Driver Application</span>
			<hr/>
			<br>
			<table>
				<tr>
					<td style="width:300px;">First Name</td>
					<td>
						<?= $driver_app["f_name"];?>
					</td>
				</tr>
				<tr>
					<td>Middle Name</td>
					<td>
						<?= $driver_app["m_name"];?>
					</td>
				</tr>
				<tr>
					<td>Last Name</td>
					<td>
						<?= $driver_app["l_name"];?>
					</td>
				</tr>
				<tr>
					<td>Phone Number</td>
					<td>
						<?= $driver_app["phone_number"];?>
					</td>
				</tr>
				<tr>
					<td>Email</td>
					<td>
						<?= $driver_app["email"];?>
					</td>
				</tr>
				<tr>
					<td>Date of Birth</td>
					<td>
						<?= $driver_app["dob"];?>
					</td>
				</tr>
				<tr>
					<td>SSN</td>
					<td>
						<?= $driver_app["ssn"];?>
					</td>
				</tr>
				<tr>
					<td>Driving Experience</td>
					<td>
						<?= $driver_app["driving_experience"];?>
					</td>
				</tr>
				<tr>
					<td>Drive Team?</td>
					<td>
						<?= $driver_app["drive_team"];?>
					</td>
				</tr>
				<tr>
					<td>Drive OTR?</td>
					<td>
						<?= $driver_app["drive_otr"];?>
					</td>
				</tr>
				<tr>
					<td>Availability</td>
					<td>
						<?= $driver_app["availability_date"];?>
					</td>
				</tr>
				<tr>
					<td>Current Address</td>
					<td>
						<?= $driver_app["current_address"];?>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>

<div id="add_applicant_notes" title="Add Note" style="padding:10px; display:none;">
	<div>
		<span id="notes_header" style="font-weight:bold;">Applicant Status Log</span>
		<br>
		<br>
		<div id="notes_ajax_div" style="height:215px; overflow:auto">
			<!-- AJAX WILL POPULATE THIS !-->
		</div>
	</div>
	<div style="position:absolute; bottom:0">
		<?php $attributes = array('name'=>'add_note_form','id'=>'add_note_form', )?>
		<?=form_open('people/add_note/',$attributes);?>
			Add Note:
			<input type="hidden" id="application_id" name="application_id">
			<textarea style="width:400px;" rows="3" id="new_note" name="new_note"></textarea>
		</form>
	</div>
</div>