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
	<img src="/images/red_exclamation_mark.png" style="height:15px; position:relative; right:4px;"title="<?=$truck_ins_stats["status_message"]?>" onclick="alert('<?=$truck_ins_stats["status_message"]?>')"/>
<?php else:?>
	<img src="/images/green_checkmark.png" style="height:15px;"title="<?=$truck_ins_stats["status_message"]?>" onclick="alert('<?=$truck_ins_stats["status_message"]?>')"/>
<?php endif;?>		
	
	
	
	