<script>
	$("#scrollable_content").height($("#body").height() - 195);
</script>
<style>
	.header_stats
	{
		font-size:14px;
	}
</style>
<div id="main_content_header">
	<div id="plain_header" style="font-size:16px;">
		<div style="float:right; width:25px; margin-left:20px;">
			<img id="filter_loading_icon" name="filter_loading_icon" src="/images/loading.gif" style="float:right; height:20px; padding-top:5px; display:none;" />
			<img id="refresh_logs" name="refresh_logs" src="/images/refresh.png" title="Refresh Log" style="cursor:pointer; float:right; height:20px; padding-top:5px;" onclick="load_missing_paperwork_report()" />
		</div>
		<div id="expected_total" class="header_stats"  style="width:160px; float:right; font-weight:bold; text-align:right;">Expected</div>
		<div id="count_total" class="header_stats"  style="float:right; font-weight:bold;">Count </div>
		<div style="float:left; font-weight:bold;">Missing Paperwork</div>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px; font-size:12px;">
	<tr class="heading" style="line-height:30px;">
		<td style="width:30px; padding-left:5px;" VALIGN="top">FM</td>
		<td style="width:110px;" VALIGN="top">Load</td>
		<td style="width:100px;" VALIGN="top">Broker</td>
		<td style="width:55px; padding-left:10px;" VALIGN="top">Method</td>
		<td style="width:75px; text-align:right;" VALIGN="top">Expected</td>
		<td style="width:75px; padding-left:10px;" VALIGN="top">Driver</td>
		<td style="width:120px;" VALIGN="top">Drop Location</td>
		<td style="width:80px; text-align:right;" VALIGN="top">Drop Date</td>
		<td style="width:80px; text-align:right;" VALIGN="top">DC Due</td>
		<td style="width:90px; text-align:right;" VALIGN="top">DC Received</td>
		<td style="width:70px; text-align:right;" VALIGN="top">HC Due</td>
		<td style="width:80px; text-align:right;" VALIGN="top">HC Received</td>
	</tr>
</table>
<div id="scrollable_content" class="scrollable_div">
	<table  style="table-layout:fixed; margin:5px; font-size:12px;">
		<?php 
			$i = 0;
			$funded_total = 0;
			$billed_total = 0;
			$short_paid = 0;
			$expected = 0;
			$numerator = 0;
			$denominator = 0;
		?>
		<?php foreach($loads as $load):?>
			<?php
				$i++;
				
				$fm_initials = substr($load["fleet_manager"]["f_name"],0,1).substr($load["fleet_manager"]["l_name"],0,1);
				
				$drop_date = "";
				if(!empty($load["final_drop_datetime"]))
				{
					$drop_date = date("n/d/y",strtotime($load["final_drop_datetime"]));
				}
				
				$digital_date = "";
				if(!empty($load["digital_received_datetime"]))
				{
					$digital_date = date("n/d/y",strtotime($load["digital_received_datetime"]));
				}
				
				$hc_received_date = "";
				if(!empty($load["hc_processed_datetime"]))
				{
					$hc_received_date = date("n/d/y",strtotime($load["hc_processed_datetime"]));
				}
				
				//CALCULATE DC DUE
				$dc_due_date = date("n/d/y",strtotime($load["final_drop_datetime"])+24*60*60);
				
				//CALCULATE HC DUE
				$hc_due_date = date("n/d/y",strtotime($load["final_drop_datetime"]));
				for ($d=0; $d<=7; $d++)
				{
					$hc_due_date = date("n/d/y",strtotime($hc_due_date)+24*60*60);
					if(date("N",strtotime($hc_due_date)) == 1)
					{
						break;
					}
				}
				
				//GET DROP LOCATION
				$drop_text = "";
				$drop_title = "";
				$these_drops = $load['load_drops'];
				sort($these_drops);
				foreach($these_drops as $drop)
				{
					$drop_text = $drop['stop']["city"].", ".$drop['stop']["state"];
					$drop_title = $drop_title."\n".$drop['stop']["city"].", ".$drop['stop']["state"]."  ".date("n/j h:i",strtotime($drop["appointment_time"]));
				}
				$drop_title = substr($drop_title,1);
				
				//DETERMINE DC DUE DATE COLOR
				$dc_due_date_color = "";
				if(strtotime($dc_due_date) >= strtotime(time()))
				{
					$dc_due_date_color = "color:red; font-weight:bold;";
				}
				
				//DETERMINE HC DUE DATE COLOR
				$hc_due_date_color = "";
				if(strtotime($hc_due_date) >= strtotime(time()))
				{
					$hc_due_date_color = "color:red; font-weight:bold;";
				}
				
				$background_color = "";
				if($i%2 == 1)
				{
					$background_color = "background-color:#F2F2F2;";
				}

				//EXPECTED TOTAL
				$expected = $expected + $load["expected_revenue"];
				
				//GET DRIVER NAME
				$main_driver = $load["client"]["client_nickname"];
				
			?>
			<tr class="" style="line-height:30px; <?=$background_color?>">
				<td style="width:30px; padding-left:5px;" VALIGN="top"><?=$fm_initials ?></td>
				<td style="width:110px;" VALIGN="top"><?=$load["customer_load_number"]?></td>
				<td style="min-width:100px; max-width:100px;" VALIGN="top" class="ellipsis" title="<?=$load["broker"]["customer_name"]?>"><?=$load["broker"]["customer_name"]?></td>
				<td style="width:55px; padding-left:10px;" VALIGN="top"><?=$load["billing_method"]?></td>
				<td style="width:75px; text-align:right;" VALIGN="top"><?=number_format($load["expected_revenue"],2)?></td>
				<td style="width:75px; padding-left:10px;" VALIGN="top" title="<?=$main_driver?>"><?=substr($main_driver,0,strpos($main_driver," ")+2)?></td>
				<td style="min-width:120px; max-width:120px;" VALIGN="top" class="ellipsis" title="<?=$drop_title?>"><?=$drop_text?></td>
				<td style="width:80px; text-align:right;" VALIGN="top"><?=$drop_date?></td>
				<td style="width:80px; text-align:right; <?=$dc_due_date_color?>" VALIGN="top"><?=$dc_due_date?></td>
				<td style="width:90px; text-align:right;" VALIGN="top"><?=$digital_date?></td>
				<td style="width:70px; text-align:right; <?=$hc_due_date_color?>" VALIGN="top"><?=$hc_due_date?></td>
				<td style="width:80px; text-align:right;" VALIGN="top"><?=$hc_received_date?></td>
			</tr>
		<?php endforeach;?>
	</table>
</div>
<script>
	$("#expected_total").html("$<?=number_format($expected,2)?>");
	$("#count_total").html("Count <?=$i?>");
</script>