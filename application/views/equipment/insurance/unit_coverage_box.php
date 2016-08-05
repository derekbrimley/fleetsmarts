<?php
	$uc_id = $unit_coverage["id"];
?>
<script>
	//DIALOG: ADD NEW TRUCK
	$( "#add_loss_payee_dialog_<?=$uc_id?>").dialog(
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
						validate_new_loss_payee('<?=$uc_id?>');
					},//end add load
				},
				{
					text: "Cancel",
					click: function() 
					{
						$( this ).dialog( "close" );
					}
				}
			],//end of buttons
		
		open: function()
			{
				//RESET ALL FIELDS
				$("#lp_name_<?=$uc_id?>").val('');
				$("#lp_address_<?=$uc_id?>").val('');
				$("#lp_city_<?=$uc_id?>").val('');
				$("#lp_state_<?=$uc_id?>").val('');
				$("#lp_zip_<?=$uc_id?>").val('');
				
			},//end open function
		close: function() 
			{
				//RESET ALL FIELDS
				$("#lp_name_<?=$uc_id?>").val('');
				$("#lp_address_<?=$uc_id?>").val('');
				$("#lp_city_<?=$uc_id?>").val('');
				$("#lp_state_<?=$uc_id?>").val('');
				$("#lp_zip_<?=$uc_id?>").val('');
			}
	});//end dialog form
</script>
<?php
	//echo $unit_coverage["id"];
	$unit = null;
	$unit_number = null;
	
	//GET UNIT
	$where = null;
	$where["id"] = $unit_coverage["unit_id"];
	if($unit_coverage["unit_type"] == "Truck")
	{
		$unit = db_select_truck($where);
		
		$unit_number = $unit["truck_number"];
	}
	else if($unit_coverage["unit_type"] == "Trailer")
	{
		$unit = db_select_trailer($where);
		$unit_number = $unit["trailer_number"];
	}
	
	//GET LOSS PAYEES
	$where = null;
	$where["ins_unit_coverage_id"] = $unit_coverage["id"];
	$where["role"] = "Loss Payee";
	$loss_payee_players = db_select_ins_players($where);
	
	//GET PROFILE CURRENT TILL TEXT
	if(empty($unit_coverage["coverage_current_till"]))
	{
		$coverage_current_till_text = "Current";
	}
	else
	{
		$coverage_current_till_text = date("m/d/y",strtotime($unit_coverage['coverage_current_till']));
	}
	
	//GET OPTIONS FOR TRUCKS
	$where = null;
	$where["dropdown_status"] = "Show";
	$trucks = db_select_trucks($where,"truck_number");
	
	$truck_options = null;
	$truck_options["Select"] = "Select";
	foreach($trucks as $truck)
	{
		$truck_options[$truck["id"]] = $truck["truck_number"];
	}
	
	//GET TRAILERS FOR TRAILER OPTIONS
	$where = null;
	$where["dropdown_status"] = "Show";
	$trailers = db_select_trailers($where,"trailer_number");
	
	$trailer_options = null;
	$trailer_options["Select"] = "Select";
	foreach($trailers as $trailer)
	{
		$trailer_options[$trailer["id"]] = $trailer["trailer_number"];
	}
?>
<form id="uc_edit_form_<?=$uc_id?>" name="uc_edit_form" enctype="multipart/form-data" method="post">
	<input type="hidden" id="ins_unit_coverage_id_<?=$uc_id?>" name="ins_unit_coverage_id" value="<?=$unit_coverage["id"]?>"/>
	<div style="padding:10px; width:920px; margin:0 auto;">
		<div style="width:920px;">
			<span class="heading" title="<?=$uc_id?>">Unit Coverage - VIN <?=$unit["vin"]?></span>
			<?php if(empty($ins_policy["policy_cancelled_date"])):?>
				<image id="edit_profile_icon_<?=$uc_id?>" name="edit_profile_icon" class="uc_details_<?=$uc_id?>" src="/images/edit.png" style="height:17px; cursor:pointer; float:right;" onclick="edit_unit_coverage('<?=$unit_coverage["id"]?>')">
			<?php endif;?>
		</div>
	</div>
	<div class="ins_box uc_details_<?=$uc_id?>" style="width:920px; padding:10px; text-align:center; position:relative; margin:0 auto; z-index:-1; bottom:34px;">
		<?=date('m/d/y H:i',strtotime($unit_coverage["coverage_current_since"]))?> to <?=$coverage_current_till_text?>
	</div>
	<div class="ins_box uc_edit_<?=$uc_id?>" style="display:none; width:920px; padding-left:10px; padding-right:10px; padding-top:10px; position:relative; margin:0 auto; z-index:1; bottom:34px; height:24px;">
		<span class="heading">Unit Coverage - VIN <?=$unit["vin"]?></span>
		<div style="width:200px; float:right;">
			<img id="save_profile_icon" class="uc_edit_<?=$uc_id?>" style="display:none; height:17px; cursor:pointer; float:right; position:relative; left:1px;" src="/images/save.png" title="Save" onclick="save_unit_coverage('<?=$unit_coverage["id"]?>');"/>
			<?php if($unit_coverage["unit_type"] == "Unknown"):?>
				<img id="delete_unit_coverage_icon" class="uc_edit_<?=$uc_id?>" style="display:none; height:17px; cursor:pointer; float:right; position:relative; left:1px; margin-right:15px;" src="/images/trash.png" title="Save" onclick="delete_unit_coverage('<?=$unit_coverage["id"]?>');"/>
			<?php elseif($unit_coverage["unit_type"] == "Truck"):?>
				<img id="remove_truck" class="uc_edit_<?=$uc_id?>" style="display:none; height:20px; margin-right:10px; cursor:pointer; float:right; position:relative; bottom:1px;" src="/images/remove_truck_icon.png" title="Remove Truck" onclick="remove_unit_coverage('<?=$unit_coverage["id"]?>');"/>
			<?php elseif($unit_coverage["unit_type"] == "Trailer"):?>
				<img id="remove_truck" class="uc_edit_<?=$uc_id?>" style="display:none; height:20px; margin-right:10px; cursor:pointer; float:right; position:relative; bottom:1px;" src="/images/remove_trailer_icon.png" title="Remove Trailer" onclick="remove_unit_coverage('<?=$unit_coverage["id"]?>');"/>
			<?php endif;?>
				<img id="duplicate_unit_coverage" class="uc_edit_<?=$uc_id?>" style="display:none; height:17px; margin-right:10px; cursor:pointer; float:right; position:relative; top:0px;" src="/images/grey_copy_icon.png" title="Duplicate Unit Coverage" onclick="load_duplicate_uc_dialog('<?=$unit_coverage["id"]?>');"/>
				<img id="uc_back_icon" class="uc_edit_<?=$uc_id?>" style="display:none; height:17px; margin-right:10px; cursor:pointer; float:right; position:relative; top:0px;" src="/images/back.png" title="Cancel" onclick="uc_back('<?=$unit_coverage["id"]?>');"/>
		</div>
		<span style="float:right; position:relative; right:147px;">
			Current as of 
			<input class="datepicker" type="text" id="current_since_date_<?=$uc_id?>" name="current_since_date" style="text-align:center; width:70px; height:20px; font-size:12px;" placeholder="<?=date('m/d/y',strtotime($unit_coverage["coverage_current_since"]))?>"/>
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
			<?php echo form_dropdown("current_since_hour",$options,"00","id='current_since_hour_$uc_id' class='' style='width:40px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
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
	<table style="margin-left:40px; float:left;">
		<tr class="heading">
			<td>
				Unit
			</td>
		</tr>
		<tr>
			<td>
				Unit #
			</td>
			<td>
				<span class="uc_details_<?=$uc_id?>"><?=$unit_number?></span>
				<?php echo form_dropdown("truck_id",$truck_options,$unit_coverage["unit_id"],"id='truck_id_$uc_id' class='uc_edit_$uc_id' style='width:80px; height:20px; font-size:12px; position:relative; bottom:5px; display:none;'");?>
				<?php echo form_dropdown("trailer_id",$trailer_options,$unit_coverage["unit_id"],"id='trailer_id_$uc_id' class='uc_edit_$uc_id' style='width:80px; height:20px; font-size:12px; position:relative; bottom:5px; display:none;'");?>
			</td>
		</tr>
		<tr>
			<td style="width: 50px;">
				Type
			</td>
			<td style="width: 80px;">
				<span class="uc_details_<?=$uc_id?>"><?=$unit_coverage["unit_type"]?></span>
				<?php
					$options = array(
						"Truck"		=>	"Truck",
						"Trailer"	=>	"Trailer"
					);
				?>
				<?php echo form_dropdown("unit_type",$options,$unit_coverage["unit_type"],"id='unit_type_$uc_id' class='uc_edit_$uc_id' style='width:80px; height:20px; font-size:12px; position:relative; bottom:5px; display:none;' onchange='edit_unit_coverage($uc_id)'");?>
			</td>
		</tr>
		<tr>
			<td style="">
				Value
			</td>
			<td style="">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["pd_limit"])?></span>
				<input type="text" id="pd_limit_<?=$uc_id?>" name="pd_limit" class="uc_edit_<?=$uc_id?>" style="display:none; width:80px; height:20px; font-size:12px; float:right;" value="<?=$unit_coverage["pd_limit"]?>" placeholder="Value"/>
			</td>
		</tr>
		<tr>
			<td style="">
				Make
			</td>
			<td style="">
				<span class="uc_details_<?=$uc_id?>"><?=$unit["make"]?></span>
			</td>
		</tr>
		<tr>
			<td style="">
				Model
			</td>
			<td style="">
				<span class="uc_details_<?=$uc_id?>"><?=$unit["model"]?></span>
			</td>
		</tr>
		<tr>
			<td style="">
				Year
			</td>
			<td style="">
				<span class="uc_details_<?=$uc_id?>"><?=$unit["year"]?></span>
			</td>
		</tr>
		<tr>
			<td style="">
				Radius
			</td>
			<td style="">
				<span class="uc_details_<?=$uc_id?>"><?=$unit_coverage["radius"]?></span>
				<input type="text" id="radius_<?=$uc_id?>" name="radius" class="uc_edit_<?=$uc_id?>" style="display:none; width:80px; height:20px; font-size:12px; float:right;" value="<?=$unit_coverage["radius"]?>" placeholder="Radius"/>
			</td>
		</tr>
	</table>
	<table style="margin-left:60px; margin-bottom:20px; float:left;">
		<tr class="heading">
			<td>
				Auto Liability
			</td>
		</tr>
		<tr>
			<td style="width:110px;">
				UM BI Limit
			</td>
			<td style="width:75px; text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["al_um_bi_limit"])?></span>
				<input type="text" id="al_um_bi_limit_<?=$uc_id?>" name="al_um_bi_limit" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["al_um_bi_limit"]?>" placeholder="Limit"/>
			</td>
		</tr>
		<tr>
			<td>
				UIM BI Limit
			</td>
			<td style="text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["al_uim_bi_limit"])?></span>
				<input type="text" id="al_uim_bi_limit_<?=$uc_id?>" name="al_uim_bi_limit" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["al_uim_bi_limit"]?>" placeholder="Limit"/>
			</td>
		</tr>
		<tr>
			<td>
				PIP Limit
			</td>
			<td style="text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["al_pip_limit"])?></span>
				<input type="text" id="al_pip_limit_<?=$uc_id?>" name="al_pip_limit" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["al_pip_limit"]?>" placeholder="Limit"/>
			</td>
		</tr>
		<tr>
			<td style="width:70px;">
				Liability Premium
			</td>
			<td style="width:70px; text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["al_prem"])?></span>
				<input type="text" id="al_prem_<?=$uc_id?>" name="al_prem" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["al_prem"]?>" placeholder="Premium"/>
			</td>
		</tr>
		<tr>
			<td>
				UM BI Premium
			</td>
			<td style="text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["al_um_bi_prem"])?></span>
				<input type="text" id="al_um_bi_prem_<?=$uc_id?>" name="al_um_bi_prem" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["al_um_bi_prem"]?>" placeholder="Premium"/>
			</td>
		</tr>
		<tr>
			<td>
				UIM BI Premium
			</td>
			<td style="text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["al_uim_bi_prem"])?></span>
				<input type="text" id="al_uim_bi_prem_<?=$uc_id?>" name="al_uim_bi_prem" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["al_uim_bi_prem"]?>" placeholder="Premium"/>
			</td>
		</tr>
		<tr>
			<td>
				PIP Premium
			</td>
			<td style="text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["al_pip_prem"])?></span>
				<input type="text" id="al_pip_prem_<?=$uc_id?>" name="al_pip_prem" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["al_pip_prem"]?>" placeholder="Premium"/>
			</td>
		</tr>
	</table>
	<table style="margin-left:75px; float:left;">
		<tr class="heading">
			<td>
				Phys Dam
			</td>
		</tr>
		<tr>
			<td style="width: 80px;">
				Comp Ded
			</td>
			<td style="width:70px; text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["pd_comp_ded"])?></span>
				<input type="text" id="pd_comp_ded_<?=$uc_id?>" name="pd_comp_ded" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["pd_comp_ded"]?>" placeholder="Deductible"/>
			</td>
		</tr>
		<tr>
			<td>
				Comp Prem
			</td>
			<td style="text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["pd_comp_prem"])?></span>
				<input type="text" id="pd_comp_prem_<?=$uc_id?>" name="pd_comp_prem" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["pd_comp_prem"]?>" placeholder="Premium"/>
			</td>
		</tr>
		<tr>
			<td>
				Coll Ded
			</td>
			<td style="text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["pd_coll_ded"])?></span>
				<input type="text" id="pd_coll_ded_<?=$uc_id?>" name="pd_coll_ded" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["pd_coll_ded"]?>" placeholder="Deductible"/>
			</td>
		</tr>
		<tr>
			<td>
				Coll Prem
			</td>
			<td style="text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["pd_coll_prem"])?></span>
				<input type="text" id="pd_coll_prem_<?=$uc_id?>" name="pd_coll_prem" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["pd_coll_prem"]?>" placeholder="Premium"/>
			</td>
		</tr>
	</table>
	<table style="margin-left:60px; float:left;">
		<tr class="heading">
			<td style="70px" colspan="2">
				Rental Reimburse
			</td>
		</tr>
		<tr>
			<td style="width:80px;">
				Daily Limit
			</td>
			<td style="width:70px; text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["pd_rental_daily_limit"])?></span>
				<input type="text" id="pd_rental_daily_limit_<?=$uc_id?>" name="pd_rental_daily_limit" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["pd_rental_daily_limit"]?>" placeholder="Limit"/>
			</td>
		</tr>
		<tr>
			<td style="width:70px;">
				Max Limit
			</td>
			<td style="text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["pd_rental_max_limit"])?></span>
				<input type="text" id="pd_rental_max_limit_<?=$uc_id?>" name="pd_rental_max_limit" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["pd_rental_max_limit"]?>" placeholder="Limit"/>
			</td>
		</tr>
		<tr>
			<td style="width:70px;">
				Premium
			</td>
			<td style="text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["pd_rental_prem"])?></span>
				<input type="text" id="pd_rental_prem_<?=$uc_id?>" name="pd_rental_prem" class="uc_edit_<?=$uc_id?>" style="display:none; width:70px; height:20px; text-align:right; font-size:12px; float:right;" value="<?=$unit_coverage["pd_rental_prem"]?>" placeholder="Premium"/>
			</td>
		</tr>
	</table>
	<table style="margin-left:30px; float:left;">
		<tr class="heading">
			<td style="text-align:right; width:70px;">
				Total Cost
			</td>
		</tr>
		<tr>
			<td style="font-size:14px; width: 70px; text-align:right;">
				<span class="uc_details_<?=$uc_id?>">$<?=number_format($unit_coverage["al_um_bi_prem"]+$unit_coverage["al_uim_bi_prem"]+$unit_coverage["al_pip_prem"]+$unit_coverage["pd_comp_prem"]+$unit_coverage["pd_coll_prem"]+$unit_coverage["pd_rental_prem"]+$unit_coverage["al_prem"])?></span>
			</td>
		</tr>
	</table>
	<div style="clear:both; margin-left:40px; margin-bottom:20px; float:left;">
		<div class="heading" style="margin-bottom:5px;">Loss Payees</div>
		<?php
			$i = 1;
		?>
		<?php if(!empty($loss_payee_players)):?>
			<?php foreach($loss_payee_players as $lp_player):?>
				<div style="float:left; margin-right:15px;" title="<?=$lp_player["address"]?>, <?=$lp_player["city"]?>, <?=$lp_player["state"]?> <?=$lp_player["zip"]?>"><?=$i?>) <?=$lp_player["name"]?></div>
				<?php
					$i++;
				?>
			<?php endforeach;?>
		<?php endif;?>
		<div style="float:left; display:none;" class="uc_edit_<?=$uc_id?>" style="display:none;"><?=$i?>) <a class="link" onclick="open_loss_payee_dialog('<?=$uc_id?>')">Add</a></div>
	</div>
</form>
<div id="add_loss_payee_dialog_<?=$uc_id?>" title="Add Loss Payee">
	<form id="add_lp_form_<?=$uc_id?>">
		<div style="height:30px; width:100%;">
		</div>
		<table style="margin: auto;">
			<tr style="height:30px;">
				<td style="width:90px;">
					Loss Payee
				</td>
				<td>
					<input type="text" id="lp_name_<?=$uc_id?>" name="lp_name" class="" style="width:160px; height:20px; font-size:12px;" value="" placeholder="Name"/>
				</td>
			</tr>
			<tr style="height:30px;">
				<td>
					Address
				</td>
				<td>
					<input type="text" id="lp_address_<?=$uc_id?>" name="lp_address" class="" style="width:160px; height:20px; font-size:12px;" value="" placeholder="Address"/>
				</td>
			</tr>
			<tr style="height:30px;">
				<td>
					City
				</td>
				<td>
					<input type="text" id="lp_city_<?=$uc_id?>" name="lp_city" class="" style="width:160px; height:20px; font-size:12px;" value="" placeholder="City"/>
				</td>
			</tr>
			<tr style="height:30px;">
				<td>
					State
				</td>
				<td>
					<input type="text" id="lp_state_<?=$uc_id?>" name="lp_state" class="" style="width:160px; height:20px; font-size:12px;" value="" placeholder="State"/>
				</td>
			</tr>
			<tr style="height:30px;">
				<td>
					Zip
				</td>
				<td>
					<input type="text" id="lp_zip_<?=$uc_id?>" name="lp_zip" class="" style="width:160px; height:20px; font-size:12px;" value="" placeholder="Zip"/>
				</td>
			</tr>
		</table>
	</form>
</div>