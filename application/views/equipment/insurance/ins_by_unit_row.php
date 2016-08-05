<?php
	
	$truck_coverage_full = get_full_unit_coverage($truck_coverage,$snapshot_date_db_format);
	//echo $truck_coverage["id"];
	//GET TRUCK
	
	
	$truck = $truck_coverage_full["unit"];
	if($truck["status"] == "On the road")
	{
		$img = "/images/on_the_road.png";
	}
	else if($truck["status"] == "In the shop")
	{
		$img = "/images/in_the_shop2.png";
	}
	else if($truck["status"] == "Subtruck")
	{
		$img = "/images/subtruck.png";
	}
	else if($truck["status"] == "Returned")
	{
		$img = "/images/turned_in.png";
	}
	
	//GET POLICY
	$ins_policy = $truck_coverage_full["ins_policy"];
	
	//GET POLICY PROFILE
	$ins_policy_profile = $truck_coverage_full["ins_policy_profile"];
	
	//GET INSURED COMPANY
	$insured_company = $truck_coverage_full["insured_company"];
	
	//GET INSURER COMPANY
	$insurer_company = $truck_coverage_full["insurer_company"];
	
	//GET AGENT COMPANY
	$agent_company = $truck_coverage_full["agent_company"];
	
	//GET FINANCIAL GUARANTOR
	$fg_client = $truck_coverage_full["fg_client"];
	
	$total_cost = $truck_coverage["al_um_bi_prem"]+$truck_coverage["al_uim_bi_prem"]+$truck_coverage["al_pip_prem"]+$truck_coverage["pd_comp_prem"]+$truck_coverage["pd_coll_prem"]+$truck_coverage["pd_rental_prem"]+$truck_coverage["al_prem"];
?>
<div class="clickable_row" style="background-color:#F7F7F7; padding-top:0px; padding-bottom:0px; min-height:30px;">
	<table style="">
		<tr class="" style="font-size:10px; height:30px; line-height:30px;" onclick="load_policy_details_view('<?=$ins_policy["id"]?>','<?=$snapshot_date_db_format?>')">
			<td class="ellipsis" style="min-width:30px;  max-width:30px;">
				<?php if($uc_i == 1):?>
					<img title="<?=$truck["status"]?>" style="height:15px; position:relative; left:5px; top:8px" src="<?=$img?>"/>
				<?php endif;?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:45px;  max-width:45px;" title="<?=$truck_coverage_full["unit_number"]?>">
				<?=$truck_coverage_full["unit_number"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:80px;  max-width:80px;" title="<?=$ins_policy["policy_number"]?>">
				<?=$ins_policy["policy_number"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:40px;  max-width:40px;" title="<?=$ins_policy_profile["term"]?>">
				<?=$ins_policy_profile["term"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:50px; max-width:50px;" title="">
				<?php if(!empty($ins_policy_profile["term"])):?>
					$<?=number_format($total_cost/$ins_policy_profile["term"])?>
				<?php endif;?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:70px;  max-width:70px;" title="<?=$insured_company["company_name"]?>">
				<?=$insured_company["company_name"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:70px;  max-width:70px;" title="<?=$insurer_company["company_name"]?>">
				<?=$insurer_company["company_name"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:70px;  max-width:70px;" title="<?=$agent_company["company_name"]?>">
				<?=$agent_company["company_name"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:50px;  max-width:50px; text-align:right;" title="<?=$truck_coverage["radius"]?>">
				<?=$truck_coverage["radius"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:45px;  max-width:45px; text-align:right;" title="<?=number_format($truck_coverage["pd_coll_ded"])?>">
				<?=number_format($truck_coverage["pd_coll_ded"])?>
			</td>
				<?php if($truck_coverage["pd_comp_prem"] > 0 || $truck_coverage["pd_coll_prem"] > 0):?>
					<td class="ellipsis" style="overflow:hidden; min-width:45px;  max-width:45px; text-align:right;" title="<?=number_format($truck_coverage["pd_limit"])?>">
						<?=number_format($truck_coverage["pd_limit"])?>
					</td>
				<?php else:?>
					<td class="ellipsis" style="overflow:hidden; min-width:45px;  max-width:45px; text-align:right;" title="0">
						0
					</td>
				<?php endif;?>
			<td class="ellipsis" style="overflow:hidden; min-width:60px;  max-width:60px; text-align:right;" title="<?=number_format(MIN($truck_coverage["al_um_bi_limit"],$truck_coverage["al_uim_bi_limit"],$truck_coverage["al_pip_limit"]))?>">
				<?=number_format(MIN($truck_coverage["al_um_bi_limit"],$truck_coverage["al_uim_bi_limit"],$truck_coverage["al_pip_limit"]))?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:50px;  max-width:50px; text-align:right;" title="<?=number_format($truck_coverage["pd_rental_daily_limit"])?>">
				<?=number_format($truck_coverage["pd_rental_daily_limit"])?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:50px;  max-width:50px; text-align:right;" title="<?=number_format($ins_policy_profile["cargo_limit"])?>">
				<?=number_format($ins_policy_profile["cargo_limit"])?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:60px;  max-width:60px; text-align:right; padding-right:10px;" title="<?=number_format($ins_policy_profile["rbd_limit"])?>">
				<?=number_format($ins_policy_profile["rbd_limit"])?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:60px;  max-width:60px;" title="<?=$fg_client["client_nickname"]?>">
				<?=$fg_client["client_nickname"]?>
			</td>
			<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" title="<?=date("m/d/y",strtotime($ins_policy_profile["expected_cancellation_date"]))?>">
				<?=date("m/d/y",strtotime($ins_policy_profile["expected_cancellation_date"]))?>
			</td>
			<td id="ins_status_<?=$truck["id"]?>" style="width:50px; padding-top:8px; padding-right:15px; text-align:right;">
				<?php if($uc_i == 1):?>
					<img id="ins_status_loading_<?=$truck["id"]?>" name="" src="/images/loading.gif" style="height:12px;" />
					<img id="ins_status_red_<?=$truck["id"]?>" src="/images/red_exclamation_mark.png" style="height:15px; position:relative; right:4px; display:none;"title="" onclick=""/>
					<img id="ins_status_green_<?=$truck["id"]?>" src="/images/green_checkmark.png" style="height:15px; display:none;"title="" onclick=""/>
				<?php endif;?>
			</td>
		</tr>
	</table>
</div>