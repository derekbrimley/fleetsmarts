<script>
	$("#scrollable_content").height($("#body").height() - 155);
	
	//PLACE DATE PICKERS ON ALL THE DATE BOXES
	$('#ucr_edit').datepicker({ showAnim: 'blind' });
	$('#running_since_edit').datepicker({ showAnim: 'blind' });
	
	//DIALOG: UPLOAD SIGNATURE DIALOG
	$( "#upload_packet_dialog" ).dialog(
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
					$("#upload_packet_form").submit();
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
	
	function load_packet_dialog(company_id)
	{
		
		//-------------- AJAX -------------------
		// GET THE DIV IN DIALOG BOX
		var target_div = $('#upload_packet_dialog');
		target_div.html('<div style="width:25px; margin:auto;"><img id="loading_icon" name="loading_icon" src="/images/loading.gif" style="width:25px; height:25px; margin-top:50px;" /></div>');
		$('#upload_packet_dialog').dialog('open')
		
		//POST DATA TO PASS BACK TO CONTROLLER
		var dataString = 'company_id='+company_id;
		
		// AJAX!
		$.ajax({
			url: "<?= base_url("index.php/people/load_packet_div")?>", // in the quotation marks
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
	
	//VALIDATE AND SAVE CARRIER
	function validate_and_save_carrier()
	{
		
		var valid_input = true;
		
		$('#company_name_edit_alert').hide();
		$('#company_side_bar_name_edit_alert').hide();
		$('#company_phone_edit_alert').hide();
		$('#company_fax_edit_alert').hide();
		$('#company_gmail_edit_alert').hide();
		$('#gmail_password_edit_alert').hide();
		$('#google_voice_edit_alert').hide();
		$('#running_since_edit_alert').hide();
		$('#start_date_edit_alert').hide();
		$('#end_date_edit_alert').hide();
		
		var company_name = $('#company_name_edit').val();
		var company_side_bar_name = $('#company_side_bar_name_edit').val();
		var running_since = $('#running_since_edit').val();
		var gmail = $('#company_gmail_edit').val();
		var gmail_password = $('#gmail_password_edit').val();
		
		if (!company_name)
		{
			$('#company_name_edit_alert').show();
			valid_input = false;
		}
		
		if (!company_side_bar_name)
		{
			$('#company_side_bar_name_edit_alert').show();
			valid_input = false;
		}
		
		if (running_since)
		{
			if (!isDate(running_since))
			{
				$('#running_since_edit_alert').show();
				valid_input = false;
			}
		}
		
		if(gmail)
		{
			if(!validate_email(gmail))
			{
				$('#company_gmail_edit_alert').show();
				valid_input = false;
			}
			
			if(!gmail_password)
			{
				$('#gmail_password_edit_alert').show();
				valid_input = false;
			}
		}
		
		
		//IF EVERY INPUT IS VALID FOR SUBMISSION
		if(valid_input)
		{
			//alert('save');
			
			//BUILD DATA STRING TO PASS TO CONTROLLER
			var dataString = "";
			
			$("#save_carrier_form select").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#save_carrier_form input").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			$("#save_carrier_form textarea").each(function() {
				//alert(this.id);
				dataString = dataString+"&"+this.id+"="+this.value;
			});
			
			//alert(dataString);
			
			//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var main_content = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/people/save_carrier")?>", // in the quotation marks
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
			alert("You missed something! Scroll up to fix it!")
		}
		
	}//end validate_and_save_client()
	
</script>

<div id="main_content_header">
	<?php
		if (empty($carrier["id"]))
		{
			$content_header_text = "New Client";
		}
		else
		{
			$content_header_text = $carrier["company_name"]." (Edit)";
		}
	?>
	<span style="font-weight:bold;"><?=$content_header_text?></span>
	<img src="<?=base_url("images/save.png")?>" style="margin-top:4px;cursor:pointer;float:right;height:20px;" id="save_carrier" onclick="validate_and_save_carrier()"/>
	<img src="<?=base_url("images/back.png")?>" style="margin-top:4px;cursor:pointer;float:right;height:20px;margin-right:15px;" id="cancel_edit_carrier" onclick="load_carrier_details('<?=$carrier["id"]?>')"/>
	<img src="<?=base_url("images/loading.gif")?>" style="display:none;margin-top:4px;cursor:pointer;float:right;height:20px;" id="loading_icon"/>
</div>
<div id="scrollable_content" class="scrollable_div">
	<?php $attributes = array('id' => 'save_carrier_form'); ?>
	<?=form_open('people/save_carrier',$attributes)?>
	<input type="hidden" id="carrier_id" name="carrier_id" value="<?=$carrier["id"]?>" />
		<div id="company_info_edit" style="margin:20px;">
			<table id="main_content_table">
				<tr>
					<td style="width:300px;vertical-align:middle;">Carrier Name</td>
					<td style="vertical-align:middle">
						<input type="text" id="company_name_edit" name="company_name_edit" value="<?=$carrier["company_name"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
					<td>
						<span id="company_name_edit_alert" class="alert" style="display:none;">Company Name must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle;">Carrier Side-Bar Name</td>
					<td style="vertical-align:middle">
						<input type="text" id="company_side_bar_name_edit" name="company_side_bar_name_edit" value="<?=$carrier["company_side_bar_name"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
					<td>
						<span id="company_side_bar_name_edit_alert" class="alert" style="display:none;">Side-Bar Name must be entered and 17 characters or less</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle">Carrier Status</td>
					<td style="vertical-align:middle">
						<?php $options = array(
							'Pending Setup'  	=> 'Pending Setup',
							'Active'  	=> 'Active',
							'Inactive'  => 'Inactive',
							); ?>
						<?php echo form_dropdown('company_status_edit',$options,$carrier['company_status'],'id="company_status_edit" class="main_content_dropdown"');?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle">Carrier DBA</td>
					<td style="vertical-align:middle">
						<input type="text" id="company_dba_edit" name="company_dba_edit" value="<?=$carrier["dba"]?>" class="main_content_input"/>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle">Carrier Packet</td>
					<td style="vertical-align:middle">
						<button type="button" onclick="load_packet_dialog('<?=$carrier["id"]?>')" class="jq_button" style="width:200px;">Upload Packet</button>
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle;">FEIN</td>
					<td style="vertical-align:middle">
						<input type="text" id="fein_edit" name="fein_edit" value="<?=$carrier["fein"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="fein_edit_alert" class="alert" style="display:none;">FEIN must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">MC Number</td>
					<td style="vertical-align:middle">
						<input type="text" id="mc_edit" name="mc_edit" value="<?=$carrier["mc_number"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="mc_edit_alert" class="alert" style="display:none;">MC Number must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">DOT Number</td>
					<td style="vertical-align:middle">
						<input type="text" id="dot_edit" name="dot_edit" value="<?=$carrier["dot_number"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="dot_edit_alert" class="alert" style="display:none;">DOT Number must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Docket PIN</td>
					<td style="vertical-align:middle">
						<input type="text" id="docket_pin_edit" name="docket_pin_edit" value="<?=$carrier["docket_pin"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">USDOT PIN</td>
					<td style="vertical-align:middle">
						<input type="text" id="usdot_pin_edit" name="usdot_pin_edit" value="<?=$carrier["usdot_pin"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Access ID (UT)</td>
					<td style="vertical-align:middle">
						<input type="text" id="access_id_edit" name="access_id_edit" value="<?=$carrier["access_id"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Entity Number</td>
					<td style="vertical-align:middle">
						<input type="text" id="entity_number_edit" name="entity_number_edit" value="<?=$carrier["entity_number"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Factor Loads Login</td>
					<td style="vertical-align:middle">
						<input type="text" id="fl_username_edit" name="fl_username_edit" value="<?=$carrier["fl_username"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Factor Loads Password</td>
					<td style="vertical-align:middle">
						<input type="text" id="fl_password_edit" name="fl_password_edit" value="<?=$carrier["fl_password"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Insurance Company</td>
					<td style="vertical-align:middle">
						<input type="text" id="insurance_company_edit" name="insurance_company_edit" value="<?=$carrier["insurance_company"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="insurance_company_edit_alert" class="alert" style="display:none;">Insurance Company must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Policy Number</td>
					<td style="vertical-align:middle">
						<input type="text" id="policy_number_edit" name="policy_number_edit" value="<?=$carrier["policy_number"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="policy_number_edit_alert" class="alert" style="display:none;">Policy Number must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Carrier Phone</td>
					<td style="vertical-align:middle">
						<input type="text" id="company_phone_edit" name="company_phone_edit" value="<?=$carrier["company_phone"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="company_phone_edit_alert" class="alert" style="display:none;">Company Phone must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Carrier Fax</td>
					<td style="vertical-align:middle">
						<input type="text" id="company_fax_edit" name="company_fax_edit" value="<?=$carrier["company_fax"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="company_fax_edit_alert" class="alert" style="display:none;">Company Fax must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Carrier Gmail</td>
					<td style="vertical-align:middle">
						<input type="text" id="company_gmail_edit" name="company_gmail_edit" value="<?=$carrier["company_gmail"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="company_gmail_edit_alert" class="alert" style="display:none;">Company Gmail must be valid</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Gmail Password</td>
					<td style="vertical-align:middle">
						<input type="text" id="gmail_password_edit" name="gmail_password_edit" value="<?=$carrier["gmail_password"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td>
						<span id="gmail_password_edit_alert" class="alert" style="display:none;">Gmail Password must be entered if Gmail account exists</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle">Carrier Address</td>
					<td style="vertical-align:middle">
						<textarea rows="3" id="address_edit" name="address_edit" class="main_content_input"><?=$carrier["address"]?></textarea>
					</td>
					<td style="color:red; width:5px; vertical-align:middle;">
					</td>
					<td style="vertical-align:middle;">
						<span id="address_edit_alert" class="alert" style="display:none;">Address must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">City</td>
					<td style="vertical-align:middle;">
						<input type="text" id="city_edit" name="city_edit" value="<?=$carrier["city"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td style="vertical-align:middle;">
						<span id="city_edit_alert" class="alert" style="display:none;">City must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">State</td>
					<td style="vertical-align:middle;">
						<input type="text" id="state_edit" name="state_edit" value="<?=$carrier["state"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td style="vertical-align:middle;">
						<span id="state_edit_alert" class="alert" style="display:none;">State must be a valid 2 digit abbreviation</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Zip Code</td>
					<td style="vertical-align:middle;">
						<input type="text" id="zip_edit" name="zip_edit" value="<?=$carrier["zip"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td style="vertical-align:middle;">
						<span id="zip_edit_alert" class="alert" style="display:none;">Zip Code must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle">Carrier Mailing Address</td>
					<td style="vertical-align:middle;">
						<textarea rows="3" id="mailing_address_edit" name="mailing_address_edit" class="main_content_input"><?=$carrier["mailing_address"]?></textarea>
					</td>
					<td style="color:red; width:5px; vertical-align:middle;">
					</td>
					<td style="vertical-align:middle;">
						<span id="address_edit_alert" class="alert" style="display:none;">Address must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle">Carrier Mailing Address</td>
					<td style="vertical-align:middle;">
						<textarea rows="3" id="mailing_address_edit" name="mailing_address_edit" class="main_content_input"><?=$carrier["mailing_address"]?></textarea>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle">Carrier Mailing City</td>
					<td style="vertical-align:middle;">
						<input type="text" id="mailing_city_edit" name="mailing_city_edit" class="main_content_input" value="<?=$carrier["mailing_city"]?>">
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle">Carrier Mailing State</td>
					<td style="vertical-align:middle;">
						<input type="text" id="mailing_state_edit" name="mailing_state_edit" class="main_content_input" value="<?=$carrier["mailing_state"]?>">
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle">Carrier Mailing Zip Code</td>
					<td style="vertical-align:middle;">
						<input type="text" id="mailing_zip_edit" name="mailing_zip_edit" class="main_content_input" value="<?=$carrier["mailing_zip"]?>">
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Oregon Permit</td>
					<td style="vertical-align:middle;">
						<input type="text" id="oregon_permit_edit" name="oregon_permit_edit" value="<?=$carrier["oregon_permit"]?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
						
					</td>
					<td style="vertical-align:middle;">
						<span id="oregon_permit_edit_alert" class="alert" style="display:none;">Oregon Permit must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">UCR Renewal Date</td>
					<td style="vertical-align:middle;">
						<?php 
							if (empty($carrier["ucr_renewal_date"]))
							{
								$ucr_renewal_date =  null;
							} 
							else
							{
								$ucr_renewal_date = date("m/d/Y",strtotime($carrier["ucr_renewal_date"]));
							}
						?>
						<input type="text" id="ucr_edit" name="ucr_edit" value="<?=$ucr_renewal_date?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td style="vertical-align:middle;">
						<span id="ucr_edit_alert" class="alert" style="display:none;">UCR Renewal Date must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="width:300px;vertical-align:middle">Running Since</td>
					<td style="vertical-align:middle;">
						<?php 
							if (empty($carrier["running_since"]))
							{
								$running_since =  null;
							} 
							else
							{
								$running_since = date("m/d/Y",strtotime($carrier["running_since"]));
							}
						?>
						<input type="text" id="running_since_edit" name="running_since_edit" value="<?=$running_since?>"  class="main_content_input" />
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td style="vertical-align:middle;">
						<span id="running_since_edit_alert" class="alert" style="display:none;">Running Since must be entered</span>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle;">Link to OSBR Info</td>
					<td style="vertical-align:middle;">
						<input type="text" id="link_osbr_edit" name="link_osbr_edit" value="<?=$carrier["link_osbr"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle;">Link to FEIN</td>
					<td style="vertical-align:middle;">
						<input type="text" id="link_ein_edit" name="link_ein_edit" value="<?=$carrier["link_ein_letter"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle;">Link to MC Letter</td>
					<td style="vertical-align:middle;">
						<input type="text" id="link_mc_edit" name="link_mc_edit" value="<?=$carrier["link_mc_letter"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle;">Link to USDOT PIN Letter</td>
					<td style="vertical-align:middle;">
						<input type="text" id="link_usdot_pin_edit" name="link_usdot_pin_edit" value="<?=$carrier["link_usdot_pin_letter"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle;">Link to Docket PIN Letter</td>
					<td style="vertical-align:middle;">
						<input type="text" id="link_docket_pin_edit" name="link_docket_pin_edit" value="<?=$carrier["link_docket_pin_letter"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle;">Link to Articles of Organization</td>
					<td style="vertical-align:middle;">
						<input type="text" id="link_aoo_edit" name="link_aoo_edit" value="<?=$carrier["link_aoo"]?>"  class="main_content_input" />
					</td>
				</tr>
				<tr>
					<td style="vertical-align:middle">Carrier Notes</td>
					<td style="vertical-align:middle;">
						<textarea rows="3" id="company_notes_edit" name="company_notes_edit" class="main_content_input"><?=$carrier["company_notes"]?></textarea>
					</td>
					<td style="color:red; width:5px;">
					</td>
					<td style="vertical-align:middle;">
						<span id="company_notes_edit_alert" class="alert" style="display:none;">Company Notes must be entered</span>
					</td>
				</tr>
				
			</table>
		</div>
	</form>
</div>


<div id="upload_packet_dialog" title="Upload Packet" style="display:none">
	<!-- AJAX GOES HERE !-->
</div>