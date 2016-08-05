<?php
	//GET TRUCK
	$where = null;
	$where["id"] = $goalpoint["truck_id"];
	$truck = db_select_truck($where);
	
	//GET TRAILER
	$where = null;
	$where["id"] = $goalpoint["trailer_id"];
	$trailer = db_select_trailer($where);
	
	//GET DRIVER
	$where = null;
	$where["id"] = $goalpoint["client_id"];
	$client = db_select_client($where);
	
	$geocode = reverse_geocode($geopoint["latitude"].",".$geopoint["longitude"]);
	
	//GET DATA FOR PRE-FILLED FIELDS
	$datetext = "";
	$timetext = "";
	if(!empty($geopoint))
	{
		$datetext = date("m/d/y",strtotime($geopoint["datetime"]));
		$timetext = date("H:i",strtotime($geopoint["datetime"]));
	}
	
	$gpstext = "";
	if(!empty($geopoint["latitude"]) && !empty($geopoint["latitude"]))
	{
		$gpstext = $geopoint["latitude"].",".$geopoint["longitude"];
	}
	
	if(empty($geopoint))
	{
		$gpstext = $goalpoint["gps"];
	}
?>
<style>
	<?php if(empty($geopoint)):?>
		.complete_details
		{
			display:none
		}
	<?php else:?>
		.complete_edit
		{
			display:none
		}
	<?php endif;?>
	.complete_edit
	{
		text-align:right;
		width:148px;
	}
	
	.late_div
	{
		display:none;
	}
</style>
<script>
	$('#gp_complete_date').datepicker({ showAnim: 'blind' });
	//show_late_div();
</script>
<form id="mark_goalpoint_complete_form">
	<input type="hidden" id="goalpoint_id" name="goalpoint_id" value="<?=$goalpoint["id"]?>" />
	<input type="hidden" id="geopoint_id" name="geopoint_id" value="<?=$geopoint["id"]?>" />
	<input type="hidden" id="codriver_id" name="codriver_id" value="" />
	<table>
		<tr>
			<td style="min-width:70px;">
				Date
			</td>
			<td style="text-align:right;">
				<span class="complete_details"><?=date("m/d/y",strtotime($geopoint["datetime"]))?></span>
				<input type="text" class="complete_edit" id="gp_complete_date" name="gp_complete_date" value="<?=$datetext?>" onchange="show_late_div()" placeholder="MM/DD/YY"/>
			</td>
		</tr>
		<tr>
			<td>
				Time
			</td>
			<td style="text-align:right;">
				<span class="complete_details"><?=date("H:i",strtotime($geopoint["datetime"]))?></span>
				<input type="text" class="complete_edit" id="gp_complete_time" name="gp_complete_time" value="<?=$timetext?>" onchange="show_late_div()" placeholder="HH:MM"/>
			</td>
		</tr>
		<tr>
			<td>
				GPS
			</td>
			<td style="text-align:right;">
				<span class="complete_details"><?=$geopoint["latitude"].",".$geopoint["longitude"]?></span>
				<input type="text" class="complete_edit" id="gp_complete_gps" name="gp_complete_gps" value="<?=$gpstext?>" onchange="draw_custom_marker()" placeholder="Lat, Long"/>
				<input type="hidden" id="gps_isvalid" value="yes"/>
			</td>
		</tr>
		<tr>
			<td>
				Address
			</td>
			<td style="text-align:right;">
				<span id="complete_gp_address_text" class=""><?=$geocode["street_number"]." ".$geocode["street"]?></span>
			</td>
		</tr>
		<tr>
			<td>
				City
			</td>
			<td style="text-align:right;">
				<span id="complete_gp_city_text" class=""><?=$geocode["city"]?></span>
			</td>
		</tr>
		<tr>
			<td>
				State
			</td>
			<td style="text-align:right;">
				<span id="complete_gp_state_text" class=""><?=$geocode["state"]?></span>
			</td>
		</tr>
		<tr>
			<td>
				Odometer
			</td>
			<td style="text-align:right;">
				<span class="complete_details"><?=number_format($geopoint["odometer"])?></span>
				<input type="text" class="complete_edit" id="gp_complete_odometer" name="gp_complete_odometer" value="<?=$geopoint["odometer"]?>"/>
			</td>
		</tr>
		<?php if($goalpoint["gp_type"] == "Drop" && $goalpoint["arrival_departure"] == "Departure"):?>
			<tr>
				<td>
					Lumper?
				</td>
				<td style="text-align:right;">
					<?php
						$options = array(
							"Yes" => "Yes",
							"No" => "No",
						);
					?>
					<?php echo form_dropdown("is_lumper",$options,"Select","id='is_lumper'  class=''  style='width:148px; height:18px; font-size:12px;' onchange='lumper_selected()'");?>
				</td>
			</tr>
			<tr id="lumper_amount_row">
				<td>
					Lumper Amount
				</td>
				<td style="text-align:right;">
					<input type="text" class="" id="gp_complete_lumper_amount" name="gp_complete_lumper_amount" value=""/>
				</td>
			</tr>
		<?php else:?>
			<input type="hidden" id="is_lumper" name="is_lumper" value="No" />
			<input type="hidden" id="gp_complete_lumper_amount" name="gp_complete_lumper_amount" value="" />
		<?php endif;?>
		<tr class="late_div">
			<td style="color:red;">
				Why Late?
			</td>
			<td style="text-align:right;">
				<input type="hidden" id="load_isLate" value="no"/>
				<?php
					$options = array(
						"Select" => "Select",
						"Management" => "Management",
						"Night Dispatch" => "Night Dispatch",
						"Driver" => "Driver",
						"Equipment" => "Equipment",
						"Broker" => "Broker",
						"God" => "God",
					);
				?>
				<?php echo form_dropdown("why_late",$options,"Select","id='why_late'  class=''  style='width:148px; height:18px; font-size:12px;' onchange=''");?>
			</td>
		</tr>
		<tr class="late_div">
			<td style="color:red;">
				Late Explanation
			</td>
			<td style="text-align:right;">
				<textarea id="late_explanation" class="" style="text-align:left; width:148px; height:30px;"></textarea>
			</td>
		</tr>
	</table>
</form>
<script>
	if($("#gp_complete_gps").val())
	{
		//draw_custom_marker();
	}
</script>