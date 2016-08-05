<html>

	<head>
			<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
			<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
			<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
			<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
			<link type="text/css" href="<?php echo base_url("css/staff.css"); ?>" rel="stylesheet" />		
			<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
			<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>
	
	<script type="text/javascript">
	$(document).ready(function(){
	
		//set height for left_bar
		$('#left_bar').height($('#main_content').height()+26);
		
		//CREATE USERNAME VALIDATION LIST ARRAY
		var username_validation_list = [
		<?php 	
				$array_string = "";
				foreach($all_people as $user)
				{
					$username = $user['username'];
					$array_string = $array_string.'"'.$username.'",';
				}
				echo substr($array_string,0,-1);
		?>];
		
		$("#new_staff").click(function()
		{
			$("#add_staff_dialog").dialog("open");
		});
		
		//SAVE STAFF BUTTON
		$("#save_staff").button().click(function()
		{
			//alert(1);
			$('#f_name_alert').hide();
			$('#l_name_alert').hide();
			$('#phone_num_alert').hide();
			$('#email_alert').hide();
			$('#username_alert').hide();
			$('#password_alert').hide();
			$('#role_alert').hide();
			$('#username_exists_alert').hide();
		
			var valid_input = true;
			var f_name = $("#f_name").val();
			var l_name = $("#l_name").val();
			var phone_num = $("#phone_num").val();
			var email = $("#email").val();
			var username = $("#username").val();
			var password = $("#password").val();
			var role = $("#role").val();
			
			if (f_name == '')
			{
				$('#f_name_alert').show();
				valid_input = false;
			}
			
			if (l_name == '')
			{
				$('#l_name_alert').show();
				valid_input = false;
			}
			
			if (phone_num != '')
			{
				phone_num = phone_num.replace(/[^0-9]/g, '');
				if(phone_num.length != 10)
				{
					$('#phone_num_alert').show();
					valid_input = false;
				}else
				{
					$("#phone_num").val(phone_num);
				}
			}
			else
			{
				$('#phone_num_alert').show();
				valid_input = false;
			}
			
			if (email == '')
			{
				$('#email_alert').show();
				valid_input = false;
			}else if(!validate_email(email))
			{
				$('#email_alert').show();
				valid_input = false;
			}
			
			if (username == '')
			{
				$('#username_alert').show();
				valid_input = false;
			}
			else
			{
				if (username != '<?=$this_staff["username"]?>')
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
						$('#username_exists_alert').show();
						valid_input = false;
					}
				}
			}
			
			if (password == '')
			{
				$('#password_alert').show();
				valid_input = false;
			}
			
			if (role == 'Select')
			{
				$('#role_alert').show();
				valid_input = false;
			}
			
			if (valid_input)
			{
				$("#save_staff_form").submit();
			}
			
			
		});//END save_staff button
		
		$("#edit_staff").button().click(function()
		{
			window.location = "<?= base_url('index.php/staff/index/'.str_replace(" ","_",$staff_role).'/edit/'.$this_staff['id']);?>"
		});
		
		$("#delete_staff").button().click(function()
		{
			$("#confirm_delete_staff_dialog").dialog("open");
		});
		
		$("#cancel_edit_staff").button().click(function()
		{
			window.location = "<?= base_url('index.php/staff/index/'.str_replace(" ","_",$staff_role).'/view/'.$this_staff['id']);?>"
		});
		
		$( "#confirm_delete_staff_dialog" ).dialog({
			resizable: false,
			autoOpen:false,
			height:160,
			width:500,
			modal: true,
			buttons: {
				"Delete": function() {
					$("#delete_staff_form").submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
		
		//ADD NEW STAFF DIALOG
		$( "#add_staff_dialog" ).dialog(
		{
				autoOpen: false,
				height: 400,
				width: 425,
				modal: true,
				buttons: 
					{
						"Add staff": function() 
							{
								$( "#add_staff_dialog" ).dialog({height: 385});
								
								$('#add_staff_f_name_alert').hide();
								$('#add_staff_l_name_alert').hide();
								$('#add_staff_phone_num_alert').hide();
								$('#add_staff_phone_carrier_alert').hide();
								$('#add_staff_email_alert').hide();
								$('#add_staff_username_alert').hide();
								$('#add_staff_password_alert').hide();
								$('#add_staff_role_alert').hide();
								$('#add_staff_username_exists_alert').hide();	
							
								var alert_height = 15;
								var valid_input = true;
								var this_dialog = $("#add_staff_dialog");
								var f_name = $("#add_staff_f_name").val();
								var l_name = $("#add_staff_l_name").val();
								var phone_num = $("#add_staff_phone_num").val();
								var phone_carrier = $("#add_staff_phone_carrier").val();
								var email = $("#add_staff_email").val();
								var username = $("#add_staff_username").val();
								var password = $("#add_staff_password").val();
								var role = $("#add_staff_role").val();
								
								if (f_name == '')
								{
									$('#add_staff_f_name_alert').show();
									$("#add_staff_dialog").height(this_dialog.height() + alert_height);
									valid_input = false;
								}
								
								if (l_name == '')
								{
									$('#add_staff_l_name_alert').show();
									$("#add_staff_dialog").height(this_dialog.height() + alert_height);
									valid_input = false;
								}
								
								if (phone_num != '')
								{
									//$("#add_staff_phone_num").val(phone_num.replace(/[^0-9]/g, ''));
									phone_num = phone_num.replace(/[^0-9]/g, '');
									if(phone_num.length != 10)
									{
										$('#add_staff_phone_num_alert').show();
										$("#add_staff_dialog").height(this_dialog.height() + alert_height);
										valid_input = false;
									}else
									{
										$("#add_staff_phone_num").val(phone_num);
									}
								}else
								{
									$('#add_staff_phone_num_alert').show();
									$("#add_staff_dialog").height(this_dialog.height() + alert_height);
									valid_input = false;
								}
								
								if (phone_carrier == 'Select')
								{
									$('#add_staff_phone_carrier_alert').show();
									$("#add_staff_dialog").height(this_dialog.height() + alert_height);
									valid_input = false;
								}
								
								if (email == '')
								{
									$('#add_staff_email_alert').show();
									$("#add_staff_dialog").height(this_dialog.height() + alert_height);
									valid_input = false;
								}else if(!validate_email(email))
								{
									$('#add_staff_email_alert').show();
									$("#add_staff_dialog").height(this_dialog.height() + alert_height);
									valid_input = false;
								}
								
								if (username == '')
								{
									$('#add_staff_username_alert').show();
									$("#add_staff_dialog").height(this_dialog.height() + alert_height);
									valid_input = false;
								}
								else
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
										$('#add_staff_username_exists_alert').show();
										$("#add_staff_dialog").height(this_dialog.height() + alert_height);
										valid_input = false;
									}
								}
								
								if (password == '')
								{
									$('#add_staff_password_alert').show();
									$("#add_staff_dialog").height(this_dialog.height() + alert_height);
									valid_input = false;
								}
								
								if (role == 'Select')
								{
									$('#add_staff_role_alert').show();
									$("#add_staff_dialog").height(this_dialog.height() + alert_height);
									valid_input = false;
								}
								
								if (valid_input)
								{
									$( this ).dialog( "close" );
									$("#add_staff_form").submit();
								}
							},//end create an user
						
						Cancel: function() 
							{
								//CLOSE DIALOG BOX
								$( this ).dialog( "close" );
								
								//RESET ALL FIELDS IN DIALOG FORM
								$("#add_staff_f_name").val(null);
								$("#add_staff_l_name").val(null);
								$("#add_staff_phone_num").val(null);
								$("#add_staff_phone_carrier").val("Select");
								$("#add_staff_email").val(null);
								$("#add_staff_username").val(null);
								$("#add_staff_password").val(null);
								$("#add_staff_role").val("Select");
								$("#add_staff_status").val("Active");
							}
					},//end of buttons
				
				open: function()
					{
						//HIDE ALL PREVIOUS ALERTS
						$('#add_staff_f_name_alert').hide();
						$('#add_staff_l_name_alert').hide();
						$('#add_staff_phone_num_alert').hide();
						$('#add_staff_phone_carrier_alert').hide();
						$('#add_staff_email_alert').hide();
						$('#add_staff_username_alert').hide();
						$('#add_staff_password_alert').hide();
						$('#add_staff_role_alert').hide();
						$('#add_staff_username_exists_alert').hide();		
								
						//RESIZE DIALOG BOX		
						$( "#add_staff_dialog" ).dialog({height: 385});
						
					},//end open function
				close: function() 
					{
						//HIDE ALL PREVIOUS ALERTS
						$('#add_staff_f_name_alert').hide();
						$('#add_staff_l_name_alert').hide();
						$('#add_staff_phone_num_alert').hide();
						$('#add_staff_phone_carrier_alert').hide();
						$('#add_staff_email_alert').hide();
						$('#add_staff_username_alert').hide();
						$('#add_staff_password_alert').hide();
						$('#add_staff_role_alert').hide();
						$('#add_staff_username_exists_alert').hide();	
					
						//RESIZE DIALOG BOX
						$( "#add_staff_dialog" ).dialog({height: 385});
					}
		});//end dialog form
		
		$(".javascript_hide").show();
	});//end document ready
	
	function validate_email(email) 
	{
		//alert('validating email');
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

	<title><?php echo $title;?></title>
	</head>

	
	
	<body>
		

		<?php include("main_menu.php"); ?>
		
		<div id="main_div">
			<div id="space_header">
			</div>
			
			
			<div id="left_bar">
				<span class="heading">Staff Role</span>
				<hr/>
				<?php $attributes = array('name'=>'staff_type_form','ID'=>'staff_type_form' )?>
				<?=form_open('staff/select_staff_type',$attributes);?>
						<?php $options = array(
							'All' => 'All',
							'Fleet Manager'  => 'Fleet Manager',
							'Dispatcher'    => 'Dispatcher',
							'Office Manager'    => 'Office Manager',
							'Office Staff'    => 'Office Staff',
					); ?>
					<?php echo form_dropdown('staff_role_dropdown',$options,$staff_role,'onChange="submit()" style="width:156px;"');?>
					<?php echo form_hidden('mode',$mode); ?>
				</form>
				<br><br>
					
				<span class="heading">Staff</span>
				<hr/>
				<br>
				<button class='left_bar_button jq_button' id="new_staff">New staff</button>
				<br>
				
				<?php foreach ($query_staff->result() as $row): ?>
					
					<?php $selected = ""; ?>
					<?php if ($this_staff['id'] == $row->ID): ?>
					<?php $selected = " color:#DD4B39; font-weight:bold;"//background: #DCDCDC;" ?>
					<?php endif ?>
					
					<div class="left_bar_link_div" style=" <?=$selected?> " onclick="location.href='<?= base_url("index.php/staff/index/$staff_role/view/".$row->ID);?>'">
						<?=$row->F_name." ".$row->L_name ?>
					</div>
				<?php endforeach; ?>
				
				
			</div>
			
			<?php if ($mode == 'view'):?>
				<div id="main_content">
					<div id="main_content_header">
						<span style="font-weight:bold;"><?=$this_staff["f_name"]." ".$this_staff["l_name"] ?></span>
					</div>
					
					<div id="staff_content">
						<div style="margin:20px;">
							<table id="staff_view" style="font-size:14px;">
								<tr>
									<td style="width:300px;">First Name:</td>
									<td>
										<?=$this_staff['f_name'];?>
									</td>
								</tr>
								<tr>
									<td>Last Name:</td>
									<td>
										<?= $this_staff['l_name'];?>
									</td>
								</tr>
								<tr>
									<td>Phone Number:</td>
									<td>
										<?= $this_staff['phone_num'];?>
									</td>
								</tr>
								<tr>
									<td>Phone Carrier:</td>
									<td>
										<?= $this_staff['phone_carrier'];?>
									</td>
								</tr>
								<tr>
									<td>Email:</td>
									<td>
										<?= $this_staff['email'];?>
									</td>
								</tr>
								<tr>
									<td>Username:</td>
									<td>
										<?= $this_staff['username'];?>
									</td>
								</tr>
								<tr>
									<td>Password:</td>
									<?php if($this_staff['id'] == $user_id): ?>
										<td>
											<?= $this_staff['password'];?>
										</td>
									<?php else: ?>
										<td>
											*******
										</td>
									<?php endif; ?>
								</tr>
								<tr>
									<td>Role:</td>
									<td>
										<?= $this_staff['role'];?>
									</td>
								</tr>
								<tr>
									<td>Status:</td>
									<td>
										<?= $this_staff['status'];?>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="button_div">
					<button style="float:right;  width:80px;" id="edit_staff">Edit</button>
				</div>
			<?php endif;?>
			
			<?php if ($mode == 'edit'):?>
				<div id="main_content">
					<div id="main_content_header">
						<span style="font-weight:bold;"><?=$this_staff["f_name"]." ".$this_staff['l_name'] ?></span>
					</div>
					
					<div id="staff_content">
						<div style="margin:20px;">
							<?php $attributes = array('id' => 'save_staff_form'); ?>
							<?=form_open('staff/save_staff',$attributes)?>
							<?=form_hidden('id',$this_staff['id'])?>
							<table id="staff_view" style="font-size:14px;">
								<tr>
									<td style="width:300px;">First Name:</td>
									<td>
										<?= form_input('f_name',$this_staff['f_name'],'id="f_name"')?>
									</td>
									<td
									<td id="f_name_alert" class="alert">* A First Name must be entered</td>
								</tr>
								<tr>
									<td>Last Name:</td>
									<td>
										<?= form_input('l_name',$this_staff['l_name'],'id="l_name"')?>
									</td>
									<td id="l_name_alert" class="alert">* A Last Name must be entered</td>
								</tr>
								<tr>
									<td>Phone Number:</td>
									<td>
										<?= form_input('phone_num',$this_staff['phone_num'],'id="phone_num"')?>
									</td>
									<td id="phone_num_alert" class="alert">* A valid 10-digit Phone Number must be entered</td>
								</tr>
								<tr>
									<td>Phone Carrier:</td>
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
									<?php echo form_dropdown('phone_carrier',$options,$this_staff['phone_carrier'],'id="phone_carrier" style="width:155px; height:21px; float:right; margin-right:2px;"');?>
									</td>
								</tr>
								<tr>
									<td>Email:</td>
									<td>
										<?= form_input('email',$this_staff['email'],'id="email"')?>
									</td>
									<td id="email_alert" class="alert">* A valid Email Address must be entered</td>
								</tr>
								<tr>
									<td>Username:</td>
									<td>
										<?=$this_staff['username']?>
									</td>
									<td id="username_alert" class="alert">* A Username must be entered</td>
									<td id="username_exists_alert" class="alert">* This Username already exists in the system</td>
								</tr>
								<tr>
									<td>Password:</td>
									<?php if($this_staff['id'] == $user_id): ?>
										<td>
											<?= form_input('password',$this_staff['password'],'id="password"')?>
										</td>
										<td id="password_alert" class="alert">* A Password must be entered</td>
									<?php else: ?>
										<td>
											*******
											<?= form_hidden('password','hidden');?>
										</td>
									<?php endif; ?>
								</tr>
								<tr>
									<td>Role:</td>
									<td>
										<?php $options = array(
											'Fleet Manager'  => 'Fleet Manager',
											'Dispatcher'    => 'Dispatcher',
											'Office Manager'    => 'Office Manager',
											'Office Staff'    => 'Office Staff',
											); ?>
										<?php echo form_dropdown('role',$options,$this_staff['role'],'id="role" style="width:155px; height:22px; float:right; margin-right:2px;"');?>
									</td>
									<td id="role_alert" class="alert">* A Role must be selected</td>
								</tr>
								<tr>
									<td>Status:</td>
									<td>
										<?php $options = array(
										'Active'  	=> 'Active',
										'Inactive'  => 'Inactive',
										); ?>
									<?php echo form_dropdown('status',$options,$this_staff['status'],'id="status" style="width:155px; height:21px; float:right; margin-right:2px;"');?>
									</td>
								</tr>
							</table>
							
						</div>
					</div>
				</div>
				<div class="button_div">
				</form>
					<button style="float:right; width:80px; margin-left:20px;" id="save_staff">Save</button>
					<button style="float:right; width:80px; margin-left:20px;" id="cancel_edit_staff">Cancel</button>
					<?php 	if($this->session->userdata('username') == "covax13")
							{
								echo "<button style=\"float:left;  width:80px;\" id=\"delete_staff\">Delete</button>";
							}
					?>
					
				</div>
			<?php endif;?>
		</div>
		
					
		
	</body>
	
<div class="javascript_hide">
		<div id="confirm_delete_staff_dialog" title="Delete staff??" style="padding-left:120px;">
		
		<p>Once you delete <b><?=$this_staff['f_name'].' '.$this_staff['l_name']?></b> there is no going back!</p>
		<p>Are you sure you want to DELETE?</p>
		
		<?php $attributes = array('id' => 'delete_staff_form'); ?>
		<?=form_open('staff/delete_staff',$attributes)?>
		<?=form_hidden('staff_id',$this_staff['id'])?>
		</form>
	</div>
	
	<div id="add_staff_dialog" title="Add New staff">
	
		<div id='add_staff_f_name_alert' class='alert'>* A First Name must be entered<br></div>
		<div id='add_staff_l_name_alert' class='alert'>* A Last Name must be entered<br></div>
		<div id='add_staff_phone_num_alert' class='alert'>* A valid 10-digit Phone Number must be entered<br></div>
		<div id='add_staff_phone_carrier_alert' class='alert'>* A Phone Carrier must be selected<br></div>
		<div id='add_staff_email_alert' class='alert'>* A valid Email Address must be entered<br></div>
		<div id='add_staff_username_alert' class='alert'>* A Username must be entered<br></div>
		<div id='add_staff_username_exists_alert' class='alert'>* This Username already exists in the system<br></div>
		<div id='add_staff_password_alert' class='alert'>* A Password must be entered<br></div>
		<div id='add_staff_role_alert' class='alert'>* A Role must be selected<br></div>
		
		<div style="margin:20px;">
			<?php $attributes = array('id' => 'add_staff_form'); ?>
			<?=form_open('staff/add_staff',$attributes)?>
			<?=form_hidden('id',$this_staff['id'])?>
			<table id="staff_view;" style="font-size:12px;">
				<tr>
					<td style="width:200px;">First Name:</td>
					<td>
						<?= form_input('add_staff_f_name','','id="add_staff_f_name"')?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td>Last Name:</td>
					<td>
						<?= form_input('add_staff_l_name','','id="add_staff_l_name"')?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td>Phone Number:</td>
					<td>
						<?= form_input('add_staff_phone_num','','id="add_staff_phone_num"')?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td>Phone Carrier:</td>
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
						<?php echo form_dropdown('add_staff_phone_carrier',$options,'Select','id="add_staff_phone_carrier" style="width:146px; height:21px; float:right; margin-right:2px;"');?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td>Email:</td>
					<td>
						<?= form_input('add_staff_email','','id="add_staff_email"')?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td>Username:</td>
					<td>
						<?= form_input('add_staff_username','','id="add_staff_username"')?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td>Password:</td>
					<td>
						<?= form_input('add_staff_password','','id="add_staff_password"')?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td>Role:</td>
					<td>
						<?php $options = array(
						'Select'  => 'Select',
						'Fleet Manager'  => 'Fleet Manager',
						'Dispatcher'    => 'Dispatcher',
						'Office Manager'    => 'Office Manager',
						'Office Staff'    => 'Office Staff',
						); ?>
						<?php echo form_dropdown('add_staff_role',$options,'select','id="add_staff_role" style="width:146px; height:21px; float:right; margin-right:2px;"');?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
				<tr>
					<td>Status:</td>
					<td>
						<?php $options = array(
						'Active'  	=> 'Active',
						'Inactive'  => 'Inactive',
						); ?>
					<?php echo form_dropdown('add_staff_status',$options,'Active','id="add_staff_status" style="width:146px; height:21px; float:right; margin-right:2px;"');?>
					</td>
					<td style="color:red; width:5px;">
						*
					</td>
				</tr>
			</table>
			</form>
		</div>
	</div>

</div>
</html>