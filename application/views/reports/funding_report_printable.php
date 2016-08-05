<title>Funding Report</title>
<link rel="shortcut icon" href="<?php echo base_url("favicon.ico");?>" />
<link type="text/css" href="<?php echo base_url("css/custom-theme/jquery-ui-1.8.16.custom.css"); ?>" rel="stylesheet" />	
<link type="text/css" href="<?php echo base_url("css/css_reset.css"); ?>" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url("css/fleet_smarts_template.css"); ?>" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url("css/main_menu.css"); ?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo base_url("js/my_js_helper.js");?>"></script>	
<script type="text/javascript" src="<?php echo base_url("js/jquery-1.6.2.min.js");?>"></script>
<script type="text/javascript" src="<?php echo base_url("js/jquery-ui-1.8.16.custom.min.js");?>"></script>

<div id="main_content_header" style="width:810px; background:#DEDEDE">
	<div id="plain_header" style="font-size:12px;">
		<div id="percentage" class="header_stats"  style="width:60px; float:right; font-weight:bold; text-align:right;">%</div>
		<div id="funded_total" class="header_stats"  style="width:130px; float:right; font-weight:bold; text-align:right;">Funded </div>
		<div id="short_total" class="header_stats"  style="width:100px; float:right; font-weight:bold; text-align:right;">Short Paid </div>
		<div id="billed_total" class="header_stats"  style="width:120px; float:right; font-weight:bold; text-align:right;">Billed </div>
		<div id="expected_total" class="header_stats"  style="width:140px; float:right; font-weight:bold; text-align:right;">Expected </div>
		<div id="unfunded_count" class="header_stats"  style="width:100px; float:right; font-weight:bold; text-align:right;">Missing HC </div>
		<div id="count_total" class="header_stats"  style="float:right; font-weight:bold;">Count </div>
		<div style="float:left; font-weight:bold;">Funding Report</div>
	</div>
</div>
<table  style="table-layout:fixed; margin:5px;font-size:10px;">
	<tr style="line-height:30px; font-weight:bold;">
		<td style="width:30px; padding-left:5px;" VALIGN="top">FM</td>
		<td style="width:110px;" VALIGN="top">Load</td>
		<td style="width:100px;" VALIGN="top">Broker</td>
		<td style="width:55px; padding-left:10px;" VALIGN="top">Method</td>
		<td style="width:85px; text-align:right;" VALIGN="top">Expected</td>
		<td style="width:100px; text-align:right;" VALIGN="top">Drop Date</td>
		<td style="width:100px; text-align:right;" VALIGN="top">Bill Date</td>
		<td style="width:100px; text-align:right;" VALIGN="top">Billed</td>
		<td style="width:100px; text-align:right;" VALIGN="top">Funded Date</td>
		<td style="width:95px; text-align:right;" VALIGN="top">Short Paid</td>
		<td style="width:90px; text-align:right; padding-right:5px;" VALIGN="top">Funded</td>
	</tr>
	<?php 
			$i = 0;
			$funded_total = 0;
			$billed_total = 0;
			$short_paid = 0;
			$expected = 0;
			$numerator = 0;
			$denominator = 0;
			$missing_hc = 0;
		?>
		<?php foreach($loads as $load):?>
			<?php
				$i++;
				
				$fm_initials = substr($load["fleet_manager"]["f_name"],0,1).substr($load["fleet_manager"]["l_name"],0,1);
				
				$billing_date = "";
				if(!empty($load["billing_datetime"]))
				{
					$billing_date = date("n/d/y",strtotime($load["billing_datetime"]));
				}
				
				$drop_date = "";
				if(!empty($load["final_drop_datetime"]))
				{
					$drop_date = date("n/d/y",strtotime($load["final_drop_datetime"]));
				}
				
				$funding_date = "";
				if(!empty($load["funded_datetime"]))
				{
					$funding_date = date("n/d/y",strtotime($load["funded_datetime"]));
					$numerator++;
				}
				
				if(empty($load["hc_processed_datetime"]))
				{
					$missing_hc++;
				}
				
				$background_color = "";
				if($i%2 == 1)
				{
					$background_color = "background-color:#F2F2F2;";
				}
				
				$billed_text = "";
				if(!empty($load["amount_billed"]))
				{
					$billed_text =number_format($load["amount_billed"],2);
					
					//DENOMINATOR
					$denominator = $denominator + $load["amount_billed"];
				}
				else
				{
					//DENOMINATOR
					$denominator = $denominator + $load["expected_revenue"];
				}
				
				$funded_amount = 0;
				$funded_text = "";
				if(!empty($load["amount_funded"]))
				{
					$funded_amount = round($load["amount_funded"]+$load["financing_cost"],2);
					$funded_total = $funded_total + $funded_amount;
					$funded_text = number_format($funded_amount,2);
					//echo $funded_amount." -- ";
					
					
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
				
				//NUMERATOR
				//$numerator = $numerator + $funded_amount + $short_amount;
				
				
				
				//echo $funded_total."<br>";
				$billed_total = $billed_total + $load["amount_billed"];
				
				//SHORT PAID TOTAL
				$short_paid = $short_paid + $load["amount_short_paid"];

				//EXPECTED TOTAL
				$expected = $expected + $load["expected_revenue"];
				
			?>
			<tr class="" style="line-height:30px; <?=$background_color?>">
				<td style="width:30px; padding-left:5px;" VALIGN="top"><?=$fm_initials ?></td>
				<td style="width:110px;" VALIGN="top"><?=$load["customer_load_number"]?></td>
				<td style="min-width:100px; max-width:100px;" VALIGN="top" class="ellipsis" title="<?=$load["broker"]["customer_name"]?>"><?=$load["broker"]["customer_name"]?></td>
				<td style="width:55px; padding-left:10px;" VALIGN="top"><?=$load["billing_method"]?></td>
				<td style="width:85px; text-align:right;" VALIGN="top"><?=number_format($load["expected_revenue"],2)?></td>
				<td style="width:100px; text-align:right;" VALIGN="top"><?=$drop_date?></td>
				<td style="width:100px; text-align:right;" VALIGN="top"><?=$billing_date?></td>
				<td style="width:100px; text-align:right;" VALIGN="top"><?=$billed_text?></td>
				<td style="width:100px; text-align:right;" VALIGN="top"><?=$funding_date?></td>
				<td style="width:95px; text-align:right; font-weight:bold; <?= $short_color ?>" VALIGN="top"><?=$short_text?></td>
				<td style="width:90px; text-align:right; padding-right:5px;" VALIGN="top"><?=$funded_text?></td>
			</tr>
		<?php endforeach;?>
	</table>
</div>
<script>
	$("#expected_total").html("Expected $<?=number_format($expected,2)?>");
	$("#funded_total").html("Funded $<?=number_format($funded_total,2)?>");
	$("#short_total").html("Short <?=number_format($short_paid,2)?>");
	$("#billed_total").html("Billed $<?=number_format($billed_total,2)?>");
	$("#percentage").html("<?=number_format($numerator/$i*100,2)?>%");
	$("#unfunded_count").html("Missing HC <?=$missing_hc?>");
	$("#count_total").html("Count <?=$i?>");
	
	$(document).ready(function()
	{
		window.print();
	});
</script>