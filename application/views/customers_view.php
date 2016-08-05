<html>

	<head>
		<title><?php echo $title;?></title>
		
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
			$("#main_content").height($("#body").height() - 115);
			$("#scrollable_content").height($("#body").height() - 155);
			$("#customer_list_div").height($("#body").height() - 295);
			//alert($("#main_content").height());
			
			$("#new_customer").button().click(function()
			{
				$("#add_customer_dialog").dialog("open");
			});
			
			$("#edit_customer").button().click(function()
			{
				window.location = "<?= base_url("index.php/customers/index/$customer_status/edit/".$this_customer['id']);?>"
			});
			
			$("#cancel_edit_customer").button().click(function()
			{
				window.location = "<?= base_url("index.php/customers/index/$customer_status/view/".$this_customer['id']);?>"
			});
			
			$("#save_customer").button().click(function()
			{
				validate_save_customer();
			});//END save_customer button
			
			
			
			//DIALOG: ADD NEW CUSTOMER
			$( "#add_customer_dialog" ).dialog(
			{
					autoOpen: false,
					height: 580,
					width: 425,
					modal: true,
					buttons: 
						[
							{
								text: "Add customer",
								click: function() 
								{
											

									//VALIDATE ADD NEW CUSTOMER
									validate_add_new_customer();
									
								},//end add load
							},
							{
								text: "Cancel",
								click: function() 
								{
									//RESIZE DIALOG BOX
									$( this ).dialog( "close" );
									
									//RESET ALL FIELDS IN DIALOG FORM
									$("#add_customer_name").val(null);
									$("#add_form_of_payment").val("Select");
									$("#add_address").val(null);
									$("#add_city").val(null);
									$("#add_state").val(null);
									$("#add_status").val("Good");
									$("#add_zip").val(null);
									$("#add_contact").val(null);
									$("#add_phone").val(null);
									$("#add_fax").val(null);
									$("#add_email").val(null);
									$("#add_mc_number").val(null);
									$("#add_notes").val(null);
								}
							}
						],//end of buttons
					
					open: function()
						{
							//HIDE ALL PREVIOUS ALERTS
							$("#add_customer_name_alert").hide();
							$("#add_customer_exists_alert").hide();
							$("#add_address_alert").hide();
							$("#add_city_alert").hide();
							$("#add_state_alert").hide();
							$("#add_zip_alert").hide();
							$("#add_contact_alert").hide();
							$("#add_phone_alert").hide();
							$("#add_fax_alert").hide();
							$("#add_email_alert").hide();
							$("#add_mc_number_alert").hide();
							$("#add_form_of_payment_alert").hide();
						
							//RESET HEIGHT OF DIALOG BOX
							$("#add_customer_dialog").height(480);
							
						},//end open function
					close: function() 
						{
							//HIDE ALL PREVIOUS ALERTS
							$("#add_customer_name_alert").hide();
							$("#add_customer_exists_alert").hide();
							$("#add_address_alert").hide();
							$("#add_city_alert").hide();
							$("#add_state_alert").hide();
							$("#add_zip_alert").hide();
							$("#add_contact_alert").hide();
							$("#add_phone_alert").hide();
							$("#add_fax_alert").hide();
							$("#add_email_alert").hide();
							$("#add_mc_number_alert").hide();
							$("#add_form_of_payment_alert").hide();
							
							//RESET HEIGHT OF DIALOG BOX
							$("#add_customer_dialog").height(480);
							
							
						}
			});//end dialog form
			
		});//END DOCUMENT READY
		
		//CREATE CUSTOMERS ARRAY
		var customer_validation_list = [
		<?php 	
				$array_string = "";
				foreach($all_customers as $customer)
				{
					$customer_name = $customer['customer_name'];
					$array_string = $array_string.'"'.$customer_name.'",';
				}
				echo substr($array_string,0,-1);
		?>];
		
		//VALIDATE ADD NEW CUSTOMER DIALOG FORM
		function validate_add_new_customer()
		{
			//SET INITIAL VARIABLES
			var this_dialog = $("#add_customer_dialog");
			var alert_height = 15;
			var isvalid = true;
			
			//HIDE ALL PREVIOUS ALERTS
			$("#add_customer_name_alert").hide();
			$("#add_customer_exists_alert").hide();
			$("#add_address_alert").hide();
			$("#add_city_alert").hide();
			$("#add_state_alert").hide();
			$("#add_zip_alert").hide();
			$("#add_contact_alert").hide();
			$("#add_phone_alert").hide();
			$("#add_fax_alert").hide();
			$("#add_email_alert").hide();
			$("#add_mc_number_alert").hide();
			$("#add_form_of_payment_alert").hide();

			//RESET HEIGHT OF DIALOG BOX
			$("#add_customer_dialog").height(480);
			
			//GET ALL FORM DATA
			var add_customer_name = $("#add_customer_name").val();
			var add_address = $("#add_address").val();
			var add_city = $("#add_city").val();
			var add_state = $("#add_state").val();
			var add_zip = $("#add_zip").val();
			var add_contact = $("#add_contact").val();
			var add_phone = $("#add_phone").val();
			var add_fax = $("#add_fax").val();
			var add_email = $("#add_email").val();
			var add_mc_number = $("#add_mc_number").val();
			var add_form_of_payment = $("#add_form_of_payment").val();
			
			
			if(!add_customer_name)
			{
				$("#add_customer_dialog").height(this_dialog.height() + alert_height);
				$("#add_customer_name"+"_alert").show();
				isvalid = false;
			}else
			{
				//DOES THE CUSTOMER ALREADY EXIST IN THE DB
				var customer_found = false;
				for (var customer in customer_validation_list)
				{
					if(add_customer_name == customer_validation_list[customer])
					{
						customer_found = true;
						break;
					}
				}
				//IF THE BROKER ALREADY EXISTS IN THE DB
				if(customer_found)
				{
					$("#add_customer_dialog").height(this_dialog.height() + alert_height);
					$("#add_customer_exists"+"_alert").show();
					isvalid = false;
				}
			}
			
			if (!add_customer_name)
			{
				$("#add_customer_dialog").height(this_dialog.height() + alert_height);
				$("#add_customer_name"+"_alert").show();
				isvalid = false;
			}
			
			if (!add_address)
			{
				$("#add_customer_dialog").height(this_dialog.height() + alert_height);
				$("#add_address"+"_alert").show();
				isvalid = false;
			}
			
			if (!add_city)
			{
				$("#add_customer_dialog").height(this_dialog.height() + alert_height);
				$("#add_city"+"_alert").show();
				isvalid = false;
			}
			
			if (!add_state)
			{
				$("#add_customer_dialog").height(this_dialog.height() + alert_height);
				$("#add_state"+"_alert").show();
				isvalid = false;
			}
			
			if (!add_zip)
			{
				$("#add_customer_dialog").height(this_dialog.height() + alert_height);
				$("#add_zip"+"_alert").show();
				isvalid = false;
			}
			
			if (!add_contact)
			{
				$("#add_customer_dialog").height(this_dialog.height() + alert_height);
				$("#add_contact"+"_alert").show();
				isvalid = false;
			}
			
			if (add_phone)
			{
				add_phone = add_phone.replace(/[^0-9]/g, '');
				if(add_phone.length != 10)
				{
					$("#add_customer_dialog").height(this_dialog.height() + alert_height);
					$("#add_phone"+"_alert").show();
					isvalid = false;
				}else
				{
					$("#add_phone").val(add_phone);
				}
			}
			else
			{
				$("#add_customer_dialog").height(this_dialog.height() + alert_height);
				$("#add_phone"+"_alert").show();
				isvalid = false;
			}
			
			//ONLY VALIDATES IF SOMETHING IS ENTERED
			if (add_fax)
			{
				add_fax = add_fax.replace(/[^0-9]/g, '');
				if(add_fax.length != 10)
				{
					$("#add_customer_dialog").height(this_dialog.height() + alert_height);
					$("#add_fax"+"_alert").show();
					isvalid = false;
				}else
				{
					$("#add_fax").val(add_fax);
				}
			}
			
			if (add_email)
			{
				if (!email_is_valid(add_email))
				{
					$("#add_customer_dialog").height(this_dialog.height() + alert_height);
					$("#add_email"+"_alert").show();
					isvalid = false;
				}
			}
			else
			{
				$("#add_customer_dialog").height(this_dialog.height() + alert_height);
				$("#add_email"+"_alert").show();
				isvalid = false;
			}
			
			/**
			if (!add_mc_number)
			{
				$("#add_customer_dialog").height(this_dialog.height() + alert_height);
				$("#add_mc_number"+"_alert").show();
				isvalid = false;
			}
			**/
			
			if (add_form_of_payment == "Select")
			{
				$("#add_customer_dialog").height(this_dialog.height() + alert_height);
				$("#add_form_of_payment"+"_alert").show();
				isvalid = false;
			}
			
			if(isvalid)
			{
				$("#add_customer_form").submit();
			}
			
		}//END VALIDATE ADD NEW CUSTOMER
		
		//VALIDATE SAVE CUSTOMER EDIT
		function validate_save_customer()
		{
			//SET INITIAL VARIABLES
			var isvalid = true;
			
			//HIDE ALL PREVIOUS ALERTS
			$("#customer_name_alert").hide();
			$("#address_alert").hide();
			$("#city_alert").hide();
			$("#state_alert").hide();
			$("#zip_alert").hide();
			$("#contact_alert").hide();
			$("#phone_alert").hide();
			$("#fax_alert").hide();
			$("#email_alert").hide();
			$("#mc_number_alert").hide();
			$("#form_of_payment_alert").hide();

			//GET ALL FORM DATA
			var customer_name = $("#customer_name").val();
			var address = $("#address").val();
			var city = $("#city").val();
			var state = $("#state").val();
			var zip = $("#zip").val();
			var contact = $("#contact").val();
			var phone = $("#phone").val();
			var fax = $("#fax").val();
			var email = $("#email").val();
			var mc_number = $("#mc_number").val();
			var form_of_payment = $("#form_of_payment").val();
			
			
			if(!customer_name)
			{
				$("#customer_name"+"_alert").show();
				isvalid = false;
			}else
			{
			
				//IF THE CUSTOMERS NAME HASN'T BEEN CHANGED
				if (customer_name != "<?=$this_customer["customer_name"]?>")
				{
					//DOES THE CUSTOMER ALREADY EXIST IN THE DB
					var customer_found = false;
					for (var customer in customer_validation_list)
					{
						if(customer_name == customer_validation_list[customer])
						{
							customer_found = true;
							break;
						}
					}
					//IF THE BROKER ALREADY EXISTS IN THE DB
					if(customer_found)
					{
						$("#customer_exists"+"_alert").show();
						isvalid = false;
					}
				}
			}
			
			if (!customer_name)
			{
				//$("#customer_name"+"_alert").show();
				//isvalid = false;
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
				if (!email_is_valid(email))
				{
					$("#email"+"_alert").show();
					isvalid = false;
				}
			}
			
			
			if (!mc_number)
			{
				$("#mc_number"+"_alert").show();
				isvalid = false;
			}
			
			
			if (form_of_payment == "Select")
			{
				$("#form_of_payment"+"_alert").show();
				isvalid = false;
			}
			
			if(isvalid)
			{
				$("#save_customer_form").submit();
			}
			
		}//END VALIDATE SAVE CUSTOMER
		
		function email_is_valid(email) 
		{
			var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			if( !emailReg.test( email ) ) 
			{
				return false;
			} else 
			{
				return true;
			}
		}//END VALIDATE EMAIL
		</script>
		
	</head>
	
	<body id="body">
		
		<?php include("main_menu.php"); ?>
	
		<div id="main_div">
			<div id="space_header">
			</div>
			
			<div id="left_bar">
				<button class='left_bar_button jq_button' id="new_customer">New Customer</button>
				<br>
				<br>
				<span class="heading">Customer Status</span>
				<hr/>
				<?php $attributes = array('name'=>'customer_status_form','ID'=>'customer_status_form' )?>
				<?=form_open('customers/select_customer_status',$attributes);?>
						<?php $options = array(
							'All' 				=> 'All',
							'Good' 			 	=> 'Good',
							'Bad'   			=> 'Bad',
							'Setup Pending' 	=> 'Setup Pending'
					); ?>
					<?php echo form_dropdown('customer_status_dropdown',$options,$customer_status,'onChange="submit()" style="width:156px;"');?>
					<?php echo form_hidden('mode',$mode); ?>
				</form>
				<br>
				<br>
				<span class="heading">Customers</span>
				<hr/>
				<div id="customer_list_div" style="overflow-y: auto; overflow-x: hidden; width: 155px;">
					<?php foreach ($customers as $customer): ?>
						<?php 
							$selected = ""; 
							if ($this_customer['id'] == $customer["id"])
							{
								$selected = " font-weight:bold;";//background: #DCDCDC;"
							}
							$title = $customer["customer_name"];
						 ?>
						<div class="left_bar_link_div" title="<?=$title ?>" style="overflow:hidden; white-space: nowrap; text-overflow:ellipsis; width:120px; <?=$selected?>" onclick="location.href='<?= base_url("index.php/customers/index/$customer_status/view/".$customer["id"]);?>'">
							<?=$customer["customer_name"]; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			
			<?php if ($mode == 'none'):?>
				<div id="main_content">
					<div id="main_content_header">
					</div>
					<div id="scrollable_content" class="scrollable_div" >
						<div style="margin:40px; width: 600px;">
							<span class="heading">Setup Pending:</span>
							<br>
							<br>
							<table>
								<?php foreach($all_customers as $broker): ?>
									<?php if($broker["status"] == "Setup Pending"): ?>
										<tr>
											<td style="width:300px;">
												<?=$broker["customer_name"]?>
											</td>
											<td>
												<?=$broker["notes"]?>
											</td>
										</tr>
									<?php endif; ?>
								<?php endforeach; ?>
							</table>
						</div>
					</div>
				</div>
			<?php endif; ?>
			
			<?php if ($mode == 'view'):?>
				<div id="main_content">
					<div id="main_content_header">
						<span style="font-weight:bold;"><?=$this_customer["customer_name"]?></span>
						<button style="float:right;  width:80px;" id="edit_customer">Edit</button>
					</div>
					
					<div id="scrollable_content"  class="scrollable_div">
						<div style="margin:20px;">
							<table id="main_content_table" style="font-size:14px;">
								<tr>
									<td style="width:300px;">Customer Name:</td>
									<td>
										<?=$this_customer['customer_name'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Form of Payment:</td>
									<td>
										<?=$this_customer['form_of_payment'];?>
									</td>
								</tr>
								
								<tr>
									<td style="width:300px;">Address:</td>
									<td>
										<?=$this_customer['address'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">City:</td>
									<td>
										<?=$this_customer['city'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">State:</td>
									<td>
										<?=$this_customer['state'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Zip Code:</td>
									<td>
										<?=$this_customer['zip'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Contact:</td>
									<td>
										<?=$this_customer['contact'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Phone Number:</td>
									<td>
										<?=$this_customer['phone'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Fax Number:</td>
									<td>
										<?=$this_customer['fax'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Email:</td>
									<td>
										<?=$this_customer['email'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">MC Number:</td>
									<td>
										<?=$this_customer['mc_number'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Status:</td>
									<td>
										<?=$this_customer['status'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Notes:</td>
									<td>
										<?=$this_customer['notes'];?>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			<?php endif;?>				
			
			<?php if ($mode == 'edit'):?>
				<div id="main_content">
					<div id="main_content_header">
						<span style="font-weight:bold;"><?=$this_customer["customer_name"]?></span>
						<button style="float:right; width:80px; margin-left:20px;" id="save_customer">Save</button>
						<button style="float:right; width:80px; margin-left:20px;" id="cancel_edit_customer">Cancel</button>
					</div>
					
					<div id="scrollable_content"  class="scrollable_div">
						<div style="margin:20px;">
							<?php $attributes = array('id' => 'save_customer_form'); ?>
							<?=form_open('customers/save_customer',$attributes)?>
							<?=form_hidden('id',$this_customer['id'])?>
							<?php $text_box_style = "width:157px; margin-left:2px;" ?>
							<table id="main_content_table" style="font-size:14px;">
								<tr>
									<td style="width:300px;">Customer Name:</td>
									<td>
										<?= form_input('customer_name',$this_customer['customer_name'],'id="customer_name" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										*
									</td>
									<td id="customer_name_alert" class="alert">A Customer Name must be entered</td>
									<td id="customer_exists_alert" class="alert">This customer already exists in the system</td>
								</tr>
								<tr>
									<td style="width:300px;">Form of Payment:</td>
									<td>
										<?php $options = array(
										'Select'  	=> 'Select',
										'Factor'  	=> 'Factor',
										'Bill Direct'  => 'Bill Direct',
										); ?>
										<?php echo form_dropdown('form_of_payment',$options,$this_customer["form_of_payment"],'id="form_of_payment" style="width:157px; height:21px; margin-left:2px;"');?>
						
									</td>
									<td style="color:red; width:5px;">
										*
									</td>
									<td id="form_of_payment_alert" class="alert">A Form of Payment must be entered</td>
								</tr>
								<tr>
									<td style="width:300px;">Address:</td>
									<td>
										<?php $data = array(
										  'name'        => "address",
										  'id'          => "address",
										  'rows'		=> '3',
										  'cols'		=> '17',
										  'value'		=> $this_customer["address"],
										);?>
										<?=form_textarea($data);?>
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td id="address_alert" class="alert">An Address must be entered</td>
								</tr>
								<tr>
									<td style="width:300px;">City:</td>
									<td>
										<?= form_input('city',$this_customer['city'],'id="city" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td id="city_alert" class="alert">A City must be entered</td>
								</tr>
								<tr>
									<td style="width:300px;">State:</td>
									<td>
										<?= form_input('state',$this_customer['state'],'id="state" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td id="state_alert" class="alert">A State must be entered</td>
								</tr>
								<tr>
									<td style="width:300px;">Zip Code:</td>
									<td>
										<?= form_input('zip',$this_customer['zip'],'id="zip" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td id="zip_alert" class="alert">A Zip must be entered</td>
								</tr>
								<tr>
									<td style="width:300px;">Contact:</td>
									<td>
										<?= form_input('contact',$this_customer['contact'],'id="contact" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td id="contact_alert" class="alert">A Contact must be entered</td>
								</tr>
								<tr>
									<td style="width:300px;">Phone Number:</td>
									<td>
										<?= form_input('phone',$this_customer['phone'],'id="phone" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td id="phone_alert" class="alert">Phone Number must be a valid 10 digit number</td>
								</tr>
								<tr>
									<td style="width:300px;">Fax Number:</td>
									<td>
										<?= form_input('fax',$this_customer['fax'],'id="fax" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td id="fax_alert" class="alert">Fax Number must be a valid 10 digit number</td>
								</tr>
								<tr>
									<td style="width:300px;">Email:</td>
									<td>
										<?= form_input('email',$this_customer['email'],'id="email" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td id="email_alert" class="alert">Email must be a valid email address</td>
								</tr>
								<tr>
									<td style="width:300px;">MC Number:</td>
									<td>
										<?= form_input('mc_number',$this_customer['mc_number'],'id="mc_number" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										*
									</td>
									<td id="mc_number_alert" class="alert">An MC Number must be entered</td>
								</tr>
								<tr>
									<td>Status:</td>
									<td>
										<?php $options = array(
										'Good'  	=> 'Good',
										'Bad'  => 'Bad',
										); ?>
										<?php echo form_dropdown('status',$options,$this_customer["status"],'id="status" style="width:157px; height:21px; margin-left:2px;"');?>
									</td>
									<td style="color:red; width:5px;">
										*
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Notes:</td>
									<td>
										<?php $data = array(
										  'name'        => "notes",
										  'id'          => "notes",
										  'rows'		=> '3',
										  'cols'		=> '17',
										  'value'		=> $this_customer["notes"],
										);?>
										<?=form_textarea($data);?>
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td id="notes_alert" class="alert">Notes must be entered</td>
								</tr>
							</table>
							</form>
						</div>
					</div>
				</div>
					
			<?php endif;?>
		</div>
	</body>
	
	<div id="add_customer_dialog" title="Add New Customer" style="display:none">
	
		<div id="add_customer_name_alert" class="alert">* A Customer Name must be entered</div>
		<div id="add_customer_exists_alert" class="alert">* This Customer already exists in the system</div>
		<div id="add_form_of_payment_alert" class="alert">* A Form of Payment must be selected</div>
		<div id="add_address_alert" class="alert">* An Address must be entered</div>
		<div id="add_city_alert" class="alert">* A City must be entered</div>
		<div id="add_state_alert" class="alert">* A State must be entered</div>
		<div id="add_zip_alert" class="alert">* A Zip must be entered</div>
		<div id="add_contact_alert" class="alert">* A Contact must be entered</div>
		<div id="add_phone_alert" class="alert">* Phone Number must be a valid 10 digit number</div>
		<div id="add_fax_alert" class="alert">* Fax Number must be a valid 10 digit number</div>
		<div id="add_email_alert" class="alert">* Email must be a valid email address</div>
		<div id="add_mc_number_alert" class="alert">* An MC Number must be entered</div>
		
		<div style="margin:20px;">
			<?php $attributes = array('id' => 'add_customer_form'); ?>
			<?=form_open('customers/add_customer',$attributes)?>
				<?php $text_box_style = "width:161px; margin-left:2px;";?>
				
				<table id="customers_view;" style="font-size:12px;">
					<tr>
						<td style="width:300px;">Customer Name:</td>
						<td>
							<?= form_input('add_customer_name',null,'id="add_customer_name" style="'.$text_box_style.'"')?>
						</td>
						<td>
							<span style="color:red">*</span>
						</td>
					</tr>
					<tr>
						<td style="width:300px;">Form of Payment:</td>
						<td>
							<?php $options = array(
							'Select'  	=> 'Select',
							'Factor'  	=> 'Factor',
							'Bill Direct'  => 'Bill Direct',
							); ?>
							<?php echo form_dropdown('add_form_of_payment',$options,'Select','id="add_form_of_payment" style="width:161px; height:21px; margin-left:2px;"');?>
						</td>
						<td>
							<span style="color:red">*</span>
						</td>
					</tr>
					<tr>
						<td style="width:300px; vertical-align: top;">Address:</td>
						<td>
							<?php $data = array(
							  'name'        => "add_address",
							  'id'          => "add_address",
							  'rows'		=> '3',
							  'cols'		=> '20',
							  'value'		=> null,
							);?>
							<?=form_textarea($data);?>
						</td>
						<td>
							<span style="color:red; vertical-align: top;">*</span>
						</td>
					</tr>
					<tr>
						<td style="width:300px;">City:</td>
						<td>
							<?= form_input('add_city',null,'id="add_city" style="'.$text_box_style.'"')?>
						</td>
						<td>
							<span style="color:red">*</span>
						</td>
					</tr>
					<tr>
						<td style="width:300px;">State:</td>
						<td>
							<?= form_input('add_state',null,'id="add_state" style="'.$text_box_style.'"')?>
						</td>
						<td>
							<span style="color:red">*</span>
						</td>
					</tr>
					<tr>
						<td style="width:300px;">Zip Code:</td>
						<td>
							<?= form_input('add_zip',null,'id="add_zip" style="'.$text_box_style.'"')?>
						</td>
						<td>
							<span style="color:red">*</span>
						</td>
					</tr>
					<tr>
						<td style="width:300px;">Contact:</td>
						<td>
							<?= form_input('add_contact',null,'id="add_contact" style="'.$text_box_style.'"')?>
						</td>
						<td>
							<span style="color:red">*</span>
						</td>
					</tr>
					<tr>
						<td style="width:300px;">Phone Number:</td>
						<td>
							<?= form_input('add_phone',null,'id="add_phone" style="'.$text_box_style.'"')?>
						</td>
						<td>
							<span style="color:red">*</span>
						</td>
					</tr>
					<tr>
						<td style="width:300px;">Fax Number:</td>
						<td>
							<?= form_input('add_fax',null,'id="add_fax" style="'.$text_box_style.'"')?>
						</td>
					</tr>
					<tr>
						<td style="width:300px;">Email:</td>
						<td>
							<?= form_input('add_email',null,'id="add_email" style="'.$text_box_style.'"')?>
						</td>
						<td>
							<span style="color:red">*</span>
						</td>
					</tr>
					<tr>
						<td style="width:300px;">MC Number:</td>
						<td>
							<?= form_input('add_mc_number',null,'id="add_mc_number" style="'.$text_box_style.'"')?>
						</td>
					</tr>
					<tr>
						<td>Status:</td>
						<td>
							<?php $options = array(
							'Good'  	=> 'Good',
							'Bad'  => 'Bad',
							); ?>
							<?php echo form_dropdown('add_status',$options,'Good','id="add_status" style="width:161px; height:21px; margin-left:2px;"');?>
						</td>
					</tr>
					<tr>
						<td style="width:300px; vertical-align: top;">Notes:</td>
						<td>
							<?php $data = array(
							  'name'        => "add_notes",
							  'id'          => "add_notes",
							  'rows'		=> '3',
							  'cols'		=> '20',
							  'value'		=> null,
							);?>
							<?=form_textarea($data);?>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>