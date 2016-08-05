<script>
	$("#scrollable_content").height($("#body").height() - 195);
	
	$("#edit_cell_form").submit(function (e) {
        e.preventDefault(); //prevent default form submit
		save_edit_cell();
    });
</script>
<style>
	table#log_table td
	{
		vertical-align:top;
		line-height:15px;
	}
	
	.settlement_row:hover
	{
		background:#FAFAFA;
	}
</style>
<div id="main_content_header">
	<div id="">
		<span style="font-weight:bold;">Commissions</span>
		<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px;" />
		<span id="booking_stats_box_1" style="font-size:16px; float:right; margin-left:40px;">Total Commissions: <?=number_format($booking_stats["total_commission"],2)?></span>
		<span id="booking_stats_box_2" style="font-size:16px; float:right; margin-left:40px;">Carrier Rate: <?=number_format($booking_stats["carrier_rate"],2)?></span>
		<span id="booking_stats_box_3" style="font-size:16px; float:right; margin-left:40px;">Booking Rate: <?=number_format($booking_stats["booking_rate"],2)?></span>
		<span id="booking_stats_box_4" style="font-size:16px; float:right; margin-left:40px;">Total Miles: <?=$booking_stats["total_miles"]?></span>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:30px;" VALIGN="top"></td>
		<td style="width:35px;" VALIGN="top">FM</td>
		<td style="width:55px;" VALIGN="top">Client</td>
		<td style="width:85px;" VALIGN="top">Broker</td>
		<td style="width:60px;" VALIGN="top">Load</td>
		<td style="width:65px;" VALIGN="top">Pick</td>
		<td style="width:65px;" VALIGN="top">Drop</td>
		<td style="width:55px; text-align:right;" VALIGN="top">Rate</td>
		<td style="width:55px; padding-left:10px;" VALIGN="top">Dropped</td>
		<td style="width:50px; text-align:right;" VALIGN="top">Shorted</td>
		<td style="width:60px; text-align:right;" VALIGN="top">Funded</td>
		<td style="width:75px; text-align:right;" VALIGN="top">Carrier Rev</td>
		<td style="width:80px; text-align:right;" VALIGN="top">Funded/mile</td>
		<td style="width:55px; text-align:right;" VALIGN="top">CR/mile</td>
		<td style="width:65px; text-align:right;" VALIGN="top">FM Margin</td>
		<td style="width:65px; text-align:right;" VALIGN="top">Approved</td>
		<td style="width:20px; text-align:right;" VALIGN="top"></td>
		<td></td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<?php if(!empty($loads)): ?>
		<?php foreach($loads as $load): ?>
			<?php
				$load_id = $load["id"];
			
				$drop_date = "";
				if(!empty($load["final_drop_datetime"]))
				{
					if($load["status_number"] < 7)
					{
						$drop_date = "";
					}
					else
					{
						$drop_date = date("m/d/y",strtotime($load["final_drop_datetime"]));
					}
				}
				
				$funded_date = "";
				if(!empty($load["funded_datetime"]))
				{
					$funded_date = date("m/d/y",strtotime($load["funded_datetime"]));
				}
				
				$settlement_date = "";
				if(!empty($load["commission_approved_datetime"]))
				{
					$settlement_date = date("m/d/y",strtotime($load["commission_approved_datetime"]));
				}
				
				
				$client_name = substr($load["client"]["client_nickname"],0,strpos($load["client"]["client_nickname"]," ")+2);
				
				//USE AMOUNT FUNDED + FINANCING COST FOR AMOUNT FUNDED
				$amount_funded = $load["amount_funded"] + $load["financing_cost"];
				
				$img = null;
				$title = "What is this?";
				//IF IN TRANSIT
				if($load["status_number"] < 7 && empty($load["funded_datetime"]) && empty($load["commission_approved_datetime"]))
				{
					$title = "In Transit";
					$img = '/images/in_transit.png'; 
				}
				//IF PENDING FUNDING
				else if($load["status_number"] == 7 && empty($load["funded_datetime"]) && empty($load["commission_approved_datetime"]))
				{
					$title = "Pending Funding";
					$img = '/images/pending_funding.png'; 
				}
				//IF PENDING SETTLEMENT
				else if($load["status_number"] == 7 && !empty($load["funded_datetime"]) && empty($load["commission_approved_datetime"]))
				{
					$title = "Pending Settlement";
					$img = '/images/pending_settlement.png'; 
				}
				//IF CLOSED
				else if($load["status_number"] == 7 && !empty($load["funded_datetime"]) && !empty($load["commission_approved_datetime"]))
				{
					$title = "Closed";
					$img = '/images/closed.png'; 
				}
				$status_img = '<img style="height:16px; position:relative; bottom:0px; margin-left:5px; margin-right:5px; cursor:pointer;" title="'.$title.'" src="'.$img.'" onclick="event_icon_clicked(\''.$load_id.'\')"/>';
				
				
				//MAKE EACH PICK OR DROP A LINK AND ADD A TITLE THAT SHOWS CITY,STATE
				$i = 1;
				$pick_text = "";
				$these_picks = $load['load_picks'];
				sort($these_picks);
				foreach($these_picks as $pick)
				{
					if($i == 1)
					{
						$pick_text = $pick['stop']["city"].", ".$pick['stop']["state"];
					}
				}
				
				$drop_text = "";
				$these_drops = $load['load_drops'];
				sort($these_drops);
				foreach($these_drops as $drop)
				{
					$drop_text = $drop['stop']["city"].", ".$drop['stop']["state"];
				}
				
				//FORMAT SHORTED AND FUNDED
				$shorted = "";
				$funded = "";
				if(!empty($load["funded_datetime"]))
				{
					$shorted = "$".number_format($load["amount_short_paid"],2);
					$funded = "$".number_format($amount_funded,2);
				}
				
				//GET FUNDED AND CR PER MILE
				$funded_per_mile = "";
				$cr_per_mile = "";
				$commission = "";
				$carrier_rev = "";
				if(!empty($load["map_miles"]))
				{
					$funded_per_mile = "$".number_format($amount_funded/$load["map_miles"],2);
					$cr_per_mile = "$".number_format($load["carrier_revenue"]/$load["map_miles"],2);
					$commission = number_format(calc_commission($load)/2,2);
					$carrier_rev = "$".number_format($load["carrier_revenue"],2);
				}
				
				//GET COMMISSION STATUS
				$commission_status = is_commission_good($load);
				
				$orb_img = '<img style="height:15px; position:relative; bottom:0px; margin-left:5px; margin-right:5px; cursor:pointer;" title="Click for Details" src="'.$commission_status["validation_icon"].'" onclick="alert(\''.$commission_status["validation_alert"].'\')"/>';
				
				//MAKE DIRECT BILLS ITALICS
				$italics = "";
				if($load["billing_method"] != "Factor")
				{
					$italics = "font-style:italic; color:grey;";
				}
				
			?>
			<div id="row_<?=$load["id"]?>" class="settlement_row" style="height:20px; overflow:hidden; padding-top:5px; padding-bottom:3px;">
				<table id="log_table" style="margin-left:3px; font-size:10px;">
					<tr style="height:15px;">
						<td style="overflow:hidden; min-width:30px;  max-width:30px;"><?=$status_img?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:35px;  max-width:35px;" VALIGN="middle" title="<?=htmlentities($load["fleet_manager"]["f_name"])?>"><?=htmlentities($load["fleet_manager"]["f_name"])?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px;" VALIGN="middle" title="<?=htmlentities($load["client"]["client_nickname"])?>"><?=htmlentities($client_name)?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:85px;  max-width:85px;" VALIGN="middle" title="<?=htmlentities($load["broker"]["customer_name"])?>"><?=htmlentities($load["broker"]["customer_name"])?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:60px;  max-width:60px;" VALIGN="middle" title="<?=htmlentities($load["customer_load_number"])?>"><?=htmlentities($load["customer_load_number"])?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:65px;  max-width:65px;" VALIGN="middle" title="<?=htmlentities($pick_text)?>"><?=htmlentities($pick_text)?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:65px;  max-width:65px;" VALIGN="middle" title="<?=htmlentities($drop_text)?>"><?=htmlentities($drop_text)?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right; <?=$italics?>" VALIGN="middle" title="<?=$load["billing_method"]?>">$<?=number_format($load["expected_revenue"],2)?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; padding-left:10px;" VALIGN="middle" title=""><?=$drop_date?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:50px;  max-width:50px; text-align:right; " VALIGN="middle" title=""><?=$shorted?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:60px;  max-width:60px; text-align:right; " VALIGN="middle" title=""><?=$funded?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:75px;  max-width:75px; text-align:right;" VALIGN="middle" title=""><?=$carrier_rev?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:80px;  max-width:80px; text-align:right;" VALIGN="middle" title=""><?=$funded_per_mile?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:55px;  max-width:55px; text-align:right;" VALIGN="middle" title=""><?=$cr_per_mile?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:65px;  max-width:65px; text-align:right;" VALIGN="middle" title=""><?=$commission?></td>
						<td class="ellipsis" style="overflow:hidden; min-width:65px;  max-width:65px; text-align:right;" VALIGN="middle" title=""><?=$settlement_date?></td>
						<td style="overflow:hidden; min-width:20px;  max-width:20px;"><?=$orb_img?></td>
					</tr>
				</table>
			</div>
			<div id="commission_details_<?=$load["id"]?>" style="display:none; font-size:12px; width:950px; margin-left:15px; min-height:30px; padding:10px; background:#EFEFEF;">
				<!-- AJAX GOES HERE !-->
				<img id="loading_icon" name="loading_icon" height="20px" src="/images/loading.gif" style="margin-left:450px; margin-top:5px;" />
			</div>
		<?php endforeach; ?>	
	<?php else: ?>
		<div style="margin:0 auto; margin-top:100px; width:230px;">There are no results for this search</div>
	<?php endif; ?>
</div>

