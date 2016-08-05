<?php
	date_default_timezone_set('America/Denver');
	
	//GET POLICY
	$where = null;
	$where["id"] = $pr["ins_policy_id"];
	$policy = db_select_ins_policy($where);
	
	$policy_id = $policy["id"];
	
	//GET POLICY INSURANCE STATS
	$full_policy_coverage = get_full_policy_coverage($policy_id,$snapshot_date_db_format);
	
	$pd_style = "";
	$al_style = "";
	$dt_style = "";
	$cargo_style = "";
	$rbd_style = "";
	$trailers_style = "";
	$alert_style = "color:red; font-weight:bold; font-size:14px;";
	if($full_policy_coverage["number_of_pd_coverages"] != $full_policy_coverage["number_of_trucks"])
	{
		$pd_style = $alert_style;
	}
	if($full_policy_coverage["number_of_al_coverages"] != $full_policy_coverage["number_of_trucks"])
	{
		$al_style = $alert_style;
	}
	if($full_policy_coverage["number_of_dtr"] != $full_policy_coverage["number_of_trucks"])
	{
		$dt_style = $alert_style;
	}
	if($full_policy_coverage["number_of_cargo_coverages"] != $full_policy_coverage["number_of_trucks"])
	{
		$cargo_style = $alert_style;
	}
	if($full_policy_coverage["number_of_rbd"] != $full_policy_coverage["number_of_trucks"])
	{
		$rbd_style = $alert_style;
	}
	if($full_policy_coverage["number_of_trailers"] != $full_policy_coverage["number_of_trucks"])
	{
		$trailers_style = $alert_style;
	}
	
	$days_to_cancel = floor((strtotime($full_policy_coverage["ins_policy_profile"]["expected_cancellation_date"]) - time())/(60*60*24));
	$days_to_cancel_style = "";
	if($days_to_cancel < 10)
	{
		$days_to_cancel_style = $alert_style;
	}
	
?>
<div class="clickable_row" style="<?=$row_background_style?> padding-top:0px; padding-bottom:0px; min-height:30px;">
	<table style="">
		<tr class="" style="font-size:10px; height:30px; line-height:30px;" onclick="load_policy_details_view('<?=$policy["id"]?>','<?=$snapshot_date_db_format?>')">
			<td class="ellipsis" style="min-width:30px;  max-width:30px;">
				
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:90px;  max-width:90px;" title="<?=$policy["policy_number"]?>">
				<?=$policy["policy_number"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:70px;  max-width:70px; padding-right:10px;" title="<?=$full_policy_coverage["insurer_company"]["company_name"]?>">
				<?=$full_policy_coverage["insurer_company"]["company_name"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:90px;  max-width:90px; padding-right:10px;" title="<?=$full_policy_coverage["agent_company"]["company_name"]?>">
				<?=$full_policy_coverage["agent_company"]["company_name"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:90px;  max-width:90px;" title="<?=$full_policy_coverage["insured_company"]["company_name"]?>">
				<?=$full_policy_coverage["insured_company"]["company_name"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:40px;  max-width:40px; text-align:right;" title="<?=$full_policy_coverage["ins_policy_profile"]["term"]?>">
				<?=$full_policy_coverage["ins_policy_profile"]["term"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:55px; max-width:55px; text-align:right;" title="">
				<?php if(!empty($full_policy_coverage["ins_policy_profile"]["term"])):?>
					$<?=number_format($full_policy_coverage["ins_policy_profile"]["total_cost"]/$full_policy_coverage["ins_policy_profile"]["term"])?>
				<?php endif;?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" title="<?=$full_policy_coverage["number_of_trucks"]?>">
				<?=$full_policy_coverage["number_of_trucks"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right; <?=$pd_style?>" title="<?=$full_policy_coverage["number_of_pd_coverages"]?>">
				<?=$full_policy_coverage["number_of_pd_coverages"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right; <?=$al_style?>" title="<?=$full_policy_coverage["number_of_al_coverages"]?>">
				<?=$full_policy_coverage["number_of_al_coverages"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right; <?=$dt_style?>" title="<?=$full_policy_coverage["number_of_dtr"]?>">
				<?=$full_policy_coverage["number_of_dtr"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right; <?=$cargo_style?>" title="<?=$full_policy_coverage["number_of_cargo_coverages"]?>">
				<?=$full_policy_coverage["number_of_cargo_coverages"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right; <?=$rbd_style?>" title="<?=$full_policy_coverage["number_of_rbd"]?>">
				<?=$full_policy_coverage["number_of_rbd"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right; <?=$trailers_style?>" title="<?=$full_policy_coverage["number_of_trailers"]?>">
				<?=$full_policy_coverage["number_of_trailers"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right; <?=$days_to_cancel_style?>" title="">
				<?=$days_to_cancel?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" title="<?=date("m/d/y",strtotime($full_policy_coverage["ins_policy_profile"]["expected_cancellation_date"]))?>">
				<?=date("m/d/y",strtotime($full_policy_coverage["ins_policy_profile"]["expected_cancellation_date"]))?>
			</td>
		</tr>
	</table>
</div>