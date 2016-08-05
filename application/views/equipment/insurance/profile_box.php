<script>
	$('#current_since_date').datepicker({ showAnim: 'blind' });
	
	//DIALOG: ADD NEW TRUCK
	$( "#add_additional_insured_dialog" ).dialog(
	{
			autoOpen: false,
			height: 300,
			width: 420,
			modal: true,
			buttons: 
				[
					{
						text: "Add",
						click: function() 
						{
							//VALIDATE ADD QUOTE
							validate_new_additional_insured();
						},//end add load
					},
					{
						text: "Cancel",
						click: function() 
						{
							//RESIZE DIALOG BOX
							$( this ).dialog( "close" );
							
							//RESET ALL FIELDS IN DIALOG FORM
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
</script>
<?php
	//GET AGENT
	$where = null;
	$where["id"] = $ins_profile["agent_id"];
	$agent_company = db_select_company($where);
	
	//GET INSURED
	$where = null;
	$where["id"] = $ins_profile["insured_company_id"];
	$insured_company = db_select_company($where);
	
	//GET INSURER
	$where = null;
	$where["id"] = $ins_profile["insurer_id"];
	$insurer_company = db_select_company($where);
	
	//GET FG CLIENT
	$where = null;
	$where["id"] = $ins_profile["fg_id"];
	$fg_client = db_select_client($where);
	
	//GET FG PERSON
	$where = null;
	$where["id"] = $fg_client["company"]["person_id"];
	$fg_person = db_select_person($where);
	
	//GET ADDITIONAL INSURED
	$where = null;
	$where["ins_profile_id"] = $ins_profile["id"];
	$where["role"] = "Additional Insured";
	$additional_insured_players = db_select_ins_players($where);
	
	//GET PROFILE CURRENT TILL TEXT
	if(empty($ins_profile["profile_current_till"]))
	{
		$profile_current_till_text = "Current";
	}
	else
	{
		$profile_current_till_text = date("m/d/y H:i",strtotime($ins_profile['profile_current_till']));
	}
	
	
	//GET AGENT OPTIONS
	$where = null;
	$where = " type = 'Insurance Agency' AND category = 'Insurance'";
	$where = $where." AND company_status = 'Active'";

	$agents = db_select_companys($where,"company_side_bar_name");
	$agent_dropdown_options = null;
	foreach($agents as $agent)
	{
		$agent_dropdown_options[$agent["id"]] = $agent["company_side_bar_name"];
	}
	
	//GET INSURED OPTIONS
	$where = null;
	$where["company_status"] = "Active";
	$where["type"] = "Carrier";

	$carriers = db_select_companys($where,"company_side_bar_name");
	$carrier_dropdown_options = null;
	foreach($carriers as $carrier)
	{
		$carrier_dropdown_options[$carrier["id"]] = $carrier["company_side_bar_name"];
	}
	
	//GET INSURER OPTIONS
	$where = null;
	$where["type"] = "Insurer";

	$insurers = db_select_companys($where,"company_side_bar_name");
	$insurer_dropdown_options = null;
	foreach($insurers as $insurer)
	{
		$insurer_dropdown_options[$insurer["id"]] = $insurer["company_side_bar_name"];
	}
	
	//GET FG PERSON OPTIONS -- GET CLIENTS WHERE CREDIT SCORE IS NOT NULL
	$where = null;
	$where = "credit_score IS NOT NULL";
	$fg_clients = db_select_clients($where,"credit_score DESC");
	
	$fg_dropdown_options = null;
	//$fg_dropdown_options["Select"] = "Select";
	foreach($fg_clients as $client)
	{
		//GET FG PERSON
		$where = null;
		$where["id"] = $client["company"]["person_id"];
		$fg_person_option = db_select_person($where);
	
		$fg_dropdown_options[$client["id"]] = "(".$client["credit_score"].") ".$fg_person_option["full_name"];
	}
	
	
	
?>
<form id="profile_edit_form" name="profile_edit_form" enctype="multipart/form-data" method="post">
	<input type="hidden" id="ins_profile_id" name="ins_profile_id" value="<?=$ins_profile["id"]?>"/>
	<input type="hidden" id="ins_policy_id" name="ins_policy_id" value="<?=$ins_policy["id"]?>"/>
	<div style="clear:both;"></div>
	<div style="margin-top:10px; padding:10px; width:920px; margin:0 auto;">
		<div style="width:920px;">
			<span class="heading" title="<?=$ins_profile["id"]?>">Profile</span>
			<?php if(empty($ins_policy["policy_cancelled_date"])):?>
				<img id="edit_profile_icon" name="edit_profile_icon" class="profile_details" src="/images/edit.png" style="height:17px; cursor:pointer; float:right;" onclick="edit_profile('<?=$ins_profile["id"]?>')">
			<?php endif;?>
		</div>
	</div>
	<div class="ins_box profile_details" style="width:920px; padding:10px; text-align:center; position:relative; margin:0 auto; z-index:-1; bottom:34px;">
		<?php if(!empty($ins_profile["profile_current_since"])):?>
			<?=date('m/d/y H:i',strtotime($ins_profile["profile_current_since"]))?> to <?=$profile_current_till_text?>
		<?php else:?>	
			No Date Info
		<?php endif;?>
	</div>
	<div class="ins_box profile_edit" style="display:none; width:920px; padding-left:10px; padding-right:10px; padding-top:10px; position:relative; margin:0 auto; z-index:1; bottom:34px; height:24px;">
		<span class="heading" style="float:left;">Profile</span>
		<img id="save_profile_icon" class="profile_edit" style="display:none; height:17px; cursor:pointer; float:right; position:relative; left:1px;" src="/images/save.png" title="Save" onclick="save_profile('<?=$ins_profile["id"]?>');"/>
		<img id="pr_back_icon" class="profile_edit" style="display:none; height:17px; margin-right:10px; cursor:pointer; float:right; position:relative; top:0px;" src="/images/back.png" title="Cancel" onclick="pr_back('<?=$ins_profile["id"]?>');"/>
		<span style="float:right; position:relative; right:300px;">
			Current as of 
			<input class="datepicker" type="text" id="profile_current_since_date" name="current_since_date" style="text-align:center; width:70px; height:20px; font-size:12px;" placeholder="<?=date('m/d/y',strtotime($ins_profile["profile_current_since"]))?>"/>
			<?php
				$options = array();
				for($i=0; $i<=12; $i++)
				{
					if($i<10)
					{
						$minute = "0".$i;
					}
					else
					{
						$minute = $i;
					}
					$options[$minute] = $minute;
				}
			?>
			<?php echo form_dropdown("current_since_hour",$options,"00","id='current_since_hour' class='' style='width:40px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
			<span style="margin-left:-3px; margin-right:-3;">:</span>
			<?php
				$options = array();
				for($i=0; $i<=60; $i++)
				{
					if($i<10)
					{
						$second = "0".$i;
					}
					else
					{
						$second = $i;
					}
					$options[$second] = $second;
				}
			?>
			<?php echo form_dropdown("current_since_minute",$options,"00","id='current_since_minute' class='' style='width:40px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
		</span>
	</div>
	<table style="margin-left:40px;">
		<tr class="heading">
			<td style="width:190px;">
				Agent
			</td>
			<td style="width:210px;">
				Insured
			</td>
			<td style="width:190px;">
				Garaging Address
			</td>
			<td style="width:190px;">
				Mailing Address
			</td>
			<td style="width:140px; text-align:right;">
				Credit Card
			</td>
		</tr>
		<tr>
			<td>
				<span class="profile_details"><?=$agent_company["company_name"]?></span>
				<?php echo form_dropdown("agent_id",$agent_dropdown_options,$agent_company["id"],"id='agent_id' class='profile_edit' style='width:125px; height:20px; font-size:12px; position:relative; bottom:5px; display:none;'");?>
			</td>
			<td style="">
				<span class="profile_details"><?=$insured_company["company_name"]?></span>
				<?php echo form_dropdown("insured_id",$carrier_dropdown_options,$insured_company["id"],"id='insured_id' class='profile_edit' style='width:125px; height:20px; font-size:12px; position:relative; bottom:5px; display:none;'");?>
			</td>
			<td style="max-width:190px;" class="ellipsis" title="<?=$ins_profile["garaging_address"]?>">
				<span class="profile_details"><?=$ins_profile["garaging_address"]?></span>
			</td>
			<td style="max-width:190px;" class="ellipsis" title="<?=$ins_profile["mailing_address"]?>">
				<span class="profile_details"><?=$ins_profile["mailing_address"]?></span>
			</td>
			<td style="text-align:right;">
				<span class="profile_details"><?=$ins_profile["cc_number"]?></span>
				<input type="text" id="cc_number" name="cc_number" class="profile_edit" style="display:none; width:135px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["cc_number"]?>" placeholder="CC Number"/>
			</td>
		</tr>
		<tr>
			<td>
				<span class="profile_details"><?=$agent_company["address"]?></span>
			</td>
			<td>
				<span class="profile_details"><?=$ins_profile["email"]?></span>
			</td>
			<td>
				<span class="profile_details"><?=$ins_profile["garaging_city"]?>, <?=$ins_profile["garaging_state"]?> <?=$ins_profile["garaging_zip"]?></span>
			</td>
			<td>
				<span class="profile_details"><?=$ins_profile["mailing_city"]?>, <?=$ins_profile["mailing_state"]?> <?=$ins_profile["mailing_zip"]?></span>
			</td>
			<td style="text-align:right;">
				<span class="profile_details"><?=$ins_profile["cc_address"]?></span>
				<input type="text" id="cc_address" name="cc_address" class="profile_edit" style="display:none; width:135px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["cc_address"]?>" placeholder="CC Address"/>
			</td>
		</tr>
		<tr>
			<td>
				<span class="profile_details"><?=$agent_company["city"]?>, <?=$agent_company["state"]?> <?=$agent_company["zip"]?></span>
			</td>
			<td>
				<span class="profile_details"><?=$ins_profile["phone"]?></span>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td style="text-align:right;">
				<span class="profile_details"><?=$ins_profile["cc_city"]?>, <?=$ins_profile["cc_state"]?> <?=$ins_profile["cc_zip"]?></span>
				<div class="profile_edit" style="display:none;">
					<input type="text" id="cc_zip" name="cc_zip" class="" style="width:40px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["cc_zip"]?>" placeholder="Zip"/>
					<input type="text" id="cc_state" name="cc_state" class="" style="width:25px; height:20px; font-size:12px; float:right; margin-right:2px;" value="<?=$ins_profile["cc_state"]?>" placeholder="State"/>
					<input type="text" id="cc_city" name="cc_city" class="" style="width:66px; height:20px; font-size:12px; float:right; margin-right:2px;" value="<?=$ins_profile["cc_city"]?>" placeholder="City"/>
				</div>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			<td style="text-align:right;">
				<span class="profile_details">Exp <?=$ins_profile["cc_exp"]?> CVV <?=$ins_profile["cc_cvv"]?></span>
				<div class="profile_edit" style="display:none;">
					<input type="text" id="cc_cvv" name="cc_cvv" class="" style="width:40px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$ins_profile["cc_cvv"]?>" placeholder="CC CVV"/>
					<input type="text" id="cc_exp" name="cc_exp" class="" style="width:93px; height:20px; font-size:12px; float:right; margin-right:2px;" value="<?=$ins_profile["cc_exp"]?>" placeholder="CC Exp"/>
				</div>
			</td>
		</tr>
	</table>
	<table style="margin-left:40px; float:left;">
		<tr class="heading">
			<td style="width:190px;">
				Insurer
			</td>
			<td style="width:210px;">
				Guarantor
			</td>
		</tr>
		<tr>
			<td>
				<span class="profile_details"><?=$insurer_company["company_name"]?></span>
				<?php echo form_dropdown("insurer_id",$insurer_dropdown_options,$insurer_company["id"],"id='insurer_id' class='profile_edit' style='width:125px; height:20px; font-size:12px; position:relative; bottom:5px; display:none;'");?>
			</td>
			<td>
				<span class="profile_details"><?=$fg_person["full_name"]?></span>
				<?php echo form_dropdown("fg_id",$fg_dropdown_options,$ins_profile["fg_id"],"id='fg_id' class='profile_edit' style='width:125px; height:20px; font-size:12px; position:relative; bottom:5px; display:none;'");?>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
				<span class="profile_details"><?=$fg_person["ssn"]?></span>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
				<?php if(!empty($fg_person["date_of_birth"])):?>
					<span class="profile_details"><?=date("m/d/y",strtotime($fg_person["date_of_birth"]))?></span>
				<?php endif;?>
			</td>
		</tr>
	</table>
	<table style="float:left; margin-bottom:30px;">
		<tr class="heading">
			<td style="width:190px;">
				Additional Insured
			</td>
		</tr>
		<?php
			$i = 1;
		?>
		<?php if(!empty($additional_insured_players)):?>
			<?php foreach($additional_insured_players as $ai_player):?>
				<tr>
					<td title="<?=$ai_player["address"]?>, <?=$ai_player["city"]?>, <?=$ai_player["state"]?> <?=$ai_player["zip"]?>">
						<?=$i?>) <?=$ai_player["name"]?>
					</td>
				</tr>
				<?php
					$i++;
				?>
			<?php endforeach;?>
		<?php endif;?>
		<tr class="profile_edit" style="display:none;">
			<td>
				<?=$i?>) <a class="link" onclick="open_additional_insured_dialog('<?=$ins_profile["id"]?>')">Add</a>
			</td>
		</tr>
	</table>
	<table style="float:left; margin-bottom:30px;">
		<tr class="heading">
			<td style="width:26px;">
				Online Account
			</td>
		</tr>
		<tr>
			<td style="">
				<span class="profile_details"><a href="<?=$ins_profile["online_url"]?>"><?=$ins_profile["online_url"]?></a></span>
				<input type="text" id="online_url" name="online_url" class="profile_edit" style="display:none; width:160px; height:20px; font-size:12px;" value="<?=$ins_profile["online_url"]?>" placeholder="URL"/>
			</td>
		</tr>
		<tr>
			<td style="">
				<span class="profile_details"><?=$ins_profile["online_username"]?></span>
				<input type="text" id="online_username" name="online_username" class="profile_edit" style="display:none; width:160px; height:20px; font-size:12px;" value="<?=$ins_profile["online_username"]?>" placeholder="Username"/>
			</td>
		</tr>
		<tr>
			<td style="">
				<span class="profile_details"><?=$ins_profile["online_password"]?></span>
				<input type="text" id="online_password" name="online_password" class="profile_edit" style="display:none; width:160px; height:20px; font-size:12px;" value="<?=$ins_profile["online_password"]?>" placeholder="Password"/>
			</td>
		</tr>
	</table>
	
</form>
<div id="add_additional_insured_dialog" title="Add Additional Insured" style="">
	<form id="add_ai_form">
		<div style="height:30px; width:100%;">
		</div>
		<table style="margin: auto;">
			<tr style="height:30px;">
				<td style="width:95px;">
					Additional Insured
				</td>
				<td>
					<input type="text" id="ai_name" name="ai_name" class="" style="width:160px; height:20px; font-size:12px;" value="" placeholder="Name"/>
				</td>
			</tr>
			<tr style="height:30px;">
				<td>
					Address
				</td>
				<td>
					<input type="text" id="ai_address" name="ai_address" class="" style="width:160px; height:20px; font-size:12px;" value="" placeholder="Address"/>
				</td>
			</tr>
			<tr style="height:30px;">
				<td>
					City
				</td>
				<td>
					<input type="text" id="ai_city" name="ai_city" class="" style="width:160px; height:20px; font-size:12px;" value="" placeholder="City"/>
				</td>
			</tr>
			<tr style="height:30px;">
				<td>
					State
				</td>
				<td>
					<input type="text" id="ai_state" name="ai_state" class="" style="width:160px; height:20px; font-size:12px;" value="" placeholder="State"/>
				</td>
			</tr>
			<tr style="height:30px;">
				<td>
					Zip
				</td>
				<td>
					<input type="text" id="ai_zip" name="ai_zip" class="" style="width:160px; height:20px; font-size:12px;" value="" placeholder="Zip"/>
				</td>
			</tr>
		</table>
	</form>
</div>