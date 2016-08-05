<script>
	$("#ins_status_loading_"+<?=$truck_id?>).hide();
	$("#ins_status_red_"+<?=$truck_id?>).hide();
	$("#ins_status_green_"+<?=$truck_id?>).hide();
</script>
<?php if(
		$truck_ins_stats["cargo_is_covered"] == false || 
		$truck_ins_stats["reefer_bd_is_covered"] == false || 
		$truck_ins_stats["pd_is_covered"] == false || 
		$truck_ins_stats["al_is_covered"] == false || 
		$truck_ins_stats["radius_is_unlimited"] == false || 
		$truck_ins_stats["rental_is_covered"] == false || 
		$truck_ins_stats["al_is_double_insured"] == true || 
		$truck_ins_stats["pd_is_double_insured"] == true || 
		$truck_ins_stats["cargo_is_double_insured"] == true
		):?>
	<script>
		$("#ins_status_red_"+<?=$truck_id?>).show();
	</script>
<?php else:?>
	<script>
		$("#ins_status_green_"+<?=$truck_id?>).show();
	</script>
<?php endif;?>		
<table style="">
	<tr>
		<td style="width:30px;">
		</td>
		<td style="width:45px;">
		</td>
		<td style="width:80px;">
		</td>
		<td style="width:40px;">
		</td>
		<td style="width:50px;">
		</td>
		<td style="width:70px;">
		</td>
		<td style="width:70px;">
		</td>
		<td style="width:70px;">
		</td>
		<td style="width:50px; text-align:right;">
			<?php if($truck_ins_stats["radius_is_unlimited"] == false):?>
				<img src="/images/red_exclamation_mark.png" style="cursor:pointer; height:15px; position:relative; right:4px;"title="Radius is not unlimited!" onclick="alert('Radius is not unlimited!')"/>
			<?php else:?>
				<img src="/images/green_checkmark.png" style="cursor:pointer; height:15px;"title="Good" onclick="alert('Good')"/>
			<?php endif;?>
		</td>
		<td style="width:45px; text-align:right;">
		</td>
		<td style="width:45px; text-align:right;">
			<?php if($truck_ins_stats["pd_is_covered"] == false):?>
				<img src="/images/red_exclamation_mark.png" style="cursor:pointer; height:15px; position:relative; right:4px;"title="Phys Dam is not covered!" onclick="alert('Phys Dam is not covered!')"/>
			<?php elseif($truck_ins_stats["pd_is_double_insured"] == true):?>
				<img src="/images/red_exclamation_mark.png" style="cursor:pointer; height:15px; position:relative; right:4px;"title="Phys Dam is double insured!" onclick="alert('Phys Dam is double insured!')"/>
			<?php else:?>
				<img src="/images/green_checkmark.png" style="cursor:pointer; height:15px;"title="Good" onclick="alert('Good')"/>
			<?php endif;?>
			<?=$truck_ins_stats["pd_is_double_insured"]?>
		</td>
		<td style="width:60px; text-align:right;">
			<?php if($truck_ins_stats["al_is_covered"] == false):?>
				<img src="/images/red_exclamation_mark.png" style="cursor:pointer; height:15px; position:relative; right:4px;"title="Auto Liability is not covered!" onclick="alert('Auto Liability is not covered!')"/>
			<?php elseif($truck_ins_stats["al_is_double_insured"] == true):?>
				<img src="/images/red_exclamation_mark.png" style="cursor:pointer; height:15px; position:relative; right:4px;"title="Auto Liability is double insured!" onclick="alert('Auto Liability is double insured!')"/>
			<?php else:?>
				<img src="/images/green_checkmark.png" style="cursor:pointer; height:15px;"title="Good" onclick="alert('Good')"/>
			<?php endif;?>
		</td>
		<td style="width:50px; text-align:right;">
			<?php if($truck_ins_stats["rental_is_covered"] == false):?>
				<img src="/images/red_exclamation_mark.png" style="cursor:pointer; height:15px; position:relative; right:4px;"title="DT Rental is not covered!" onclick="alert('DT Rental is not covered!')"/>
			<?php else:?>
				<img src="/images/green_checkmark.png" style="cursor:pointer; height:15px;"title="Good" onclick="alert('Good')"/>
			<?php endif;?>
		</td>
		<td style="width:50px; text-align:right;">
			<?php if($truck_ins_stats["cargo_is_covered"] == false):?>
				<img src="/images/red_exclamation_mark.png" style="cursor:pointer; height:15px; position:relative; right:4px;"title="Cargo is not covered!" onclick="alert('Cargo is not covered!')"/>
			<?php else:?>
				<img src="/images/green_checkmark.png" style="cursor:pointer; height:15px;"title="Good" onclick="alert('Good')"/>
			<?php endif;?>
		</td>
		<td style="width:60px; text-align:right; padding-right:10px;">
			<?php if($truck_ins_stats["reefer_bd_is_covered"] == false):?>
				<img src="/images/red_exclamation_mark.png" style="cursor:pointer; height:15px; position:relative; right:4px;"title="Reefer Breakdown is not covered!" onclick="alert('Reefer Breakdown is not covered!')"/>
			<?php else:?>
				<img src="/images/green_checkmark.png" style="cursor:pointer; height:15px;"title="Good" onclick="alert('Good')"/>
			<?php endif;?>
		</td>
		<td style="width:60px;">
		</td>
		<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" title="">
		</td>
	</tr>
</table>