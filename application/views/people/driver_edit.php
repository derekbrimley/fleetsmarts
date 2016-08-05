<script>
	$("#scrollable_content").height($("#body").height() - 155);
	
	//DIALOG: UPLOAD SIGNATURE DIALOG
	$( "#upload_signature_dialog" ).dialog(
	{
		autoOpen: false,
		height: 200,
		width: 400,
		modal: true,
		buttons: 
		[
			{
				text: "Upload",
				click: function() 
				{
					//SUBMIT FORM
					$("#upload_signature_form").submit();
					$( this ).dialog( "close" );
				},//end add load
			},
			{
				text: "Cancel",
				click: function() 
				{
					//RESIZE DIALOG BOX
					$( this ).dialog( "close" );
				}
			}
		],//end of buttons
		open: function()
		{
		},//end open function
		close: function() 
		{
		}
	});//end dialog form
	
	//DIALOG: UPLOAD SIGNATURE DIALOG
	$( "#upload_contract_dialog" ).dialog(
	{
		autoOpen: false,
		height: 200,
		width: 400,
		modal: true,
		buttons: 
		[
			{
				text: "Upload",
				click: function() 
				{
					//SUBMIT FORM
					$("#upload_contract_form").submit();
					$( this ).dialog( "close" );
					
					
					
				},//end add load
			},
			{
				text: "Cancel",
				click: function() 
				{
					//RESIZE DIALOG BOX
					$( this ).dialog( "close" );
				}
			}
		],//end of buttons
		open: function()
		{
		},//end open function
		close: function() 
		{
		}
	});//end dialog form
	
	function load_signature_dialog(person_id)
	{
		
		//-------------- AJAX -------------------
		// GET THE DIV IN DIALOG BOX
		var target_div = $('#upload_signature_dialog');
		target_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="width:25px; height:25px; margin-top:50px;" /></div>');
		$('#upload_signature_dialog').dialog('open')
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = 'person_id='+person_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_signature_div")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: target_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					target_div.html(response);
					
					
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
	
	function load_contract_dialog(client_id)
	{
		
		//-------------- AJAX -------------------
		// GET THE DIV IN DIALOG BOX
		var target_div = $('#upload_contract_dialog');
		target_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="width:25px; height:25px; margin-top:50px;" /></div>');
		$('#upload_contract_dialog').dialog('open')
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = 'client_id='+client_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_contract_div")?>", // in the quotation marks
			type: "POST",
			data: dataString,
			cache: false,
			context: target_div, // use a jquery object to select the result div in the view
			statusCode: {
				200: function(response){
					// Success!
					target_div.html(response);
					
					
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
	
	var last_visible_fee = 1;
	var last_visible_rs = 1;
	
	//PLACE DATE PICKERS ON ALL THE DATE BOXES
	$('#dob_edit').datepicker({ showAnim: 'blind' });
	$('#license_expiration_edit').datepicker({ showAnim: 'blind' });
	$('#cdl_since_edit').datepicker({ showAnim: 'blind' });
	$('#start_date_edit').datepicker({ showAnim: 'blind' });
	$('#end_date_edit').datepicker({ showAnim: 'blind' });
	$('#first_full_settlement_date_edit').datepicker({ showAnim: 'blind' });
	
	//VALIDATE AND SAVE CLIENT
	function validate_and_save_client()
	{
		//CREATE USERNAME VALIDATION LIST ARRAY
		var username_validation_list = [
		<?php 	
				$array_string = "";
				foreach($all_users as $user)
				{
					$username = $user['username'];
					$array_string = $array_string.'"'.$username.'",';
				}
				echo substr($array_string,0,-1);
		?>];
		
		var valid_input = true;
		
		$('#short_name_edit_alert').hide();
		$('#profit_split_edit_alert').hide();
		$('#f_name_edit_alert').hide();
		$('#l_name_edit_alert').hide();
		$('#username_edit_alert').hide();
		$('#username_exists_edit_alert').hide();
		$('#password_edit_alert').hide();
		$('#phone_num_edit_alert').hide();
		$('#phone_carrier_edit_alert').hide();
		$('#email_edit_alert').hide();
		$('#dob_edit_alert').hide();
		$('#license_state_edit_alert').hide();
		$('#license_expiration_edit_alert').hide();
		$('#cdl_since_edit_alert').hide();
		$('#start_date_edit_alert').hide();
		$('#end_date_edit_alert').hide();
		
		
		var short_name = $('#short_name_edit').val();
		var f_name = $('#f_name_edit').val();
		var f_name = $('#f_name_edit').val();
		var l_name = $('#l_name_edit').val();
		var username = $('#username_edit').val();
		var password = $('#password_edit').val();
		var phone_num = $('#phone_num_edit').val();
		var phone_carrier = $('#phone_carrier_edit').val();
		var email = $('#email_edit').val();
		var dob = $('#dob_edit').val();
		var license_number = $('#license_number_edit').val();
		var license_state = $('#license_state_edit').val();
		var license_expiration = $('#license_expiration_edit').val();
		var cdl_since = $('#cdl_since_edit').val();
		var start_date = $('#start_date_edit').val();
		var end_date = $('#end_date_edit').val();
		var first_full_settlement_date = $('#first_full_settlement_date').val();
		
		
		//VALIDATE ALL THE REQUIRED FIELDS
		
		//VALIDATE SHORT NAME
		if(!short_name)
		{
			$('#short_name_edit_alert').show();
			valid_input = false;
		}
		
		//VALIDATE FIRST NAME
		if(!f_name)
		{
			$('#f_name_edit_alert').show();
			valid_input = false;
		}
		
		//VALIDATE LAST NAME
		if(!l_name)
		{
			$('#l_name_edit_alert').show();
			valid_input = false;
		}
		
		//VALIDATE NON-REQUIRED FIELDS THAT NEED VALIDATION
		if($("#profit_split_edit").val())
		{
			if(isNaN($("#profit_split_edit").val()))
			{
				$('#profit_split_edit_alert').show();
				valid_input = false;
			}
			else
			{
				if($("#profit_split_edit").val() < 0 || $("#profit_split_edit").val() > 100)
				{
					$('#profit_split_edit_alert').show();
					valid_input = false;
				}
			}
		}
		
		
		//MAKE SURE USERNAME IS UNIQUE AND PASSWORD IS INPUTTED
		if (username)
		{
			//IF THE USERNAME HAS BEEN CHANGED
			if (username != '<?=$this_client["user"]["username"]?>')
			{
				//DOES THE USERNAME ALREADY EXIST?
				var username_found = false;
				for(var this_username in username_validation_list)
				{
					if(username == username_validation_list[this_username])
					{
						username_found = true;
						break;
					}
				}
				if(username_found)
				{
					$('#username_exists_edit_alert').show();
					valid_input = false;
				}
			}
			
			if (password == '')
			{
				$('#password_edit_alert').show();
				valid_input = false;
			}
		}
		
		//IS PHONE NUMBER VALID
		if (phone_num)
		{
			phone_num = phone_num.replace(/[^0-9]/g, '');
			if(phone_num.length != 10)
			{
				$('#phone_num_edit_alert').show();
				valid_input = false;
			}else
			{
				$("#phone_num_edit").val(phone_num);
			}
			
			if (phone_carrier == 'Select')
			{
				$('#phone_carrier_edit_alert').show();
				valid_input = false;
			}
		}
		
		//IS EMAIL VALID
		if (email)
		{
			if(!validate_email(email))
			{
				$('#email_edit_alert').show();
				valid_input = false;
			}
		}
		
		//IS DOB VALID
		if(dob)
		{
			if (!isDate(dob))
			{
				$('#dob_edit_alert').show();
				valid_input = false;
			}
		}
		
		//IS LICENSE STATE VALID
		if(license_state)
		{
			if (license_state.length != 2)
			{
				$('#license_state_edit_alert').show();
				valid_input = false;
			}
		}
		
		//IS LICENSE EXPIRATION VALID
		if(license_expiration)
		{
			if (!isDate(license_expiration))
			{
				$('#license_expiration_edit_alert').show();
				valid_input = false;
			}
		}
		
		//IS CDL SINCE VALID
		if(cdl_since)
		{
			if (!isDate(cdl_since))
			{
				$('#cdl_since_edit_alert').show();
				valid_input = false;
			}
		}
		
		//IS START DATE VALID
		if(start_date)
		{
			if (!isDate(start_date))
			{
				$('#start_date_edit_alert').show();
				valid_input = false;
			}
		}
		
		//IS START DATE VALID
		if(end_date)
		{
			if (!isDate(end_date))
			{
				$('#start_date_edit_alert').show();
				valid_input = false;
			}
		}
		
		//VALIDATE CREDIT SCORE
		if($("#credit_score_edit").val())
		{
			if(isNaN($("#credit_score_edit").val()))
			{
				alert('Credit Score must be a number!');
				valid_input = false;
			}
		}
		
		//IF EVERY INPUT IS VALID FOR SUBMISSION
		if(valid_input)
		{
			//BUILD DATA STRING TO PASS TO CONTROLLER
			var dataString = $("#save_driver_form").serialize();
			
			//alert(dataString);
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var main_content = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/people/save_driver")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: main_content, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						main_content.html(response);
						main_content.show();
						
						//alert(	response);
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
			alert("You missed something! Scroll up to see what it was!");
		}
		
	}//end validate_and_save_client()
	
</script>

<div id="main_content_header">
	<span style="font-weight:bold;"><?=$this_client["client_nickname"]?> (Edit)</span>
	<img src="<?=base_url("images/save.png")?>" style="margin-top:4px;cursor:pointer;float:right;height:20px;" id="save_carrier" onclick="validate_and_save_client()"/>
	<img src="<?=base_url("images/back.png")?>" style="margin-top:4px;cursor:pointer;float:right;height:20px;margin-right:15px;" id="cancel_edit_carrier" onclick="load_driver_details('<?=$this_client["id"]?>')"/>
	<img src="<?=base_url("images/loading.gif")?>" style="display:none;margin-top:4px;cursor:pointer;float:right;height:20px;" id="loading_icon"/>
</div>
<div id="scrollable_content" class="scrollable_div">
	<?php $attributes = array('id' => 'save_driver_form'); ?>
	<?=form_open_multipart('people/save_driver',$attributes)?>
		<input type="hidden" id="client_id" name="client_id" value="<?=$this_client["id"]?>"/>
		<input type="hidden" id="user_id" name="user_id" value="<?=$this_client["user"]["id"]?>"/>
		<input type="hidden" id="person_id" name="person_id" value="<?=$this_client["company"]["person"]["id"]?>"/>
		<div id="personal_info_edit" style="margin:20px;">
			<span class="section_heading">Personal Info</span>
			<hr/>
			<br>
			<table id="main_content_table">
				<tr>
					<td style="width:300px;">Driver Side-Bar Name</td>
					<td>
						<input type="text" id="short_name_edit" name="short_name_edit" value="<?=$this_client["client_nickname"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
					<td>
						<span id="short_name_edit_alert" class="alert" style="display:none;">Client Short Name must be entered and less than 17 characters long</span>
					</td>
					
				</tr>
				<tr>
					<td>Driver Type:</td>
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
					<td style="width:130px;">Pay Structure</td>
					<td>
						<?php
							$options = null;
							$options = array(
								"Profit Based" => "Profit Based",
								"Training Stipend" => "Training Stipend",
								"Placement Repayment" => "Placement Repayment",
							);
						?>
						<?php echo form_dropdown("pay_structure_dropdown",$options,$this_client["pay_structure"],'id="pay_structure_dropdown" class="main_content_dropdown"');?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Profit Split</td>
					<td>
						<input type="text" id="profit_split_edit" name="profit_split_edit" value="<?=$this_client["profit_split"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="profit_split_edit_alert" class="alert" style="display:none;">Profit Split must be a number 0 - 100</span>
					</td>
				</tr>
				<tr>
					<td>Driver Status:</td>
					<td>
						<?php $options = array(
							'Applicant'  => 'Applicant',
							'Active'  => 'Active',
							'Pending Closure' => 'Pending Closure',
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
					<td>Dropdown Status:</td>
					<td>
						<?php $options = array(
							'Show'  => 'Show',
							'Hide'  => 'Hide',
							); 
						?>
						<?php echo form_dropdown('dropdown_status_edit',$options,$this_client['dropdown_status'],'id="dropdown_status_edit" class="main_content_dropdown"');?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td>Fleet Manager</td>
					<td>
						<?php echo form_dropdown('fleet_manager_edit',$fleet_manager_dropdown_options,$this_client["fleet_manager_id"],'id="fleet_manager_edit" class="main_content_dropdown"');?>
					</td>
					<td style="color:red; width:5px;">
					</td>
				</tr>
				<tr>
					<td style="width:300px;">First Name</td>
					<td>
						<input type="text" id="f_name_edit" name="f_name_edit" value="<?=$this_client["company"]["person"]["f_name"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
					<td>
						<span id="f_name_edit_alert" class="alert" style="display:none;">First Name must be entered</span>
					</td>
					
				</tr>
				<tr>
					<td>Last Name</td>
					<td>
						<input type="text" id="l_name_edit" name="l_name_edit" value="<?=$this_client["company"]["person"]["l_name"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
					<td>
						<span id="l_name_edit_alert" class="alert" style="display:none;">Last Name must be entered</span>
					</td>
				</tr>
				<tr>
					<td>Name on Fuel Card</td>
					<td>
						<input type="text" id="fuel_card_name_edit" name="fuel_card_name_edit" value="<?=$this_client["fuel_card_name"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td>Fuel Card Number</td>
					<td>
						<input type="text" id="fuel_card_number_edit" name="fuel_card_number_edit" value="<?=$this_client["fuel_card_number"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td>Ultimate Platinum Card #</td>
					<td>
						<input type="text" id="fuel_card_edit" name="fuel_card_edit" value="<?=$this_client["fuel_card_number"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td>Pay Card #</td>
					<td>
						<input type="text" id="pay_card_edit" name="pay_card_edit" value="<?=$this_client["pay_card_number"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td>Bigroad Username</td>
					<td>
						<input type="text" id="bigroad_username_edit" name="bigroad_username_edit" value="<?=$this_client["bigroad_username"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td>Bigroad Password</td>
					<td>
						<input type="text" id="bigroad_password_edit" name="bigroad_password_edit" value="<?=$this_client["bigroad_password"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td>Home Phone Number</td>
					<td>
						<input type="text" id="home_phone_num_edit" name="home_phone_num_edit" value="<?=$this_client["company"]["person"]["home_phone"]?>" class="main_content_input"  />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="phone_num_edit_alert" class="alert" style="display:none;">Phone Number must be a valid 10 digit number</span>
					</td>
				</tr>
				<tr>
					<td>Personal Phone Number</td>
					<td>
						<input type="text" id="phone_num_edit" name="phone_num_edit" value="<?=$this_client["company"]["person"]["phone_number"]?>" class="main_content_input"  />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="phone_num_edit_alert" class="alert" style="display:none;">Phone Number must be a valid 10 digit number</span>
					</td>
				</tr>
				<tr>
					<td>Phone Carrier</td>
					<td>
						<?php $options = array(
						'Select'  	=> 'Select',
						'Sprint'  	=> 'Sprint',
						'ATT'  	=> 'ATT',
						'T-mobile'  => 'T-mobile',
						'Virgin Mobile'  => 'Virgin Mobile',
						'Cingular'  => 'Cingular',
						'Verizon'  => 'Verizon',
						'Nextel'  => 'Nextel',
						'Boost'		=> 'Boost',
						'Cricket'	=> 'Cricket'
						); ?>
					<?php echo form_dropdown('phone_carrier_edit',$options,$this_client['company']["person"]["phone_carrier"],'id="phone_carrier_edit" class="main_content_dropdown"');?>
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="phone_carrier_edit_alert" class="alert" style="display:none;">Phone Carrier must be selected</span>
					</td>
				</tr>
				<tr>
					<td>Email</td>
					<td>
						<input type="text" id="email_edit" name="email_edit" value="<?=$this_client["company"]["person"]["email"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="email_edit_alert" class="alert" style="display:none;">Email must be entered and valid</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top">Home Address</td>
					<td>
						<textarea rows="3" id="home_address_edit" name="home_address_edit" class="main_content_input"><?=$this_client["company"]["person"]["home_address"]?></textarea>
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="home_address_edit_alert" class="alert" style="display:none;">Home Address must be entered and valid</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top">Home City</td>
					<td>
						<input type="text" id="home_city_edit" name="home_city_edit" class="main_content_input" value="<?=$this_client["company"]["person"]["home_city"]?>">
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top">Home State</td>
					<td>
						<input type="text" id="home_state_edit" name="home_state_edit" class="main_content_input" value="<?=$this_client["company"]["person"]["home_state"]?>">
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top">Home Zip Code</td>
					<td>
						<input type="text" id="home_zip_edit" name="home_zip_edit" class="main_content_input" value="<?=$this_client["company"]["person"]["home_zip"]?>">
					</td>
				</tr>
				<tr>
					<td>Date of Birth</td>
					<td>
						<?php 
							if (empty($this_client["end_date"]))
							{
								$date_of_birth =  null;
							} 
							else
							{
								$date_of_birth = date("m/d/Y",strtotime($this_client["user"]["person"]["date_of_birth"]));
							}
						?>
						<input type="text" id="dob_edit" name="dob_edit" value="<?=$date_of_birth?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="dob_edit_alert" class="alert" style="display:none;">Date of Birth must be entered and valid</span>
					</td>
				</tr>
				<tr>
					<td>License Number</td>
					<td>
						<input type="text" id="license_number_edit" name="license_number_edit" value="<?=$this_client["license_number"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="license_number_edit_alert" class="alert" style="display:none;">License Number must be entered and valid</span>
					</td>
				</tr>
				<tr>
					<td>License State</td>
					<td>
						<input type="text" id="license_state_edit" name="license_state_edit" value="<?=$this_client["license_state"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="license_state_edit_alert" class="alert" style="display:none;">License State must be a valid 2 digit abbreviation</span>
					</td>
				</tr>
				<tr>
					<td>License Expiration</td>
					<td>
						<?php 
							if (empty($this_client["end_date"]))
							{
								$license_expiration =  null;
							} 
							else
							{
								$license_expiration = date("m/d/Y",strtotime($this_client["license_expiration"]));
							}
						?>
						<input type="text" id="license_expiration_edit" name="license_expiration_edit" value="<?=$license_expiration?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="license_expiration_edit_alert" class="alert" style="display:none;">License Expiration must be entered and valid</span>
					</td>
				</tr>
				<tr>
					<td>CDL Since</td>
					<td>
						<?php 
							if (empty($this_client["cdl_since"]))
							{
								$cdl_since =  null;
							} 
							else
							{
								$cdl_since = date("m/d/Y",strtotime($this_client["cdl_since"]));
							}
						?>
						<input type="text" id="cdl_since_edit" name="cdl_since_edit" value="<?=$cdl_since?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="cdl_since_edit_alert" class="alert" style="display:none;">CDL Since must be entered and valid</span>
					</td>
				</tr>
				<tr>
					<td>Years of Experience</td>
					<td>
						<input type="text" id="years_of_experience_edit" name="years_of_experience_edit" value="<?=$this_client["years_of_experience"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td>Desired Company Name</td>
					<td>
						<input type="text" id="desired_company_name_edit" name="desired_company_name_edit" value="<?=$this_client["desired_company_name"]?>" class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td>SSN</td>
					<td>
						<input type="text" id="ssn_edit" name="ssn_edit" value="<?=$this_client["company"]["person"]["ssn"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="ssn_edit_alert" class="alert" style="display:none;">SSN must be entered and valid</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Start Date</td>
					<td>
						<?php 
							if (empty($this_client["end_date"]))
							{
								$start_date =  null;
							} 
							else
							{
								$start_date = date("m/d/Y",strtotime($this_client["start_date"]));
							}
						?>
						<input type="text" id="start_date_edit" name="start_date_edit" value="<?=$start_date?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="start_date_edit_alert" class="alert" style="display:none;">Start Date must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">End Date</td>
					<td>
						<?php 
							if (empty($this_client["end_date"]))
							{
								$end_date =  null;
							} 
							else
							{
								$end_date = date("m/d/Y",strtotime($this_client["end_date"]));
							}
						?>
						<input type="text" id="end_date_edit" name="end_date_edit" value="<?=$end_date?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="end_date_edit_alert" class="alert" style="display:none;">End Date must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">First Full Settlement Date</td>
					<td>
						<?php 
							if (empty($this_client["first_full_settlement_date"]))
							{
								$first_full_settlement_date = '';
							} 
							else
							{
								$first_full_settlement_date = date("m/d/Y",strtotime($this_client["first_full_settlement_date"]));
							}
						?>
						<input type="text" id="first_full_settlement_date_edit" name="first_full_settlement_date_edit" value="<?=$first_full_settlement_date?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td>Owner of Carrier</td>
					<td>
						<?php echo form_dropdown('carrier_edit',$carrier_options,$this_client['carrier_id'],'id="carrier_edit" class="main_content_dropdown"');?>
					</td>
				</tr>
				<tr>
					<td>Credit Score</td>
					<td>
						<input type="text" id="credit_score_edit" name="credit_score_edit" value="<?=$this_client["credit_score"]?>" class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td>Link to Credit Score</td>
					<td>
						<?php if(!empty($this_client["credit_score_guid"])): ?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["credit_score_guid"]?>" onclick="">Credit Score</a>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>Link to License</td>
					<td>
						<?php if(!empty($this_client["link_license"])): ?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["link_license"]?>" onclick="">License</a>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>Link to MVR</td>
					<td>
						<?php if(!empty($this_client["mvr_guid"])): ?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["mvr_guid"]?>" onclick="">MVR</a>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>Number of Violations</td>
					<td>
						<?php $options = array(
						'Select'  	=> 'Select',
						'0'  		=> '0',
						'1'  		=> '1',
						'2'  		=> '2',
						'3' 		=> '3',
						'4'  		=> '4',
						'5'  		=> '5+',
						); ?>
						<?php echo form_dropdown('num_of_violations_edit',$options,$this_client['number_of_violations'],'id="num_of_violations_edit" class="main_content_dropdown"');?>
					</td>
				</tr>
				<tr>
					<td>Link to Social Security Card</td>
					<td>
						<?php if(!empty($this_client["company"]["person"]["link_ss_card"])): ?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["company"]["person"]["link_ss_card"]?>" onclick="">Social Security Card</a>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>Link to Medical Card</td>
					<td>
						<?php if(!empty($this_client["medical_card_link"])): ?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["medical_card_link"]?>" onclick="">Medical Card</a>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>Link to Driver Application</td>
					<td>
						<?php if(!empty($this_client["driver_application_link"])): ?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["driver_application_link"]?>" onclick="">Driver Application</a>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>Link to Drug Test</td>
					<td>
						<?php if(!empty($this_client["drug_test_link"])): ?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["drug_test_link"]?>" onclick="">Drug Test</a>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>Service Contract</td>
					<td>
						<?php if(!empty($this_client["contract_guid"])): ?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$this_client["contract_guid"]?>" onclick="">Service Contract</a>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top">E Signature</td>
					<td style="vertical-align:top">
						<button type="button" onclick="load_signature_dialog('<?=$this_client["company"]["person"]["id"]?>')" class="jq_button" style="width:200px;">Upload Signatures</button>
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top">Personal Notes</td>
					<td>
						<textarea rows="3" id="person_notes_edit" name="person_notes_edit" class="main_content_input"><?=$this_client["company"]["person"]["person_notes"]?></textarea>
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="person_notes_edit_alert" class="alert" style="display:none;">Personal Notes must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top">Emergency Contact Name</td>
					<td>
						<input type="text" id="emergency_contact_name_edit" name="emergency_contact_name_edit" class="main_content_input"  value="<?=$this_client["company"]["person"]["emergency_contact_name"]?>"/>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top">Emergency Contact Number</td>
					<td>
						<input type="text" id="emergency_contact_phone_edit" name="emergency_contact_phone_edit" class="main_content_input"  value="<?=$this_client["company"]["person"]["emergency_contact_phone"]?>"/>
					</td>
				</tr>
			</table>
		</div>
		
		
		<div id="client_pay_structure"  style="margin:20px; display:none;">
			<span class="section_heading">Fleet Manager Pay Structure</span>
			<hr/>
			<br>
			<div id="revenue_based_settings" name="revenue_based_settings" style="">
				<table id="main_content_table">
					<tr style="font-weight:bold;">
						<td style=" min-width:25px;  max-width:25px;"></td>
						<td style="width:130px;">
							Owner
						</td>
						<td style='width:180px;'>
							Description
						</td>
						<td style='width:60px; text-align:right;'>
							Percent
						</td>
					</tr>
						
						<?php if(!empty($revenue_splits)): ?>
							<?php foreach($revenue_splits as $revenue_split): ?>
								<?php
									$rs_id = $revenue_split["id"];
									$owner_id = $revenue_split["owner_id"];
								
									
									//GET PAY ACCOUNT OPTIONS FOR THE CURRENT OWNERS
									$company_id = $owner_id;
									
									$where = null;
									$where["id"] = $company_id;
									$company = db_select_company($where);
									
									//GET CLIENT BILL ACCOUNT DROPDOWN OPTIONS
									$where = null;
									$where["company_id"] = $company_id;
									if($company["type"] == "Business")
									{
										$where["category"] = "Profit";
									}
									else if($company["type"] == "Fleet Manager")
									{
										$where["category"] = "Pay";
									}
									else
									{
										//$where["category"] = "Bill";
									}
									
									$bill_accounts = db_select_accounts($where);
									
									$account_dropdown_options = array();
									$account_dropdown_options["Select"] = "Select";
									foreach($bill_accounts as $account)
									{
										$account_dropdown_options[$account["id"]] = $account["account_name"];
									}
									
									
								?>
								<tr>
									<td style="overflow:hidden; min-width:25px;  max-width:25px;  cursor:default;"   VALIGN="top" ><img title="Delete" onclick="alert('You do not have permission to delete existing Revenue Splits!')" style="cursor:pointer; position:relative; top:5px;" src="/images/trash.png" height="15" /></td>
									<td style="width:130px;">
										<?php echo form_dropdown("rs_owner_$rs_id",$owner_options,$revenue_split["owner_type"],'id="rs_owner_'.$rs_id.'" onchange="get_pay_account_dropdown(this)" style="width:115px;"');?>
									</td>
									<td style='width:180px;'>
										<input type="text" id="rs_desc_<?=$rs_id?>" name="rs_desc_<?=$rs_id?>" value="<?=$revenue_split["description"]?>"  style="width:160px;" />
									</td>
									<td style='width:60px; text-align:right;'>
										<input type="text" id="rs_percentage_<?=$rs_id?>" name="rs_percentage_edit_<?=$rs_id?>" value="<?=$revenue_split["percent"]*100?>"  style="text-align:right; width:55px;" />
									</td>
									<td>
										<span id="rs_alert_<?=$revenue_split["id"]?>" class="alert" style="display:none; margin-left:10px;">Missing entries</span>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php for ($i = 1; $i <= 10; $i++): ?>
						<tr id="add_rs_row_<?=$i?>" style="display:none;">
							<td style="overflow:hidden; min-width:25px;  max-width:25px;  cursor:default;"   VALIGN="top" ><img title="Delete" onclick="$('#add_rs_row_<?=$i?>').hide(); $('#add_rs_percentage_<?=$i?>').val(''); " style="cursor:pointer; position:relative; top:5px;" src="/images/trash.png" height="15" /></td>
							<td style="">
								<?php echo form_dropdown("add_rs_owner_$i",$owner_options,"Select",'id="add_rs_owner_'.$i.'" style="width:115px;"');?>
							</td>
							<td style=''>
								<input type="text" id="add_rs_desc_<?=$i?>" name="add_rs_desc_<?=$i?>" value=""  style="width:160px;" />
							</td>
							<td style='text-align:right;'>
								<input type="text" id="add_rs_percentage_<?=$i?>" name="add_rs_percentage_<?=$i?>" value=""  style="text-align:right; width:55px;" />
							</td>
							<td>
								<span id="add_rs_alert_<?=$i?>" class="alert" style="display:none; margin-left:10px;">Missing entries</span>
							</td>
						</tr>
					<?php endfor; ?>
				</table>
				<span style="float:left; margin-left:25px;"><a href="javascript:void(0);" style="margin-right:20px;" onclick="add_revenue_split_row()" >+ Add</a></span>
				<br>
			</div>
		</div>
		<div id="client_fee_settings_edit" style="margin:20px; display:none;">
			<span class="section_heading">Client Fee Settings</span>
			
			<hr/>
			<br>
			<table id="main_content_table">
				<tr style="font-weight:bold;">
					<td>
					</td>
					<td>
						Name
					</td>
					<td>
						Amount
					</td>
					<td>
					</td>
					<td>
						Type
					</td>
					<td>
						% Tax
					</td>
					<td>
						Expense Account
					</td>
				</tr>
				<?php foreach($this_client["client_fee_settings"] as $setting): ?>
					<tr id="existing_setting_row_<?= $setting["id"]?>">
						<td style="overflow:hidden; min-width:25px;  max-width:25px;  cursor:default;"   VALIGN="top" ><img title="Delete" onclick="cannot_delete_fee_setting()" style="cursor:pointer; position:relative; top:5px;" src="/images/trash.png" height="15" /></td>
						<td style="width:275px;"><input type="text" id="fee_description_<?= $setting["id"]?>" name="fee_description_<?= $setting["id"]?>" value="<?=$setting["fee_description"]?>"  style="width:200;" /></td>
						<td>
							<input type="text" id="fee_amount_<?= $setting["id"]?>" name="fee_amount_<?= $setting["id"]?>" value="<?=$setting["fee_amount"]?>"  style="width:50; text-align:right; margin-right:27px;" />
						</td>
						<td style="width:40px; padding-bottom:6px; vertical-align: middle;">
							Per
						</td>
						<td style="width:160px">
							<?php $options = array(
								'Select'  	=> 'Select',
								'Map Mile'  	=> 'Map Mile',
								'Odometer Mile'  	=> 'Odometer Mile',
								'Day'  => 'Day',
								'Week'  => 'Week',
								'Month'  => 'Month',
								'Year'  => 'Year',
								'Fuel Allocation'  => 'Fuel Allocation',
								); 
							?>
							<?php echo form_dropdown("fee_type_".$setting["id"],$options,$setting['fee_type'],"id='fee_type_".$setting["id"]."'style='width:120px; margin-left:3px; margin-right:3px;'");?>
						</td>
						<td>
							<?php
								$client_id = $this_client["id"];
								
								if($client_id == "new")
								{
									$fee_tax = 0;
								}
								else
								{
									$fee_tax = $setting['fee_tax'];
								}
							?>
							<input type="text" id="fee_tax_<?= $setting["id"]?>" name="fee_tax_<?= $setting["id"]?>" value="<?=$fee_tax?>"  style="width:50; text-align:right; margin-right:27px;" />
						</td>
						<td>
							<?php
								if($client_id == "new")
								{
									$setting_account_id = "Select Account";
								}
								else
								{
									$setting_account_id = $setting['account_id'];
								}
							?>
							<?php echo form_dropdown("fee_account_".$setting["id"],$client_account_options,$setting_account_id,"id='fee_account_".$setting["id"]."'style='width:120px; margin-left:3px; margin-right:3px;'");?>
						</td>
						<td style="color:red; width:5px;">
							*
						</td>
						<td>
							<span id="fee_alert_<?= $setting["id"]?>" class="alert" style="display:none;">Missing entries</span>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php for ($i = 1; $i <= 10; $i++): ?>
					<tr id="add_fee_setting_row_<?=$i?>" style="display:none;">
						<td style="overflow:hidden; min-width:25px;  max-width:25px;  cursor:default;"   VALIGN="top" ><img title="Delete" onclick="delete_fee_setting_row('add_fee_setting_row_<?=$i?>','fee_description_add_<?= $i?>')" style="cursor:pointer; position:relative; top:5px;" src="/images/trash.png" height="15" /></td>
						<td style="width:300px;"><input type="text" id="fee_description_add_<?= $i?>" name="fee_description_add_<?= $i?>"  style="width:200;" /></td>
						<td>
							<input type="text" id="fee_amount_add_<?= $i?>" name="fee_amount_add_<?= $i?>" style="width:50; text-align:right; margin-right:27px;" />
						</td>
						<td style="width:40px; padding-bottom:6px; vertical-align: middle;">
							Per
						</td>
						<td style="width:160px">
							<?php $options = array(
								'Select'  	=> 'Select',
								'Map Mile'  	=> 'Map Mile',
								'Odometer Mile'  	=> 'Odometer Mile',
								'Day'  => 'Day',
								'Week'  => 'Week',
								'Month'  => 'Month',
								'Year'  => 'Year',
								'Fuel Allocation'  => 'Fuel Allocation',
								); 
							?>
							<?php echo form_dropdown("fee_type_add_".$i,$options,"Select","id='fee_type_add_".$i."'style='width:120px; margin-left:3px; margin-right:3px;'");?>
						</td>
						<td>
							<input type="text" id="fee_tax_add_<?= $i?>" name="fee_tax_add_<?= $i?>" style="width:50; text-align:right; margin-right:27px;" />
						</td>
						<td>
							<?php echo form_dropdown("fee_account_add_".$i,$client_account_options,$setting['account_id'],"id='fee_account_add_".$i."'style='width:120px; margin-left:3px; margin-right:3px;'");?>
						</td>
						<td>
						<td style="color:red; width:5px;">
							*
						</td>
						<td>
							<span id="fee_alert_add_<?= $i?>" class="alert" style="display:none;">Missing entries</span>
						</td>
					</tr>
				<?php endfor;?>
			</table>
			<span style="float:left; margin-left:25px;"><a href="javascript:void(0);" style="" onclick="add_fee_setting_link()" >+ Add</a></span>
			<br>
		</div>
		<div id="user_info_edit" style="margin:20px;">
			<span class="section_heading">User Info</span>
			<hr/>
			<br>
			<table id="main_content_table">
				<tr>
					<td style="width:300px;">Username</td>
					<td>
						<input type="text" id="username_edit" name="username_edit" value="<?=$this_client["user"]["username"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="username_edit_alert" class="alert" style="display:none;">Username must be entered</span>
						<span id="username_exists_edit_alert" class="alert" style="display:none;">This Username already exists in the system</span>
					</td>
				</tr>
				<tr>
					<td>Password</td>
					<td>
						<input type="text" id="password_edit" name="password_edit" value="<?=$this_client["user"]["password"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="password_edit_alert" class="alert" style="display:none;">Password must be entered</span>
					</td>
				</tr>
			</table>
		</div>
		
	</form>
</div>

<div id="upload_signature_dialog" title="Upload Signatures" style="display:none">
	<!-- AJAX GOES HERE !-->
</div>

<div id="upload_contract_dialog" title="Upload Contract" style="display:none">
	<!-- AJAX GOES HERE !-->
</div>