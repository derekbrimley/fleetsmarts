<?php
		$img = "/images/billing_status_".$load['billing_status_number']."_icon.png";
		
		//DETERMINE NOTES IMAGE
		if(empty($load["billing_notes"]))
		{
			$notes_img = "/images/add_notes_empty.png";
		}
		else
		{
			$notes_img = "/images/add_notes.png";
		}
		
		//SHORTEN BILLING NOTES WITH ...
		$billing_notes_short = $load['billing_notes'];
		if (strlen($billing_notes_short)>15)
		{
			//find last space within length
			$last_space = strrpos(substr($billing_notes_short, 0, 20), ' ');
			$trimmed_text = substr($billing_notes_short, 0, $last_space);
		  
			//add ellipses (...)
			$billing_notes_short = $trimmed_text.' ...';
		}
		
		//MAKE TEXT FOR ORIGIN AND DESTINATION
		$pick_text = "";
		$pick_title = "";
		$these_picks = $load['load_picks'];
		sort($these_picks);
		foreach($these_picks as $pick)
		{
			$pick_text = $pick['stop']["city"].", ".$pick['stop']["state"];
			$pick_title = $pick['stop']["city"].", ".$pick['stop']["state"]." ".date("n/j/y",strtotime($pick["in_time"]));
			break;
		}
		
		$drop_text = "";
		$drop_title = "";
		$these_drops = $load['load_drops'];
		sort($these_drops);
		foreach($these_drops as $drop)
		{
			$drop_text = $drop['stop']["city"].", ".$drop['stop']["state"];
			$drop_title = $drop['stop']["city"].", ".$drop['stop']["state"]." ".date("n/j/y",strtotime($drop["in_time"]));
			break;
		}
		
		$load_url = $load['id'];
		$action_number = $load["status_number"];
		
		//STYLE OF THE CHECKBOX ACTION ICON
		$action_style = "cursor:pointer; position:relative; left:2px; height:16px; width:16px";
		
		$status_code = "";
		//DETERMINE STATUS CODES
		if ($load["billing_status_number"] == 1) //PENDING DIGITAL COPY
		{
			$status_code = "Digital";
		}
		else if ($load["billing_status_number"] == 2) //PENDING BILLING
		{
			$status_code = "Billing";
		}
		else if ($load["billing_status_number"] == 3) //PENDING FUNDING
		{
			$status_code = "Funding";
		}
		else if ($load["billing_status_number"] == 4) //PENDING HARD COPY PROCESSED
		{
			$status_code = "Scan";
		}
		else if ($load["billing_status_number"] == 5) //PENDING HARD COPY SENT
		{
			$status_code = "Mailing";
		}
		else if ($load["billing_status_number"] == 6) //PENDING HARD COPY RECEIVED
		{
			$status_code = "Receival";
		}
		else if ($load["billing_status_number"] == 7) //PENDING INVOICE CLOSED
		{
			$status_code = "Closure";
		}
		else if ($load["billing_status_number"] == 8) //PENDING INVOICE CLOSED
		{
			$status_code = "Closed";
		}
		
		//IF RC LINK IS AVAILABLE, MAKE LINK TEXT
		$bol_link_text = "";
		if(!empty($load["bol_link"]))
		{
			$bol_link_text = "<a href='".$load['bol_link']."' target='_blank'>BOL</a> ";
		}
		
		$rate_link_text = "<a href='".$load['rc_link']."' target='_blank'>$".number_format($load['expected_revenue'], 2,'.','')."</a> ";
		
		$load_number_link_text = "<a href='".base_url('index.php/billing/hc_coversheet')."/".$load["id"]."' target='_blank'>".$load['customer_load_number']."</a> ";
		
		//IF LOAD IS NOT FACTOR, TURN GREY
		$row_style = "";
		if($load["billing_method"] != "Factor")
		{
			$row_style = "color:#CFCFCF";
		}
		
		//MAKE DROP DATE RED IF LATE
		$drop_date_color = "";
		if($load["billing_status_number"] < 4)
		{
			if (strtotime($load["final_drop_datetime"]) < (time() - (2*24*60*60)))
			{
				$drop_date_color = "color:red; font-weight:bold;";
			}
		}
		
		
		
?>

<td style="overflow:hidden; min-width:33px;  max-width:33px;  cursor:default;"   VALIGN="top" ><img title="Billing Checklist" onclick="open_billing_checklist('<?=$load_url?>')" style="<?=$action_style?>" src="<?=$img?>" /></td>
<td style="overflow:hidden; min-width:55px;  max-width:55px;  line-height:18px;" VALIGN="top" title="<?=$load['billing_status']?>" onclick="view_load_details('<?=$load_url?>')"><?=$status_code?></td>
<td style="overflow:hidden; min-width:85px; max-width:85px; line-height:18px;" VALIGN="top" title="HC Coversheet <?=$load['customer_load_number']?>"><?=$load_number_link_text?></td>
<td style="overflow:hidden; min-width:60px;  max-width:60px;  line-height:18px;" VALIGN="top" onclick="view_load_details('<?=$load_url?>')"><?=$load["fleet_manager"]["f_name"]?></td>
<td style="overflow:hidden; min-width:85px; max-width:85px; line-height:18px;" VALIGN="top" title="<?=$load['client']["company"]["company_name"]?>" onclick="view_load_details('<?=$load_url?>')"><?=$load['client']["company"]["company_side_bar_name"]?></td>
<td style="overflow:hidden; min-width:85px; max-width:85px; line-height:18px;" VALIGN="top" title="<?=$load["billed_under_carrier"]["company_name"]?>" onclick="view_load_details('<?=$load_url?>')"><?=$load["billed_under_carrier"]["company_side_bar_name"]?></td>
<td style="overflow:hidden; min-width:65px;  max-width:65px;  line-height:18px;" VALIGN="top" onclick="view_load_details('<?=$load_url?>')" title="<?=$load['broker']["customer_name"]?>"><?=$load['broker']["customer_name"]?></td>
<td style="overflow:hidden; min-width:60px;  max-width:60px;  line-height:18px" VALIGN="top" onclick="view_load_details('<?=$load_url?>')"><?=$load['broker']["mc_number"]?></td>
<td style="overflow:hidden; min-width:52px;  max-width:52px;  line-height:18px; text-align:right;" VALIGN="top" title="Rate Con"><?=$rate_link_text?></td>
<td style="overflow:hidden; min-width:75px;  max-width:75px;  line-height:18px; padding-left:10px;" VALIGN="top" title="<?=$pick_title?>" onclick="view_load_details('<?=$load_url?>')"><?=$pick_text?></td>
<td style="overflow:hidden; min-width:75px;  max-width:75px;  line-height:18px; padding-left:10px;" VALIGN="top" title="<?=$drop_title?>" onclick="view_load_details('<?=$load_url?>')"><?=$drop_text?></td>
<td style="overflow:hidden; min-width:65px;  max-width:65px;  line-height:18px; padding-left:10px; <?=$drop_date_color?>" VALIGN="top" onclick="view_load_details('<?=$load_url?>')"><?=date("m/d/y",strtotime($load["final_drop_datetime"]))?></td>
<td style="overflow:hidden; min-width:65px;  max-width:65px; line-height:18px;" VALIGN="top" onclick="view_load_details('<?=$load_url?>')"><?=$load["invoice_number"]?></td>
<td style="overflow:hidden; min-width:30px;  max-width:30px; padding-left:5px;"  VALIGN="top" ><img title="<?=$load['billing_notes']?>" onclick="open_billing_notes('<?=$load_url?>')" style="<?=$action_style?>" src="<?=$notes_img?>" /></td>
<td style="overflow:hidden; cursor:default;  padding-left:10px;" VALIGN="top"><?=$bol_link_text?></td>