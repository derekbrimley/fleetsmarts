<script>
	$("#scrollable_content").height($("#main_content").height() - 40);
	
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
	
</script>

<div id="main_content_header">
	<span style="font-weight:bold;"><?=$this_client["client_nickname"] ?></span>
	<button class='jq_button' style="float:right; width:80px;" id="edit_client" onclick="load_driver_edit('<?=$this_client["id"]?>')">Edit</button>
</div>
<div id="scrollable_content" class="scrollable_div">
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
				<td style="width:300px;">Driver Type</td>
				<td>
					<?=$this_client["client_type"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Applicant Status</td>
				<td>
					<?=$this_client["client_status"];?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Application Datetime</td>
				<td>
					<?= date('m/d/y H:i',strtotime($driver_app["application_datetime"]));?>
				</td>
			</tr>
			<tr>
				<td style="width:300px;">Assigned FM</td>
				<td>
					<?=$this_client["fleet_manager"]["full_name"];?>
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
			<tr>
				<td>Drug Record</td>
				<td>
					<?= $driver_app["tested_positive_or_refused"];?>
				</td>
			</tr>
			<tr>
				<td>Past Address 1</td>
				<td>
					<?= $driver_app["previous_address_1"];?>
				</td>
			</tr>
			<tr>
				<td>Past Address 2</td>
				<td>
					<?= $driver_app["previous_address_2"];?>
				</td>
			</tr>
			<tr>
				<td>Past Address 3</td>
				<td>
					<?= $driver_app["previous_address_3"];?>
				</td>
			</tr>
		</table>
	</div>
	<div id="user"  style="margin:20px;">
		<span class="section_heading">License Information</span>
		<hr/>
		<br>
		<table>
			<tr style="font-weight:bold;">
				<td style="width:150px;" >
					State
				</td>
				<td style="width:150px;" >
					License No
				</td>
				<td style="width:150px;" >
					Type
				</td>
				<td style="width:150px;" >
					Exp Date
				</td>
			</tr>
			<?php for($i=1; $i<=4; $i++):?>
				<tr>
					<td>
						<?= $driver_app["previous_license_state_$i"];?>
					</td>
					<td>
						<?= $driver_app["previous_license_number_$i"];?>
					</td>
					<td>
						<?= $driver_app["previous_license_type_$i"];?>
					</td>
					<td>
						<?= $driver_app["previous_license_exp_date_$i"];?>
					</td>
				</tr>
			<?php endfor; ?>
		</table>
	</div>
	<div id="user"  style="margin:20px;">
		<span class="section_heading">Accident History</span>
		<hr/>
		<br>
		<table>
			<tr style="font-weight:bold;">
				<td style="width:100px;" >
					Date
				</td>
				<td style="width:400px;" >
					Nature
				</td>
				<td style="width:100px;" >
					Fatalities
				</td>
				<td style="width:100px;" >
					Injuries
				</td>
			</tr>
			<?php for($i=1; $i<=3; $i++):?>
				<tr>
					<td>
						<?= $driver_app["accident_date_$i"];?>
					</td>
					<td>
						<?= $driver_app["accident_nature_$i"];?>
					</td>
					<td>
						<?= $driver_app["accident_fatalities_$i"];?>
					</td>
					<td>
						<?= $driver_app["accident_injuries_$i"];?>
					</td>
				</tr>
			<?php endfor; ?>
		</table>
	</div>
	<div id="user"  style="margin:20px;">
		<span class="section_heading">Employment History</span>
		<hr/>
		<br>
		<table>
			<tr style="font-weight:bold;">
				<td style="width:100px;" >
					Employer
				</td>
				<td style="width:100px;" >
					Address
				</td>
				<td style="width:100px;" >
					Position
				</td>
				<td style="width:100px;" >
					Start Date
				</td>
				<td style="width:100px;" >
					End Date
				</td>
				<td style="width:100px;" >
					Salary
				</td>
				<td style="width:100px;" >
					Reasons for Leaving
				</td>
				<td style="width:100px;" >
					Subject to FMCSR's?
				</td>
				<td style="width:100px;" >
					Drug/Alcohol testing?
				</td>
			</tr>
			<?php for($i=1; $i<=3; $i++):?>
				<tr style="font-size:10px;">
					<td style="width:100px;" >
						<?= $driver_app["previous_job_employer_name_$i"];?>
					</td>
					<td style="width:100px;" >
						<?= $driver_app["previous_job_address_$i"];?>
					</td>
					<td style="width:100px;" >
						<?= $driver_app["previous_job_position_$i"];?>
					</td>
					<td style="width:100px;" >
						<?= $driver_app["previous_job_start_date_$i"];?>
					</td>
					<td style="width:100px;" >
						<?= $driver_app["previous_job_end_date_$i"];?>
					</td>
					<td style="width:100px;" >
						<?= $driver_app["previous_job_salary_$i"];?>
					</td>
					<td style="width:100px;" >
						<?= $driver_app["previous_job_reason_for_leaving_$i"];?>
					</td>
					<td style="width:100px;" >
						<?= $driver_app["previous_job_subject_to_fmcsr_$i"];?>
					</td>
					<td style="width:100px;" >
						<?= $driver_app["previous_job_drug_test_$i"];?>
					</td>
				</tr>
			<?php endfor; ?>
		</table>
	</div>
	<div id="user"  style="margin:20px;">
		<span class="section_heading">Personal References</span>
		<hr/>
		<br>
		<table>
			<tr style="font-weight:bold;">
				<td style="width:150px;" >
					Name
				</td>
				<td style="width:100px;" >
					Relationship
				</td>
				<td style="width:150px;" >
					Phone Number
				</td>
				<td style="width:100px;" >
					Address
				</td>
			</tr>
			<?php for($i=1; $i<=3; $i++):?>
				<tr>
					<td>
						<?= $driver_app["personal_reference_$i"];?>
					</td>
					<td>
						<?= $driver_app["personal_reference_relationship_$i"];?>
					</td>
					<td>
						<?= $driver_app["personal_reference_number_$i"];?>
					</td>
					<td>
						<?= $driver_app["personal_reference_address_$i"];?>
					</td>
				</tr>
			<?php endfor; ?>
		</table>
	</div>
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