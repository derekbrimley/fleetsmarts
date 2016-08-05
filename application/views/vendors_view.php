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
			//alert($("#main_content").height());
			
			$("#new_vendor").button().click(function()
			{
				$("#add_vendor_dialog").dialog("open");
			});
			
			$("#edit_vendor").button().click(function()
			{
				window.location = "<?= base_url("index.php/vendors/index/edit/".$this_vendor['id']);?>"
			});
			
			$("#cancel_edit_vendor").button().click(function()
			{
				window.location = "<?= base_url("index.php/vendors/index/view/".$this_vendor['id']);?>"
			});
			
			$("#save_vendor").button().click(function()
			{
				validate_save_vendor();
			});//END save_vendor button
			
			//DIALOG: ADD NEW CUSTOMER
			$( "#add_vendor_dialog" ).dialog(
			{
					autoOpen: false,
					height: 580,
					width: 425,
					modal: true,
					buttons: 
						[
							{
								text: "Add Vendor",
								click: function() 
								{
											

									//VALIDATE ADD NEW CUSTOMER
									validate_add_new_vendor();
									
								},//end add load
							},
							{
								text: "Cancel",
								click: function() 
								{
									//RESIZE DIALOG BOX
									$( this ).dialog( "close" );
									
									//RESET ALL FIELDS IN DIALOG FORM
									$("#add_company_name").val(null);
									$("#add_company_short_name").val(null);
									$("#add_address").val(null);
									$("#add_city").val(null);
									$("#add_state").val(null);
									$("#add_status").val("Good");
									$("#add_zip").val(null);
									$("#add_contact").val(null);
									$("#add_email").val(null);
									$("#add_phone").val(null);
									$("#add_fax").val(null);
									$("#add_email").val(null);
									$("#add_notes").val(null);
								}
							}
						],//end of buttons
					
					open: function()
						{
							//HIDE ALL PREVIOUS ALERTS
							$("#add_company_name_alert").hide();
							$("#add_company_short_name_alert").hide();
							$("#add_vendor_exists_alert").hide();
							$("#add_address_alert").hide();
							$("#add_city_alert").hide();
							$("#add_state_alert").hide();
							$("#add_zip_alert").hide();
							$("#add_contact_alert").hide();
							$("#add_email_alert").hide();
							$("#add_phone_alert").hide();
							$("#add_fax_alert").hide();
							$("#add_email_alert").hide();
						
							//RESET HEIGHT OF DIALOG BOX
							$("#add_vendor_dialog").height(480);
							
						},//end open function
					close: function() 
						{
							//HIDE ALL PREVIOUS ALERTS
							$("#add_company_name_alert").hide();
							$("#add_company_short_name_alert").hide();
							$("#add_vendor_exists_alert").hide();
							$("#add_address_alert").hide();
							$("#add_city_alert").hide();
							$("#add_state_alert").hide();
							$("#add_zip_alert").hide();
							$("#add_contact_alert").hide();
							$("#add_email_alert").hide();
							$("#add_phone_alert").hide();
							$("#add_fax_alert").hide();
							$("#add_email_alert").hide();
							
							//RESET HEIGHT OF DIALOG BOX
							$("#add_vendor_dialog").height(480);
							
							
						}
			});//end dialog form
			
			//DIALOG: CREATE CC ACCOUNT
			$( "#create_cc_account" ).dialog(
			{
				autoOpen: false,
				height: 150,
				width: 350,
				modal: true,
				buttons: 
				[
					{
						text: "Submit",
						click: function() 
						{
							//VALIDATE CREATE CC ACCOUNT
							add_cc_to_vendor();
							
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
			
		});//END DOCUMENT READY
		
		//CREATE VENDORS ARRAY - FULL NAME
		var vendor_validation_list = [
		<?php 	
				$array_string = "";
				foreach($vendors as $vendor)
				{
					$company_name = $vendor['company_name'];
					$array_string = $array_string.'"'.$company_name.'",';
				}
				echo substr($array_string,0,-1);
		?>];
		
		//CREATE VENDORS ARRAY - SHORT NAME
		var vendor_validation_list_short_name = [
		<?php 	
				$array_string = "";
				foreach($vendors as $vendor)
				{
					$company_name = $vendor['company_side_bar_name'];
					$array_string = $array_string.'"'.$company_name.'",';
				}
				echo substr($array_string,0,-1);
		?>];
		
		//VALIDATE ADD NEW CUSTOMER DIALOG FORM
		function validate_add_new_vendor()
		{
			//SET INITIAL VARIABLES
			var this_dialog = $("#add_vendor_dialog");
			var alert_height = 15;
			var isvalid = true;
			
			//HIDE ALL PREVIOUS ALERTS
			$("#add_company_name_alert").hide();
			$("#add_company_short_name_alert").hide();
			$("#add_vendor_exists_alert").hide();
			$("#add_address_alert").hide();
			$("#add_city_alert").hide();
			$("#add_state_alert").hide();
			$("#add_zip_alert").hide();
			$("#add_contact_alert").hide();
			$("#add_email_alert").hide();
			$("#add_phone_alert").hide();
			$("#add_fax_alert").hide();
			$("#add_email_alert").hide();

			//RESET HEIGHT OF DIALOG BOX
			$("#add_vendor_dialog").height(480);
			
			//GET ALL FORM DATA
			var add_company_name = $("#add_company_name").val();
			var add_company_short_name = $("#add_company_short_name").val();
			var add_address = $("#add_address").val();
			var add_city = $("#add_city").val();
			var add_state = $("#add_state").val();
			var add_zip = $("#add_zip").val();
			var add_contact = $("#add_contact").val();
			var add_email = $("#add_email").val();
			var add_phone = $("#add_phone").val();
			var add_fax = $("#add_fax").val();
			var add_email = $("#add_email").val();
			
			
			if(!add_company_name)
			{
				$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
				$("#add_company_name"+"_alert").show();
				isvalid = false;
			}else
			{
				//DOES THE CUSTOMER ALREADY EXIST IN THE DB
				var vendor_found = false;
				for (var vendor in vendor_validation_list)
				{
					if(add_company_name == vendor_validation_list[vendor])
					{
						vendor_found = true;
						break;
					}
				}
				//IF THE BROKER ALREADY EXISTS IN THE DB
				if(vendor_found)
				{
					$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
					$("#add_vendor_exists"+"_alert").show();
					isvalid = false;
				}
			}
			
			if(!add_company_short_name)
			{
				$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
				$("#add_company_short_name"+"_alert").show();
				isvalid = false;
			}else
			{
				//DOES THE CUSTOMER ALREADY EXIST IN THE DB
				var vendor_found = false;
				for (var vendor in vendor_validation_list_short_name)
				{
					if(add_company_short_name == vendor_validation_list_short_name[vendor])
					{
						vendor_found = true;
						break;
					}
				}
				//IF THE BROKER ALREADY EXISTS IN THE DB
				if(vendor_found)
				{
					$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
					$("#add_short_name_exists"+"_alert").show();
					isvalid = false;
				}
			}
			
			if (!add_company_name)
			{
				$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
				$("#add_company_name"+"_alert").show();
				isvalid = false;
			}
			
			if (!add_address)
			{
				$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
				$("#add_address"+"_alert").show();
				isvalid = false;
			}
			
			if (!add_city)
			{
				$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
				$("#add_city"+"_alert").show();
				isvalid = false;
			}
			
			if (!add_state)
			{
				$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
				$("#add_state"+"_alert").show();
				isvalid = false;
			}
			
			if (!add_zip)
			{
				$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
				$("#add_zip"+"_alert").show();
				isvalid = false;
			}
			
			if (!add_contact)
			{
				$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
				$("#add_contact"+"_alert").show();
				isvalid = false;
			}
			
			if (add_phone)
			{
				add_phone = add_phone.replace(/[^0-9]/g, '');
				if(add_phone.length != 10)
				{
					$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
					$("#add_phone"+"_alert").show();
					isvalid = false;
				}else
				{
					$("#add_phone").val(add_phone);
				}
			}
			else
			{
				$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
				$("#add_phone"+"_alert").show();
				isvalid = false;
			}
			
			//ONLY VALIDATES IF SOMETHING IS ENTERED
			if (add_fax)
			{
				add_fax = add_fax.replace(/[^0-9]/g, '');
				if(add_fax.length != 10)
				{
					$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
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
					$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
					$("#add_email"+"_alert").show();
					isvalid = false;
				}
			}
			else
			{
				$("#add_vendor_dialog").height(this_dialog.height() + alert_height);
				$("#add_email"+"_alert").show();
				isvalid = false;
			}
			
			if(isvalid)
			{
				$("#add_vendor_form").submit();
			}
			
		}//END VALIDATE ADD NEW CUSTOMER
		
		//VALIDATE SAVE CUSTOMER EDIT
		function validate_save_vendor()
		{
			//SET INITIAL VARIABLES
			var isvalid = true;
			
			//HIDE ALL PREVIOUS ALERTS
			$("#company_name_alert").hide();
			$("#company_short_name_alert").hide();
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
			var address = $("#address").val();
			var city = $("#city").val();
			var state = $("#state").val();
			var zip = $("#zip").val();
			var contact = $("#contact").val();
			var email = $("#email").val();
			var phone = $("#phone").val();
			var fax = $("#fax").val();
			var email = $("#email").val();
			
			
			if(!company_name)
			{
				$("#company_name"+"_alert").show();
				isvalid = false;
			}else
			{
			
				//IF THE CUSTOMERS NAME HASN'T BEEN CHANGED
				if (company_name != "<?=$this_vendor["company_name"]?>")
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
			}else
			{
			
				//IF THE CUSTOMERS NAME HASN'T BEEN CHANGED
				if (company_short_name != "<?=$this_vendor["company_side_bar_name"]?>")
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
			
			if(isvalid)
			{
				$("#save_vendor_form").submit();
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
		
		//ADD CREDIT CARD ACCOUNT TO VENDOR
		function add_cc_to_vendor()
		{
			var isValid = true;
		
			//VALIDATE THE CC NUMBER
			cc_number = $("#cc_number").val();
			
			if(cc_number.length != 4)
			{
				isValid = false;
				alert("Credit Card Number must be a 4 digit number!")
			}
		
			if(isValid)
			{
				$("#add_vendor_cc").submit();
			}
		}
		</script>
		
	</head>
	
	<body id="body">
		
		<?php include("main_menu.php"); ?>
	
		<div id="main_div">
			<div id="space_header">
			</div>
			
			<div id="left_bar">
				<button class='left_bar_button jq_button' id="new_vendor">New Vendor</button>
				<br>
				<br>
				<span class="heading">Vendors</span>
				<hr/>
				<br>
				<?php foreach ($vendors as $vendor): ?>
					<?php 
						$selected = ""; 
						if ($this_vendor['id'] == $vendor["id"])
						{
							$selected = " color:#DD4B39; font-weight:bold;";//background: #DCDCDC;"
						}
					 ?>
					<div class="left_bar_link_div" style="<?=$selected?>" onclick="location.href='<?= base_url("index.php/vendors/index/view/".$vendor["id"]);?>'">
						<?=$vendor["company_side_bar_name"] ?>
					</div>
				<?php endforeach; ?>
			</div>
			
			
			<?php if ($mode == 'view'):?>
				<div id="main_content">
					<div id="main_content_header">
						<span style="font-weight:bold;"><?=$this_vendor["company_name"]?></span>
						<button style="float:right;  width:80px;" id="edit_vendor">Edit</button>
					</div>
					
					<div id="scrollable_content"  class="scrollable_div">
						<div style="margin:20px;">
							<table id="main_content_table" style="font-size:14px;">
								<tr>
									<td style="width:300px;">Vendors Name:</td>
									<td>
										<?=$this_vendor['company_name'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Short Name:</td>
									<td>
										<?=$this_vendor['company_side_bar_name'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Address:</td>
									<td>
										<?=$this_vendor['address'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">City:</td>
									<td>
										<?=$this_vendor['city'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">State:</td>
									<td>
										<?=$this_vendor['state'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Zip Code:</td>
									<td>
										<?=$this_vendor['zip'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Contact:</td>
									<td>
										<?=$this_vendor['contact'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Email:</td>
									<td>
										<?=$this_vendor['company_email'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Phone Number:</td>
									<td>
										<?=$this_vendor['company_phone'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Fax Number:</td>
									<td>
										<?=$this_vendor['company_fax'];?>
									</td>
								</tr>
								<tr>
									<td style="width:300px;">Notes:</td>
									<td>
										<?=$this_vendor['company_notes'];?>
									</td>
								</tr>
								<tr>
									<td>
										Spark CC Account ID
									</td>
									<?php if(!empty($spark_cc_account)):?>
										<td>
											<?=$spark_cc_account["id"]?>
										</td>
									<?php else:?>
										<td>
											None
										</td>
									<?php endif;?>
								</tr>
							</table>
						</div>
					</div>
				</div>
			<?php endif;?>				
			
			<?php if ($mode == 'edit'):?>
				<div id="main_content">
					<div id="main_content_header">
						<span style="font-weight:bold;"><?=$this_vendor["company_name"]?></span>
						<button style="float:right; width:80px; margin-left:20px;" id="save_vendor">Save</button>
						<button style="float:right; width:80px; margin-left:20px;" id="cancel_edit_vendor">Cancel</button>
					</div>
					
					<div id="scrollable_content"  class="scrollable_div">
						<div style="margin:20px;">
							<?php $attributes = array('id' => 'save_vendor_form'); ?>
							<?=form_open('vendors/save_vendor',$attributes)?>
							<?=form_hidden('id',$this_vendor['id'])?>
							<?php $text_box_style = "width:200px; margin-left:2px;" ?>
							<table id="main_content_table" style="font-size:14px;">
								<tr>
									<td style="width:300px;">Vendor Name:</td>
									<td>
										<?= form_input('company_name',$this_vendor['company_name'],'id="company_name" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										*
									</td>
									<td id="company_name_alert" class="alert">A Vendor Name must be entered</td>
									<td id="vendor_exists_alert" class="alert">This vendor already exists in the system</td>
								</tr>
								<tr>
									<td style="width:300px;">Short Name:</td>
									<td>
										<?= form_input('company_short_name',$this_vendor['company_side_bar_name'],'id="company_short_name" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										*
									</td>
									<td id="company_short_name_alert" class="alert">A Short Name must be entered</td>
									<td id="short_name_exists_alert" class="alert">This short name already exists in the system</td>
								</tr>
								<tr>
									<td style="width:300px;">Address:</td>
									<td>
										<?php $data = array(
										  'name'        => "address",
										  'id'          => "address",
										  'rows'		=> '3',
										  'style'		=> 'margin-left:2px; width:200px;',
										  'value'		=> $this_vendor["address"],
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
										<?= form_input('city',$this_vendor['city'],'id="city" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td id="city_alert" class="alert">A City must be entered</td>
								</tr>
								<tr>
									<td style="width:300px;">State:</td>
									<td>
										<?= form_input('state',$this_vendor['state'],'id="state" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td id="state_alert" class="alert">A State must be entered</td>
								</tr>
								<tr>
									<td style="width:300px;">Zip Code:</td>
									<td>
										<?= form_input('zip',$this_vendor['zip'],'id="zip" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td id="zip_alert" class="alert">A Zip must be entered</td>
								</tr>
								<tr>
									<td style="width:300px;">Contact:</td>
									<td>
										<?= form_input('contact',$this_vendor['contact'],'id="contact" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td id="contact_alert" class="alert">A Contact must be entered</td>
								</tr>
								<tr>
									<td style="width:300px;">Email:</td>
									<td>
										<?= form_input('email',$this_vendor['company_email'],'id="email" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td id="email_alert" class="alert">Email must be a valid email address</td>
								</tr>
								<tr>
									<td style="width:300px;">Phone Number:</td>
									<td>
										<?= form_input('phone',$this_vendor['company_phone'],'id="phone" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
										
									</td>
									<td id="phone_alert" class="alert">Phone Number must be a valid 10 digit number</td>
								</tr>
								<tr>
									<td style="width:300px;">Fax Number:</td>
									<td>
										<?= form_input('fax',$this_vendor['company_fax'],'id="fax" style="'.$text_box_style.'"')?>
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td id="fax_alert" class="alert">Fax Number must be a valid 10 digit number</td>
								</tr>
								<tr>
									<td style="width:300px;">Notes:</td>
									<td>
										<?php $data = array(
										  'name'        => "notes",
										  'id'          => "notes",
										  'rows'		=> '3',
										  'style'		=> 'margin-left:2px; width:200px;',
										  'value'		=> $this_vendor["company_notes"],
										);?>
										<?=form_textarea($data);?>
									</td>
									<td style="color:red; width:5px;">
									</td>
									<td id="notes_alert" class="alert">Notes must be entered</td>
								</tr>
								<tr>
									<td>
										Spark CC Account ID
									</td>
									<?php if(!empty($spark_cc_account)):?>
										<td>
											<?=$spark_cc_account["id"]?>
										</td>
									<?php else:?>
										<td>
											<button class="jq_button" style="float:right; width:200px;" id="add_cc" onclick="$('#create_cc_account').dialog('open'); return false;" >Add CC Account</button>
										</td>
									<?php endif;?>
								</tr>
							</table>
							</form>
						</div>
					</div>
				</div>
					
			<?php endif;?>
		</div>
	</body>
	
	
	
	
	<div id="create_cc_account" title="Create CC Account" style="display:none">
		<?=$spark_cc_account["id"]?>
		<?php $attributes = array('id' => 'add_vendor_cc', 'name'=>'add_vendor_cc'); ?>
		<?=form_open('vendors/add_cc',$attributes)?>
			<input type="hidden" id="vendor_id" name="vendor_id" value="<?=$this_vendor["id"]?>">
			<table style="margin-top:20px; margin-left:40px;">
				<tr>
					<td style="width:200px; vertical-align:middle;">
						Last four of CC
					</td>
					<td style="width:200px; vertical-align:middle;">
						<input type="text" id="cc_number" name="cc_number" style="text-align:right; width:130px;">
					</td>
				</tr>
			</table>
		</form>
	</div>
	
	
	
	
	<div id="add_vendor_dialog" title="Add New Vendors" style="display:none">
	
		<div id="add_company_name_alert" class="alert">* A Vendors Name must be entered</div>
		<div id="add_vendor_exists_alert" class="alert">* This Vendors already exists in the system</div>
		<div id="add_company_short_name_alert" class="alert">* A Short Name must be entered</div>
		<div id="add_short_name_exists_alert" class="alert">* This Short Name already exists in the system</div>
		<div id="add_form_of_payment_alert" class="alert">* A Form of Payment must be selected</div>
		<div id="add_address_alert" class="alert">* An Address must be entered</div>
		<div id="add_city_alert" class="alert">* A City must be entered</div>
		<div id="add_state_alert" class="alert">* A State must be entered</div>
		<div id="add_zip_alert" class="alert">* A Zip must be entered</div>
		<div id="add_contact_alert" class="alert">* A Contact must be entered</div>
		<div id="add_email_alert" class="alert">* Email must be a valid email address</div>
		<div id="add_phone_alert" class="alert">* Phone Number must be a valid 10 digit number</div>
		<div id="add_fax_alert" class="alert">* Fax Number must be a valid 10 digit number</div>
		<div id="add_email_alert" class="alert">* Email must be a valid email address</div>
		<div id="add_mc_number_alert" class="alert">* An MC Number must be entered</div>
		
		<div style="margin:20px;">
			<?php $attributes = array('id' => 'add_vendor_form'); ?>
			<?=form_open('vendors/add_vendor',$attributes)?>
				<?php $text_box_style = "width:161px; margin-left:2px;";?>
				
				<table id="vendors_view;" style="font-size:12px;">
					<tr>
						<td style="width:300px;">Vendor Name:</td>
						<td>
							<?= form_input('add_company_name',null,'id="add_company_name" style="'.$text_box_style.'"')?>
						</td>
						<td>
							<span style="color:red">*</span>
						</td>
					</tr>
					<tr>
						<td style="width:300px;">Short Name:</td>
						<td>
							<?= form_input('add_company_short_name',null,'id="add_company_short_name" style="'.$text_box_style.'"')?>
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
						<td style="width:300px;">Email:</td>
						<td>
							<?= form_input('add_email',null,'id="add_email" style="'.$text_box_style.'"')?>
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