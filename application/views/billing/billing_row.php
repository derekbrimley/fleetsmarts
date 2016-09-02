<?php
	date_default_timezone_set('America/Denver');
	
	$load_id = $load["id"];
	
	$fm_initials = substr($load["fleet_manager"]["f_name"],0,1).substr($load["fleet_manager"]["l_name"],0,1);
	
	$dm_initials = substr($load["driver_manager"]["f_name"],0,1).substr($load["driver_manager"]["l_name"],0,1);
	
	//GET DRIVER 2
	$where = null;
	$where["id"] = $load["driver2_id"];
	$driver2_client = db_select_client($where);
	
	//GET AR SPECIALIST USER
	$where = null;
	$where["id"] = $load["ar_specialist_id"];
	$ar_user = db_select_user($where);
	$ars_initials = substr($ar_user["person"]["f_name"],0,1).substr($ar_user["person"]["l_name"],0,1);
	
	$billing_date = "";
	$billing_date_text_title = "";
	if(!empty($load["billing_datetime"]))
	{
		$billing_date = date("m/d/y",strtotime($load["billing_datetime"]));
		$billing_date_text_title = date("m/d/y H:i",strtotime($load["billing_datetime"]));
	}
	
	$drop_date_text = "";
	if(!empty($load["final_drop_datetime"]))
	{
		$drop_date_text = date("m/d/y H:i",strtotime($load["final_drop_datetime"]));
	}
	
	$pushed_date_text = "";
	$pushed_date_text_title = "";
	if(!empty($load["pushed_datetime"]))
	{
		$pushed_date_text = date("m/d/y",strtotime($load["pushed_datetime"]));
		$pushed_date_text_title = date("m/d/y H:i",strtotime($load["pushed_datetime"]));
	}
	
	$expected_payment_text = "";
	if(!empty($load["expected_pay_datetime"]))
	{
		$expected_payment_text = date("m/d/y",strtotime($load["expected_pay_datetime"]));
	}
	//CALC AGE
	$now = time();
	$drop_date = strtotime($load["final_drop_datetime"]);
	$age = floor(($now - $drop_date)/(60*60*24));
	
	$funded_style = "color:red; font-weight:bold;";
	$funding_date = "Unverified";
	if(!empty($load["funded_datetime"]))
	{
		$funding_date = date("m/d/y",strtotime($load["funded_datetime"]));
		$funded_style = "";
	}
	
	$billed_text = "";
	if(!empty($load["amount_billed"]))
	{
		$billed_text =number_format($load["amount_billed"],2);
		
		//DENOMINATOR
		//$denominator = $denominator + $load["amount_billed"];
	}
	else
	{
		//DENOMINATOR
		//$denominator = $denominator + $load["expected_revenue"];
	}
	
	$funded_text = "";
	if(!empty($load["amount_funded"]))
	{
		$funded_text = number_format($load["amount_funded"]+$load["financing_cost"],2);
	}

	$short_amount = 0;
	$short_text = "";
	$short_color = "";
	if(!empty($load["amount_short_paid"]))
	{
		$short_amount = $load["amount_short_paid"];
		
		if($load["amount_short_paid"] > .015 || $load["amount_short_paid"] < -.015)
		{
			$short_text = number_format($load["amount_short_paid"],2);
			
			if($load["amount_short_paid"] > 0)
			{
				$short_color = " color:red; ";
			}
			else
			{
				$short_color = " color:green; ";
			}
		}
	}
	
	//GET DROP LOCATION TEXT
	$drop_goalpoint = get_final_drop_goalpoint($load_id);
	$drop_text = $drop_goalpoint["location"];
	
	//GET BILLING NOTES
	$where = null;
	$where["note_type"] = "load_billing";
	$where["note_for_id"] = $load_id;
	$notes = db_select_notes($where,"note_datetime DESC");
	
	$notes_title = "";
	if(!empty($notes))
	{
		foreach($notes as $note)
		{
			$notes_title = $notes_title.date("m/d/y H:i",strtotime($note["note_datetime"]))." ".$note["note_text"]."\n\n";
		}
	}
	
	$last_update_text = "";
	//if($view == 'Updates')
	//{
		//GET DATETIME FOR 12 HOURS AGO
		$twelve_hours_ago_time = time() - 60*60*12;
		$twelve_hours_ago = date('Y-m-d H:i',$twelve_hours_ago_time);
		//GET LAST UPDATE FROM BILLING NOTES
		$where = null;
		$where = " note_type = 'load_billing' AND note_for_id = $load_id AND note_datetime > '$twelve_hours_ago'";
		$last_note = db_select_note($where,'note_datetime');
		if(!empty($last_note))
		{
			//GET NOTE USER
			$where = null;
			$where["id"] = $last_note["user_id"];
			$note_user = db_select_user($where);
		
			$initials = substr($note_user["person"]["f_name"],0,1).substr($note_user["person"]["l_name"],0,1);
			$last_update_text = $initials." ".date("m/d/y H:i",strtotime($last_note["note_datetime"]))." ".$last_note["note_text"];
		}
	//}
	
	$load_number_link_text = "<a href='".base_url('index.php/billing/hc_coversheet')."/".$load["id"]."' target='_blank'>".$load['customer_load_number']."</a> ";
	
	//GET PROCESS AUDIT
	$where = null;
	$where["load_id"] = $load_id;
	$process_audit = db_select_load_process_audit($where);
?>
<table  style="table-layout:fixed; font-size:10px;">
	<tr class="" style="line-height:30px;">
		<?php if($load["billing_status"] == "Digital"):?>
			<td onclick="open_billing_checklist_dialog('<?=$load_id?>')" style="width:25px; padding-left:5px;" VALIGN="top"><img id="action_icon_<?=$load_id?>" style="height:16px; position:relative; top:7px; left:5px;" src="/images/white_check_black_box.png" title=""/></td>
			<td class="pending_td" onclick="row_clicked('<?=$load_id?>')" style="width:60px;" VALIGN="top">Digital</td>
		<?php elseif($load["billing_status"] == "Envelope"):?>
			<td onclick="open_billing_checklist_dialog('<?=$load_id?>')" style="width:25px; padding-left:5px;" VALIGN="top"><img id="action_icon_<?=$load_id?>" style="height:16px; position:relative; top:7px; left:5px;" src="/images/white_check_sky_blue_box.png" title=""/></td>
			<td class="pending_td" onclick="row_clicked('<?=$load_id?>')" style="width:60px;" VALIGN="top">Envelope</td>
		<?php elseif($load["billing_status"] == "Dropbox"):?>
			<td onclick="open_billing_checklist_dialog('<?=$load_id?>')" style="width:25px; padding-left:5px;" VALIGN="top"><img id="action_icon_<?=$load_id?>" style="height:16px; position:relative; top:7px; left:5px;" src="/images/white_check_pink_box.png" title=""/></td>
			<td class="pending_td" onclick="row_clicked('<?=$load_id?>')" style="width:60px;" VALIGN="top">Dropbox</td>
		<?php elseif($load["billing_status"] == "Billing"):?>
			<td onclick="open_billing_checklist_dialog('<?=$load_id?>')" style="width:25px; padding-left:5px;" VALIGN="top"><img id="action_icon_<?=$load_id?>" style="height:16px; position:relative; top:7px; left:5px;" src="/images/white_check_orange_box.png" title=""/></td>
			<td class="pending_td" onclick="row_clicked('<?=$load_id?>')" style="width:60px;" VALIGN="top">Billing</td>
		<?php elseif($load["billing_status"] == "Funding"):?>
			<td onclick="open_billing_checklist_dialog('<?=$load_id?>')" style="width:25px; padding-left:5px;" VALIGN="top"><img id="action_icon_<?=$load_id?>" style="height:16px; position:relative; top:7px; left:5px;" src="/images/white_check_red_box.png" title=""/></td>
			<td class="pending_td" onclick="row_clicked('<?=$load_id?>')" style="width:60px;" VALIGN="top">Funding</td>
		<?php elseif($load["billing_status"] == "Scanning"):?>
			<td onclick="open_billing_checklist_dialog('<?=$load_id?>')" style="width:25px; padding-left:5px;" VALIGN="top"><img id="action_icon_<?=$load_id?>" style="height:16px; position:relative; top:7px; left:5px;" src="/images/white_check_yellow_box.png" title=""/></td>
			<td class="pending_td" onclick="row_clicked('<?=$load_id?>')" style="width:60px;" VALIGN="top">Scanning</td>
		<?php elseif($load["billing_status"] == "Recoursed"):?>
			<td onclick="open_billing_checklist_dialog('<?=$load_id?>')" style="width:25px; padding-left:5px;" VALIGN="top"><img id="action_icon_<?=$load_id?>" style="height:16px; position:relative; top:7px; left:5px;" src="/images/white_check_purple_box.png" title=""/></td>
			<td class="pending_td" onclick="row_clicked('<?=$load_id?>')" style="width:60px;" VALIGN="top">Recoursed</td>
		<?php elseif($load["billing_status"] == "Hold"):?>
			<td onclick="open_billing_checklist_dialog('<?=$load_id?>')" style="width:25px; padding-left:5px;" VALIGN="top"><img id="action_icon_<?=$load_id?>" style="height:16px; position:relative; top:7px; left:5px;" src="/images/white_check_dark_blue_box.png" title=""/></td>
			<td class="pending_td" onclick="row_clicked('<?=$load_id?>')" style="width:60px;" VALIGN="top">Hold</td>
		<?php elseif($load["billing_status"] == "Closing"):?>
			<td onclick="open_billing_checklist_dialog('<?=$load_id?>')" style="width:25px; padding-left:5px;" VALIGN="top"><img id="action_icon_<?=$load_id?>" style="height:16px; position:relative; top:7px; left:5px;" src="/images/white_check_light_green_box.png" title=""/></td>
			<td class="pending_td" onclick="row_clicked('<?=$load_id?>')" style="width:60px;" VALIGN="top">Closing</td>
		<?php elseif($load["billing_status"] == "Closed"):?>
			<td onclick="open_billing_checklist_dialog('<?=$load_id?>')" style="width:25px; padding-left:5px;" VALIGN="top"><img id="action_icon_<?=$load_id?>" style="height:16px; position:relative; top:7px; left:5px;" src="/images/white_check_green_box.png" title=""/></td>
			<td class="pending_td" onclick="row_clicked('<?=$load_id?>')" style="width:60px;" VALIGN="top">Closed</td>
		<?php else:?>
			<td onclick="open_billing_checklist_dialog('<?=$load_id?>')" style="width:25px; padding-left:5px;" title="No Status Found" VALIGN="top"></td>
			<td class="pending_td" onclick="row_clicked('<?=$load_id?>')" style="width:60px;" title="No Status Found" VALIGN="top"></td>
		<?php endif;?>
		<td onclick="row_clicked('<?=$load_id?>')" style="width:70px; padding-right:5px;" VALIGN="top"><?=$load_number_link_text?></td>
		<td class="fm_td" onclick="row_clicked('<?=$load_id?>')" style="width:30px;" VALIGN="top" title="<?=$load["fleet_manager"]["f_name"]." ".$load["fleet_manager"]["l_name"]?>"><?=$fm_initials ?></td>
		<td class="dm_td" onclick="row_clicked('<?=$load_id?>')" style="width:30px;" VALIGN="top" title="<?=$load["driver_manager"]["f_name"]." ".$load["driver_manager"]["l_name"]?>"><?=$dm_initials ?></td>
		<td onclick="row_clicked('<?=$load_id?>')" style="width:30px;" VALIGN="top" title="<?=$ar_user["person"]["f_name"]." ".$ar_user["person"]["l_name"]?>"><?=$ars_initials ?></td>
		<td class="driver1_td" onclick="row_clicked('<?=$load_id?>')" style="min-width:55px; max-width:55px;" VALIGN="top" class="ellipsis" title="<?=$load["client"]["client_nickname"]?>"><?=$load["client"]["client_nickname"]?></td>
		<td class="driver2_td" onclick="row_clicked('<?=$load_id?>')" style="min-width:55px; max-width:55px;" VALIGN="top" class="ellipsis" title="<?=$driver2_client["client_nickname"]?>"><?=$driver2_client["client_nickname"]?></td>
		<td onclick="row_clicked('<?=$load_id?>')" style="min-width:60px; max-width:60px;" VALIGN="top" class="ellipsis" title="<?=$load["billed_under_carrier"]["company_side_bar_name"]?>"><?=$load["billed_under_carrier"]["company_side_bar_name"]?></td>
		<?php if($load["originals_required"] == "Yes" && empty($load["hc_received_datetime"])):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="min-width:60px; max-width:60px; color:red; font-weight:bold;" VALIGN="top" class="ellipsis broker_td" title="<?=$load["broker"]["customer_name"]?>"><?=$load["broker"]["customer_name"]?></td>
		<?php else:?>
			<td onclick="row_clicked('<?=$load_id?>')" style="min-width:60px; max-width:60px;" VALIGN="top" class="ellipsis broker_td" title="<?=$load["broker"]["customer_name"]?>"><?=$load["broker"]["customer_name"]?></td>
		<?php endif;?>
		<?php if(!empty($load["amount_billed"])):?>
			<td style="width:50px; padding-right:5px; text-align:right;" VALIGN="top"><a class="" title="Billed" target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$load["rc_link"]?>"><?=number_format($load["amount_billed"],2)?></a></td>
		<?php else:?>		
			<td style="width:50px; padding-right:5px; text-align:right;" VALIGN="top"><a class="" title="Expected" target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$load["rc_link"]?>"><?=number_format($load["expected_revenue"],2)?></a></td>
		<?php endif;?>
		<td onclick="row_clicked('<?=$load_id?>')" style="min-width:60px; max-width:60px; padding-right:5px;"  class="ellipsis drop_city_td" VALIGN="top" title="<?=$drop_text?>"><?=$drop_text?></td>
		<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; text-align:right;" VALIGN="top" title="<?=$pushed_date_text_title?>"><?=$pushed_date_text?></td>
		<td class="billed_td" onclick="row_clicked('<?=$load_id?>')" style="width:50px; padding-right:5px; text-align:right;" VALIGN="top" title="<?=$billing_date_text_title?>"><?=$billing_date?></td>
		<td class="method_td" onclick="row_clicked('<?=$load_id?>')" style="width:50px;" VALIGN="top"><?=$load["billing_method"]?></td>
		<?php if(strtotime($load["expected_pay_datetime"]) > time()):?>
			<td class="expect_payment_td" onclick="row_clicked('<?=$load_id?>')" style="width:50px; text-align:right;" VALIGN="top"><?=$expected_payment_text?></td>
		<?php else:?>
			<td class="expect_payment_td" onclick="row_clicked('<?=$load_id?>')" style="width:50px; color:red; font-weight:bold; text-align:right;" VALIGN="top"><?=$expected_payment_text?></td>
		<?php endif;?>
		<td class="age_td" onclick="row_clicked('<?=$load_id?>')" style="width:45px; text-align:right;" VALIGN="top" title="<?=$drop_date_text?>"><?=$age?></td>
		<?php if(empty($load["short_pay_report_guid"])):?>
			<td class="short_td" onclick="row_clicked('<?=$load_id?>')" style="width:50px; text-align:right; font-weight:bold; <?= $short_color ?>" VALIGN="top"><?=$short_text?></td>
		<?php else:?>
			<td class="short_td" onclick="" style="width:50px; text-align:right;" VALIGN="top"><a class="" target="_blank" href="<?=base_url("/index.php/documents/download_file")."/".$load["short_pay_report_guid"]?>"><?=$short_text?></a></td>
		<?php endif;?>
		<td class="funded_td" onclick="row_clicked('<?=$load_id?>')" style="width:50px; text-align:right; padding-right:5px; <?=$funded_style?>" VALIGN="top" title="<?=$funding_date?>"><?=$funded_text?></td>
		<td onclick="row_clicked('<?=$load_id?>')" style="max-width:85px; min-width:85px; padding-left:10px; padding-right:5px; display:none;" VALIGN="top" class="hold_reason_td ellipsis"><?=$load["denied_reason"]?></td>
		<td onclick="row_clicked('<?=$load_id?>')" style="max-width:300px; min-width:300px; display:none;" VALIGN="top" title="<?=$last_update_text?>" class="last_update_td ellipsis"><?=$last_update_text?></td>
		
		<?php if($process_audit["defer_to_tarriff"] == "Pass"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center; padding-left:10px;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Pass" src="/images/green_checkmark.png"/></td>
		<?php elseif($process_audit["defer_to_tarriff"] == "Fail"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Fail" src="/images/red_exclamation_mark.png"/></td>
		<?php else:?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"></td>
		<?php endif;?>
		
		<?php if($process_audit["ontime_by_rc"] == "Pass"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Pass" src="/images/green_checkmark.png"/></td>
		<?php elseif($process_audit["ontime_by_rc"] == "Fail"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Fail" src="/images/red_exclamation_mark.png"/></td>
		<?php else:?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"></td>
		<?php endif;?>
		
		<?php if($process_audit["shipper_load_and_count"] == "Pass"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Pass" src="/images/green_checkmark.png"/></td>
		<?php elseif($process_audit["shipper_load_and_count"] == "Fail"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Fail" src="/images/red_exclamation_mark.png"/></td>
		<?php else:?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"></td>
		<?php endif;?>
		
		<?php if($process_audit["seal_pic_depart"] == "Pass"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Pass" src="/images/green_checkmark.png"/></td>
		<?php elseif($process_audit["seal_pic_depart"] == "Fail"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Fail" src="/images/red_exclamation_mark.png"/></td>
		<?php else:?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"></td>
		<?php endif;?>
		
		<?php if($process_audit["load_pic_depart"] == "Pass"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Pass" src="/images/green_checkmark.png"/></td>
		<?php elseif($process_audit["load_pic_depart"] == "Fail"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Fail" src="/images/red_exclamation_mark.png"/></td>
		<?php else:?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"></td>
		<?php endif;?>
		
		<?php if($process_audit["seal_number"] == "Pass"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Pass" src="/images/green_checkmark.png"/></td>
		<?php elseif($process_audit["seal_number"] == "Fail"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Fail" src="/images/red_exclamation_mark.png"/></td>
		<?php else:?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"></td>
		<?php endif;?>
		
		<?php if($process_audit["seal_pic_arrive"] == "Pass"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Pass" src="/images/green_checkmark.png"/></td>
		<?php elseif($process_audit["seal_pic_arrive"] == "Fail"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Fail" src="/images/red_exclamation_mark.png"/></td>
		<?php else:?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"></td>
		<?php endif;?>
		
		<?php if($process_audit["load_pic_arrive"] == "Pass"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Pass" src="/images/green_checkmark.png"/></td>
		<?php elseif($process_audit["load_pic_arrive"] == "Fail"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Fail" src="/images/red_exclamation_mark.png"/></td>
		<?php else:?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"></td>
		<?php endif;?>
		
		<?php if($process_audit["seal_intact"] == "Pass"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Pass" src="/images/green_checkmark.png"/></td>
		<?php elseif($process_audit["seal_intact"] == "Fail"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Fail" src="/images/red_exclamation_mark.png"/></td>
		<?php else:?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"></td>
		<?php endif;?>
		
		<?php if($process_audit["clean_bills"] == "Pass"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Pass" src="/images/green_checkmark.png"/></td>
		<?php elseif($process_audit["clean_bills"] == "Fail"):?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"><img style="height:14px; position:relative; top:8px; right:3px;" title="Fail" src="/images/red_exclamation_mark.png"/></td>
		<?php else:?>
			<td onclick="row_clicked('<?=$load_id?>')" style="width:50px; display:none; text-align:center;" VALIGN="top" class="process_audit"></td>
		<?php endif;?>
		
		
		<?php if(empty($notes)):?>
			<td class="notes_td" onclick="open_notes('<?=$load_id?>')" style="width:25px; padding-left:5px;" VALIGN="top"><img style="height:16px; position:relative; top:7px; left:5px;" src="/images/add_notes_empty.png" title="<?=htmlspecialchars($notes_title)?>"/></td>
		<?php else:?>
			<td class="notes_td" onclick="open_notes('<?=$load_id?>')" style="width:25px; padding-left:5px;" VALIGN="top"><img style="height:16px; position:relative; top:7px; left:5px;" src="/images/add_notes.png" title="<?=htmlspecialchars($notes_title)?>"/></td>
		<?php endif;?>
	</tr>
</table>
