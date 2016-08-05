<script>
	//SET HEIGHT OF MAIN CONTENT AND SCROLLABLE CONTENT
	$("#main_content").height($(window).height() - 115);
	$("#scrollable_content").height($("#main_content").height() - 90);
</script>
<div id="main_content_header">
	<span style="font-weight:bold;">Unit Coverages</span>
	<img id="loading_img" src="/images/loading.gif" style="display:none;float:right;margin-top:4px;height:20px;"/>
</div>

<table style="margin-top:20px;">
	<tr class="heading" style="font-size:12px;">
		<td style="width:30px;">
			
		</td>
		<td style="width:45px;">
			Unit
		</td>
		<td style="width:80px;">
			Policy
		</td>
		<td style="width:50px;">
			Quote/<br>Policy
		</td>
		<td style="width:40px;">
			Term
		</td>
		<td style="width:50px;">
			Cost/<br>Month
		</td>
		<td style="width:70px;">
			Insured
		</td>
		<td style="width:70px;">
			Insurer
		</td>
		<td style="width:70px;">
			Agent
		</td>
		<td style="width:50px; text-align:right;">
			Radius
		</td>
		<td style="width:45px; text-align:right;">
			PD<br>Ded
		</td>
		<td style="width:45px; text-align:right;">
			Phys<br>Dam
		</td>
		<td style="width:60px; text-align:right;">
			Auto<br>Liab
		</td>
		<td style="width:50px; text-align:right;">
			DT<br>Rental
		</td>
		<td style="width:50px; text-align:right;">
			Cargo
		</td>
		<td style="width:60px; text-align:right; padding-right:10px;">
			Reefer<br>BD
		</td>
		<td style="width:60px;">
			Finance<br>Guar
		</td>
		<td style="width:55px; text-align:right;">
			Expected<br>Cancel
		</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php if(!empty($insured_trucks)):?>
			<?php foreach($insured_trucks as $insured_truck):?>
				<?php
					$i = 0;
				
					//GET ALL UNIT COVERAGES FOR THIS UNIT
					$where = null;
					$where = "coverage_current_since <= '".$snapshot_date_db_format."' 
					AND (coverage_current_till > '".$snapshot_date_db_format."' OR coverage_current_till IS NULL)
					AND unit_type = 'Truck'
					AND unit_id = ".$insured_truck["id"];
					$truck_coverages = db_select_ins_unit_coverages($where);
					//echo $where;
					//echo " ".count($truck_coverages);
				?>
				<?php include("application/views/equipment/insurance/ins_by_audit_row.php"); ?>
				<?php
				/**
				<div id="uc_for_unit_<?=$insured_truck["id"]?>" class="" style="margin-bottom:20px;">
					<?php foreach($truck_coverages as $truck_coverage):?>
						<?php include("application/views/equipment/insurance/ins_by_unit_row.php"); ?>
					<?php endforeach;?>
				</div>
				**/
				?>
			<?php endforeach;?>
	<?php else:?>
		No insured trucks
	<?php endif;?>
	<?php if(!empty($unknown_unit_coverages)):?>
		<div id="unknown_units" class="" style="margin-bottom:20px;">
			<?php foreach($unknown_unit_coverages as $truck_coverage):?>
				<?php include("application/views/equipment/insurance/ins_by_unit_row.php"); ?>
			<?php endforeach;?>
		</div>
	<?php endif;?>
</div>