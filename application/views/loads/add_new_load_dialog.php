<div>
	<?php $attributes = array('name'=>'add_load_form','id'=>'add_load_form', 'target'=>'_blank' )?>
	<?=form_open_multipart('loads/add_new_load',$attributes);?>
	<?php $text_box_style = 'width:154px; position:relative; left:0px;'; ?>
		<div id='fm_alert' class='alert'>* <b>Fleet Manager</b> must be assigned to the load<br></div>
		<div id='client_alert' class='alert'>* <b>Booked Under</b> must be assigned to the load<br></div>
		<div id='broker_alert' class='alert'>* <b>Broker</b> must be assigned to the load<br></div>
		<div id='broker_not_found_alert' class='alert'>* This <b>broker</b> does NOT exist in the system<br></div>
		<div id='broker_not_new_alert' class='alert'>* This <b>broker</b> is not a NEW broker<br></div>
		<div id='expected_revenue_alert' class='alert'>* <b>Expected revenue</b> must be assigned to the load<br></div>
		<div id='expected_revenue_nan_alert' class='alert'>* The <b>expected revenue</b> must be a number<br></div>
		<table>
			<tr>
				<td style="width:155px; vertical-align:middle;">
					Client
				</td>
				<td style="width:155px; vertical-align:middle;">
					<?php echo form_dropdown('client_id',$clients_dropdown_options,"Select",'id="client_id" style="'.$text_box_style.'"');?>
				</td>
			</tr>
			<tr>
				<td style="vertical-align:middle;">
					Fleet Manager
				</td>
				<td style="vertical-align: middle;">
					<?php echo form_dropdown('new_load_fm_dropdown',$fleet_managers_dropdown_options,$this->session->userdata('person_id'),'id="new_load_fm_dropdown" onChange="" style="'.$text_box_style.'"');?>
				</td>
				<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
					*
				</td>
			</tr>
			<tr>
				<td style="vertical-align:middle;">
					Driver Manager
				</td>
				<td style="vertical-align: middle;">
					<?php echo form_dropdown('new_load_dm_dropdown',$dm_dropdown_options,$this->session->userdata('person_id'),'id="new_load_dm_dropdown" onChange="" style="'.$text_box_style.'"');?>
				</td>
				<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
					*
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;">
					Booked Under
				</td>
				<td style="vertical-align: middle;">
					<?php echo form_dropdown('carrier_id',$billed_under_options,"Select",'id="carrier_id" style="'.$text_box_style.'"');?>
				</td>
				<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
					*
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;">
					Originals Required
				</td>
				<td style="vertical-align: middle;">
					<?php $options = array(
					 'Select'	=> 'Select',
					 'Yes'     	=> 'Yes',
					  'No'		=> 'No',
					);?>
					<?=form_dropdown("originals_required",$options,'Select',"id='originals_required' style='$text_box_style' onchange='new_load_orignals_required_selected()'");?>
				</td>
				<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
					*
				</td>
			</tr>
			<tr id="new_load_proof_notes_row">
				<td style="vertical-align: middle;">
					Proof Notes
				</td>
				<td style="vertical-align: middle;">
					<input type="text" name="proof_notes" id="proof_notes" style="<?=$text_box_style?>" placeholder="Where to find proof">
				</td>
				<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
					*
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;">
					Broker MC
				</td>
				<td style="vertical-align: middle;">
					<input type="text" name="broker_mc" id="broker_mc" style="<?=$text_box_style?>" onblur="search_for_broker()">
				</td>
				<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
					*
				</td>
			</tr>
			<tr id="broker_found_tr"  style="">
				<td style="vertical-align: middle;">
				</td>
				<td style="vertical-align: middle;">
					<span id="broker_found_span" style="display:none;"><!-- AJAX goes here!--></span>
				</td>
			</tr>
			<tr id="broker_name_row" name="broker_name_row" style="display:none;">
				<td style="vertical-align: middle;">
					Broker Name
				</td>
				<td style="vertical-align: middle;">
					<input type="text" name="broker_name" id="broker_name" style="<?=$text_box_style?>" onblur="search_for_broker()">
				</td>
				<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
					*
				</td>
			</tr>
			<tr style="">
				<td style="vertical-align: middle;">
					New Broker?
				</td>
				<td style="vertical-align: middle;">
					<input type="checkbox" id="broker_is_new" name="broker_is_new" value="old" onclick="broker_is_new_clicked()" style="position:relative; right:4px;" />
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;">
					Expected Miles<br>(w/ deadhead)
				</td>
				<td style="vertical-align: middle;">
					<?php $data = array(
					  'name'        => 'expected_miles',
					  'id'          => 'expected_miles',
					  'style'		=> $text_box_style
					);?>
					<?=form_input($data);?> 
				</td>
				<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
					*
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;">
					Expected Rate
				</td>
				<td style="vertical-align: middle;">
					<?php $data = array(
					  'name'        => 'expected_revenue',
					  'id'          => 'expected_revenue',
					  'style'		=> $text_box_style
					);?>
					<?=form_input($data);?> 
				</td>
				<td style="vertical-align: middle;color:red; padding-left:5px; font-weight:bold;">
					*
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;">
					Load Description
				</td>
				<td>
					<?php $data = array(
					  'name'        => 'load_notes',
					  'id'          => 'load_notes',
					  'rows'		=> '3',
					  'cols'		=> '19',
					);?>
					<?=form_textarea($data);?>
				</td>
			</tr>
			<tr>
				<td style="vertical-align: middle;">
					Rate Con
				</td>
				<td style="vertical-align: middle;margin-top:5px;">
					<input type="file" id="new_load_file" name="new_load_file" style="width:170px;" />
				</td>
			</tr>
		</table>
	</form>
</div>