<html>
	<!-- 	TODO: 								!-->
	<!-- 	SEARCH FUNCTION 					!-->
	<!-- 	ACTIVE/INACTIVE FILTER BOX			!-->
	<!-- 	FLEET MANAGER FILTER BOX			!-->
	<head>
		<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
		<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
		<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
		<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
		<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>

		<script type="text/javascript">
		$(document).ready(function(){
			
			//SET HEIGHT OF MAIN CONTENT AND SCROLLABLE CONTENT
			//alert($("#body").height());
			$("#main_content").height($("#body").height() - 115);
			$("#scrollable_content").height($("#body").height() - 155);
			$("#client_list_div").height($("#body").height() - 307);
			//alert($("#main_content").height());
			
			//PLACE DATE PICKERS ON ALL THE DATE BOXES
			$('#dob_edit').datepicker({ showAnim: 'blind' });
			$('#license_expiration_edit').datepicker({ showAnim: 'blind' });
			$('#cdl_since_edit').datepicker({ showAnim: 'blind' });
			$('#ucr_edit').datepicker({ showAnim: 'blind' });
			$('#running_since_edit').datepicker({ showAnim: 'blind' });
			$('#start_date_edit').datepicker({ showAnim: 'blind' });
			$('#end_date_edit').datepicker({ showAnim: 'blind' });
			
			
			
			
			//HANDLE BUTTONS
			
			$("#edit_client").click(function()
			{
				window.location = "<?= base_url('index.php/clients/index/edit/'.$this_client['id']);?>"
			});
			
			$("#new_client").click(function()
			{
				window.location = "<?= base_url('index.php/clients/index/edit/new');?>"
			});
		
			//HIDE ADD FEE SETTINGS 1-5
			for (i=1;i<=10;i++)
			{
				$("#add_fee_setting_row_"+i).hide();
			}
		
			//JAVASCRIPT FOR "ADD FEE SETTING" LINK
			var last_visible_fee = 1;
			$("#add_fee_setting_link").click(function()
			{
				for (i=0;i<=10;i++)
				{
					if(last_visible_fee == i)
					{
						$("#add_fee_setting_row_"+i).show();
						last_visible_fee = i+1;
						var height = $('#scrollable_content')[0].scrollHeight;
						$('#scrollable_content').scrollTop(height);
						break;
					}
					
					if(last_visible_fee == 11)
					{
						alert("You can only add 10 additional rows before saving!");
						break;
					}
				}
			});
			
			
			
		});//end document ready
		
		function delete_fee_setting_row(row_id,fee_description_id)
		{
			$("#"+row_id).hide();
			$("#"+fee_description_id).val("");
		}
		
		function cannot_delete_fee_setting()
		{
			alert("You do not have permission to delete existing fee settings");
		}
		
		//UPDATE LIST
		function load_client_list()
		{
			//LOADING ICON IN THE EQUIPMENT LIST DIV
			
			
			//-------------- AJAX TO LOAD TRUCK LIST ---------
			// GET THE DIV IN DIALOG BOX
			var client_list_div = $('#client_list_div');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var driver_status = $("#driver_type_dropdown").val();
			var dataString = "&driver_type_dropdown="+driver_status;
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/clients/client_status_selected")?>", // in the quotation marks
				type: "POST",
				data: dataString,
				cache: false,
				context: client_list_div, // use a jquery object to select the result div in the view
				statusCode: {
					200: function(response){
						// Success!
						client_list_div.html(response);
						
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
		}//end load_truck_list
		
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
			$('#f_name_edit_alert').hide();
			$('#l_name_edit_alert').hide();
			$('#username_edit_alert').hide();
			$('#username_exists_edit_alert').hide();
			$('#password_edit_alert').hide();
			$('#phone_num_edit_alert').hide();
			$('#phone_carrier_edit_alert').hide();
			$('#email_edit_alert').hide();
			$('#home_address_edit_alert').hide();
			$('#dob_edit_alert').hide();
			$('#license_number_edit_alert').hide();
			$('#license_state_edit_alert').hide();
			$('#license_expiration_edit_alert').hide();
			$('#cdl_since_edit_alert').hide();
			$('#ssn_edit_alert').hide();
			$('#person_notes_edit_alert').hide();
			
			$('#company_name_edit_alert').hide();
			$('#company_side_bar_name_edit_alert').hide();
			$('#fein_edit_alert').hide();
			$('#mc_edit_alert').hide();
			$('#dot_edit_alert').hide();
			$('#insurance_company_edit_alert').hide();
			$('#policy_number_edit_alert').hide();
			$('#company_phone_edit_alert').hide();
			$('#company_fax_edit_alert').hide();
			$('#company_gmail_edit_alert').hide();
			$('#gmail_password_edit_alert').hide();
			$('#google_voice_edit_alert').hide();
			$('#address_edit_alert').hide();
			$('#city_edit_alert').hide();
			$('#state_edit_alert').hide();
			$('#zip_edit_alert').hide();
			$('#ucr_edit_alert').hide();
			$('#running_since_edit_alert').hide();
			$('#start_date_edit_alert').hide();
			$('#end_date_edit_alert').hide();
			<?php foreach($client_fee_settings as $setting): ?>
				$('#fee_alert_<?= $setting["id"]?>').hide();
			<?php endforeach; ?>
			<?php for ($i = 1; $i <= 10; $i++): ?>
				$('#fee_alert_add_<?= $i?>').hide();
			<?php endfor; ?>
			
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
			var ssn = $('#ssn_edit').val();
			var person_notes = $('#person_notes_edit').val();
			
			var company_name = $('#company_name_edit').val();
			var company_side_bar_name = $('#company_side_bar_name_edit').val();
			var fein = $('#fein_edit').val();
			var mc = $('#mc_edit').val();
			var dot = $('#dot_edit').val();
			var insurance_company = $('#insurance_company_edit').val();
			var policy_number = $('#policy_number_edit').val();
			var address = $('#address_edit').val();
			var city = $('#city_edit').val();
			var state = $('#state_edit').val();
			var zip = $('#zip_edit').val();
			var ucr = $('#ucr_edit').val();
			var running_since = $('#running_since_edit').val();
			var start_date = $('#start_date_edit').val();
			var end_date = $('#end_date_edit').val();
			<?php foreach($client_fee_settings as $setting): ?>
				var fee_description_<?=$setting["id"]?> = $('#fee_description_<?= $setting["id"]?>').val();
				var fee_amount_<?=$setting["id"]?> = $('#fee_amount_<?= $setting["id"]?>').val();
				var fee_type_<?=$setting["id"]?> = $('#fee_type_<?= $setting["id"]?>').val();
				var fee_tax_<?=$setting["id"]?> = $('#fee_tax_<?= $setting["id"]?>').val();
			<?php endforeach; ?>
			<?php for ($i = 1; $i <= 10; $i++): ?>
				var fee_description_add_<?=$i?> = $('#fee_description_add_<?=$i?>').val();
				var fee_amount_add_<?=$i?> = $('#fee_amount_add_<?=$i?>').val();
				var fee_type_add_<?=$i?> = $('#fee_type_add_<?=$i?>').val();
				var fee_tax_add_<?=$i?> = $('#fee_tax_add_<?=$i?>').val();
			<?php endfor; ?>
			if (!short_name || short_name.length > 17)
			{
				$('#short_name_edit_alert').show();
				valid_input = false;
			}
			
			if(!f_name)
			{
				$('#f_name_edit_alert').show();
				valid_input = false;
			}
			
			if(!l_name)
			{
				$('#l_name_edit_alert').show();
				valid_input = false;
			}
			
			if (!username)
			{
				$('#username_edit_alert').show();
				valid_input = false;
			}
			else
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
			}
			
			if (password == '')
			{
				$('#password_edit_alert').show();
				valid_input = false;
			}
			
			if (!phone_num)
			{
				$('#phone_num_edit_alert').show();
				valid_input = false;
			}
			else
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
			}
			
			if (phone_carrier == 'Select')
			{
				$('#phone_carrier_edit_alert').show();
				valid_input = false;
			}
			
			if (email == '')
			{
				$('#email_edit_alert').show();
				valid_input = false;
			}
			else if(!validate_email(email))
			{
				$('#email_edit_alert').show();
				valid_input = false;
			}
			
			if (!isDate(dob))
			{
				$('#dob_edit_alert').show();
				valid_input = false;
			}
			
			if (!license_number)
			{
				$('#license_number_edit_alert').show();
				valid_input = false;
			}
			
			if (!license_state || license_state.length != 2)
			{
				$('#license_state_edit_alert').show();
				valid_input = false;
			}
			
			if (!isDate(license_expiration))
			{
				$('#license_expiration_edit_alert').show();
				valid_input = false;
			}
			
			if (!isDate(cdl_since))
			{
				$('#cdl_since_edit_alert').show();
				valid_input = false;
			}
			
			if (!company_name)
			{
				$('#company_name_edit_alert').show();
				valid_input = false;
			}
			
			if (!company_side_bar_name || company_side_bar_name.length > 17)
			{
				$('#company_side_bar_name_edit_alert').show();
				valid_input = false;
			}
			
			//if (!fein)
			//{
			//	$('#fein_edit_alert').show();
			//	valid_input = false;
			//}
			
			//if (!mc)
			//{
			//	$('#mc_edit_alert').show();
			//	valid_input = false;
			//}
			
			//if (!dot)
			//{
			//	$('#dot_edit_alert').show();
			//	valid_input = false;
			//}
			
			//if (!address)
			//{
			//	$('#address_edit_alert').show();
			//	valid_input = false;
			//}
			
			//if (!city)
			//{
			//	$('#city_edit_alert').show();
			//	valid_input = false;
			//}
			
			//if (!state || state.length != 2)
			//{
			//	$('#state_edit_alert').show();
			//	valid_input = false;
			//}
			
			//if (!zip)
			//{
			//	$('#zip_edit_alert').show();
			//	valid_input = false;
			//}
			
			//if (!running_since)
			//{
			//	$('#running_since_edit_alert').show();
			//	valid_input = false;
			//}
			
			if (!start_date)
			{
				$('#start_date_edit_alert').show();
				valid_input = false;
			}
			
			<?php foreach($client_fee_settings as $setting): ?>
				if($("#existing_setting_row_<?= $setting["id"]?>").is(":visible"))
				{
					if (!fee_description_<?= $setting["id"]?>)
					{
						$('#fee_alert_<?= $setting["id"]?>').show();
						valid_input = false;
					}
					
					if (!fee_amount_<?= $setting["id"]?> || isNaN(fee_amount_<?= $setting["id"]?> ))
					{
						$('#fee_alert_<?= $setting["id"]?>').show();
						valid_input = false;
					}
					
					if (fee_type_<?= $setting["id"]?> == "Select")
					{
						$('#fee_alert_<?= $setting["id"]?>').show();
						valid_input = false;
					}
					
					if (!fee_tax_<?= $setting["id"]?> || isNaN(fee_tax_<?= $setting["id"]?> ))
					{
						alert("problem");
						$('#fee_alert_<?= $setting["id"]?>').show();
						valid_input = false;
					}
					
				}
				
			<?php endforeach; ?>
			<?php for ($i = 1; $i <= 10; $i++): ?>
				if($("#add_fee_setting_row_<?=$i?>").is(":visible"))
				{
					if (!fee_description_add_<?=$i?> )
					{
						$('#fee_alert_add_<?=$i?>').show();
						valid_input = false;
					}
					
					if (!fee_amount_add_<?=$i?> || isNaN(fee_amount_add_<?=$i?> ))
					{
						$('#fee_alert_add_<?=$i?>').show();
						valid_input = false;
					}
					
					if (fee_type_add_<?=$i?> == "Select")
					{
						$('#fee_alert_add_<?=$i?>').show();
						valid_input = false;
					}
					
					if (!fee_tax_add_<?=$i?> || isNaN(fee_tax_add_<?=$i?> ))
					{
						$('#fee_alert_add_<?=$i?>').show();
						valid_input = false;
					}
					
				}
			<?php endfor; ?>
			
			
			//IF EVERY INPUT IS VALID FOR SUBMISSION
			if(valid_input)
			{
				//alert('save');
				$("#save_driver_form").submit();
			}
		}//end validate_and_save_client()
		
		</script>

		<title><?php echo $title;?></title>
	</head>
	
	<body id="body">

		<?php include("main_menu.php"); ?>
		
		<div id="main_div">
			<div id="space_header">
			</div>
			
			<div id="left_bar">
				<button class='left_bar_button jq_button' id="new_client">New Client</button>
				<br>
				<br>
				<span class="heading">Search</span>
				<hr/>
				<input type="text" id="client_search" name="client_search"  class="left_bar_input"/>
				<br>
				<br>
				<span class="heading">Clients</span>
				<hr/>
				<?php $attributes = array('name'=>'driver_type_form','ID'=>'driver_type_form' )?>
				<!-- //OPEN FORM HERE !-->
						<?php $options = array(
							'all'=> 'All',
							'Main Driver'  => 'Main Driver',
							'Co-driver'    => 'Co-driver',
							'Pending Closure' => 'Pending Closure',
							'Closed' => 'Closed',
							); 
						?>
					<?php echo form_dropdown('driver_type_dropdown',$options,"Main Driver",'id="driver_type_dropdown" onchange="load_client_list()" class="left_bar_input"');?>
				<!-- </form> !-->
				<br>
				<div id="client_list_div" class="scrollable_div" style="overflow-y: auto; overflow-x: hidden; width: 155px;">
					<?php foreach ($all_clients as $client): ?>
						<?php $selected = ""; ?>
						<?php 
							if($client_id == $client["id"])
							{
								$selected = " font-weight:bold;";//background: #DCDCDC;" 
							}
						?>
						
						<div class="left_bar_link_div" style="with:145px; <?=$selected?> " onclick="location.href='<?= base_url("index.php/clients/index/details/".$client['id']);?>'">
							<?=$client["client_nickname"]?>
						</div>
					<?php endforeach; ?>
				</div>
				
			</div>
			
			<?php if ($view == 'details'):?>
				<div id="main_content">
					<div id="main_content_header">
						<span style="font-weight:bold;"><?=$this_client["company"]["company_name"] ?></span>
						<button class='jq_button' style="float:right;  width:80px;" id="edit_client">Edit</button>
					</div>
					<div id="scrollable_content" class="scrollable_div">
						<div id="personal_info" style="margin:20px;">
							<span class="section_heading">Personal Info</span>
							<hr/>
							<br>
							<table id="main_content_table" style="margin-top:6px;">
								<tr>
									<td style="width:300px;">Client Short Name</td>
									<td>
										<?=$this_client["client_nickname"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Client Status</td>
									<td>
										<?=$this_client["client_status"];?>
									</td>
								</tr>								
								<tr>
									<td style="width:300px;">Fleet Manager</td>
									<td>
										<?=$this_client["fleet_manager"]["full_name"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">First Name</td>
									<td>
										<?=$this_client["company"]["person"]["f_name"];?>
									</td>
								</tr>
								<tr>
									<td>Last Name</td>
									<td>
										<?= $this_client["company"]["person"]["l_name"];?>
									</td>
								</tr>
								<tr>
									<td>Username</td>
									<td>
										<?= $this_client["user"]["username"];?>
									</td>
								</tr>
								<tr>
									<td>Password</td>
									<td>
										<?= $this_client["user"]["password"];?>
									</td>
								</tr>
								<tr>
									<td>Fuel Card #</td>
									<td>
										<?= $this_client["fuel_card_number"];?>
									</td>
								</tr>
								<tr>
									<td>Pay Card #</td>
									<td>
										<?= $this_client["pay_card_number"];?>
									</td>
								</tr>
								<tr>
									<td>Personal Phone Number</td>
									<td>
										<?= $this_client["company"]["person"]["phone_number"];?>
									</td>
								</tr>
								<tr>
									<td>Phone Carrier</td>
									<td>
										<?= $this_client["company"]["person"]["phone_carrier"];?>
									</td>
								</tr>
								<tr>
									<td>Email</td>
									<td>
										<?= $this_client["company"]["person"]["email"];?>
									</td>
								</tr>
								<tr>
									<td>Home Address</td>
									<td>
										<?= $this_client["company"]["person"]["home_address"];?>
									</td>
								</tr>
								<tr>
									<td>Date of Birth</td>
									<td>
										<?= date("n/j/Y",strtotime($this_client["company"]["person"]["date_of_birth"]));?>
									</td>
								</tr>
								<tr>
									<td>Link to License</td>
									<td>
										<?php if(!empty($this_client["company"]["person"]["link_license"])): ?>
											<a href="<?= $this_client["company"]["person"]["link_license"];?>" target="_blank">License</a>
										<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td>License Number</td>
									<td>
										<?= $this_client["license_number"];?>
									</td>
								</tr>
								<tr>
									<td>License State</td>
									<td>
										<?= $this_client["license_state"];?>
									</td>
								</tr>
								<tr>
									<td>License Expiration</td>
									<td>
										<?= date("n/j/Y",strtotime($this_client["license_expiration"]));?>
									</td>
								</tr>
								<tr>
									<td>CDL Since</td>
									<td>
										<?= date("n/j/Y",strtotime($this_client["cdl_since"]));?>
									</td>
								</tr>
								<tr>
									<td>Link to Social Security Card</td>
									<td>
										<?php if(!empty($this_client["company"]["person"]["link_ss_card"])): ?>
											<a href="<?= $this_client["company"]["person"]["link_ss_card"];?>" target="_blank">View Social Security Card</a>
										<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td>SSN</td>
									<td>
										<?= $this_client["company"]["person"]["ssn"];?>
									</td>
								</tr>
								<tr>
									<td>Link to Service Contract</td>
									<td>
										<?php if(!empty($this_client["link_contract"])): ?>
											<a href="<?= $this_client["link_contract"];?>" target="_blank">Service Contract</a>
										<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Start Date</td>
									<td>
										<?=date("n/j/Y",strtotime($this_client["start_date"]));?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">End Date</td>
									<td>
										<?=date("n/j/Y",strtotime($this_client["end_date"]));?>
									</td>
								</tr>
								<tr>
									<td>Personal Notes</td>
									<td>
										<?= $this_client["company"]["person"]["person_notes"];?>
									</td>
								</tr>
							</table>
						</div>
						<div id="company_info"  style="margin:20px;">
							<span class="section_heading">Company Info</span>
							<hr/>
							<br>
							<table id="main_content_table">
								<tr>
									<td style="width:300px;">Company Status</td>
									<td>
										<?=$this_client["company"]["company_status"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Company Name</td>
									<td>
										<?=$this_client["company"]["company_name"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Company Side-bar Name</td>
									<td>
										<?=$this_client["company"]["company_side_bar_name"];?>
									</td>
								</tr>
								<tr>
									<td>Link to FEIN</td>
									<td>
										<?php if(!empty($this_client["company"]["link_ein_letter"])): ?>
											<a href="<?= $this_client["company"]["link_ein_letter"];?>" target="_blank">FEIN Letter</a>
										<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">FEIN</td>
									<td>
										<?=$this_client["company"]["fein"];?>
									</td>
								</tr>
								<tr>
									<td>Link to MC Letter</td>
									<td>
										<?php if(!empty($this_client["company"]["link_mc_letter"])): ?>
											<a href="<?= $this_client["company"]["link_mc_letter"];?>" target="_blank">MC Letter</a>
										<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">MC Number</td>
									<td>
										<?=$this_client["mc_number"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">DOT Number</td>
									<td>
										<?=$this_client["dot_number"];?>
									</td>
								</tr>
								<tr>
									<td>Link to Docket PIN Letter</td>
									<td>
										<?php if(!empty($this_client["company"]["link_docket_pin_letter"])): ?>
											<a href="<?= $this_client["company"]["link_docket_pin_letter"];?>" target="_blank">Docket PIN Letter</a>
										<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Docket PIN</td>
									<td>
										<?=$this_client["company"]["docket_pin"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">USDOT PIN</td>
									<td>
										<?=$this_client["company"]["usdot_pin"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Access ID (UT)</td>
									<td>
										<?=$this_client["company"]["access_id"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Entity Number</td>
									<td>
										<?=$this_client["company"]["entity_number"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Factor Loads Login</td>
									<td>
										<?=$this_client["company"]["fl_username"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Factor Loads Password</td>
									<td>
										<?=$this_client["company"]["fl_password"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Insurance Company</td>
									<td>
										<?=$this_client["insurance_company"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Policy Number</td>
									<td>
										<?=$this_client["policy_number"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Company Phone</td>
									<td>
										<?=$this_client["company"]["company_phone"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Company K7 Fax</td>
									<td>
										<?=$this_client["company"]["company_fax"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Company Gmail</td>
									<td>
										<?=$this_client["company_gmail"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Gmail Password</td>
									<td>
										<?=$this_client["gmail_password"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Company Address</td>
									<td>
										<?=$this_client["company"]["address"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">City</td>
									<td>
										<?=$this_client["company"]["city"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">State</td>
									<td>
										<?=$this_client["company"]["state"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Zip Code</td>
									<td>
										<?=$this_client["company"]["zip"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Company Mailing Address</td>
									<td>
										<?=$this_client["company"]["mailing_address"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Oregon Permit</td>
									<td>
										<?=$this_client["oregon_permit"];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">UCR Renewal Date</td>
									<td>
										<?=date("n/j/Y",strtotime($this_client["ucr_renewal_date"]));?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Running Since</td>
									<td>
										<?=date("n/j/Y",strtotime($this_client["running_since"]));?>
									</td>
								</tr>
								<tr>
									<td>Link to Articles of Organization</td>
									<td>
										<?php if(!empty($this_client["company"]["link_aoo"])): ?>
											<a href="<?= $this_client["company"]["link_aoo"];?>" target="_blank">Articles of Organization</a>
										<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Company Notes</td>
									<td>
										<?=$this_client["company"]["company_notes"];?>
									</td>
								</tr>
							</table>
						</div>
						<div id="client_fee_settings"  style="margin:20px;">
							<span class="section_heading">Client Fee Settings</span>
							<hr/>
							<br>
							<table id="main_content_table">
								<tr style="font-weight:bold;">
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
									<tr>
										<td style="width:300px;">
											<?=$setting["fee_description"] ?>
										</td>
										<td style="width:80px; margin-right:27px;">
											<span><?=$setting["fee_amount"]?></span>
										</td>
										<td style="width:40px;">
											Per
										</td>
										<td style='width:160px;'>
											<span><?=$setting["fee_type"]?></span>
										</td>
										<td style='width:90px;'>
											<span><?=$setting["fee_tax"]?></span> %
										</td>
										<td style='width:90px;'>
											<span><?=$setting["account"]["account_name"]?></span>
										</td>
									</tr>
								<?php endforeach; ?>
							</table>
						</div>
					</div>
				</div>
			<?php endif;?>
			
			<?php if ($view == 'edit'):?>
				<div id="main_content">
					<div id="main_content_header">
						<?php
							if (empty($this_client["id"]))
							{
								$content_header_text = "New Client";
							}
							else
							{
								$content_header_text = $this_client["company"]["company_name"]." (Edit)";
							}
						?>
						<span style="font-weight:bold;"><?=$content_header_text?></span>
						<button class="jq_button" style="float:right; width:80px; margin-left:20px;" onclick="validate_and_save_client()">Save</button>
						<button class="jq_button" style="float:right; width:80px; margin-left:20px;" onclick="history.go(-1)">Cancel</button>
					</div>
					<div id="scrollable_content" class="scrollable_div">
						<?php $attributes = array('id' => 'save_driver_form'); ?>
						<?=form_open('clients/save_client',$attributes)?>
						<?=form_hidden('client_id',$this_client['id'])?>
						<?=form_hidden('company_id',$this_client['company']["id"])?>
						<?=form_hidden('user_id',$this_client['user']["id"])?>
						<?=form_hidden('person_id',$this_client['user']["person_id"])?>
						<div id="personal_info_edit" style="margin:20px;">
							<span class="section_heading">Personal Info</span>
							<hr/>
							<br>
							<table id="main_content_table">
								<tr>
									<td style="width:300px;">Client Short Name</td>
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
									<td>Client Status:</td>
									<td>
										<?php $options = array(
											'Select'  => 'Select',
											'Main Driver'  => 'Main Driver',
											'Co-driver'    => 'Co-driver',
											'Pending Closure' => 'Pending Closure',
											'Closed' => 'Closed',
											); 
										?>
										<?php echo form_dropdown('client_status_edit',$options,$this_client['client_status'],' class="main_content_dropdown"');?>
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
										*
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
									<td>Username</td>
									<td>
										<input type="text" id="username_edit" name="username_edit" value="<?=$this_client["user"]["username"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
										*
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
										*
									</td>
									<td>
										<span id="password_edit_alert" class="alert" style="display:none;">Password must be entered</span>
									</td>
								</tr>
								<tr>
									<td>Fuel Card #</td>
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
									<td>Personal Phone Number</td>
									<td>
										<input type="text" id="phone_num_edit" name="phone_num_edit" value="<?=$this_client["company"]["person"]["phone_number"]?>" class="main_content_input"  />
									</td>
									<td style="color:red; width:5px;">
										*
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
										'AT&T'  	=> 'AT&T',
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
										*
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
										*
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
										*
									</td>
									<td>
										<span id="dob_edit_alert" class="alert" style="display:none;">Date of Birth must be entered and valid</span>
									</td>
								</tr>
								<tr>
									<td>Link to License</td>
									<td>
										<input type="text" id="link_license_edit" name="link_license_edit" value="<?=$this_client["company"]["person"]["link_license"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="ssn_edit_alert" class="alert" style="display:none;">SSN must be entered and valid</span>
									</td>
								</tr>
								<tr>
									<td>License Number</td>
									<td>
										<input type="text" id="license_number_edit" name="license_number_edit" value="<?=$this_client["license_number"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
										*
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
										*
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
										*
									</td>
									<td>
										<span id="license_expiration_edit_alert" class="alert" style="display:none;">License Expiration must be entered and valid</span>
									</td>
								</tr>
								<tr>
									<td>CDL Since</td>
									<td>
										<?php 
											if (empty($this_client["end_date"]))
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
										*
									</td>
									<td>
										<span id="cdl_since_edit_alert" class="alert" style="display:none;">CDL Since must be entered and valid</span>
									</td>
								</tr>
								<tr>
									<td>Link to Social Security Card</td>
									<td>
										<input type="text" id="link_ssn_edit" name="link_ssn_edit" value="<?=$this_client["company"]["person"]["link_ss_card"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="ssn_edit_alert" class="alert" style="display:none;">SSN must be entered and valid</span>
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
									<td>Link to Service Contract</td>
									<td>
										<input type="text" id="link_contract_edit" name="link_contract_edit" value="<?=$this_client["link_contract"]?>"  class="main_content_input" />
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
										*
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
							</table>
						</div>
						<div id="company_info_edit" style="margin:20px;">
							<span class="section_heading">Company Info</span>
							<hr/>
							<br>
							<table id="main_content_table">
								<tr>
									<td>Company Status</td>
									<td>
										<?php $options = array(
											'Pending Setup'  	=> 'Pending Setup',
											'Active'  	=> 'Active',
											'Inactive'  => 'Inactive',
											); ?>
										<?php echo form_dropdown('company_status_edit',$options,$this_client["company"]['company_status'],'id="company_status_edit" class="main_content_dropdown"');?>
									</td>
									<td style="color:red; width:5px;">
										*
									</td>
								</tr>								
								<tr>
									<td style="width:300px;">Company Name</td>
									<td>
										<input type="text" id="company_name_edit" name="company_name_edit" value="<?=$this_client["company"]["company_name"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
										*
									</td>
									<td>
										<span id="company_name_edit_alert" class="alert" style="display:none;">Company Name must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Company Side-Bar Name</td>
									<td>
										<input type="text" id="company_side_bar_name_edit" name="company_side_bar_name_edit" value="<?=$this_client["company"]["company_side_bar_name"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
										*
									</td>
									<td>
										<span id="company_side_bar_name_edit_alert" class="alert" style="display:none;">Side-Bar Name must be entered and 17 characters or less</span>
									</td>
								</tr>
								<tr>
									<td>Link to FEIN</td>
									<td>
										<input type="text" id="link_ein_edit" name="link_ein_edit" value="<?=$this_client["company"]["link_ein_letter"]?>"  class="main_content_input" />
									</td>
								</tr>
								<tr>
									<td style="width:300px;">FEIN</td>
									<td>
										<input type="text" id="fein_edit" name="fein_edit" value="<?=$this_client["company"]["fein"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="fein_edit_alert" class="alert" style="display:none;">FEIN must be entered</span>
									</td>
								</tr>
								<tr>
									<td>Link to MC Letter</td>
									<td>
										<input type="text" id="link_mc_edit" name="link_mc_edit" value="<?=$this_client["company"]["link_mc_letter"]?>"  class="main_content_input" />
									</td>
								</tr>
								<tr>
									<td style="width:300px;">MC Number</td>
									<td>
										<input type="text" id="mc_edit" name="mc_edit" value="<?=$this_client["mc_number"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="mc_edit_alert" class="alert" style="display:none;">MC Number must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">DOT Number</td>
									<td>
										<input type="text" id="dot_edit" name="dot_edit" value="<?=$this_client["dot_number"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="dot_edit_alert" class="alert" style="display:none;">DOT Number must be entered</span>
									</td>
								</tr>
								<tr>
									<td>Link to Docket PIN Letter</td>
									<td>
										<input type="text" id="link_docket_pin_edit" name="link_docket_pin_edit" value="<?=$this_client["company"]["link_docket_pin_letter"]?>"  class="main_content_input" />
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Docket PIN</td>
									<td>
										<input type="text" id="docket_pin_edit" name="docket_pin_edit" value="<?=$this_client["company"]["docket_pin"]?>"  class="main_content_input" />
									</td>
								</tr>
								<tr>
									<td style="width:300px;">USDOT PIN</td>
									<td>
										<input type="text" id="usdot_pin_edit" name="usdot_pin_edit" value="<?=$this_client["company"]["usdot_pin"]?>"  class="main_content_input" />
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Access ID (UT)</td>
									<td>
										<input type="text" id="access_id_edit" name="access_id_edit" value="<?=$this_client["company"]["access_id"]?>"  class="main_content_input" />
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Entity Number</td>
									<td>
										<input type="text" id="entity_number_edit" name="entity_number_edit" value="<?=$this_client["company"]["entity_number"]?>"  class="main_content_input" />
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Factor Loads Login</td>
									<td>
										<input type="text" id="fl_username_edit" name="fl_username_edit" value="<?=$this_client["company"]["fl_username"]?>"  class="main_content_input" />
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Factor Loads Password</td>
									<td>
										<input type="text" id="fl_password_edit" name="fl_password_edit" value="<?=$this_client["company"]["fl_password"]?>"  class="main_content_input" />
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Insurance Company</td>
									<td>
										<input type="text" id="insurance_company_edit" name="insurance_company_edit" value="<?=$this_client["insurance_company"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="insurance_company_edit_alert" class="alert" style="display:none;">Insurance Company must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Policy Number</td>
									<td>
										<input type="text" id="policy_number_edit" name="policy_number_edit" value="<?=$this_client["policy_number"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="policy_number_edit_alert" class="alert" style="display:none;">Policy Number must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Company Phone</td>
									<td>
										<input type="text" id="company_phone_edit" name="company_phone_edit" value="<?=$this_client["company"]["company_phone"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="company_phone_edit_alert" class="alert" style="display:none;">Company Phone must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Company K7 Fax</td>
									<td>
										<input type="text" id="company_fax_edit" name="company_fax_edit" value="<?=$this_client["company"]["company_fax"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="company_fax_edit_alert" class="alert" style="display:none;">Company Fax must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Company Gmail</td>
									<td>
										<input type="text" id="company_gmail_edit" name="company_gmail_edit" value="<?=$this_client["company_gmail"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="company_gmail_edit_alert" class="alert" style="display:none;">Company Gmail must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Gmail Password</td>
									<td>
										<input type="text" id="gmail_password_edit" name="gmail_password_edit" value="<?=$this_client["gmail_password"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="gmail_password_edit_alert" class="alert" style="display:none;">Gmail Password must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="vertical-align:top">Company Address</td>
									<td>
										<textarea rows="3" id="address_edit" name="address_edit" class="main_content_input"><?=$this_client["company"]["address"]?></textarea>
									</td>
									<td style="color:red; width:5px; vertical-align:top;">
									</td>
									<td style="vertical-align:top;">
										<span id="address_edit_alert" class="alert" style="display:none;">Address must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">City</td>
									<td>
										<input type="text" id="city_edit" name="city_edit" value="<?=$this_client["company"]["city"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="city_edit_alert" class="alert" style="display:none;">City must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">State</td>
									<td>
										<input type="text" id="state_edit" name="state_edit" value="<?=$this_client["company"]["state"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="state_edit_alert" class="alert" style="display:none;">State must be a valid 2 digit abbreviation</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Zip Code</td>
									<td>
										<input type="text" id="zip_edit" name="zip_edit" value="<?=$this_client["company"]["zip"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="zip_edit_alert" class="alert" style="display:none;">Zip Code must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="vertical-align:top">Company Mailing Address</td>
									<td>
										<textarea rows="3" id="mailing_address_edit" name="mailing_address_edit" class="main_content_input"><?=$this_client["company"]["mailing_address"]?></textarea>
									</td>
									<td style="color:red; width:5px; vertical-align:top;">
									</td>
									<td style="vertical-align:top;">
										<span id="address_edit_alert" class="alert" style="display:none;">Address must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Oregon Permit</td>
									<td>
										<input type="text" id="oregon_permit_edit" name="oregon_permit_edit" value="<?=$this_client["oregon_permit"]?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td>
										<span id="oregon_permit_edit_alert" class="alert" style="display:none;">Oregon Permit must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">UCR Renewal Date</td>
									<td>
										<?php 
											if (empty($this_client["end_date"]))
											{
												$ucr_renewal_date =  null;
											} 
											else
											{
												$ucr_renewal_date = date("m/d/Y",strtotime($this_client["end_date"]));
											}
										?>
										<input type="text" id="ucr_edit" name="ucr_edit" value="<?=$ucr_renewal_date?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="ucr_edit_alert" class="alert" style="display:none;">UCR Renewal Date must be entered</span>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Running Since</td>
									<td>
										<?php 
											if (empty($this_client["end_date"]))
											{
												$running_since =  null;
											} 
											else
											{
												$running_since = date("m/d/Y",strtotime($this_client["running_since"]));
											}
										?>
										<input type="text" id="running_since_edit" name="running_since_edit" value="<?=$running_since?>"  class="main_content_input" />
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="running_since_edit_alert" class="alert" style="display:none;">Running Since must be entered</span>
									</td>
								</tr>
								<tr>
									<td>Link to Articles of Organization</td>
									<td>
										<input type="text" id="link_aoo_edit" name="link_aoo_edit" value="<?=$this_client["company"]["link_aoo"]?>"  class="main_content_input" />
									</td>
								</tr>
								<tr>
									<td style="vertical-align:top">Company Notes</td>
									<td>
										<textarea rows="3" id="company_notes_edit" name="company_notes_edit" class="main_content_input"><?=$this_client["company"]["company_notes"]?></textarea>
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td>
										<span id="company_notes_edit_alert" class="alert" style="display:none;">Company Notes must be entered</span>
									</td>
								</tr>
								
							</table>
						</div>
						<div id="client_fee_settings_edit" style="margin:20px;">
							<span class="section_heading">Client Fee Settings</span>
							<span style="float:right;"><a href="javascript:void(0);" style="margin-right:20px;" id="add_fee_setting_link" >+ Add</a></span>
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
								<?php foreach($client_fee_settings as $setting): ?>
									<tr id="existing_setting_row_<?= $setting["id"]?>">
										<td style="overflow:hidden; min-width:25px;  max-width:25px;  cursor:default;"   VALIGN="top" ><img title="Delete" onclick="cannot_delete_fee_setting()" style="cursor:pointer; position:relative; top:5px;" src="/images/cancel_icon.png" height="17" width="17" /></td>
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
									<tr id="add_fee_setting_row_<?=$i?>">
										<td style="overflow:hidden; min-width:25px;  max-width:25px;  cursor:default;"   VALIGN="top" ><img title="Delete" onclick="delete_fee_setting_row('add_fee_setting_row_<?=$i?>','fee_description_add_<?= $i?>')" style="cursor:pointer; position:relative; top:5px;" src="/images/cancel_icon.png" height="17" width="17" /></td>
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
							
						</div>
						</form>
					</div>
				</div>
			<?php endif;?>
		</div>
		
					
		
	</body>
	
</html>