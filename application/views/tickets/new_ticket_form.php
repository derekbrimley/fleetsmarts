<script>
	$(function(){
		$("#incident_date_input").datepicker();
		$("#estimated_completion_date_input").datepicker();
		var drivers = new Array();
		<?php foreach($driver_options as $driver): ?>
			drivers.push('<?= $driver ?>')
		<?php endforeach ?>
		$("#responsible_party_dropdown").autocomplete({
			source: drivers
		})
	})
	function load_unit_number_div()
	{
		var unit_type = $("#unit_type").val();
		$("#truck_number").hide();
		$("#trailer_number").hide();
		$("#truck_id_dropdown").val("Select");
		$("#trailer_id_dropdown").val("Select");
		
		if(unit_type == "Truck")
		{
			$("#truck_number").show();
		}
		else if(unit_type == "Trailer")
		{
			$("#trailer_number").show();
		}
	
	}
</script>
<?php $attributes = array('id' => 'new_ticket_form'); ?>
<?=form_open('tickets/load_new_ticket_form',$attributes)?>
	<table style="font-size:14px; width:360px; margin:auto; margin-top:10px;">
		<tr>
			<td>Category</td>
			<td>
				<?php $options = array(
					'Select'  	=> 'Select' ,
					'Claim'  => 'Claim',
					'Damage'  => 'Damage',
					'Inspection'  => 'Inspection',
					); ?>
				<?php echo form_dropdown('category_dropdown',$options,"Select",'id="category_dropdown" class="left_bar_input"');?>
			</td>
		</tr>
		<tr>
			<td style="width:180px;">Truck or Trailer?</td>
			<td>
				<select class="left_bar_input" id="unit_type" name="unit_type" onChange="load_unit_number_div()">
					<option>Select</option>
					<option>Truck</option>
					<option>Trailer</option>
					<option>Other</option>
				</select>
			</td>
		</tr>
		<tr id="truck_number" style="display:none;">
			<td style="width:180px;">Truck Number</td>
			<td>
				<?php echo form_dropdown('truck_id_dropdown',$truck_options,"Select",'id="truck_id_dropdown" class="left_bar_input"');?>
			</td>
		</tr>
		<tr id="trailer_number" style="display:none;">
			<td style="width:180px;">Trailer Number</td>
			<td>
				<?php echo form_dropdown('trailer_id_dropdown',$trailer_options,"Select",'id="trailer_id_dropdown" class="left_bar_input"');?>
			</td>
		</tr>
		<tr>
			<td>Description</td>
			<td>
				<textarea style="width:157px;margin-bottom:10px;" id="description_input" name="description_input"></textarea>
			</td>
		</tr>
		<tr>
			<td>Responsible Party</td>
			<td>
				<input id="responsible_party_dropdown" name="responsible_party_dropdown" class="left_bar_input"/>
			</td>
		</tr>
		<tr>
			<td>Incident Date</td>
			<td>
				<input class="left_bar_input" type="text" id="incident_date_input" name="incident_date_input"/>
			</td>
		</tr>
		<tr>
			<td>Estimated Repair Date</td>
			<td>
				<input class="left_bar_input" id="estimated_completion_date_input" name="estimated_completion_date_input" />
			</td>
		</tr>
		<tr>
			<td>Amount</td>
			<td>
				<input class="left_bar_input" id="amount_input" name="amount_input"/>
			</td>
		</tr>
	</table>
</form>