<script>
	$('#duplicate_uc_date').datepicker({ showAnim: 'blind' });
</script>
<?php
	//GET UC
	$where = null;
	$where["id"] = $uc_id;
	$unit_coverage = db_select_ins_unit_coverage($where);
	
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
	
	$truck_dd_style = "";
	$trailer_dd_style = "";
	if($unit_coverage["unit_type"] == "Truck")
	{
		$trailer_dd_style = "display:none;";
		unset($truck_options[$unit_coverage["unit_id"]]);
	}
	else if($unit_coverage["unit_type"] == "Trailer")
	{
		$truck_dd_style = "display:none;";
	}
	else
	{
		$truck_dd_style = "display:none;";
		$trailer_dd_style = "display:none;";
	}
?>

<form id="duplicate_unit_coverage_form">
	<input type="hidden" id="duplicate_uc_id" name="duplicate_uc_id" value="<?=$uc_id?>"/>
	<table style="margin:auto; margin-top:50px; width:356px;">
		<tr>
			<td style="width:150px;">
				New Unit Type
			</td>
			<td style="width:156px;">
				<?php
					$options = array(
						"Select"	=>	"Select",
						"Truck"		=>	"Truck",
						"Trailer"	=>	"Trailer"
					);
				?>
				<?php echo form_dropdown("duplicate_uc_unit_type",$options,$unit_coverage["unit_type"],"id='duplicate_uc_unit_type' class='' style='width:156px; height:20px; font-size:12px; position:relative; bottom:5px;' onchange='unit_type_changed_for_duplicate_dialog()'");?>
			</td>
		</tr>
		<tr>
			<td>
				New Unit #
			</td>
			<td>
				<?php echo form_dropdown("duplicate_uc_truck_id",$truck_options,"Select","id='duplicate_uc_truck_id' class='' style='$truck_dd_style width:156px; height:20px; font-size:12px; position:relative; bottom:5px;'");?>
				<?php echo form_dropdown("duplicate_uc_trailer_id",$trailer_options,"Select","id='duplicate_uc_trailer_id' class='' style='$trailer_dd_style width:156px; height:20px; font-size:12px; position:relative; bottom:5px;'");?>
			</td>
		</tr>
		<tr>
			<td style="">
				Coverage Added Date
			</td>
			<td>
				<span class="" style="">
					<input class="" type="text" id="duplicate_uc_date" name="duplicate_uc_date" style="text-align:center; width:70px; height:20px;"/>
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
					<?php echo form_dropdown("duplicate_uc_hour",$options,"00","id='duplicate_uc_hour' class='' style='width:40px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
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
					<?php echo form_dropdown("duplicate_uc_minute",$options,"00","id='duplicate_uc_minute' class='' style='width:40px; height:20px; font-size:12px; position:relative; bottom:0px;'");?>
				</span>
			</td>
		</tr>
	</table>
</form>