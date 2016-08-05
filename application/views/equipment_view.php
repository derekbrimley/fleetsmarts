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
		
		<?php include("equipment/equipment_script.php"); ?>
		
	</head>
	
	<body id="body">
		
		<?php include('main_menu.php');  ?>
		<div id="main_div">
			<div id="space_header">
			</div>
			
			<?php include("equipment/equipment_left_bar_div.php"); ?>
			
			<div id="main_content"  style="display:none;">
				<!-- AJAX WILL FILL THIS IN !-->
				<div id="scrollable_content">
				</div>
			</div>
			
		</div>
	</body>
	
	<div id="new_quote_dialog" title="New Quote" style="display:none;">
		<!-- AJAX GOES HERE !-->
	</div>
	
	<div id="new_equipment_dialog" title="New Equipment" style="display:none;">
		<?php $text_box_style = "width:161px; margin-left:2px;";?>
		<div style="margin:20px;">
			<table id="truck_view" style="font-size: 14px;">
				<tr>
					<td style="width:180px;">Equipment Type</td>
					<td>
						<?php $options = array(
						'Select'  	=> 'Select',
						'Truck'  	=> 'Truck',
						'Trailer'  	=> 'Trailer',
						); ?>
						<?php echo form_dropdown('equipment_type',$options,'Select','onchange="equipment_type_selected()" id="equipment_type" style="width:161px; height:21px; margin-left:2px;"');?>
					</td>
					<td style="color:red; width:10px; text-align:right;">
						*
					</td>
				</tr>
			</table>
			<div id="new_truck_div" style="display:none;">
				<?php $attributes = array('id' => 'add_truck_form'); ?>
				<?=form_open('equipment/add_truck',$attributes)?>
					
					<table id="truck_view" style="font-size: 14px;">
						<tr>
							<td style="width:180px;">Status</td>
							<td>
								<?php $options = array(
								'Select'  	=> 'Select',
								'On the road'  	=> 'On the road',
								'In the shop'  => 'In the shop',
								'Subtruck'  => 'Subtruck',
								'Returned'  => 'Returned',
								); ?>
								<?php echo form_dropdown('truck_status',$options,'Select','id="truck_status" style="width:161px; height:21px; margin-left:2px;"');?>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Client</td>
							<td>
								<?php echo form_dropdown('client',$client_dropdown_options,'Select','id="client" style="width:161px; height:21px; margin-left:2px;"');?>
							</td>
						</tr>
						<tr>
							<td>Company Stickers</td>
							<td>
								<?php echo form_dropdown('company',$company_dropdown_options,'Select','id="company" style="width:161px; height:21px; margin-left:2px;"');?>
							</td>
						</tr>
						<tr>
							<td>Lease Company</td>
							<td>
								<?php echo form_dropdown('leasing_company',$vendor_dropdown_options,'Select','id="leasing_company" style="width:161px; height:21px; margin-left:2px;"');?>
							</td>
						</tr>
						<tr>
							<td>Truck Number</td>
							<td>
								<?= form_input('truck_number',null,'id="truck_number" style="'.$text_box_style.'"')?>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Make</td>
							<td>
								<?= form_input('make',null,'id="make" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>Model</td>
							<td>
								<?= form_input('model',null,'id="model" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>Year</td>
							<td>
								<?= form_input('year',null,'id="year" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>Value</td>
							<td>
								<?= form_input('value',null,'id="value" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>VIN</td>
							<td>
								<?= form_input('vin',null,'id="vin" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>Plate Number</td>
							<td>
								<?= form_input('plate_number',null,'id="plate_number" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>Insurance Policy</td>
							<td>
								<?= form_input('insurance_policy',null,'id="insurance_policy" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>Rental Rate</td>
							<td>
								<input type="text" id="rental_rate" name="rental_rate" style="position:relative; left:2px; width:55px; margin-right:5px;">
								<span style="vertical-align:center;">per</span>
								<?php $options = array(
								'Select'  	=> 'Select',
								'Day'  	=> 'Day',
								'Week'  => 'Week',
								'Month'  => 'Month',
								); ?>
								<?php echo form_dropdown('rental_rate_period',$options,'Select','id="rental_rate_period" style="width:70px; height:21px; margin-left:5px;"');?>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Mileage Rate</td>
							<td>
								<?= form_input('mileage_rate',null,'id="mileage_rate" style="'.$text_box_style.'"')?>
							</td>
							<td style="color:red; width:10px; text-align:right;">
								*
							</td>
						</tr>
						<tr>
							<td>Registration</td>
							<td>
								<?= form_input('registration_link',null,'id="registration_link" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>Insurance Cert</td>
							<td>
								<?= form_input('insurance_link',null,'id="insurance_link" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>IFTA</td>
							<td>
								<?= form_input('ifta_link',null,'id="ifta_link" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>Lease Agreement</td>
							<td>
								<?= form_input('lease_agreement_link',null,'id="lease_agreement_link" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>Wet Service Due (mileage)</td>
							<td>
								<?= form_input('next_wet_service',null,'id="next_wet_service" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>Dry Service Due (mileage)</td>
							<td>
								<?= form_input('next_dry_service',null,'id="next_dry_service" style="'.$text_box_style.'"')?>
							</td>
						</tr>
						<tr>
							<td>Notes</td>
							<td>
								<?php $data = array(
								  'name'        => "truck_notes",
								  'id'          => "truck_notes",
								  'rows'		=> '3',
								  'style'		=> $text_box_style,
								  'value'		=> null,
								);?>
								<?=form_textarea($data);?>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<?php $attributes = array('id' => 'add_trailer_form'); ?>
			<?=form_open('equipment/add_trailer',$attributes)?>
				<input type="hidden" id="trailer_number_is_valid" name="trailer_number_is_valid" value="false">
				<div id="new_trailer_div" style="display:none;">
					<table id="trailer_view" style="font-size: 14px;">
						<tr>
							<td style="width:180px;">Status</td>
							<td>
								<?php $options = array(
								'Select'  	=> 'Select',
								'On the road'  	=> 'On the road',
								'In the shop'  => 'In the shop',
								'Returned'  => 'Retired',
								); ?>
								<?php echo form_dropdown('trailer_status',$options,'Select','id="trailer_status" style="width:161px; height:21px; margin-left:2px;"');?>
							</td>
							<td style="color:red;padding-left:5px;">
								*
							</td>
						</tr>
						<tr>
							<td>Lease Company</td>
							<td>
								<?php echo form_dropdown('trailer_leasing_company',$vendor_dropdown_options,'Select','id="trailer_leasing_company" style="width:161px; height:21px; margin-left:2px;"');?>
							</td>
							<td style="color:red;padding-left:5px;">
								*
							</td>
						</tr>
						<tr>
							<td>Trailer Number</td>
							<td>
								<input type="text" id="trailer_number" name="trailer_number" style="<?=$text_box_style?>" onblur="trailer_number_entered()">
							</td>
							<td id="trailer_error_div" style="color:red;padding-left:5px;">
								<div id="trailer_error_div">*</div>
							</td>
						</tr>
						<tr>
							<td>Rental Rate</td>
							<td>
								<input type="text" id="trailer_rental_rate" name="trailer_rental_rate" style="position:relative; left:2px; width:55px; margin-right:5px;">
								<span style="vertical-align:center;">per</span>
								<?php $options = array(
								'Select'  	=> 'Select',
								'Day'  	=> 'Day',
								'Week'  => 'Week',
								'Month'  => 'Month',
								); ?>
								<?php echo form_dropdown('trailer_rental_period',$options,'Select','id="trailer_rental_period" style="width:70px; height:21px; margin-left:5px;"');?>
							</td>
							<td style="color:red; width:10px; text-align:right;">
							</td>
						</tr>
						<tr>
							<td>Mileage Rate</td>
							<td>
								<input type="text" id="trailer_mileage_rate" name="trailer_mileage_rate" style="<?=$text_box_style?>">
							</td>
							<td style="color:red; width:10px; text-align:right;">
							</td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
	
	<div id="add_service_notes" title="Add Truck Service Note" style="padding:10px; display:none;">
		<div id="service_notes_ajax_div">
			<!-- AJAX WILL POPULATE THIS !-->
		</div>
		<div style="position:absolute; bottom:0">
			<?php $attributes = array('name'=>'add_truck_service_note_form','id'=>'add_truck_service_note_form', )?>
			<?=form_open('equipment/add_truck_service_note/',$attributes);?>
				Add Note:
				<input type="hidden" id="truck_id" name="truck_id">
				<textarea style="width:400px;" rows="3" id="new_note" name="new_note"></textarea>
			</form>
		</div>
	</div>
	
	<div title="Equipment Attachment Upload" id="file_upload_dialog" name="file_upload_dialog" style="display:hidden;" >
		<!-- AJAX GOES HERE !-->
	</div>