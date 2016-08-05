<div style="margin-top:30px;">
	<form id="lumper_details_form">
		<input type="hidden" id="po_id" name="po_id" value="<?=$po_id?>"/>
		<table style="margin:auto;">
			<tr>
				<td style="width:80px;">
					Driver
				</td>
				<td>
					<?php echo form_dropdown('lumper_client_id',$clients_dropdown_options,"Select","id='lumper_client_id' class='' style='width:150px;'");?>
				</td>
			</tr>
			<tr>
				<td style="width:80px;">
					Truck
				</td>
				<td>
					<?php echo form_dropdown('lumper_truck_id',$truck_dropdown_options,"Select","id='lumper_truck_id' class='' style='width:150px;'");?>
				</td>
			</tr>
			<tr>
				<td style="width:80px;">
					Load
				</td>
				<td>
					<input id="lumper_load_number" name="lumper_load_number" style="width:150px;" placeholder="Load Number"/>
				</td>
			</tr>
		</table>
	</form>
</div>
<div style="display:none;" id="lumper_ajax_response_div">
	<!--AJAX GOES HERE !-->
</div>