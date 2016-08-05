<script>
	$("#scrollable_content").height($("#body").height() - 155);
	
	//CREATE VENDORS ARRAY - FULL NAME
	var vendor_validation_list = [
	<?php 	
			$array_string = "";
			foreach($companies as $a_company)
			{
				$company_name = $a_company['company_name'];
				$array_string = $array_string.'"'.$company_name.'",';
			}
			echo substr($array_string,0,-1);
	?>];
	
	//CREATE VENDORS ARRAY - SHORT NAME
	var vendor_validation_list_short_name = [
	<?php 	
			$array_string = "";
			foreach($companies as $a_company)
			{
				$company_name = $a_company['company_side_bar_name'];
				$array_string = $array_string.'"'.$company_name.'",';
			}
			echo substr($array_string,0,-1);
	?>];
	
	
	//VALIDATE SAVE CUSTOMER EDIT
	function validate_broker()
	{
		//SET INITIAL VARIABLES
		var isvalid = true;
		
		//HIDE ALL PREVIOUS ALERTS
		$("#company_name_alert").hide();
		$("#company_short_name_alert").hide();
		$("#company_status_alert").hide();
		$("#address_alert").hide();
		$("#city_alert").hide();
		$("#state_alert").hide();
		$("#zip_alert").hide();
		$("#contact_alert").hide();
		$("#email_alert").hide();
		$("#phone_alert").hide();
		$("#fax_alert").hide();
		$("#email_alert").hide();

		//GET ALL FORM DATA
		var company_name = $("#company_name").val();
		var company_short_name = $("#company_short_name").val();
		var company_status = $("#company_status").val();
		var address = $("#address").val();
		var city = $("#city").val();
		var state = $("#state").val();
		var zip = $("#zip").val();
		var contact = $("#contact").val();
		var email = $("#email").val();
		var phone = $("#phone").val();
		var fax = $("#fax").val();
		var email = $("#email").val();
		
		var username = $("#username_edit").val();
		var password = $("#password_edit").val();
		
		
		if(!company_name)
		{
			$("#company_name"+"_alert").show();
			isvalid = false;
		}else
		{
		
			//IF THE CUSTOMERS NAME HASN'T BEEN CHANGED
			if (company_name != "<?=$company["company_name"]?>")
			{
				//DOES THE CUSTOMER ALREADY EXIST IN THE DB
				var vendor_found = false;
				for (var vendor in vendor_validation_list)
				{
					if(company_name == vendor_validation_list[vendor])
					{
						vendor_found = true;
						break;
					}
				}
				//IF THE BROKER ALREADY EXISTS IN THE DB
				if(vendor_found)
				{
					$("#vendor_exists"+"_alert").show();
					isvalid = false;
				}
			}
		}
		
		if(!company_short_name)
		{
			$("#company_short_name"+"_alert").show();
			isvalid = false;
		}
		else
		{
		
			//IF THE CUSTOMERS NAME HASN'T BEEN CHANGED
			if (company_short_name != "<?=$company["company_side_bar_name"]?>")
			{
				//DOES THE CUSTOMER ALREADY EXIST IN THE DB
				var vendor_found = false;
				for (var vendor in vendor_validation_list_short_name)
				{
					if(company_short_name == vendor_validation_list_short_name[vendor])
					{
						vendor_found = true;
						break;
					}
				}
				//IF THE BROKER ALREADY EXISTS IN THE DB
				if(vendor_found)
				{
					$("#short_name_exists"+"_alert").show();
					isvalid = false;
				}
			}
		}
		
		if (!company_name)
		{
			//$("#company_name"+"_alert").show();
			//isvalid = false;
		}
		
		if (company_status == 'Select')
		{
			$("#company_status"+"_alert").show();
			isvalid = false;
		}
		
		if (!address)
		{
			//$("#address"+"_alert").show();
			//isvalid = false;
		}
		
		if (!city)
		{
			//$("#city"+"_alert").show();
			//isvalid = false;
		}
		
		if (!state)
		{
			//$("#state"+"_alert").show();
			//isvalid = false;
		}
		
		if (!zip)
		{
			//$("#zip"+"_alert").show();
			//isvalid = false;
		}
		
		if (!contact)
		{
			//$("#contact"+"_alert").show();
			//isvalid = false;
		}
		
		if (phone)
		{
			phone = phone.replace(/[^0-9]/g, '');
			if(phone.length != 10)
			{
				//$("#phone"+"_alert").show();
				//isvalid = false;
			}else
			{
				//$("#phone").val(phone);
			}
		}
		else
		{
			//$("#phone"+"_alert").show();
			//isvalid = false;
		}
		
		//ONLY VALIDATES IF SOMETHING IS ENTERED
		if (fax)
		{
			fax = fax.replace(/[^0-9]/g, '');
			if(fax.length != 10)
			{
				$("#fax"+"_alert").show();
				isvalid = false;
			}else
			{
				$("#fax").val(fax);
			}
		}
		
		if (email)
		{
			if (!validate_email(email))
			{
				$("#email"+"_alert").show();
				isvalid = false;
			}
		}
		
		
		if(isvalid)
		{
			save_company();
			//alert('is valid');
		}
		
	}//END VALIDATE SAVE COMPANY

	//SAVE COMPANY
	function save_company()
	{
		//-------------- AJAX TO LOAD TRUCK DETAILS -------------------
			// GET THE DIV IN DIALOG BOX
			var main_content = $('#main_content');
			
			//POST DATA TO PASS BACK TO CONTROLLER
			var dataString = $("#save_company_form").serialize();
			// AJAX!
			$.ajax({
				url: "<?= base_url("index.php/people/save_broker")?>", // in the quotation marks
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
	
	
	
	
</script>


<div id="main_content_header">
	<span style="float:left; font-weight:bold;"><?=$company["company_name"]?></span>
	<img src="<?=base_url("images/save.png")?>" style="margin-top:4px;cursor:pointer;float:right;height:20px;" id="save_staff" onclick="validate_broker()"/>
	<img src="<?=base_url("images/back.png")?>" style="margin-top:4px;cursor:pointer;float:right;height:20px;margin-right:15px;" id="cancel_edit_staff" onclick="load_broker_details('<?=$company["id"]?>')"/>
	<img src="<?=base_url("images/loading.gif")?>" style="display:none;margin-top:4px;cursor:pointer;float:right;height:20px;" id="loading_icon"/>
</div>
<div id="scrollable_content"  class="scrollable_div">
	<div style="margin:20px;">
		<?php $attributes = array('id' => 'save_company_form'); ?>
		<?=form_open('vendors/save_vendor',$attributes)?>
		<?=form_hidden('id',$company['id'])?>
		<?php $text_box_style = "width:200px; margin-left:2px;" ?>
		<table id="main_content_table" style="font-size:14px;">
			<tr>
				<td style="width:300px;">Company Name</td>
				<td>
					<?= form_input('company_name',$company['company_name'],'id="company_name" style="'.$text_box_style.'"')?>
				</td>
				<td style="color:red; width:5px;">
					*
				</td>
				<td id="company_name_alert" class="alert">A Company Name must be entered</td>
				<td id="vendor_exists_alert" class="alert">This company already exists in the system</td>
			</tr>
			<tr>
				<td style="width:300px;">Short Name</td>
				<td>
					<?= form_input('company_short_name',$company['company_side_bar_name'],'id="company_short_name" style="'.$text_box_style.'"')?>
				</td>
				<td style="color:red; width:5px;">
					*
				</td>
				<td id="company_short_name_alert" class="alert">A Short Name must be entered</td>
				<td id="short_name_exists_alert" class="alert">This short name already exists in the system</td>
			</tr>
			<tr>
				<td>Status</td>
				<td>
					<?php $options = array(
						'Select'  => 'Select',
						'Active'  => 'Active',
						'Inactive' => 'Inactive',
						'Pending Closure' => 'Pending Closure',
						); 
					?>
					<?php echo form_dropdown('company_status',$options,$company['company_status'],'id="company_status" class="main_content_dropdown"');?>
				</td>
				<td style="color:red; width:5px;">
					*
				</td>
				<td id="company_status_alert" class="alert">Status must be selected</td>
			</tr>
			<tr>
				<td style="width:300px;">Address</td>
				<td>
					<?php $data = array(
					  'name'        => "address",
					  'id'          => "address",
					  'rows'		=> '3',
					  'style'		=> 'margin-left:2px; width:200px;',
					  'value'		=> $company["address"],
					);?>
					<?=form_textarea($data);?>
				</td>
				<td style="color:red; width:5px;">
					
				</td>
				<td id="address_alert" class="alert">An Address must be entered</td>
			</tr>
			<tr>
				<td style="width:300px;">City</td>
				<td>
					<?= form_input('city',$company['city'],'id="city" style="'.$text_box_style.'"')?>
				</td>
				<td style="color:red; width:5px;">
					
				</td>
				<td id="city_alert" class="alert">A City must be entered</td>
			</tr>
			<tr>
				<td style="width:300px;">State</td>
				<td>
					<?= form_input('state',$company['state'],'id="state" style="'.$text_box_style.'"')?>
				</td>
				<td style="color:red; width:5px;">
					
				</td>
				<td id="state_alert" class="alert">A State must be entered</td>
			</tr>
			<tr>
				<td style="width:300px;">Zip Code</td>
				<td>
					<?= form_input('zip',$company['zip'],'id="zip" style="'.$text_box_style.'"')?>
				</td>
				<td style="color:red; width:5px;">
					
				</td>
				<td id="zip_alert" class="alert">A Zip must be entered</td>
			</tr>
			<tr>
				<td style="width:300px;">Contact</td>
				<td>
					<?= form_input('contact',$company['contact'],'id="contact" style="'.$text_box_style.'"')?>
				</td>
				<td style="color:red; width:5px;">
					
				</td>
				<td id="contact_alert" class="alert">A Contact must be entered</td>
			</tr>
			<tr>
				<td style="width:300px;">Email</td>
				<td>
					<?= form_input('email',$company['company_email'],'id="email" style="'.$text_box_style.'"')?>
				</td>
				<td style="color:red; width:5px;">
				</td>
				<td id="email_alert" class="alert">Email must be a valid email address</td>
			</tr>
			<tr>
				<td style="width:300px;">Phone Number</td>
				<td>
					<?= form_input('phone',$company['company_phone'],'id="phone" style="'.$text_box_style.'"')?>
				</td>
				<td style="color:red; width:5px;">
					
				</td>
				<td id="phone_alert" class="alert">Phone Number must be a valid 10 digit number</td>
			</tr>
			<tr>
				<td style="width:300px;">Fax Number</td>
				<td>
					<?= form_input('fax',$company['company_fax'],'id="fax" style="'.$text_box_style.'"')?>
				</td>
				<td style="color:red; width:5px;">
				</td>
				<td id="fax_alert" class="alert">Fax Number must be a valid 10 digit number</td>
			</tr>
			<tr>
				<td style="width:300px;">Notes</td>
				<td>
					<?php $data = array(
					  'name'        => "notes",
					  'id'          => "notes",
					  'rows'		=> '3',
					  'style'		=> 'margin-left:2px; width:200px;',
					  'value'		=> $company["company_notes"],
					);?>
					<?=form_textarea($data);?>
				</td>
				<td style="color:red; width:5px;">
				</td>
				<td id="notes_alert" class="alert">Notes must be entered</td>
			</tr>
		</table>
		
		<div id="broker_specific" style="margin-bottom:20px; margin-top:20px;">
			<div class="section_heading" style="height:30px;">
				Broker Specific Info
			</div>
			<hr/>
			<br>
			<table id="main_content_table" style="font-size:14px;">
				<tr>
					<td style="width:300px;">MC Number</td>
					<td>
						<?= form_input('mc_number',$customer['mc_number'],'id="mc_number" class="main_content_dropdown"')?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Broker Status</td>
					<td>
						<?php $options = array(
										'Good'  	=> 'Good',
										'Bad'  => 'Bad',
										); ?>
						<?php echo form_dropdown('status',$options,$customer["status"],'id="status" class="main_content_dropdown"');?>
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Form of Payment</td>
					<td>
						<?php $options = array(
										'Select'  	=> 'Select',
										'Factor'  	=> 'Factor',
										'Bill Direct'  => 'Bill Direct',
										); ?>
						<?php echo form_dropdown('form_of_payment',$options,$customer["form_of_payment"],'id="form_of_payment" class="main_content_dropdown"');?>
					</td>
				</tr>
			</table>
		</div>
		</form>
	</div>
</div>


