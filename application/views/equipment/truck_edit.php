<script>
		$("#scrollable_content").height($("#body").height() - 155);
		
		//CREATE TRUCK ARRAY - TRUCK NUMBER
		truck_validation_list = [
		<?php 	
				$array_string = "";
				foreach($trucks as $itruck)
				{
					$truck_number = $itruck['truck_number'];
					$array_string = $array_string.'"'.$truck_number.'",';
				}
				
				echo substr($array_string,0,-1);
		?>];
			
		original_truck_number = "<?=$truck["truck_number"]?>";
		//alert(original_truck_number);
</script>

<div id="main_content_header">
	<span style="font-weight:bold;">Edit Truck <?=$truck["truck_number"]?></span>
	<img src="/images/save.png" style="cursor:pointer;float:right;margin-top:4px;height:20px; margin-left:10px;" id="save_truck" onclick="validate_save_truck()"/>
	<img src="/images/back.png" style="cursor:pointer;float:right;margin-top:4px;height:20px;" id="back_btn" onclick="load_truck_details('<?=$truck["id"]?>')"/>
	<img src="/images/loading.gif" style="display:none;float:right;margin-top:4px;height:20px;" id="loading_img"/>
</div>

<div id="scrollable_content" class="scrollable_div">
	<?php $attributes = array('id' => 'truck_edit_form'); ?>
	<?=form_open('equipment/save_truck',$attributes)?>
	<input type="hidden" id="truck_id" name="truck_id" value="<?=$truck["id"]?>">
		<div style="margin:20px;">
			<?php $text_box_style = "width:161px; margin-left:2px;";?>
			<table id="truck_view" style="font-size: 14px;">
				<tr>
					<td style="width:300px;">Truck Status</td>
					<td>
						<?php $options = array(
						'Select'  	=> 'Select',
						'On the road'  	=> 'On the road',
						'In the shop'  => 'In the shop',
						'Subtruck'  => 'Subtruck',
						'Returned'  => 'Returned',
						); ?>
						<?php echo form_dropdown('edit_truck_status',$options,$truck["status"],'id="edit_truck_status" style="width:161px; height:21px; margin-left:2px;"');?>
					</td>
					<td style="color:red; vertical-align:middle; padding-left:5px; font-weight:bold;">
						*
					</td>
				</tr>
				<tr>
					<td style="width:300px;">Dropdown Status</td>
					<td>
						<?php $options = array(
							'Show'  => 'Show',
							'Hide'  => 'Hide',
							); 
						?>
						<?php echo form_dropdown('edit_truck_dropdown_status',$options,$truck["dropdown_status"],'id="edit_truck_dropdown_status" style="width:161px; height:21px; margin-left:2px;"');?>
					</td>
					<td style="color:red; vertical-align:middle; padding-left:5px; font-weight:bold;">
						*
					</td>
				</tr>
				<tr>
					<td>Fleet Manager</td>
					<td>
						<?php echo form_dropdown('edit_fm',$fleet_manager_dropdown_options,$truck["fm_id"],'id="edit_fm" style="width:161px; height:21px; margin-left:2px;"');?>
					</td>
					<td style="color:red; vertical-align:middle; padding-left:5px; font-weight:bold;">
						*
					</td>
				</tr>
				<tr>
					<td>Driver Manager</td>
					<td>
						<?php echo form_dropdown('edit_dm',$driver_manager_dropdown_options,$truck["dm_id"],'id="edit_dm" style="width:161px; height:21px; margin-left:2px;"');?>
					</td>
					<td style="color:red; vertical-align:middle; padding-left:5px; font-weight:bold;">
						*
					</td>
				</tr>
				<tr>
					<td>Client</td>
					<td>
						<?php echo form_dropdown('edit_client',$client_dropdown_options,$truck["client_id"],'id="edit_client" style="width:161px; height:21px; margin-left:2px;"');?>
					</td>
				</tr>
				<tr>
					<td>Co-Driver</td>
					<td>
						<?php echo form_dropdown('edit_codriver',$codriver_dropdown_options,$truck["codriver_id"],'id="edit_codriver" style="width:161px; height:21px; margin-left:2px;"');?>
					</td>
				</tr>
				<tr>
					<td>Pulling Trailer</td>
					<td>
						<?php echo form_dropdown('edit_trailer',$trailer_dropdown_options,$truck["trailer_id"],'id="edit_trailer" style="width:161px; height:21px; margin-left:2px;"');?>
					</td>
					<td style="color:red; vertical-align:middle; padding-left:5px; font-weight:bold;">
						*
					</td>
				</tr>
				<tr>
					<td>Company Stickers</td>
					<td>
						<?php echo form_dropdown('edit_company',$company_dropdown_options,$truck["company_id"],'id="edit_company" style="width:161px; height:21px; margin-left:2px;"');?>
					</td>
				</tr>
				<tr>
					<td>Lease Company</td>
					<td>
						<?php echo form_dropdown('edit_leasing_company',$vendor_dropdown_options,$truck['vendor_id'],'id="edit_leasing_company" style="width:161px; height:21px; margin-left:2px;"');?>
					</td>
				</tr>
				<tr>
					<td>Truck Number</td>
					<td>
						<input type="text" id="edit_truck_number" name="edit_truck_number" style="<?=$text_box_style?>" value="<?=$truck["truck_number"]?>">
					</td>
					<td style="color:red; vertical-align:middle; padding-left:5px; font-weight:bold;">
						*
					</td>
				</tr>
				<tr>
					<td>Make</td>
					<td>
						<input type="text" id="edit_make" name="edit_make" style="<?=$text_box_style?>" value="<?=$truck["make"]?>">
					</td>
				</tr>
				<tr>
					<td>Model</td>
					<td>
						<input type="text" id="edit_model" name="edit_model" style="<?=$text_box_style?>" value="<?=$truck["model"]?>">
					</td>
				</tr>
				<tr>
					<td>Year</td>
					<td>
						<input type="text" id="edit_year" name="edit_year" style="<?=$text_box_style?>" value="<?=$truck["year"]?>">
					</td>
				</tr>
				<tr>
					<td>Value</td>
					<td>
						<input type="text" id="edit_value" name="edit_value" style="<?=$text_box_style?>" value="<?=$truck["value"]?>">
					</td>
				</tr>
				<tr>
					<td>VIN</td>
					<td>
						<input type="text" id="edit_vin" name="edit_vin" style="<?=$text_box_style?>" value="<?=$truck["vin"]?>">
					</td>
				</tr>
				<tr>
					<td>Plate Number</td>
					<td>
						<input type="text" id="edit_plate_number" name="edit_plate_number" style="<?=$text_box_style?>" value="<?=$truck["plate_number"]?>">
					</td>
				</tr>
				<tr>
					<td>Insurance Policy</td>
					<td>
						<input type="text" id="edit_insurance_policy" name="edit_insurance_policy" style="<?=$text_box_style?>" value="<?=$truck["insurance_policy"]?>">
					</td>
				</tr>
				<tr>
					<td>Rental Rate</td>
					<td>
						<input type="text" id="edit_rental_rate" name="edit_rental_rate" style="position:relative; left:2px; width:45px; margin-right:5px;" value="<?=$truck["rental_rate"]?>">
						<span style="vertical-align:center;">per</span>
						<?php $options = array(
						'Day'  	=> 'Day',
						'Week'  => 'Week',
						'Month'  => 'Month',
						); ?>
						<?php echo form_dropdown('edit_rental_rate_period',$options,$truck["rental_rate_period"],'id="edit_rental_rate_period" style="width:80px; height:21px; margin-left:5px;"');?>
					</td>
					<td style="color:red; vertical-align:middle; padding-left:5px; font-weight:bold;">
						*
					</td>
				</tr>
				<tr>
					<td>Mileage Rate</td>
					<td>
						<input type="text" id="edit_mileage_rate" name="edit_mileage_rate" style="<?=$text_box_style?>" value="<?=$truck["mileage_rate"]?>">
					</td>
					<td style="color:red; vertical-align:middle; padding-left:5px; font-weight:bold;">
						*
					</td>
				</tr>
				<tr>
					<td>Current Registration</td>
					<td>
						<?php if(!empty($truck['registration_link'])):?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$truck["registration_link"]?>" onclick="">Registration</a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td>Current Insurance</td>
					<td>
						<?php if(!empty($truck['insurance_link'])):?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$truck["insurance_link"]?>" onclick="">Insurance</a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td>Current IFTA</td>
					<td>
						<?php if(!empty($truck['ifta_link'])):?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$truck["ifta_link"]?>" onclick="">IFTA</a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td>Current Lease Agreement</td>
					<td>
						<?php if(!empty($truck['lease_agreement_link'])):?>
							<a target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$truck["lease_agreement_link"]?>" onclick="">Lease Agreement</a>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td>Wet Service Interval</td>
					<td>
						<input type="text" id="edit_next_wet_service" name="edit_next_wet_service" style="<?=$text_box_style?>" value="<?=$truck["next_wet_service"]?>">
					</td>
				</tr>
				<tr>
					<td>Dry Service Interval</td>
					<td>
						<input type="text" id="edit_next_dry_service" name="edit_next_dry_service" style="<?=$text_box_style?>" value="<?=$truck["next_dry_service"]?>">
					</td>
				</tr>
				<tr>
					<td>Notes</td>
					<td>
						<?php $data = array(
						  'name'        => "edit_truck_notes",
						  'id'          => "edit_truck_notes",
						  'rows'		=> '3',
						  'style'		=> $text_box_style,
						  'value'		=> $truck["truck_notes"],
						);?>
						<?=form_textarea($data);?>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>