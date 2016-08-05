<script>
	//CREATE BROKERS ARRAY
	var rcr_broker_auto_complete = [
	<?php 	
			$array_string = "";
			foreach($brokers as $broker)
			{
				$broker_name = $broker['customer_name'];
				$array_string = $array_string.'"'.$broker_name.'",';
			}
			echo substr($array_string,0,-1);
	?>];
	
	$(document).ready(function(){	
	
	
		//ADD DATE PICKERS TO PICKS AND DROPS
		for(i=1;i<=5;i++)
		{
			$('#rcr_pick_date_'+i).datepicker({ showAnim: 'blind' });
			$('#rcr_drop_date_'+i).datepicker({ showAnim: 'blind' });
		}
		
		$( "#rcr_broker" ).autocomplete({
			source: rcr_broker_auto_complete
		});
		
		//FOR EACH PICK AND DROP GET TIME (THIS IS NOT CURRENTLY USED)
		for(var ii=1;ii<=10;ii++)
		{
			pd = 'pick';
			var i = ii;
			if (ii>5)
			{
				i = ii-5;
				pd = 'drop';
				
			}
			
			var number = i;
			
			var hour = $("#rcr_"+pd+"_app_hour_"+number);
			var minute = $("#rcr_"+pd+"_app_minute_"+number).val();
			var ampm = $("#rcr_"+pd+"_app_ampm_"+number).val();
			var timezone = $("#rcr_"+pd+"_app_timezone_"+number).val();
			
		}
		
		
	});//END DOCUMENT READY
	
	var last_visible_pick = 2;
	var last_visible_drop = 2;
	
	function rcr_add_pick()
	{
		$("#rcr_pick_div_"+last_visible_pick).show();
		last_visible_pick = last_visible_pick + 1;
	}
	function rcr_add_drop()
	{
		$("#rcr_drop_div_"+last_visible_drop).show();
		last_visible_drop = last_visible_drop + 1;
	}
	
	function clear_pd_app_time(pd,number)
	{
		$("#rcr_"+pd+"_app_hour_"+number).val("--");
		$("#rcr_"+pd+"_app_minute_"+number).val("--");
		$("#rcr_"+pd+"_app_ampm_"+number).val("--");
		$("#rcr_"+pd+"_app_timezone_"+number).val("--");
	}
	
	function uncheck_tbd(pd,number)
	{
		$("#rcr_"+pd+"_app_time_tba_"+number).attr('checked', false);

	}




</script>
<style>
	#rate_con_received_dialog hr
	{
		width:450px;
	}
</style>
<?php $attributes = array('name'=>'rcr_save_form','id'=>'rcr_save_form', 'target'=>'_blank'  )?>
<?=form_open_multipart('loads/rcr_save',$attributes);?>
	<input type="hidden" id="previous_load_number" name="previous_load_number" value="<?=$load["internal_load_number"]?>" />
	<input type="hidden" id="load_id" name="load_id" value="<?=$load["id"]?>" />
	
	<?php $text_box_style = 'margin-left:2px;width:196px;'; ?>
	<span class="heading">Billing Info</span>
	<hr/>
	<div id='rcr_no_client_alert' class='alert'>* Client must be selected<br></div>
	<div id='rcr_no_billed_under_alert' class='alert'>* Billed Under must be selected<br></div>
	<div id='rcr_no_billing_method_alert' class='alert'>* Billing Method must be selected<br></div>
	<div id='rcr_no_rc_link_alert' class='alert'>* Rate Con link must be entered<br></div>
	<div id='rcr_natl_fuel_avg_nan_alert' class='alert'>* Natl Fuel Avg must be a number<br></div>
	
	<?php $text_box_style = 'margin-left:2px;width:196px;'; ?>
	<table style="font-size:12px;">
		
		<tr>
			<td style="width:220px;" valign="top">Billed Under</td>
			<td>
				<?php echo form_dropdown('rcr_billed_under_dropdown',$billed_under_options,$load['billed_under'],"id='rcr_billed_under_dropdown' style='$text_box_style'");?>
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td valign="top">Billing Method</td>
			<td>
				<?php
					$bm_options["Select"] = "Select";
					$bm_options["Factor"] = "Factor";
					$bm_options["Direct Bill"] = "Direct Bill";
				?>
				<?php echo form_dropdown('rcr_billing_method_dropdown',$bm_options,$load['billing_method'],"id='rcr_billing_method_dropdown' style='$text_box_style'");?>
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td style="width:220px;" valign="top">Originals Required?</td>
			<td>
				<?php $options = array(
				  'Yes'          => 'Yes',
				  'No'		=> 'No',
				);?>
				<?=form_dropdown("rcr_originals_required",$options,$load["originals_required"],"id='rcr_originals_required' style='$text_box_style' onchange='rcr_orignals_required_selected()'");?>
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr id="original_proof_row" style="">
			<td style="width:220px;" valign="top">Proof of No Originals Required</td>
			<td style="padding-top:5px;">
				<input type="file" id="rcr_proof_of_no_org" name="rcr_proof_of_no_org" style="width:200px; position:relative; left:2px; bottom:5px;" />
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td style="width:220px;" valign="top">Nat'l Fuel Avg</td>
			<td>
				<?php $data = array(
				  'name'        => 'natl_fuel_avg',
				  'id'          => 'natl_fuel_avg',
				  'style'		=> $text_box_style,
				  'value'		=> $natl_fuel_avg
				);?>
				<?=form_input($data);?> 
			</td>
			<td valign="top" style="color:red">* <a target="_blank" href="http://fuelgaugereport.aaa.com/todays-gas-prices/">AAA</a></td>
		</tr>
	</table>
	<br>
	<span class="heading">Load Info</span>
	<hr/>
	<div id='rcr_load_number_alert' class='alert'>* Load Number must be entered<br></div>
	<div id='rcr_no_fleet_manager_alert' class='alert'>* Fleet Manager must be selected<br></div>
	<div id='rcr_no_broker_alert' class='alert'>* Broker must be entered<br></div>
	<div id='rcr_bad_broker_alert' class='alert'>* The Broker you have entered cannot be found in the system<br></div>
	<div id='rcr_contact_info_alert' class='alert'>* Contact Info must be entered<br></div>
	<div id='rcr_expected_revenue_alert' class='alert'>* Expected Revenue must be entered and be a number<br></div>

	<?php $text_box_style = 'margin-left:2px;width:196px;'; ?>
	<table style="font-size:12px;">
		<tr>
			<td style="width:220px;" valign="top">Load Number</td>
			<td>
				<?php $data = array(
				  'name'        => 'rcr_load_number',
				  'id'          => 'rcr_load_number',
				  'style'		=> $text_box_style
				);?>
				<?=form_input($data);?> 
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td valign="top">Fleet Manager</td>
			<td>
				<?php echo form_dropdown('rcr_fleet_manager_dropdown',$fleet_manager_dropdown_options,$load['fleet_manager_id'],"id='rcr_fleet_manager_dropdown' style='$text_box_style'");?>
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td valign="top">Driver Manager</td>
			<td>
				<?php echo form_dropdown('rcr_driver_manager_dropdown',$dm_dropdown_options,$load['dm_id'],"id='rcr_driver_manager_dropdown' style='$text_box_style'");?>
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td valign="top">Broker</td>
			<td>
				<?php $data = array(
				  'name'        => 'rcr_broker',
				  'id'          => 'rcr_broker',
				  'value'		=> $load["broker"]["customer_name"],
				  'style'		=> $text_box_style
				);?>
				<?=form_input($data);?> 
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td valign="top">Contact Info</td>
			<td style="padding-bottom:10px;">
				<textarea style="position:relative; left:2px; " name="rcr_contact_info" id="rcr_contact_info" rows="3" cols="25" value="<?=$load["contact_info"]?>"></textarea>
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td valign="top">Expected Revenue</td>
			<td style="padding-bottom:10px;">
				<?php $data = array(
				  'name'        => 'rcr_expected_revenue',
				  'id'          => 'rcr_expected_revenue',
				  'value'		=> $load["expected_revenue"],
				  'style'		=> $text_box_style
				);?>
				<?=form_input($data);?> 
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td valign="top">Load Description</td>
			<td style="padding-bottom:10px;">
				<?php $data = array(
				  'name'        => "rcr_load_notes",
				  'id'          => "rcr_load_notes",
				  'value'		=> $load["load_desc"],
				  'rows'		=> '3',
				  'cols'		=> '25',
				  'style'		=> 'position:relative; left:2px;'
				);?>
				<?=form_textarea($data);?>
			</td>
		</tr>
		<tr>
			<td style="width:220px;" valign="top">Rate Con</td>
			<td style="padding-top:5px;">
				<?php if(!empty($load["rc_link"])):?>
					<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$load["rc_link"]?>">RC</a> 
				<?php endif;?>
				<input type="file" id="rc_save_attachment_file" name="rc_save_attachment_file" style="width:200px; position:relative; left:2px; bottom:5px;" />
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td style="width:220px;" valign="top">Load Type</td>
			<td>
				<?php $options = array(
				  'Full Load'        => 'Full Load',
				  'Partial'          => 'Partial',
				  'Power Only'		=> 'Power Only',
				);?>
				<?=form_dropdown("load_type",$options,'Full Load',"id='load_type' style='$text_box_style'");?>
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td style="width:220px;" valign="top">Freight Type</td>
			<td>
				<?php $options = array(
					'Reefer' => 'Reefer',
					'Dry' => 'Dry',
					); 
				?>
				<?php echo form_dropdown('rcr_is_reefer',$options,$load['is_reefer'],"id='rcr_is_reefer' class='' style='$text_box_style' onchange='rcr_freight_type_selected()'");?>
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr id="rcr_reefer_temp_row">
			<td style="width:220px;" valign="top">Reefer Temp Range</td>
			<td>
				<div class="">
					<div style="width:80px; text-align:center; float:left;">
						<input type="text" class="edit_input" style="width:80px; text-align:center; " id="rcr_reefer_low_set" name="rcr_reefer_low_set" value="<?=$load["reefer_low_set"]?>"/>
					</div>
					<div style="margin-left:14px; margin-right:14px; float:left;">
						to
					</div>
					<div style="width:80px; text-align:center; float:left;">
						<input type="text" class="edit_input" style="width:80px; text-align:center;" id="rcr_reefer_high_set" name="rcr_reefer_high_set" value="<?=$load["reefer_high_set"]?>"/>
					</div>
				</div>
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td valign="top">Driver</td>
			<td>
				<?php echo form_dropdown('rcr_client_dropdown',$clients_dropdown_options,$load['client_id'],"id='rcr_client_dropdown' style='$text_box_style'");?>
			</td>
			<td valign="top" style="color:red">*</td>
		</tr>
		<tr>
			<td style="width:220px;" valign="top">Truck</td>
			<td>
				<?=form_dropdown("rcr_truck_id",$truck_dropdown_options,$load["load_truck_id"],"id='rcr_truck_id' style='$text_box_style'");?>
			</td>
			<td valign="top" style="color:red"></td>
		</tr>
		<tr>
			<td style="width:220px;" valign="top">Trailer</td>
			<td>
				<?=form_dropdown("rcr_trailer_id",$trailer_dropdown_options,$load["load_trailer_id"],"id='rcr_trailer_id' style='$text_box_style'");?>
			</td>
			<td valign="top" style="color:red"></td>
		</tr>
	</table>


	<?php $i = 1; ?>
	<?php foreach($picks as $this_pick):?>
		<input type="hidden" id="previous_pick_pd_number_<?=$i?>" name="previous_pick_pd_number_<?=$i?>" value="<?=$this_pick["pick_number"]?>" />
		<input type="hidden" id="pick_id_<?=$i?>" name="pick_id_<?=$i?>" value="<?=$this_pick["id"]?>" />
		<input type="hidden" id="pick_stop_id_<?=$i?>" name="pick_stop_id_<?=$i?>" value="<?=$this_pick["stop"]["id"]?>" />
		<?php 	$div_style = "display:none;"; ?>
		<?php 	if(!empty($this_pick["appointment_time"])): 		?>
		<?php		$div_style = ""; 					?>
					<script>
						last_visible_pick = <?=$i+1?>;
					</script>
		<?php 	endif;
				if($i == 1)
				{
					$div_style = "";
				}
		?>
		<div id="rcr_pick_div_<?=$i?>" style="<?=$div_style?>">
			<?php 	
				if ($this_pick["appointment_time"] == null)
				{
					$this_date = "";
				}
				else
				{
					$this_date = date("n/d/y",strtotime($this_pick["appointment_time"]));
				}
			?>
			<br>
			<span class="heading">Pick <?=$i?></span>
			<hr/>
			<table style="font-size:12px;">
				<tr>
					<td style="width:220px;"  valign="top">Date</td>
					<td>
						<?php $data = array(
						  'name'        => "rcr_pick_date_$i",
						  'id'          => "rcr_pick_date_$i",
						  'value'		=> $this_date,
						  'style'		=> $text_box_style
						);?>
						<?=form_input($data);?> 
						<td valign="top" style="color:red">*</td>
					</td>
				</tr>
				<tr>
					<td valign="top">Appointment Time</td>
					<td VALIGN="top" style="width:203px;" >
						<?php 	$hours = array(
											  '--'  => '--',
											  '1'  => '1',
											  '2'   => '2',
											  '3'   => '3',
											  '4' => '4',
											  '5' => '5',
											  '6' => '6',
											  '7' => '7',
											  '8' => '8',
											  '9' => '9',
											  '10' => '10',
											  '11' => '11',
											  '12' => '12',
											);
								$minutes = array(
											  '--'  => '--',
											  '00'  => '00',
											  '05'  => '05',
											  '10'   => '10',
											  '15'   => '15',
											  '20' => '20',
											  '25' => '25',
											  '30' => '30',
											  '35' => '35',
											  '40' => '40',
											  '45' => '45',
											  '50' => '50',
											  '55' => '55',
											);
								$timezones = array(
											  "---"	=> "---",
											  '1' 	=> "PST",
											  '0'	=> "MST",
											  "-1"	=> "CST",
											  "-2"	=> "EST"
											);
								
								
								if (!empty($this_pick["appointment_time"]) && date("s",strtotime($this_pick["appointment_time"])) != "01")
								{
									$this_hour = date("h",strtotime($this_pick["appointment_time"]));
									$this_minute = date("i",strtotime($this_pick["appointment_time"]));
									$this_ampm = strtolower(date("A",strtotime($this_pick["appointment_time"])));
									$this_timezone = $this_pick['appointment_time_mst'] - $this_pick['appointment_time'];
								}
								else
								{
									$this_hour = "--";
									$this_minute = "--";
									$this_ampm = "--";
									$this_timezone = "---";
								}
						?>
						<?php echo form_dropdown("rcr_pick_app_hour_$i\" onchange=\"uncheck_tbd('pick',$i)",$hours,$this_hour,"  id='rcr_pick_app_hour_$i' style='width:41px;margin-top:2px; margin-left:2px; margin-right:3px;'");?>:
						<?php echo form_dropdown("rcr_pick_app_minute_$i\" onchange=\"uncheck_tbd('pick',$i)",$minutes,$this_minute,"id='rcr_pick_app_minute_$i' style='width:41px;margin-top:2px; margin-right:1px;'");?>
						<?php echo form_dropdown("rcr_pick_app_ampm_$i\" onchange=\"uncheck_tbd('pick',$i)",array('--'=>'--','am'=>'AM','pm'=>'PM'),$this_ampm,"id='rcr_pick_app_ampm_$i' style='width:44px;margin-top:2px;margin-right:2px;'");?>
						<?php echo form_dropdown("rcr_pick_app_timezone_$i\" onchange=\"uncheck_tbd('pick',$i)",$timezones,$this_timezone,"id='rcr_pick_app_timezone_$i' style='width:52px;margin-top:2px;'");?>
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td valign="top">Appointment Time TBD</td>
					<td>
						<input type="checkbox" id="rcr_pick_app_time_tba_<?=$i?>" name="rcr_pick_app_time_tba_<?=$i?>" onclick="clear_pd_app_time('pick','<?=$i?>')"/>
					</td>
				</tr>
				<tr>
					<td valign="top">Location Name</td>
					<td>
						<?php $data = array(
						  'name'        => "rcr_pick_location_$i",
						  'id'          => "rcr_pick_location_$i",
						  'value'		=> $this_pick["stop"]["location_name"],
						  'style'		=> $text_box_style
						);?>
						<?=form_input($data);?> 
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td  valign="top">GPS</td>
					<td>
						<input type="text" id="rcr_pick_gps_<?=$i?>" name="rcr_pick_gps_<?=$i?>" style="<?=$text_box_style?>" onblur="fill_in_rcr_address_fields('pick','<?=$i?>')"/>
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td  valign="top">Street Address</td>
					<td style="padding-bottom:10px;">
						<textarea style="position:relative; left:2px; " name="rcr_pick_address_<?=$i?>" id="rcr_pick_address_<?=$i?>" rows="3" cols="25" value="<?=$this_pick["stop"]["address"]?>"></textarea>
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td valign="top">City</td>
					<td>
						<?php $data = array(
						  'name'        => "rcr_pick_city_$i",
						  'id'          => "rcr_pick_city_$i",
						  'value'		=> $this_pick["stop"]["city"],
						  'style'		=> $text_box_style
						);?>
						<?=form_input($data);?> 
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td valign="top">State</td>
					<td>
						<?php $data = array(
						  'name'        => "rcr_pick_state_$i",
						  'id'          => "rcr_pick_state_$i",
						  'value'		=> $this_pick["stop"]["state"],
						  'style'		=> $text_box_style
						);?>
						<?=form_input($data);?> 
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td  valign="top">PU Number</td>
					<td>
						<?php $data = array(
						  'name'        => "rcr_pick_pu_number_$i",
						  'id'          => "rcr_pick_pu_number_$i",
						  'value'		=> $this_pick["pu_number"],
						  'style'		=> $text_box_style
						);?>
						<?=form_input($data);?> 
					</td>
					<td valign="top" style="color:red"></td>
				</tr>
				<tr>
					<td  valign="top">Notes</td>
					<td style="padding-bottom:10px;">
						<?php $data = array(
						  'name'        => "rcr_pick_dispatch_notes_$i",
						  'id'          => "rcr_pick_dispatch_notes_$i",
						  'value'		=> $this_pick["dispatch_notes"],
						  'rows'		=> '3',
						  'cols'		=> '25',
						  'style'		=> 'position:relative; left:2px;'
						);?>
						<?=form_textarea($data);?>
					</td>
				</tr>
			</table>
		</div>
		<?php $i++; ?>
	<?php endforeach;?>
	<br>
	<a href="javascript:void(0);" style="" onclick="rcr_add_pick()">+ Add Pick</a> 

	<?php $i = 1; ?>
	<?php foreach($drops as $this_drop):?>
		<input type="hidden" id="previous_drop_pd_number_<?=$i?>" name="previous_drop_pd_number_<?=$i?>" value="<?=$this_drop["drop_number"]?>" />
		<input type="hidden" id="drop_id_<?=$i?>" name="drop_id_<?=$i?>" value="<?=$this_drop["id"]?>" />
		<input type="hidden" id="drop_stop_id_<?=$i?>" name="drop_stop_id_<?=$i?>" value="<?=$this_drop["stop"]["id"]?>" />
		<?php 	$div_style = "display:none;"; 			?>
		<?php 	if(!empty($this_drop["appointment_time"])): 		?>
		<?php		$div_style = ""; 					?>
					<script>
						last_visible_drop = <?=$i+1?>;
					</script>
		<?php 	endif;
				if($i == 1)
				{
					$div_style = "";
				}
		?>
		<div id="rcr_drop_div_<?=$i?>" style="<?=$div_style?>">
			<?php 	if ($this_drop["appointment_time"] == null)
					{
						$this_date = "";
					}
					else
					{
						$this_date = date("n/d/y",strtotime($this_drop["appointment_time"]));
					}
			?>
			<br>
			<span class="heading">Drop <?=$i?></span>
			<hr/>
			<div id='date_alert_drop_<?=$i?>' class='alert'>* Date must be selected and valid<br></div>
			<div id='app_time_alert_drop_<?=$i?>' class='alert'>* Appointment Time must be selected<br></div>
			<div id='city_alert_drop_<?=$i?>' class='alert'>* City must be entered<br></div>
			<div id='state_alert_drop_<?=$i?>' class='alert'>* State must be entered<br></div>
			<div id='address_alert_drop_<?=$i?>' class='alert'>* Address must be entered<br></div>
			<div id='ref_number_alert_drop_<?=$i?>' class='alert'>* Ref Number must be entered<br></div>
			
			<table style="font-size:12px;">
				<tr>
					<td style="width:220px;" valign="top">Date</td>
					<td>
						<?php $data = array(
						  'name'        => "rcr_drop_date_$i",
						  'id'          => "rcr_drop_date_$i",
						  'value'		=> $this_date,
						  'style'		=> $text_box_style
						);?>
						<?=form_input($data);?> 
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td  valign="top">Appointment Time</td>
					<td VALIGN="top">
						<?php 	$hours = array(
											  '--'  => '--',
											  '1'  => '1',
											  '2'   => '2',
											  '3'   => '3',
											  '4' => '4',
											  '5' => '5',
											  '6' => '6',
											  '7' => '7',
											  '8' => '8',
											  '9' => '9',
											  '10' => '10',
											  '11' => '11',
											  '12' => '12',
											);
								$minutes = array(
											  '--'  => '--',
											  '00'  => '00',
											  '05'  => '05',
											  '10'   => '10',
											  '15'   => '15',
											  '20' => '20',
											  '25' => '25',
											  '30' => '30',
											  '35' => '35',
											  '40' => '40',
											  '45' => '45',
											  '50' => '50',
											  '55' => '55',
											);
								$timezones = array(
											  "---"	=> "---",
											  '1' 	=> "PST",
											  '0'	=> "MST",
											  "-1"	=> "CST",
											  "-2"	=> "EST"
											);
								//DETERMINE THIS_HOUR, THIS_AMPM, AND THIS TIMEZONE
								if(!empty($this_drop["appointment_time"]) && date("s",strtotime($this_drop["appointment_time"])) != "01")
								{
									$this_hour = date("h",strtotime($this_drop["appointment_time"]));
									$this_minute = date("i",strtotime($this_drop["appointment_time"]));
									$this_ampm = strtolower(date("A",strtotime($this_drop["appointment_time"])));
									$this_timezone = $this_drop['appointment_time_mst'] - $this_drop['appointment_time'];
								}
								else
								{
									$this_hour = "--";
									$this_minute = "--";
									$this_ampm = "--";
									$this_timezone = "---";
								}
						?>
						<?php echo form_dropdown("rcr_drop_app_hour_$i\" onchange=\"uncheck_tbd('drop',$i)",$hours,$this_hour,"id='rcr_drop_app_hour_$i' style='width:41px;margin-top:2px; margin-left:2px; margin-right:3px;'");?>:
						<?php echo form_dropdown("rcr_drop_app_minute_$i\" onchange=\"uncheck_tbd('drop',$i)",$minutes,$this_minute,"id='rcr_drop_app_minute_$i' style='width:41px;margin-top:2px; margin-right:1px;'");?>
						<?php echo form_dropdown("rcr_drop_app_ampm_$i\" onchange=\"uncheck_tbd('drop',$i)",array('--'=>'--','am'=>'AM','pm'=>'PM'),$this_ampm,"id='rcr_drop_app_ampm_$i' style='width:44px;margin-top:2px;margin-right:2px;'");?>
						<?php echo form_dropdown("rcr_drop_app_timezone_$i\" onchange=\"uncheck_tbd('drop',$i)",$timezones,$this_timezone,"id='rcr_drop_app_timezone_$i' style='width:52px;margin-top:2px;'");?>
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td valign="top">Appointment Time TBD</td>
					<td>
						<input type="checkbox" id="rcr_drop_app_time_tba_<?=$i?>" name="rcr_drop_app_time_tba_<?=$i?>"/>
					</td>
				</tr>
				<tr>
					<td  valign="top">Location Name</td>
					<td>
						<?php $data = array(
						  'name'        => "rcr_drop_location_$i",
						  'id'          => "rcr_drop_location_$i",
						  'value'		=> $this_drop["stop"]["location_name"],
						  'style'		=> $text_box_style
						);?>
						<?=form_input($data);?> 
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td  valign="top">GPS</td>
					<td>
						<input type="text" id="rcr_drop_gps_<?=$i?>" name="rcr_drop_gps_<?=$i?>" style="<?=$text_box_style?>" onblur="fill_in_rcr_address_fields('drop','<?=$i?>')"/>
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td  valign="top">Address</td>
					<td style="padding-bottom:10px;">
						<?php $data = array(
						  'name'        => "rcr_drop_address_$i",
						  'id'          => "rcr_drop_address_$i",
						  'value'		=> $this_drop["stop"]["address"],
						  'rows'		=> '3',
						  'cols'		=> '25',
						  'style'		=> 'position:relative; left:2px;'
						);?>
						<?=form_textarea($data);?>
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td  valign="top">City</td>
					<td>
						<?php $data = array(
						  'name'        => "rcr_drop_city_$i",
						  'id'          => "rcr_drop_city_$i",
						  'value'		=> $this_drop["stop"]["city"],
						  'style'		=> $text_box_style
						);?>
						<?=form_input($data);?> 
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td  valign="top">State</td>
					<td>
						<?php $data = array(
						  'name'        => "rcr_drop_state_$i",
						  'id'          => "rcr_drop_state_$i",
						  'value'		=> $this_drop["stop"]["state"],
						  'style'		=> $text_box_style
						);?>
						<?=form_input($data);?> 
					</td>
					<td valign="top" style="color:red">*</td>
				</tr>
				<tr>
					<td  valign="top">Ref Number</td>
					<td>
						<?php $data = array(
						  'name'        => "rcr_drop_ref_number_$i",
						  'id'          => "rcr_drop_ref_number_$i",
						  'value'		=> $this_drop["ref_number"],
						  'style'		=> $text_box_style
						);?>
						<?=form_input($data);?> 
					</td>
					<td valign="top" style="color:red"></td>
				</tr>
				<tr>
					<td  valign="top">Notes</td>
					<td style="padding-bottom:10px;">
						<?php $data = array(
						  'name'        => "rcr_drop_dispatch_notes_$i",
						  'id'          => "rcr_drop_dispatch_notes_$i",
						  'value'		=> $this_drop["dispatch_notes"],
						  'rows'		=> '3',
						  'cols'		=> '25',
						  'style'		=> 'position:relative; left:2px;'
						);?>
						<?=form_textarea($data);?>
					</td>
				</tr>
			</table>
		</div>
		<?php $i++; ?>
	<?php endforeach;?>
	<br>
	<a href="javascript:void(0);" style="" onclick="rcr_add_drop()">+ Add Drop</a> 
</form>
<script>rcr_orignals_required_selected()</script>